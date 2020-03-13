<?php
namespace App\Controllers;


use App\Models\UserUtil;
use Lib\BaseController;
use Lib\Util\Config;
use Lib\Session;

/**
 * 控制器
 * Class IndexController
 * @package App\Controllers
 */
class IndexController extends BaseController
{

	/**
	 * 当前登录用户
	 * @return int
	 */
    protected function getCurrentUser()
    {
        return UserUtil::getLoginUid($this->request);
    }

	/**
	 * 获取用户登录信息
	 * @return array
	 * @throws \Exception
	 */
    protected function getUserInfo()
    {
        $uid = $this->getCurrentUser();
        $isLogin = $uid ? true : false;

        $info = ['isLogin' => $isLogin];
        if (!$isLogin) {
            return $info;
        }
        $csrfToken = $this->getCSRFToken();
        $userModel = new User($uid);
        $userAuthModel = new UserAuth();
        $userData = $userModel->findUser();
        //过滤掉敏感信息
        $userData['id'] = "".$userData['_id'];
        unset($userData['_id']);
        unset($userData['password']);
        $info['basic'] = $userData;
        $info['sns'] = $userAuthModel->getBindList($uid);
        $info['csrf_token'] = $csrfToken;
        Session::set(Session::CSRF_TOKEN, $csrfToken);
        //用户统计信息
        $model = new UserGameRecentStatistics($uid);
        $stat = $model->getRecentInfo("0", GameConst::BET_TYPE_SINGLE);
        $recent_stat = [
            "consecutive_wins" => UserStatistics::getTotalConsecutiveWins($uid),
            "day7"             => $stat["recent_day_7"],
            "day30"            => $stat["recent_day_30"],
            "all"              => $stat["recent_day_all"],
        ];
        $info["recent_stat"] = $recent_stat;
        return $info;
    }
	/**
	 * 获取当前选择的语言
	 * @return mixed
	 */
    protected function getSelectedLanguage()
    {
        $struct = Config::loadConfig('struct');
        $languages = $struct['languages'];
        $default = $struct['language_default'];

        //获取当前选择的语言
        $userInfo = $this->getUserInfo()['basic'];
        if (empty($userInfo['language'])) {
            $cookies = $this->request->getCookieParams();
            $selected = $cookies['language'];
        } else {
            $selected = $userInfo['language'];
        }
        return isset($languages[$selected]) ? $selected : $default;
    }

	/**
	 * 获取语言列表及当前选择的语言
	 * @param $selected
	 * @return array
	 */
    private function getLanguage($selected)
    {
        $languages = Config::loadConfig('struct')['languages'];
        $list = [];
        foreach ($languages as $code => $data) {
            $list[] = ['code' => $code, 'name' => $data['name']];
        }

        return [
            'selected' => [
                'code' => $selected,
                'name' => $languages[$selected]['name']
            ],
            'list' => $list
        ];
    }

	/**
	 * 模板渲染
	 * @param $template
	 * @param array $data
	 * @return mixed
	 * @throws \Exception
	 */
    protected function renderTemplate($template, array $data)
    {
        $tplData = $this->computeTplData($data);
        $template.=".html";
        return $this->view->render($this->response, $template, $tplData);
    }

	/**
	 * 渲染静态页面
	 * @param $template
	 * @param array $data
	 * @return mixed
	 * @throws \Exception
	 */
    protected function renderStaticPages($template, array $data)
    {
        $tplData = $this->computeTplData($data);
        $selected = $this->getSelectedLanguage();
        $template = $selected . "/" . $template;
        return $this->view->renderStaticPages($this->response, $template, $tplData);
    }
    
    /**
     * 获取静态页面内容
     * @param $template
     * @return mixed
     */
    protected function getStaticPagesContent($template)
    {
        $selected = $this->getSelectedLanguage();
        $template = $selected . "/" . $template;
        return $this->view->getStaticPagesContent($template);
    }

	/**
	 * 获取游戏
	 * @return mixed
	 */
    public function getSortedGames()
    {
        $list = Config::loadData('games');
        $sortKeys = [];
        foreach ($list as $key => $row) {
            $sortKeys[$key] = $row['sort'];
        }
        array_multisort($sortKeys, SORT_ASC, $list);
        return $list;
    }

	/**
	 * 获取CSRF TOKEN
	 * @return string
	 * @throws \Exception
	 */
    protected function getCSRFToken()
    {
        $oldToken = Session::get(Session::CSRF_TOKEN);
        return $oldToken ?: bin2hex(random_bytes(16));
    }

    /**
     * 获取模板数据
     * @param array $data
     * @return array
     * @throws \Exception
     */
    private function computeTplData(array $data)
    {
        $selected = $this->getSelectedLanguage();
        $struct=Config::loadConfig('struct');
        $cdnUrlApp = $struct['cdn_url_app'];
        $cdnUrlTools=$struct['cdn_url_tools'];
        $userInfo = $this->getUserInfo();
        $languageList = $this->getLanguage($selected);

        $tplData = [
            'cdnUrlApp'     => $cdnUrlApp,
            'cdnUrlTools'     => $cdnUrlTools,
            'language'       => $languageList,
            'uInfo'          => $userInfo,
            'stringUInfo'    => rawurlencode(json_encode($userInfo)),
            "serverTime"     => time()
        ];
        if ($data) {
            $intersect = array_intersect_key($tplData, $data);
            if ($intersect) {
                throw new \Exception('duplicate keys:' . implode(',', array_keys($intersect)));
            }
            $tplData += $data;
        }
        return $tplData;
    }
}