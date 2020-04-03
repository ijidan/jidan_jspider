<?php

namespace Business\Fang;

/**
 * 美国
 * Class USFang
 * @package Business\Fang
 */
class USFang extends BaseFang {

	public $platformsSubDir='us';
	public $country='us';
	public $detailReplacePatternList=[
		'/.*了解更多海外房产请点击查看.*/'=> ''
	];
	public $baseUrl = "https://us.fang.com/";

}