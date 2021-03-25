<?php

namespace Lib\Util;


/**
 * 数组工具
 * Class ArrayUtil
 * @package Lib\Util
 */
class ArrayUtil {

	/**
	 * 按某个键进hash map
	 * @param array $array 数组
	 * @param string $by_key 键
	 * @param bool $limit 是否只返回第一个
	 * @return array
	 */
	public static function arrayGroup($array, $by_key, $limit = false) {
		if (empty ($array) || !is_array($array)) {
			return $array;
		}
		$_result = array();
		foreach ($array as $item) {
			$sub_keys = array_keys($item);
			if (in_array($by_key, $sub_keys)) {
				/** @var Object | String $val */
				$val = $item[$by_key];
				$val = is_string($val) || is_int($val) ? $val : $val->__toString();
				$_result[$val][] = $item;
			} else {
				$_result[count($_result)][] = $item;
			}
		}
		if (!$limit) {
			return $_result;
		}
		$result = array();
		foreach ($_result as $key => $item) {
			$result[$key] = $item[0];
		}
		return $result;
	}


	/**
	 * 字符串转数组
	 * @param $str
	 * @param string $sep
	 * @return mixed
	 */
	public static function convertStringToArray($str, $sep = ",") {
		$arr = [];
		if ($str) {
			$arr = explode($sep, $str);
		}
		return $arr;
	}

	/**
	 * 数组转字符串
	 * @param array $arr
	 * @param string $sep
	 * @return string
	 */
	public static function convertArrayToString(array $arr, $sep = ",") {
		$str = "";
		if ($arr) {
			$str = join($sep, $arr);
		}
		return $str;
	}

	/**
	 * 数组清除
	 * @param array $arr
	 * @param string $str
	 * @return array
	 */
	public static function trimArray(array $arr, $str = "") {
		return array_map(function ($v) use ($str) {
			return $str ? trim($v, $str) : trim($v);
		}, $arr);
	}


	/**
	 * 数组转字符串数组
	 * @param array $array
	 * @return array
	 */
	public static function Array2String(array $array) {
		$convertedArray = [];
		foreach ($array as $idx => $val) {
			$convertedArray[$idx] = is_array($val) ? join(',', $val) : $val;
		}
		return $convertedArray;
	}

	/**
	 * 过滤数组字段
	 * @param array $in
	 * @param array $column
	 * @return array
	 */
	public static function filterColumn(array $in, array $column) {
		$a = array_flip($column);
		$out = array_intersect_key($in, $a);
		return $out;
	}

	/**
	 * 输出数据
	 * @param $var
	 * @param string $indent
	 * @return mixed|string
	 */
	public static function var_export54($var, $indent = "") {
		switch (gettype($var)) {
			case "string":
				return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
			case "array":
				$indexed = array_keys($var) === range(0, count($var) - 1);
				$r = [];
				foreach ($var as $key => $value) {
					$r[] = "$indent    " . ($indexed ? "" : self::var_export54($key) . " => ") . self::var_export54($value, "$indent    ");
				}
				return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
			case "boolean":
				return $var ? "TRUE" : "FALSE";
			default:
				return var_export($var, true);
		}
	}

}