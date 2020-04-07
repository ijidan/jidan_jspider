<?php

use Business\ULu;
use Business\Uoolu\Topic;
use Lib\Util\SDUtil;
use Symfony\Component\Console\Output\OutputInterface;

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
//error_reporting(E_ALL);

set_time_limit(0);
ini_set('memory_limit', '2048M');

require dirname(dirname(__FILE__)) . "/public/common.php";


$app = new Silly\Application();

$app->command('crawl [type] [keyword1] [keyword2]', function ($type, $keyword1, $keyword2, OutputInterface $output) {
	switch ($type) {
		case "us":
			SDUtil::uploadUS($output);
			break;
		case 'topic':
			$uLu=new Topic($output);
			$uLu->crawl();
	}
	$output->writeln("执行完毕");
});
$app->run();