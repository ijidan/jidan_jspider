<?php

use \Workerman\Worker;
use \GatewayWorker\BusinessWorker;
use Lib\Util\Config;


$socketConfig=Config::loadConfig("socket");
$registerAddress=$socketConfig["register_address"];

// bussinessWorker 进程
$worker = new BusinessWorker();
// worker名称
$worker->name = 'VietBusinessWorker';
// bussinessWorker进程数量
$worker->count = 4;
// 服务注册地址
$worker->registerAddress = $registerAddress;
//注册PID
$pid="/var/run/workman.pid";
BusinessWorker::$pidFile=$pid;
//注册日志
//$structConfig=Config::loadConfig('struct');
//$logFile = $structConfig["event_log_path"] . "workman" . '.' . date('Y-m-d') . '.log';
BusinessWorker::$logFile="/data/workerman.log";

// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}