<?php

use Business\Fang\AUFang;
use Business\Fang\GRFang;
use Business\Fang\JPFang;
use Business\Fang\KHFang;
use Business\Fang\MYFang;
use Business\Fang\PHFang;
use Business\Fang\UKFang;
use Business\Fang\USFang;
use Business\Fang\WorldFang;
use Business\WaiGF;
use Symfony\Component\Console\Output\OutputInterface;

include 'config.php';

$app = new Silly\Application();

$app->command('crawl [type] [keyword1] [keyword2]', function ($type, $keyword1, $keyword2, OutputInterface $output) {
	switch ($type) {
		case 'au':
			$fang = new AUFang($output);
			$fang->crawl();
			break;
		case 'gr':
			$fang = new GRFang($output);
			$fang->crawl();
			break;
		case 'jp':
			$fang = new JPFang($output);
			$fang->crawl();
			break;
		case 'kh':
			$fang = new KHFang($output);
			$fang->crawl();
			break;
		case 'my':
			$fang = new MYFang($output);
			$fang->crawl();
			break;
		case 'ph':
			$fang = new PHFang($output);
			$fang->crawl();
			break;
		case 'uk':
			$fang = new UKFang($output);
			$fang->crawl();
			break;
		case 'us':
			$fang = new USFang($output);
			$fang->crawl();
			break;
		case 'world':
			$fang = new WorldFang($output);
			$fang->crawl();
			break;
	}
	$output->writeln("执行完毕");
});
$app->run();