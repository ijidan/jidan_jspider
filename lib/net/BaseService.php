<?php

namespace Lib\Net;


use Lib\Util\Config;

/**
 * 基础服务类
 * Class BaseService
 * @package Service
 */
class BaseService {

	const MAX_LIMIT = 10000;

	const SERVICE_ORDER_TYPE_ASC = 1; //升序
	const SERVICE_ORDER_TYPE_DESC = 2; //降序


	/**
	 * GET请求
	 * @param string $host
	 * @param array $param
	 * @param array $requestConfig
	 * @return \GuzzleHttp\Psr7\Response|\Lib\Net\Response|mixed|null|\Psr\Http\Message\ResponseInterface
	 * @throws \Exception
	 */
	public static function sendGetRequest($host = "", array $param = [], array $requestConfig = []) {
		return self::sendRequest(Request::METHOD_GET, $host, $param, $requestConfig);
	}

	/**
	 * POST 请求
	 * @param string $host
	 * @param array $param
	 * @param array $requestConfig
	 * @return \GuzzleHttp\Psr7\Response|\Lib\Net\Response|mixed|null|\Psr\Http\Message\ResponseInterface
	 * @throws \Exception
	 */
	public static function sendPostRequest($host = "", array $param = [], array $requestConfig = []) {
		return self::sendRequest(Request::METHOD_POST, $host, $param, $requestConfig);
	}

	/**
	 * POST JSON请求
	 * @param string $host
	 * @param array $param
	 * @param array $requestConfig
	 * @return \GuzzleHttp\Psr7\Response|\Lib\Net\Response|mixed|null|\Psr\Http\Message\ResponseInterface
	 * @throws \Exception
	 */
	public static function sendPostJsonRequest($host = "", array $param = [], array $requestConfig = []) {
		return self::sendRequest(Request::METHOD_POST_JSON, $host, $param, $requestConfig);
	}

	/**
	 * 文件上传
	 * @param string $host
	 * @param string $filePath
	 * @param string $name
	 * @param array $requestConfig
	 * @return \GuzzleHttp\Psr7\Response|Response|\Psr\Http\Message\ResponseInterface|null
	 * @throws \Exception
	 */
	public static function sendFile($host = "", $filePath = "",  $name='upfile',array $requestConfig = []) {
		$content = fopen($filePath, 'r');
		$param = [
			'name'     => $name,
			'contents' => $content
		];
		/** @var Request $request */
		$request = Request::instance($requestConfig);
		$request->setUrl($host);
		$request->setParams($param);
		return $request->sendFile();
	}

	/**
	 * 保存房源信息
	 * @param $host
	 * @param $roomName
	 * @param $houseInfo
	 * @param array $requestConfig
	 * @return \GuzzleHttp\Psr7\Response|Response|mixed|\Psr\Http\Message\ResponseInterface|null
	 * @throws \Exception
	 */
	public static function saveHouse($host,$roomName,$houseInfo,array $requestConfig = []){
		return self::sendPostJsonRequest($host,$houseInfo);
	}

	/**
	 * 下载文件
	 * @param $host
	 * @param string $destPath
	 * @param array $requestConfig
	 * @return Response|null
	 * @throws \Exception
	 */
	public static function getFile($host,$destPath="/tmp/",array $requestConfig = []){
		/** @var Request $request */
		$request = Request::instance($requestConfig);
		$request->setUrl($host);
		$request->setParams(['dest_path'=> $destPath]);
		return $request->getFile();
	}

	/**
	 * PUT请求
	 * @param string $host
	 * @param array $params
	 * @param array $requestConfig
	 * @return \GuzzleHttp\Psr7\Response|\Lib\Net\Response|mixed|null|\Psr\Http\Message\ResponseInterface
	 * @throws \Exception
	 */
	public static function sendPutRequest($host = "", array $params = [], array $requestConfig = []) {
		return self::sendRequest(Request::METHOD_PUT, $host, $params, $requestConfig);
	}

