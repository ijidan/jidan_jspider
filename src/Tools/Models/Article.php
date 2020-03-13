<?php
namespace Tools\Models;

use Lib\BaseModel;

/**
 * Class Article
 * @package Tools\Models
 */
class Article extends BaseModel {


	const VISIBILITY_YES=1; //可见
	const VISIBILITY_NO=0; //不可见
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
		return "article";
	}

	/**
	 * 主键
	 * @return string
	 */
	public function getPrimaryKey() {
		return "id";
	}
}