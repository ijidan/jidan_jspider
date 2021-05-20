<?php

namespace Business\House;

use Model\Spider\HouseEvaluateJP;

/**
 * 贝壳加拿大新房
 * Class KeUSSecondHouse
 * @package Business\House
 */
class KeNewHouseJP extends KeNewHouseUS {

	//唯一ID
	protected $uniqueId = 'KeJPNew';

	/*
	 * 映射
	 * @var array
	 */
	protected $data = [];

	public $baseUrl = 'https://i.ke.com/newhomes/jp/';

	/**
	 * 子目录
	 * @var string
	 */
	protected $platformsSubDir = 'KeJPNew';


	/**
	 * 爬取总数
	 * @param $shortUrl
	 * @return mixed
	 * @throws \Exception
	 */
	public function crawlPageCnt($shortUrl) {
		return 4;
	}


	/**
	 * 获取过滤列表
	 * @return array
	 */
	public function getReplaceList() {
		return ['/newhomes/jp/', '.html'];
	}

	/**
	 * 获取货币符号
	 * @return string
	 */
	public function getCurrencySymbol(){
		return '¥';
	}

	/**
	 * 获取价格替换
	 * @return array
	 */
	public function getPriceReplacement(){
		return ['万日元/套', '万日元'];
	}


	/**
	 * 海房评估
	 * @param $originId
	 * @param $data
	 * @throws \ErrorException
	 */
	public function writeHouseEval($originId, $data) {
		$queryWhere = 'f_unique_id =? and f_origin_id=?';
		$queryParam = [$this->uniqueId, $originId];
		$record = HouseEvaluateJP::findOne($queryWhere, $queryParam);
		$data=$this->computeKeHouseData($originId,$data);
		if (!$record) {
			$insData = $data;
			$insData['f_create_time'] = time();
			$insData['f_update_time'] = 0;
			HouseEvaluateJP::insert($insData);
			$this->info('数据写入完毕：' . $originId);
		} else {
			$updateData = $data;
			$updateData['f_update_time'] = time();
			$id = $record['f_id'];
			HouseEvaluateJP::update($updateData, 'f_id=' . $id);
			$this->info('数据更新完毕：' . $originId);
		}
	}



}