<?php

namespace Lib;

use Lib\DataBase\BaseDeleteStatement;
use Lib\DataBase\BaseInsertStatement;
use Lib\DataBase\BaseSelectStatement;
use Lib\DataBase\BaseUpdateStatement;
use Lib\Util\Config;
use Lib\Util\Paginate;
use PSolr\Client\SolrClient;
use PSolr\Request\Add;
use PSolr\Request\Document;

/**
 * Class BaseSolr
 * @package Lib
 */
abstract class BaseSolr {

	/**
	 * @var SolrClient $solr
	 */
	protected $solr;
	/**
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * 构造函数
	 * BaseModel constructor.
	 * @throws \ErrorException
	 */
	public function __construct() {
	}


	/**
	 * 查找一个
	 * @param string $where
	 * @param array $values
	 * @param string $order
	 * @param string $orderType
	 * @return array|mixed
	 * @throws \ErrorException
	 */
	public static function findOne($where = "", array $values = [], $order = "", $orderType = "ASC") {
		$data = self::find($where, $values, $order, $orderType);
		return $data ? $data[0] : [];
	}

	/**
	 * 查找
	 * @param string $where
	 * @param array $values
	 * @param string $order
	 * @param string $orderType
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 * @throws \ErrorException
	 */
	public static function find($where = "", array $values = [], $order = "", $orderType = "ASC", $offset = 0, $limit = 0) {
		$model = self::getModel();
		if (strpos($where, 'platform') !== false) {
		}
		$fullTableName = $model->getFullTableName();
		/** @var BaseSelectStatement $selectStatement */
		$selectStatement = $model->pdo->select()->from($fullTableName);
		self::buildWhere($selectStatement, $where, $values);
		if ($limit) {
			$selectStatement->limit($limit, $offset);
		}
		if ($order) {
			$selectStatement->orderBy($order, $orderType);
		}
		$stmt = $selectStatement->execute();
		$data = $stmt->fetchAll();
		return $data;
	}

	/**
	 * 分页查询
	 * @param Paginate $paginate
	 * @param string $where
	 * @param array $values
	 * @param string $order
	 * @param string $orderType
	 * @return array
	 * @throws \ErrorException
	 */
	public static function paginate(Paginate &$paginate, $where = "", array $values = [], $order = "", $orderType = "ASC") {
		$model = self::getModel();
		$limit = $paginate->getLimit();
		$count = $model->count($where, $values);
		$paginate->setItemTotal($count);
		$data = $model->find($where, $values, $order, $orderType, $limit[0], $limit[1]);
		return $data;
	}

	/**
	 * 查询总数
	 * @param string $where
	 * @param array $values
	 * @return int
	 * @throws \ErrorException
	 */
	public static function count($where = "", array $values = []) {
		$model = self::getModel();
		$fullTableName = $model->getFullTableName();
		/** @var BaseSelectStatement $selectStatement */
		$selectStatement = $model->pdo->select()->from($fullTableName);
		self::buildWhere($selectStatement, $where, $values);
		$stmt = $selectStatement->execute();
		$cnt = $stmt->rowCount();
		return $cnt;
	}

	/**
	 * 插入数据
	 * @param array $kvMap
	 * @return \PSolr\Response\Response|\SimpleXMLElement
	 * @throws \ErrorException
	 */
	public static function insert(array $kvMap) {
		$model = self::getModel();
		$solr=$model->solr;
		$add = Add::factory();
		$document        = new Document();
		foreach ($kvMap as $key=>$value){
			$document->$key=$value;
		}
		$add->addDocument($document);
		$response = $add->sendRequest($solr,['Content-Type'=>'application/json']);
		return  $response;
	}

