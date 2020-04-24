<?php

namespace Business\News\Fang;

/**
 * 美国
 * Class USFang
 * @package Business\Fang
 */
class USFang extends BaseFang {

	public $platformsSubDir='us';
	public $country='us';


	public $cat1=self::CAT1_TRENDS;
	public $cat2=self::CAT2_MARKET_TRENDS;


	public $detailReplacePatternList=[
		'/.*了解更多海外房产请点击查看.*/'=> ''
	];
	public $baseUrl = "https://us.fang.com/";

	/**
	 * 获取平台
	 * @return mixed
	 */
	public function getPlatform() {
		return 'fang_us';
	}
}