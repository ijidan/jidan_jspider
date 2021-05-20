<?php

namespace Model\Spider;

use Lib\BaseModel;

/**
 * 估价（加拿大）
 * Class HouseEvaluate
 * @package Model\Spider
 */
class HouseEvaluateCA extends BaseModel {
	/**
	 * 数据库名
	 * @return mixed
	 */
	public function getDatabaseName() {
		return 'd_hnb';
		// TODO: Implement getDatabaseName() method.
	}

	/**
	 * 表名
	 * @return mixed
	 */
	public function getTableName() {
		return 'house_evaluate_ca';
		// TODO: Implement getTableName() method.
	}

	/**
	 * 表前缀
	 * @return string
	 */
	public function getTablePrefix() {
		return 't_';
		// TODO: Implement getTablePrefix() method.
	}

	/**
	 * 主键
	 * @return string
	 */
	public function getPrimaryKey() {
		return 'f_id';
		// TODO: Implement getPrimaryKey() method.
	}
}