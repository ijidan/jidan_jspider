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

	public $detailReplacePatternList = [
		'/.*查看柬埔寨更.*/' => '',
		'/.*推介会.*/'    => '',
		'/.*地址：.*/'    => '',
		'/.*预约热线.*/'   => '',
		'/.*微信公众号：.*/' => ''
	];

	public $baseUrl = "https://cambodia.fang.com/";

}