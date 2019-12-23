<?php

namespace Lib;

use Lib\Util\Config;
use Lib\Util\DBUtil;
use MongoDB\Client;
use MongoDB\BSON\ObjectID;
use MongoDB\Collection;
use MongoDB\Database;

/**
 * 可参考：
 * http://mongodb.github.io/mongo-php-library/classes/collection/
 * http://php.net/manual/zh/mongodb-driver-query.construct.php
 * mongoDB基础操作类
 * Class BaseMongodbModel
 * @package Lib
 */
class BaseMongodbModel {
	/** @var Database $db */
	protected $db;
	/** @var  Collection $collection */
	protected $collection;
	protected $uid = 0;
	protected $dbName = '';
	protected $tableName = '';

	protected static $instances = [];

	/**
	 * 构造函数
	 * BaseMongodbModel constructor.
	 * @param $db
	 * @param $table
	 * @throws \Exception
	 */
	public function __construct($db, $table) {
		if (is_numeric($db)) {
			$uid = intval($db);
			if (empty($uid)) {
				throw new \Exception('Error User');
			}
			$this->uid = $uid;
			$db = DBUtil::getDbName($uid);
		}
		$this->dbName = $db;
		$this->tableName = $table;
		$database = Config::loadConfig('database')["mongodb"][$db]['database'];
		$client = self::dbInstance($db);
		$this->db = $client->selectDatabase($database);
		$this->collection = $this->db->selectCollection($this->tableName);
	}

	/**
	 * 实例
	 * @param $db
	 * @return mixed
	 * @throws \ErrorException
	 */
	public static function dbInstance($db) {
		if (!isset(self::$instances[$db])) {
			$config = Config::loadConfig('database')[$db];
			$uri = $config['server'] ? 'mongodb://' . $config['server'] : '';
			$options = $config['options'] ?: [];
			//user php array
			$driverOptions = [
				'typeMap' => [
					'root'     => 'array',
					'document' => 'array',
					'array'    => 'array'
				]
			];
			self::$instances[$db] = new Client($uri, $options, $driverOptions);
		}
		return self::$instances[$db];
	}

	/**
	 * 验证ID
	 * @param $id
	 * @return bool
	 */
	public static function isValidObjectId($id) {
		if ($id instanceof ObjectID) {
			return true;
		}
		$valid = false;
		try {
			$id = new ObjectID($id);
			$valid = true;
		} catch (\Exception $e) {
			//do nothing
		}
		return $valid;
	}

	/**
	 * 插入一条数据
	 * @param $document
	 * @param array $options
	 * @return \MongoDB\InsertOneResult
	 */
	public function insertOne($document, array $options = []) {
		return $this->collection->insertOne($document, $options);
	}

	/**
	 * 插入多条数据
	 * @param array $documents
	 * @param array $options
	 * @return \MongoDB\InsertManyResult
	 */
	public function insertMany(array $documents, array $options = []) {
		return $this->collection->insertMany($documents, $options);
	}

	/**
	 * 删除一条数据
	 * @param $filter
	 * @param array $options
	 * @return \MongoDB\DeleteResult
	 */
	public function deleteOne($filter, array $options = []) {
		return $this->collection->deleteOne($filter, $options);
	}

	/**
	 * 删除多条数据
	 * @param $filter
	 * @param array $options
	 * @return \MongoDB\DeleteResult
	 */
	public function deleteMany($filter, array $options = []) {
		return $this->collection->deleteMany($filter, $options);
	}

	/**
	 * 总数
	 * @param array $filter
	 * @param array $options
	 * @return int
	 */
	public function count($filter = [], array $options = []) {
		return $this->collection->count($filter, $options);
	}

	/**
	 * 查找一条
	 * @param array $filter
	 * @param array $options
	 * @return array|null|object
	 */
	public function findOne($filter = [], array $options = []) {
		return $this->collection->findOne($filter, $options);
	}

	/**
	 * 查找多条
	 * @param array $filter
	 * @param array $fields
	 * @param array $sort
	 * @param int $limit
	 * @param int $skip
	 * @param array $options
	 * @return array
	 */
	public function find($filter = [], $fields = [], $sort = [], $limit = 0, $skip = 0, array $options = []) {
		$cursor = $this->findCursor($filter, $fields, $sort, $limit, $skip, $options);
		$rows = [];
		foreach ($cursor as $row) {
			$rows[] = $row;
		}
		return $rows;
	}

	/**
	 * @param array $filter
	 * @param array $options
	 * @param Paginate|null $paginate 分页对象
	 * @param array $fields
	 * @param array $sort
	 * @return array
	 */
	public function paginate(Paginate &$paginate, $filter = [], array $options = [], $fields = [], $sort = []) {
		$limit = $paginate->getLimit();
		$count = $this->count($filter);
		$paginate->setItemTotal($count);
		$ite = $this->findCursor($filter, $fields, $sort, $limit[1], $limit[0], $options);
		$rows = iterator_to_array($ite);
		return $rows;
	}

	/**
	 * 游标
	 * @param array $filter
	 * @param array $fields
	 * @param array $sort
	 * @param int $limit
	 * @param int $skip
	 * @param array $options
	 * @return \MongoDB\Driver\Cursor
	 */
	public function findCursor($filter = [], $fields = [], $sort = [], $limit = 0, $skip = 0, array $options = []) {
		if ($fields) {
			$options['projection'] = $fields;
		}
		if ($sort) {
			$options['sort'] = $sort;
		}
		if ($limit) {
			$options['limit'] = $limit;
		}
		if ($skip) {
			$options['skip'] = $skip;
		}
		return $this->collection->find($filter, $options);
	}

	/**
	 * 更新一条
	 * @param $filter
	 * @param $update
	 * @param array $options
	 * @return \MongoDB\UpdateResult
	 */
	public function updateOne($filter, $update, array $options = []) {
		return $this->collection->updateOne($filter, $update, $options);
	}

	/**
	 * 更新多条
	 * @param $filter
	 * @param $update
	 * @param array $options
	 * @return \MongoDB\UpdateResult
	 */
	public function updateMany($filter, $update, array $options = []) {
		return $this->collection->updateMany($filter, $update, $options);
	}

	/**
	 * 查找并更新
	 * @param $filter
	 * @param $update
	 * @param array $options
	 * @return null|object
	 */
	public function findOneAndUpdate($filter, $update, array $options = []) {
		return $this->collection->findOneAndUpdate($filter, $update, $options);
	}

	/**
	 * 魔术方法
	 * @param $method
	 * @param $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters) {
		return call_user_func_array([$this->collection, $method], $parameters);
	}

	/**
	 * 聚合
	 * @param array $pipeline
	 * @param array $options
	 * @return mixed
	 */
	public function aggregate(array $pipeline, array $options = []) {
		return $this->collection->aggregate($pipeline, $options);
	}

	/**
	 * 去重
	 * @param $fieldName
	 * @param array $filter
	 * @param array $options
	 * @return \mixed[]
	 */
	public function distinct($fieldName, $filter = [], array $options = []) {
		return $this->collection->distinct($fieldName, $filter, $options);
	}

	/**
	 * 执行命令
	 * @param array|object $command Command document
	 * @param array $options
	 * @return \MongoDB\Driver\Cursor
	 */
	public function command($command, array $options = []) {
		return $this->db->command($command, $options);
	}
}