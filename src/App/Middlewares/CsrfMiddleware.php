<?php
namespace App\Middlewares;

use Lib\Session;
use Lib\ErrorCode;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * CSRF自定义处理
 */
class CsrfMiddleware
{
    /**
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $params = $request->getParams();
        $isRobot = $params['robot'] && is_numeric($params['robot']);
        if ($isRobot) {
            //机器人不验证
        } else if (empty($params['csrf_token']) || $params['csrf_token'] != Session::get(Session::CSRF_TOKEN)) {
            $output = [
                'error' => 'CSRF middleware failed',
                'code' => ErrorCode::ERROR_CSRF
            ];
            return $response->withJson($output, 200);
        }
        return $next($request, $response);
    }
}