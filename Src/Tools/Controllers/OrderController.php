<?php
namespace Tools\Controllers;

use Lib\BaseModel;
use Lib\ErrorCode;
use Lib\Paginate;
use Lib\Util\CommonUtil;
use Tools\Models\Order;
use Tools\Models\OrderGoods;
use Tools\Models\User;

/**
 * Class OrderController
 * @package Tools\Controllers
 */
class OrderController extends IndexController {
	/**
	 * 订单列表
	 * @param $param
	 * @return mixed
	 */
	public function showList($param) {
		$orderNo = $param["order_no"] ? trim($param["order_no"]) : "";
		$orderStatus = $param["order_status"];
		$startTime = $param["start_time"] ?: date("Y-m-d H:i:s", 0);
		$endTime = $param["end_time"] ?: date("Y-m-d H:i:s");
		$receiverName = $param["receiver_name"] ? trim($param["receiver_name"]) : "";
		$receiverMobile = $param["receiver_mobile"] ? trim($param["receiver_mobile"]) : "";
		$userMobile = $param["user_mobile"] ? trim($param["user_mobile"]) : "";
		$userId = 0;
		if ($userMobile) {
			$userData = User::findOne("username=?", [$userMobile]);
			if ($userData) {
				$userId = $userData["id"];
			} else {
				$userId = -1;
			}
		}
		//拼凑Where条件
		$conWhere = "1=1 ";
		$conData = [];
		if ($userId != 0) {
			$conWhere .= " and user_id=?";
			$conData[] = $userId;
		}
		if ($orderNo) {
			$conWhere .= " and order_no like ? ";
			$conData[] = "%${orderNo}%";
		}
		if ($startTime >= 0) {
			$conWhere .= " and create_time>=?";
			$conData[] = $startTime;
		}
		if ($endTime) {
			$conWhere .= " and create_time<=?";
			$conData[] = $endTime;
		}
		if ($receiverName) {
			$conWhere .= " and accept_name like ? ";
			$conData[] = "%${receiverName}%";
		}
		if ($receiverMobile) {
			$conWhere .= " and mobile like ? ";
			$conData[] = "%${receiverMobile}%";
		}
		$paginate = Paginate::instance($this->request);
		$orderList = Order::paginate($paginate, $conWhere, $conData, "create_time", "DESC");
		$userListById = [];
		$sortedGoodsListByOrderId = [];
		if ($orderList) {
			//用户信息
			$userIdList = array_unique(array_column($orderList, "user_id"));
			$userQuestionMark = BaseModel::buildQuestionMark($userIdList);
			$userList = User::find("id in (${userQuestionMark})", $userIdList);
			$userListById = CommonUtil::arrayGroup($userList, "id", true);
			//订单商品信息
			$orderIdList = array_unique(array_column($orderList, "id"));
			$orderGoodsQuestionMark = BaseModel::buildQuestionMark($orderList);
			$orderGoodsList = OrderGoods::find("order_id in (${orderGoodsQuestionMark})", $orderIdList);
			$orderGoodsListByOrderId = CommonUtil::arrayGroup($orderGoodsList, "order_id");
			//排序
			$sortedGoodsListByOrderId = [];
			array_walk($orderGoodsListByOrderId, function ($orderGoodsList, $orderId) use (&$sortedGoodsListByOrderId) {
				$isDelList = [];
				foreach ($orderGoodsList as $orderGoods) {
					$isDelList[] = $orderGoods["is_del"];
				}
				$sortedOrderGoodsList = $orderGoodsList;
				array_multisort($isDelList, SORT_ASC, $sortedOrderGoodsList);
				$sortedGoodsListByOrderId[$orderId] = $sortedOrderGoodsList;
			});
		}
		//		pr($sortedGoodsListByOrderId,1);
		$search = $param;
		$search["start_time"] = strtotime($startTime);
		$search["end_time"] = strtotime($endTime);
		return $this->renderTemplate("order/show_list.php", [
			"orderList"               => $orderList,
			"paginate"                => $paginate,
			"userListById"            => $userListById,
			"orderGoodsListByOrderId" => $sortedGoodsListByOrderId,
			"search"                  => $search
		]);
	}

	/**
	 * @return mixed
	 */
	public function addGoods() {
		return $this->renderTemplate("order/add_goods.php", []);
	}

	/**
	 * 编辑商品
	 * @param $param
	 * @return mixed
	 */
	public function editGoods($param) {
		$id = intval($param["id"]);
		$request = $this->request;
		if ($request->isPost()) {
			$goodsAttr = trim($param["goods_attr"]);
			$goodsWeight = floatval($param["goods_weight"]);
			$goodsPrice = floatval($param["goods_price"]);
			$goodsNum = intval($param["goods_num"]);
			$goodsFright = floatval($param["goods_freight"]);
			$interFreight = floatval($param["inter_freight"]);
			if (!$goodsAttr) {
				return $this->iFrameResponseFail(ErrorCode::ERROR, "goods attr cant be blank");
			}
			if ($goodsWeight == 0) {
				return $this->iFrameResponseFail(ErrorCode::ERROR, "goods weight must greater than zero");
			}
			if ($goodsPrice == 0) {
				return $this->iFrameResponseFail(ErrorCode::ERROR, "goods price error");
			}
			if ($goodsNum == 0) {
				return $this->iFrameResponseFail(ErrorCode::ERROR, "goods number can not be zero");
			}
			//更新
			OrderGoods::update([
				"goods_attr"    => "?",
				"goods_weight"  => "?",
				"goods_price"   => "?",
				"goods_num"     => "?",
				"goods_freight" => "?",
				"inter_freight" => "?",

			], "id=?", [$goodsAttr, $goodsWeight, $goodsPrice, $goodsNum, $goodsFright, $interFreight, $id]);
			return $this->iFrameResponseSuccess("success");
		}
		$orderGoods = OrderGoods::findOne("id=?", [$id]);
		return $this->renderTemplate("order/edit_goods.php", [
			"orderGoods" => $orderGoods
		]);
	}

	/**
	 * 取消商品
	 * @param $param
	 * @return static
	 */
	public function cancelGoods($param) {
		$orderGoodsId = $param["id"];
		OrderGoods::update(["is_del" => "?"], "id=?", [OrderGoods::IS_DEL_YES, $orderGoodsId]);
		return $this->jsonSuccess("success");
	}

	/**
	 * 激活商品
	 * @param $param
	 * @return \Lib\BaseController
	 */
	public function ActiveGoods($param) {
		$orderGoodsId = $param["id"];
		OrderGoods::update(["is_del" => "?"], "id=?", [OrderGoods::IS_DEL_NO, $orderGoodsId]);
		return $this->jsonSuccess("success");
	}

}