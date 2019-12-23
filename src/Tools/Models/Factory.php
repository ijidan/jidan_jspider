<?php

namespace Tools\Models;

use Lib\BaseModel;
use Lib\Util\CommonUtil;


/**
 * 工厂
 * Class Factory
 * @package Tools\Models
 */
class Factory extends BaseModel {

	const IS_SHOW_YES = 1; //显示
	const IS_SHOW_NO = 0;  //不显示

	const IS_REAL_FACTORY_YES = 1; //是工厂
	const IS_REAL_FACTORY_NO = 2;//不是工厂
	const IS_REAL_FACTORY_UNKNOWN = 3;//未知，不确定是不是工厂

	const IS_RECOMMEND_YES = 1; //推荐
	const IS_RECOMMEND_NO = 0;  //不推荐

	/**
	 * 是否工厂映射
	 * @var array
	 */
	public static $IS_REAL_FACTORY_ID_DESC_MAP = [
		self::IS_REAL_FACTORY_YES     => "是",
		self::IS_REAL_FACTORY_NO      => "否",
		self::IS_REAL_FACTORY_UNKNOWN => "未知，不确定"
	];

	/**
	 * 是否推荐映射
	 * @var array
	 */
	public static $IS_RECOMMEND_ID_DESC_MAP = [
		self::IS_RECOMMEND_YES => "是",
		self::IS_RECOMMEND_NO  => "否"
	];

	/**
	 * 工厂人数
	 * @return array
	 */
	public static $FACTORY_STAFF_NUM_RANGE_MAP = [
		"0_10"          => "0-10人",
		"10_20"         => "10-20人",
		"20_50"         => "20-50人",
		"50_100"        => "50-100人",
		"100_500"       => "50-100人",
		"500_1000"      => "50-1000人",
		"1000_10000"    => "1000-10000人",
		"10000_1000000" => "10000人以上"
	];

	/**
	 * 工厂态度
	 * @return array
	 */
	public static $FACTORY_ATTITUDE_MAP = [
		"1"  => "恶言恶语",
		"2"  => "态度极差",
		"3"  => "态度很差",
		"4"  => "态度较差",
		"5"  => "态度冷淡",
		"6"  => "态度一般",
		"7"  => "态度还行",
		"8"  => "态度较好",
		"9"  => "态度很好",
		"10" => "态度热情",
		"11" => "非常热情",
		"12" => "开车接送",
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
		return "factory";
	}

	/**
	 * 主键
	 * @return string
	 */
	public function getPrimaryKey() {
		return "id";
	}

	/**
	 * 外贸商品分类
	 * @return array
	 */
	public static function getForeignTradeProductsCategory() {
		return [
			"electronic_products"      => "电子产品",
			"plastic_products"         => "塑料制品",
			"toys_products"            => "玩具产品",
			"maternal_infant_products" => "母婴用品",
		];
	}

	/**
	 * 获取工厂信息
	 * @param int $id
	 * @return array
	 */
	public static function getFactoryInfoById($id) {
		$factoryInfo = Factory::findOne("id=" . $id);
		return self::convertFactoryInfo($factoryInfo);
	}

	/**
	 * 构造数据
	 * @param $factoryInfo
	 * @return mixed
	 */
	public static function convertFactoryInfo($factoryInfo) {
		if (!$factoryInfo) {
			return [];
		}

		$factoryProductCategory = $factoryInfo["factory_product_category"];
		$productCertificate = $factoryInfo["product_certificate"];
		$factoryProductCategoryArray = CommonUtil::convertStringToArray($factoryProductCategory);

		//产品分类
		$allFactoryProductCategory = FactoryProductCategory::getAllFactoryProductCategory();
		$factoryProductCategoryNameMap = [];
		array_walk($factoryProductCategoryArray, function ($v, $k) use (&$factoryProductCategoryNameMap, $allFactoryProductCategory) {
			$factoryProductCategoryNameMap[$v] = $allFactoryProductCategory[$v];
		});
		$factoryInfo["factory_product_category_array"] = CommonUtil::convertStringToArray($factoryProductCategory);
		$factoryInfo["factory_product_category_map"] = $factoryProductCategoryNameMap;

		$factoryProductCategoryNameArray = array_values($factoryProductCategoryNameMap);
		$factoryInfo["factory_product_category_name"] = CommonUtil::convertArrayToString($factoryProductCategoryNameArray);
		$factoryInfo["factory_product_category_name_array"] = $factoryProductCategoryNameArray;

		//认证
		$allFactoryCertificate = FactoryCertificate::getAllFactoryCertificate();
		$factoryInfo["product_certificate_array"] = CommonUtil::convertStringToArray($productCertificate);
		$factoryInfo["product_certificate_map"] = $allFactoryCertificate;
		$productCertificateNameArray = array_values($allFactoryCertificate);;
		$factoryInfo["product_certificate_name"] = CommonUtil::convertArrayToString($productCertificateNameArray);
		$factoryInfo["product_certificate_name_array"] = $productCertificateNameArray;

		//员工人数
		$factoryInfo["factory_staff_num_name"] = self::$FACTORY_STAFF_NUM_RANGE_MAP[trim($factoryInfo["factory_staff_num"])];

		//是否工厂
		$factoryInfo["is_real_factory_name"] = self::$IS_REAL_FACTORY_ID_DESC_MAP[trim($factoryInfo["is_real_factory"])];

		//是否推荐
		$factoryInfo["is_recommend_name"] = self::$IS_RECOMMEND_ID_DESC_MAP[trim($factoryInfo["is_recommend"])];

		//		dump($factory_staff_num,self::$FACTORY_STAFF_NUM_RANGE,$factoryInfo,1);
		return $factoryInfo;

	}

	/**
	 * 批量构造数据
	 * @param array $factoryInfoList
	 * @return array
	 */
	public static function convertFactoryInfoList(array $factoryInfoList) {
		$convertedFactoryInfoList = [];
		array_walk($factoryInfoList, function ($factoryInfo, $idx) use (&$convertedFactoryInfoList) {
			$convertedFactoryInfoList[$idx] = self::convertFactoryInfo($factoryInfo);
		});
		return $convertedFactoryInfoList;
	}
}