<?php

namespace Business\News\Fang;

/**
 * 柬埔寨
 * Class KHFang
 * @package Business\Fang
 */
class KHFang extends BaseFang {

	public $platformsSubDir = 'kh';
	public $country = 'kh';


	public $cat1=self::CAT1_TRENDS;
	public $cat2=self::CAT2_MARKET_TRENDS;

	public $detailReplacePatternList = [
		'/.*查看柬埔寨更.*/' => '',
		'/.*推介会.*/'    => '',
		'/.*地址：.*/'    => '',
		'/.*预约热线.*/'   => '',
		'/.*微信公众号：.*/' => ''
	];

	public $baseUrl = "https://cambodia.fang.com/";

}