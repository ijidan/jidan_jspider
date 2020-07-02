<?php

require 'config.php';

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