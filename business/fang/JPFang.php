<?php

namespace Business\Fang;


/**
 * 日本
 * Class JPFang
 * @package Business\Fang
 */
class JPFang extends BaseFang {

	public $platformsSubDir = 'jp';
	public $country = 'jp';

	public $detailReplacePatternList = [
		'/.*咨询热线.*/'                                                               => '',
		'/.*微信.*/'                                                                 => '',
		'/.*推荐房源.*/'                                                               => '',
		'/.*<p style="text-align: center;"><font color="#0066cc"><\/font><\/p>.*/' => '',
		'/.*房源ID.*/'                                                               => '',
		'/.*推荐指数.*/'                                                               => '',
		'/.*<p>价格：.*万日元<\/p>.*/'                                                   => '',
		'/.*<p>格局：.*<\/p>.*/'                                                      => '',
		'/.*<p>装修：.*<\/p>.*/'                                                      => '',
		'/.*<p>产权：.*<\/p>*/'                                                       => '',
		'/.*<p>联系方式：.*<\/p>*/'                                                     => '',

	];

	public $baseUrl = "https://japan.fang.com/";

}