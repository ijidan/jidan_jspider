<?php

namespace Business\House;


use Model\Spider\IdParse;
use Model\Spider\ImageMap;

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
		return 50;
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
		$content = $this->fetchContentFromDb($fileName, $shortUrl);
		$contentArr=explode('window[',$content);
		$filteredContent=$contentArr[1];
		$this->multiReplace($filteredContent,["__domain_group/APP_PROPS']","'"," ","=",";"]);
		$data=\json_decode($filteredContent,true);
		$allIdList=$data['listingSearchResultIds'];
		$listingsMap=$data['listingsMap'];
		//解析详情
		foreach ($allIdList as $id){
			$this->parseDetail($id,$listingsMap);
		}
		return $allIdList;
	}

	/**
	 * 获取页数
	 * @param $shortUrl
	 * @return mixed|string
	 */
	public function extractId($shortUrl){
		$page=$this->getQueryValue($shortUrl,'page');
		return $page;
	}


	/**
	 * 解析详情
	 * @param $id
	 * @param $listingsMap
	 */
	private function parseDetail($id,$listingsMap){
		$houseType=$listingsMap[$id]['listingType'];
		$houseInfo=$listingsMap[$id]['listingModel'];
		$childList=$houseInfo['childListingIds'];
		foreach ($childList as $childId){
			$houseInfo['childListingInfos'][$childId]=$listingsMap[$childId];
		}
		//记录图片 TODO
		//保存解析内容
		$this->doParse($id, $houseInfo);
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
	 * 图片上传
	 */
	public function uploadImage(){
		$dataList = ImageMap::find("f_unique_id=? and f_new_img_url=''", [$this->uniqueId]);
		if ($dataList) {
			foreach ($dataList as $data) {
				$id=$data['f_id'];
				$originUrl=$data['f_origin_img_url'];
				$toUpUrl=$this->cleanImage($originUrl);
				try {
					$newUrl = $this->uploadFile2Cache($toUpUrl, $this->config);
					ImageMap::update(['f_new_img_url'=> $newUrl],'f_id='.$id);
				} catch (\Exception $e) {
				}
				$this->info('图片上传结束：'.$id);

			}
		}
	}

	/**
	 * 清理图片
	 * @param $originUrl
	 * @return string
	 */
	private function cleanImage($originUrl){
		$pathInfo=pathinfo($originUrl);
		$extension=$pathInfo['extension'];
		$extensionArr=explode('-',$extension);
		$ext=$extensionArr[0];
		$originUrlArr=explode($ext,$originUrl);
		$toUpUrl=$originUrlArr[0].$ext;
		return $toUpUrl;

	}
	/**
	 * 数据上传
	 * @throws \Exception
	 */
	public function uploadData() {
		$dataList = IdParse::find('f_unique_id=?', [$this->uniqueId]);
		if ($dataList) {
			foreach ($dataList as $data) {
				$originId=$data['f_origin_id'];
				$content=$data['f_parse_content'];
				$houseItem=\json_decode($content,true);
				$newId = $this->getNewId($originId);
				if ($newId) {
					$houseItem['f_id'] = $newId;
				}
				$this->replaceImage($houseItem);
				$rspId = $this->saveHouse('', $houseItem, $this->config);
				if (!$newId && $rspId) {
					$this->doId($originId, $rspId);
				}
				$this->info('数据上传完毕：'.$originId);
			}
		}
	}

	/**
	 * 图片替换
	 * @param array $houseItem
	 */
	private function replaceImage(array &$houseItem){
		//推广图片
		$promotionImg=$houseItem['promotion_img'];
		if($promotionImg){
			$imgUrl=$this->getNewImageUrl($promotionImg);
			$houseItem['promotion_img']=$imgUrl;
		}
		$bannerInfos=$houseItem['banner_infos'];
		if($bannerInfos){
			$this->convertImage($bannerInfos);
			$houseItem['banner_infos']=$bannerInfos;
		}
		$houseLayoutInfos=$houseItem['house_layout_infos'];
		if($houseLayoutInfos){
			$this->convertImage($houseLayoutInfos);
			$houseItem['house_layout_infos']=$houseLayoutInfos;
		}
	}

	/**
	 * 图片转换
	 * @param $dataList
	 */
	private function convertImage(&$dataList){
		foreach ($dataList as &$data){
			$img=$data['img'];
			$newImg=$this->getNewImageUrl($img);
			$data['img']=$newImg;
		}
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
	 * @param string $key
	 * @return mixed
	 * @throws \Exception
	 */
	private function shoppingMap($id, $key = '') {
		return $this->crawlMap('shopping', $key, $id);
	}

	/**
	 * 抓取教育数据
	 * @param $id
	 * @param string $key
	 * @return mixed
	 * @throws \Exception
	 */
	private function eduMap($id, $key = '') {
		return $this->crawlMap('edu', $key, $id);
	}

	/**
	 * 抓取交通数据
	 * @param $id
	 * @param string $key
	 * @return mixed
	 * @throws \Exception
	 */
	private function subwayMap($id, $key = '') {
		return $this->crawlMap('subway', $key, $id);
	}

	/**
	 *抓取生活数据
	 * @param $id
	 * @param string $key
	 * @return mixed
	 * @throws \Exception
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
	 * @throws \Exception
	 */
	private function crawlMap($type, $key, $id) {
		$fileName = __FUNCTION__ . '_type_' . $type . '_id_' . $id;
		$url = $this->baseUrl . "ajax/{$type}Map.html?id={$id}";
		$content = $this->fetchContentFromDb($fileName, $url, '', '', 'content');
		$contentArr = \json_decode($content, true);
		$data = $contentArr['code'] == 0 && $contentArr['data'] ? $contentArr['data'] : [];
		return $data && $key && isset($data[$key]) ? $data[$key] : $data;
	}
}