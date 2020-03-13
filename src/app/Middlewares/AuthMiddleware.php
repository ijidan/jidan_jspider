<?php
namespace App\Middlewares;

use Lib\ErrorCode;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Models\UserUtil;

/**
 * 验证token是否合法
 */
class AuthMiddleware
{
    public function __construct()
    {

    }

    /**
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        // before
        if (!$this->checkToken($request)) {
            $output = [
                'error' => 'Please login first',
                'code' => ErrorCode::NOT_LOGGED_IN
            ];
            return $response->withJson($output, 200);
        }

        return $next($request, $response);
    }

    /*
     * 验证token
     */
    private function checkToken(ServerRequestInterface $request)
    {
        $uid = UserUtil::getLoginUid($request);
        return $uid ? true : false;
    }
}