	/**
	 * 更新
	 * @param array $kvMap_
	 * @param string $where
	 * @param array $values_
	 * @return int
	 * @throws \ErrorException
	 */
	public static function update(array $kvMap_, $where = "", array $values_ = []) {
		$argsNum = func_num_args();
		if ($argsNum == 2) {
			$_kvMap = $_values = [];
			self::genKvMapAndValues($kvMap_, $_kvMap, $_values);
			$kvMap = $_kvMap;
			$values = $_values;
		} else {
			$kvMap = $kvMap_;
			$values = $values_;
		}

		$model = self::getModel();
		$fullTableName = $model->getFullTableName();
		/** @var BaseUpdateStatement $updateStatement */
		$updateStatement = $model->pdo->update($kvMap)->table($fullTableName);
		self::buildWhere($updateStatement, $where, $values);
		$affectedRows = $updateStatement->execute();
		return $affectedRows;
	}

	/**
	 * 删除
	 * @param string $where
	 * @param array $values
	 * @return int
	 * @throws \ErrorException
	 */
	public static function delete($where = "", array $values = []) {
		$model = self::getModel();
		$fullTableName = $model->getFullTableName();
		/** @var BaseDeleteStatement $deleteStatement */
		$deleteStatement = $model->pdo->delete()->from($fullTableName);
		self::buildWhere($deleteStatement, $where, $values);
		$affectedRows = $deleteStatement->execute();
		return $affectedRows;
	}

	/**
	 * @param BaseSelectStatement|BaseInsertStatement|BaseUpdateStatement|BaseDeleteStatement $statement
	 * @param string $where
	 * @param array $values
	 */
	private static function buildWhere(&$statement, $where = "", array $values = []) {
		if ($where) {
			$statement->setConditionWhere($where);
			$statement->setConditionValues($values);
		}
	}

	/**
	 * 获取Model
	 * @return BaseSolr
	 * @throws \ErrorException
	 */
	private static function getModel() {
		$cls = get_called_class();
		/** @var BaseSolr $model */
		$model = new $cls();
		$connectionName = $model->getConnectionName();
		$coreName = $model->getCoreName();
		$model->solr = self::getInstance($connectionName, $coreName);
		return $model;
	}


	/**
	 * 单例
	 * @param $connectionName
	 * @param $coreName
	 * @return mixed
	 * @throws \ErrorException
	 */
	private static function getInstance($connectionName, $coreName) {
		if (is_null(self::$instance[$connectionName])) {
			$solrConfig = Config::loadConfig('solr');
			if (!isset($solrConfig[$connectionName])) {
				throw new \RuntimeException('solr连接配置不存在：' . $connectionName);
			}
			$dbConfig = $solrConfig[$connectionName];
			$host = $dbConfig["host"];
			$port = $dbConfig["port"];
			$solr = SolrClient::factory(array(
				'base_url'  => "http://{$host}:{$port}",
				'base_path' => "/solr/{$coreName}",
			));
			self::$instance[$connectionName] = $solr;
		}
		return self::$instance[$connectionName];
	}

	/**
	 * 构造Question Mark
	 * @param array $data
	 * @return string
	 */
	public static function buildQuestionMark(array $data) {
		if (!$data) {
			throw new \InvalidArgumentException("param error");
		}
		$questionMark = "";
		array_walk($data, function ($item) use (&$questionMark) {
			$questionMark .= "?,";
		});
		$questionMark = rtrim($questionMark, ",");
		return $questionMark;
	}

	/**
	 * 产生相关的数据
	 * @param $insData
	 * @param array $kvMap
	 * @param array $values
	 */
	public static function genKvMapAndValues($insData, array &$kvMap, array &$values) {
		$kvMap_ = [];
		$values_ = [];
		array_walk($insData, function ($value, $key) use (&$kvMap_, &$values_) {
			$kvMap_[$key] = "?";
			array_push($values_, $value);
		});
		$kvMap = $kvMap_;
		$values = $values_;
	}

	/**
	 * 数据库名
	 * @return mixed
	 */
	abstract public function getConnectionName();

	/**
	 * 表名
	 * @return mixed
	 */
	abstract public function getCoreName();

}