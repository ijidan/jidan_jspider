<?php

namespace Business;

use Lib\Net\BaseService;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * 爬虫爬数据
 * Class GearBestUtil
 * @package Lib\Util
 */
class ULu extends BaseCrawl {

	const BASE_URL = "https://www.uoolu.com/";

	public static function crawlDetail($id, OutputInterface $output=null) {
		$url=self::BASE_URL.'house-'.$id.'.html';
		$content=BaseService::sendGetRequest($url);
		pr($content,1);
	}
}