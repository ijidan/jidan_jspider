<?php

use Lib\Ffmpeg\VideoUtil;
use Symfony\Component\Console\Output\OutputInterface;

require 'config.php';

$app = new Silly\Application();
$app->command('run [cmd] [path] [arg] [other]', function ($cmd,$path, $arg, $other, OutputInterface $output=null) {
	switch ($cmd){
		case 'clip':
			$videoUtil=new VideoUtil($path,[],$output);
			$videoUtil->clip();
		default:
			break;
	}
	$output->writeln("执行完毕");
});
$app->run();