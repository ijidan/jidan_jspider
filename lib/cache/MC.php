<?php
namespace Lib\Cache;

use Lib\Util\Config;

/**
 * Memcached
 * Class MC
 * @package Lib
 */
class MC
{
	/**
	 * 实例
	 * @var MC $instance
	 */
    protected static $instance;

	/**
	 * 获取实例
	 * @return MC|\Memcached
	 * @throws \ErrorException
	 */
    private static function getInstance()
    {
        if (!empty(self::$instance)) {
            return self::$instance;
        }

        $config = Config::loadConfig('cache');
        $driver = $config['driver'];
        if (!extension_loaded($driver)) {
            throw new \Exception("error driver [$driver]");
        }
        $servers = $config['servers'] ?: [];
        $options = $config['options'] ?: [];
        $mc = new \Memcached();
        $mc->setOptions($options);
        $mc->addServers($servers);
        return self::$instance = $mc;
    }

	/**
	 * 获取数据
	 * @param $key
	 * @param null $default
	 * @param null $cas
	 * @return null
	 * @throws \ErrorException
	 */
    public static function get($key, $default = NULL, $cas = NULL)
    {
        $ret = self::getInstance()->get($key, null, $cas);
        return $ret ?: $default;
    }

	/**
	 * 设置数据
	 * @param $key
	 * @param $value
	 * @param int $ttl
	 * @return mixed
	 * @throws \ErrorException
	 */
    public static function set($key, $value, $ttl = 86400)
    {
        return self::getInstance()->set($key, $value, $ttl);
    }

	/**
	 * 添加数据
	 * @param $key
	 * @param $value
	 * @param int $ttl
	 * @return mixed
	 * @throws \ErrorException
	 */
    public static function add($key, $value, $ttl = 86400)
    {
        return self::getInstance()->add($key, $value, $ttl);
    }

	/**
	 * INC
	 * @param $key
	 * @param $offset
	 * @param int $init
	 * @param int $ttl
	 * @return int
	 * @throws \ErrorException
	 */
    public static function inc($key, $offset, $init = 0, $ttl = 86400)
    {
        return self::getInstance()->increment($key, $offset, $init, $ttl);
    }

	/**
	 * CAS数据
	 * @param $cas
	 * @param $key
	 * @param $value
	 * @param int $ttl
	 * @return mixed
	 * @throws \ErrorException
	 */
    public static function cas($cas, $key, $value, $ttl = 86400)
    {
        return self::getInstance()->cas($cas, $key, $value, $ttl);
    }

	/**
	 * 删除数据
	 * @param $key
	 * @return mixed
	 * @throws \ErrorException
	 */
    public static function delete($key)
    {
        return self::getInstance()->delete($key);
    }
}