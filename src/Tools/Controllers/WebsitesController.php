<?php

namespace Tools\Controllers;


/**
 * 网站管理
 * Class WebsitesController
 * @package Tools\Controllers
 */
class WebsitesController extends IndexController {
	/**
	 * 网站列表
	 * @return mixed
	 */
	public function showList() {
		$websiteList = array(
			array(
				"name"           => "smileDeer官网",
				"url"            => "www.smiledeer.com",
				"note"           => "",
				"readonly"       => 0,
				"key_point"      => 0,
				"cross_the_wall" => 0,
			),
			array(
				"name"           => "smileDeer官网后台",
				"url"            => "www.smiledeer.com/WebUser/index.php?m=login&a=index",
				"note"           => "",
				"readonly"       => 0,
				"key_point"      => 0,
				"cross_the_wall" => 0,
			),
			array(
				"name"           => "smileDeer内网",
				"url"            => "manager.smiledeer.com",
				"note"           => "",
				"readonly"       => 0,
				"key_point"      => 1,
				"cross_the_wall" => 0,
			),
			array(
				"name"           => "企业邮箱",
				"url"            => "mail.smiledeer.com",
				"note"           => "",
				"readonly"       => 0,
				"key_point"      => 1,
				"cross_the_wall" => 0,
			),
			array(
				"name"           => "谷歌翻译",
				"url"            => "translate.google.cn",
				"note"           => "",
				"readonly"       => 0,
				"key_point"      => 0,
				"cross_the_wall" => 0,
			),
			array(
				"name"           => "1688",
				"url"            => "www.1688.com",
				"note"           => "",
				"readonly"       => 0,
				"key_point"      => 0,
				"cross_the_wall" => 0,
			),
			array(
				"name"           => "Alibaba",
				"url"            => "www.alibaba.com",
				"note"           => "",
				"readonly"       => 0,
				"key_point"      => 0,
				"cross_the_wall" => 0,
			),
			array(
				"name"           => "Amazon",
				"url"            => "www.alibaba.com",
				"note"           => "",
				"readonly"       => 0,
				"key_point"      => 0,
				"cross_the_wall" => 0,
			),
			array(
				"name"           => "FTP文件共享",
				"url"            => "ftp://47.254.87.158/",
				"note"           => "",
				"readonly"       => 1,
				"key_point"      => 0,
				"cross_the_wall" => 0,
			),
		);
		return $this->renderTemplate("websites/show_list.php", [
			"websiteList" => $websiteList
		]);
	}
}