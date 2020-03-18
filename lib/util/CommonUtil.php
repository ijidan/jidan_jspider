<?php

namespace Lib\Util;

use Endroid\QrCode\QrCode;
use Exception;
use Mobile_Detect;

/**
 * 通用工具
 * Class CommonUtil
 * @package Lib\Util
 */
class CommonUtil {

	/**
	 * 验证密码
	 * @param $input
	 * @param $hash
	 * @return bool
	 */
	public static function checkPassword($input, $hash) {
		//兼容旧的数据，旧的数据使用md5加密
		if (md5($input) == $hash) {
			return true;
		}
		return password_verify($input, $hash);
	}

	/**
	 * 根据用户输入生成hash
	 * @param $password
	 * @return bool|string
	 */
	public static function hashPassword($password) {
		return password_hash($password, PASSWORD_BCRYPT);
	}

	/**
	 * @param $input
	 * @return bool|string
	 */
	public static function base64_url_decode($input) {
		return base64_decode(strtr($input, '-_', '+/'));
	}

	/**
	 * @param $input
	 * @return string
	 */
	public static function base64_url_encode($input) {
		return strtr(base64_encode($input), '+/', '-_');
	}

	/**
	 * 百分比
	 * @param $num
	 * @param $base
	 * @param bool $showNum
	 * @return string
	 */
	public static function showPercent($num, $base, $showNum = true) {
		if (empty($base)) {
			return '--';
		}
		$str = number_format($num / $base * 100, 2) . "%";
		if ($showNum) {
			$str .= " <span style=\"color:#999999;\">({$num}/{$base})</span>";
		}
		return $str;
	}

	/**
	 * @param $num
	 * @param int $precision
	 * @return float
	 */
	public static function numberFormat($num, $precision = 2) {
		$num = (float)$num;
		return round($num, $precision);
	}

	/**
	 * 玩家金币做整数处理(舍去小数位)
	 * @param $gold
	 * @return float
	 */
	public static function goldFormat($gold) {
		return intval($gold);
	}

	/**
	 * 用于多语言字符串替换
	 * @param $str
	 * @param array $replace
	 * @return mixed
	 */
	public static function replace($str, array $replace) {
		$keys = []; //需要替换的key
		$vals = []; //替换的值
		foreach ($replace as $key => $val) {
			$keys[] = '{{' . $key . '}}';
			$vals[] = $val;
		}
		return str_replace($keys, $vals, $str);
	}

	/**
	 * 读取PHP文件，返回PHP54格式（短格式）的字符串
	 * @param $file
	 * @return string
	 */
	public static function convertArraysToSquareBrackets($file) {
		$code = file_get_contents($file);
		$out = '';
		$brackets = [];
		$tokens = token_get_all($code);
		for ($i = 0; $i < count($tokens); $i++) {
			$token = $tokens[$i];
			if ($token === '(') {
				$brackets[] = false;
			} elseif ($token === ')') {
				$token = array_pop($brackets) ? ']' : ')';
			} elseif (is_array($token) && $token[0] === T_ARRAY) {
				$a = $i + 1;
				if (isset($tokens[$a]) && $tokens[$a][0] === T_WHITESPACE) {
					$a++;
				}
				if (isset($tokens[$a]) && $tokens[$a] === '(') {
					$i = $a;
					$brackets[] = true;
					$token = '[';
				}
			}
			$out .= is_array($token) ? $token[1] : $token;
		}
		return $out;
	}

	/**
	 * 数组格式化成PHP54的Array
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
	 * 生成二维码
	 * @param $url
	 * @param int $size
	 * @param string $label
	 * @return QrCode
	 * @throws \Endroid\QrCode\Exceptions\ImageTypeInvalidException
	 */
	public static function gen($url, $size = 100, $label = "") {

		$qrCode = new QrCode();
		$qrCode->setText($url)->setSize($size)->setPadding(10)->setErrorCorrection('high')->setForegroundColor(array(
			'r' => 0,
			'g' => 0,
			'b' => 0,
			'a' => 0
		))->setBackgroundColor(array(
			'r' => 255,
			'g' => 255,
			'b' => 255,
			'a' => 0
		))->setLabelFontSize(12)->setImageType(QrCode::IMAGE_TYPE_PNG);
		if ($label) {
			$qrCode->setLabel($label);
		}
		return $qrCode;
	}

	/**
	 * 根域名下根据浏览器做跳转
	 */
	public static function checkMobile() {
		$host = $_SERVER["HTTP_HOST"];
		$requestUri = $_SERVER["REQUEST_URI"];
		$domain = CommonUtil::getUrlToDomain($host);
		if ($domain && preg_match("/^${domain}/", $host)) {
			$struct = Config::loadConfig('struct');
			if (self::isMobile()) {
				if (strpos($requestUri, "invite?channel=") !== false) {
					$requestUri = str_replace("invite?channel=", "invite?channel=", $requestUri);
				}
				$jumpUrl = rtrim($struct["mobile_url"], "/") . $requestUri;
			} else {
				$jumpUrl = rtrim($struct["server_url"], "/") . $requestUri;
			}
			header("Location:${jumpUrl}");
			exit();
		}
	}

	/**
	 * 判断是否访问手机版
	 * @return bool
	 */
	public static function isMobile() {
		if (defined("IS_MOBILE") && constant("IS_MOBILE")) {
			return true;
		}
		$detect = new Mobile_Detect();
		$isMobile = $detect->isMobile();
		$isTablet = $detect->isTablet();
		return $isTablet || $isMobile;
	}

	/**
	 * 获取域名
	 * @param $url
	 * @return string
	 */
	public static function getUrlToDomain($url) {
		$ret = tld_extract($url);
		return $ret->getRegistrableDomain();
	}

	/**
	 * 获取URL里的JSON内容 (第三方源)
	 * @param $url
	 * @param array $query
	 * @return mixed
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public static function getJSONContent($url, $query = []) {
		$httpClient = new \GuzzleHttp\Client();
		$response = $httpClient->request('GET', $url, [
			'http_errors' => false,
			'query'       => $query,
			'timeout'     => 60
		]);

		$result = json_decode($response->getBody()->getContents(), true);
		if (isset($result['result']['error'])) {
			throw new Exception($result['result']['error']);
		}
		if (json_last_error() != JSON_ERROR_NONE) {
			throw new Exception(json_last_error_msg());
		}

		return $result['data'] ?: [];
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
	 * 提取字符串
	 * @param string $str
	 * @return string
	 */
	public static function extractNumber($str = '') {
		$str = trim($str);
		if (empty($str)) {
			return '';
		}
		$temp = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '0');
		$result = '';
		for ($i = 0; $i < strlen($str); $i++) {
			if (in_array($str[$i], $temp)) {
				$result .= $str[$i];
			}
		}
		return intval($result);
	}

	/**
	 * 填充零
	 * @param $num
	 * @param int $length
	 * @return string
	 */
	public static function fillZero($num, $length = 4) {
		return sprintf("%0${length}d", $num);
	}

	/**
	 * 去除空白
	 * @param $str
	 * @return mixed
	 */
	public static function removeSpace($str) {
		return str_replace(" ", "", $str);
	}

	/**
	 * 数组转字符串数组
	 * @param array $array
	 * @return array
	 */
	public static function Array2String(array $array){
		$convertedArray=[];
		foreach ($array as $idx=>$val){
			$convertedArray[$idx]=is_array($val)?join(',',$val):$val;
		}
		return $convertedArray;
	}
}