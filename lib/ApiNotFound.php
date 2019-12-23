<?php
namespace Lib;

use Slim\Handlers\NotFound;
use Slim\Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ApiNotFound extends NotFound
{
    protected $view;
    /**
     * @var Monolog\Logger
     */
    protected $logger;

    public function __construct(Container $container)
    {
        $this->logger = $container->get('logger');
        $this->view = $container->get('view');
    }

    public function __invoke(Request $request, Response $response)
    {
        $msg = 'Not Found : ' . $request->getUri()->__toString();

        $this->logger->alert($msg);

        return parent::__invoke($request, $response);
    }

    /*
     * 重载
     */
    protected function renderHtmlNotFoundOutput(Request $request)
    {
        return $this->view->fetch('404', []);
    }
}