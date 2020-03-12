<?php
namespace Tools\Controllers;

use Lib\ErrorCode;
use Lib\Paginate;
use Lib\Util\Config;
use Lib\Util\CommonUtil;
use Tools\Models\Article;


/**
 * 网站相关
 * Class SiteController
 * @package Tools\Controllers
 */
class SiteController extends IndexController {
	
	/**
	 * 业务介绍
	 * @param $param
	 * @return mixed
	 */
	public function showBusinessList($param) {
		$paginate = Paginate::instance($this->request);
		$articleList = Article::paginate($paginate, "", [], "id", "DESC");
		return $this->renderTemplate('blog/show_article_list.php', [
			"articleList" => $articleList,
			"paginate" => $paginate,
			"search"   => $param
		]);
	}

	/**
	 * @param $param
	 * @return mixed|string
	 */
	public function addArticle($param){
		$request=$this->request;
		if($request->isPost()){
			$title=$param["title"];
			$content=$param["content"];
			$insData=[
				"title"=> $title,
				"content"=> $content,
				"category_id"=> 0,
				"visibility"=> Article::VISIBILITY_NO,
				"create_time"=> date("Y-m-d H:i:s")
			];
			Article::insert($insData);
			return $this->jsonSuccess("success",[],"/blog/showArticleList");
		}
		return $this->renderTemplate('blog/add_article.php', []);
	}

	/**
	 * 编辑
	 * @param $param
	 * @return mixed|string
	 */
	public function editArticle($param){
		$id=$param["id"];
		$request=$this->request;
		if($request->isPost()){
			$title=$param["title"];
			$content=$param["content"];
			Article::update(["title"=> "?","content"=> "?"],"id=?",[$title,$content,$id]);
			return $this->jsonSuccess("success");
		}
		$article=Article::findOne("id=?",[$id]);
		return $this->renderTemplate('blog/edit_article.php', ["article"=> $article]);
	}

	/**
	 * 发布
	 * @param $param
	 * @return string
	 */
	public function toggleArticle($param){
		$id=$param["id"];
		$visibility=$param["visibility"];
		Article::update(["visibility"=> "?"],"id=?",[$visibility,$id]);
		return $this->jsonSuccess("success");
	}
}