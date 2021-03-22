<?php

namespace Business\House;


/**
 * 日本房源
 * Class HiMaWaRi
 * @package Business\News\Fang
 */
class HiMaWaRi extends HouseBase {

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
		$content = $this->fetchContent('', $shortUrl, 'GBK', 'UTF-8//IGNORE');
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
		$content = $this->fetchContent('', $shortUrl);
		$idList = $this->computeData($content, '.item-title a', "href");
		$this->computeListId($idList);
		return $idList;
	}

	/**
	 * 爬取详情页
	 * @param $id
	 * @return array|mixed
	 * @throws \ErrorException
	 */
	public function crawlDetail($id) {
		$fileName = __FUNCTION__ . '_id_' . $id;
		$url = $this->computeDetailPageUrl($id);
		$htmlContent = $this->fetchContent($fileName, $url);
		//标题
		$room_name = $this->computeOnlyOneData($htmlContent, '.house-title h1');
		//推广图片
		$promotion_img = '';
		//房源类型(新房、二手房)
		$houseType = '';
		//地理位置
		$address = '';
		$room_country = '日本';//国家,
		$room_city = '';//城市,
		$room_zone = '';//区域,
		///房产描述
		$room_desc = $this->computeOnlyOneData($htmlContent, '.house-feature .col-right');//房产描述,
		$houseTableKeyList = $this->computeHtmlContentList($htmlContent, '.house-table li label');
		$houseTableValueList = $this->computeHtmlContentList($htmlContent, '.house-table li span');

		//价格
		$price = $this->extractValue($houseTableKeyList, $houseTableValueList, '价格');
		$this->multiReplace($price, [',', '万日元']);
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

		//交通
		$trans=$this->subwayMap($id);

		dump($trans,1);
		dump($perPrice,1);
		//		$room_rate;//近一年房价涨幅,
		//		$rental_rate;//年均租金,
		//		$downpayment_rate;//首付款比率,
		//		$loan_payments_rate;//贷款首付比率,
		$currency_gb = '日元';//币种,
		$room_price_total = $price;//外币最低总价,
		$apartment_type = $apartmentType;//物业类型,
		//		$invest_purpose;//投资目的,
		//		$property_right;//产权年限,
		//		$price_per_sqm;//项目均价,
		//		$room_area_min;//最小使用面积,
		//		$room_area_max;//最大使用面积,
		//		$room_standard;//交房标准,
		//		$handing_in_date;//交房时间,
		//		$house_layout_infos;//户型,
		//		$location_round;//位置周边,
		$abstract = '';
		//详情页替换
		$content = $this->computeHtmlContent($htmlContent, 'div.news_content', 'first');
		//入库
		$seqId = $this->doDetail(NewsBase::CAT1_TRENDS, NewsBase::CAT2_OVERSEAS_LIFE, $id, $title, $abstract, $content);
		//图片入库
		$imgList = $this->extractImage($content);
		$this->doImage($seqId, $imgList);
		$detailData = [
			'id'       => $id,
			'title'    => $title,
			'abstract' => $abstract,
			'content'  => $content
		];
		return $detailData;
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
	 * 抓取购物数据
	 * @param $id
	 * @return mixed
	 */
	private function shoppingMap($id) {
		return $this->crawlMap('shopping', $id);
	}

	/**
	 * 抓取教育数据
	 * @param $id
	 * @return mixed
	 */
	private function eduMap($id) {
		return $this->crawlMap('edu', $id);
	}

	/**
	 * 抓取交通数据
	 * @param $id
	 * @return mixed
	 */
	private function subwayMap($id) {
		return $this->crawlMap('subway', $id);
	}

	/**
	 *抓取生活数据
	 * @param $id
	 * @return mixed
	 */
	private function lifeMap($id) {
		return $this->crawlMap('life', $id);
	}

	/**
	 * 抓取接口数据
	 * @param $type
	 * @param $id
	 * @return mixed
	 */
	private function crawlMap($type, $id) {
		$url = $this->baseUrl . "ajax/{$type}Map.html?id={$id}";
		$content = file_get_contents($url);
		$contentArr=\json_decode($content, true);
		return $contentArr['code']==0 && $contentArr['data'] ? $contentArr['data']:[];
	}
}