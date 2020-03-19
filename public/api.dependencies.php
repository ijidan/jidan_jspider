<?php
// DIC configuration

$container = $app->getContainer();

// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------

// php-view
$container['view'] = function ($c) {
	return null;
};

// monolog
$container['logger'] = function ($c) {
    return Lib\BaseLogger::instance('api');
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