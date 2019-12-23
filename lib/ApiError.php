<?php
namespace Lib;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Container;
use Slim\Handlers\Error;

/**
 * 自定义错误处理
 * Class ApiError
 * @package Lib
 */
final class ApiError extends Error
{
    /**
     * @var \Monolog\Logger
     */
    protected $logger;

	/**
	 * 构造函数
	 * ApiError constructor.
	 * @param Container $container
	 */
    public function __construct(Container $container)
    {
        $this->logger = $container->get('logger');
        parent::__construct($container);
    }

	/**
	 * 调用
	 * @param Request $request
	 * @param Response $response
	 * @param \Exception $exception
	 * @return mixed
	 */
    public function __invoke(Request $request, Response $response, \Exception $exception)
    {
        $code = $exception->getCode();
        $msg = $exception->getMessage();
        $name = $this->logger->getName();
        $context = [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'code' => $exception->getCode()
        ];
        switch ($name) {
            case 'app':
                $context['uid'] = Session::get(SESSION::LOGIN_UID);
                break;
            case 'tools':
                $context['user'] = Session::get(Session::TOOLS_LOGIN_ACCOUNT);
                break;
        }
        $this->logger->critical($msg, $context);
        BaseLogger::dailyError($exception);

	    //return parent::__invoke($request, $response, $exception);
        $output = [
            'error' => $msg,
            'code' => $code
        ];
        return $response->withJson($output, 500);
    }
}