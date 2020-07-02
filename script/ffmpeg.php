<?php

use Lib\Ffmpeg\VideoUtil;
use Symfony\Component\Console\Output\OutputInterface;

require 'config.php';

/**
 * 执行
 */
$app = new Silly\Application();
$app->command('run [cmd] [path] [arg] [other]', function ($cmd, $path, $arg, $other, OutputInterface $output = null) {
	$videoUtil = new VideoUtil($path, [], $output);
	switch ($cmd) {
		case 'clip':
			$videoUtil->clip();
			break;
		case 'watermark':
			$videoUtil->watermark();
			break;
		default:
			break;
	}
	$output->writeln("执行完毕");
});
$app->run();