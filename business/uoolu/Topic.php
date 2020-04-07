<?php

namespace Business\Uoolu;

/**
 * 主题关键字
 * Class Topic
 * @package Business\uoolu
 */
class Topic extends BaseUlu {

	public $platformsSubDir = 'topic';

	/**
	 * 抓取
	 * @return mixed|void
	 * @throws \Exception
	 */
	public function crawl() {
		$this->platformsSubDir = "topic";
		$pageCnt = 2751;
		for ($i = 1; $i <= $pageCnt; $i++) {
			//随机等待多少秒
//			$this->waitRandomMS();
			$currentUrl = $this->computeTopicPageUrl($i);
			$currentTopicList = $this->crawTopicDetail($currentUrl,$i);
			foreach ($currentTopicList as $topic){
				$this->writeFile('topic',$topic."\r\n");
			}
			$this->success("第{$i}页 写入完毕");
		}
	}


	/**
	 * 计算主题URL
	 * @param int $page
	 * @return string
	 */
	private function computeTopicPageUrl($page = 1) {
		$url = $this->baseUrl . "topic2/{$page}/";
		return $url;
	}

	/**
	 * 抓取主题关键字
	 * @param $shortUrl
	 * @param $page
	 * @return array|mixed
	 * @throws \Exception
	 */
	private function crawTopicDetail($shortUrl, $page) {
		$fileName = __FUNCTION__ . '_url_' . $shortUrl;
		$content = $this->fetchContent($fileName, $shortUrl);
		$currentTopicList = $this->computeData($content, '.jhlabel a');
		$cnt = count($currentTopicList);
		$msg = "第{$page}页主题抓取结束：一共 {$cnt} 个";
		if ($cnt) {
			$this->success($msg);
		} else {
			$this->warning($msg);
		}
		return $currentTopicList;
	}


}