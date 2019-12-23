<?php
namespace Tools\Controllers;

use Lib\Paginate;
use Lib\Util\CommonUtil;
use Tools\Models\Article;
use Tools\Models\ArticleCategory;


/**
 * 博客
 * Class BlogController
 * @package Tools\Controllers
 */
class BlogController extends IndexController {

	/**
	 * 文章分类
	 * @param $param
	 * @return mixed
	 */
	public function showArticleCategory($param){
		$paginate = Paginate::instance($this->request);
		$articleCategory = ArticleCategory::paginate($paginate, "", [], "id", "DESC");
		return $this->renderTemplate('blog/show_article_category.php', [
			"articleCategory" => $articleCategory,
			"paginate" => $paginate,
			"search"   => $param
		]);
	}
	/**
	 * 用户列表
	 * @param $param
	 * @return mixed
	 */
	public function showArticleList($param) {
		$paginate = Paginate::instance($this->request);
		$articleList = Article::paginate($paginate, "", [], "id", "DESC");
		$categoryList=ArticleCategory::find();
		$categoryListById=$categoryList ? CommonUtil::arrayGroup($categoryList,"id",true):[];
		return $this->renderTemplate('blog/show_article_list.php', [
			"articleList" => $articleList,
			"categoryListById"=> $categoryListById,
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
			$categoryId=$param["category_id"];
			$title=$param["title"];
			$content=$param["content"];
			$insData=[
				"title"=> $title,
				"content"=> $content,
				"category_id"=> $categoryId,
				"visibility"=> Article::VISIBILITY_NO,
				"create_time"=> date("Y-m-d H:i:s")
			];
			Article::insert($insData);
			return $this->jsonSuccess("success",[],"/blog/showArticleList");
		}
		$categoryList=ArticleCategory::find();
		return $this->renderTemplate('blog/add_article.php', ["categoryList"=>$categoryList]);
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
			$categoryId=$param["category_id"];
			$title=$param["title"];
			$content=$param["content"];
			Article::update(["title"=> "?","content"=> "?","category_id"=> "?"],"id=?",[$title,$content,$categoryId,$id]);
			return $this->jsonSuccess("success");
		}
		$article=Article::findOne("id=?",[$id]);
		$categoryList=ArticleCategory::find();
		return $this->renderTemplate('blog/edit_article.php', ["article"=> $article,"categoryList"=>$categoryList]);
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