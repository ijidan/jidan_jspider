<?php

namespace Business;

use League\Flysystem\FileNotFoundException;
use Lib\Util\CommonUtil;
use Symfony\Component\Console\Output\OutputInterface;
use League\Csv\Writer;
use Curl\Curl;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Filesystem;
use Tools\Models\SdGBProduct;


/**
 * 爬虫爬数据
 * Class GearBestUtil
 * @package Lib\Util
 */
class GearBest {

	const GB_URL = "https://www.gearbest.com/";
	const TEXT_SYMBOL = "_text";
	const COLOR_RED = "red";
	const COLOR_YELLOW = "yellow";
	const COLOR_BLUE = "blue";


	/**
	 * 抓取数据
	 * @param $catName
	 * @param $shortUrl
	 * @param OutputInterface $output
	 * @return bool
	 * @throws FileNotFoundException
	 * @throws \ErrorException
	 */
	public static function grab($catName, $shortUrl, OutputInterface $output) {
		$totalPage = self::getTotalPage($shortUrl, $output);
		if ($totalPage == 0) {
			$output->writeln("total page error");
			return false;
		}
		$pageList = self::buildPageList($shortUrl, $totalPage);
		$allProductUrls = [];
		foreach ($pageList as $page) {
			$content = self::getContent($page, $output);
			$map = self::computeData($content, 'a[class="icon-loading gbGoodsItem_thumb  js-selectItemd"]', [
				"data-id",
				"href",
				"data-img"
			]);
			$productUrls = $map["href"];
			$allProductUrls = array_merge($allProductUrls, $productUrls);
		}
		$output->writeln("URL提取完毕");
		foreach ($allProductUrls as $idx => $productUrl) {
			$rand = mt_rand(1, 10);
			sleep($rand);
			$productInfo = self::crawlProductDetail($productUrl, $output);
			$productId = $productInfo["id"];
			$output->writeln("商品[$productId] 数据抓取完毕");
			self::writeDatabase($productInfo, $output);
			self::writeColorLn("商品[$productId] 数据入库完毕", $output, self::COLOR_YELLOW);
			$index = $idx + 1;
			self::writeColorLn("这是第[$index]个商品", $output, self::COLOR_RED);
		}
		$output->writeln("done");
	}

	/**
	 * 处理分类
	 * @param OutputInterface|null $output
	 */
	public static function handleCategory(OutputInterface $output = null) {
		$where = "product_category1=''  or product_category2='' or product_category3='' or product_category4='' ";
		$recordList = SdGBProduct::find($where);
		dump($recordList, 1);
		if ($recordList) {
			foreach ($recordList as $record) {
				$productCategory = $record["product_category"];
				if (!$productCategory) {
					continue;
				}
				$productId = $record["product_id"];
				$condition = "product_id=${productId}";
				$productCategoryList = CommonUtil::convertStringToArray($productCategory);
				$data = [
					"product_category1" => $productCategoryList[0],
					"product_category2" => $productCategoryList[1],
					"product_category3" => $productCategoryList[2],
					"product_category4" => $productCategoryList[3],
				];
				SdGBProduct::update($data, $condition);
			}
		}
		if ($output) {
			$output->writeln("handle category done");
		}
	}

	/**
	 * @param string $category4
	 * @param OutputInterface|null $output
	 * @throws \ErrorException
	 */
	public static function grabImg($category4 = "", OutputInterface $output = null) {
		$where = $category4 ? "product_category4='" . $category4 . "'" : "1=1";
		$recordList = SdGBProduct::find($where);
		if ($recordList) {
			$recordListByProductNo = CommonUtil::arrayGroup($recordList, "product_no", true);
			$idx=1;
			foreach ($recordListByProductNo as $productNo=>$record) {
				$normalSrc = $record["product_data_normal_src"];
				$normalSrcList = CommonUtil::convertStringToArray($normalSrc);
				$src = $normalSrcList[0];
				$srcInfo=pathinfo($src);
				$ext=$srcInfo["extension"];
				$productTitle = $record["product_title"];
				$productModel = $record["product_model"];
				$productBrand = $record["product_brand"];

				$modelName=self::computeModelNo($productModel,$ext, strtoupper(substr($category4,0,3)), $productNo);
				$curl = new Curl();
				$curl->get($src);
				if ($curl->error) {
					continue;
				}
				$response = $curl->response;
				self::saveImage($modelName,$response,$category4);
				if($output){
					$output->writeln("商品【".$productNo."】 图片【 ".$modelName." 】抓取完毕");
					self::writeColorLn("这是第[$idx]个商品", $output, self::COLOR_RED);
				}
				$idx++;
				//$rand = mt_rand(1, 3);
				//sleep($rand);
			}
		}
	}

