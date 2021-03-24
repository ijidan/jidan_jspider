<?php

namespace Business\House;

use Business\BaseCrawl;
use GuzzleHttp\Cookie\CookieJar;
use Lib\Util\Config;


/**
 *房源基础类
 * Class HouseBase
 * @package Business\News
 */
abstract class HouseBase extends BaseCrawl {


	/**
	 * 业务类型
	 * @var string
	 */
	public $business = 'house';


	/**
	 * 计算列表页URL
	 * @param int $page
	 * @return mixed
	 */
	abstract public function computeListPageUrl($page = 1);

	/**
	 * 计算详情页URL
	 * @param $id
	 * @return mixed
	 */
	abstract public function computeDetailPageUrl($id);

	/**
	 * 爬取总数
	 * @param $url
	 * @return mixed
	 */
	abstract public function crawlPageCnt($url);

	/**
	 * 爬取所有ID
	 * @param $shortUrl
	 * @return mixed
	 */
	abstract public function crawAllId($shortUrl);

	/**
	 * 爬取详情页
	 * @param $id
	 * @return mixed
	 */
	abstract public function crawlDetail($id);


	/**
	 * 提取ID
	 * @param array $strIdList
	 */
	public function computeListId(array &$strIdList) {
		if ($strIdList) {
			array_walk($strIdList, function (&$value) {
				$value = $this->extractId($value);
			});
		}
	}

	/**
	 * 提取ID
	 * @param $value
	 * @return mixed
	 */
	public function extractId($value) {
		$pattern = '/(\d)+/';
		preg_match($pattern, $value, $matches);
		$id = $matches[0];
		return $id;
	}


	/**
	 * 开始爬取
	 * @return mixed|void
	 */
	public function crawl() {
	}

	/**
	 * 获取Guzzle配置
	 * @return mixed
	 */
	public function getGuzzleHttpConfig() {
		$cookies = Config::getConfigItem('cookie/' . $this->business);
		$cookieJar = CookieJar::fromArray([$cookies['name'] => $cookies['value']], $cookies['domain']);
		$guzzleConfig = [
			'cookies' => $cookieJar,
		];
		return $guzzleConfig;
	}


	/**
	 * 获取业务配置
	 * @return mixed
	 */
	public function getBusinessConfig() {
		// TODO: Implement getBusinessConfig() method.
	}

	/**
	 * 获取自定义配置
	 * @return mixed
	 */
	public function getCustomConfig() {
		$customConfig = ['need_uuid' => false];
		return $customConfig;
	}


	/**
	 * 开始爬取
	 * @return mixed|void
	 */
	public function crawlContent() {
		$this->info('总页数抓取开始');
		$firstListPage = $this->computeListPageUrl(1);
		//		$pageCnt = $this->crawlPageCnt($firstListPage);
		$pageCnt = 1;
		$this->info("总页数抓取结束：一共 {$pageCnt} 页");
		if ($pageCnt) {
			for ($i = 1; $i <= $pageCnt; $i++) {
				$listPageUrl = $this->computeListPageUrl($i);
				//随机等待多少秒
				//$this->waitRandomMS();
				try {
					$allId = $this->crawAllId($listPageUrl);
				} catch (\Exception $e) {
					continue;
				}
				$this->info("列表抓取开始：第 {$i} 页");
				foreach ($allId as $id) {
					$this->info("项目详情抓取开始： ID为 $id");
					try {
						$this->crawlDetail($id);
					} catch (\Exception $e) {
						continue;
					}
					$this->info("项目详情抓取结束： ID为 $id");
				}
			}
		}
	}

}