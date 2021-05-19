<?php

namespace Business\House;


use Model\Spider\IdParse;


/**
 * 贝壳美国二手房
 * Class KeUSSecondHouse
 * @package Business\House
 */
class KeUSSecondHouse extends HouseBase {

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
			$id = str_replace('/homes/us/', '', $data);
			$id = str_replace('.html', '', $id);
			array_push($allIdList, $id);
		}
		return $allIdList;
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
		$this->multiReplace($housePrice, ['万美元)', '(']);
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
	 * 清理图片
	 * @param $originUrl
	 * @return string
	 */
	public function cleanImage($originUrl) {
		return $originUrl;
	}

	/**
	 * 计算所有子ID
	 * @param array $dataList
	 * @return array
	 */
	private function computeAllChildIds(array $dataList) {
		$allChildIds = [];
		foreach ($dataList as $data) {
			$content = $data['f_parse_content'];
			$detail = \json_decode($content, true);
			$listingType = $detail['listingType'];
			if (isset($detail['listingModel']['childListingIds'])) {
				$childListingIds = $detail['listingModel']['childListingIds'];
				if ($childListingIds) {
					foreach ($childListingIds as $id) {
						if (!in_array($allChildIds, $id)) {
							array_push($allChildIds, $id);
						}
					}
				}

			}
		}
		return $allChildIds;
	}

	/**
	 * 生成EXCEL
	 * @return bool
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
				'f_currency_symbol'   => '$',
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
			$this->writeHouseEvalUS($id,$data);



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
}