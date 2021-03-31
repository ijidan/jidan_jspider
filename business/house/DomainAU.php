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
				//户型处理
				$childListingIds=$listingModel['childListingIds'];
				foreach ($childListingIds as $childId){
					if(isset($dataList[$childId])){
						$itemContent=$dataList[$childId]['f_parse_content'];
						$itemDetail=\json_decode($itemContent,true);
						$itemListingModel=$itemDetail['listingModel'];
						unset($itemListingModel['skeletonImages'], $itemListingModel['branding']);

						$addressInfo = $itemListingModel['address'];
						$postCode = $addressInfo['postcode'];
						$province=$addressInfo['state'];
						$city=$addressInfo['suburb'];
						$address=$addressInfo['street'];

						$fullAddress = $address . ' ' . $city . ' ' . $province . ' ' . $postCode;

						$price = $itemListingModel['price'];

						$features = $itemListingModel['features'];
						$propertyType=$features['propertyType'];
						$propertySizes=$features['landSize'];
						$sizeUnit=$features['landUnit'];
						$bedrooms=$features['beds'];
						$bathroom=$features['baths'];
						$parkingSpace=$features['parking'];


						//写数据库
						$data = [
							'f_origin_id'         => $childId,
							'f_origin_parent_id'  => $id,
							'f_title'             => $projectName,
							'f_post_code'         => $postCode,
							'f_province'          => $province,
							'f_city'              => $city,
							'f_address'           => $address,
							'f_full_address'      => $fullAddress,
							'f_house_type'        => $propertyType,
							'f_house_area'        => $propertySizes,
							'f_house_unit'        => $sizeUnit,
							'f_currency_symbol'   => '$',
							'f_price'             => $price,
							'f_bedroom_num'       => $bedrooms,
							'f_bathroom_num'      => $bathroom,
							'f_parking_space_num' => $parkingSpace,
						];
						$this->writeHouseEval($childId,$data);

					}
				}
			} else {
				if (in_array($id, $allChildId)) {
					continue;
				}
				$projectName = $listingModel['price'];
				$addressInfo = $listingModel['address'];
				$postCode = $addressInfo['postcode'];
				$province=$addressInfo['state'];
				$city=$addressInfo['suburb'];
				$address=$addressInfo['street'];

				$fullAddress = $address . ' ' . $city . ' ' . $province . ' ' . $postCode;

				$price = $listingModel['price'];
				//户型处理
				$features = $listingModel['features'];
				$propertyType=$features['propertyType'];
				$propertySizes=$features['landSize'];
				$sizeUnit=$features['landUnit'];
				$bedrooms=$features['beds'];
				$bathroom=$features['baths'];
				$parkingSpace=$features['parking'];

				//写数据库
				$data = [
					'f_origin_id'         => $id,
					'f_origin_parent_id'  => 0,
					'f_title'             => $projectName,
					'f_post_code'         => $postCode,
					'f_province'          => $province,
					'f_city'              => $city,
					'f_address'           => $address,
					'f_full_address'      => $fullAddress,
					'f_house_type'        => $propertyType,
					'f_house_area'        => $propertySizes,
					'f_house_unit'        => $sizeUnit,
					'f_currency_symbol'   => '$',
					'f_price'             => $price,
					'f_bedroom_num'       => $bedrooms,
					'f_bathroom_num'      => $bathroom,
					'f_parking_space_num' => $parkingSpace,
				];

				$this->writeHouseEval($id,$data);
			}

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