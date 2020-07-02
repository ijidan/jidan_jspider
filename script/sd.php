<?php

use Lib\Util\SDUtil;
use Symfony\Component\Console\Output\OutputInterface;

require 'config.php';

$app = new Silly\Application();
$app->command('run [type] [keyword1] [keyword2]', function ($type, $keyword1, $keyword2, OutputInterface $output) {
	switch ($type) {
		case "us":
			SDUtil::uploadUS($output);
			break;
		case 'opener':
			SDUtil::uploadOpener($output);
			break;
		case 'plush_toys':
			SDUtil::uploadPlushToys($output);
			break;
		case 'gen_plush_toys':
			SDUtil::genPlushToys($output);
			break;
		case 'move_plush_toys':
			SDUtil::movePlushToys($output);
			break;
		case 'move_pvc':
			SDUtil::movePVC($output);
			break;
		case 'pvc':
			SDUtil::uploadPVC($output);
			break;
		case 'gen_mugs':
			SDUtil::genMugs($output);
			break;
		case 'mugs':
			SDUtil::uploadMugs($output);
			break;
		case 'rename_power_bank':
			SDUtil::renamePowerBank($output);
			break;
		case 'power_bank':
			SDUtil::uploadPowerBank($output);
			break;
		case 'polo':
			SDUtil::uploadPolo($output);
			break;
		case 'cat':
			SDUtil::updateCat($keyword1, $output);
			break;
		case 'img':
			SDUtil::uploadImage($keyword1, $output);
			break;
		case 'rename':
			SDUtil::doRename($keyword1, $keyword2, 3,$output);
			break;
		case 'gb':
			GearBestUtil::grab($keyword1, $keyword2, $output);
			break;
		case 'grab_img':
			GearBestUtil::grabImg($keyword1,$output);
			break;
		case 'handle_cat':
			GearBestUtil::handleCategory($output);
			break;

	}
	$output->writeln("执行完毕");
});
$app->run();