	/**
	 * send request
	 * @param $method
	 * @param string $host
	 * @param array $param
	 * @param array $requestConfig
	 * @return \GuzzleHttp\Psr7\Response|\Lib\Net\Response|mixed|null|\Psr\Http\Message\ResponseInterface
	 * @throws \Exception
	 */
	private static function sendRequest($method, $host = "", array $param = [], array $requestConfig = []) {
		/** @var Request $request */
		$request = Request::instance($requestConfig);
		$request->setMethod($method);
		$request->setUrl($host);
		$request->setParams($param);
		return $request->send();
	}

	/**
	 * 查询
	 * @param $configKey
	 * @param array $params
	 * @param array $requestConfig
	 * @return array
	 * @throws \Exception
	 * @internal param $keyItem
	 */
	protected static function doGetRequest($configKey, array $params, array $requestConfig = []) {
		$host = self::getRequestHost($configKey);
		$response = self::sendGetRequest($host, $params, $requestConfig);
		if ($response->fail()) {
			return [];
		}
		$resData = $response->getData();
		return $resData;
	}

	/**
	 * POST请求
	 * @param $configKey
	 * @param array $params
	 * @param array $requestConfig
	 * @return null
	 * @throws \Exception
	 */
	protected static function doPostRequest($configKey, array $params, array $requestConfig = []) {
		$host = self::getRequestHost($configKey);
		$response = self::sendPostRequest($host, $params, $requestConfig);
		if ($response->fail()) {
			throw new \RuntimeException($response->getMessage());
		}
		$resData = $response->getData();
		return $resData;
	}

	/**
	 * PUT请求
	 * @param $configKey
	 * @param array $params
	 * @param array $requestConfig
	 * @return bool
	 * @throws \Exception
	 */
	protected static function doPutRequest($configKey, array $params, array $requestConfig = []) {
		$host = self::getRequestHost($configKey);
		$response = self::sendPutRequest($host, $params, $requestConfig);
		if ($response->fail()) {
			throw new \RuntimeException($response->getMessage());
		}
		return true;
	}

	/**
	 * 获取HOST
	 * @param $key
	 * @return mixed
	 */
	public static function getRequestHost($key) {
		$key = "service_config/" . $key;
		$host = Config::getConfigItem($key);
		return $host;
	}

	/**
	 * POST请求
	 * @param $url
	 * @param $param
	 * @param int $timeout
	 * @param bool $isStrictCheck
	 * @return mixed
	 */
	public static function curlPost($url, $param, $timeout = 10, $isStrictCheck = false)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($param));
		curl_setopt($curl, CURLOPT_URL, $url);

		if (!empty($_SERVER['HTTP_USER_AGENT']) || $_SERVER['HTTP_USER_AGENT'] != null) {
			curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		} else {
			curl_setopt($curl, CURLOPT_USERAGENT, 'Dg_Helper_Curl');
		}
		$clientIP = self::getClientIp();
		if ($clientIP) {
			$headers = ["CLIENT-IP:$clientIP"];
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);  //构造IP
		}
		$response = curl_exec($curl);
		if ($isStrictCheck) {
			$arr_temp = curl_getinfo($curl);
			$errorCode = $arr_temp['http_code'] == 404 ? 404 : 0;
		} else {
			$errorCode = curl_errno($curl);
		}
		$errorMsg = curl_error($curl);
		curl_close($curl);
		return $errorCode ? '':$response;
	}

	/**
	 * 获取IP
	 * @param int $type
	 * @return mixed
	 */
	public static function getClientIp($type = 0)
	{
		$type = $type ? 1 : 0;

		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$pos = array_search('unknown', $arr);
			if (false !== $pos)
				unset($arr[$pos]);
			$ip = trim($arr[0]);
		} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		// IP地址合法验证
		$long = sprintf("%u", ip2long($ip));
		$ip = $long ? array($ip, $long) : array('0.0.0.0', 0);

		return $ip[$type];
	}

}

