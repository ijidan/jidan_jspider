<?php

namespace Business\Fang;

/**
 * 英国
 * Class UKFang
 * @package Business\Fang
 */
class UKFang extends BaseFang {

	public $platformsSubDir = 'uk';
	public $country = 'uk';
	public $detailReplacePatternList = [
		'/.*我们帮你.*/'    => '',
		'/.*可拨打.*/'     => '',
		'/.*添加微信.*/'    => '',
		'/.*一对一沟通交流.*/' => '',
		'/.*详询.*/'      => '',

	];
	public $baseUrl = "https://uk.fang.com/";

}