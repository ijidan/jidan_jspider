<?php

namespace Business;

use Exception;
use Lib\Util\CommonUtil;
use Model\WaiGF\City;
use Model\WaiGF\Country;
use Model\WaiGF\House;

/**
 * 爬虫爬数据
 * Class GearBestUtil
 * @package Lib\Util
 */
class WaiGF extends BaseCrawl {

	protected $baseUrl = "http://www.waigf.com/";

	/**
	 * 抓取国家城市
	 * @return mixed
	 * @throws Exception
	 */
	public function crawlCountryCity() {
		$fileName = __FUNCTION__;
		$url = $this->baseUrl . 'common/diqujson.ashx';
		$content = $this->fetchContent($fileName, $url);
		$contentArr = json_decode($content, true);
		$isSuccess = json_last_error() == JSON_ERROR_NONE && $contentArr ? true : false;
		$msg = $isSuccess ? '解析正确' : '解析错误，错误信息为：' . json_last_error_msg();
		$msg = '国家城市抓取完毕，' . $msg;
		if ($isSuccess) {
			$this->success($msg);
		} else {
			$this->warning($msg);
		}
		if ($isSuccess) {
			$this->doCountryCity($contentArr);
		}
		return $contentArr;
	}

	/**
	 * 国家城市入库
	 * @param array $contentArr
	 * @throws \ErrorException
	 */
	private function doCountryCity(array $contentArr) {
		foreach ($contentArr as $country) {
			$countryName = $country['name'];
			$countryId = $country['id'];
			$countryUrl = $country['url'];
			$cityList = $country['children'];

			$countryModel = new Country();
			$countryRecord = Country::findOne('f_origin_id=?', [$countryId]);
			$countryInsData = [
				'f_name'        => $countryName,
				'f_origin_id'   => $countryId,
				'f_url'         => $countryUrl,
				'f_update_time' => time()
			];
			if ($countryRecord) {
				$countryModel->update($countryInsData, 'f_origin_id=' . $countryId);
			} else {
				$countryModel->insert($countryInsData);
			}
			$this->success('国家 ' . $countryName . ':' . $countryId . '入库完毕');
			//城市
			if ($cityList) {
				$this->doCity($countryId, $cityList);
			}
		}
	}

	/**
	 * 城市入库
	 * @param $countryId
	 * @param array $cityList
	 * @throws \ErrorException
	 */
	private function doCity($countryId, array $cityList) {
		$countryRecord = Country::findOne('f_origin_id=?', [$countryId]);
		$countryFId = $countryRecord['f_id'];
		foreach ($cityList as $city) {

			$cityName = $city['cname'];
			$cityId = $city['cid'];
			$cityUrl = $city['curl'];

			$cityModel = new City();
			$cityRecord = City::findOne('f_origin_id=?', [$cityId]);
			$cityInsData = [
				'f_name'        => $cityName,
				'f_origin_id'   => $cityId,
				'f_url'         => $cityUrl,
				'f_country_id'  => $countryFId,
				'f_update_time' => time()
			];
			if ($cityRecord) {
				$cityModel->update($cityInsData, 'f_origin_id=' . $cityId);
			} else {
				$cityModel->insert($cityInsData);
			}
			$this->info('城市 ' . $cityName . ':' . $cityId . '入库完毕');
		}
	}


	/**
	 * 计算页数
	 * @param $shortUrl
	 * @return int|mixed
	 * @throws Exception
	 */
	public function crawlPageCnt($shortUrl) {
		$shortUrl = trim($shortUrl, '/');
		$fileName = __FUNCTION__ . '_url_' . $shortUrl;
		$url = $this->baseUrl . $shortUrl;
		$content = $this->fetchContent($fileName, $url);
		$pageCnt = $this->computeOnlyOneData($content, '#pagefy .info');
		$pageCnt = str_replace('页次： /', '', $pageCnt);
		$pageCnt = str_replace('GO', '', $pageCnt);
		$pageCnt = intval($pageCnt);
		$this->success('页数抓取完毕');
		return $pageCnt;
	}

	/**
	 * 爬取所有ID
	 * @return array|mixed
	 * @throws Exception
	 */
	public function crawAllId() {
		$fileName = __FUNCTION__;
		$shortUrl = '/newhouselist_t1016_a0_m0_j0_o1_p1.html';
		$shortUrl = trim($shortUrl, '/');
		$url = $this->baseUrl . $shortUrl;
		$content = $this->fetchContent($fileName, $url);
		$idList = $this->computeData($content, '.house_txt a.name', "href");
		if ($idList) {
			array_walk($idList, function (&$value) {
				$pattern = '/(\d)+/';
				preg_match($pattern, $value, $matches);
				$id = $matches[0];
				$value = $id;
			});
		}
		$cnt = count($idList);
		$msg = '所有ID抓取完毕，一共 ' . $cnt . ' 个';
		if ($cnt) {
			$this->success($msg);
		} else {
			$this->warning($msg);
		}
		return $idList;
	}

