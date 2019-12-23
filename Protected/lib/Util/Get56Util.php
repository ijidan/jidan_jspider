<?php

namespace Lib\Util;


use League\Csv\Writer;
use QL\QueryList;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * 爬取数据
 * Class Get56
 * @package App\Model
 */
class Get56Util {

	//分类名称
	const TYPE_OFFICE_CULTURE_NAME = "办公文教";
	const TYPE_OFFICE_STATIONERY_NAME = "办公文具";
	const TYPE_GLASS_PRODUCT_NAME = "玻璃制品";
	const TYPE_KITCHEN_UTENSILS_NAME = "餐厨用具";
	const TYPE_VEHICLES_AND_ACCESSORIES_NAME = "车辆及配件";
	const TYPE_LIGHTING_NAME = "灯具照明";
	const TYPE_ELECTRONIC_AND_ELECTRICAL_NAME = "电子电气";
	const TYPE_TEXTILE_AND_CLOTHING_NAME = "纺织服装类";
	const TYPE_TEXTILE_AND_LEATHER_GOODS_NAME = "纺织及皮革制品";
	const TYPE_CLOTHING_NAME = "服装服饰";
	const TYPE_HANDCRAFTED_GIFT_NAME = "工艺礼品";
	const TYPE_CRAFT_CERAMIC_NAME = "工艺陶瓷";
	const TYPE_CHEMICAL_INDUSTRY_NAME = "化工";
	const TYPE_EQUIPMENT_NAME = "机械设备";
	const TYPE_HOUSEHOLD_PRODUCTS_NAME = "家居用品";
	const TYPE_FURNITURE_NAME = "家具";
	const TYPE_HOUSEHOLD_APPLIANCES_NAME = "家用电器";
	const TYPE_HEALTH_CARE_NAME = "健康医疗";
	const TYPE_BUILDING_MATERIALS_NAME = "建材类";
	const TYPE_CONSTRUCTION_AND_REAL_ESTATE_NAME = "建筑与房地产";
	const TYPE_MINERAL_METALLURGY_NAME = "矿产冶金";
	const TYPE_CONSUMER_GOODS_NAME = "日用消费品";
	const TYPE_FOOD_AND_DRINK_NAME = "食品饮料";
	const TYPE_SOIL_ANIMAL_NAME = "土畜类";
	const TYPE_TOY_NAME = "玩具";
	const TYPE_HARDWARE_TOOLS_NAME = "五金工具";
	const TYPE_CONSUMER_ELECTRONICS_NAME = "消费电子";
	const TYPE_SHOE_NAME = "鞋";
	const TYPE_LUGGAGE_BAG_NAME = "行李箱包";
	const TYPE_GARDENING_NAME = "园艺";
	const TYPE_SPORTS_AND_LEISURE_NAME = "运动休闲";
	const TYPE_WATCHES_JEWELLERY_AND_GLASSES_NAME = "钟表、珠宝和眼镜";
	const TYPE_DECORATIVE_PAINTINGS_NAME = "装饰画";
	const TYPE_COMPREHENSIVE_NAME = "综合";


	//分类代码

