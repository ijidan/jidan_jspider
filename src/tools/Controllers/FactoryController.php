<?php

namespace Tools\Controllers;

use Lib\ErrorCode;
use Lib\Paginate;
use Lib\Util\CommonUtil;
use Tools\Models\Factory;
use Tools\Models\FactoryCertificate;
use Tools\Models\FactoryProductCategory;
use Tools\Models\ForeignTradeProducts;


/**
 * 工厂管理
 * Class FactoryController
 * @package Tools\Controllers
 */
class FactoryController extends IndexController {

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
		$conWhere = "is_show=" . Factory::IS_SHOW_YES . "  ";
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
		$paginate = Paginate::instance($this->request, "page", ["page_size" => 10000]);
		$factoryList = Factory::paginate($paginate, $conWhere, $conData, "id", "DESC");
		$convertedFactoryList = Factory::convertFactoryInfoList($factoryList);
		//		pr($convertedFactoryList,1);
		return $this->renderTemplate("factory/show_factory_list.php", [
			"factoryList" => $convertedFactoryList,
			"paginate"    => $paginate,
			"search"      => $param
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
	 * 编辑工厂
	 * @param $param
	 * @return \Lib\BaseController|mixed
	 */
	public function updateFactory($param) {
		$param = CommonUtil::trimArray($param);
		$request = $this->request;
		$factoryProductCategoryList = FactoryProductCategory::getAllFactoryProductCategory();
		$factoryCertList = FactoryCertificate::getAllFactoryCertificate();
		$factoryStaffNumRange = Factory::$FACTORY_STAFF_NUM_RANGE_MAP;
		$factoryAttitudeList = Factory::$FACTORY_ATTITUDE_MAP;

		$factory_id = intval($param["factory_id"]);
		$factory_info = $factory_id ? Factory::getFactoryInfoById($factory_id) : [];
		if ($request->isPost()) {
			$factory_name = trim($param["factory_name"]);
			$factory_product_category = $param["factory_product_category"];
			$product_certificate = $param["product_certificate"];
			$factory_process_method = $param["factory_process_method"];
			$factory_process_type = $param["factory_process_type"];
			$factory_brand = $param["factory_brand"];
			$factory_website = $param["factory_website"];
			$factory_address = $param["factory_address"];
			$factory_traffic = $param["factory_traffic"];
			$is_real_factory = $param["is_real_factory"];
			$factory_situation = $param["factory_situation"];
			$factory_staff_num = $param["factory_staff_num"];
			$factory_attitude = $param["factory_attitude"];
			$factory_grade = $param["factory_grade"];
			$is_recommend = $param["is_recommend"];
			$recommend_reason = $param["recommend_reason"];
			$factory_comment = $param["factory_comment"];
			$factory_img = $param["factory_img"];
			$is_show = $param["is_show"];
			if (!$factory_name) {
				return $this->iFrameResponseFail(ErrorCode::ERROR_PARAMS, "工厂名称不能为空");
			}
			$insData = [
				"factory_name"             => $factory_name,
				"factory_product_category" => $factory_product_category ? CommonUtil::convertArrayToString($factory_product_category) : "",
				"product_certificate"      => $product_certificate ? CommonUtil::convertArrayToString($product_certificate) : "",
				"factory_process_method"   => $factory_process_method,
				"factory_process_type"     => $factory_process_type,
				"factory_brand"            => $factory_brand,
				"factory_website"          => $factory_website,
				"factory_address"          => $factory_address,
				"factory_traffic"          => $factory_traffic,
				"is_real_factory"          => $is_real_factory,
				"factory_situation "       => $factory_situation,
				"factory_staff_num"        => $factory_staff_num,
				"factory_attitude"         => $factory_attitude,
				"factory_grade"            => $factory_grade,
				"is_recommend"             => $is_recommend,
				"recommend_reason"         => $recommend_reason,
				"factory_comment"          => $factory_comment,
				"factory_img"              => $factory_img ? CommonUtil::convertArrayToString($factory_img) : "",
				"is_show"                  => $is_show
			];
			if ($factory_id) {
				$kvMap = $values = [];
				array_walk($insData, function ($value, $key) use (&$kvMap, &$values) {
					$kvMap[$key] = "?";
					array_push($values, $value);
				});
				Factory::update($kvMap, "id=" . $factory_id, $values);
			} else {
				Factory::insert($insData);
			}
			return $this->iFrameResponseSuccess("success", []);
		}
		return $this->renderTemplate('factory/update_factory.php', [
			"factory_id"                 => $factory_id,
			"factory_info"               => $factory_info,
			"factoryProductCategoryList" => $factoryProductCategoryList,
			"factoryCertList"            => $factoryCertList,
			"factoryStaffNumRange"       => $factoryStaffNumRange,
			"factoryAttitudeList"        => $factoryAttitudeList,
		]);
	}

	/**
	 * 查看详情页
	 * @param $param
	 * @return mixed
	 */
	public function showDetail($param) {
		$factoryId = $param["factory_id"];
		$factoryInfo = Factory::getFactoryInfoById($factoryId);
		return $this->renderTemplate("factory/show_detail.php", [
			"factory_id"   => $factoryId,
			"factory_info" => $factoryInfo
		]);
	}

	/**
	 * 编辑商品
	 * @param $param
	 * @return mixed
	 */
	public function editProduct($param) {
		$request = $this->request;
		$categoryList = ForeignTradeProducts::getForeignTradeProductsCategory();
		if ($request->isPost()) {
			$product_id = $param["product_id"];
			$product_category = $param["product_category"];
			$product_chinese_name = $param["product_chinese_name"];
			$product_english_name = $param["product_english_name"];
			$product_information = $param["product_information"];
			$product_urls = $param["product_urls"];
			$is_show = intval($param["is_show"]);
			$product_img = $param["product_img"];


			if (!$product_category || !isset($categoryList[$product_category])) {
				return $this->iFrameResponseFail(ErrorCode::ERROR_PARAMS, "请选择商品分类");
			}
			if (!$product_chinese_name && !$product_english_name) {
				return $this->iFrameResponseFail(ErrorCode::ERROR_PARAMS, "商品中文名称或者英文名称必须填写一个");
			}
			$filtered_product_img = array_filter($product_img, function ($value) {
				return $value;
			});
			$filtered_product_img_string = join(",", $filtered_product_img);
			$kvMap = [
				"product_category"     => "?",
				"product_chinese_name" => "?",
				"product_english_name" => "?",
				"product_information"  => "?",
				"product_urls"         => "?",
				"img_list"             => "?",
				"update_time"          => "?",
				"is_show"              => "?",
			];
			$values = [
				$product_category,
				$product_chinese_name,
				$product_english_name,
				$product_information,
				$product_urls,
				$filtered_product_img_string,
				time(),
				$is_show,
				$product_id
			];
			ForeignTradeProducts::update($kvMap, "id=?", $values);
			return $this->iFrameResponseSuccess("success", [], "/trade/showDetail?id=" . $product_id);
		}
		$id = $param["id"];
		$productInfo = $this->getProductInfo($id);
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
		$productInfo = ForeignTradeProducts::findOne("id=?", [$id]);
		$productInfoList = $this->handleProducts($productInfo);
		$productInfo = $productInfoList[0];
		return $productInfo;
	}

}