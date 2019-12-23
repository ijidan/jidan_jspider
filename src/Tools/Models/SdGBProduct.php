<?php

namespace Tools\Models;

use Lib\BaseModel;

/**
 * Class SdGBProduct
 * @package Tools\Models
 */
class SdGBProduct extends BaseModel {
	/**
	 * 表前缀
	 * @return string
	 */
	public function getTablePrefix() {
		return "";
	}

	/**
	 * 表名
	 * @return mixed
	 */
	public function getTableName() {
		return "lanke_gb_product";
	}

	/**
	 * 主键
	 * @return string
	 */
	public function getPrimaryKey() {
		return "id";
	}
}