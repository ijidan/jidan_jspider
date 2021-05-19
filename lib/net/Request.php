<?php

namespace Lib\Net;

use App\Models\Queue;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Lib\BaseLogger;
use Lib\Util\UuidUtil;
use Pdp\Parser;
use Pdp\PublicSuffixListManager;
use SplFileInfo;

/**
 * 请求类
 * Class Request
 * @package Lib\Net
 */
class Request {

	private static $instance_list = [];
	private $uuid = null;
	private $url = "";
	private $params = [];
	private $method = "";
	private $cacheKey = "";
	private $cacheTTL = 1;
	private $startTime = 0;


	/** @var \Monolog\Logger|void 单例 */
	private $logger = null;

	const METHOD_GET = "GET"; //GET请求
	const METHOD_POST = "POST"; //POST请求
	const METHOD_PUT = "PUT"; //PUT请求
	const METHOD_POST_JSON = 'POST_JSON'; //POST JSON

	/**
	 * 配置
	 * @var array
	 */
	private $businessConfig = array();

	/**
	 * Guzztle配置
	 * @var array
	 */
	private $guzzleHttpConfig = array(
		RequestOptions::TIMEOUT         => 30,                     //超时时间(秒)
		RequestOptions::VERIFY          => false,
		RequestOptions::ALLOW_REDIRECTS => true,
		RequestOptions::HTTP_ERRORS     => true,
		RequestOptions::PROXY           => [
			//'http' => 'tcp://127.0.0.1:8888',
			//'https' => 'tcp://127.0.0.1:8888',
		],
	);

	/**
	 * 行为配置
	 * @var array
	 */
	private $customConfig = array();


	/**
	 * 跟踪数据
	 * @var array
	 */
	private $traceData=array();

	/**
	 * 配置文件
	 * Request constructor.
	 * @param array $config
	 * @throws \Exception
	 */
	private function __construct(array $config = []) {
		if (isset($config["guzzleHttp"]) && $config["guzzleHttp"]) {
			$this->guzzleHttpConfig = array_merge($this->guzzleHttpConfig, $config["guzzleHttp"]);
		}
		if (isset($config["business"]) && $config["business"]) {
			$this->businessConfig = array_merge($this->businessConfig, $config["business"]);
		}

		if (isset($config["custom"]) && $config["custom"]) {
			$this->customConfig = array_merge($this->customConfig, $config["custom"]);
		}
		$logName = 'request.log';
		$this->logger = BaseLogger::instance(BaseLogger::CHANNEL_BUSINESS_SERVICE);
	}

	/**
	 * 单例模式
	 * @param array $config
	 * @return mixed
	 * @throws \Exception
	 */
	public static function instance($config = array()) {
		$key = serialize($config);
		if (!self::$instance_list[$key]) {
			self::$instance_list[$key] = new self($config);
		}
		/** @var Request $request */
		$request = self::$instance_list[$key];
		$request->setUUID();
		return $request;
	}

	/**
	 * 设置请求方法
	 * @param $method
	 */
	public function setMethod($method) {
		$this->method = $method;
	}

	/**
	 * 获取请求方法
	 * @return string
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * 设置URL
	 * @param $url
	 */
	public function setUrl($url) {
		$this->url = trim($url);
	}

	/**
	 * 获取URL
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * 设置数据
	 * @param array $params
	 * @return array
	 */
	public function setParams(array $params) {
		$mergedParams = isset($this->customConfig['need_uuid']) && $this->customConfig['need_uuid'] == false ? $params : array_merge($params, ["uuid" => $this->uuid]);
		return $this->params = $this->method == self::METHOD_GET ? $mergedParams : $params;
	}

	/**
	 * 获取数据
	 * @return array
	 */
	public function getParams() {
		return $this->params;
	}

	/**
	 * 设置UUID
	 * @param $uuid
	 */
	public function setUUID($uuid = null) {
		$uuid = $uuid ?: UuidUtil::v4();
		$this->uuid = $uuid;
	}

	/**
	 * 获取UUID
	 * @return null
	 */
	public function getUUID() {
		return $this->uuid;
	}