	/**
	 * 保存图片
	 * @param $fileName
	 * @param $content
	 * @param $cat
	 */
	public static function saveImage($fileName, $content, $cat) {
		$fileName=strtoupper($fileName);
		$fileName=CommonUtil::removeSpace($fileName);
		$imgRoot=dirname(dirname(dirname(__FILE__)))."/docs/$cat";
		$filesystem = new Filesystem();
		if (!$filesystem->exists($imgRoot)) {
			$filesystem->mkdir($imgRoot);
		}
		$fileName = "$imgRoot/$fileName";
		$filesystem = new Filesystem();
		if (!$filesystem->exists($fileName)) {
			$filesystem->touch($fileName);
		}
		$filesystem->appendToFile($fileName, $content);
	}

	/**
	 * 计算图片名称
	 * @param $model
	 * @param $ext
	 * @param $keyword
	 * @param $productNo
	 * @return string
	 */
	public static function computeModelNo($model,$ext, $keyword, $productNo) {
		$model_ = $model ? $model : $productNo;
		$model_= "SDP-$keyword-$model_.$ext";
		$model_="SDP-SPE-LP - 269S.JPG";
		$model_=CommonUtil::removeSpace($model_);
		return $model_;

	}

	/**
	 * 输出信息
	 * @param $message
	 * @param OutputInterface $output
	 * @param $fg
	 * @param $bg
	 */
	public static function writeColorLn($message, OutputInterface $output, $fg, $bg = "") {
		$output->writeln("<fg=$fg>$message</>");
	}

	/**
	 * 写数据库
	 * @param array $productInfo
	 * @param OutputInterface $output
	 */
	public static function writeDatabase(array $productInfo, OutputInterface $output) {
		$productId = $productInfo["id"];
		$map = [
			"product_no"                 => "id",
			"product_model"              => "model",
			"product_attr"               => "attr",
			"product_title"              => "title",
			"product_category"           => "category",
			"product_category1"          => "category1",
			"product_category2"          => "category2",
			"product_category3"          => "category3",
			"product_category4"          => "category4",
			"product_url"                => "url",
			"product_src"                => "src",
			"product_data_normal_src"    => "data-normal-src",
			"product_data_origin_src"    => "data-origin-src",
			"product_data_thumbnail_src" => "data-thumbnail-src",
			"product_brand"              => "brand",
			"product_summary"            => "summary",
			"product_specification"      => "specification",
		];
		$data = [];
		foreach ($map as $k => $v) {
			$productValue = $productInfo[$v];
			$data[$k] = is_array($productValue) ? CommonUtil::convertArrayToString($productValue) : $productValue;
		}
		$where = "product_no=${productId}";
		$record = SdGBProduct::findOne($where);
		$record ? SdGBProduct::update($data, $where) : SdGBProduct::insert($data);
	}

