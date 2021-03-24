<?php

namespace Business;

use ErrorException;
use Exception;
use Gregwar\Cache\Cache;
use Lib\BaseLogger;
use Lib\BaseModel;
use Lib\Http\UserAgent;
use Lib\Net\BaseService;
use Lib\Util\Config;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Filesystem;


/**
 * 基础类
 * Class BaseCrawl
 * @package Business
 */
abstract class BaseCrawl {

	const COLOR_RED = "red";
	const COLOR_YELLOW = "yellow";
	const COLOR_BLUE = "blue";

	//文本
	const TEXT_SYMBOL = "_text";

	/**
	 * 是否debug模式
	 * @var bool
	 */
	protected $isDebugMode = true;
	/**
	 * 日志接口
	 * @var \Monolog\Logger
	 */
	protected $logger;

	/**
	 * output接口
	 * @var OutputInterface|null
	 */
	protected $output = null;

	/**
	 * 是否使用缓存
	 * @var bool
	 */
	protected $useCache = true;

	/**
	 * 配置文件
	 * @var array
	 */
	protected $config = [];

	/**
	 * 缓存目录
	 * @var string
	 */
	protected $cacheDir = '';

	protected $business = '';
	/**
	 * 平台
	 * @var string
	 */
	protected $platform = '';

	protected $platformsSubDir = '';

	/**
	 * @var Cache
	 */
	protected $cache;
	/**
	 * BaseURl
	 * @var string
	 */
	protected $baseUrl = '';
	/**
	 * 是否命令行
	 * @var bool
	 */
	protected $isConsole = false;

	/**
	 * 是否输出日志
	 * @var bool
	 */
	protected $isOutputLog = false;

	/**
	 * 图片上传URL
	 * @var mixed|string
	 */
	protected $imgUploadURL = '';


	/**
	 * 存储数据
	 * @var array
	 */
	protected $data = [];

	/**
	 * 构造函数
	 * BaseCrawl constructor.
	 * @param OutputInterface|null $output
	 * @param bool $useCache 是否使用缓存
	 * @param bool $isDebugMode 是否调试模式
	 * @throws Exception
	 */
	public function __construct(OutputInterface $output = null, $useCache = true, $isDebugMode = true) {
		$this->imgUploadURL = Config::getConfigItem('struct/img_upload_url');
		$this->logger = BaseLogger::instance(BaseLogger::CHANNEL_SPIDER_CACHE);
		$this->output = $output;
		$this->useCache = $useCache;
		$this->isConsole = $this->isConsole();
		$this->isOutputLog = $this->isConsole && $output;
		$this->platform = $this->getPlatform();
		$this->cacheDir = BASE_DIR . '/storage/spider_cache/' . $this->business . '/' . $this->platform;
		if ($this->platformsSubDir) {
			$this->cacheDir .= '/' . $this->platformsSubDir;
		}
		$this->cacheDir .= '/';
		$this->isDebugMode = $isDebugMode;
		$guzzleConfig = $this->getGuzzleHttpConfig();
		$customConfig = $this->getCustomConfig();
		$config = [
			'guzzleHttp' => $guzzleConfig,
			'custom'     => $customConfig
		];
		$this->config = $config;

	}

	/**
	 * 计算平台
	 */
	private function computePlatform() {
		$parsedUrl = parse_url($this->baseUrl);
		$host = $parsedUrl['host'];
		$hostArr = explode('.', $host);
		$this->platform = $hostArr[1];
	}

	/**
	 * 抓取
	 * @return mixed
	 */
	abstract public function crawl();

	/**
	 * 获取Guzzle配置
	 * @return mixed
	 */
	abstract public function getGuzzleHttpConfig();

	/**
	 * 获取业务配置
	 * @return mixed
	 */
	abstract public function getBusinessConfig();

	/**
	 * 获取自定义配置
	 * @return mixed
	 */
	abstract public function getCustomConfig();

	/**
	 * 获取平台
	 * @return mixed
	 */
	abstract public function getPlatform();


	/**
	 * 输出信息
	 * @param $message
	 * @param string $fg
	 */
	public function writeColorLn($message, $fg = '') {
		if ($this->isOutputLog) {
			$msg = $fg ? "<fg=$fg>$message</>" : "$message";
			$this->output->writeln($msg);
		}
	}

	/**
	 * 信息
	 * @param $message
	 */
	public function info($message) {
		$this->writeColorLn($message);
	}

