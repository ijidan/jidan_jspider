<?php
namespace Tools\Models;

use Lib\BaseModel;
use Lib\Util\CommonUtil;

/**
 *产品订单
 * Class ProductOrder
 * @package Tools\Models
 */
class ProductOrder extends BaseModel {

	const IS_DEL_YES = 1; //删除
	const IS_DEL_NO = 0; //有效

	const DEAL_STATUS_INIT = 0; //未处理
	const DEAL_STATUS_BOUGHT = 1; //已经下单
	const DEAL_STATUS_CANCEL = 4; //订单取消

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
		return "product_order";
	}

	/**
	 * 主键
	 * @return string
	 */
	public function getPrimaryKey() {
		return "id";
	}

	/**
	 * 列
	 * @return array
	 */
	public static function getColumns() {
		return [
			"product_name",
			"product_attr",
			"product_link",
			"product_img",
			"product_price",
			"product_num",
			"cn_freight",
			"cn_vn_freight",
			"vn_freight",
			"buy_fee",
			"search_fee",
			"insure_fee",
		];
	}
}