	/**
	 * 获取返回值
	 * @return \GuzzleHttp\Psr7\Response|Response|mixed|null|\Psr\Http\Message\ResponseInterface
	 * @throws \Exception
	 */
	public function send() {
		if (!$this->url) {
			throw new \Exception('REQUEST NEED HOST');
		}
		//检测缓存
		/*
		if($this->method==self::METHOD_GET && isset($this->businessConfig["cache_ttl"]) && $this->businessConfig["cache_ttl"]>0){
			$cacheParam = array_merge(["host" => $this->url], $this->params);
			$this->cacheKey=\serialize($cacheParam);
			$this->cacheTTL=$this->businessConfig["cache_ttl"];
			$cacheData = MC::get($this->cacheKey);
			if ($cacheData) {
				$response = new Response(RetCode::SUCCESS, '', \json_decode($cacheData,true), "缓存成功");
				return $response;
			}
		}
		*/

		//合并
		$this->mergeHeaders();;
		$clientConfig = $this->guzzleHttpConfig;
		if ($this->method == self::METHOD_POST_JSON) {
			$clientConfig['headers']['Content-Type'] = 'application/json';
		}
		$this->startTime = microtime(true);
		$httpClient = new Client($clientConfig);
		$rsp = null;
		$cookieDomain = $this->getCookieDomain();
		$cookieJar = CookieJar::fromArray($_COOKIE, $cookieDomain);

		//请求
		try {
			switch ($this->method) {
				case self::METHOD_GET:
					$config = ['cookies' => $cookieJar,];
					$link = $this->params ? $this->url . "?" . http_build_query($this->params) : $this->url;
					$rsp = $httpClient->get($link, $config);
					break;
				case self::METHOD_POST_JSON:
				case self::METHOD_POST:
					$config = ['cookies' => $cookieJar, 'body' => \json_encode($this->params)];
					$rsp = $httpClient->post($this->url, $config);
					break;
				case self::METHOD_PUT:
					$config = ['cookies' => $cookieJar, 'body' => \json_encode($this->params)];
					$rsp = $httpClient->put($this->url, $config);
			}
			/** @var \GuzzleHttp\Psr7\Response $rsp */
			$response = $this->handleResponse($rsp);
		} catch (RequestException  $e) {
			$msg = $e->getMessage();
			if($e->hasResponse()){
				$rsp=$e->getResponse();
				$response=$this->handleResponse($rsp,$msg);
			}else{
				//记录日志
				$this->addLog(0, "", "请求异常" . $msg);
				$response = new Response(RetCode::UNKNOWN, $msg, []);
			}
			//发送邮件
			//$this->sendRequestExceptionMail($msg);
		}
		return $response;
	}




	/**
	 * 处理响应
	 * @param \GuzzleHttp\Psr7\Response $rsp
	 * @param string $msg
	 * @return Response
	 */
	private function handleResponse($rsp,$msg='') {
		$this->rpsWriteCookie($rsp);
		$statusCode = $rsp->getStatusCode();
		$message = $msg ?:$rsp->getReasonPhrase();
		if ($statusCode != Response::HTTP_STATUS_CODE_SUCCESS) {
			//记录日志
			$this->addLog($statusCode, "", $message);
			$response = new Response(RetCode::REQ_FAIL, $message, []);
		} else {
			$contents = $rsp->getBody()->getContents();
			//记录日志
			$this->addLog($statusCode, $contents, "请求成功");
			$result = json_decode($contents, true);
			//判断是否JSON
			if (json_last_error() == JSON_ERROR_NONE && $result) {
				if (isset($result['code']) || isset($result['errorCode'])) {
					$code = isset($result['code']) ? $result['code'] : '--';
					if ($code === '--') {
						$code = isset($result['errorCode']) ? $result['errorCode'] : '--';
					}
					if ($code == RetCode::SUCCESS) {
						$data = isset($result['data']) ? $result['data'] : [];
						$response = new Response(RetCode::SUCCESS, '', $data, $message);
					} else {
						//记录日志
						$response = new Response(RetCode::JSON_PARSE_FAIL, $message, $result);
					}
				} else {
					$response = new Response(RetCode::SUCCESS, 'JSON格式', $contents, $message);
				}
			} else {
				$response = new Response(RetCode::SUCCESS, '非JSON格式', $contents, $message);
			}
		}
		//header处理
		$rspHeaders=$rsp->getHeaders();
		$dc=DataContainer::getInstance();
		$dc->setData('headers',$rspHeaders);
		//日志
		$this->writeDebugLog($rspHeaders,'rsp');
		return $response;
	}

