<?php

//返回json


$authMiddleware = new \App\Middlewares\AuthMiddleware();
$csrfMiddleware = new \App\Middlewares\CsrfMiddleware();

$app->group('', function () use ($app) {
});

//验证是否登录
$app->group('', function () use ($app) {
})->add($csrfMiddleware)->add($authMiddleware);


//统一处理session
$app->add(new \App\Middlewares\SessionMiddleware());
//$app->add(new \App\Middlewares\CaseInsensitiveMiddleware());