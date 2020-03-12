<?php

namespace Tools\Controllers;

use Lib\ErrorCode;
use Lib\Paginate;
use Lib\Util\SocketUtil;
use Respect\Validation\Exceptions\IntValException;
use Lib\Util\CommonUtil;
use Tools\Models\AdminRole;
use Tools\Models\AdminUser;
use Tools\Models\Menu;

/**
 * Class AdminController
 * @package Tools\Controllers
 */
class AdminController extends IndexController {


	/**
	 * 首页
	 * @param $params
	 * @return \Slim\Http\Response
	 */
	public function index($params) {
		return $this->response->withRedirect("/admin/showRoleList");
	}

	/**
	 * 角色列表
	 * @param $params
	 * @return mixed
	 */
	public function showRoleList($params) {
		$paginate = Paginate::instance($this->request);
		$roleList = AdminRole::paginate($paginate);
		return $this->renderTemplate("/admin/show_role_list.php", [
			"roleList" => $roleList,
			"paginate" => $paginate
		]);

	}

	/**
	 * 添加角色
	 * @param $params
	 * @return mixed|\Slim\Http\Response
	 */
	public function addRole($params) {
		/** @var \Slim\Http\Request $request */
		$request = $this->request;
		if ($request->isPost()) {
			$roleId = $params["role_id"];
			$roleName = $params["role_name"];
			if (!$roleId || !$roleName) {
				throw new IntValException("param error");
			}
			$adminRole = AdminRole::find("role_id=?", [$roleId]);
			if ($adminRole) {
				throw new \LogicException("user existed");
			}
			AdminRole::insert(["role_id" => $roleId, "role_name" => $roleName]);
			return $this->iFrameResponseSuccess("success",[],"/admin/addRole");
		}
		return $this->renderTemplate('admin/add_role.php', []);
	}

	/**
	 * 编辑角色
	 * @param $params
	 * @return mixed|\Slim\Http\Response
	 */
	public function editRole($params) {
		/** @var \Slim\Http\Request $request */
		$request = $this->request;
		if ($request->isPost()) {
			$roleId = $params["role_id"];
			$roleName = $params["role_name"];
			if (!$roleId || !$roleName) {
				throw new IntValException("param error");
			}
			AdminRole::update(["role_name" => "?"], "role_id=?", [$roleName, $roleId]);
			$role = AdminRole::findOne("role_id=?", [$roleId]);
			return $this->iFrameResponseSuccess("success",[],"/admin/editRole?id=" . $role["id"]);
		} else {
			if (!isset($params["id"]) || !$params["id"]) {
				throw new \InvalidArgumentException("id not found!");
			}
			$role = AdminRole::findOne("id=?", [$params["id"]]);
			return $this->renderTemplate('admin/edit_role.php', [
				"role" => $role
			]);
		}
	}

	/**
	 * 编辑权限
	 * @param $params
	 * @return mixed|string
	 */
	public function editRoleAccess($params) {
		$id = $params["id"];
		/** @var \Slim\Http\Request $request */
		$request = $this->request;
		if ($request->isPost()) {
			$roleAccess = $params["role_access"];
			array_walk($roleAccess, function (&$access) {
				$access = Menu::cleanUrl($access);
			});
			if ($roleAccess) {
				AdminRole::update(["role_access" => '?'], "id=?", [\serialize($roleAccess), $id]);
			}
			return $this->iFrameResponseSuccess("success",[],"/admin/editRoleAccess?id=" . $id);
		}
		$role = AdminRole::findOne("id=?", [$id]);
		$roleAccess = $role["role_access"] ? unserialize($role["role_access"]) : [];
		$menuModel = new Menu();
		$menus = $menuModel->getAll();

		return $this->renderTemplate('admin/edit_role_access.php', [
			"role"       => $role,
			"roleAccess" => $roleAccess,
			"menus"      => $menus,
		]);
	}

