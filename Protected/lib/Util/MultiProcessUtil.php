<?php


namespace Lib\Util;


/**
 * 多进程工具
 * Class MultiProcessUtil
 * @package Lib\Util
 */
class MultiProcessUtil {

	public $mpid = 0;
	public $works = [];
	public $max_precess = 1;
	public $new_index = 0;


	/**
	 * 构造函数
	 * MultiProcessUtil constructor.
	 */
	public function __construct() {
		try {
			\swoole_set_process_name(sprintf('php-ps:%s', 'master'));
			$this->mpid = posix_getpid();
			$this->run();
			$this->processWait();
		} catch (\Exception $e) {
			die('ALL ERROR: ' . $e->getMessage());
		}
	}

	/**
	 * 运行
	 */
	public function run() {
		for ($i = 0; $i < $this->max_precess; $i++) {
			$this->CreateProcess();
		}
	}

	/**
	 * 创建进程
	 * @param null $index
	 * @return mixed
	 */
	public function CreateProcess($index = null) {
		$process = new \swoole_process(function (\swoole_process $worker) use ($index) {
			if (is_null($index)) {
				$index = $this->new_index;
				$this->new_index++;
			}
			\swoole_set_process_name(sprintf('php-ps:%s', $index));
			for ($j = 0; $j < 16000; $j++) {
				$this->checkMpid($worker);
				echo "msg: {$j}\n";
				sleep(1);
			}
		}, false, false);
		$pid = $process->start();
		$this->works[$index] = $pid;
		return $pid;
	}

	/**
	 *检测主进程
	 * @param $worker
	 */
	public function checkMpid(&$worker) {
		if (!\swoole_process::kill($this->mpid, 0)) {
			$worker->exit();
			// 这句提示,实际是看不到的.需要写到日志中
			echo "Master process exited, I [{$worker['pid']}] also quit\n";
		}
	}

	/**
	 * 进程重启
	 * @param $ret
	 * @throws \Exception
	 */
	public function rebootProcess($ret) {
		$pid = $ret['pid'];
		$index = array_search($pid, $this->works);
		if ($index !== false) {
			$index = intval($index);
			$new_pid = $this->CreateProcess($index);
			echo "rebootProcess: {$index}={$new_pid} Done\n";
			return;
		}
		throw new \Exception('rebootProcess Error: no pid');
	}

	/**
	 *进程等待
	 * @throws \Exception
	 */
	public function processWait() {
		while (1) {
			if (count($this->works)) {
				$ret = \swoole_process::wait();
				if ($ret) {
					$this->rebootProcess($ret);
				}
			} else {
				break;
			}
		}
	}
}

$util=new MultiProcessUtil();
$util->run();