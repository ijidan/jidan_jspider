<?php

namespace Business\Fang;


/**
 * 希腊
 * Class GRFang
 * @package Business\Fang
 */
class GRFang extends BaseFang {

	public $platformsSubDir='gr';
	public $country='gr';

	public $removeKeywordsContainerExpress=[
		'/.*希腊购房/移民等咨询热线.*/'=>'',
		'/.*<p style="text-align: center;"><img.*/i'=> ''
	];
	public $baseUrl = "https://greece.fang.com/";

}