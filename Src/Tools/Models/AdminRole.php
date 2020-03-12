<?php
namespace Tools\Models;

use Lib\BaseModel;

/**
 * 账号角色
 * Class AdminRole
 * @package Tools\Models
 */
class AdminRole extends BaseModel {

	const ROLE_ID_ADMIN = "admin"; //管理员账号
	const ROLE__ID_SERVICE="service"; //客服人员

	/**
	 * 是否有访问权限
	 * @param $roleId
	 * @param $route
	 * @return bool
	 */
	public static function isAccessed($roleId, $route) {
		$menuModel = new Menu();
		$urls = $menuModel->getAllUrls();
		if ($roleId == self::ROLE_ID_ADMIN || !in_array(Menu::cleanUrl($route), $urls)) {
			return true;
		}
		$roleData = AdminRole::findOne("role_id=?", [$roleId]);
		$roleAccess = $roleData["role_access"] ? \unserialize($roleData["role_access"]) : [];
		$check = in_array(Menu::cleanUrl($route), $roleAccess);
		return $check;
	}

	/**
	 * 获取角色ID
	 * @param $role
	 * @return mixed
	 */
	public function getRoleId($role){
		$roleData=AdminRole::findOne("role_id=?",[$role]);
		return $roleData["id"];
	}

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
		return "admin_role";
	}

	/**
	 * 主键
	 * @return string
	 */
	public function getPrimaryKey() {
		return "id";
	}
}