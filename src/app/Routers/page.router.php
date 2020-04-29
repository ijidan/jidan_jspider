<?php

//页面跳转相关

$app->group('', function () use ($app) {
    $app->get('/', 'App\Controllers\SiteController:index');
	$app->get('/price', 'App\Controllers\SiteController:price');
	$app->get('/richText/{category_id}', 'App\Controllers\SiteController:richText');
	$app->get('/agreement', 'App\Controllers\SiteController:agreement');
	$app->get('/introduction', 'App\Controllers\SiteController:introduction');
	$app->get('/test/index','App\Controllers\TestController:index');
	$app->get('/test/video','App\Controllers\TestController:video');
});

