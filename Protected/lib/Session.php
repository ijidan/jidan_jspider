<?php
namespace Lib;
/*
 * 参考：https://github.com/akrabat/rka-slim-session-middleware/blob/master/RKA/Session.php
 */
class Session
{
	const TOOLS_LOGIN_ID='lb_tools_id';
    const TOOLS_LOGIN_ACCOUNT = 'lb_tools_account';
    const TOOLS_LOGIN_ROLE = 'lb_tools_role';
    const PARTNER_LOGIN_ACCOUNT = 'lb_partner_account';

    const LOGIN_UID = 'lb_uid'; //登录账号UID
    const CAPTCHA = 'captcha';  //验证码
    const CSRF_TOKEN = 'csrf_token';

    const TOOLS_COOKIE_ID='tools_id';
    const TOOLS_COOKIE_ACCOUNT = 'tools_account';
    const TOOLS_COOKIE_TOKEN = 'tools_token';

    const PARTNER_COOKIE_ACCOUNT = 'partner_account';
    const PARTNER_COOKIE_TOKEN = 'partner_token';

    public static function get($key, $default = null)
    {
        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }
        return $default;
    }

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function delete($key)
    {
        if (array_key_exists($key, $_SESSION)) {
            unset($_SESSION[$key]);
        }
    }

    public static function clearAll()
    {
        $_SESSION = [];
    }

    public static function regenerate()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public static function destroy()
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 86400,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        if (session_status() == PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}