	/**
	 * 获取商品信息
	 * @param $productUrl
	 * @param OutputInterface $output
	 * @return array|mixed
	 * @throws FileNotFoundException
	 * @throws \ErrorException
	 */
	public static function crawlProductDetail($productUrl, OutputInterface $output) {
		$productContent = self::getContent($productUrl, $output);
		$productMap = self::computeData($productContent, 'img[class="goodsIntro_thumbnailImg js-goodsThumbnailItem js-lazyload"]', [
			"title",
			"src",
			"data-normal-src",
			"data-origin-src",
			"data-thumbnail-src"
		]);
		$productCategory = self::computeData($productContent, 'span[itemprop="name"]', self::TEXT_SYMBOL);
		array_unshift($productCategory, "Home");
		$productMap["category"] = $productCategory;
		$productMap["category1"] = "Home";
		$productMap["category2"] = $productCategory[1];
		$productMap["category3"] = $productCategory[2];
		$productMap["category4"] = $productCategory[3];

		$summary = self::computeSummary($productContent);
		$specification = self::computeSpecification($productContent, $model);
		$productMap["summary"] = \json_encode($summary);
		$productMap["specification"] = \json_encode($specification);
		$productMap["model"] = $model;
		$productMap["title"] = $productMap["title"][0];
		$productMap["id"] = self::computeId($productUrl);
		$productMap["url"] = $productUrl;
		$productBrand = self::computeData($productContent, 'label[class="goodsIntro_brand"] a', self::TEXT_SYMBOL);
		$productMap["brand"] = $productBrand;
		$attr = self::computeAttr($productContent);
		$productMap["attr"] = \json_encode($attr);
		return $productMap;
	}

	/**
	 * 获取商品ID
	 * @param $productUrl
	 * @return int
	 */
	public static function computeId($productUrl) {
		$pattern = "/pp_(\d+)/";
		$matchResult = preg_match_all($pattern, $productUrl, $matches);
		$productId = $matchResult !== false ? $matches[1][0] : 0;
		return $productId;
	}


	/**
	 * 提取数据
	 * @param $content
	 * @param $express
	 * @return array|mixed
	 */
	public static function computeData($content, $express, $attr) {
		$crawler = new Crawler($content);
		$node = $crawler->filter($express);
		$isAttrArray = is_array($attr);
		$attrList = $isAttrArray ? $attr : [$attr];
		$map = [];
		foreach ($attrList as $item) {
			$value = $node->extract($item);
			$map[$item] = $value;
		}
		return $isAttrArray ? $map : $map[$attr];
	}

	/**
	 * 计算属性
	 * @param $content
	 * @return array
	 */
	public static function computeAttr($content) {
		$crawler = new Crawler($content);
		$node = $crawler->filter('li[class="goodsIntro_attrItem goodsIntro_attrRow"]');
		$it = $node->getIterator();
		$map = [];
		foreach ($it as $item) {
			$nodeValue = $item->nodeValue;
			$nodeValueList = CommonUtil::convertStringToArray($nodeValue, "\n");
			$nodeValueList = CommonUtil::trimArray($nodeValueList);
			$title = array_shift($nodeValueList);
			$title = trim($title, ":");
			$map[$title] = CommonUtil::trimArray(CommonUtil::trimArray($nodeValueList, "Size Guide"));
		}
		return $map;
	}

	/**
	 * 计算summary
	 * @param $content
	 * @return array
	 */
	public static function computeSummary($content) {
		$crawler = new Crawler($content);
		$node = $crawler->filter('div[class="product_pz_info mainfeatures desc_simp"]');
		$it = $node->getIterator();
		$map = [];
		foreach ($it as $item) {
			$nodeValue = $item->nodeValue;
			$nodeValueList = CommonUtil::convertStringToArray($nodeValue, "•");
			$nodeValueList = CommonUtil::trimArray($nodeValueList);
			$map = $nodeValueList;
		}
		return $map;
	}

	/**
	 * 计算规格
	 * @param $content
	 * @param string $model
	 * @return array
	 */
	public static function computeSpecification($content, &$model_) {
		$crawler = new Crawler($content);
		$node = $crawler->filter('div[class="product_pz_info product_pz_style2"] tr');
		$it = $node->getIterator();
		$map = [];
		$model = "";
		foreach ($it as $item) {
			$nodeValue = $item->nodeValue;
			$nodeValueList = CommonUtil::convertStringToArray($nodeValue, "             ");
			$nodeValueList = array_filter($nodeValueList);
			$nodeValueList = CommonUtil::trimArray($nodeValueList);
			$nodeValueList = array_filter($nodeValueList);
			$title = array_shift($nodeValueList);
			$map[$title] = CommonUtil::trimArray($nodeValueList);
			if (!$model) {
				foreach ($nodeValueList as $itemItemValue) {
					if (strpos($itemItemValue, "Model") !== false) {
						$itemItemValueList = CommonUtil::convertStringToArray($itemItemValue, ":");
						$itemItemValueList = CommonUtil::trimArray($itemItemValueList);
						$model = $itemItemValueList[1];
					}
				}
			}
		}
		$model_ = $model;
		return $map;
	}

