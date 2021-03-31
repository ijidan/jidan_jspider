<?php

namespace Business\House;


use GuzzleHttp\RequestOptions;
use Lib\Util\ArrayUtil;
use Model\Spider\IdParse;

/**
 * https://www.realestate.com.au/ 房源
 * Class DomainAU
 * @package Business\HousecrawAllId
 */
class RealEstateAU extends HouseBase {

	//唯一ID
	protected $uniqueId = 'RealEstateAU';

	/*
	 * 映射
	 * @var array
	 */
	protected $data = [];

	public $baseUrl = 'https://www.realestate.com.au/';

	/**
	 * 子目录
	 * @var string
	 */
	protected $platformsSubDir = 'RealEstateAU';


	/**
	 * 获取平台
	 * @return mixed
	 */
	public function getPlatform() {
		// TODO: Implement getPlatform() method.
	}

	/**
	 * 获取Guzzle配置
	 * @return mixed
	 */
	public function getGuzzleHttpConfig() {
		$headerStr = <<<EOF
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
Accept-Encoding: gzip, deflate, br
Accept-Language: zh-CN,zh;q=0.9,en;q=0.8,zh-TW;q=0.7,vi;q=0.6
Cache-Control: no-cache
Connection: keep-alive
Cookie: reauid=a4f00f1783420000607a5d6021000000fcd80200; mid=13535977853519781310; s_vi=[CS]v1|302ECE66B660D403-60000CB481DD805E[CE]; s_ecid=MCMID%7C08942914255572467283036022309113061265; _fbp=fb.2.1616747730505.1320986141; VT_LANG=language%3Dzh-CN; bx_bmp1=6eb614be-55a2-3524-6c7d-b8acdb020a05; _gid=GA1.3.1820447405.1616981395; s_nr=1616981472371; QSI_SI_50fmRcIKsSzWlmZ_intercept=true; External=%2FAPPNEXUS%3D6373007538168404366%2FCASALE%3DX%252EGWJc-NDY3y11nx1Uk8tQAA%2526891%2FPUBMATIC%3DBBC5B4B2-B76B-4D31-A663-2D3CF66C073C%2FRUBICON%3DKMOP8SDM-1E-EIGY%2FTRIPLELIFT%3D4711573441076099271%2F_EXP%3D1648523818%2F_exp%3D1648523846; Country=CN; AMCVS_341225BE55BBF7E17F000101%40AdobeOrg=1; AMCV_341225BE55BBF7E17F000101%40AdobeOrg=-330454231%7CMCIDTS%7C18716%7CMCMID%7C08942914255572467283036022309113061265%7CMCAAMLH-1617688117%7C11%7CMCAAMB-1617688117%7CRKhpRz8krg2tLO6pguXWp5olkAcUniQYPHaMWWgdJ3xzPWQmdj0y%7CMCOPTOUT-1617090517s%7CNONE%7CMCAID%7C302ECE66B660D403-60000CB481DD805E%7CvVersion%7C3.1.2; s_cc=true; ab.storage.sessionId.746d0d98-0c96-45e9-82e3-9dfa6ee28794=%7B%22g%22%3A%225c53e9dd-6da2-3db6-7ebc-769e094d5834%22%2C%22e%22%3A1617085119445%2C%22c%22%3A1617083319428%2C%22l%22%3A1617083319445%7D; _sp_ses.2fe7=*; QSI_HistorySession=https%3A%2F%2Fwww.realestate.com.au%2Fbuy%2Flist-15~1617086654834; pageview_counter.srs=3; s_sq=%5B%5BB%5D%5D; utag_main=v_id:01786dac7a880017f4990d1d02ea03072002806a00bd0$_sn:5$_ss:0$_st:1617088862371$vapi_domain:realestate.com.au$dc_visit:5$ses_id:1617085529047%3Bexp-session$_pn:3%3Bexp-session$dc_event:2%3Bexp-session$dc_region:ap-southeast-2%3Bexp-session; _sp_id.2fe7=bbd8b490-c445-4d66-bc42-cf9d4cc77a3d.1616747725.5.1617087064.1617083318.ec165fd9-807f-4ce8-bbc1-24297ec34444; _ga_F962Q8PWJ0=GS1.1.1617086611.12.1.1617087063.0; _ga=GA1.3.1668698787.1616747725; KP_UIDz=9TCirYa%2FgXflgLKcSxk3mQ%3D%3D%3A%3AAMirX8dXlXLSk4Zz4PukV4ztKt5fejXh69gdCvY4Z%2BTDx40pZyCbHwFp9qYgRGPYTtnPTju9MgI0O%2B56CVqzIMd26v4XPQmLYMZrBUQdO0jIyytulhWMsJI%2FZ5tyhhvLDxwIJqQ1u%2BcbpTPXbTaGreeCgwyCfcCYPJ6jaJGc09yG%2FzUimrbwBDbAMMjZrjwGG4E8%2FpnzWX7l7OqWRcVtxMgiBPTobhYQq%2BJEVFZMJQT5p52mCNKjRpVSMM%2FxKAAFWv1yt1%2FoT%2Bw8L1wc34YGnlX6eZYKSIo0lupGFYXDmhsyNIIRKJSc6mCy6gpnF10F96%2B1%2BmQh4vPzj1ey2uvuxedv8qbnYNoQ96wUs29opT1tnRUG%2BttBNUq6IZHeY%2FPqLA7Yq7Shz%2FqGKS89e9Guza6nlP056H2tcYEJVloVnVezQWJV%2BSI2uEMAQ%2Fifhnoc2ly6cNY06q9H2qywn0SvXnHC7Pgrqo6fuOnrlQnJNDQBJKsT9qytJNylpSxcPsx%2BAMf24wFNBLs%2B5PDu1OheH4guhkBVUjTKG8sMQyNn4Cs%3D
Host: www.realestate.com.au
Pragma: no-cache
sec-ch-ua: "Google Chrome";v="89", "Chromium";v="89", ";Not A Brand";v="99"
sec-ch-ua-mobile: ?0
Sec-Fetch-Dest: document
Sec-Fetch-Mode: navigate
Sec-Fetch-Site: none
Sec-Fetch-User: ?1
Upgrade-Insecure-Requests: 1
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90 Safari/537.36
EOF;
		$headersArr = $this->parseHeader($headerStr);
		$guzzleConfig = [
			RequestOptions::HEADERS => $headersArr
		];
		return $guzzleConfig;
	}