	/**
	 * 错误信息
	 * @param $message
	 */
	public function error($message) {
		$this->writeColorLn($message, self::COLOR_RED);
	}

	/**
	 * 警告信息
	 * @param $message
	 */
	public function warning($message) {
		$this->writeColorLn($message, self::COLOR_YELLOW);
	}

	/**
	 * 成功信息
	 * @param $message
	 */
	public function success($message) {
		$this->writeColorLn($message, self::COLOR_BLUE);
	}

	/**
	 * 检测并创建文件
	 * @param $fileName
	 */
	public function checkAndCreateFile($fileName) {
		$filePath = $this->computeFilePath($fileName);
		$filesystem = new Filesystem();
		if (!$filesystem->exists($filePath)) {
			if (!$filesystem->exists($this->cacheDir)) {
				$filesystem->mkdir($this->cacheDir);
			}
			$filesystem->touch($filePath);
		}
	}

	/**
	 * 写文件
	 * @param $fileName
	 * @param $content
	 */
	public function writeFile($fileName, $content) {
		$filePath = $this->computeFilePath($fileName);
		$filesystem = new Filesystem();
		if (!$filesystem->exists($filePath)) {
			$this->checkAndCreateFile($fileName);
		}
		$filesystem->appendToFile($filePath, $content);
	}

	/**
	 * 读文件
	 * @param $fileName
	 * @return false|string
	 * @throws FileNotFoundException
	 */
	public function readFile($fileName) {
		$filePath = $this->computeFilePath($fileName);
		$filesystem = new Filesystem();
		if (!$filesystem->exists($filePath)) {
			$this->checkAndCreateFile($fileName);
			throw new FileNotFoundException(" $fileName file not exist");
		}
		$content = file_get_contents($filePath);
		return $content;
	}


	/**
	 * 读取内容
	 * @param $fileName
	 * @return bool|string
	 */
	public function getContent($fileName) {
		try {
			$content = self::readFile($fileName);
		} catch (FileNotFoundException $e) {
			$content = "";
		}
		return $content;
	}

	/**
	 * 缓存文件路径
	 * @param $fileName
	 * @return string
	 */
	private function computeFilePath($fileName) {
		$filePath = $this->cacheDir . $fileName . '.log';
		return $filePath;
	}

	/**
	 * 驼峰转下划线
	 * @param $str
	 * @return string|string[]|null
	 */
	private function humpToLine($str) {
		$str = preg_replace_callback('/([A-Z])/', function ($matches) {
			return '_' . strtolower($matches[0]);
		}, $str);
		return $str;
	}

	/**
	 * 标准化文件名
	 * @param $fileName
	 * @return mixed|string|string[]|null
	 */
	public function standardizeFileName($fileName) {
		$fileName = str_replace('\\', '_', $fileName);
		$fileName = str_replace('https://', '', $fileName);
		$fileName = str_replace('/', '_', $fileName);
		$fileName = str_replace('.', '_', $fileName);
		$fileName = $this->humpToLine($fileName);
		$fileName = strtolower($fileName);
		return $fileName;
	}

	/**
	 * 获取内容，提供编码转换
	 * @param $fileName
	 * @param $url
	 * @param string $sourceCharset
	 * @param string $destCharset
	 * @param string $type
	 * @return bool|false|string|null
	 * @throws Exception
	 */
	protected function fetchContent($fileName, $url, $sourceCharset = 'UTF-8', $destCharset = '',$type='base') {
		if ($fileName) {
			$fileName = $this->standardizeFileName($fileName);
			$content = $this->fetchContentFromCache($fileName);
			if(!$content){
				$content=$this->doFetchContent($url,$type);
				$this->writeFile($fileName, $content);
			}
		} else {
			$content=$this->doFetchContent($url,$type);
		}
		if (!$content) {
			throw new RuntimeException($fileName . ':content empty');
		}
		if ($sourceCharset && $destCharset) {
			$content = iconv($sourceCharset, $destCharset, $content);
		}
		return $content;
	}

	/**
	 * 获取内容
	 * @param $url
	 * @param $type
	 * @return false|string|null
	 * @throws Exception
	 */
	protected function doFetchContent($url,$type){
		switch ($type){
			case 'content':
				$content=file_get_contents($url);
				break;
			case 'base':
			default:
				$rsp = BaseService::sendGetRequest($url, [], $this->config);
				$content = $rsp->success() ? $rsp->getData() : '';
		}
		return $content;

	}

