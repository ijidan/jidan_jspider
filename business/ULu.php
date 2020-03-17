<?php

namespace Business;

use Lib\Net\BaseService;
use Lib\Util\CommonUtil;
use QL\QueryList;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * 爬虫爬数据
 * Class GearBestUtil
 * @package Lib\Util
 */
class ULu extends BaseCrawl {

	protected $baseUrl="https://www.uoolu.com/";


	public function crawlDetail($id) {
		$fileName = __FUNCTION__ . '_id_' . $id;
		$url = $this->baseUrl. 'house-' . $id . '.html';
		$content = $this->fetchContent($fileName, $url);
		$title = $this->computeOnlyOneData($content, 'div[class=intro-title] span');
		$address = $this->computeOnlyOneData($content, 'span[class=item-desc]');
		$desc = $this->computeOnlyOneData($content, 'div[class=cont-desc]');
		//基本描述
		$baseDesc=$this->computeOnlyOneData($content,'div[class="cont-wrap house-cont-wrap"] .cont-desc');
		//基本属性
		$baseInfoList = $this->computeData($content, '.cont-list .list-item span');
		$developer = $baseInfoList[1];
		$propertyYear = $baseInfoList[3];
		$area = $baseInfoList[5];
		$handleOverTime = $baseInfoList[7];
		$houseNum = $baseInfoList[9];
		$canLoan = $baseInfoList[11];

		//经纬度
		$lng = $this->computeLng($content);
		$lat = $this->computeLat($content);

		//户型
		$layoutList=$this->computeData($content,'div[class="cont-wrap house-cont-wrap  event_room_content"] .cont-list  ');
	}

	/**
	 * 计算经纬度
	 * @param $content
	 * @param string $keyword
	 * @return int|mixed
	 */
	private function computeLngLat($content, $keyword = 'latitude') {
		$pattern = '/' . $keyword . ' = (.*)/';
		$re = preg_match($pattern, $content, $matches);
		$pos = 0;
		if ($re !== false && $matches && isset($matches[1])) {
			$pos = $matches[1];
		}
		return $pos;
	}

	/**
	 * 计算精度
	 * @param $content
	 * @return int|mixed
	 */
	private function computeLng($content) {
		return $this->computeLngLat($content, 'longitude');
	}

	/**
	 * 计算维度
	 * @param $content
	 * @return int|mixed
	 */
	private function computeLat($content) {
		return $this->computeLngLat($content, 'latitude');
	}

	/**
	 * 抓取周边配套信息
	 * @param $id
	 * @return array
	 * @throws \Exception
	 */
	public function crawlLocationRound($id) {
		$fileName = __FUNCTION__ . '_id_' . $id;
		$fileName = $this->standardizeFileName($fileName);
		if ($this->useCache) {
			try {
				$content = $this->getContent($fileName);
			} catch (\Exception $e) {
				$content = '';
			}
		}
		if (!$content) {
			$url = self::BASE_URL . 'house/rim_data/' . $id;
			$rsp = BaseService::sendGetRequest($url, [], $this->config);
			$content = $rsp->success() ? $rsp->getData() : '';
			$this->writeFile($fileName, $content);
		}
		if (!$content) {
			throw new \RuntimeException($fileName . ':content empty');
		}
		$markList = str_replace('var marklist=', '', $content);
		$markList = \json_decode($markList, true);
		//重新索引
		$markList = array_values($markList);
		$markListByIcon = CommonUtil::arrayGroup($markList, 'icon');
		return $markListByIcon;
	}

	public function crawl() {
		// TODO: Implement crawl() method.
	}
}