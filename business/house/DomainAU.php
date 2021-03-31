<?php

namespace Business\House;


use Lib\Util\ArrayUtil;
use Model\Spider\IdParse;

/**
 * 澳洲房源
 * Class DomainAU
 * @package Business\House
 */
class DomainAU extends HouseBase {

	//唯一ID
	protected $uniqueId = 'DomainAU';

	/*
	 * 映射
	 * @var array
	 */
	protected $data = [];

	public $baseUrl = 'https://www.domain.com.au/';

	/**
	 * 子目录
	 * @var string
	 */
	protected $platformsSubDir = 'DomainAU';


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
		$url = $this->baseUrl . "/sale/?excludeunderoffer=1&page={$page}";
		return $url;
	}


	/**
	 * 爬取总数
	 * @param $shortUrl
	 * @return mixed
	 * @throws \Exception
	 */
	public function crawlPageCnt($shortUrl) {
		return 50;
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
		$contentArr = explode('window[', $content);
		$filteredContent = $contentArr[1];
		$this->multiReplace($filteredContent, ["__domain_group/APP_PROPS']", "'", " ", "=", ";"]);
		$data = \json_decode($filteredContent, true);
		$allIdList = $data['listingSearchResultIds'];
		$listingsMap = $data['listingsMap'];
		//解析详情
		foreach ($listingsMap as $id=>$detail) {
			$this->doParse($id, $detail);
		}
		return $allIdList;
	}

	/**
	 * 获取页数
	 * @param $shortUrl
	 * @return mixed|string
	 */
	public function extractId($shortUrl) {
		$page = $this->getQueryValue($shortUrl, 'page');
		return $page;
	}

	/**
	 * 爬取详情页
	 * @param $id
	 * @return array|mixed
	 * @throws \Exception
	 */
	public function crawlDetail($id) {
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
	 * @throws \ErrorException
	 */
	public function genExcel() {
		$dataList = IdParse::find('f_unique_id=?', [$this->uniqueId]);
		if (!$dataList) {
			$this->warning('无数据');
			return false;
		}
		$dataList=ArrayUtil::arrayGroup($dataList,'f_origin_id',true);
		$allChildId = $this->computeAllChildIds($dataList);

		//重组数据
		$map = [];
		foreach ($dataList as $data) {
			//			$id = $data['f_origin_id'];
			$content = $data['f_parse_content'];
			$detail = \json_decode($content, true);

			$id = $detail['id'];
			$listingType = $detail['listingType'];
			$listingModel = $detail['listingModel'];
			unset($listingModel['skeletonImages'], $listingModel['branding']);

			//区分类型
			if ($listingType == 'project') {
				$projectName = $listingModel['projectName'];
				$address = $listingModel['address'];
				$postCode = $address['postcode'];

				$displayAddress = $listingModel['displayAddress'];

				//户型处理
				$childListingIds=$listingModel['childListingIds'];
				$layoutListInfos = [];
				foreach ($childListingIds as $childId){
					if(isset($dataList[$childId])){
						$itemContent=$dataList[$childId]['f_parse_content'];
						$itemDetail=\json_decode($itemContent,true);
						$itemListingModel=$itemDetail['listingModel'];
						unset($itemListingModel['skeletonImages'], $itemListingModel['branding']);

						$features = $itemListingModel['features'];
						$features['price'] = $itemListingModel['price'];
						array_push($layoutListInfos, $features);
					}
				}
			} else {
				if (in_array($id, $allChildId)) {
					continue;
				}
				$projectName = $listingModel['price'];
				$address = $listingModel['address'];
				$postCode = $address['postcode'];
				$displayAddress = $address['street'] . ' ' . $address['suburb'] . ' ' . $address['state'] . ' ' . $address['postcode'];
				//户型处理
				$features = $listingModel['features'];
				$features['price'] = $listingModel['price'];

				pr($features,1);
				$layoutListInfos = [$features];
			}
			$mapItem = [
				'id'              => $id,
				'project_name'    => $projectName,
				//				'direction'       => '',
				'post_code'       => $postCode,
				'display_address' => $displayAddress,
				'currency_gb'     => '$',

				//				'property_right'  => '',
				//				'price_per_sqm'   => '',
				//				'room_standard'   => '',
				//				'handing_in_date' => '',
				'layout'          => $layoutListInfos ? \json_encode($layoutListInfos) : ''
			];
			array_push($map, $mapItem);
		}

		$headers = [
			'Id',
			'项目名称',
			//			'朝向',
			'邮政编号',
			'地理位置',
			'币种',
			//'房屋单价',
			//'物业类型',
			//			'产权年限',
			//			'项目均价',
			//			'交房标准',
			//			'交房时间（建造年份）',
			'户型（房间数、洗浴室数量、停车位数量、房源类型、面积、价格）'
		];
		try {
			$file = $this->writeExcel($headers, $map);
			$this->success('excel 创建成功：' . $file);
		} catch (\PHPExcel_Exception $e) {
			$this->error('excel 创建失败!');
		}
	}


	/**
	 * 计算详情页URL
	 * @param $id
	 * @return mixed
	 */
	public function computeDetailPageUrl($id) {
		// TODO: Implement computeDetailPageUrl() method.
	}
}