	const TYPE_OFFICE_CULTURE_CODE = "_office_culture";
	const TYPE_OFFICE_STATIONERY_CODE = "_office_stationery";
	const TYPE_GLASS_PRODUCT_CODE = "_glass_product";
	const TYPE_KITCHEN_UTENSILS_CODE = "_kitchen_utensils";
	const TYPE_VEHICLES_AND_ACCESSORIES_CODE = "_vehicles_and_accessories";
	const TYPE_LIGHTING_CODE = "_lighting";
	const TYPE_ELECTRONIC_AND_ELECTRICAL_CODE = "_electronic_and_electrical";
	const TYPE_TEXTILE_AND_CLOTHING_CODE = "_textile_and_clothing";
	const TYPE_TEXTILE_AND_LEATHER_GOODS_CODE = "_textile_and_leather_goods";
	const TYPE_CLOTHING_CODE = "_clothing";
	const TYPE_HANDCRAFTED_GIFT_CODE = "_handcrafted_gift";
	const TYPE_CRAFT_CERAMIC_CODE = "_craft_ceramic";
	const TYPE_CHEMICAL_INDUSTRY_CODE = "_chemical_industry";
	const TYPE_EQUIPMENT_CODE = "_equipment";
	const TYPE_HOUSEHOLD_PRODUCTS_CODE = "_household_products";
	const TYPE_FURNITURE_CODE = "_furniture";
	const TYPE_HOUSEHOLD_APPLIANCES_CODE = "_household_appliances";
	const TYPE_HEALTH_CARE_CODE = "_health_care";
	const TYPE_BUILDING_MATERIALS_CODE = "_building_materials";
	const TYPE_CONSTRUCTION_AND_REAL_ESTATE_CODE = "_construction_and_real_estate";
	const TYPE_MINERAL_METALLURGY_CODE = "_mineral_metallurgy";
	const TYPE_CONSUMER_GOODS_CODE = "_consumer_goods";
	const TYPE_FOOD_AND_DRINK_CODE = "_food_and_drink";
	const TYPE_SOIL_ANIMAL_CODE = "_soil_animal";
	const TYPE_TOY_CODE = "_toy";
	const TYPE_HARDWARE_TOOLS_CODE = "_hardware_tools";
	const TYPE_CONSUMER_ELECTRONICS_CODE = "_consumer_electronics";
	const TYPE_SHOE_CODE = "_shoe";
	const TYPE_LUGGAGE_BAG_CODE = "_luggage_bag";
	const TYPE_GARDENING_CODE = "_gardening";
	const TYPE_SPORTS_AND_LEISURE_CODE = "_sports_and_leisure";
	const TYPE_WATCHES_JEWELLERY_AND_GLASSES_CODE = "_watches_jewellery_and_glasses";
	const TYPE_DECORATIVE_PAINTINGS_CODE = "_decorative_paintings";
	const TYPE_COMPREHENSIVE_CODE = "_comprehensive";


