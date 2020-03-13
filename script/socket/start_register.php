<?php

use \Workerman\Worker;
use \GatewayWorker\Register;
use Lib\Util\Config;

$socketConfig=Config::loadConfig("socket");
$registerAddress=$socketConfig["register_address"];

$register = new Register('text://'.$registerAddress);

// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}

