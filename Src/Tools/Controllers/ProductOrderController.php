<?php
namespace Tools\Controllers;

use GatewayWorker\Lib\Gateway;
use Lib\ErrorCode;
use Lib\Paginate;
use Lib\Util\Config;
use Tools\Models\OrderGoods;
use Tools\Models\ProductOrder;
use Tools\Models\SocketBusiness;
use Tools\Models\User;


/**
 * 商品订单
 * Class ProductOrderController
 * @package Tools\Controllers
 */
class ProductOrderController extends IndexController {

	/**
	 * 订单列表
	 * @param $param
	 * @return mixed
	 */
	public function showList($param) {
//		$data = [
//			"event_id"   => SocketBusiness::EVENT_SOCKET_SEND_MSG,
//			"event_data" => [
//				"service_id" => 2,
//				"message"    => "XXX"
//			]
//		];
//		$socketConfig=Config::loadConfig("socket");
//		$registerAddress=$socketConfig["register_address"];
//		Gateway::$registerAddress=$registerAddress;
//		Gateway::sendToAll(\json_encode($data));
//		dump("1",1);

		$paginate = Paginate::instance($this->request);
		$orderList = ProductOrder::paginate($paginate, "is_del=? and deal_status=?", [
			ProductOrder::IS_DEL_NO,
			ProductOrder::DEAL_STATUS_INIT
		], "id", "ASC");
		$this->appendUserInfo($orderList);
		return $this->renderTemplate("product_order/show_list.php", [
			"orderList" => $orderList,
			"paginate"  => $paginate,
		]);
	}

	/**
	 * 追加手机号码
	 * @param array $orderList
	 */
	private function appendUserInfo(array &$orderList) {
		if ($orderList) {
			$userIdList = array_column($orderList, "user_id");
			$userListById = User::findUserByIdList($userIdList);
			array_walk($orderList, function (&$order) use ($userListById) {
				$userId = $order["user_id"];
				$user = $userListById[$userId];
				$userMobile = $user["mobile"];
				$order["user_mobile"] = $userMobile;
			});
		}
	}

	/**
	 * 已经购买的订单
	 * @return mixed
	 */
	public function showBoughtList() {
		$paginate = Paginate::instance($this->request);
		$orderList = ProductOrder::paginate($paginate, "deal_status=?", [ProductOrder::DEAL_STATUS_BOUGHT], "id", "ASC");
		$this->appendUserInfo($orderList);
		return $this->renderTemplate("product_order/show_bought_list.php", [
			"orderList" => $orderList,
			"paginate"  => $paginate,
		]);
	}

	/**
	 * 已经取消的订单
	 * @return mixed
	 */
	public function showCanceledBoughtList() {
		$paginate = Paginate::instance($this->request);
		$orderList = ProductOrder::paginate($paginate, "deal_status=?", [ProductOrder::DEAL_STATUS_CANCEL], "id", "ASC");
		$this->appendUserInfo($orderList);
		return $this->renderTemplate("product_order/show_canceled_bought_list.php", [
			"orderList" => $orderList,
			"paginate"  => $paginate,
		]);
	}

	/**
	 * 删除商品列表
	 * @return mixed
	 */
	public function showDeletedList() {
		$paginate = Paginate::instance($this->request);
		$orderList = ProductOrder::paginate($paginate, "is_del=?", [ProductOrder::IS_DEL_YES], "id", "ASC");
		$this->appendUserInfo($orderList);
		return $this->renderTemplate("product_order/show_deleted_list.php", [
			"orderList" => $orderList,
			"paginate"  => $paginate,
		]);
	}

	/**
	 * 重新激活
	 * @param $param
	 * @return \Lib\BaseController
	 */
	public function reactiveDeleted($param) {
		$id = $param["id"];
		ProductOrder::update(["is_del" => "?"], "id=?", [ProductOrder::IS_DEL_NO, $id]);
		return $this->jsonSuccess("success");
	}

	/**
	 * 显示详情
	 * @param $param
	 * @return mixed
	 */
	public function addProduct($param) {
		$request = $this->request;
		if ($request->isPost()) {
			$insData = $this->getInsData($param);
			ProductOrder::insert($insData);
			return $this->iFrameResponseSuccess("success");
		}
		return $this->renderTemplate("product_order/add_product.php", []);
	}

	/**
	 * 显示详情
	 * @param $param
	 * @return mixed
	 */
	public function showDetail($param) {
		$id = $param["id"];
		$productOrder = ProductOrder::findOne("id=?", [$id]);
		return $this->renderTemplate("product_order/show_detail.php", ["productOrder" => $productOrder]);
	}

	/**
	 * 编辑
	 * @param $param
	 * @return mixed
	 */
	public function editDetail($param) {
		$id = $param["id"];
		$request = $this->request;
		if ($request->isPost()) {
			$updateKey = $updateValue = [];
			$this->getUpdateData($param, $updateKey, $updateValue);
			$updateValue[] = $id;
			ProductOrder::update($updateKey, "id=?", $updateValue);
			return $this->iFrameResponseSuccess("success");
		}
		$productOrder = ProductOrder::findOne("id=?", [$id]);
		return $this->renderTemplate("product_order/edit_detail.php", ["productOrder" => $productOrder, "id" => $id]);
	}

	/**
	 * 删除
	 * @param $param
	 * @return string
	 */
	public function delete($param) {
		$id = $param["id"];
		ProductOrder::update(["is_del" => "?"], "id=?", [ProductOrder::IS_DEL_YES, $id]);
		return $this->jsonSuccess("success");
	}

	/**
	 * 已经下单
	 * @param $param
	 * @return \Lib\BaseController
	 */
	public function bought($param) {
		$id = $param["id"];
		ProductOrder::update(["deal_status" => "?"], "id=?", [ProductOrder::DEAL_STATUS_BOUGHT, $id]);
		return $this->jsonSuccess("success");
	}

	/**
	 * 取消已经购买的订单
	 * @param $param
	 * @return \Lib\BaseController
	 */
	public function cancelBought($param) {
		$id = $param["id"];
		ProductOrder::update(["deal_status" => "?"], "id=?", [ProductOrder::DEAL_STATUS_CANCEL, $id]);
		return $this->jsonSuccess("success");
	}


	/**
	 * 获取插入数据
	 * @param array $param
	 * @return array
	 */
	private function getInsData(array $param) {
		$insData = [];
		$columns = ProductOrder::getColumns();
		foreach ($param as $key => $value) {
			if (in_array($key, $columns)) {
				$insData[$key] = $value;
			}
		}
		$productImg = "";
		if (isset($insData["product_img"]) && $insData["product_img"]) {
			$productImg = join(",", $insData["product_img"]);
		}
		$insData["product_img"] = $productImg;
		return $insData;
	}

	/**
	 * @param array $param
	 * @param array $updateKey
	 * @param array $updateValue
	 */
	private function getUpdateData(array $param, array &$updateKey, array &$updateValue) {
		$insData = $this->getInsData($param);
		foreach ($insData as $key => $value) {
			$updateKey[$key] = "?";
			$updateValue[] = $value;
		}
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