<?php

namespace Tools\Models;

use Lib\BaseModel;


/**
 * 工厂产品类型
 * Class Factory
 * @package Tools\Models
 */
class FactoryProductCategory extends BaseModel {

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
		return "factory_product_category";
	}

	/**
	 * 主键
	 * @return string
	 */
	public function getPrimaryKey() {
		return "id";
	}

	/**
	 * 获取所有的商品
	 * @return array
	 */
	public static function getAllFactoryProductCategory() {
		return [
			"bluetooth_audion"  => "蓝牙音响",
			"bluetooth_headset" => "蓝牙耳机"
		];
	}
}