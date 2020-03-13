<?php
namespace Tools\Controllers;

use Lib\Paginate;
use Tools\Models\Goods;

/**
 * Class ProductsController
 * @package Tools\Controllers
 */
class ProductsController extends IndexController {
	/**
	 * 订单列表
	 * @param $param
	 * @return mixed
	 */
	public function showList($param) {
		$goodsId=$param["goods_id"];
		$goodsName=$param["goods_name"];
		$startPrice=floatval($param["start_price"]);
		$endPrice=floatval($param["end_price"]);
		$websiteId=intval($param["website_id"]);
		$websiteGoodsId=$param["website_goods_id"];
		$conWhere="1=1";
		$conData=[];
		if($goodsId){
			$conWhere.=" and id like ?";
			$conData[]="%${goodsId}%";
		}
		if($goodsName){
			$conWhere.=" and name like ?";
			$conData[]="%${goodsName}%";
		}
		if($startPrice){
			$conWhere.=" and sell_price >= ?";
			$conData[]=$startPrice;
		}
		if($endPrice){
			$conWhere.=" and sell_price <= ?";
			$conData[]=$endPrice;
		}
		if($websiteId && in_array($websiteId,[Goods::WEBSITE_1688,Goods::WEBSITE_TAOBAO,Goods::WEBSITE_TMALL])){
			$conWhere.=" and website_id= ?";
			$conData[]=$websiteId;
			if($websiteGoodsId){
				$conWhere.=" and website_goods_id=?";
				$conData[]=$websiteGoodsId;
			}
		}
		$paginate = Paginate::instance($this->request);
		$goodsList = Goods::paginate($paginate,$conWhere,$conData);
		return $this->renderTemplate("goods/show_list.php", [
			"goodsList"               => $goodsList,
			"paginate"                => $paginate,
			"search"=> $param
		]);
	}

	/**
	 * @return mixed
	 */
	public function addGoods() {
		return $this->renderTemplate("order/add_goods.php", []);
	}

	/**
	 * 取消商品
	 * @param $param
	 * @return static
	 */
	public function cancelGoods($param) {
		$orderId = intval($param["order_id"]);
		$goodsId = intval($param["goods_id"]);
		OrderGoods::update(["is_del" => "?"], "order_id=? and goods_id=?", [OrderGoods::IS_DEL_YES,$orderId, $goodsId]);
		return $this->jsonSuccess("success");
	}

}