	/**
	 * 回写cookie
	 * @param $response
	 */
	private function rpsWriteCookie($response) {
		/** @var \GuzzleHttp\Psr7\Response $response */
		//cookie处理
		$cookies = $response->getHeader('Set-Cookie');
		if ($cookies) {
			foreach ($cookies as $cookie) {
				$setCookie = SetCookie::fromString($cookie);
				\setcookie($setCookie->getName(), $setCookie->getValue(), $setCookie->getExpires(), $setCookie->getPath(), $setCookie->getDomain(), $setCookie->getSecure(), $setCookie->getHttpOnly());
			}
		}
	}

	/**
	 * 发送文件
	 * @return \GuzzleHttp\Psr7\Response|Response|\Psr\Http\Message\ResponseInterface|null
	 * @throws \Exception
	 */
	public function sendFile() {
		if (!$this->url) {
			throw new \Exception('REQUEST NEED HOST');
		}
		$this->startTime = microtime(true);
		$response = null;
		$httpClient = new Client($this->guzzleHttpConfig);
		try {
			$config = ['multipart' => [$this->params]];
			$rsp = $httpClient->post($this->url, $config);
			/** @var \GuzzleHttp\Psr7\Response $rsp */
			$response = $this->handleResponse($rsp);
		} catch (\Exception $e) {
			$msg = $e->getMessage();
			//记录日志
			$this->addLog(0, "", "请求异常" . $msg);
			$response = new Response(RetCode::UNKNOWN, $msg, []);
		}
		return $response;
	}

	/**
	 * 下载图片
	 * @return Response|null
	 * @throws \Exception
	 */
	public function getFile() {
		if (!$this->url) {
			throw new \Exception('REQUEST NEED HOST');
		}
		$this->startTime = microtime(true);
		$response = null;
		$httpClient = new Client($this->guzzleHttpConfig);
		try {
			$file = new SplFileInfo($this->url);
			$ext = $file->getExtension();
			$destPath = $this->params['dest_path'];
			$fileName = 'dn_' . time() . rand(1, 100000) . '.' . $ext;
			$filePath = $destPath . $fileName;
			$body = ['save_to' => $filePath];
			$config = array_merge($this->guzzleHttpConfig, $body);
			$rsp = $httpClient->get($this->url, $config);
			$statusCode = $rsp->getStatusCode();
			$message = $rsp->getReasonPhrase();
			if ($statusCode != Response::HTTP_STATUS_CODE_SUCCESS) {
				//记录日志
				$this->addLog($statusCode, "", $message);
				$response = new Response(RetCode::REQ_FAIL, $message, []);
			} else {
				$response = new Response(RetCode::SUCCESS, '下载成功', ['path' => $filePath], $message);
			}
		} catch (\Exception $e) {
			$msg = $e->getMessage();
			//记录日志
			$this->addLog(0, "", "请求异常" . $msg);
			$response = new Response(RetCode::UNKNOWN, $msg, []);
		}
		return $response;

	}

