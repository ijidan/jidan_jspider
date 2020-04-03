<?php

namespace Model\Spider;

use Lib\BaseModel;

/**
 * 公用表
 * Class SeqCommon
 * @package Model\Spider
 */
abstract class SeqCommon extends BaseModel {
	/**
	 * 数据库名
	 * @return mixed
	 */
	public function getDatabaseName() {
		return 'd_spider_common';
		// TODO: Implement getDatabaseName() method.
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