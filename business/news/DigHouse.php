<?php

namespace Business\News;

use Exception;

/**
 * 资讯爬取
 * Class DigHouse
 * @package Business\Deju
 */
class DigHouse extends NewsBase {

	public $platformsSubDir = ''; //子目录

	public $baseUrl = 'https://www.dighouse.com/';

	/**
	 * 计算列表页URL
	 * @param int $page
	 * @return int|mixed
	 */
	public function computeListPageUrl($page = 1) {
		$url = $this->baseUrl . "article/analysis/page{$page}/";
		return $url;
	}

	/**
	 * 计算详情页URL
	 * @param $id
	 * @return string
	 */
	public function computeDetailPageUrl($id) {
		$url = $this->baseUrl . "article/{$id}";
		return $url;
	}

	/**
	 * 抓取总页数
	 * @param $url
	 * @param $express
	 * @return mixed
	 * @throws \Exception
	 */
	public function crawlPageCnt($url) {
		$fileName = __FUNCTION__ . '_url_' . $url;
		$content = $this->fetchContent($fileName, $url);
		$idList = $this->computeData($content, '.pagination-item');
		$filteredList = array_filter($idList, function ($val) {
			$intVal = intval($val);
			$convertedVal = (string)$intVal == $val;
			return $convertedVal == $val;
		});
		$pageCnt = array_pop($filteredList);
		return $pageCnt;
	}


	/**
	 * 爬取所有的ID
	 * @param $shortUrl
	 * @return array|mixed
	 * @throws \ErrorException
	 * @throws Exception
	 */
	public function crawAllId($shortUrl) {
		$fileName = __FUNCTION__ . '_url_' . $shortUrl;
		$content = $this->fetchContent($fileName, $shortUrl);
		$idList = $this->computeData($content, '.ArticleListItem', "href");
		$this->computeListId($idList);
		$thumbnailList = $this->computeData($content, '.ArticleListItem img', 'src');
		$abstractList = $this->computeData($content, '.ArticleListItem .rich-text');

		//ID入库
		$this->doId($idList, $thumbnailList, $abstractList);
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
	 * @param $id
	 * @return array|mixed
	 * @throws \ErrorException
	 * @throws Exception
	 */
	public function crawlDetail($id) {
		$fileName = __FUNCTION__ . '_id_' . $id;
		$url = $this->computeDetailPageUrl($id);
		$htmlContent = $this->fetchContent($fileName, $url);
		$title = $this->computeOnlyOneData($htmlContent, '.article-title');
		$abstract = '';

		//详情页替换
		$replacePattern = '';
		$content = $this->computeHtmlContent($htmlContent, '.rich-text', 'first', $replacePattern);
		$tagList=$this->computeData($htmlContent,'.ToolsLineView span');
		//入库
		$seqId = $this->doDetail(NewsBase::CAT1_TRENDS,NewsBase::CAT2_HOUSE_INVEST,$id, $title, $abstract, $content,$tagList);
		//图片入库
		$imgList = $this->extractImage($content, 'img', 'data-src');
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