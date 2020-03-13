<?php

namespace Tools\Controllers;


/**
 * 聊天
 * Class ChatController
 * @package Tools\Controllers
 */
class ChatController extends IndexController {

	/**
	 * 聊天列表
	 * @param $param
	 * @return mixed
	 */
	public function showList($param) {
		return $this->renderTemplate('chat/show_list.php', ["articleList"=>[]]);
	}
}