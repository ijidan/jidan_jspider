<?php

namespace Business\Fang;


/**
 * 日本
 * Class JPFang
 * @package Business\Fang
 */
class JPFang extends BaseFang {

	public $country='jp';
	public $removeKeywordsContainerExpress = 'section[powered-by="xiumi.us"]';
	public $removeKeywords = ['专注于马来西亚', '搜房', '咨询筛选匹配成交签约移居安家售后物业管理等服务', '楼盘的交易平台'];
	public $baseUrl = "https://japan.fang.com/";

}