<?php

namespace Business;

/**
 * 爬虫爬数据
 * Class GearBestUtil
 * @package Lib\Util
 */
class Test extends BaseCrawl {

	protected $baseUrl = "http://api.hinabian.com/";

	public function index() {
		$fileName = __FUNCTION__;
		$url = $this->baseUrl . 'index';
		$content = $this->fetchContent($fileName, $url);
		dump($content, 1);
	}

	public function crawl() {
		// TODO: Implement crawl() method.
	}
}