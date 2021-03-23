<?php

namespace Business\House;

use Business\BaseCrawl;
use Lib\Http\UserAgent;
use Lib\Util\CommonUtil;
use Model\Spider\News;
use Model\Spider\NewsImage;


/**
 *房源基础类
 * Class HouseBase
 * @package Business\News
 */
abstract class HouseBase extends BaseCrawl {


	/**
	 * 业务类型
	 * @var string
	 */
	public $business = 'house';


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
	 * 爬取总数
	 * @param $url
	 * @return mixed
	 */
	abstract public function crawlPageCnt($url);

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
	 * 提取ID
	 * @param array $strIdList
	 */
	public function computeListId(array &$strIdList) {
		if ($strIdList) {
			array_walk($strIdList, function (&$value) {
				$value=$this->extractId($value);
			});
		}
	}

	/**
	 * 提取ID
	 * @param $value
	 * @return mixed
	 */
	public function extractId($value){
		$pattern = '/(\d)+/';
		preg_match($pattern, $value, $matches);
		$id = $matches[0];
		return $id;
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
	public function doId(array $idList, array $thumbnailList, array $abstractList, array $cusData = []) {
		if (!$idList) {
			return false;
		}
		foreach ($idList as $idx => $id) {
			if ($id == 0) {
				continue;
			}
			$thumbnail = isset($thumbnailList[$idx]) ? $thumbnailList[$idx] : '';
			$abstract = isset($abstractList[$idx]) ? $abstractList[$idx] : '';
			$record = News::findOne('f_platform= ? and f_origin_id=? ', [$this->platform, $id]);
			$insData = [
				'f_platform'    => $this->platform,
				'f_thumbnail'   => $thumbnail,
				'f_abstract'    => $abstract,
				'f_update_time' => time()
			];
			if ($cusData) {
				$insData = $insData + $cusData;
			}
			if ($record) {
				$seqId = $record['f_id'];
				News::update($insData, 'f_id=' . $seqId);
			} else {
				$insData['f_origin_id'] = $id;
				$seqId = News::insert($insData);
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
	 */
	public function doImage($newsId, array $imgList) {
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
	 * 资讯入库
	 * @param $cat1
	 * @param $cat2
	 * @param $id
	 * @param $title
	 * @param $abstract
	 * @param $content
	 * @param array $tagList
	 * @param array $cusData
	 * @return string
	 * @throws \ErrorException
	 */
	public function doDetail($cat1, $cat2, $id, $title, $abstract, $content, array $tagList = [], array $cusData = []) {
		$tagStr = $tagList ? join(',', $tagList) : '';
		$record = News::findOne('f_origin_id=?', [$id]);
		$insData = [
			'f_cat1'             => $cat1,
			'f_cat2'             => $cat2,
			'f_title'            => $title,
			'f_content_abstract' => $abstract,
			'f_content'          => $content,
			'f_tag'              => $tagStr,
			'f_update_time'      => time()
		];
		if ($cusData) {
			$insData = $insData + $cusData;
		}
		$insData = CommonUtil::Array2String($insData);
		if ($record) {
			$seqId = $record['f_id'];
			News::update($insData, 'f_id=' . $seqId);
		} else {
			$insData['f_origin_id'] = $id;
			$seqId = News::insert($insData);
		}
		$this->info("资讯入库结束： ID:{$id}  标题:{$title}");
		return $seqId;
	}

	/**
	 * 开始爬取
	 * @return mixed|void
	 */
	public function crawl() {
		$this->info('总页数抓取开始');
		//$firstListPage = $this->computeListPageUrl(1);
		//$pageCnt = $this->crawlPageCnt($firstListPage);
		$pageCnt = 1;
		$this->info("总页数抓取结束：一共 {$pageCnt} 页");
		if ($pageCnt) {
			for ($i = 1; $i <= $pageCnt; $i++) {
				$listPageUrl = $this->computeListPageUrl($i);
				//随机等待多少秒
				$this->waitRandomMS();
				try{
					$allId = $this->crawAllId($listPageUrl);
				}catch (\Exception $e){
					continue;
				}
				$this->info("列表抓取开始：第 {$i} 页");
				$allId=[3172];
				foreach ($allId as $id) {
					$this->info("项目详情抓取开始： ID为 $id");
					try{
						$this->crawlDetail($id);
					}catch (\Exception $e){
						continue;
					}
					$this->info("项目详情抓取结束： ID为 $id");
				}
			}
		}
	}

	/**
	 * 获取Guzzle配置
	 * @return mixed
	 */
	public function getGuzzleHttpConfig() {
		$userAgent = UserAgent::random();
		$guzzleConfig = [
			'timeout' => 20,
			'headers' => [
				'User-Agent' => $userAgent
			]
		];
		if(!$this->isDebugMode){
			$guzzleConfig['proxy']=[
				'http' => 'tcp://163.204.246.18:9999'
			];
		}
		return $guzzleConfig;
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
		$customConfig = ['need_uuid' => false];
		return $customConfig;
	}
}