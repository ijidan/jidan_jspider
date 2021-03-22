<?php

use Business\House\HiMaWaRi;
use Symfony\Component\Console\Output\OutputInterface;

include 'config.php';

$app = new Silly\Application();
$app->command('crawl [type] [keyword1] [keyword2]', function ($type, $keyword1, $keyword2, OutputInterface $output) {
	switch ($type) {
		case 'all':
			$glo = new HiMaWaRi($output);
			$glo->crawl();
			break;
	}
	$output->writeln("执行完毕");
});
$app->run();