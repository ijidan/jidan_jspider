<?php

namespace Business\Fang;


/**
 * 马来西亚
 * Class MYFang
 * @package Business
 */
class MYFang extends BaseFang {

	public $platformsSubDir='my';
	public $country = 'my';

	public $detailReplacePatternList = [
		'/<p style="border:0px currentcolor; margin:0px; padding:10px 0px; text-align:start; -webkit-text-stroke-width:0px">[\s\S]*/' => ''
	];
	public $removeKeywordsContainerExpress = 'section[powered-by="xiumi.us"]';
	public $removeKeywords = ['专注于马来西亚', '搜房', '咨询筛选匹配成交签约移居安家售后物业管理等服务', '楼盘的交易平台'];
	public $baseUrl = "https://malaysia.fang.com/";

}