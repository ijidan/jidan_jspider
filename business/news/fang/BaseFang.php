<?php

namespace Business\News\Fang;

use Business\Category\NewsBase;
use Exception;


/**
 * 爬虫爬取数据
 * Class BaseFang
 * @package Business\News\Fang
 */
class BaseFang extends NewsBase {

	public $business='news';
	public $platformsSubDir = ''; //子目录
	public $country = ''; //国家

	public $detailReplacePatternList = []; //详情替换正则
	public $detailRemovePositionPatternList = []; //详情页按照位置替换内容正则

	public $removeKeywordsContainerExpress = '';
	public $removeKeywords = [];

	public $baseUrl = '';

	/**
	 * 计算列表页URL
	 * @param int $page
	 * @return int|mixed
	 */
	public function computeListPageUrl($page = 1) {
		$url = $this->baseUrl . "news/market_{$page}.htm";
		return $url;
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
		$abstractList = $this->computeData($content, '.fr p');
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
	 */
	public function crawlDetail($id) {
		$fileName = __FUNCTION__ . '_id_' . $id;
		$url = $this->computeDetailPageUrl($id);
		$htmlContent = $this->fetchContent($fileName, $url);
		$title = $this->computeOnlyOneData($htmlContent, '.textTit h1');
		$abstract = $this->computeOnlyOneData($htmlContent, 'div.abstract');

		//详情页替换
		$replacePattern = $this->detailReplacePatternList ?: '';
		$content = $this->computeHtmlContent($htmlContent, '.mtcomment', 'first', $replacePattern);
		if ($this->removeKeywordsContainerExpress && $this->removeKeywords) {
			$content = $this->computeNodeRemovedContent($content, $this->removeKeywordsContainerExpress, $this->removeKeywords);
		}
		if ($this->detailRemovePositionPatternList) {
			$content = $this->computePositionRemovedContent($content, $this->detailRemovePositionPatternList);
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