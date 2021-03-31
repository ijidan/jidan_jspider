<?php

namespace Lib\Net;


/**
 * 数据容器
 * Class DataContainer
 * @package Lib\Net
 */
class DataContainer {

	/**
	 * 实例
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * 内部数据
	 * @var array
	 */
	private $data = [];

	/**
	 * 私有化默认构造方法，保证外界无法直接实例化
	 */
	private function __construct() {
	}

	/**
	 * 静态工厂方法，返还此类的唯一实例
	 */
	public static function getInstance() {
		if (is_null(self::$_instance)) {
			self::$_instance = new DataContainer();
		}

		return self::$_instance;
	}

	/**
	 * 设置数据
	 * @param $key
	 * @param $value
	 */
	public function setData($key, $value) {
		$this->data[$key] = $value;
	}

	/**
	 * 回去数据
	 * @param $key
	 * @return mixed|null
	 */
	public function getData($key) {
		return isset($this->data[$key]) ? $this->data[$key] : null;
	}

	/**
	 * 获取所有数据
	 * @return array
	 */
	public function getAllData() {
		return $this->data;
	}

	/**
	 * 禁止克隆
	 */
	private function __clone() {
	}

}