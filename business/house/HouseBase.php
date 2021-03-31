<?php

namespace Business\House;

use Business\BaseCrawl;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
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
	 * 获取查询字符串
	 * @param $url
	 * @return mixed
	 */
	public function getQuery($url){
		$urlList=parse_url($url);
		return $urlList['query'];
	}
	/**
	 * 转换查询字符串
	 * @param $query
	 * @return array
	 */
	public function convertUrlQuery($query) {
		$queryParts = explode('&', $query);
		$params = array();
		foreach ($queryParts as $param) {
			$item = explode('=', $param);
			$params[$item[0]] = $item[1];
		}
		return $params;
	}

	/**
	 * 获取参数值
	 * @param $url
	 * @param $key
	 * @return mixed|string
	 */
	public function getQueryValue($url,$key){
		$query=$this->getQuery($url);
		$queryList=$this->convertUrlQuery($query);
		return isset($queryList[$key])?$queryList[$key]:'';
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
		$reBuildCookies=[];
		foreach ($cookies as $idx=>$cookie){
			if(isset($cookie['name'])){
				$reBuildCookies[$idx]=$cookie;
			}else{
				$cookieStr=$cookie[0];
				$cookieStrList=explode(';',$cookieStr);
				foreach ($cookieStrList as $item){
					$setCookie = SetCookie::fromString($item);
					$newCookieItem=['name'=> $setCookie->getName(),'value'=> $setCookie->getValue()];
					$reBuildCookies[$idx]=$newCookieItem;
				}
			}
		}
		$cookieJar = CookieJar::fromArray($reBuildCookies,'');
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
		$pageCnt = $this->crawlPageCnt($firstListPage);
		$this->info("总页数抓取结束：一共 {$pageCnt} 页");
		if ($pageCnt) {
			for ($i = 1; $i <= $pageCnt; $i++) {
				$listPageUrl = $this->computeListPageUrl($i);
				//随机等待多少秒
				$this->waitRandomMS();
				try {
					$allId = $this->crawAllId($listPageUrl);
				} catch (\Exception $e) {
					$this->error($e->getMessage());
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