<?php
namespace Lib;

use Slim\Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ApiNotAllowed extends NotAllowed
{
    /**
     * @var Monolog\Logger
     */
    protected $logger;

    public function __construct(Container $container)
    {
        $this->logger = $container->get('logger');
    }

    public function __invoke(Request $request, Response $response, array $methods)
    {
        $msg = 'Method must be one of: ' . implode(', ', $methods);

        $this->logger->alert($msg);

        $output = [
            'error' => $msg,
            'code' => 405
        ];

        return $response->withJson($output, 405);
    }
}