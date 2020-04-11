<?php
namespace Model\Fang;

use Lib\BaseModel;


/**
 * 资讯图片表
 * Class NewsImage
 * @package Model\Fang
 */
class NewsImage extends BaseModel {
	/**
	 * 数据库名
	 * @return mixed
	 */
	public function getDatabaseName() {
		return 'd_spider_fang';
		// TODO: Implement getDatabaseName() method.
	}

	/**
	 * 表名
	 * @return mixed
	 */
	public function getTableName() {
		return 'news_img';
		// TODO: Implement getTableName() method.
	}

	/**
	 * 表前缀
	 * @return string
	 */
	public function getTablePrefix() {
		return 't_';
		// TODO: Implement getTablePrefix() method.
	}

	/**
	 * 主键
	 * @return string
	 */
	public function getPrimaryKey() {
		return 'f_id';
		// TODO: Implement getPrimaryKey() method.
	}
}