<?php

namespace App\Controllers;


use App\Models\House as SolrHouse;
use Business\GloFang;
use Lib\Net\BaseService;
use Lib\Util\Paginate;
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

		$fang=new GloFang();
//		$data=$fang->crawlDetail('35517560');
		$data=$fang->crawl();
		pr($data,1);
		$url='http://api.shenjian.io/';

		$param=[
			'weixinId'=> 'yiminhnb',
			'appid'=>'f56d2f84f716c5325085af92e7010f79'
		];
		$config=['timeout' => 50];
		$data=BaseService::sendGetRequest($url,$param,$config);
		pr($data,1);
//		$kv=[
//			'f_id'=>8
//		];
		$data=SolrHouse::delete([]);
		pr($data,1);
//		$kv=[
//			'f_price'=> '鸡蛋'
//		];
//		$data=SolrHouse::update($kv,'f_id',8);
		$txt='深圳和吉隆坡哪里买房好呢';
		$words=SolrHouse::splitWord($txt);
		pr($words,1);
		$pager=Paginate::instance([]);
		$kv=[
			'f_tag'=> '吉隆坡*',
			'f_title'=> 'the*'
		];
		$kv=[
			'f_title'=> '吉隆坡时代8号3房3卫公寓'
		];
		$data=SolrHouse::delete($kv);
		pr($data,1);
		$data=House::findOne();
		try{
			$data=SolrHouse::insert($data);
		}catch (\Exception $e){
			dump($e->getMessage());
		}
		dump($data,1);
		return $this->renderTemplate("site/index", ["businessList" => []]);
	}
}