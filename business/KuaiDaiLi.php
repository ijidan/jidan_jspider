<?php

namespace Business;

use DOMDocument;
use Lib\Net\BaseService;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * 爬虫爬数据
 * Class GearBestUtil
 * @package Lib\Util
 */
class KuaiDaiLi extends BaseCrawl {

	const BASE_URL = "https://www.kuaidaili.com/free/inha/";

	public static function crawlDetail(OutputInterface $output = null) {
		$url = self::BASE_URL . '1';
//		$config = [
//			'guzzleHttp' => [
//				'proxy' => [
//					'http'  => 'tcp://163.204.246.18:9999', // Use this proxy with "http"
////					'https' => 'tcp://localhost:9124', // Use this proxy with "https",
//				]
//			]
//		];
		$rsp = BaseService::sendGetRequest($url, [],[]);
		$content = $rsp->success() ? $rsp->getData() : '';
		$dom = new DOMDocument();
		$dom->loadHTML($content);
		$dom->normalize();
		$tableList=$dom->getElementsByTagName('table');
		$table=$tableList->item(0);
		$nodeList=$table->childNodes;
		dump($nodeList,1);
		$markList = str_replace('var marklist=', '', $content);
		$markList = \json_decode($markList, true);
		pr($markList, 1);
	}
}