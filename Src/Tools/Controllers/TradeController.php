<?php

namespace Tools\Controllers;

use Lib\ErrorCode;
use Lib\Paginate;
use Lib\Util\CommonUtil;
use Tools\Models\ForeignTradeProducts;

/**
 * 外贸商品管理
 * Class tradeProductsController
 * @package Tools\Controllers
 */
class TradeController extends IndexController {

	/**
	 * 查看分类列表
	 * @return mixed
	 */
	public function showCategoryList() {
		$categoryList = ForeignTradeProducts::getForeignTradeProductsCategory();
		return $this->renderTemplate("trade/show_category_list.php", [
			"categoryList" => $categoryList,
		]);
	}

	/**
	 * 订单列表
	 * @param $param
	 * @return mixed
	 */
	public function showList($param) {
		$goodsId = $param["goods_id"];
		$goodsName = $param["goods_name"];
		$startPrice = floatval($param["start_price"]);
		$endPrice = floatval($param["end_price"]);
		$websiteId = intval($param["website_id"]);
		$websiteGoodsId = $param["website_goods_id"];
		$conWhere = "is_show=" . ForeignTradeProducts::IS_SHOW_YES . "  ";
		$conData = [];
		/*		if($goodsId){
			$conWhere.=" and id like ?";
			$conData[]="%${goodsId}%";
		}
		if($goodsName){
			$conWhere.=" and name like ?";
			$conData[]="%${goodsName}%";
		}
		if($startPrice){
			$conWhere.=" and sell_price >= ?";
			$conData[]=$startPrice;
		}
		if($endPrice){
			$conWhere.=" and sell_price <= ?";
			$conData[]=$endPrice;
		}
		if($websiteId && in_array($websiteId,[Goods::WEBSITE_1688,Goods::WEBSITE_TAOBAO,Goods::WEBSITE_TMALL])){
			$conWhere.=" and website_id= ?";
			$conData[]=$websiteId;
			if($websiteGoodsId){
				$conWhere.=" and website_goods_id=?";
				$conData[]=$websiteGoodsId;
			}
		}
		*/
		$goodsCategory = ForeignTradeProducts::getForeignTradeProductsCategory();
		$paginate = Paginate::instance($this->request, "page", ["page_size" => 10000]);
		$goodsList = ForeignTradeProducts::paginate($paginate, $conWhere, $conData);
		$goodsList = $this->handleProducts($goodsList);
		$goodsListByCategory = CommonUtil::arrayGroup($goodsList, "product_category");
		return $this->renderTemplate("trade/show_product_list.php", [
			"goodsCategory"       => $goodsCategory,
			"goodsListByCategory" => $goodsListByCategory,
			"goodsList"           => $goodsList,
			"paginate"            => $paginate,
			"search"              => $param
		]);
	}

	/**
	 * 处理商品
	 * @param array $goodsList
	 * @return array
	 */
	private function handleProducts(array $goodsList) {
		//判断是一维数组还是二维数组
		if (count($goodsList) == count($goodsList, COUNT_RECURSIVE)) {
			$goodsList = [$goodsList];
		}
		array_walk($goodsList, function (&$value, $key) {
			//商品图片处理
			$productImgList = $value["img_list"];
			$productImgArr = $productImgList ? explode(",", $productImgList) : [];
			$productFirstImg = $productImgArr ? $productImgArr[0] : "";
			$value["product_img_list"] = $productImgArr;
			$value["product_first_img"] = $productFirstImg;

			//商品链接处理
			$productUrlList = $value["product_urls"];
			$productUrlArr = $productUrlList ? explode(",", $productUrlList) : [];
			$value["product_url_list"] = $productUrlArr;

		});
		return $goodsList;
	}

	/**
	 * 添加商品
	 * @param $param
	 * @return \Lib\BaseController|mixed
	 */
	public function addProduct($param) {
		$request = $this->request;
		$categoryList = ForeignTradeProducts::getForeignTradeProductsCategory();
		if ($request->isPost()) {

			$product_category = $param["product_category"];
			$product_chinese_name = $param["product_chinese_name"];
			$product_english_name = $param["product_english_name"];
			$product_information = $param["product_information"];
			$product_urls = $param["product_urls"];
			$is_show = intval($param["is_show"]);
			$product_img = $param["product_img"];
			$product_no = $this->genProductNo($product_category);

			if (!$product_category || !isset($categoryList[$product_category])) {
				return $this->iFrameResponseFail(ErrorCode::ERROR_PARAMS, "请选择商品分类");
			}
			if (!$product_chinese_name && !$product_english_name) {
				return $this->iFrameResponseFail(ErrorCode::ERROR_PARAMS, "商品中文名称或者英文名称必须填写一个");
			}
			$insData = [
				"product_no"           => $product_no,
				"product_category"     => $product_category,
				"product_chinese_name" => $product_chinese_name,
				"product_english_name" => $product_english_name,
				"product_information"  => $product_information,
				"product_urls"         => $product_urls,
				"img_list"             => $product_img,
				"create_time"          => time(),
				"update_time"          => time(),
				"is_show"              => $is_show
			];
			ForeignTradeProducts::insert($insData);
			return $this->iFrameResponseSuccess("success", [], "/trade/showList");
		}
		return $this->renderTemplate('trade/add_product.php', [
			"categoryList" => $categoryList
		]);
	}

