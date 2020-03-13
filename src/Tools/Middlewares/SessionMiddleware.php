<?php
namespace Tools\Middlewares;

/*
 * 参考：https://github.com/akrabat/rka-slim-session-middleware/blob/master/RKA/SessionMiddleware.php
 */
use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;

/**
 * Class SessionMiddleware
 * @package Tools\Middlewares
 */
class SessionMiddleware {

	protected $options = [
		'name'          => 'SESSID', //session name
		'lifetime'      => 60,
		'path'          => null,
		'domain'        => null,
		'secure'        => false,
		'httponly'      => true,
		'cache_limiter' => 'nocache'
	];

	/**
	 * 构造函数
	 * SessionMiddleware constructor.
	 * @param array $options
	 */
	public function __construct($options = []) {
		$keys = array_keys($this->options);
		foreach ($keys as $key) {
			if (array_key_exists($key, $options)) {
				$this->options[$key] = $options[$key];
			}
		}
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @param callable $next
	 * @return mixed
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next) {
		$this->start();
		return $next($request, $response);
	}

	/**
	 * 开启
	 */
	private function start() {
		if (session_status() == PHP_SESSION_ACTIVE) {
			return;
		}

		$current = session_get_cookie_params();
		session_set_cookie_params($this->options['lifetime'] ?: $current['lifetime'], $this->options['path'] ?: $current['path'], $this->options['domain'] ?: $current['domain'], $this->options['secure'], $this->options['httponly']);
		session_name($this->options['name']);
		session_cache_limiter($this->options['cache_limiter']);

		/*
		 * 使用其它方式存储session
		 */
		//$config = ['prefix' => 'session_', 'expiretime' => 7200];
		//$sessionHandler = new \Lib\Session\MemcachedSession($config);
		//session_set_save_handler($sessionHandler, true);
		session_start();
	}
}