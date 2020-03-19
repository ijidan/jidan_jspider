<?php

namespace Api\Controllers;

/**
 * 新房
 * Class HouseController
 * @package App\Controllers
 */
class HouseController extends IndexController {
	/**
	 * 首页
	 * @return mixed
	 * @throws \Exception
	 */
	public function index() {
		return $this->jsonSuccess('succ',['name'=> 'jidan']);
	}
}