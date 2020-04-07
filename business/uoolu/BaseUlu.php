<?php

namespace Business\Uoolu;

use Business\BaseCrawl;

/**
 * 爬虫基础类
 * Class BaseUlu
 * @package Business\Uoolu
 */
class BaseUlu extends BaseCrawl {

	protected $baseUrl = "https://www.uoolu.com/";

	/**
	 * 抓取
	 * @return mixed
	 */
	public function crawl() {
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