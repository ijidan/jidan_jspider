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
use Model\Spider\HouseSeq;
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

	/**
	 * 平台
	 * @var string
	 */
	protected $platform = '';

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

	protected $imgUploadURL = '';

	/**
	 * 构造函数
	 * BaseCrawl constructor
	 * @param OutputInterface $output
	 * @param bool $useCache 是否使用缓存
	 * @param bool $debugModel 调试模式
	 * @throws \Exception
	 */
	public function __construct(OutputInterface $output = null, $useCache = true, $debugModel = true) {
		$this->imgUploadURL = Config::getConfigItem('struct/img_upload_url');
		$this->logger = BaseLogger::instance(BaseLogger::CHANNEL_SPIDER_CACHE);
		$this->output = $output;
		$this->useCache = $useCache;
		$this->isConsole = $this->isConsole();
		$this->isOutputLog = $this->isConsole && $output;
		$this->computePlatform();
		$this->cacheDir = BASE_DIR . '/storage/spider_cache/' . $this->platform . '/';
		$userAgent = UserAgent::random();
		$guzzleConfig = [
			'timeout' => 20,
			'headers' => [
				'User-Agent' => $userAgent
			]
		];
		$needUUID = ['need_uuid' => false];
		$config = $debugModel ? [
			'guzzleHttp' => $guzzleConfig,
			'custom'     => $needUUID
		] : [
			'guzzleHttp' => $guzzleConfig + [
					'proxy' => [
						'http' => 'tcp://163.204.246.18:9999', // Use this proxy with "http"
						//'https' => 'tcp://localhost:9124', // Use this proxy with "https",
					]
				],
			'custom'     => $needUUID
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
	 * 获取内容
	 * @param $fileName
	 * @param $url
	 * @return bool|string|null
	 * @throws Exception
	 */
	protected function fetchContent($fileName, $url) {
		$fileName = $this->standardizeFileName($fileName);
		$content = $this->fetchContentFromCache($fileName);
		if (!$content) {
			$rsp = BaseService::sendGetRequest($url, [], $this->config);
			$content = $rsp->success() ? $rsp->getData() : '';
			$this->writeFile($fileName, $content);
		}
		if (!$content) {
			throw new RuntimeException($fileName . ':content empty');
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
	 * 获取分布式ID
	 * @return int
	 * @throws ErrorException
	 */
	public function getNextHouseSeq() {
		$lastRecord = HouseSeq::findOne('f_platform=?', [$this->platform], 'f_id', 'DESC');
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
		HouseSeq::insert($insData);
		return $nextId;
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