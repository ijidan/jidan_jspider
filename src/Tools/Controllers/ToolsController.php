<?php
namespace Tools\Controllers;

use Lib\Paginate;
use Lib\Util\CommonUtil;
use Tools\Models\Category;
use Tools\Models\Goods;
use Tools\Models\TransSearchKeywords;


/**
 * 工具列表
 * Class ToolsController
 * @package Tools\Controllers
 */
class ToolsController extends IndexController {

	const TOOLS_TYPE_SYNC_CATEGORY_2_SEARCH_KEYWORDS = 1;

	/**
	 * 相关说明
	 * @var array
	 */
	public static $toolsInfoMap = [
		self::TOOLS_TYPE_SYNC_CATEGORY_2_SEARCH_KEYWORDS => [
			"name" => "Sync category 2 search keywords",
			"desc" => "Sync category 2 search keywords",
		]
	];
	/**
	 * 列表
	 * @var array
	 */
	public static $toolsList = [
		self::TOOLS_TYPE_SYNC_CATEGORY_2_SEARCH_KEYWORDS
	];

	/**
	 * 订单列表
	 * @param $param
	 * @return mixed
	 */
	public function showList($param) {
		return $this->renderTemplate("tools/show_list.php", [
		]);
	}

	/**
	 * 执行工具
	 * @param $param
	 * @return \Lib\BaseController
	 */
	public function execTools($param) {
		$id = $param["id"];
		if ($id == self::TOOLS_TYPE_SYNC_CATEGORY_2_SEARCH_KEYWORDS) {
			$this->syncCategory2SearchWords();
		}
		return $this->jsonSuccess("success");
	}

	/**
	 * 同步
	 */
	private function syncCategory2SearchWords() {
		$categoryList = Category::find();
		if ($categoryList) {
			foreach ($categoryList as $category) {
				$zhCnName = $category["zh_cn_name"];
				$viVnName = $category["vi_vn_name"];
				//判断中文
				if ($zhCnName) {
					$searchKeywords = TransSearchKeywords::findOne("zh_cn=?", [$zhCnName]);
					if (!$searchKeywords) {
						TransSearchKeywords::insert(["zh_cn" => $zhCnName, "vi_vn" => $viVnName]);
					}
				}
				//判断越南语
				if ($viVnName) {
					$searchKeywords = TransSearchKeywords::findOne("vi_vn=?", [$viVnName]);
					if (!$searchKeywords) {
						TransSearchKeywords::insert(["zh_cn" => $zhCnName, "vi_vn" => $viVnName]);
					}
				}
			}
		}
	}
}