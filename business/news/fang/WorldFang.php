<?php

namespace Business\News\Fang;

/**
 * 全部
 * Class WorldFang
 * @package Business\Fang
 */
class WorldFang extends BaseFang {

	public $platformsSubDir = 'world';
	public $country = 'world';

	public $baseUrl = "https://world.fang.com/";

	public $detailRemovePositionPatternList = [
		'/<img.*title="1213685662.jpg".*/' => [
			"-1" => ""
		]
	];

	/**
	 * 计算列表页URL
	 * @param int $page
	 * @return int|mixed
	 */
	public function computeListPageUrl($page = 1) {
		$url = $this->baseUrl . "open/news_{$page}.htm";
		return $url;
	}

	/**
	 * 计算详情页URL
	 * @param $id
	 * @return string
	 */
	public function computeDetailPageUrl($id) {
		$url = $this->baseUrl . "news/open/{$id}.htm";
		return $url;
	}
}