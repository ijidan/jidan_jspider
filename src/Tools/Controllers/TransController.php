<?php
namespace Tools\Controllers;

use Lib\ErrorCode;
use Lib\Paginate;
use Lib\Util\CommonUtil;
use Tools\Models\Category;
use Tools\Models\TransSearchKeywords;

/**
 * Class TransController
 * @package Tools\Controllers
 */
class TransController extends IndexController {
	/**
	 * 订单列表
	 * @param $param
	 * @return mixed
	 */
	public function showSearchKeywordsList($param) {

		$viVn = $param["vi_vn"];
		$zhCn = $param["zh_cn"];
		$conWhere = "1=1 ";
		$conData = [];
		if ($viVn) {
			$conWhere .= " and vi_vn=?";
			$conData[] = $viVn;
		}
		if ($zhCn) {
			$conWhere .= " and zh_cn=?";
			$conData[] = $zhCn;
		}
		$paginate = Paginate::instance($this->request);
		$keywordsList = TransSearchKeywords::paginate($paginate, $conWhere, $conData, "id", "DESC");
		return $this->renderTemplate("trans/show_search_keywords_list.php", [
			"keywordsList" => $keywordsList,
			"paginate"     => $paginate,
			"search"       => $param
		]);
	}

	/**
	 * 添加搜索关键字
	 * @param $param
	 * @return \Lib\BaseController|mixed
	 */
	public function addSearchKeywords($param) {
		$request = $this->request;
		if ($request->isPost()) {
			$viVn = $param["vi_vn"] ?: "";
			$zhCn = $param["zh_cn"] ?: "";
			if (!$viVn && !$zhCn) {
				return $this->iFrameResponseFail(ErrorCode::ERROR, "Vietnamese or Chinese(Simplified) cant all empty!");
			}
			$conWhere = "1=1";
			$conData = [];
			if ($viVn) {
				$conWhere .= " and vi_vn=?";
				$conData[] = $viVn;
			}
			if ($zhCn) {
				$conWhere .= " and zh_cn=?";
				$conData[] = $zhCn;
			}
			if ($conData) {
				$keywords = TransSearchKeywords::findOne($conWhere, $conData);
				if ($keywords) {
					return $this->iFrameResponseFail(ErrorCode::ERROR, "the translation exists!");
				}
			}
			$insertId = TransSearchKeywords::insert(["vi_vn" => $viVn, "zh_cn" => $zhCn]);
			return $insertId ? $this->iFrameResponseSuccess("success") : $this->jsonFail(ErrorCode::ERROR, "fail");
		} else {
			return $this->renderTemplate("trans/add_search_keywords.php", []);
		}
	}

	/**
	 * 编辑搜索关键字
	 * @param $param
	 * @return \Lib\BaseController|mixed
	 */
	public function editSearchKeywords($param) {
		$request = $this->request;
		$id = $param["id"];
		if ($request->isPost()) {
			$viVn = $param["vi_vn"] ?: "";
			$zhCn = $param["zh_cn"] ?: "";
			if (!$viVn && !$zhCn) {
				return $this->iFrameResponseFail(ErrorCode::ERROR, "Vietnamese or Chinese(Simplified) cant all empty!");
			}
			TransSearchKeywords::update(["vi_vn" => "?", "zh_cn" => "?"], "id=?", [$viVn, $zhCn, $id]);
			return $this->iFrameResponseSuccess("success");
		} else {
			$keywords = TransSearchKeywords::findOne("id=?", [$id]);
			return $this->renderTemplate("trans/edit_search_keywords.php", [
				"keywords" => $keywords
			]);
		}
	}

	/**
	 * 删除搜索关键字
	 * @param $param
	 * @return \Lib\BaseController
	 */
	public function deleteSearchKeywords($param) {
		$id = $param["id"];
		if (!$id) {
			return $this->jsonFail(ErrorCode::ERROR, "param error");
		}
		TransSearchKeywords::delete("id=?", [$id]);
		return $this->jsonSuccess("success");
	}

	/**
	 * @param $param
	 * @return mixed
	 */
	/**
	 * 显示所有
	 * @return mixed
	 */
	public function showKeywordsCategory() {
		$categoryList = Category::find("is_del=?", [Category::IS_DEL_NO]);
		$data = $this->arrToTree($categoryList, 0);
		return $this->renderTemplate("trans/show_keywords_category_list.php", [
			"categoryList" => $data
		]);
	}

	/**
	 * @param $param
	 * @return mixed
	 */
	public function editCategory($param) {
		$id = $param["id"];
		$request = $this->request;
		if ($request->isPost()) {
			$img = $param["img"];
			$viVnName = $param["vi_vn_name"];
			$zhCnName = $param["zh_cn_name"];
			$enUsName = $param["en_us_name"];

			Category::update(["img" => "?", "vi_vn_name" => "?", "zh_cn_name" => "?","en_us_name" => "?"], "id=?", [
				$img,
				$viVnName,
				$zhCnName,
				$enUsName,
				$id
			]);
			return $this->iFrameResponseSuccess("success");
		}
		$category = Category::findOne("id=?", [$id]);
		return $this->renderTemplate("trans/edit_keywords_category.php", [
			"category" => $category
		]);
	}

	/**
	 * 查看详情
	 * @param $param
	 * @return mixed
	 */
	public function categoryDetail($param) {
		$id = $param["id"];
		$category = Category::findOne("id=?", [$id]);
		return $this->renderTemplate("trans/keywords_category_detail.php", [
			"category" => $category
		]);
	}

	/**
	 * 删除
	 * @param $param
	 * @return \Lib\BaseController
	 */
	public function deleteCategory($param) {
		$id = $param["id"];
		Category::update(["is_del" => "?"], "id=?", [Category::IS_DEL_YES, $id]);
		return $this->jsonSuccess("success");
	}

	/**
	 * 数组转Tree
	 * @param $data
	 * @param $pid
	 * @return array
	 */
	private function arrToTree($data, $pid) {
		$tree = array();
		foreach ($data as $k => $v) {
			if ($v['parent_id'] == $pid) {
				$v['parent_id'] = $this->arrToTree($data, $v['id']);
				$tree[] = $v;
			}
		}
		return $tree;
	}
}