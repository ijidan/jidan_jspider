<?php
namespace Tools\Models;

use Lib\BaseModel;

/**
 * Class AdminUser
 * @package Tools\Models
 */
class AdminUser extends BaseModel {

	const STATUS_IS_DEL_YES = 1; //无效
	const STATUS_IS_DEL_NO = 0;  //有效

	/**
	 *状态描述
	 * @var array
	 */
	public static $STATUS_DES_MAP = [
		self::STATUS_IS_DEL_YES => "Inactive",
		self::STATUS_IS_DEL_NO  => "Active"
	];

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
		return "admin_user";
	}

	/**
	 * 主键
	 * @return string
	 */
	public function getPrimaryKey() {
		return "id";
	}

	/**
	 * 获取客服
	 * @return array|mixed|null|object
	 */
	public static function getValidServiceList(){
		$roleId=(new AdminRole())->getRoleId(AdminRole::ROLE__ID_SERVICE);
		$adminUserList = AdminUser::find("role_id=? and is_del=?", [$roleId,self::STATUS_IS_DEL_NO]);
		if(!$adminUserList){
			return [];
		}
		array_walk($adminUserList,function(&$adminUser){
			unset($adminUser["password"]);
		});
		return $adminUserList;
	}
}