	/**
	 * 构造页面
	 * @param $shortUrl
	 * @param $totalPage
	 * @return array
	 */
	public static function buildPageList($shortUrl, $totalPage) {
		$url = self::buildUrl($shortUrl);
		$pageList = [];
		for ($i = 1; $i <= $totalPage; $i++) {
			$page = "${url}/${i}.html";
			array_push($pageList, $page);
		}
		return $pageList;
	}

	/**
	 * 获取最大值
	 * @param $shortUrl
	 * @param OutputInterface $output
	 * @return int|mixed
	 * @throws FileNotFoundException
	 * @throws \ErrorException
	 */
	public static function getTotalPage($shortUrl, OutputInterface $output) {
		$url = self::buildUrl($shortUrl);
		$content = self::getContent($url, $output);
		$maxPage = 0;
		$pattern = "/$shortUrl\/(\d+).html/";
		$matchResult = preg_match_all($pattern, $content, $matches);
		if ($matchResult !== false) {
			$maxPage = max($matches[1]);
		}
		return $maxPage;
	}

	/**
	 * 获取内容
	 * @param $url
	 * @param OutputInterface $output
	 * @return bool|null|string
	 * @throws FileNotFoundException
	 * @throws \ErrorException
	 */
	public static function getContent($url, OutputInterface $output) {
		try {
			$content = self::readFile($url);
		} catch (FileNotFoundException $e) {
			$content = "";
		}
		if (!$content) {
			$content = self::crawlContent($url, $output);
			self::writeFile($url, $content);
		}
		return $content;
	}

	/**
	 * 构造URL
	 * @param $shortUrl
	 * @return string
	 */
	public static function buildUrl($shortUrl) {
		return self::GB_URL . $shortUrl;
	}

