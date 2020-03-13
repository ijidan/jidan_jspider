<?php

namespace Tools\Models;

use Lib\BaseModel;

/**
 * Class SdList
 * @package Tools\Models
 */
class SdList extends BaseModel {
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
		return "lanke_list";
	}

	/**
	 * 主键
	 * @return string
	 */
	public function getPrimaryKey() {
		return "id";
	}
}