	/**
	 * 抓取详情
	 * @param $id
	 * @return array
	 * @throws Exception
	 */
	public function crawlDetail($id) {
		$fileName = __FUNCTION__ . '_id_' . $id;
		$url = $this->baseUrl . 'newhouse/' . $id . '.html';
		$content = $this->fetchContent($fileName, $url);
		$title = $this->computeOnlyOneData($content, '.proshow_info .name');
		$address = $this->computeHtmlContent($content, '.proshow_info .address', null, '/(.)*<\/i>/');
		$tag = $this->computeHtmlContent($content, '.proshow_info span[id=tag]');
		$tag = str_replace('|', ',', $tag);
		$minRmbPrice = $this->computeOnlyOneData($content, '.house_info .price b');
		$otherInfo = $this->computeData($content, '.other_info span');
		$otherInfo = join($otherInfo, ',');
		//项目相关
		$express = '.prod_content';
		$pattern = '/(.*)img(.*)/';
		$projectAdvantage = $this->computeHtmlContent($content, $express, 'first', $pattern);
		$projectAround = $this->computeHtmlContent($content, $express, 1, $pattern);
		//实景图
		$projectRealImg = $this->extractImageList($content, $express, 2);
		//户型图
		$projectLayoutImg = $this->extractImageList($content, $express, 3);
		//入库
		$countryId = 3;
		$cityId = 1016;
		$this->doDetail($countryId, $cityId, $id, $title, $address, $tag, $minRmbPrice, $otherInfo, $projectAdvantage, $projectAround, $projectRealImg, $projectLayoutImg);
		$detailData = [
			'house_id'           => $id,
			'title'              => $title,
			'address'            => $address,
			'tag'                => $tag,
			'min_rmb_price'      => $minRmbPrice,
			'other_info'         => $otherInfo,
			'project_advantage'  => $projectAdvantage,
			'project_around'     => $projectAround,
			'project_real_img'   => $projectRealImg,
			'project_layout_img' => $projectLayoutImg
		];
		return $detailData;
	}

	/**
	 * 房源入库
	 * @param $originCountryId
	 * @param $originCityId
	 * @param $houseId
	 * @param $title
	 * @param $address
	 * @param $tag
	 * @param $minRmbPrice
	 * @param $otherInfo
	 * @param $projectAdvantage
	 * @param $projectAround
	 * @param $projectRealImg
	 * @param $projectLayoutImg
	 * @throws \ErrorException
	 */
	private function doDetail($originCountryId, $originCityId, $houseId, $title, $address, $tag, $minRmbPrice, $otherInfo, $projectAdvantage, $projectAround, $projectRealImg, $projectLayoutImg) {
		$countryRecord = Country::findOne('f_origin_id=?', [$originCountryId]);
		$cityRecord = City::findOne('f_origin_id=?', [$originCityId]);
		$countryId = $countryRecord['f_id'];
		$cityId = $cityRecord['f_id'];
		$houseRecord = House::findOne('f_origin_id=?', [$houseId]);
		$houseInsData = [
			'f_origin_id'          => $houseId,
			'f_city_id'            => $cityId,
			'f_country_id'         => $countryId,
			'f_title'              => $title,
			'f_address'            => $address,
			'f_tag'                => $tag,
			'f_other_info'         => $otherInfo,
			'f_project_advantage'  => $projectAdvantage,
			'f_project_around'     => $projectAround,
			'f_project_real_img'   => $projectRealImg,
			'f_project_layout_img' => $projectLayoutImg,
			'f_update_time'        => time()
		];
		$houseInsData = CommonUtil::Array2String($houseInsData);
		$houseModel = new House();
		if ($houseRecord) {
			$houseModel->update($houseInsData, 'f_origin_id=' . $houseId);
		} else {
			$houseModel->insert($houseInsData);
		}
		$this->info('房源 ' . $title . ':' . $houseId . '入库完毕');

	}

	/**
	 * 提取图片
	 * @param $content
	 * @param $express
	 * @param $func
	 * @param string $pattern
	 * @return array
	 */
	private function extractImageList($content, $express, $func, $pattern = '/data-url="(.*)"/') {
		$projectImgContent = $this->computeHtmlContent($content, $express, $func);
		$projectImgContent = str_replace('<p>', '', $projectImgContent);
		$projectImgContent = str_replace('</p>', '', $projectImgContent);
		$projectImgContent = str_replace('<img', "</br><img", $projectImgContent);
		$projectImgContentList = explode('</br>', $projectImgContent);
		$filteredProjectImgContentList = array_filter($projectImgContentList, function ($value) {
			return trim($value) ? true : false;
		});
		$imgList = [];
		array_walk($filteredProjectImgContentList, function ($value) use (&$imgList, $pattern) {
			$re = preg_match($pattern, $value, $matches);
			if ($re !== false) {
				array_push($imgList, $matches[1]);
			}
		});
		return $imgList;
	}

	public function crawl() {
		// TODO: Implement crawl() method.
	}
}