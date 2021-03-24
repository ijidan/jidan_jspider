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
		return self::sendPostJsonRequest($host,$houseInfo,$requestConfig);
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

}

