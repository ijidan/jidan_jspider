<?php

namespace Business\Fang;

use Business\BaseCrawl;
use Exception;
use Lib\Util\CommonUtil;
use Model\Fang\News;
use Model\Fang\NewsImage;
use Model\Spider\NewsSeq;


/**
 * 爬虫爬取数据
 * Class BaseFang
 * @package Business
 */
class BaseFang extends BaseCrawl {

	public $country = ''; //国家

	public $detailReplacePatternList = []; //想起替换正则

	public $removeKeywordsContainerExpress = '';
	public $removeKeywords = [];

	public $baseUrl = '';

	/**
	 * 计算列表页URL
	 * @param int $page
	 * @return int|mixed
	 */
	public function computeListPageUrl($page = 1) {
		$url = $this->baseUrl . 'news/market_{{pageIdx}}.htm';
		$page = str_replace('{{pageIdx}}', $page, $url);
		return $page;
	}

	/**
	 * 计算详情页URL
	 * @param $id
	 * @return string
	 */
	public function computeDetailPageUrl($id) {
		$url = $this->baseUrl . "news/{$id}.htm";
		return $url;
	}

	/**
	 * 计算页数
	 * @param $shortUrl
	 * @return mixed|string
	 * @throws Exception
	 */
	public function crawlPageCnt($shortUrl) {
		$fileName = __FUNCTION__ . '_url_' . $shortUrl;
		$content = $this->fetchContent($fileName, $shortUrl);
		$pattern = '/<a rel=\"nofollow\" href=\"\/news\/market_(\d+)\.htm\">尾页<\/a>/';
		$pageCnt = $this->regComputeOnlyOneData($content, $pattern);
		$this->success("总页数抓取结束：一共 {$pageCnt} 页");
		return $pageCnt;
	}

	/**
	 * 爬取所有ID
	 * @param $shortUrl
	 * @return array|mixed
	 * @throws Exception
	 */
	public function crawAllId($shortUrl) {
		$fileName = __FUNCTION__ . '_url_' . $shortUrl;
		$content = $this->fetchContent($fileName, $shortUrl);
		$idList = $this->computeData($content, '.strongTit a', "href");
		if ($idList) {
			array_walk($idList, function (&$value) {
				$pattern = '/(\d)+/';
				preg_match($pattern, $value, $matches);
				$id = $matches[0];
				$value = $id;
			});
		}
		$thumbnailList = $this->computeData($content, '.fl img', 'src');
		//ID入库
		$this->doId($idList, $thumbnailList);
		$cnt = count($idList);
		$msg = "所有ID抓取结束：一共 {$cnt} 个";
		if ($cnt) {
			$this->success($msg);
		} else {
			$this->warning($msg);
		}
		return $idList;
	}

	/**
	 * 抓取详情
	 * @param $originCountryId
	 * @param $originCityId
	 * @param $id
	 * @return array
	 * @throws Exception
	 */
	public function crawlDetail($id) {
		$fileName = __FUNCTION__ . '_id_' . $id;
		$url = $this->computeDetailPageUrl($id);
		$htmlContent = $this->fetchContent($fileName, $url);
		$title = $this->computeOnlyOneData($htmlContent, '.textTit h1');
		$abstract = $this->computeOnlyOneData($htmlContent, 'div.abstract');
		if ($this->detailReplacePatternList) {
			$content = $this->computeHtmlContent($htmlContent, '.mtcomment', 'first', $this->detailReplacePatternList);
		} else {
			$content = $this->computeHtmlContent($htmlContent, '.mtcomment', 'first');
		}
		if ($this->removeKeywordsContainerExpress && $this->removeKeywords) {
			$content = $this->computeNodeRemovedContent($content, $this->removeKeywordsContainerExpress, $this->removeKeywords);
		}
		//入库
		$seqId = $this->doDetail($id, $title, $abstract, $content);
		//图片入库
		$imgList = $this->extractImage($content);
		$this->doImage($seqId, $imgList);
		$detailData = [
			'id'       => $id,
			'title'    => $title,
			'abstract' => $abstract,
			'content'  => $content
		];
		return $detailData;
	}

	/**
	 * 资讯入库
	 * @param $id
	 * @param $title
	 * @param $abstract
	 * @param $content
	 * @return int|mixed
	 * @throws \ErrorException
	 */
	private function doDetail($id, $title, $abstract, $content) {
		$record = News::findOne('f_origin_id=?', [$id]);
		$cls = NewsSeq::class;
		$insData = [
			'f_country'     => $this->country,
			'f_title'       => $title,
			'f_abstract'    => $abstract,
			'f_content'     => $content,
			'f_update_time' => time()
		];
		$insData = CommonUtil::Array2String($insData);
		$model = new News();
		if ($record) {
			$seqId = $record['f_id'];
			$model->update($insData, 'f_origin_id=' . $id);
		} else {
			$nextId = $this->getNextSeq($cls);
			$seqId = $nextId;

			$insData['f_id'] = $nextId;
			$insData['f_origin_id'] = $id;
			$model->insert($insData);
		}
		$this->info("资讯入库结束： ID:{$id}  标题:{$title}");
		return $seqId;
	}

	/**
	 * ID入库
	 * @param array $idList
	 * @param array $thumbnailList
	 * @return bool
	 * @throws \ErrorException
	 */
	private function doId(array $idList, array $thumbnailList) {
		if (!$idList) {
			return false;
		}
		$cls = NewsSeq::class;
		foreach ($idList as $idx => $id) {
			$record = News::findOne('f_origin_id=?', [$id]);
			$insData = [
				'f_country'     => $this->country,
				'f_thumbnail'   => isset($thumbnailList[$idx]) ? $thumbnailList[$idx] : '',
				'f_update_time' => time()
			];
			if ($record) {
				News::update($insData, 'f_origin_id=' . $id);
			} else {
				$nextId = $this->getNextSeq($cls);
				$insData['f_id'] = $nextId;
				$insData['f_origin_id'] = $id;
				News::insert($insData);
			}
		}
		return true;
	}

	/**
	 * 资讯图片入库
	 * @param $newsId
	 * @param array $imgList
	 * @throws \ErrorException
	 */
	private function doImage($newsId, array $imgList) {
		foreach ($imgList as $img) {
			$record = NewsImage::findOne('f_origin_img_url=?', [$img]);
			if (!$record) {
				$insData = [
					'f_news_id'        => $newsId,
					'f_origin_img_url' => $img,
					'f_update_time'    => time()
				];
				NewsImage::insert($insData);
			}
		}
		$this->info("资讯图片入库结束：资讯ID {$newsId}");
	}


	/**
	 * 开始爬取
	 * @return mixed|void
	 * @throws Exception
	 */
	public function crawl() {
		$this->info('总页数住区开始');
		$firstListPat = $this->computeListPageUrl(1);
		$pageCnt = $this->crawlPageCnt($firstListPat);
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
		// TODO: Implement crawl() method.
	}

	/**
	 * 获取Guzzle配置
	 * @return mixed
	 */
	public function getGuzzleHttpConfig() {
		// TODO: Implement getGuzzleHttpConfig() method.
	}

	/**
	 * 获取业务配置
	 * @return mixed
	 */
	public function getBusinessConfig() {
		// TODO: Implement getBusinessConfig() method.
	}

	/**
	 * 获取自定义配置
	 * @return mixed
	 */
	public function getCustomConfig() {
		// TODO: Implement getCustomConfig() method.
	}
}