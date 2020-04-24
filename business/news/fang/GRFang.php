<?php

namespace Business\News\Fang;


/**
 * 希腊
 * Class GRFang
 * @package Business\News\Fang
 */
class GRFang extends BaseFang {

	public $platformsSubDir = 'gr';
	public $country = 'gr';


	public $cat1=self::CAT1_TRENDS;
	public $cat2=self::CAT2_MARKET_TRENDS;

	public $detailReplacePatternList = [
		'/.*希腊购房\/移民等咨询热线.*/' => ''
	];

	public $detailRemovePositionPatternList = [
		'/.*<p style="text-align: center;"><img.*/i' => [
			"-1" => ""
		]
	];
	public $baseUrl = "https://greece.fang.com/";

	/**
	 * 获取平台
	 * @return mixed
	 */
	public function getPlatform() {
		return 'fang_gr';
	}
}