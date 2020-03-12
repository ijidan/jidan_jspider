<?php
namespace Lib;

use Dflydev\FigCookies\SetCookie;
use Dflydev\FigCookies\FigResponseCookies;
use Slim\Container;

/**
 * BaseController
 * Class BaseController
 * @package Lib
 */
abstract class BaseController {

	protected $view;
	protected $logger;
	/** @var \Slim\Http\Request $request */
	protected $request;
	/** @var \Slim\Http\Response $response */
	protected $response;

	/**
	 * 构造函数
	 * BaseController constructor.
	 * @param Container $container
	 * @throws \Interop\Container\Exception\ContainerException
	 */
	public function __construct(Container $container) {
		$this->logger = $container->get('logger');
		$this->view = $container->get('view');;
		$this->request = $container->request;
		$this->response = $container->response;
	}

	/**
	 *成功JSON响应
	 * @param string $message
	 * @param array $data
	 * @param string $jumpUrl
	 * @return BaseController
	 */
	public function jsonSuccess($message = "", array $data = [], $jumpUrl = "") {
		return $this->json(ErrorCode::RIGHT, $message, $data, $jumpUrl);
	}

	/**
	 * 失败JSON响应
	 * @param $code
	 * @param string $message
	 * @param array $data
	 * @param string $jumpUrl
	 * @return BaseController
	 */
	public function jsonFail($code, $message = "", array $data = [], $jumpUrl = "") {
		return $this->json($code, $message, $data, $jumpUrl);
	}

	/**
	 * JSONP 响应
	 * @param $code
	 * @param string $message
	 * @param array $data
	 * @param string $jumpUrl
	 * @param string $callback
	 * @return static
	 */
	public function jsonP($code, $message = "", array $data = [], $jumpUrl = "", $callback = '_callback') {
		$data = $this->buildResponseResult($code, $message, $data, $jumpUrl);
		$result = $callback . '(' . json_encode($data) . ');';
		return $this->response->write($result);
	}

	/**
	 * 输出JSON
	 * @param int $code
	 * @param string $message
	 * @param array $data
	 * @param string $jumpUrl
	 * @return static
	 */
	private function json($code = ErrorCode::RIGHT, $message = "", array $data = [], $jumpUrl = "") {
		$result = $this->buildResponseResult($code, $message, $data, $jumpUrl);
		return $this->response->withJson($result, 200);
	}

	/**
	 * 成功响应
	 * @param $message
	 * @param array $data
	 * @param string $jumpUrl
	 * @param string $callback
	 * @return string
	 */
	public function iFrameResponseSuccess($message, array $data = [], $jumpUrl = "", $callback = '_callback') {
		return $this->iFrameResponse(ErrorCode::RIGHT, $message, $data, $jumpUrl, $callback);
	}

	/**
	 * 失败响应
	 * @param $code
	 * @param $message
	 * @param array $data
	 * @param string $jumpUrl
	 * @param string $callback
	 * @return string
	 */
	public function iFrameResponseFail($code, $message, array $data = [], $jumpUrl = "", $callback = '_callback') {
		return $this->iFrameResponse($code, $message, $data, $jumpUrl, $callback);
	}


	/**
	 * 转换当前对象为iframe相应格式
	 * @param $code
	 * @param $message
	 * @param array $data
	 * @param string $jumpUrl
	 * @param string $callback
	 * @return string
	 */
	private function iFrameResponse($code, $message, array $data = [], $jumpUrl = "", $callback = '_callback') {
		$data = $this->buildResponseResult($code, $message, $data, $jumpUrl);
		$html = '<!doctype html><html lang="en"><head><meta charset="UTF-8" /><title></title>' . '<script>
				var frame = null;
				try {
					frame = window.frameElement;
					if(!frame){
						throw("no frame 1");
					}
				} catch(ex){
					try {
						document.domain = location.host.replace(/^[\w]+\./, \'\');
						frame = window.frameElement;
						if(!frame){
							throw("no frame 2");
						}
					} catch(ex){
						if(window.console){
							console.log("i try twice to cross domain. sorry, i m give up...");
						}
					}
				};
				</script>' . "<script>frame.$callback(" . json_encode($data) . ");</script>" . '</head><body></body></html>';
		return $html;
	}

	/**
	 * 构建响应数据
	 * @param $code
	 * @param $message
	 * @param array $data
	 * @param string $jumpUrl
	 * @return array
	 */
	private function buildResponseResult($code, $message, array $data = [], $jumpUrl = "") {
		$result = [
			"code"     => $code,
			"message"  => $message,
			"data"     => $data,
			"jump_url" => $jumpUrl
		];
		return $result;
	}

	/**
	 * 渲染
	 * @param $template
	 * @param array $data
	 * @return mixed
	 */
	public function render($template, array $data) {
		return $this->view->render($this->response, $template, $data);
	}

	/**
	 * 设置cookie
	 * @param $name
	 * @param $value
	 * @param int $maxAge
	 * @param string $path
	 * @param null $domain
	 * @param bool $secure
	 */
	protected function setCookie($name, $value, $maxAge = 720000, $path = '/', $domain = null, $secure = false) {
		$setCookie = SetCookie::create($name, $value)->withExpires(time() + $maxAge)->withMaxAge($maxAge)->withPath($path)->withDomain($domain)->withSecure($secure);
		$this->response = FigResponseCookies::set($this->response, $setCookie);
	}
}