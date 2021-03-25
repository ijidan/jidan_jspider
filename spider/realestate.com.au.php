<?php

use Business\House\RealEstate;
use Symfony\Component\Console\Output\OutputInterface;

include 'config.php';

$app = new Silly\Application();
$app->command('crawl [type] [keyword1] [keyword2]', function ($type, $keyword1, $keyword2, OutputInterface $output) {
	$crawler = new RealEstate($output);
	switch ($type) {
		case 'content': //爬取内容
			$crawler->crawlContent();
			break;
		case 'image'://图片上传
			$crawler->uploadImage();
			break;
		case 'data':// 添加数据
			$crawler->uploadData();
			break;
		default:
			$output->writeln('爬取类型错误!');
	}
	$output->writeln("执行完毕");
});
$app->run();