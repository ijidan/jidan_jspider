<?php
namespace Tools\Controllers;

use Lib\BaseController;
use Lib\Util\CommonUtil;
use Lib\Util\DateUtil;
use Lib\Session;
use Tools\Models\AdminRole;
use Tools\Models\AdminUser;

/**
 * Class IndexController
 * @package Tools\Controllers
 */
class IndexController extends BaseController {

	/**
	 * 获取当前登录用户
	 * @return array
	 */
	protected function getCurrentUser() {
		return self::getLoginUser();
	}

	/**
	 * 获取登录用户
	 * @return array
	 */
	public static function getLoginUser() {
		return [
			'account' => Session::get(Session::TOOLS_LOGIN_ACCOUNT),
			'role'    => Session::get(Session::TOOLS_LOGIN_ROLE),
			'id'=>  Session::get(Session::TOOLS_LOGIN_ID)
		];
	}
	/**
	 * 获取模板内容
	 * @param $template
	 * @return mixed
	 */
	protected function getTemplate($template) {
		$content = $this->view->fetch($template, []);
		return $content;
	}
	/**
	 * 模板渲染
	 * @param $template
	 * @param array $data
	 * @return mixed
	 */
	protected function renderTemplate($template, array $data) {
		$path=$this->request->getUri()->getPath();
		$mergedData=$data+["currentPath"=>$path];
		return $this->view->render($this->response, $template, $mergedData);
	}

	/**
	 * 首页
	 * @param $params
	 * @return mixed
	 */
	public function index($params) {
		$user = $this->getCurrentUser();
		if (!$user['account']) {
			return $this->render('login.php', $params);
		}
		return $this->response->withRedirect('/websites/showList');
		//return $this->renderTemplate('index.php', ['account' => $user['account']]);
	}

	/**
	 * 登录
	 * @param $params
	 * @return \Slim\Http\Response
	 */
	public function login($params) {
		$account = $params['account'] ?: '';
		$password = $params['password'] ?: '';
		$remember = $params['remember'] ? true : false;

		if ($account) {
			$account = strtolower($account);
		}
		$conWhere = "account=? and is_del=" . AdminUser::STATUS_IS_DEL_NO;
		$conValues = [$account];
		$userData = AdminUser::findOne($conWhere, $conValues);
		if ($userData && CommonUtil::checkPassword($password, $userData['password'])) {
			AdminUser::update(["update_time" => "?"], $conWhere, [time(), $account]);
			$adminRoleId = $userData["role_id"];
			$adminRole = AdminRole::findOne("id=?", [$adminRoleId]);
			//先存session
			Session::set(Session::TOOLS_LOGIN_ACCOUNT, $account);
			Session::set(Session::TOOLS_LOGIN_ROLE, $adminRole['role_id']);
			Session::set(Session::TOOLS_LOGIN_ID,$userData["id"]);
			//再存cookie
			if ($remember) {
				$this->setCookie(Session::TOOLS_COOKIE_ACCOUNT, $account, DateUtil::YEAR);
				$this->setCookie(Session::TOOLS_COOKIE_TOKEN, md5($account . $userData['password']), DateUtil::YEAR);
				$this->setCookie(Session::TOOLS_COOKIE_ID,$userData["id"],DateUtil::YEAR);
			}
		}
		return $this->response->withRedirect('/');
	}
	/**
	 * 退出登录
	 * @return \Slim\Http\Response
	 */
	public function logout() {
		//清除session
		Session::destroy();
		//清除cookie
		$cookies = $this->request->getCookieParams();
		$cookieKeys = array_keys($cookies);
		foreach ($cookieKeys as $key) {
			$this->setCookie($key, '', -1);
		}
		return $this->response->withRedirect('/');
	}
}