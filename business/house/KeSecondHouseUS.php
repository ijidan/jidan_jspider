<?php

namespace Business\House;


use Model\Spider\HouseEvaluateUS;
use Model\Spider\IdParse;


/**
 * 贝壳美国二手房
 * Class KeUSSecondHouse
 * @package Business\House
 */
class KeSecondHouseUS extends HouseBase {

	//唯一ID
	protected $uniqueId = 'KeUSSecond';

	/*
	 * 映射
	 * @var array
	 */
	protected $data = [];

	public $baseUrl = 'https://i.ke.com/homes/us/';

	/**
	 * 子目录
	 * @var string
	 */
	protected $platformsSubDir = 'KeUSSecond';



	/**
	 * 获取平台
	 * @return mixed
	 */
	public function getPlatform() {
		// TODO: Implement getPlatform() method.
	}

	/**
	 * 计算列表页URL
	 * @param int $page
	 * @return mixed
	 */
	public function computeListPageUrl($page = 1) {
		$url = $this->baseUrl . "pg{$page}";
		return $url;
	}


	/**
	 * 爬取总数
	 * @param $shortUrl
	 * @return mixed
	 * @throws \Exception
	 */
	public function crawlPageCnt($shortUrl) {
		return 100;
	}


	/**
	 * 爬取所有ID
	 * @param $shortUrl
	 * @return mixed
	 * @throws \Exception
	 */
	public function crawAllId($shortUrl) {
		$id = $this->extractId($shortUrl);
		$fileName = __FUNCTION__ . '_id_' . $id;
		$content = $this->fetchContentFromDb($fileName, $shortUrl);
		$dataList = $this->computeData($content, '.list-wrap dd h3 a', 'href');
		$allIdList = [];
		foreach ($dataList as $data) {
			$id=$data;
			$replaceList=$this->getReplaceList();
			$this->multiReplace($id,$replaceList);
			array_push($allIdList, $id);
		}
		return $allIdList;
	}

	/**
	 * 获取过滤列表
	 * @return array
	 */
	public function getReplaceList(){
		return ['/homes/us/','.html'];
	}


	/**
	 * 爬取详情页
	 * @param $id
	 * @return array|mixed
	 * @throws \Exception
	 */
	public function crawlDetail($id) {
		$fileName = __FUNCTION__ . '_id_' . $id;
		$url = $this->computeDetailPageUrl($id);
		$htmlContent = $this->fetchContentFromDb($fileName, $url);
		//标题
		$houseName = $this->computeOnlyOneData($htmlContent, '.name-box a h1');
		//地址
		$houseAddress = $this->computeOnlyOneData($htmlContent, '.info-box-detail .translation span');
		//价格
		$housePrice = $this->computeOnlyOneData($htmlContent, '.info-box-price-rmb');
		$priceReplacement=$this->getPriceReplacement();
		$this->multiReplace($housePrice, $priceReplacement);
		$housePriceRMB = $this->computeOnlyOneData($htmlContent, '.info-box-price-shuzi');
		//数据
		$houseInfoList = $this->computeData($htmlContent, '.intro-list li p span');
		$houseInfoMap = [];
		foreach ($houseInfoList as $idx => $houseInfo) {
			if ($idx % 2 == 0) {
				$houseInfoMap[$houseInfo] = $houseInfoList[$idx + 1];
			}
		}
		$houseItem = [
			'house_name'      => $houseName,
			'house_address'   => $houseAddress,
			'house_price'     => $housePrice,
			'house_price_rmb' => $housePriceRMB,
			'house_info'      => $houseInfoMap
		];
		//保存解析内容
		$this->doParse($id, $houseItem);
		return $houseItem;
	}