	/**
	 * 删除角色
	 * @param $params
	 * @return \Slim\Http\Response
	 */
	public function deleteRole($params) {
		if (isset($params["id"]) && $params["id"]) {
			$id = $params["id"];
			AdminRole::delete("id=?", [$id]);
		}
		return $this->response->withRedirect("/admin/index");
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function operator($params) {
		$paginate = Paginate::instance($this->request);
		$userList = AdminUser::paginate($paginate);
		$roleList = AdminRole::find();
		if ($roleList) {
			$roleList = CommonUtil::arrayGroup($roleList, "id", true);
		}
		return $this->renderTemplate('admin/show_operator.php', [
			"userList" => $userList,
			"roleList" => $roleList,
			"paginate" => $paginate
		]);
	}

	/**
	 * 添加管理员
	 * @param $params
	 * @return mixed|string
	 */
	public function addOperator($params) {
		$roleList = AdminRole::find();
		if ($roleList) {
			$roleList = CommonUtil::arrayGroup($roleList, "id", true);
		}
		/** @var \Slim\Http\Request $request */
		$request = $this->request;
		if ($request->isPost()) {
			$account = $params["account"];
			$password = $params["password"];
			$roleId = $params["role_id"];
			if (!$account || !$password || !$roleId || !isset($roleList[$roleId])) {
				throw new IntValException("param error");
			}
			$adminUser = AdminUser::findOne("account=?", [$account]);
			if ($adminUser) {
				throw new \LogicException("user existed");
			}
			AdminUser::insert([
				"account"     => strtolower($account),
				"password"    => CommonUtil::hashPassword($password),
				"role_id"     => $roleId,
				"is_del"      => AdminUser::STATUS_IS_DEL_NO,
				"create_time" => NOW,
				"update_time" => NOW
			]);
			return $this->iFrameResponseSuccess("success", [], "/admin/addOperator");
		}
		return $this->renderTemplate('admin/add_operator.php', [
			"roleList" => $roleList,
		]);
	}

	/**
	 * 编辑账号
	 * @param $params
	 * @return mixed|string
	 */
	public function editOperator($params) {
		$roleList = AdminRole::find();
		if ($roleList) {
			$roleList = CommonUtil::arrayGroup($roleList, "id", true);
		}
		/** @var \Slim\Http\Request $request */
		$request = $this->request;
		if ($request->isPost()) {
			$account = $params["account"];
			$password = $params["password"];
			$roleId = $params["role_id"];
			if (!$account || !$password || !$roleId || !isset($roleList[$roleId])) {
				throw new IntValException("param error");
			}
			AdminUser::update([
				"password" => "?",
				"role_id"  => "?"
			], "account=?", [CommonUtil::hashPassword($password), $roleId, $account]);
			$user = AdminUser::findOne("account=?", [$account]);
			return $this->iFrameResponseSuccess(ErrorCode::RIGHT, [], "success", "/admin/editOperator?id=" . $user["id"]);
		} else {
			if (!isset($params["id"]) || !$params["id"]) {
				throw new \InvalidArgumentException("id not found!");
			}
			$user = AdminUser::findOne("id=?", [$params["id"]]);
			return $this->renderTemplate('admin/edit_operator.php', [
				"roleList" => $roleList,
				"user"     => $user
			]);
		}
	}

	/**
	 * 更新
	 * @param $params
	 */
	public function updateOperator($params) {
		if (isset($params["id"]) && $params["id"]) {
			$id = $params["id"];
			$adminUser = AdminUser::findOne("id=?", [$id]);
			$newIsDel = $adminUser["is_del"] == AdminUser::STATUS_IS_DEL_YES ? AdminUser::STATUS_IS_DEL_NO : AdminUser::STATUS_IS_DEL_YES;
			AdminUser::update(["is_del" => "?"], "id=?", [$newIsDel, $id]);
		}
		$this->operator([]);
	}

	/**
	 * 绑定socket
	 * @param $params
	 * @return \Lib\BaseController
	 */
	public function bindSocket($params) {
		$clientId = $params["client_id"];
		$loginUser = $this->getCurrentUser();
		$loginUserId=$loginUser["id"];
		$socketUtil = new SocketUtil();
		$serviceId = $socketUtil->buildServiceId($loginUserId);
		$socketUtil->bindUid($clientId, $serviceId);
		return $this->jsonSuccess("success");
	}
}