	/**
	 * 抓取内容
	 * @param $url
	 * @param OutputInterface $output
	 * @return null|string
	 * @throws \ErrorException
	 */
	public static function crawlContent($url, OutputInterface $output) {
		$curl = new Curl();
		$curl->setUserAgent('Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36');
		//$curl->setCookie("Cookie","AKAM_CLIENTID=93161fca0ae4ce381e8de15b8646c122; gb_lang=en; gb_pipeline=GB; _gcl_au=1.1.1422051910.1553078600; _ga=GA1.2.763365950.1553078600; gb_currencyCode=USD; gb_countryCode=CN; _fbp=fb.1.1553078600499.149104444; od=kdhultwjcgby1553078602095; gb_vid=780dd47d-5121-a415-abb0-f9e96bd2bb8f; gb_guid=302991624; __atssc=google%3B1; _gid=GA1.2.1783132720.1556444358; osr_referrer=originalurl; osr_landing=https%3A%2F%2Fwww.gearbest.com%2F; ORIGINDC=1; gb_vsign=69af46e57dfce69f70d3b7065cbba274e647ed6a; __atuvc=2%7C14%2C2%7C15%2C0%7C16%2C0%7C17%2C1%7C18; WEBF_guid=93161fca0ae4ce381e8de15b8646c122_1556521952; ak_bmsc=2FA4415EEBCD4D3CC56E1A3C8F6A3EF968620344B1720000CDA3C65C6633AF55~plaoOLK24vWq4J7LtBnYjq2iLSXWZGe3JPh4nBAP/qjFql1CsMO8kMACVSEHM9C6Fg3q56vg6g9LEHG5gc36D2nNB2JNjoPPqMUisDvnXA3rf5cWN3Xk4DDS2Z88+gPbeXmAQXr8+X4fBCp9vOgnrTjAZQKGcSmU9uh7lh2CB5P7y08vxYj46uPA6t7ozJ8L7XJkTUR+r7wjycidAUI7bybUNEI0mlO/vZ7/3HKKTk38MddFTPnguZcJy/DuCd6We4; cdn_countryCode=CN; gb2019_gb_sid=732e48e2-968e-aa5c-bc78-35614bc5be74; landingUrl=https://www.gearbest.com/speakers-c_11260; gb_categoryAB=C; gb_searchAB=B; gb_fcm=0; gb_fcmPipeLine=GB; gb_soa_www_session=eyJpdiI6IjNSbnVaZ1A0elRXOTZRcTRRWnlpbHc9PSIsInZhbHVlIjoiczJGWjhGZXRrOWJYR0c0elVnVEp4VXZ3UWxiRWJJRWNuMXVsUEZNcTJmeDA0YWlmeGtGY3JFUzdYeHB0Z0ozbDQrY0pya1FPY0RLTUQyU01EWXNZeUE9PSIsIm1hYyI6ImVjZGMzZGZjOGFiZTJjMjQ5ZDM1MGYyYTFiN2Y4ZTlmZDhmMDUwZWY0MGU4MzY0NzBiODRhYTMwMTI1NTMxOTIifQ%3D%3D; bm_sv=86115D560F2BF769BEBF79B451EC5065~Y0YR0h8USo4nCq8neemfh3LMaTSBbNcYiBdBybDIrCVBr6yXBrlaGjbfl8RaAXNE4wrvescdunzJxPxNVsdEADRcfssJMuvlOdFj1xEc3s8aBGMYIE6PPyJrP4fEv/Ysv5wfsNykNhDOa5an5ZfV4CYdbpkOxyGbuCp6drQonqE=; WEBF_predate=1556522379; gb2019_gb_sid_732e48e2-968e-aa5c-bc78-35614bc5be74=false; gb_pf=%7B%22rp%22%3A%22originalurl%22%2C%22lp%22%3A%22https%3A%2F%2Fwww.gearbest.com%2Fspeakers-c_11260%22%2C%22wt%22%3A1556522380333%7D");
		$curl->setCookieString("AKAM_CLIENTID=93161fca0ae4ce381e8de15b8646c122; gb_lang=en; gb_pipeline=GB; _gcl_au=1.1.1422051910.1553078600; _ga=GA1.2.763365950.1553078600; gb_currencyCode=USD; gb_countryCode=CN; _fbp=fb.1.1553078600499.149104444; od=kdhultwjcgby1553078602095; gb_vid=780dd47d-5121-a415-abb0-f9e96bd2bb8f; gb_guid=302991624; __atssc=google%3B1; _gid=GA1.2.1783132720.1556444358; osr_referrer=originalurl; osr_landing=https%3A%2F%2Fwww.gearbest.com%2F; ORIGINDC=1; gb_vsign=69af46e57dfce69f70d3b7065cbba274e647ed6a; __atuvc=2%7C14%2C2%7C15%2C0%7C16%2C0%7C17%2C1%7C18; WEBF_guid=93161fca0ae4ce381e8de15b8646c122_1556521952; ak_bmsc=2FA4415EEBCD4D3CC56E1A3C8F6A3EF968620344B1720000CDA3C65C6633AF55~plaoOLK24vWq4J7LtBnYjq2iLSXWZGe3JPh4nBAP/qjFql1CsMO8kMACVSEHM9C6Fg3q56vg6g9LEHG5gc36D2nNB2JNjoPPqMUisDvnXA3rf5cWN3Xk4DDS2Z88+gPbeXmAQXr8+X4fBCp9vOgnrTjAZQKGcSmU9uh7lh2CB5P7y08vxYj46uPA6t7ozJ8L7XJkTUR+r7wjycidAUI7bybUNEI0mlO/vZ7/3HKKTk38MddFTPnguZcJy/DuCd6We4; cdn_countryCode=CN; gb2019_gb_sid=732e48e2-968e-aa5c-bc78-35614bc5be74; landingUrl=https://www.gearbest.com/speakers-c_11260; gb_categoryAB=C; gb_searchAB=B; gb_fcm=0; gb_fcmPipeLine=GB; gb_soa_www_session=eyJpdiI6IjNSbnVaZ1A0elRXOTZRcTRRWnlpbHc9PSIsInZhbHVlIjoiczJGWjhGZXRrOWJYR0c0elVnVEp4VXZ3UWxiRWJJRWNuMXVsUEZNcTJmeDA0YWlmeGtGY3JFUzdYeHB0Z0ozbDQrY0pya1FPY0RLTUQyU01EWXNZeUE9PSIsIm1hYyI6ImVjZGMzZGZjOGFiZTJjMjQ5ZDM1MGYyYTFiN2Y4ZTlmZDhmMDUwZWY0MGU4MzY0NzBiODRhYTMwMTI1NTMxOTIifQ%3D%3D; bm_sv=86115D560F2BF769BEBF79B451EC5065~Y0YR0h8USo4nCq8neemfh3LMaTSBbNcYiBdBybDIrCVBr6yXBrlaGjbfl8RaAXNE4wrvescdunzJxPxNVsdEADRcfssJMuvlOdFj1xEc3s8aBGMYIE6PPyJrP4fEv/Ysv5wfsNykNhDOa5an5ZfV4CYdbpkOxyGbuCp6drQonqE=; WEBF_predate=1556522379; gb2019_gb_sid_732e48e2-968e-aa5c-bc78-35614bc5be74=false; gb_pf=%7B%22rp%22%3A%22originalurl%22%2C%22lp%22%3A%22https%3A%2F%2Fwww.gearbest.com%2Fspeakers-c_11260%22%2C%22wt%22%3A1556522380333%7D");
		$curl->get($url);
		if ($curl->error) {
			$output->writeln("curl error");
			return "";
		}
		$response = $curl->response;
		return $response;
	}