	/**
	 * 记录日志
	 * @param $statusCode
	 * @param $return
	 * @param $message
	 * @param string $func
	 */
	private function addLog($statusCode, $return, $message, $func = "addError") {
		if ($this->method == self::METHOD_GET) {
			$link = $this->url . "?" . http_build_query($this->params);
		} else {
			$link = $this->url;
		}
		$endTime = microtime(true);
		$off = ($endTime - $this->startTime) * 1000;

		$loggerData = [
			"uuid"             => $this->uuid,
			"url"              => $link,
			"param"            => $this->params,
			"http_status_code" => $statusCode,
			"return"           => $return,
			"message"          => $message,
			"start_time"       => $this->startTime,
			"end_time"         => $endTime,
			"spend_time"       => $off //毫秒
		];
		$this->logger->$func(\json_encode($loggerData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
	}

	/**
	 * 发送异常邮件
	 * @param string $msg
	 */
	private function sendRequestExceptionMail($msg = "") {
		if ($this->method == self::METHOD_GET) {
			$link = $this->url . "?" . http_build_query($this->params);
			$link = "<a href='" . $link . "'>" . $link . "</a>";
		} else {
			$link = $this->url;
		}
		$config = \json_encode($this->guzzleHttpConfig);
		$param = \json_encode($this->params);
		$title = "API请求异常";
		$content = <<<EOF
                   提示信息:$msg</br>
                API请求地址：$link</br>
                API请求参数：$param</br>
                API请求配置：$config</br>
                API请求方式：$this->method</br>
EOF;
		Queue::pushEmailMessage(["title" => $title, "content" => $content]);
	}

	/**
	 * 获取消息
	 * @param $result
	 * @return string
	 */
	private function getMessage($result) {
		if (isset($result["message"])) {
			return $result["message"];
		}
		if (isset($result["error"])) {
			return $result["error"];
		}
		return "";
	}

	/**
	 * 获取缓存key
	 * @return string
	 */
	public function getCacheKey() {
		return $this->cacheKey;
	}

	/**
	 * 设置缓存key
	 * @param string $cacheKey
	 */
	public function setCacheKey($cacheKey) {
		$this->cacheKey = $cacheKey;
	}

	/**
	 * 获取缓存时间
	 * @return int
	 */
	public function getCacheTTL() {
		return $this->cacheTTL;
	}

	/**
	 * 设置缓存时间
	 * @param int $cacheTTL
	 */
	public function setCacheTTL($cacheTTL) {
		$this->cacheTTL = $cacheTTL;
	}

	/**
	 * 合并header
	 */
	private function mergeHeaders(){
		$dc=DataContainer::getInstance();
		$headers=$dc->getData('headers');
		$guzzleHeaders=$headers ?$headers:isset($this->guzzleHttpConfig['headers'])?$this->guzzleHttpConfig['headers']:[];
		$this->guzzleHttpConfig['headers']=$guzzleHeaders;

		$this->writeDebugLog($guzzleHeaders,'req');

//		$headers = $this->computeHeaders();
//		if($headers){
//			$guzzleHeaders=isset($this->guzzleHttpConfig['headers'])?$this->guzzleHttpConfig['headers']:[];
//			foreach ($headers as $key=>$v){
//				$guzzleHeaders[$key]=$v;
//			}
//			$this->guzzleHttpConfig['headers']=$guzzleHeaders;
//		}

	}
	/**
	 * 计算header
	 * @return array
	 */
	private function computeHeaders() {
		$urlArr = parse_url($this->url);
		$host = $urlArr['host'];

		$server['User-Agent'] = self::genRandomUA();
		//$server['referer'] = $host;
		//$server['client-ip']=get_ip();
		return $server;
	}

	/**
	 * 获取域名
	 * @return mixed
	 */
	private function getCookieDomain() {
		$pslManager = new PublicSuffixListManager();
		$parser = new Parser($pslManager->getList());
		$url = $parser->parseUrl($this->url);
		$regDomain = $url->getHost()->getRegisterableDomain();
		return $regDomain;
	}

	/**
	 * 随机UA
	 * @return string
	 */
	private function genRandomUA() {
		try {
			$ua = \Campo\UserAgent::random(['os_type' => 'Windows']);
			return $ua;
		} catch (\Exception $e) {
			return '';
		}
	}

	/**
	 * 写调试日志
	 * @param $message
	 * @param string $type
	 */
	private function writeDebugLog($message,$type='req'){
		$_message=is_array($message)?\json_encode($message):$message;
		$msg=date('Y-m-d H:i:s').' '.$type.'       '.$_message;
		file_put_contents('/tmp/jidan.log',$msg,FILE_APPEND);
	}

}