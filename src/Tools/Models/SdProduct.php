<?php

namespace Tools\Models;

use Lib\BaseModel;

/**
 * Class SDProduct
 * @package App\Model
 */
class SdProduct extends BaseModel {
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
		return "lanke_product";
	}

	/**
	 * 主键
	 * @return string
	 */
	public function getPrimaryKey() {
		return "id";
	}
}