	/**
	 * 存储图片
	 * @param $url
	 * @param OutputInterface $output
	 * @throws \ErrorException
	 */
	public static function crawlImage($url, OutputInterface $output) {
		$response = self::crawlContent($url, $output);
		$imageName = basename($url);
		$fp = fopen($imageName, 'w');
		fwrite($fp, $response);
		fclose($fp);
	}

	/**
	 * 检测并创建文件
	 * @param $url
	 */
	public static function checkAndCreateFile($url) {
		$fileName = self::computeFileName($url);
		$filesystem = new Filesystem();
		if (!$filesystem->exists($fileName)) {
			$filesystem->touch($fileName);
		}
	}

	/**
	 * 写文件
	 * @param $url
	 * @param $content
	 * @throws FileNotFoundException
	 */
	public static function writeFile($url, $content) {
		$fileName = self::computeFileName($url);
		$filesystem = new Filesystem();
		if (!$filesystem->exists($fileName)) {
			self::checkAndCreateFile($url);
		}
		$filesystem->appendToFile($fileName, $content);
	}

	/**
	 * 读文件
	 * @param $url
	 * @return bool|string
	 * @throws FileNotFoundException
	 */
	public static function readFile($url) {
		$fileName = self::computeFileName($url);
		$filesystem = new Filesystem();
		if (!$filesystem->exists($fileName)) {
			throw new FileNotFoundException("$url's file not exist");
		}
		$content = file_get_contents($fileName);
		return $content;
	}

	/**
	 * 计算文件名
	 * @param $url
	 * @return string
	 */
	private static function computeFileName($url) {
		$fileName = "cache/" . md5(trim($url, "/"));
		return $fileName;
	}

	/**
	 * 写CSV文件
	 * @param $code
	 * @param array $companyInfoList
	 * @throws FileNotFoundException
	 * @throws \League\Csv\CannotInsertRecord
	 */
	public function genCSVDoc($code, array $companyInfoList) {
		$firstCompany = $companyInfoList[0];
		$header = array_keys($firstCompany);
		$csv = Writer::createFromString('');
		$csv->insertOne($header);
		$csv->insertAll($companyInfoList);
		$content = $csv->getContent(); //returns the CSV document as a string
		$file = self::getTypeCodeFileMap($code)["_csv"];
		pr($content, 1);
		self::writeFile($file, $content);
	}
}