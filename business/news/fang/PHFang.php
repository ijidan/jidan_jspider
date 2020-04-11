<?php

namespace Business\News\Fang;

/**
 * 菲律宾
 * Class PHFang
 * @package Business\Fang
 */
class PHFang extends BaseFang {

	public $platformsSubDir = 'ph';
	public $country = 'ph';


	public $cat1=self::CAT1_TRENDS;
	public $cat2=self::CAT2_MARKET_TRENDS;

	public $detailReplacePatternList = [
		'/.*欢迎咨询掘金海外.*/'                                 => '',
		'/.*了解菲律宾一线投资信息.*/'                              => '',
		'/.*a079c688-9a5b-4921-bcac-745bb614cc50.jpg.*/' => '',
		'/.*更多海外房产资讯，欢迎关注公众号.*/'                         => '',
	];
	public $baseUrl = "https://philippines.fang.com/";

}