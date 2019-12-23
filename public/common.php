<?php
# include the composer autoloader
include __DIR__ . '/../vendor/autoload.php';

include __DIR__.'/debugger.php';
include __DIR__.'/compat.php';

//定义整个项目的根目录
define('BASE_DIR', realpath(__DIR__.'/../'));

//添加include_path
$configPath=BASE_DIR.PATH_SEPARATOR."config";
set_include_path(get_include_path() . PATH_SEPARATOR . $configPath);



define('NOW_FLOAT', microtime(true));
define('NOW', time());

//定义默认字符集
mb_internal_encoding('UTF-8');

//定义默认时区
date_default_timezone_set('UTC');