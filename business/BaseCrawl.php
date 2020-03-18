<?php

namespace Business;

use Gregwar\Cache\Cache;
use Lib\BaseLogger;
use Lib\Http\UserAgent;
use Lib\Net\BaseService;
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
	 * @var bool \
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


	protected $baseUrl = '';

	protected $isConsole=false;

	/**
	 * 构造函数
	 * BaseCrawl constructor.
	 * @param OutputInterface $output
	 * @param bool $useCache 是否使用缓存
	 * @param bool $debugModel 调试模式
	 * @throws \Exception
	 */
	public function __construct(OutputInterface $output = null, $useCache = true, $debugModel = true) {
		$this->logger = BaseLogger::instance(BaseLogger::CHANNEL_SPIDER_CACHE);
		$this->output = $output;
		$this->useCache = $useCache;
		$this->isConsole=$this->isConsole();
		$this->computePlatform();
		$this->cacheDir = BASE_DIR . '/storage/cache/' . $this->platform . '/';
		$userAgent = UserAgent::random();
		$guzzleConfig = [
			'timeout' => 20,
			'headers' => [
				'User-Agent' => $userAgent
			]
		];
		$config = $debugModel ? [
			'guzzleHttp' => $guzzleConfig
		] : [
			'guzzleHttp' => $guzzleConfig + [
					'proxy' => [
						'http' => 'tcp://163.204.246.18:9999', // Use this proxy with "http"
						//'https' => 'tcp://localhost:9124', // Use this proxy with "https",
					]
				]
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

	abstract public function crawl();


	/**
	 * 输出信息
	 * @param $message
	 * @param OutputInterface $output
	 * @param $fg
	 * @param $bg
	 */
	public function writeColorLn($message, $fg) {
		$this->output->writeln("<fg=$fg>$message</>");
	}

	/**
	 * 检测并创建文件
	 * @param $filePath
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
		$str = preg_replace_callback('/([A-Z]{1})/', function ($matches) {
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
		$fileName = $this->humpToLine($fileName);
		$fileName = strtolower($fileName);
		return $fileName;
	}

	/**
	 * 获取内容
	 * @param $fileName
	 * @param $url
	 * @return bool|string|null
	 * @throws \Exception
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
			throw new \RuntimeException($fileName . ':content empty');
		}
		return $content;
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
			} catch (\Exception $e) {
			}
		}else{
			$this->writeFile($fileName,'');
		}
		$content = trim($content);
		return $content;
	}

	/**
	 * 提取数据
	 * @param $content
	 * @param $express
	 * @param string $attr
	 * @param null $func
	 * @return array|mixed
	 */
	protected function computeData($content, $express, $attr = self::TEXT_SYMBOL, $func = null) {
		$crawler = new Crawler($content);
		$node = $crawler->filter($express);
		//		if($func!==null){
		//			$node=is_numeric($func) ? $node->eq($func):$node->$func();
		//		}
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
	 * 提取只有一条数据的数据
	 * @param $content
	 * @param $express
	 * @param string $attr
	 * @param null $func
	 * @return mixed
	 */
	protected function computeOnlyOneData($content, $express, $attr = self::TEXT_SYMBOL, $func = null) {
		$computedData = $this->computeData($content, $express, $attr);
		return $computedData[0];
	}

	/**
	 * 提取HTML内容
	 * @param $content
	 * @param $express
	 * @param null $func
	 * @param string $replacePattern
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
			$content = preg_replace($replacePattern, '', $content);
		}
		return $content;
	}

	/**
	 * 是否命令行
	 * @return bool
	 */
	protected function isConsole() {
		return php_sapi_name() == 'cli' ? true : false;
	}

}