	/**
	 * 图片上传
	 * @param $file
	 * @param array $config
	 * @return string
	 */
	protected function uploadFile2Cache($file, array $config = []) {
		$data = '{"originalName":"","name":"c66cc3e614ffd868325adaca7d560504.png","url":"images\/release\/c\/4\/c66cc3e614ffd868325adaca7d560504.png","real_url":"https:\/\/cache.hinabian.com\/images\/release\/c\/4\/c66cc3e614ffd868325adaca7d560504.png","size":512541,"type":".png","state":"SUCCESS"}';
		//		$rsp=BaseService::sendFile($this->imgUploadURL,$file,$config);
		//		if($rsp->fail()){
		//			return '';
		//		}
		//		$data=$rsp->getData();
		$dataArr = \json_decode($data, true);
		$state = $dataArr['state'];
		if ($state !== 'SUCCESS') {
			return '';
		}
		$url = $dataArr['real_url'];
		return $url;
	}

	/**
	 * 保存房源
	 * @param $roomName
	 * @param array $houseInfo
	 * @param array $config
	 * @return string|null
	 * @throws Exception
	 */
	protected function saveHouse($roomName,array $houseInfo, array $config = []) {
		$host = Config::getConfigItem('struct/house_save_url');
		$rsp = BaseService::saveHouse($host, $roomName,$houseInfo, $config);
		if ($rsp->fail()) {
			return '';
		}
		return $rsp->getData();
	}

	/**
	 * 从缓存中取内容
	 * @param $fileName
	 * @return bool|string
	 */
	protected function fetchContentFromCache($fileName) {
		$content = '';
		if ($this->useCache) {
			try {
				$content = $this->getContent($fileName);
			} catch (Exception $e) {
			}
		} else {
			$this->writeFile($fileName, '');
		}
		$content = trim($content);
		return $content;
	}

	/**
	 * 通过正则表达式提取一条数据
	 * @param $content
	 * @param $pattern
	 * @param int $matchesIdx
	 * @return mixed|string
	 */
	protected function regComputeOnlyOneData($content, $pattern, $matchesIdx = 1) {
		$matches = [];
		$find = preg_match($pattern, $content, $matches);
		if ($find !== false) {
			return $matches[$matchesIdx];
		}
		return '';
	}

	/**
	 * 抓取数据
	 * @param $content
	 * @param $express
	 * @param string $attr
	 * @return array|mixed
	 */
	protected function computeData($content, $express, $attr = self::TEXT_SYMBOL) {
		$crawler = new Crawler($content);
		$node = $crawler->filter($express);
		$isAttrArray = is_array($attr);
		$attrList = $isAttrArray ? $attr : [$attr];
		$map = [];
		foreach ($attrList as $item) {
			$value = $node->extract($item);
			$map[$item] = $value;
		}
		$result = $isAttrArray ? $map : $map[$attr];
		array_walk_recursive($result, function (&$val) {
			$val = trim($val);
		});
		return $result;
	}

	/**
	 * 提取只有一条的数据
	 * @param $content
	 * @param $express
	 * @param string $attr
	 * @return mixed
	 */
	protected function computeOnlyOneData($content, $express, $attr = self::TEXT_SYMBOL) {
		$computedData = $this->computeData($content, $express, $attr);
		return $computedData[0];
	}

	/**
	 * 提取HTML内容
	 * @param $content
	 * @param $express
	 * @param null $func
	 * @param string | array $replacePattern
	 * @return string|string[]|null
	 */
	protected function computeHtmlContent($content, $express, $func = null, $replacePattern = '') {
		$crawler = new Crawler($content);
		$node = $crawler->filter($express);
		if ($func !== null) {
			$node = is_numeric($func) ? $node->eq($func) : $node->$func();
		}
		$content = $node->html();
		$content = trim($content);
		if ($replacePattern) {
			if (is_array($replacePattern)) {
				foreach ($replacePattern as $key => $value) {
					$content = preg_replace($key, $value, $content);
				}
			} else {
				$content = preg_replace($replacePattern, '', $content);
			}
		}
		return $content;
	}

	/**
	 * 计算列表
	 * @param $content
	 * @param $express
	 * @return array
	 */
	protected function computeHtmlContentList($content, $express) {
		$crawler = new Crawler($content);
		$nodeValues = $crawler->filter($express)->each(function (Crawler $node, $i) {
			return trim($node->text());
		});
		return $nodeValues;
	}

