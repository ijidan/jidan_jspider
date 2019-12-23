<?php
namespace Tools\Models;

use Lib\BaseModel;

/**
 * Class Order
 * @package Tools\Models
 */
class Category extends BaseModel {

	const IS_DEL_YES=1; //已删除
	const IS_DEL_NO=0; //未删除

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
		return "category";
	}

	/**
	 * 主键
	 * @return string
	 */
	public function getPrimaryKey() {
		return "id";
	}
}