	/**
	 * 获取业务配置
	 * @return array|mixed
	 */
	public function getCustomConfig() {
		return ['tracingDataKey' => $this->uniqueId];
	}

	/**
	 * 计算列表页URL
	 * @param int $page
	 * @return mixed
	 */
	public function computeListPageUrl($page = 1) {
		$url = $this->baseUrl . "buy/list-{$page}";
		return $url;
	}


	/**
	 * 爬取总数
	 * @param $shortUrl
	 * @return mixed
	 * @throws \Exception
	 */
	public function crawlPageCnt($shortUrl) {
		return 80;
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
		if (!$content) {
			return [];
		}
		$contentArr = explode('REA.lexaCache', $content);
		$content = $contentArr[1];
		$contentArr = explode("REA.datafile", $content);
		$filteredContent = $contentArr[0];
		$filteredContent = strip_tags($filteredContent);
		$this->multiReplace($filteredContent, ['', '=', '']);

		//数据解析
		$dataList = \json_decode($filteredContent, true);
		$data = '';
		foreach ($dataList as $dataItem) {
			if (isset($dataItem['trackingData'])) {
				$data = $dataItem['trackingData'];
				break;
			}
		}
		$dataInfoList = \json_decode($data, true);
		$listingSearchResults = $dataInfoList['listing_search_results'];
		$searchResults = $listingSearchResults['data']['listings'];
		$ids = $this->computeAllIds($searchResults);
		$map = $this->filterProjectInfo($ids, $dataList, $searchResults);
		//解析详情
		foreach ($map as $mapId => $mapDetail) {
			$this->doParse($mapId, $mapDetail);
		}
		return $ids;
	}

	/**
	 * 过滤项目信息
	 * @param array $idList
	 * @param array $dataList
	 * @param array $searchResults
	 * @return array
	 */
	private function filterProjectInfo(array $idList, array $dataList, array $searchResults) {
		$map = [];
		$searchResultsById = ArrayUtil::arrayGroup($searchResults, 'id', true);
		foreach ($idList as $id) {
			$map[$id] = isset($searchResultsById[$id]) ? $searchResultsById[$id] : [];
			foreach ($dataList as $dataItemKey => $dataItemValue) {
				if (strpos($dataItemKey, $id) !== false) {
					$map[$id][$dataItemKey] = $dataItemValue;
				}
			}
		}
		return $map;
	}

