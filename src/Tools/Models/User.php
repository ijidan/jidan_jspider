<?php
namespace Tools\Models;

use Lib\BaseModel;
use Lib\Util\CommonUtil;

/**
 * 用户
 * Class User
 * @package Tools\Models
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

	/**
	 * 获取用户信息
	 * @param array $userIdList
	 * @return array
	 */
	public static function findUserByIdList(array $userIdList = []) {
		if (!$userIdList) {
			return [];
		}
		$userQuestionMark = BaseModel::buildQuestionMark($userIdList);
		$userList = User::find("id in (${userQuestionMark})", $userIdList);
		if(!$userList){
			return [];
		}
		return CommonUtil::arrayGroup($userList,"id",true);
	}
}