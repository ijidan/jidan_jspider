<?php
use Slim\Http\Request;
use Slim\Http\Response;

//中间件的执行顺序是后添加的后执行
$app->add(new \Tools\Middlewares\AuthMiddleware());
$app->add(new \Tools\Middlewares\SessionMiddleware());


$app->any('/[{controller}[/{action}]]', function (Request $request, Response $response, $args) {
	$controller = $args['controller'] ?: 'index';
	$action = $args['action'] ?: 'index';
	$className = 'Tools\\Controllers\\' . ucfirst($controller) . 'Controller';
	if (class_exists($className, true) && is_callable([$className, $action], false)) {
		$class = new $className($this);
		$params = $request->getParams();
		return $class->$action($params);
	} else {
		throw new Exception("Error Path:{$controller}/{$action}");
	}
});