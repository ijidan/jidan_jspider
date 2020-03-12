<?php
namespace Tools\Models;

use Lib\BaseModel;
use Lib\Util\CommonUtil;

/**
 * 关键字翻译
 * Class TransSearchKeywords
 * @package Tools\Models
 */
class TransSearchKeywords extends BaseModel {
	

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
		return "trans_search_keywords";
	}

	/**
	 * 主键
	 * @return string
	 */
	public function getPrimaryKey() {
		return "id";
	}
}