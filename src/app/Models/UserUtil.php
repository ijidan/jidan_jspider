<?php
namespace App\Models;

use Lib\Util\Config;
use Lib\Util\Crypt;
use Lib\Util\DBUtil;
use Lib\Session;
use \Psr\Http\Message\ServerRequestInterface;

/**
 * 用户登陆相关
 * Class UserUtil
 * @package App\Models
 */
class UserUtil
{
    const SESSION_KEY_UID = 'lb_uid'; //live bet user id

	/**
	 * 验证用户TOKEN
	 * @param $uid
	 * @param $token
	 * @return bool
	 */
    public static function verifyToken($uid, $token)
    {
        $isValid = false;

        $salt = Config::loadConfig('struct')['cookie_salt'];
        $token = Crypt::decrypt($token, $salt);
        list($user, $expires, $signature) = explode(':', $token);

        if ($uid == $user
            && md5($user.$expires) == $signature
            && $expires > NOW) {
            $isValid = true;
        }

        return $isValid;
    }

	/**
	 * 创建TOKEN
	 * @param $user
	 * @param $lifeTime
	 * @return string
	 */
    public static function generateToken($user, $lifeTime)
    {
        $raw = [$user, NOW + $lifeTime];
        $raw[] = md5(implode('', $raw));
        $token = implode(':', $raw);

        $salt = Config::loadConfig('struct')['cookie_salt'];
        return Crypt::encrypt($token, $salt);
    }

	/**
	 * 获取登录用户ID
	 * @param ServerRequestInterface $request
	 * @return int
	 */
    public static function getLoginUid(ServerRequestInterface $request)
    {
        //先从session取
        $uid = Session::get(Session::LOGIN_UID);

        //验证是否为机器人
        if (empty($uid)) {
            $uid = $request->getParams()['robot'];
            //$uid = $request->getQueryParams()['robot'];
            if ($uid) {
                $uid = intval($uid);
                $userModel = new User($uid);
                $userData = $userModel->findUser();
                if (empty($userData) || $userData['type'] != User::$types['Robot']) {
                    return 0;
                }
                //更新session
                Session::set(Session::LOGIN_UID, $uid);
            }
        }

        //再从cookie取
        if (empty($uid)) {
            $cookies = $request->getCookieParams();
            $uid = $cookies['uid'];
            $token = $cookies['token'];
            if (empty($uid) || empty($token)) {
                return 0;
            }
            if (!self::verifyToken($uid, $token)) {
                return 0;
            }
            $uid = intval($uid);
            $userModel = new User($uid);
            $userModel->updateUser(['$set' => ['time_login' => NOW]]);
            //更新session
            Session::set(Session::LOGIN_UID, $uid);
        }
        return intval($uid);;
    }

	/**
	 * TOOLS里面显示玩家所在DB
	 * @param $uid
	 * @return string
	 */
    public static function getUserDb($uid)
    {
        $dbConf = Config::loadConfig('database');
        $dbNo = DBUtil::getDbNo($uid);
        $dbItem = $dbConf["db{$dbNo}"];
        return "db:{$dbItem['database']} [{$dbItem['server']}]";
    }
}