	/**
	 * 生成EXCEL
	 * @return bool
	 * @throws \ErrorException
	 */
	public function genExcel() {
		$dataList = IdParse::find('f_unique_id=?', [$this->uniqueId]);
		if (!$dataList) {
			$this->warning('无数据');
			return false;
		}
		//重组数据
		foreach ($dataList as $data) {
			$id = $data['f_origin_id'];
			$content = $data['f_parse_content'];
			$detail = \json_decode($content, true);

			$houseName = $detail['house_name'];
			$houseAddress = $detail['house_address'];
			$housePrice = $detail['house_price'];
			$housePriceRMB = $detail['house_price_rmb'];
			$houseInfo = $detail['house_info'];

			//过滤处理
			if ($houseInfo) {
				array_walk($houseInfo, function (&$value, $key) {
					if ($value == '暂无') {
						$value = '';
					}
				});
			}

			//价格
			$price = $housePrice;
			$priceReplacement=$this->getPriceReplacement();
			$this->multiReplace($price,$priceReplacement);
			//地址
			$houseAddressList = explode(',', $houseAddress);
			list($address, $city, $province, $postCode) = $houseAddressList;

			//物业类型
			$propertyType = $houseInfo['物业类型：'];
			$landArea = str_replace('平米', '', $houseInfo['土地面积：']);
			$sizeUnit = 'm²';

			//数据
			$houseLayout = $houseInfo['房产户型：'];
			$bedrooms = $this->extractNum('/(\d)卧/', $houseLayout);
			$bathroom = $this->extractNum('/(\d)卫/', $houseLayout);
			$houseArea = str_replace('平米', '', $houseInfo['房屋面积：']);
			$buildingTime = $houseInfo['建成年份：'];
			$handingTime = $houseInfo['上市日期：'];
			$houseStandard = $houseInfo['装修状况：'];
			$parkingSpace = $houseInfo['车位信息：'];
			$propertyInfo = $houseInfo['产权说明：'];

			$currencySymbol=$this->getCurrencySymbol();
			$data = [
				'f_origin_id'         => $id,
				'f_origin_parent_id'  => 0,
				'f_title'             => $houseName,
				'f_post_code'         => $postCode,
				'f_province'          => $province,
				'f_city'              => $city,
				'f_address'           => $address,
				'f_full_address'      => $houseAddress,
				'f_house_type'        => $propertyType,
				'f_house_area'        => $houseArea,
				'f_house_unit'        => $sizeUnit,
				'f_currency_symbol'   => $currencySymbol,
				'f_price'             => $price,
				'f_bedroom_num'       => $bedrooms,
				'f_bathroom_num'      => $bathroom,
				'f_parking_space_num' => $parkingSpace,

				'f_house_layout'   => $houseLayout,
				'f_building_time'  => $buildingTime,
				'f_handing_time'   => $handingTime,
				'f_land_area'      => $landArea,
				'f_balcony_area'   => '',
				'f_house_no'       => '',
				'f_house_floor'    => '', //楼层
				'f_house_standard' => $houseStandard, //装修标准
				'f_property_info'  => $propertyInfo,

				'f_spu_price'  => '',
				'f_spu_layout' => '',
				'f_spu_area'   => '',
				'f_tag'        => ''
			];
			$this->writeHouseEval($id,$data);
		}
	}

	/**
	 * 获取货币符号
	 * @return string
	 */
	public function getCurrencySymbol(){
		return '$';
	}

	/**
	 * 获取价格替换
	 * @return array
	 */
	public function getPriceReplacement(){
		return ['万美元)', '('];
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
		$record = HouseEvaluateUS::findOne($queryWhere, $queryParam);
		$data=$this->computeKeHouseData($originId,$data);
		if (!$record) {
			$insData = $data;
			$insData['f_create_time'] = time();
			$insData['f_update_time'] = 0;
			HouseEvaluateUS::insert($insData);
			$this->info('数据写入完毕：' . $originId);
		} else {
			$updateData = $data;
			$updateData['f_update_time'] = time();
			$id = $record['f_id'];
			HouseEvaluateUS::update($updateData, 'f_id=' . $id);
			$this->info('数据更新完毕：' . $originId);
		}
	}


	/**
	 * 计算详情页URL
	 * @param $id
	 * @return mixed
	 */
	public function computeDetailPageUrl($id) {
		$url = $this->baseUrl . $id . '.html';
		return $url;
	}

	/**
	 * 图片整理
	 * @param $originUrl
	 * @return mixed
	 */
	public function cleanImage($originUrl) {
		// TODO: Implement cleanImage() method.
	}
}