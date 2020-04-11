<?php

namespace Business\News;

use Business\Category\NewsBase;
use Exception;

/**
 * 资讯爬取
 * Class GloFang
 * @package Business\News
 */
class GloFang extends NewsBase {

	public $platformsSubDir = ''; //子目录
	public $country = ''; //国家

	public $baseUrl = 'https://www.glofang.com/';


	/**
	 * 计算列表页URL
	 * @param int $page
	 * @return int|mixed
	 */
	public function computeListPageUrl($page = 1) {
		$url = $this->baseUrl . "news/list/33/{$page}/";
		return $url;
	}

	/**
	 * 计算详情页URL
	 * @param $id
	 * @return string
	 */
	public function computeDetailPageUrl($id) {
		$url = $this->baseUrl . "news/show/{$id}/";
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
		$content = $this->fetchContent($fileName, $shortUrl,'GBK','UTF-8//IGNORE');
		$idStr=$this->computeOnlyOneData($content,'.list_page a','href');
		$idList=explode('/',$idStr);
		$idList=array_filter($idList,function ($value){
			return boolval($value);
		});
		$pageCnt=array_pop($idList);
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
		$idList = $this->computeData($content, '.list_con_img_div a', "href");
		$this->computeListId($idList);
		$thumbnailList = $this->computeData($content, '.list_con_img_div img', 'src');
		$abstractList = $this->computeData($content, '.list_con_text_div p');

		//ID入库
		$this->doId($idList, $thumbnailList,$abstractList);
		$newIdList = array_filter($idList, function ($value) {
			return $value > 0;
		});
		$cnt = count($newIdList);
		$msg = "所有ID抓取结束：一共 {$cnt} 个";
		if ($cnt) {
			$this->success($msg);
		} else {
			$this->warning($msg);
		}
		return $newIdList;
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
		$htmlContent = $this->fetchContent($fileName, $url,'UTF-8','gb2312//IGNORE');
		$title = $this->computeOnlyOneData($htmlContent, '.textTit h1');
		$abstract = $this->computeOnlyOneData($htmlContent, 'div.abstract');

		//详情页替换
		$replacePattern ='';
		$content = $this->computeHtmlContent($htmlContent, '.mtcomment', 'first', $replacePattern);
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