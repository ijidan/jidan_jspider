<?php
namespace Tools\Controllers;

use Lib\ErrorCode;
use Lib\Paginate;
use Lib\Util\Config;
use Lib\Util\CommonUtil;
use Tools\Models\Address;
use Tools\Models\User;

/**
 * Class UserController
 * @package Tools\Controllers
 */
class UserController extends IndexController {
	/**
	 * 用户列表
	 * @param $param
	 * @return mixed
	 */
	public function showList($param) {
		$mobile = $param["mobile"] ? trim($param["mobile"]) : "";
		//拼凑Where条件
		$conWhere = "1=1 ";
		$conData = [];
		if ($mobile) {
			$conWhere .= " and mobile like ?";
			$conData[] = "%${mobile}%";
		}
		$paginate = Paginate::instance($this->request);
		$userList = User::paginate($paginate, $conWhere, $conData, "id", "DESC");
		return $this->renderTemplate('user/show_list.php', [
			"userList" => $userList,
			"paginate" => $paginate,
			"search"   => $param
		]);
	}

	/**
	 * 地址列表
	 * @param $param
	 * @return mixed
	 */
	public function showAddressList($param) {
		$mobile = $param["mobile"] ? trim($param["mobile"]) : "";
		//拼凑Where条件
		$conWhere = "1=1 ";
		$conData = [];
		if ($mobile) {
			$conWhere .= " and telphone like ?";
			$conData[] = "%${mobile}%";
		}
		$paginate = Paginate::instance($this->request);
		$addressList = Address::paginate($paginate, $conWhere, $conData, "id", "DESC");
		return $this->renderTemplate('user/show_address_list.php', [
			"addressList" => $addressList,
			"paginate"    => $paginate,
			"search"      => $param
		]);
	}

	/**
	 * 添加地址
	 * @param $param
	 * @return mixed|string
	 */
	public function addAddress($param) {
		$request = $this->request;
		if ($request->isPost()) {
			$name = $param["name"];
			$mobile = $param["mobile"];
			$address = $param["address"];
			if (!$name) {
				return $this->iFrameResponseFail(ErrorCode::ERROR, " name cant be empty");
			}
			if (!$mobile) {
				return $this->iFrameResponseFail(ErrorCode::ERROR, "mobile cant be empty");
			}
			if (!$address) {
				return $this->iFrameResponseFail(ErrorCode::ERROR, "address cant be empty");
			}
			$conWhere = " telphone like ?";
			$conData[] = "%${mobile}%";
			$addressData = Address::findOne($conWhere, $conData);
			if ($addressData) {
				return $this->iFrameResponseFail(ErrorCode::ERROR, "this mobile exist");
			}
			$conWhereUser = " mobile = ? ";
			$conDataUser[] = $mobile;
			$userData = User::findOne($conWhereUser, $conDataUser);
			if ($userData) {
				$userId = $userData["id"];
			} else {
				$userId = User::insert(["mobile" => $mobile, "username" => $name, "password" => md5($mobile)]);
			}
			$insData = ["user_id" => $userId, "accept_name" => $name, "telphone" => $mobile, "address" => $address];
			Address::insert($insData);
			return $this->iFrameResponseSuccess("success");
		}
		return $this->renderTemplate('user/add_address.php', []);
	}

	/**
	 * 编辑地址信息
	 * @param $param
	 * @return mixed|string
	 */
	public function editAddress($param) {
		$id = $param["id"];
		$conWhere = " id=? ";
		$conData[] = $id;
		$addressData = Address::findOne($conWhere, $conData);

		$request = $this->request;
		if ($request->isPost()) {
			$name = $param["name"];
			$mobile = $param["mobile"];
			$address = $param["address"];
			if (!$id) {
				return $this->iFrameResponseFail(ErrorCode::ERROR, " id cant be empty");
			}
			if (!$name) {
				return $this->iFrameResponseFail(ErrorCode::ERROR, " name cant be empty");
			}
			if (!$mobile) {
				return $this->iFrameResponseFail(ErrorCode::ERROR, "mobile cant be empty");
			}
			if (!$address) {
				return $this->iFrameResponseFail(ErrorCode::ERROR, "address cant be empty");
			}
			Address::update(["accept_name"=> "?","telphone"=> "?","address"=>"?"],"id=?",[$name,$mobile,$address,$id]);
			$userId=$addressData["user_id"];
			if ($mobile != $addressData["telphone"]) {
				User::update(["mobile"=> "?","username"=>"?","password"=>"?"],"id=?",[$mobile,$mobile,md5($mobile),$userId]);
			}
			return $this->iFrameResponseSuccess("success");
		}
		return $this->renderTemplate('user/edit_address.php', ["addressData" => $addressData]);
	}
}