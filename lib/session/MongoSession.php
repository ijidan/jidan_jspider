<?php
namespace Lib\Session;

use Lib\BaseMongodbModel;

/**
 *
 * 可参考：Symfony\Component\HttpFoundation\Session\Storage\Handler\MongoDbSessionHandler.php
 * 表结构如下
 * {
 *      "_id":"",
 *      "value":"",
 *      "time":"",
 *      "expire":""
 * }
 * Mongo Session 支持
 * Class MongoSession
 * @package Lib\Session
 */
class MongoSession implements \SessionHandlerInterface
{
    private $mongo;
    private $ttl;

	/**
	 * 构造函数
	 * MongoSession constructor.
	 * @param $config
	 * @throws \Exception
	 */
    public function __construct($config)
    {
        $database = $config['database'];
        $collection = $config['collection'];
        if (empty($database) || empty($collection)) {
            throw new \Exception("you must provide database and collection");
        }
        $this->mongo = new BaseMongodbModel($database, $collection);
        $this->ttl = $config['ttl'] ? (int) $config['ttl'] : 7200;
    }

	/**
	 * 打开
	 * @param string $path
	 * @param string $name
	 * @return bool
	 */
    public function open($path, $name)
    {
        return true;
    }

	/**
	 * 关闭
	 * @return bool
	 */
    public function close()
    {
        return true;
    }

	/**
	 * 读
	 * @param string $sid
	 * @return string
	 */
    public function read($sid)
    {
        $data = $this->mongo->findOne(['_id' => $sid]);
        return $data['expire'] > time() ? $data['value'] : '';
    }

	/**
	 * 写
	 * @param string $sid
	 * @param string $data
	 * @return bool
	 */
    public function write($sid, $data)
    {
        $update = [
            '$set' => [
                'value' => $data,
                'time' => time(),
                'expire' => time() + $this->ttl
            ]
        ];
        $this->mongo->updateOne(['_id' => $sid], $update, ['upsert' => true]);
        return true;
    }

	/**
	 * 删除
	 * @param string $sid
	 * @return bool
	 */
    public function destroy($sid)
    {
        $this->mongo->deleteOne(['_id' => $sid]);
        return true;
    }

	/**GC
	 * @param int $maxLifeTime
	 * @return bool
	 */
    public function gc($maxLifeTime)
    {
        $this->mongo->deleteMany(['expire' => ['$lt' => time()]]);
        return true;
    }
}