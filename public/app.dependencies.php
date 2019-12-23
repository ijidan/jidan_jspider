<?php
// DIC configuration

$container = $app->getContainer();

// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------

// php-view
$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig( APP_DIR . '/Views/', [
        //'cache' => BASE_DIR . '/storage/cache/'
	    'cache' => false
    ]);
    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($c['router'], $basePath));
	return $view;
};

// monolog
$container['logger'] = function ($c) {
    return Lib\BaseLogger::instance('app');
};

// error handler
$container['errorHandler'] = function ($c) {
    return new Lib\ApiError($c);
};

// not found handler
$container['notFoundHandler'] = function ($c) {
    return new Lib\ApiNotFound($c);
};

// not allowed handler
$container['notAllowedHandler'] = function ($c) {
    return new Lib\ApiNotAllowed($c);
};