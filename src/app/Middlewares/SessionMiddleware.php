<?php
namespace App\Middlewares;

use Lib\Util\Config;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 参考：https://github.com/akrabat/rka-slim-session-middleware/blob/master/RKA/SessionMiddleware.php
 */
class SessionMiddleware
{
    protected $options = [
        'name' => 'APP_SID', //session name
        'lifetime' => 7200,
        'path' => null,
        'domain' => null,
        'secure' => false,
        'httponly' => true,
        'cache_limiter' => 'nocache'
    ];

    public function __construct()
    {
        $options = Config::loadConfig('struct')['cookie'];
        $keys = array_keys($this->options);
        foreach ($keys as $key) {
            if (array_key_exists($key, $options)) {
                $this->options[$key] = $options[$key];
            }
        }
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $this->start();
        return $next($request, $response);
    }

    /*
     * 开启session
     */
    private function start()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            return;
        }

        $current = session_get_cookie_params();
        session_set_cookie_params(
            $this->options['lifetime'] ?: $current['lifetime'],
            $this->options['path'] ?: $current['path'],
            $this->options['domain'] ?: $current['domain'],
            $this->options['secure'],
            $this->options['httponly']
        );
        session_name($this->options['name']);
        session_cache_limiter($this->options['cache_limiter']);

        /*
         * 使用其它方式存储session
         */
        $config = ['prefix' => 'session_', 'expiretime' => $this->options['lifetime']];
        $sessionHandler = new \Lib\Session\MemcachedSession($config);
        session_set_save_handler($sessionHandler, true);
        session_start();
    }
}