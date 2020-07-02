<?php

namespace Business\Uoolu;

use Business\BaseCrawl;

/**
 * Class BGImage
 * @package Business\Uoolu
 */
class BGImage extends BaseCrawl {

	/**
	 * 业务类型
	 * @var string
	 */
	public $business = 'bg_img';

	/**
	 * URL
	 * @var string
	 */
	public $baseUrl = 'https://www.uoolu.com/';

	public function crawlDetail() {
		$fileName = __FUNCTION__ . 'index';
		$htmlContent = $this->fetchContent($fileName, $this->baseUrl);
		$hotAreaList=$this->computeHtmlContentList($htmlContent,'.side-filter-content_country');
		dump($hotAreaList,1);
		$hotAreaList=$this->computeData($htmlContent, 'span.hot-area-title');
		$hotAreaImgList = $this->computeData($htmlContent, 'img.hot-area-img','src');
		pr($hotAreaList,$hotAreaImgList,1);
	}
	/**
	 * 抓取
	 * @return mixed
	 */
	public function crawl() {
		$this->crawlDetail();
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

	/**
	 * 获取平台
	 * @return mixed
	 */
	public function getPlatform() {
		return 'uoolu';
	}
}