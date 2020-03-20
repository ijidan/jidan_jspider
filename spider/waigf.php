<?php

use Business\WaiGF;
use Symfony\Component\Console\Output\OutputInterface;

include 'config.php';

$app = new Silly\Application();

$app->command('crawl [type] [keyword1] [keyword2]', function ($type, $keyword1, $keyword2, OutputInterface $output) {
	switch ($type) {
		case 'next':
			$wf=new WaiGF($output);
			$nextId=$wf->getNextHouseSeq();
			$output->writeln($nextId);
			break;
		case 'city':
			$wf=new WaiGF($output);
			$wf->crawlCountryCity();
			break;
		case 'all':
			$wf=new WaiGF($output);
			$wf->crawl();
	}
	$output->writeln("执行完毕");
});
$app->run();