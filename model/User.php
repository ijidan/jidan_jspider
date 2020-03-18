<?php
namespace Model;

use Lib\BaseModel;

/**
 * 用户表
 * Class User
 * @package Models
 */
class User extends BaseModel {
	/**
	 * 数据库名
	 * @return mixed
	 */
	public function getDatabaseName() {
		return 'd_spider_waigf';
		// TODO: Implement getDatabaseName() method.
	}

	/**
	 * 表名
	 * @return mixed
	 */
	public function getTableName() {
		return 'country';
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