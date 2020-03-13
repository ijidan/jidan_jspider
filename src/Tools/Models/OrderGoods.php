<?php
namespace Tools\Models;

use Lib\BaseModel;
use Lib\Util\CommonUtil;

/**
 * Class Order
 * @package Tools\Models
 */
class OrderGoods extends BaseModel {
	
	const IS_DEL_YES=1; //删除
	const IS_DEL_NO=0; //有效
	/**
	 * 表前缀
	 * @return string
	 */
	public function getTablePrefix() {
		return "self_";
		// TODO: Implement getTablePrefix() method.
	}

	/**
	 * 表名
	 * @return mixed
	 */
	public function getTableName() {
		return "order_goods";
		// TODO: Implement getTableName() method.
	}

	/**
	 * 主键
	 * @return string
	 */
	public function getPrimaryKey() {
		return "id";
		// TODO: Implement getPrimaryKey() method.
	}
}