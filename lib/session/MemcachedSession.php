<?php

namespace Lib\Session;

use Lib\MC;

/**
 * Memcached Session 支持
 * Class MemcachedSession
 * @package Lib\Session
 */
class MemcachedSession implements \SessionHandlerInterface {
	private $ttl;
	private $prefix;

	/**
	 * 构造函数
	 * MemcachedSession constructor.
	 * @param $config
	 */
	public function __construct($config) {
		if ($diff = array_diff(array_keys($config), array('prefix', 'expiretime'))) {
			throw new \InvalidArgumentException(sprintf('The following options are not supported "%s"', implode(', ', $diff)));
		}
		$this->ttl = $config['expiretime'] ? (int)$config['expiretime'] : 86400;
		$this->prefix = $config['prefix'] ?: 'session_';
	}

	/**
	 * 打开
	 * @param string $path
	 * @param string $name
	 * @return bool
	 */
	public function open($path, $name) {
		return true;
	}

	/**
	 * 关闭
	 * @return bool
	 */
	public function close() {
		return true;
	}

	/**
	 * 读
	 * @param string $sid
	 * @return string|null
	 * @throws \ErrorException
	 */
	public function read($sid) {
		return MC::get($this->prefix . $sid, '');
	}

	/**
	 * 写
	 * @param string $sid
	 * @param string $data
	 * @return bool
	 * @throws \ErrorException
	 */
	public function write($sid, $data) {
		MC::set($this->prefix . $sid, $data, $this->ttl);
		return true;
	}

	/**
	 * 删除
	 * @param string $sid
	 * @return bool
	 * @throws \ErrorException
	 */
	public function destroy($sid) {
		MC::delete($this->prefix . $sid);
		return true;
	}

	/**
	 * GC
	 * @param int $maxlifetime
	 * @return bool
	 */
	public function gc($maxlifetime) {
		return true;
	}
}