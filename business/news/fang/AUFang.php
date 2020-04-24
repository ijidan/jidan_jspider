<?php

namespace Business\News\Fang;

/**
 * 澳大利亚
 * Class AUFang
 * @package Business\Fang
 */
class AUFang extends BaseFang {

	public $platformsSubDir='au';
	public $country='au';

	public $cat1=self::CAT1_TRENDS;
	public $cat2=self::CAT2_MARKET_TRENDS;

	public $baseUrl = "https://au.fang.com/";

	/**
	 * 获取平台
	 * @return mixed
	 */
	public function getPlatform() {
		return 'fang_au';
	}
}