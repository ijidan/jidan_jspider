<?php

namespace Business\House;


/**
 * 日本房源
 * Class HiMaWaRi
 * @package Business\News\Fang
 */
class HiMaWaRi extends HouseBase {

	/**
	 * 映射
	 * @var array
	 */
	protected $data = [];

	public $baseUrl = 'https://www.himawari-japan.com/';

	/**
	 * 子目录
	 * @var string
	 */
	protected $platformsSubDir = 'house';


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
		$url = $this->baseUrl . "house?page={$page}";
		return $url;
	}

	/**
	 * 计算详情页URL
	 * @param $id
	 * @return mixed
	 */
	public function computeDetailPageUrl($id) {
		$url = $this->baseUrl . "fang/{$id}";
		return $url;
	}

	/**
	 * 爬取总数
	 * @param $shortUrl
	 * @return mixed
	 * @throws \Exception
	 */
	public function crawlPageCnt($shortUrl) {
		$shortUrl = trim($shortUrl, '/');
		$fileName = __FUNCTION__ . '_url_' . $shortUrl;
		$content = $this->fetchContent($fileName, $shortUrl, 'GBK', 'UTF-8//IGNORE');
		$idStr = $this->computeData($content, '.pagination li a', 'href');
		$maxId = $this->computeMaxId($idStr);
		return $maxId;
	}

	/**
	 * 获取最大ID
	 * @param array $strIdList
	 * @return int|mixed
	 */
	public function computeMaxId(array &$strIdList) {
		if ($strIdList) {
			$idList = [];
			array_walk($strIdList, function (&$value) use (&$idList) {
				$id = str_replace('/house?page=', '', $value);
				array_push($idList, $id);
			});
			return max($idList);
		}
		return 0;
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
		$content = $this->fetchContent($fileName, $shortUrl);
		//解析数据
		$idList = $this->computeData($content, '.item-title a', "href");
		$this->computeListId($idList);
		$map = $this->parseListData($content);
		$this->data = $map;
		return $idList;
	}

	/**
	 * 解析列表数据
	 * @param $content
	 * @return array
	 */
	private function parseListData($content) {
		$houseList = $this->extractContentHtml($content, '.house-lists .item');
		$map = [];
		foreach ($houseList as $house) {
			$img = $this->computeOnlyOneData($house, '.item-photo img', 'src');
			$url = $this->computeOnlyOneData($house, '.item-title a', 'href');
			$id = $this->extractId($url);
			$itemTable = $this->computeData($house, '.item-table td');
			$map[$id] = array_push($itemTable, $img);
		}
		return $map;
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
		$htmlContent = $this->fetchContent($fileName, $url);
		//标题
		$roomName = $this->computeOnlyOneData($htmlContent, '.house-title h1');

		//户型图
		$sliderList = $this->extractContentHtml($htmlContent, '.gallery-top .swiper-slide');
		$layoutImg = '';
		$bannerInfos = [];
		foreach ($sliderList as $slider) {
			$_img = $this->computeOnlyOneData($slider, 'img', 'src');
			$_desc = $this->computeOnlyOneData($slider, 'em');
			$_item = ['img' => $_img, 'desc' => $_desc];
			array_push($bannerInfos, $_item);
			//户型图
			if (strpos($slider, '户型') !== false) {
				$layoutImg = $this->extractImage($slider);
				break;
			}
		}

		//地址
		$houseAround = $this->computeOnlyOneData($htmlContent, '.house-around span');
		$this->multiReplace($houseAround, ['地址：']);
		$cityMap = [
			'东京都'  => '20000043',
			'大阪府'  => '20000044',
			'神奈川县' => '20000063',
			'京都府'  => '20000070',
			//		    '北海道'=>'',
			'静岡県'  => '20000066',
			'静冈县'  => '20000066',
			'茨城县'  => '20000069'
		];
		$cityName = '';
		$cityId = '';
		$roomZone = $houseAround;
		foreach ($cityMap as $name => $id) {
			if (strpos($houseAround, $name) !== false) {
				$cityName = $name;
				$cityId = $id;
				$roomZone = str_replace($roomName, '', $houseAround);
				break;
			}
		}

		//房源信息
		$houseInfo = $this->extractOnlyOneContentHtml($htmlContent, '.house-infor');
		$houseInfoData = $this->computeData($houseInfo, 'p');
		list($layoutName, $floor, $layoutArea, $roomStandard, $layoutDirection, $handingInDate) = $houseInfoData;
		$this->multiReplace($layoutArea, ['平米']);

		//列表页数据
		list($apartmentType, $direction, $layoutName, $houseArea, $houseType, $propertyRight, $isDownloadable, $broker, $img) = $this->data;
		$this->multiReplace($houseArea, ['平米']);
		//公寓类型
		$apartmentTypeMap = [
			'公寓'   => 5,
			'一户建'  => 2,
			'整栋公寓' => 5,
			'酒店'   => 3,
			'土地'   => 6
		];
		$apartmentTypeConverted = isset($apartmentTypeMap[$apartmentType]) ? $apartmentTypeMap[$apartmentType] : 8;

		//推广图片
		$promotionImg = $this->computeData($htmlContent, '.gallery-top .swiper-slide img', 'src');
		///房产描述
		$roomDesc = $this->computeOnlyOneData($htmlContent, '.house-feature .col-right');//房产描述,
		$houseTableKeyList = $this->computeHtmlContentList($htmlContent, '.house-table li label');
		$houseTableValueList = $this->computeHtmlContentList($htmlContent, '.house-table li span');

		//价格
		$price = $this->extractValue($houseTableKeyList, $houseTableValueList, '价格');
		$this->multiReplace($price, ['亿',',', '万日元']);
		//价格转化
		$priceRMB = $this->extractValue($houseTableKeyList, $houseTableValueList, '约合');
		$this->multiReplace($priceRMB, [',', '万人民币']);
		//物业类型
		$apartmentType = $this->extractValue($houseTableKeyList, $houseTableValueList, '房产类型');
		//面积
		$roomAreaMin = $this->extractValue($houseTableKeyList, $houseTableValueList, '专有面积');
		$this->multiReplace($roomAreaMin, ['平米']);
		$roomAreaBalcony = $this->extractValue($houseTableKeyList, $houseTableValueList, '阳台面积');
		$this->multiReplace($roomAreaMax, ['平米']);
		$roomAreaMax = $roomAreaMin + $roomAreaBalcony;
		//单价
		$perPrice = $this->computeOnlyOneData($htmlContent, '.danjia span');

		//生活相关
		$life = $this->lifeMap($id);
		$bankContent = $this->computeBank($life);
		$hospitalContent = $this->computeHospital($life);

		//购物
		$shopping = $this->shoppingMap($id);
		$shoppingContent = $this->computeShopping($shopping);
		//学校
		$edu = $this->eduMap($id);
		$eduContent = $this->computeEdu($edu);
		//交通
		$trans = $this->subwayMap($id);
		$transContent = $this->computeTrans($trans);
		//构造数据
		$houseItem = array(
			'file'                     => '',
			'weight'                   => '',
			'room_name'                => $roomName,
			'room_desc'                => $roomDesc,
			'room_rate'                => '',
			'rental_rate'              => '',
			'downpayment_rate'         => '',
			'loan_payments_rate'       => '',
			'annual_net_earnings_rate' => '',
			'apartment_type'           => array($apartmentTypeConverted),
			'invest_purpose'           => array(),
			'property_right'           => $propertyRight,
			'price_per_sqm'            => $perPrice,
			'room_area_min'            => $roomAreaMin,
			'room_area_max'            => $roomAreaMax,
			'room_standard'            => $roomStandard,
			'handing_in_date'          => $handingInDate,
			'payment_deposit'          => '',
			'f_id'                     => '',
			'promotion_img'            => $promotionImg,
			'banner_infos'             => $bannerInfos,
			'is_online'                => 0,
			'status'                   => '0',
			'process_id'               => '',
			'project_leader'           => '',
			'room_country'             => '20000034',
			'city_id'                  => $cityId,
			'room_zone'                => $roomZone,
			'currency_gb'              => 'JPY',
			'base_info_tags'           => array(),
			'house_info_tags'          => array(),
			'house_layout_infos'       => array(
				0 => array(
					'img'      => $layoutImg,
					'layout'   => $layoutName,
					'floorage' => $layoutArea,
					'price'    => $price,
					'tag'      => array(),
				),
			),
			'house_static_map'         => '',
			'location_round'           => array(
				'school'        => $eduContent,
				'restaurant'    => '',
				'shopping_mall' => $shoppingContent,
				'hospital'      => $hospitalContent,
				'bank'          => $bankContent,
				'bus_station'   => $transContent,
				'hotel'         => '',
			),
			'purchase_process'         => array(),
			'full_trading_period'      => array(),
			'full_holding_period'      => array(),
			'loan_rate_max'            => '请选择最高比例',
			'loan_year_limit'          => '请选择最高比例',
			'loan_trading_period'      => array(),
			'loan_holding_period'      => array(),
			'project_intro'            => '',
			'room_price_total'         => $price,
			'join_assess'              => '1',
			'promotion_video'          => '',
			'vr_link'                  => '',
			'video_pro'                => '',
			'project_news'             => '',
		);
		return $houseItem;
	}

	/**
	 * 提取值
	 * @param array $houseTableKeyList
	 * @param array $houseTableValueList
	 * @param $key
	 * @return mixed|string
	 */
	private function extractValue(array $houseTableKeyList, array $houseTableValueList, $key) {
		$vIdx = -1;
		foreach ($houseTableKeyList as $idx => $k) {
			if ($k == $key) {
				$vIdx = $idx;
			}
		}
		if ($vIdx == -1) {
			return '';
		}
		return isset($houseTableValueList[$vIdx]) ? $houseTableValueList[$vIdx] : '';
	}

	/**
	 * 计算医院
	 * @param array $data
	 * @return string
	 */
	private function computeBank(array $data) {
		$map = ['yinhang' => '银行'];
		$content = $this->computeSupportSimple($data, $map);
		return $content;
	}

	/**
	 * 计算医院
	 * @param array $data
	 * @return string
	 */
	private function computeHospital(array $data) {
		$map = ['yiyuan' => '医院'];
		$content = $this->computeSupportSimple($data, $map);
		return $content;
	}

	/**
	 * 计算教育
	 * @param array $data
	 * @return string
	 */
	private function computeEdu(array $data) {
		$map = ['youeryuan' => '幼儿园', 'zhongxiaoxue' => '中小学', 'gaozhong' => '高中', 'daxue' => '大学'];
		$content = $this->computeSupportSimple($data, $map);
		return $content;
	}


	/**
	 * 计算购物
	 * @param array $data
	 * @return string
	 */
	private function computeShopping(array $data) {
		$map = ['bianlidian' => '便利店', 'chaoshi' => '超市', 'baihuodian' => '百货店', 'yaozhuangdian' => '药妆店'];
		$content = $this->computeSupportSimple($data, $map);
		return $content;
	}

	/**
	 * 计算交通
	 * @param array $data
	 * @return string
	 */
	private function computeTrans(array $data) {
		$map = ['ditiezhan' => '地铁站'];
		$content = $this->computeSupportSimple($data, $map);
		return $content;
	}

	/**
	 * 计算配套（简单模式）
	 * @param array $data
	 * @param array $map
	 * @return string
	 */
	private function computeSupportSimple(array $data, array $map) {
		$content = '';
		if (!$data) {
			return $content;
		}
		$nameList = [];
		foreach ($data as $idx => $item) {
			if (isset($map[$idx])) {
				foreach ($item as $_item) {
					$name = $_item['name'];
					array_push($nameList, $name);
				}
			}
		}
		$content = $nameList ? \join('、', $nameList) : '';
		return $content;
	}

	/**
	 * 计算配套
	 * @param array $data
	 * @param array $map
	 * @return string
	 */
	private function computeSupport(array $data, array $map) {
		$content = '';
		if (!$data) {
			return $content;
		}
		foreach ($data as $idx => $item) {
			if (isset($map[$idx])) {
				$name = $map[$idx];
				$ctx = $this->computeContent($item, '、');
				if (count($map) == 1) {
					$contentItem = $ctx . '；';
				} else {
					$contentItem = $name . '：' . $ctx . '；';
				}
				$content .= $contentItem;
			}
		}
		$content = trim($content, '；');
		$content = trim($content, '、');
		return $content;

	}

	/**
	 * 计算内容
	 * @param array $dt
	 * @param string $sep
	 * @return string
	 */
	private function computeContent(array $dt, $sep = ';') {
		$content = '';
		foreach ($dt as $item) {
			//$address=$item['address'];
			$name = $item['name'];
			$distance = $item['distance'];
			$distanceTime = floor($item['distance_time']);
			$contentItem = $name . ' 距离' . $distance . '米' . ' 步行' . $distanceTime . '分' . $sep;
			$content .= $contentItem;
		}
		$content = trim($content, ';');
		return $content;
	}

	/**
	 * 抓取购物数据
	 * @param $id
	 * @param $key
	 * @return mixed
	 */
	private function shoppingMap($id, $key = '') {
		return $this->crawlMap('shopping', $key, $id);
	}

	/**
	 * 抓取教育数据
	 * @param $id
	 * @param $key
	 * @return mixed
	 */
	private function eduMap($id, $key = '') {
		return $this->crawlMap('edu', $key, $id);
	}

	/**
	 * 抓取交通数据
	 * @param $id
	 * @param $key
	 * @return mixed
	 */
	private function subwayMap($id, $key = '') {
		return $this->crawlMap('subway', $key, $id);
	}

	/**
	 *抓取生活数据
	 * @param $id
	 * @param $key
	 * @return mixed
	 */
	private function lifeMap($id, $key = '') {
		return $this->crawlMap('life', $key, $id);
	}

	/**
	 * 抓取接口数据
	 * @param $type
	 * @param $key
	 * @param $id
	 * @return mixed
	 */
	private function crawlMap($type, $key, $id) {
		$url = $this->baseUrl . "ajax/{$type}Map.html?id={$id}";
		$content = file_get_contents($url);
		$contentArr = \json_decode($content, true);
		$data = $contentArr['code'] == 0 && $contentArr['data'] ? $contentArr['data'] : [];
		return $data && $key && isset($data[$key]) ? $data[$key] : $data;
	}
}