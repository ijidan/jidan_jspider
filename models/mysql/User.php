<?php
namespace Models;

use Lib\BaseModel;

/**
 * 用户表
 * Class User
 * @package Models
 */
class User extends BaseModel {
	/**
	 * 表前缀
	 * @return string
	 */
	public function getTablePrefix() {
		return "self_";
		// TODO: Implement getTablePrefix() method.
	}

	/**
	 * 表名
	 * @return mixed
	 */
	public function getTableName() {
		return "user";
		// TODO: Implement getTableName() method.
	}

	/**
	 * 主键
	 * @return string
	 */
	public function getPrimaryKey() {
		return "id
	    ";
		// TODO: Implement getPrimaryKey() method.
	}
}