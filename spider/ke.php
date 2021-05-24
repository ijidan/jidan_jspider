<?php

use Business\House\KeNewHouseCA;
use Business\House\KeNewHouseDE;
use Business\House\KeNewHouseFR;
use Business\House\KeNewHouseJP;
use Business\House\KeNewHouseSG;
use Business\House\KeNewHouseTH;
use Business\House\KeNewHouseUK;
use Business\House\KeNewHouseUS;
use Business\House\KeSecondHouseJP;
use Business\House\KeSecondHouseUS;
use Symfony\Component\Console\Output\OutputInterface;

include 'config.php';

$app = new Silly\Application();
$app->command('crawl [type] [keyword1] [keyword2]', function ($type, $keyword1, $keyword2, OutputInterface $output) {
	$crawlerList = [
		new KeNewHouseCA($output),
		new KeNewHouseDE($output),
		new KeNewHouseFR($output),
		new KeNewHouseJP($output),
		new KeNewHouseSG($output),
		new KeNewHouseTH($output),
		new KeNewHouseUK($output),
		new KeNewHouseUS($output),

		new KeSecondHouseJP($output),
		new KeSecondHouseUS($output)
	];
	switch ($type) {
		case 'content_excel': //爬取内容
			foreach ($crawlerList as $crawler) {
				$crawler->crawlContent();
				$crawler->genExcel();
			}
			break;
		default:
			$output->writeln('爬取类型错误!');
	}
	$output->writeln("执行完毕");
});
$app->run();