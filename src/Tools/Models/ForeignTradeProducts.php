<?php

namespace Tools\Models;

use Lib\BaseModel;

/**
 * 外贸商品信息
 * Class ForeignTradeProducts
 * @package Tools\Models
 */
class ForeignTradeProducts extends BaseModel {

	const IS_SHOW_YES = 1; //显示
	const IS_SHOW_NO = 0;  //不显示

	/**
	 * 表前缀
	 * @return string
	 */
	public function getTablePrefix() {
		return "self_";
	}

	/**
	 * 表名
	 * @return mixed
	 */
	public function getTableName() {
		return "foreign_trade_products";
	}

	/**
	 * 主键
	 * @return string
	 */
	public function getPrimaryKey() {
		return "id";
	}

	/**
	 * 外贸商品分类
	 * @return array
	 */
	public static function getForeignTradeProductsCategory() {
		return [
			"electronic_products"      => "电子产品",
			"plastic_products"         => "塑料制品",
			"toys_products"            => "玩具产品",
			"maternal_infant_products" => "母婴用品",
		];
	}
}