	/**
	 * 计算所有ID
	 * @param array $searchResults
	 * @return array
	 */
	private function computeAllIds(array $searchResults) {
		$ids = [];
		foreach ($searchResults as $item) {
			$id = $item['id'];
			if (!in_array($id, $ids)) {
				array_push($ids, $id);
			}
			if (isset($item['child_listing_ids'])) {
				$childListingIds = $item['child_listing_ids'];
				if ($childListingIds) {
					foreach ($childListingIds as $childId) {
						if (!in_array($childId, $ids)) {
							array_push($ids, $childId);
						}
					}
				}
			}
		}
		return $ids;
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
			if (isset($detail['child_listing_ids'])) {
				$childListingIds = $detail['child_listing_ids'];
				if ($childListingIds) {
					foreach ($childListingIds as $childId) {
						if (!in_array($childId, $allChildIds)) {
							array_push($allChildIds, $childId);
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
		$dataList = IdParse::find('f_unique_id=? ', [$this->uniqueId]);
		if (!$dataList) {
			$this->warning('无数据');
			return false;
		}
		$dataList = ArrayUtil::arrayGroup($dataList, 'f_origin_id', true);
		$allChildId = $this->computeAllChildIds($dataList);

		//重组数据
		foreach ($dataList as $data) {
			$id = $data['f_origin_id'];
			$content = $data['f_parse_content'];
			$detail = \json_decode($content, true);
			$hasId = isset($detail['id']);
			if (!$hasId) {
				continue;
			}
			if (isset($detail['child_listing_ids'])) {
				$this->handleChildData($id, $detail, $dataList);
			} else {
				$this->handleParentData($id, $detail);
			}
		}

	}


	/**
	 * 处理父数据
	 * @param $id
	 * @param array $detail
	 * @throws \ErrorException
	 */
	private function handleParentData($id, array $detail) {
		//物业类型
		$propertyTypeKey = sprintf('$BuyResidentialListing%s.propertyType', $id);
		$propertyType = $detail[$propertyTypeKey]['display'];

		$propertySizesKey = sprintf('$BuyResidentialListing%s.propertySizes.land', $id);
		$propertySizes = $detail[$propertySizesKey]['displayValue'];

		$sizeUnitKey = sprintf('$BuyResidentialListing%s.propertySizes.land.sizeUnit', $id);
		$sizeUnit = $detail[$sizeUnitKey]['displayValue'];

		//价格
		$priceKey = sprintf('$BuyResidentialListing%s.price', $id);
		$price = $detail[$priceKey]['display'];
		$projectName = $price;
		//$this->cleanPrice($price);


		//地址
		$addressKey = sprintf('$BuyResidentialListing%s.address', $id);
		$addressInfo = $detail[$addressKey];
		$postCode = $addressInfo['postcode'];
		$province = $addressInfo['state'];
		$city = $addressInfo['suburb'];

		$addressDisplayKey = sprintf('$BuyResidentialListing%s.address.display', $id);
		$addressDisplayInfo = $detail[$addressDisplayKey];
		$address = $addressDisplayInfo['shortAddress'];
		$fullAddress = $addressDisplayInfo['fullAddress'];

		//特性
		$bedroomsKey = sprintf('$BuyResidentialListing%s.generalFeatures.bedrooms', $id);
		$bathroomKey = sprintf('$BuyResidentialListing%s.generalFeatures.bathrooms', $id);
		$parkingSpacesKey = sprintf('$BuyResidentialListing%s.generalFeatures.parkingSpaces', $id);

		$bedrooms = $detail[$bedroomsKey]['value'];
		$bathroom = $detail[$bathroomKey]['value'];
		$parkingSpace = $detail[$parkingSpacesKey]['value'];

		//户型处理
		$features = [
			'propertyType'  => $propertyType,
			'propertySizes' => $propertySizes,
			'sizeUnit'      => $sizeUnit,
			'bedrooms'      => $bedrooms,
			'bathroom'      => $bathroom,
			'parkingSpace'  => $parkingSpace,
		];

		$layoutListInfos = [$features];

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
		$this->writeHouseEval($id, $data);

	}

	/**
	 * 处理子数据
	 * @param $id
	 * @param array $detail
	 * @param array $dataList
	 * @throws \ErrorException
	 */
	private function handleChildData($id, array $detail, array $dataList) {
		$childIdList = $detail['child_listing_ids'];
		$childIdStr = \join('-', $childIdList);
		$key = sprintf('$ProjectProfile%s__%s', $id, $childIdStr);
		$propertyType = $detail[$key . '.propertyType']['display'];

		//价格
		$price = $detail[$key . '.priceRange']['display'];
		$projectName = $price;
		//地址
		$addressInfo = $detail[$key . '.address'];
		$postCode = $addressInfo['postcode'];
		$province = $addressInfo['state'];
		$city = $addressInfo['suburb'];

		$addressDisplayInfo = $detail[$key . '.address.display'];
		$address = $addressDisplayInfo['shortAddress'];
		$fullAddress = $addressDisplayInfo['fullAddress'];

		//不同户型的数据
		$layoutListInfos = [];
		foreach ($childIdList as $childId) {

			$childContent = $dataList[$childId]['f_parse_content'];
			$childDetail = \json_decode($childContent, true);

			$propertyTypeKey = sprintf('$BuyResidentialListing%s.propertyType', $childId);
			$propertyType = $childDetail[$propertyTypeKey]['display'];

			$propertySizesKey = sprintf('$BuyResidentialListing%s.propertySizes.preferred.size', $childId);
			$propertySizes = $childDetail[$propertySizesKey]['displayValue'];

			$sizeUnitKey = sprintf('$BuyResidentialListing%s.propertySizes.preferred.size.sizeUnit', $childId);
			$sizeUnit = $childDetail[$sizeUnitKey]['displayValue'];

			$childPriceKey = sprintf('$BuyResidentialListing%s.price', $childId);
			$childPrice = $childDetail[$childPriceKey]['display'];
			//$this->cleanPrice($childPrice);

			//特性
			$bedroomsKey = sprintf('$BuyResidentialListing%s.generalFeatures.bedrooms', $childId);
			$bathroomKey = sprintf('$BuyResidentialListing%s.generalFeatures.bathrooms', $childId);
			$parkingSpacesKey = sprintf('$BuyResidentialListing%s.generalFeatures.parkingSpaces', $childId);

			$bedrooms = $childDetail[$bedroomsKey]['value'];
			$bathroom = $childDetail[$bathroomKey]['value'];
			$parkingSpace = $childDetail[$parkingSpacesKey]['value'];

			$features = [
				'propertyType'  => $propertyType,
				'propertySizes' => $propertySizes,
				'sizeUnit'      => $sizeUnit,
				'price'         => $price,
				'bedrooms'      => $bedrooms,
				'bathroom'      => $bathroom,
				'parkingSpace'  => $parkingSpace,
			];
			array_push($layoutListInfos, $features);

			//写数据库
			$data = [
				'f_origin_id'         => $childId,
				'f_origin_parent_id'  => $id,
				'f_title'             => $projectName,
				'f_province'          => $province,
				'f_city'              => $city,
				'f_address'           => $address,
				'f_full_address'      => $fullAddress,
				'f_post_code'         => $postCode,
				'f_house_type'        => $propertyType,
				'f_house_area'        => $propertySizes,
				'f_house_unit'        => $sizeUnit,
				'f_currency_symbol'   => '$',
				'f_price'             => $childPrice,
				'f_bedroom_num'       => $bedrooms,
				'f_bathroom_num'      => $bathroom,
				'f_parking_space_num' => $parkingSpace,
			];
			pr($data, 1);
			$this->writeHouseEval($childId, $data);

		}
	}

	/**
	 * 解析地址
	 * @param $fullAddress
	 * @return array
	 */
	private function parseAddress($fullAddress) {
		list($address, $city, $provinceAndPostCode) = explode(',', $fullAddress);
		$provinceAndPostCode = trim($provinceAndPostCode);
		list($province, $postCode) = explode(' ', $provinceAndPostCode);

		return [trim($postCode), trim($province), trim($city), trim($address)];
	}

	/**
	 * 价格整理
	 * @param $price
	 */
	private function cleanPrice(&$price) {
		$this->multiReplace($price, ['$', ',']);
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