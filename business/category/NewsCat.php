<?php

namespace Business\Category;

use Business\BaseCrawl;
use Lib\BaseModel;
use Lib\Util\CommonUtil;
use Model\Spider\NewsSeq;

/**
 * 资讯类
 * Class NewsCat
 * @package Business\Category
 */
abstract class NewsCat extends BaseCrawl {

	/**
	 * @var BaseModel $newsModel ;
	 */
	public $newsModel;
	/**
	 * @var BaseModel $newsImgModel ;
	 */
	public $newsImgModel;

	/**
	 * 计算列表页URL
	 * @param int $page
	 * @return mixed
	 */
	abstract public function computeListPageUrl($page = 1);

	/**
	 * 计算详情页URL
	 * @param $id
	 * @return mixed
	 */
	abstract public function computeDetailPageUrl($id);

	/**
	 * 爬取所有ID
	 * @param $shortUrl
	 * @return mixed
	 */
	abstract public function crawAllId($shortUrl);

	/**
	 * 爬取详情页
	 * @param $id
	 * @return mixed
	 */
	abstract public function crawlDetail($id);


	/**
	 * 设置模型类别
	 * @param String $newsCls
	 * @param String $newsImgCls
	 */
	public function setModelCls($newsCls, $newsImgCls) {
		$this->newsModel = new $newsCls();
		$this->newsImgModel = new $newsImgCls();
	}


	/**
	 * 提取ID
	 * @param array $strIdList
	 */
	public function computeListId(array &$strIdList) {
		if ($strIdList) {
			array_walk($strIdList, function (&$value) {
				$pattern = '/(\d)+/';
				preg_match($pattern, $value, $matches);
				$id = $matches[0];
				$value = $id;
			});
		}
	}

	/**
	 * 抓取总页数
	 * @param $url
	 * @param $express
	 * @return mixed
	 * @throws \Exception
	 */
	public function crawlPageCnt($url, $express) {
		$fileName = __FUNCTION__ . '_url_' . $url;
		$content = $this->fetchContent($fileName, $url);
		$idList = $this->computeData($content, $express);
		$filteredList = array_filter($idList, function ($val) {
			$intVal = intval($val);
			$convertedVal = (string)$intVal == $val;
			return $convertedVal == $val;
		});
		$pageCnt = array_pop($filteredList);
		return $pageCnt;
	}

	/**
	 * ID入库
	 * @param array $idList
	 * @param array $thumbnailList
	 * @param array $abstractList
	 * @param array $cusData 自定义数据
	 * @return bool
	 * @throws \ErrorException
	 */
	public function doId(array $idList, array $thumbnailList, array $abstractList,array $cusData=[]) {
		if (!$idList) {
			return false;
		}
		$cls = NewsSeq::class;
		foreach ($idList as $idx => $id) {
			if ($id == 0) {
				continue;
			}
			$thumbnail = isset($thumbnailList[$idx]) ? $thumbnailList[$idx] : '';
			$abstract = isset($abstractList[$idx]) ? $abstractList[$idx] : '';
			$record = $this->newsModel->findOne('f_origin_id=?', [$id]);
			$insData = [
				'f_thumbnail'   => $thumbnail,
				'f_abstract'    => $abstract,
				'f_update_time' => time()
			];
			if($cusData){
				$insData=$insData+$cusData;
			}
			if ($record) {
				$seqId = $record['f_id'];
				$this->newsModel->update($insData, 'f_origin_id=' . $id);
			} else {
				$nextId = $this->getNextSeq($cls);
				$seqId = $nextId;
				$insData['f_id'] = $nextId;
				$insData['f_origin_id'] = $id;
				$this->newsModel->insert($insData);
			}
			//插入图片
			$this->doImage($seqId, [$thumbnail]);
		}
		return true;
	}

	/**
	 * 资讯入库
	 * @param $newsId
	 * @param array $imgList
	 * @throws \ErrorException
	 */
	public function doImage($newsId, array $imgList) {
		foreach ($imgList as $img) {
			$record = $this->newsImgModel->findOne('f_origin_img_url=?', [$img]);
			if (!$record) {
				$insData = [
					'f_news_id'        => $newsId,
					'f_origin_img_url' => $img,
					'f_update_time'    => time()
				];
				$this->newsImgModel->insert($insData);
			}
		}
		$this->info("资讯图片入库结束：资讯ID {$newsId}");
	}

	/**
	 * 资讯入库
	 * @param $id
	 * @param $title
	 * @param $abstract
	 * @param $content
	 * @param array $cusData
	 * @return int|mixed
	 * @throws \ErrorException
	 */
	public function doDetail($id, $title, $abstract, $content, array $cusData = []) {
		$record = $this->newsModel->findOne('f_origin_id=?', [$id]);
		$insData = [
			'f_title'            => $title,
			'f_content_abstract' => $abstract,
			'f_content'          => $content,
			'f_update_time'      => time()
		];
		if ($cusData) {
			$insData = $insData + $cusData;
		}
		$insData = CommonUtil::Array2String($insData);
		if ($record) {
			$seqId = $record['f_id'];
			$this->newsModel->update($insData, 'f_origin_id=' . $id);
		} else {
			$cls = NewsSeq::class;
			$nextId = $this->getNextSeq($cls);
			$seqId = $nextId;

			$insData['f_id'] = $nextId;
			$insData['f_origin_id'] = $id;
			$this->newsModel->insert($insData);
		}
		$this->info("资讯入库结束： ID:{$id}  标题:{$title}");
		return $seqId;
	}

	/**
	 * 开始爬取
	 * @return mixed|void
	 * @throws \Exception
	 */
	public function crawl() {
		$this->info('总页数抓取开始');
		$firstListPage = $this->computeListPageUrl(1);
		$pageCnt = $this->crawlPageCnt($firstListPage, '.pagination-item');
		$pageCnt = 1;
		$this->info("总页数抓取结束：一共 {$pageCnt} 页");
		if ($pageCnt) {
			for ($i = 1; $i <= $pageCnt; $i++) {
				$listPageUrl = $this->computeListPageUrl($i);
				//随机等待多少秒
				$this->waitRandomMS();
				$allId = $this->crawAllId($listPageUrl);
				$this->info("列表抓取开始：第 {$i} 页");
				foreach ($allId as $id) {
					$this->info("项目详情抓取开始： ID为 $id");
					$this->crawlDetail($id);
					$this->info("项目详情抓取结束： ID为 $id");
				}
			}
		}
	}
}