	/**
	 * 提取一个HTML
	 * @param $content
	 * @param $express
	 * @return mixed|string
	 */
	protected function extractOnlyOneContentHtml($content, $express) {
		$contentHtml = $this->extractContentHtml($content, $express);
		return $contentHtml ? $contentHtml[0] : '';
	}

	/**
	 * 提取HTML
	 * @param $content
	 * @param $express
	 * @return array
	 */
	protected function extractContentHtml($content, $express) {
		$crawler = new Crawler($content);
		$nodeValues = $crawler->filter($express)->each(function (Crawler $node, $i) {
			return trim($node->html());
		});
		return $nodeValues;
	}

	/**
	 * 根据位置替换内容
	 * @param $content
	 * @param array $replacePatternList
	 * @return mixed
	 */
	protected function computePositionRemovedContent($content, array $replacePatternList) {
		foreach ($replacePatternList as $express => $replaceArr) {
			$re = preg_match_all($express, $content, $matches);
			if ($re === false) {
				continue;
			}
			$matchList = $matches[0];
			foreach ($replaceArr as $idx => $value) {
				$_idx = $idx >= 0 ? $idx : count($matchList) - abs($idx);
				$find = isset($matchList[$_idx]) ? $matchList[$_idx] : "";
				if ($find) {
					$content = str_replace($find, '', $content);
				}
			}
		}
		return $content;
	}

	/**
	 * 提取图片
	 * @param string $content
	 * @param string $express
	 * @param string $attr
	 * @return array|mixed
	 */
	protected function extractOnlyOneImage($content, $express = 'img', $attr = 'src') {
		$imgList = $this->computeData($content, $express, $attr);
		return $imgList ? $imgList[0]:'';
	}
	/**
	 * 提取图片
	 * @param string $content
	 * @param string $express
	 * @param string $attr
	 * @return array|mixed
	 */
	protected function extractImage($content, $express = 'img', $attr = 'src') {
		$imgList = $this->computeData($content, $express, $attr);
		return $imgList;
	}

	/**
	 * 根据关键字移除节点
	 * @param $content
	 * @param $express
	 * @param $removeKeywords
	 * @return string
	 */
	protected function computeNodeRemovedContent($content, $express, $removeKeywords) {
		if (!is_array($removeKeywords)) {
			$removeKeywords = [$removeKeywords];
		}
		$crawler = new Crawler($content);
		$crawler = $crawler->filter($express);
		$cnt = $crawler->count();
		if (!$cnt) {
			return $content;
		}
		$crawler = $crawler->reduce(function (Crawler $node, $i) use ($removeKeywords) {
			$text = $node->text();
			return in_array($text, $removeKeywords) ? false : true;
		});
		$content = $crawler->html();
		return $content;
	}

	/**
	 * 替换图片
	 * @param $img
	 * @return string
	 * @throws Exception
	 */
	protected function replaceContentImag($img) {
		$rsp1 = BaseService::getFile($img);
		if ($rsp1->fail()) {
			return '';
		}
		$data1 = $rsp1->getData();
		$path = $data1['path'];
		$newUrl = $this->uploadFile2Cache($path);
		return $newUrl;
	}

	/**
	 * 是否命令行
	 * @return bool
	 */
	protected function isConsole() {
		return php_sapi_name() == 'cli' ? true : false;
	}

	/**
	 * 随机等待多少微秒
	 * @return int
	 */
	protected function waitRandomMS() {
		$rand = mt_rand(500000, 2500000);
		usleep($rand);
		return $rand;
	}

	/**
	 * 替换
	 * @param $content
	 * @param array $replacementList
	 */
	protected function multiReplace(&$content, array $replacementList) {
		foreach ($replacementList as $r) {
			$content = str_replace($r, '', $content);
		}
	}

	/**
	 *获取分布式ID
	 * @param BaseModel $cls
	 * @return int|mixed
	 * @throws ErrorException
	 */
	public function getNextSeq($cls) {
		$lastRecord = $cls::findOne('f_platform=?', [$this->platform], 'f_id', 'DESC');
		if ($lastRecord) {
			$lastId = $lastRecord['f_id'];
			$step = $lastRecord['f_step'];
			$nextId = $lastId + $step;
		} else {
			$nextId = 1;
		}
		$insData = [
			'f_id'          => $nextId,
			'f_step'        => 1,
			'f_platform'    => $this->platform,
			'f_create_time' => time()
		];
		$cls::insert($insData);
		return $nextId;
	}
}