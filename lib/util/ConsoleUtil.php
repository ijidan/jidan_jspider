<?php

namespace Lib\Util;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * 终端工具
 * Class ConsoleUtil
 * @package Lib\Util
 */
class ConsoleUtil {

	const COLOR_RED = "red";
	const COLOR_YELLOW = "yellow";
	const COLOR_BLUE = "blue";

	/**
	 * 终端输出对象
	 * @var OutputInterface|null
	 */
	private $output;


	/**
	 * 构造函数
	 * ConsoleUtil constructor.
	 * @param OutputInterface|null $output
	 */
	public function __construct(OutputInterface $output = null) {
		$this->output = $output;
	}

	/**
	 * 输出信息
	 * @param $message
	 * @param string $fg
	 */
	public function writeColorLn($message, $fg = '') {
		$msg = $fg ? "<fg=$fg>$message</>" : "$message";
		$this->output->writeln($msg);
	}

	/**
	 * 信息
	 * @param $message
	 */
	public function info($message) {
		$this->writeColorLn($message,self::COLOR_BLUE);
	}

	/**
	 * 错误信息
	 * @param $message
	 */
	public function error($message) {
		$this->writeColorLn($message, self::COLOR_RED);
	}

	/**
	 * 警告信息
	 * @param $message
	 */
	public function warning($message) {
		$this->writeColorLn($message, self::COLOR_YELLOW);
	}

	/**
	 * 成功信息
	 * @param $message
	 */
	public function success($message) {
		$this->writeColorLn($message, self::COLOR_BLUE);
	}

}