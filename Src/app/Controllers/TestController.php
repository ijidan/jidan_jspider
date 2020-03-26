<?php

namespace App\Controllers;


use App\Models\JCG;
use Model\WaiGF\House;

/**
 * 测试相关
 * Class TestController
 * @package App\Controllers
 */
class TestController extends IndexController {

	/**
	 * 首页
	 * @return mixed
	 * @throws \Exception
	 */
	public function index() {
		phpinfo();
		pr('1',1);
		$data=House::findOne();
		try{
			$insData=[
				'f_id'=> 1,
			];
			$data=JCG::insert($insData);
		}catch (\Exception $e){
			pr($e->getMessage(),1);
		}
		dump($data,1);
		return $this->renderTemplate("site/index", ["businessList" => []]);
	}
}