	/**
	 * 映射
	 * @var array
	 */
	public static $TYPE_CODE_NAME_MAP = [
		self::TYPE_OFFICE_CULTURE_CODE                => self::TYPE_OFFICE_CULTURE_NAME,
		self::TYPE_OFFICE_STATIONERY_CODE             => self::TYPE_OFFICE_STATIONERY_NAME,
		self::TYPE_GLASS_PRODUCT_CODE                 => self::TYPE_GLASS_PRODUCT_NAME,
		self::TYPE_KITCHEN_UTENSILS_CODE              => self::TYPE_KITCHEN_UTENSILS_NAME,
		self::TYPE_VEHICLES_AND_ACCESSORIES_CODE      => self::TYPE_VEHICLES_AND_ACCESSORIES_NAME,
		self::TYPE_LIGHTING_CODE                      => self::TYPE_LIGHTING_NAME,
		self::TYPE_ELECTRONIC_AND_ELECTRICAL_CODE     => self::TYPE_ELECTRONIC_AND_ELECTRICAL_NAME,
		self::TYPE_TEXTILE_AND_CLOTHING_CODE          => self::TYPE_TEXTILE_AND_CLOTHING_NAME,
		self::TYPE_TEXTILE_AND_LEATHER_GOODS_CODE     => self::TYPE_TEXTILE_AND_LEATHER_GOODS_NAME,
		self::TYPE_CLOTHING_CODE                      => self::TYPE_CLOTHING_NAME,
		self::TYPE_HANDCRAFTED_GIFT_CODE              => self::TYPE_HANDCRAFTED_GIFT_NAME,
		self::TYPE_CRAFT_CERAMIC_CODE                 => self::TYPE_CRAFT_CERAMIC_NAME,
		self::TYPE_CHEMICAL_INDUSTRY_CODE             => self::TYPE_CHEMICAL_INDUSTRY_NAME,
		self::TYPE_EQUIPMENT_CODE                     => self::TYPE_EQUIPMENT_NAME,
		self::TYPE_HOUSEHOLD_PRODUCTS_CODE            => self::TYPE_HOUSEHOLD_PRODUCTS_NAME,
		self::TYPE_FURNITURE_CODE                     => self::TYPE_FURNITURE_NAME,
		self::TYPE_HOUSEHOLD_APPLIANCES_CODE          => self::TYPE_HOUSEHOLD_APPLIANCES_NAME,
		self::TYPE_HEALTH_CARE_CODE                   => self::TYPE_HEALTH_CARE_NAME,
		self::TYPE_BUILDING_MATERIALS_CODE            => self::TYPE_BUILDING_MATERIALS_NAME,
		self::TYPE_CONSTRUCTION_AND_REAL_ESTATE_CODE  => self::TYPE_CONSTRUCTION_AND_REAL_ESTATE_NAME,
		self::TYPE_MINERAL_METALLURGY_CODE            => self::TYPE_MINERAL_METALLURGY_NAME,
		self::TYPE_CONSUMER_GOODS_CODE                => self::TYPE_CONSUMER_GOODS_NAME,
		self::TYPE_FOOD_AND_DRINK_CODE                => self::TYPE_FOOD_AND_DRINK_NAME,
		self::TYPE_SOIL_ANIMAL_CODE                   => self::TYPE_SOIL_ANIMAL_NAME,
		self::TYPE_TOY_CODE                           => self::TYPE_TOY_NAME,
		self::TYPE_HARDWARE_TOOLS_CODE                => self::TYPE_HARDWARE_TOOLS_NAME,
		self::TYPE_CONSUMER_ELECTRONICS_CODE          => self::TYPE_CONSUMER_ELECTRONICS_NAME,
		self::TYPE_SHOE_CODE                          => self::TYPE_SHOE_NAME,
		self::TYPE_LUGGAGE_BAG_CODE                   => self::TYPE_LUGGAGE_BAG_NAME,
		self::TYPE_GARDENING_CODE                     => self::TYPE_GARDENING_NAME,
		self::TYPE_SPORTS_AND_LEISURE_CODE            => self::TYPE_SPORTS_AND_LEISURE_NAME,
		self::TYPE_WATCHES_JEWELLERY_AND_GLASSES_CODE => self::TYPE_WATCHES_JEWELLERY_AND_GLASSES_NAME,
		self::TYPE_DECORATIVE_PAINTINGS_CODE          => self::TYPE_DECORATIVE_PAINTINGS_NAME,
		self::TYPE_COMPREHENSIVE_CODE                 => self::TYPE_COMPREHENSIVE_NAME
	];

	/**
	 * 获取CODE列表
	 * @return array
	 */
	public static function getTypeCodeList() {
		return array_keys(self::$TYPE_CODE_NAME_MAP);
	}

	/**
	 * 映射
	 * @param $code
	 * @return array
	 */
	public static function getTypeCodeFileMap($code) {
		$company = $code . "_company_";
		$id = $company . "id.log";
		$list = $company . "list.json";
		$csv = $company . "doc.csv";
		$log = $company . "log.log";

		self::checkAndCreateFile($id);
		self::checkAndCreateFile($list);
		self::checkAndCreateFile($csv);
		self::checkAndCreateFile($log);

		return [
			"_id"   => $id,
			"_list" => $list,
			"_csv"  => $csv,
			"_log"  => $log
		];
	}

	/**
	 * 检测并创建文件
	 * @param $file
	 */
	public static function checkAndCreateFile($file) {
		$filesystem = new Filesystem();
		if (!$filesystem->exists($filesystem)) {
			$filesystem->touch($file);
		}
	}

	/**
	 * 写文件
	 * @param $file
	 * @param $content
	 */
	public static function writeFile($file,$content){
		$filesystem = new Filesystem();
		if(is_array($content)){
			$content=\json_encode($content);
		}
		$filesystem->appendToFile($file,$content);
	}

