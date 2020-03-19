<?php

//页面跳转相关

$app->group('', function () use ($app) {
    $app->get('/', 'Api\Controllers\HouseController:index');
});

