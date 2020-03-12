<?php
namespace Tools\Controllers;

use Lib\BaseModel;
use MongoDB\BSON\ObjectID;
use Tools\Models\Pagination;

/**
 * 日志相关展示逻辑
 * Class LogController
 * @package Tools\Controllers
 */
class LogController extends IndexController {
	/**
	 * 每日错误汇总
	 * @param $params
	 */
	public function appDailyLogs($params) {
		$date = $params['date'] ?: date('Y-m-d');
		$logModel = new BaseModel('logger', 'logger_app_daily');
		$list = $logModel->find(['date' => $date], [], ['count' => -1]);
		return $this->renderTemplate('log/appsDaily.php', [
			'list' => $list,
			'date' => $date
		]);
	}

	public function appLogs($params) {
		$currentPage = $params['page'] ?: 1;
		$filter = [];
		$sort = ['_id' => -1];

		$logModel = new BaseModel('logger', 'logger_app');
		$count = $logModel->count($filter);
		$pageModel = new Pagination($count, $currentPage, $params);
		$list = $logModel->find($filter, [], $sort, $pageModel->getPageSize(), $pageModel->getSkips());

		return $this->renderTemplate('log/apps.php', [
			'list'     => $list,
			'pageInfo' => $pageModel->getPageInfo()
		]);
	}

	public function appLogDetails($params) {
		$id = $params['id'] ?: '';
		$logModel = new BaseModel('logger', 'logger_app');
		$item = $logModel->findOne(['_id' => new ObjectID($id)]);
		return $this->json($item);
	}

	public function toolLogs($params) {
		$currentPage = $params['page'] ?: 1;
		$filter = [];
		$sort = ['_id' => -1];

		$logModel = new BaseModel('logger', 'logger_tools');
		$count = $logModel->count($filter);
		$pageModel = new Pagination($count, $currentPage, $params);
		$list = $logModel->find($filter, [], $sort, $pageModel->getPageSize(), $pageModel->getSkips());

		return $this->renderTemplate('log/tools.php', [
			'list'     => $list,
			'pageInfo' => $pageModel->getPageInfo()
		]);
	}

	public function toolLogDetails($params) {
		$id = $params['id'] ?: '';
		$logModel = new BaseModel('logger', 'logger_tools');
		$item = $logModel->findOne(['_id' => new ObjectID($id)]);
		return $this->json($item);
	}

	public function eventLogs($params) {
		$currentPage = $params['page'] ?: 1;
		$filter = [];
		$sort = ['_id' => -1];

		$logModel = new BaseModel('logger', 'logger_events');
		$count = $logModel->count($filter);
		$pageModel = new Pagination($count, $currentPage, $params);
		$list = $logModel->find($filter, [], $sort, $pageModel->getPageSize(), $pageModel->getSkips());

		return $this->renderTemplate('log/events.php', [
			'list'     => $list,
			'pageInfo' => $pageModel->getPageInfo()
		]);
	}

	public function eventLogDetails($params) {
		$id = $params['id'] ?: '';
		$logModel = new BaseModel('logger', 'logger_events');
		$item = $logModel->findOne(['_id' => new ObjectID($id)]);
		return $this->json($item);
	}
}