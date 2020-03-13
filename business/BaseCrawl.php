<?php

namespace Business;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * 基础类
 * Class BaseCrawl
 * @package Business
 */
class BaseCrawl {

	const COLOR_RED = "red";
	const COLOR_YELLOW = "yellow";
	const COLOR_BLUE = "blue";


	/**
	 * 输出信息
	 * @param $message
	 * @param OutputInterface $output
	 * @param $fg
	 * @param $bg
	 */
	public static function writeColorLn($message, OutputInterface $output, $fg, $bg = "") {
		$output->writeln("<fg=$fg>$message</>");
	}


}