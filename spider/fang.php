<?php


use Business\News\Fang\AUFang;
use Business\News\Fang\GRFang;
use Business\News\Fang\JPFang;
use Business\News\Fang\KHFang;
use Business\News\Fang\MYFang;
use Business\News\Fang\PHFang;
use Business\News\Fang\UKFang;
use Business\News\Fang\USFang;
use Business\News\Fang\WorldFang;
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