	/**
	 * 从文件中获取数据
	 * @param $code
	 * @return array
	 */
	public static function getCompanyIdListFromFile($code) {
		$filePath = self::getTypeCodeFileMap($code)["_id"];
		$file = fopen($filePath, "r");
		$allCompanyIdList = [];
		while (!feof($file)) {
			$content = trim(fgets($file));
			if ($content) {
				$contentArr = \json_decode($content, true);
				$companyIdList = $contentArr["companyIdList"];
				$allCompanyIdList = array_merge($allCompanyIdList, $companyIdList);
			}
		}
		fclose($file);
		$uniqueAllCompanyIdList = array_unique($allCompanyIdList);
		return $uniqueAllCompanyIdList;
	}

	/**
	 * 获取公司信息
	 * @param $code
	 * @return mixed
	 */
	public static function getCompanyInfoListFromFile($code) {
		$file = self::getTypeCodeFileMap($code)["_list"];
		$contents = file_get_contents($file);
		$data = \json_decode($contents, true);
		return $data;
	}


	/**
	 * 获取公司列表
	 * @param $code
	 * @param OutputInterface $output
	 * @return array
	 */
	public function getIndustryCompanyList($code, $output) {
		$companyInfoList = self::getCompanyInfoListFromFile($code);
		if ($companyInfoList) {
			return $companyInfoList;
		}
		if($output){
			$output->writeln("公司信息列表");
		}
		$companyIdList = self::getCompanyIdListFromFile($code);
		if (count($companyIdList) == 0) {
			$companyIdList = self::getCompanyIdList($code, $output);
		}
		if($output){
			$output->writeln("公司ID列表");
		}
		$companyInfoList = [];
		foreach ($companyIdList as $idx => $companyId) {
			$companyInfo = self::getCompanyInfo($companyId, $output, $idx);
			if ($companyInfo["companyMail"]) {
				array_push($companyInfoList, $companyInfo);
			}
		}
		$file = self::getTypeCodeFileMap($code)["_list"];
		if($output){
			$output->writeln("公司信息文件");
		}
		self::writeFile($file,$companyInfoList);
		$companyInfoList = self::getCompanyInfoListFromFile($code);
		return $companyInfoList;
	}

	/**
	 * 写数据
	 * @param $code
	 * @param array $companyInfoList
	 * @throws \League\Csv\CannotInsertRecord
	 */
	public function genCSVDoc($code,array $companyInfoList) {
		$firstCompany = $companyInfoList[0];
		$header = array_keys($firstCompany);
		$csv = Writer::createFromString('');
		$csv->insertOne($header);
		$csv->insertAll($companyInfoList);
		$content = $csv->getContent(); //returns the CSV document as a string
		$file = self::getTypeCodeFileMap($code)["_csv"];
		pr($content,1);
		self::writeFile($file,$content);
	}

	/**
	 * 获取公司ID
	 * @param $type
	 * @param OutputInterface $output
	 * @return array
	 */
	public static function getCompanyIdList($code, $output) {
		$lastPage = self::getCompanyTotalPage($code);
		$name = self::$TYPE_CODE_NAME_MAP[$code];
		$allCompanyIdList = [];
		for ($i = 1; $i <= $lastPage; $i++) {
			$url = "http://www.get56.com/get56n/info/custom_${name}_${i}.shtml";
			$queryObj = QueryList::get($url);
			$detailHtml = "div[class=seller_unit_more] a";
			$detailUrlCollection = $queryObj->find($detailHtml)->attrs("href");
			$detailUrlList = $detailUrlCollection->toArray();

			$currentPageCompanyIdList = [];
			if ($detailUrlList) {
				$pattern = "/_(\d+).shtml/";
				foreach ($detailUrlList as $detailUrl) {
					$detailMatchResult = preg_match($pattern, $detailUrl, $matches);
					if ($detailMatchResult !== false) {
						$currentId = $matches[1];
						array_push($currentPageCompanyIdList, $currentId);
						array_push($allCompanyIdList, $currentId);
					}
				}
			}
			$data = [
				"page"          => $i,
				"companyIdList" => $currentPageCompanyIdList
			];
			file_put_contents(self::getTypeCodeFileMap($code)["_id"], \json_encode($data) . "\r\n", FILE_APPEND);
			$message = "第${i}页处理完毕，公司ID分别是:" . \json_encode($currentPageCompanyIdList);
			file_put_contents(self::getTypeCodeFileMap($code)["_log"], $message . " \r\n", FILE_APPEND);
			if ($output) {
				$output->writeln($message);
			}

			$rand = rand(1, 1000);
			usleep($rand);
		}
		return uniqid($allCompanyIdList);
	}

