<?php

namespace Tools\Models;

use Lib\BaseModel;


/**
 * 工厂证书
 * Class FactoryCertificate
 * @package Tools\Models
 */
class FactoryCertificate extends BaseModel {

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
		return "factory_certificate";
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
	public static function getAllFactoryCertificate() {
		return [
			"ce"    => "CE",
			"rohs"  => "ROHS",
			"en71"  => "EN71",
			"un383" => "UN38.3",
			"fcc"   => "FCC"
		];
	}
}