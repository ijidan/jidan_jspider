<?php

namespace Business\House;


use Model\Spider\IdParse;


/**
 * 贝壳美国新房
 * Class KeUSSecondHouse
 * @package Business\House
 */
class KeUSNewHouse extends KeUSSecondHouse {

	//唯一ID
	protected $uniqueId = 'KeUSNew';

	/*
	 * 映射
	 * @var array
	 */
	protected $data = [];

	public $baseUrl = 'https://i.ke.com/newhomes/us/';

	/**
	 * 子目录
	 * @var string
	 */
	protected $platformsSubDir = 'KeUSNew';


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
		return ['/newhomes/us/', '.html'];
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

		//标签
		$houseTag = $this->computeData($htmlContent, '.title-box .tag');
		//地址
		$houseAddress = $this->computeOnlyOneData($htmlContent, '.address .content');
		//价格
		$spuPrice = $this->computeOnlyOneData($htmlContent, '.info-box .local-price');
		$this->multiReplace($spuPrice, ['万美元']);
		$spuPriceRMB = $this->computeOnlyOneData($htmlContent, '.info-box .rmb-price');
		$this->multiReplace($spuPriceRMB, ['万']);

		//数据
		$houseInfoList = $this->computeData($htmlContent, 'div.introduction div span');
		$houseInfoMap = [];
		foreach ($houseInfoList as $idx => $houseInfo) {
			if ($idx % 2 == 0) {
				$houseInfoMap[$houseInfo] = $houseInfoList[$idx + 1];
			}
		}
		//户型
		$houseLayoutList = [];
		$houseTypeTitleList = $this->computeData($htmlContent, '.housetype-box .housetype-title');
		$houseTypePriceRMBList = $this->computeData($htmlContent, '.housetype-box .rmb-price');
		$houseTypeAreaList = $this->computeData($htmlContent, '.housetype-box .indoor-area');
		$houseTypePriceList = $this->computeData($htmlContent, '.housetype-box .local-price');
		if ($houseTypeTitleList) {
			foreach ($houseTypeTitleList as $idx => $title) {
				$housePrice = $houseTypePriceList[$idx];
				$this->multiReplace($housePrice, ['万美元/套']);
				$housePriceRMB = $houseTypePriceRMBList[$idx];
				$this->multiReplace($housePriceRMB, ['万/套']);
				$houseArea = $houseTypeAreaList[$idx];
				$this->multiReplace($houseArea, ['㎡']);
				$layout = [
					'house_title'     => $title,
					'house_price'     => $housePrice,
					'house_price_rmb' => $housePriceRMB,
					'house_area'      => $houseArea
				];
				array_push($houseLayoutList, $layout);
			}
		}
		$houseItem = [
			'house_address' => $houseAddress,
			'house_tag'     => \join(',', $houseTag),
			'spu_price'     => $spuPrice,
			'spu_price_rmb' => $spuPriceRMB,
			'house_info'    => $houseInfoMap,
			'house_layout'  => $houseLayoutList
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

			$houseTag = $detail['house_tag'];
			$houseAddress = $detail['house_address'];
			$spuPrice = $detail['spu_price'];
			$spuPriceRMB = $detail['spu_price_rmb'];
			$houseInfo = $detail['house_info'];
			$houseLayout = $detail['house_layout'];

			//过滤处理
			if ($houseInfo) {
				array_walk($houseInfo, function (&$value, $key) {
					$this->multiReplace($value, ['\n', '暂无', '']);
				});
			}

			//地址
			$address = '';
			$city = '';
			$province = '';
			$postCode = '';
			$this->multiReplace($houseAddress, ['地图', '\n', '']);
			$houseAddressList = explode(',', $houseAddress);
			$houseAddressListCnt = count($houseAddressList);
			switch ($houseAddressListCnt) {
				case 3:
					list($address, $city, $provincePostCode) = $houseAddressList;
					$this->multiReplace($provincePostCode, ['地图', '\n', '']);
					list($province, $postCode) = explode(' ', $provincePostCode);
					break;
				case 2:
					list($address, $provincePostCode) = $houseAddressList;
					$this->multiReplace($provincePostCode, ['地图', '\n', '']);
					list($province, $postCode) = explode(' ', $provincePostCode);
					break;
				default:
					$address = '';
					$city = '';
					$province = '';
					$postCode = '';
			}


			$this->multiReplace($address, ['地图', '\n', '']);
			$this->multiReplace($city, ['地图', '\n', '']);
			$this->multiReplace($province, ['地图', '\n', '']);
			$this->multiReplace($postCode, ['地图', '\n', '']);


			//物业类型
			$propertyType = $houseInfo['物业类型'];
			$sizeUnit = 'm²';

			//数据
			$houseName = $houseInfo['楼盘当地名称'];
			$houseStandard = $houseInfo['装修标准'];
			$propertyInfo = $houseInfo['产权年限'];
			$parkingSpace = $houseInfo['车位数量'];
			$this->multiReplace($parkingSpace, ['个', '']);

			list($houseLayoutStr, $priceStr, $housePriceRMBStr, $houseAreaStr) = $this->computeHouseLayout($houseLayout);
			foreach ($houseLayout as $idx => $layout) {

				$houseLayout = $layout['house_title'];
				$price = $layout['house_price'];
				$housePriceRMB = $layout['house_price_rmb'];
				$houseArea = $layout['house_area'];

				$bedrooms = $this->extractNum('/(\d)室/', $houseLayout);

				$index = $idx + 1;
				$originId = sprintf('%s-%d', $id, $index);

				$data = [
					'f_origin_id'         => $originId,
					'f_origin_parent_id'  => $id,
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
					'f_bathroom_num'      => 0,
					'f_parking_space_num' => $parkingSpace,

					'f_house_layout'   => $houseLayout,
					'f_building_time'  => '',
					'f_handing_time'   => '',
					'f_land_area'      => '',
					'f_balcony_area'   => '',
					'f_house_no'       => '',
					'f_house_floor'    => '', //楼层
					'f_house_standard' => $houseStandard, //装修标准
					'f_property_info'  => $propertyInfo,

					'f_spu_price'  => $priceStr,
					'f_spu_layout' => $houseLayoutStr,
					'f_spu_area'   => $houseAreaStr,
					'f_tag'        => $houseTag
				];
				$this->writeHouseEvalUS($originId, $data);
			}
		}
	}

	/**
	 * 计算
	 * @param array $houseLayout
	 * @return array
	 */
	private function computeHouseLayout(array $houseLayout) {
		$houseLayoutList = array_column($houseLayout, 'house_title');
		$priceList = array_column($houseLayout, 'house_price');
		$priceRMBList = array_column($houseLayout, 'house_price_rmb');
		$houseAreaList = array_column($houseLayout, 'house_area');
		if (count($houseLayoutList) > 1) {
			$houseLayout = \join(',', $houseLayoutList);
			$price = min($priceList) . '-' . max($priceList);
			$housePriceRMB = min($priceRMBList) . '-' . max($priceRMBList);
			$houseArea = min($houseAreaList) . '-' . max($houseAreaList);
		} else {
			$houseLayout = $houseLayoutList[0];
			$price = $priceList[0];
			$housePriceRMB = $priceRMBList[0];
			$houseArea = $houseAreaList[0];
		}
		return [$houseLayout, $price, $housePriceRMB, $houseArea];
	}

}