	/**
	 * 查看详情页
	 * @param $param
	 * @return mixed
	 */
	public function showDetail($param) {
		$id = $param["product_id"];
		$productInfo = $this->getProductInfo($id);
		$categoryList = ForeignTradeProducts::getForeignTradeProductsCategory();

		return $this->renderTemplate("trade/show_detail.php", [
			"productInfo"  => $productInfo,
			"categoryList" => $categoryList
		]);
	}

	/**
	 * 编辑商品
	 * @param $param
	 * @return mixed
	 */
	public function editProduct($param) {
		$request = $this->request;
		$param = CommonUtil::trimArray($param);
		$product_id = $param["product_id"];

		$categoryList = ForeignTradeProducts::getForeignTradeProductsCategory();
		if ($request->isPost()) {
			$product_model_no = $param["product_model_no"];
			$product_category = $param["product_category"];
			$product_chinese_name = $param["product_chinese_name"];
			$product_english_name = $param["product_english_name"];

			$product_item_price = $param["product_item_price"];
			$product_function = $param["product_function"];
			$product_main_features = $param["product_main_features"];
			$product_item_specifics = $param["product_item_specifics"];
			$product_description = $param["product_description"];
			$product_packing_information = $param["product_packing_information"];

			$product_img = $param["product_img"];
			$product_urls = $param["product_urls"];
			$is_show = intval($param["is_show"]);


			if (!$product_category || !isset($categoryList[$product_category])) {
				return $this->iFrameResponseFail(ErrorCode::ERROR_PARAMS, "请选择商品分类");
			}
			if (!$product_chinese_name && !$product_english_name) {
				return $this->iFrameResponseFail(ErrorCode::ERROR_PARAMS, "商品中文名称或者英文名称必须填写一个");
			}
			$filtered_product_img = array_filter($product_img, function ($value) {
				return $value;
			});

			$insData = [
				"product_model_no"     => $product_model_no,
				"product_category"     => $product_category,
				"product_chinese_name" => $product_chinese_name,
				"product_english_name" => $product_english_name,

				"product_item_price"          => $product_item_price,
				"product_function"            => $product_function,
				"product_main_features"       => $product_main_features,
				"product_item_specifics"      => $product_item_specifics,
				"product_description"         => $product_description,
				"product_packing_information" => $product_packing_information,

				"product_img"  => $filtered_product_img ? CommonUtil::convertArrayToString($filtered_product_img) : "",
				"product_urls" => $product_urls,
				"is_show"      => $is_show,
			];

			if ($product_id) {
				$kvMap = $values = [];
				array_walk($insData, function ($value, $key) use (&$kvMap, &$values) {
					$kvMap[$key] = "?";
					array_push($values, $value);
				});
				ForeignTradeProducts::update($kvMap, "id=" . $product_id, $values);
			} else {
				ForeignTradeProducts::insert($insData);
			}
			return $this->iFrameResponseSuccess("success", []);
		}
		$productInfo = $product_id ?$this->getProductInfo($product_id):[];
		return $this->renderTemplate("trade/edit_product.php", [
			"productInfo"  => $productInfo,
			"categoryList" => $categoryList
		]);
	}

	/**
	 * 获取商品信息
	 * @param $id
	 * @return array|mixed
	 */
	private function getProductInfo($id) {
		$productInfo = ForeignTradeProducts::findOne("id=" . $id);
//		$productInfoList = $this->handleProducts($productInfo);
//		$productInfo = $productInfoList[0];
		return $productInfo;
	}

	/**
	 * 获取商品编号
	 * @param $productCategory
	 * @return string
	 */
	private function genProductNo($productCategory) {
		//当前产品编号
		$productInfoList = ForeignTradeProducts::findOne("product_category = ?", [$productCategory], $order = "id", $orderType = "DESC");
		if ($productInfoList) {
			$currentProductNo = $productInfoList["product_no"];

			//产品编号前缀
			$productCategoryPrefix3 = strtoupper(substr($currentProductNo, 0, 3));
			//当前产品序号
			$currentProductNoNumber = intval(str_replace($productCategoryPrefix3, "", $currentProductNo));
			$nextProductNoNumber = $currentProductNoNumber + 1;
		} else {
			$productCategoryPrefix3 = "P" . strtoupper(substr($productCategory, 0, 2));
			$nextProductNoNumber = 1;
		}
		//下一个产品编号
		$nextProductNo = $productCategoryPrefix3 . \str_pad($nextProductNoNumber, 3, "0", STR_PAD_LEFT);
		return $nextProductNo;
	}
}