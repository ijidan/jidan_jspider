<?php

namespace Tools\Models;

use Lib\BaseModel;


/**
 * 文章分类
 * Class ArticleCategory
 * @package Tools\Models
 */
class ArticleCategory extends BaseModel {

	const ARTICLE_CATEGORY_PRICE = "main_nav_price";  //报价
	const ARTICLE_CATEGORY_AGREEMENT = "main_nav_agreement";  //协议
	const ARTICLE_CATEGORY_INTRODUCTION = "main_nav_introduction"; //介绍


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
		return "article_category";
	}

	/**
	 * 主键
	 * @return string
	 */
	public function getPrimaryKey() {
		return "id";
	}

	/**
	 * 根据key查询分类
	 * @param $categoryKey
	 * @return array|mixed
	 */
	public static function findByCategoryKey($categoryKey){
		$articleCategory = self::findOne("category_key=?", [$categoryKey]);
		return $articleCategory;
	}
}