<?php

use Symfony\Component\Console\Output\OutputInterface;

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
//error_reporting(E_ALL);

set_time_limit(0);
ini_set('memory_limit', '2048M');

require dirname(dirname(__FILE__)) . "/public/common.php";

$app = new Silly\Application();
$app->command('run [type] [keyword1] [keyword2]', function ($type, $keyword1, $keyword2, OutputInterface $output) {
	$dirPath = '/vagrant/SEO官网文章打包25篇';
	$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPath));
	/** @var SplFileInfo $object */
	foreach ($objects as $object) {
		if ($object->isFile()) {
			$pathName = $object->getPathname();
			$destName = str_replace("webp", "jpg", $pathName);
			$ext = $object->getExtension();
			if ($ext == "webp") {
				$cmd = "dwebp {$pathName} -o $destName";
				exec($cmd);
				unlink($pathName);
			}
		}
	}
	$output->writeln("执行完毕");
});
$app->run();