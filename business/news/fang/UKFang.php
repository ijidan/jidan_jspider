<?php

namespace Business\News\Fang;

/**
 * 英国
 * Class UKFang
 * @package Business\Fang
 */
class UKFang extends BaseFang {

	public $platformsSubDir = 'uk';
	public $country = 'uk';

	public $cat1=self::CAT1_TRENDS;
	public $cat2=self::CAT2_MARKET_TRENDS;

	public $detailReplacePatternList = [
		'/.*我们帮你.*/'    => '',
		'/.*可拨打.*/'     => '',
		'/.*添加微信.*/'    => '',
		'/.*一对一沟通交流.*/' => '',
		'/.*详询.*/'      => '',

	];
	public $baseUrl = "https://uk.fang.com/";

	/**
	 * 获取平台
	 * @return mixed
	 */
	public function getPlatform() {
		return 'fang_uk';
	}
}