	/**
	 * 获取总页数
	 * @param $code
	 * @return mixed
	 */
	public static function getCompanyTotalPage($code) {
		$name = self::$TYPE_CODE_NAME_MAP[$code];
		$pattern = "/_(\d+).shtml/";
		$url = "http://www.get56.com/get56n/info/custom_${name}_1.shtml";
		$lastPageHtml = "a[title=尾页]";
		$queryObj = QueryList::get($url);
		/** @var TYPE_NAME $queryObj */
		$lastUrl = $queryObj->find($lastPageHtml)->attr("href");
		$matchResult = preg_match($pattern, $lastUrl, $matches);
		if ($matchResult === false) {
			dump("尾页解析错误", 1);
		}
		$lastPage = $matches[1];
		return $lastPage;
	}

	/**
	 * 获取公司信息
	 * @param $companyId
	 * @param OutputInterface $output
	 * @param $idx
	 * @return array
	 */
	public static function getCompanyInfo($companyId, $output, $idx) {
		$url = "http://www.get56.com/get56n/info/customdetail_${companyId}.shtml";
		$queryObj = QueryList::get($url);
		$divHtml = "div[class=seller_detail_info_unit_detail]";
		$InfoList = $queryObj->find($divHtml)->texts();
		$infoArr = $InfoList->toArray();

		$companyName = $infoArr[0];
		$companyAddress = $infoArr[1];
		$companyIndustry = $infoArr[2];
		$companyCountry = $infoArr[3];
		$companyContacts = $infoArr[4];
		$companyTel = $infoArr[5];
		$companyPhone = $infoArr[6];
		$companyFax = $infoArr[7];
		$companyMail = $infoArr[8];
		if ($companyMail) {
			$companyMailList = explode("@", $companyMail);
			$userName = $companyMailList[0];

			$companyInfo = [
				"companyName"     => $companyName,
				"companyAddress"  => $companyAddress,
				"companyIndustry" => $companyIndustry,
				"companyCountry"  => $companyCountry,
				"companyContacts" => $companyContacts,
				"companyTel"      => $companyTel,
				"companyPhone"    => $companyPhone,
				"companyFax"      => $companyFax,
				"companyMail"     => $companyMail,
				"userName"        => $userName,
			];
		} else {
			$companyInfo = [
				"companyName"     => "",
				"companyAddress"  => "",
				"companyIndustry" => "",
				"companyCountry"  => "",
				"companyContacts" => "",
				"companyTel"      => "",
				"companyPhone"    => "",
				"companyFax"      => "",
				"companyMail"     => "",
				"userName"        => "",
			];
		}
		$idx += 1;
		if ($output) {
			$output->writeln("第${idx}个公司【${companyId}】数据获取完毕.");
		}
		return $companyInfo;
	}

	/**
	 * 执行一类
	 * @param $code
	 * @param OutputInterface|null $output
	 * @throws \League\Csv\CannotInsertRecord
	 */
	public static function doOneType($code,OutputInterface $output=null){
		$get56 = new Get56Util();
		$companyInfoList=$get56->getIndustryCompanyList($code, $output);
		$get56->genCSVDoc($code,$companyInfoList);
		if($output){
			$output->writeln(Get56Util::$TYPE_CODE_NAME_MAP[$code]."执行完毕");
		}
		$sec=mt_rand(5,30);
		sleep($sec);
	}
}