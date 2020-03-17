<?php

namespace Business;

use Lib\Net\BaseService;
use Lib\Util\CommonUtil;
use function foo\func;


/**
 * 爬虫爬数据
 * Class GearBestUtil
 * @package Lib\Util
 */
class WaiGF extends BaseCrawl {

	protected $baseUrl= "http://www.waigf.com/";

	/**
	 * 抓取国家城市
	 * @return mixed
	 * @throws \Exception
	 */
	public function crawlCountryCity(){
		$fileName = __FUNCTION__;
		$url = $this->baseUrl.'common/diqujson.ashx';
		$content = $this->fetchContent($fileName, $url);
		$contentArr=\json_decode($content,true);
		return $contentArr;
	}

	/**
	 * 计算页数
	 * @param $shortUrl
	 * @return int|mixed
	 * @throws \Exception
	 */
	public function crawlPageCnt($shortUrl){
		$shortUrl=trim($shortUrl,'/');
		$fileName = __FUNCTION__ . '_url_' . $shortUrl;
		$url=$this->baseUrl.$shortUrl;
		$content=$this->fetchContent($fileName,$url);
		$pageCnt=$this->computeOnlyOneData($content,'#pagefy .info');
		$pageCnt=str_replace('页次： /','',$pageCnt);
		$pageCnt=str_replace('GO','',$pageCnt);
		$pageCnt=intval($pageCnt);
		return $pageCnt;
	}

	/**
	 * 爬取所有ID
	 * @return array|mixed
	 * @throws \Exception
	 */
	public function crawAllId(){
		$fileName=__FUNCTION__;
		$shortUrl='/newhouselist_t1016_a0_m0_j0_o1_p1.html';
		$shortUrl=trim($shortUrl,'/');
		$url=$this->baseUrl.$shortUrl;
		$content=$this->fetchContent($fileName,$url);
		$idList=$this->computeData($content,'.house_txt a.name',"href");
		if($idList){
			array_walk($idList,function (&$value){
				$pattern='/(\d)+/';
				preg_match($pattern,$value,$matches);
				$id=$matches[0];
				$value=$id;
			});
		}
		return $idList;
	}
	public function crawlDetail($id){
		$fileName = __FUNCTION__ . '_id_' . $id;
		$url=$this->baseUrl.'newhouse/'.$id.'.html';
		$content=$this->fetchContent($fileName,$url);
		$title=$this->computeOnlyOneData($content,'.proshow_info .name');
		$address=$this->computeHtmlContent($content,'.proshow_info .address',null,'/(.)*<\/i>/');
		$tag=$this->computeHtmlContent($content,'.proshow_info span[id=tag]');
		$tag=str_replace('|',',',$tag);
		$minRmbPrice=$this->computeOnlyOneData($content,'.house_info .price b');
		$otherInfo=$this->computeData($content,'.other_info span');
		$otherInfo=\join($otherInfo,',');
		//项目相关
		$express='.prod_content';
		$pattern='/(.*)img(.*)/';
		$projectAdvantage=$this->computeHtmlContent($content,$express,'first',$pattern);
		$projectAround=$this->computeHtmlContent($content,$express,1,$pattern);
		$projectRealImg=$this->computeHtmlContent($content,$express,2,$pattern);
		$projectLayoutImg=$this->computeData($content,$express,'data-url',3);
		dump($projectLayoutImg,1);
	}

	public function crawl() {
		// TODO: Implement crawl() method.
	}
}