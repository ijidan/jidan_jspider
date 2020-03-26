<?php

namespace App\Models;

use Lib\BaseSolr;

/**
 * 房源
 * Class House
 * @package App\Models
 */
class House extends BaseSolr {

	/**
	 * 数据库名
	 * @return mixed
	 */
	public function getConnectionName() {
		return 'house';
	}

	/**
	 * 表名
	 * @return mixed
	 */
	public function getCoreName() {
		return 'house';
	}
}