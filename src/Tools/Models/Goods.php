<?php
namespace Tools\Models;

use Lib\BaseModel;
use Lib\Util\CommonUtil;

/**
 * Class Goods
 * @package Tools\Models
 */
class Goods extends BaseModel {

	const WEBSITE_TAOBAO=1;  //淘宝
	const WEBSITE_TMALL=2; //天猫
	const WEBSITE_1688=3; //1688

	/**
	 * 映射
	 * @var array
	 */
	public static $websiteIdNameMap=[
		self::WEBSITE_TAOBAO=> "Taobao.com",
		self::WEBSITE_TMALL=> "Tmall.com",
		self::WEBSITE_1688=> "1688.com"
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
		return "goods";
	}

	/**
	 * 主键
	 * @return string
	 */
	public function getPrimaryKey() {
		return "id";
	}
}