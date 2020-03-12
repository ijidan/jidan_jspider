<?php
namespace Tools\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tools\Models\AdminRole;
use Tools\Models\AdminUser;
use Lib\Session;

/**
 * 权限中间件
 * Class AuthMiddleware
 * @package Tools\Middlewares
 */
class AuthMiddleware {

	//不验证token的route
	private $excludedRoute = [
		'/',
		'/index/index',
		'/index/login',
		'/index/logout'
	];

	/**
	 * 构造函数
	 * AuthMiddleware constructor.
	 */
	public function __construct() {

	}

	/**
	 * @param  \Psr\Http\Message\ServerRequestInterface $request PSR7 request
	 * @param  \Psr\Http\Message\ResponseInterface $response PSR7 response
	 * @param  callable $next Next middleware
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next) {
		// before
		if (!$this->checkToken($request)) {
			return $response->withRedirect('/');
		}
		$newResponse = $next($request, $response);
		// after
		return $newResponse;
	}

	/**
	 *验证权限
	 * @param ServerRequestInterface $request
	 * @return bool
	 */
	private function checkToken(ServerRequestInterface $request) {
		if ($this->loggedOn($request) && $this->checkAccess($request)) {
			return true;
		}
		$route = $request->getUri()->getPath();
		if (in_array($route, $this->excludedRoute)) {
			return true;
		}
		return false;
	}
	/**
	 * 是否已经登录
	 * @param ServerRequestInterface $request
	 * @return bool
	 */
	private function loggedOn(ServerRequestInterface $request) {
		//先从session取
		$account = Session::get(Session::TOOLS_LOGIN_ACCOUNT);
		$role = Session::get(Session::TOOLS_LOGIN_ROLE);
		$id=Session::get(Session::TOOLS_LOGIN_ID);
		//再从cookie取
		if (empty($account) || empty($role) || empty($id)) {
			$cookies = $request->getCookieParams();
			$account = $cookies[Session::TOOLS_COOKIE_ACCOUNT];
			$token = $cookies[Session::TOOLS_COOKIE_TOKEN];
			$id=$cookies[Session::TOOLS_COOKIE_ID];
			if (empty($account) || empty($token) || empty($id)) {
				return false;
			}
			$userData = AdminUser::findOne("account=?", [$account]);
			$calcToken = md5($account . $userData['password']);
			if (empty($userData) || $token != $calcToken) {
				return false;
			}
			$role = $userData['role_id'];
			$roleData=AdminRole::findOne("id=?",[$role]);
			$roleId=$roleData["role_id"];
			//重新保存session
			Session::set(Session::TOOLS_LOGIN_ACCOUNT, $account);
			Session::set(Session::TOOLS_LOGIN_ROLE, $roleId);
			Session::set(Session::TOOLS_LOGIN_ID, $userData["id"]);
		}
		return true;
	}

	/**
	 * 检查是否有访问权限
	 * @param ServerRequestInterface $request
	 * @return bool
	 */
	private function checkAccess(ServerRequestInterface $request) {
		$route = $request->getUri()->getPath();
		$role = Session::get(Session::TOOLS_LOGIN_ROLE);
		$check = AdminRole::isAccessed($role, $route);
		return $check;
	}
}