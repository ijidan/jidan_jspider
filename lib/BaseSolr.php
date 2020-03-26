<?php

namespace Lib;

use Lib\Net\BaseService;
use Lib\Util\Config;
use Lib\Util\Paginate;

/**
 * 基础Model
 * Class BaseSolr
 * @package Lib
 */
abstract class BaseSolr {

	/**
	 * @var \SolrClient $solr
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
	 * @param array $qKvMap
	 * @param string $order
	 * @param string $orderType
	 * @return array|mixed
	 * @throws \ErrorException
	 */
	public static function findOne(array $qKvMap, $order = "", $orderType = "ASC") {
		$data = self::find($qKvMap, $order, $orderType, 0, 1);
		return $data ? $data[0] : [];
	}

	/**
	 * 查找
	 * @param array $qKvMap
	 * @param string $order
	 * @param string $orderType
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 * @throws \ErrorException
	 */
	public static function find(array $qKvMap, $order = "", $orderType = "ASC", $offset = 0, $limit = 0) {
		$model = self::getModel();
		$solr = $model->solr;
		$qList = [];
		foreach ($qKvMap as $key => $value) {
			$qList[] = "{$key}:{$value}";
		}
		$qString = $qList ? join(' AND ', $qList) : '*:*';
		$query = new \SolrQuery();
		$query->setQuery($qString);
		if ($order && $orderType) {
			$map = [
				'asc'  => \SolrQuery::ORDER_ASC,
				'desc' => \SolrQuery::ORDER_DESC
			];
			$query->addSortField($order, $map[strtolower($orderType)]);
		}
		if ($offset) {
			$query->setStart($offset);
		}
		if ($limit) {
			$query->setRows($limit);
		}
		$response = $solr->query($query);
		if (!$response->success()) {
			return [];
		}
		$resArray = $response->getArrayResponse();
		$docs = $resArray['response']['docs'];
		return $docs;
	}

	/**
	 * 分页查询
	 * @param Paginate $paginate
	 * @param array $qKvMap
	 * @param string $order
	 * @param string $orderType
	 * @return array
	 * @throws \ErrorException
	 */
	public static function paginate(Paginate &$paginate, array $qKvMap, $order = "", $orderType = "ASC") {
		$model = self::getModel();
		$limit = $paginate->getLimit();
		$count = $model->count($qKvMap);
		$paginate->setItemTotal($count);
		$data = $model->find($qKvMap, $order, $orderType, $limit[0], $limit[1]);
		return $data;
	}


	/**
	 * 查询总数
	 * @param array $qKvMap
	 * @return int
	 * @throws \ErrorException
	 */
	public static function count(array $qKvMap) {
		$model = self::getModel();
		$solr = $model->solr;
		$qList = [];
		foreach ($qKvMap as $key => $value) {
			$qList[] = "{$key}:{$value}";
		}
		$qString = $qList ? join(' AND ', $qList) : '*:*';
		$query = new \SolrQuery();
		$query->setQuery($qString);
		$response = $solr->query($query);
		if (!$response->success()) {
			return 0;
		}
		$resArray = $response->getArrayResponse();
		$cnt = $resArray['response']['numFound'];
		return $cnt;
	}

	/**
	 * 插入一条数据
	 * @param array $kvMap
	 * @return mixed
	 * @throws \ErrorException
	 */
	public static function insert(array $kvMap) {
		ksort($kvMap);
		$model = self::getModel();
		$solr = $model->solr;
		$doc = new \SolrInputDocument();
		foreach ($kvMap as $key => $value) {
			$doc->addField($key, $value);
		}
		$solr->addDocument($doc);
		$response = $solr->commit();
		return $response;
	}

	/**
	 * 批量插入多条数据
	 * @param array $kvMapList
	 * @throws \ErrorException
	 */
	public static function batchInsert(array $kvMapList) {
		if (count($kvMapList) == count($kvMapList, 1)) {
			throw new \InvalidArgumentException('must be a multi array');
		}
		foreach ($kvMapList as $kvMap) {
			self::insert($kvMap);
		}
	}

	/**
	 * 更新
	 * @param array $qKvMap
	 * @param $id
	 * @return mixed
	 * @throws \ErrorException
	 */
	public static function update(array $qKvMap, $id) {
		if (!$qKvMap) {
			throw new \InvalidArgumentException('qkvMap cant be empty');
		}
		$model = self::getModel();
		$solr = $model->solr;
		$qList = ["id:{$id}"];
		foreach ($qKvMap as $key => $value) {
			$qList[] = "{$key}:{$value}";
		}
		$qString = join(' AND ', $qList);
		$query = new \SolrQuery();
		$query->setQuery($qString);
		$response = $solr->commit();
		return $response->success();
	}

	/**
	 * 删除
	 * @param array $qKvMap
	 * @return mixed
	 * @throws \ErrorException
	 */
	public static function delete(array $qKvMap) {
		$model = self::getModel();
		$solr = $model->solr;
		$qList = [];
		foreach ($qKvMap as $key => $value) {
			$qList[] = "{$key}:{$value}";
		}
		$qString = $qList ? join(' AND ', $qList) : '*:*';
		$solr->deleteByQuery($qString);
		$response = $solr->commit();
		return $response->success();
	}

	/**
	 * 根据唯一ID删除
	 * @param $id
	 * @return mixed
	 * @throws \ErrorException
	 */
	public static function deleteById($id) {
		$model = self::getModel();
		$solr = $model->solr;
		$solr->deleteById($id);
		$response = $solr->commit();
		return $response->success();
	}

	/**
	 * 根据唯一ID批量删除
	 * @param array $ids
	 * @return mixed
	 * @throws \ErrorException
	 */
	public static function deleteByIds(array $ids) {
		$model = self::getModel();
		$solr = $model->solr;
		$solr->deleteByIds($ids);
		$response = $solr->commit();
		return $response->success();
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
			$config = $solrConfig[$connectionName];
			$config['path'] = "/solr/{$coreName}";
			$client = new \SolrClient($config);
			self::$instance[$connectionName] = $client;
		}
		return self::$instance[$connectionName];
	}

	/**
	 * 分词
	 * @param $txt
	 * @param bool $rawData
	 * @param string $fieldType
	 * @return array
	 * @throws \ErrorException
	 */
	public static function splitWord($txt, $rawData = false, $fieldType = 'text_smartcn') {
		$model = self::getModel();
		$solr = $model->solr;
		$options = $solr->getOptions();
		$host = $options['hostname'];
		$port = $options['port'];
		$path = $options['path'];
		$coreName = $model->getCoreName();
		$url = "http://{$host}:{$port}/{$path}/analysis/field";
		$params = [
			'analysis.fieldtype'  => $fieldType,
			'analysis.fieldvalue' => $txt,
			'analysis.showmatch'  => 'true',
			'verbose_output'      => 0,
			'wt'                  => 'json'
		];
		$rsp = BaseService::sendGetRequest($url, $params);
		if ($rsp->fail()) {
			return [];
		}
		$jsonData = $rsp->getData();
		$data = \json_decode($jsonData, true);
		$data = $data['analysis']['field_types'][$fieldType]['index'][1];
		return $rawData ? $data : array_column($data, 'text');
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