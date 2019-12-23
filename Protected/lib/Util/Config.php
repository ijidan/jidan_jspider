<?php
namespace Lib\Util;

/*
 * 获取配置的工具类
 */
use App\Models\OperationConf;

class Config
{
    private static $configList = [];
    private static $dataList = [];
    private static $i18nList = [];

	/**
	 * 加载配置
	 * @param $fileName
	 * @return mixed
	 */
    public static function loadConfig($fileName)
    {
        $path = "config/{$fileName}.php";
        if (!isset(self::$configList[$path])) {
            self::$configList[$path] = self::load($path);
        }
        return self::$configList[$path];
    }
    
    /**
     * 获取配置项
     * @param $key
     * @return mixed
     */
    public static function getConfigItem($key)
    {
        $key = strtolower(trim($key,"/"));
        $keyList = explode("/", $key);
        $filename = array_shift($keyList);
        $configContent = self::loadConfig($filename);
        if (!$keyList) {
            return $configContent;
        }
        $value = $configContent;
        foreach ($keyList as $k) {
            if (!isset($value[$k])) {
                throw new \OutOfRangeException("{$key} configure not found");
            }
            $value = $value[$k];
        }
        return $value;
    }

	/**
	 * 加载数据
	 * @param $fileName
	 * @return mixed
	 */
    public static function loadData($fileName)
    {
        //$list = explode('.', $fileName);
        //$fileName = implode('/', $list);
        $defaultPath = BASE_DIR . "/data/default/{$fileName}.php";
        $path = BASE_DIR . "/data/{$fileName}.php";
        if (!isset(self::$dataList[$path])) {
            if (file_exists($defaultPath)) {
                self::$dataList[$path] = array_replace_recursive(self::load($defaultPath), self::load($path));
            }
            self::$dataList[$path] = self::load($path);
        }
        return self::$dataList[$path];
    }
    
    /**
     * 获取所有游戏，ID为0，代表所有游戏
     * @return mixed
     */
    public static function loadGamesIncludingAll()
    {
        $games = self::loadData('games');
        $games[0] = [
            "id"   => 0,
            "name" => "",
            "tag"  => "",
            "key"  => "",
            "logo" => ""
        ];
        return $games;
    }

	/**
	 * 加载游戏数据
	 * @param $gameId
	 * @param $fileName
	 * @return mixed
	 */
    public static function loadGameData($gameId, $fileName)
    {
        $games = Config::loadData('games');
        $file = implode('/', [$games[$gameId]['key'], $fileName]);
        return Config::loadData($file);
    }

	/**
	 * 多语言文件
	 * @param $lang
	 * @param $fileName
	 * @return mixed
	 */
    public static function loadI18n($lang, $fileName)
    {
        $path = BASE_DIR . "/i18n/{$lang}/{$fileName}.php";
        if (!isset(self::$i18nList[$path])) {
            self::$i18nList[$path] = self::load($path);
        }
        return self::$i18nList[$path];
    }

	/**
	 * 加载PHP文件
	 * @param $path
	 * @return mixed
	 * @throws \ErrorException
	 */
    private static function load($path)
    {
        $data = require $path;

        if (!is_array($data)) {
            throw new \ErrorException('PHP file does not return an array');
        }

        return $data;
    }
    
    /**
     * 计算 quizTypeCatId
     * @param $gameId
     * @param $quizType
     * @return int|string
     */
    public static function getQuizTypeCatId($gameId, $quizType)
    {
        $catConfig = Config::loadGameData($gameId, 'user_quiz_stat');
        $quizTypeCatId = "";
        foreach ($catConfig as $cid => $quizTypeList) {
            if (in_array($quizType, $quizTypeList)) {
                $quizTypeCatId = $cid;
                break;
            }
        }
        return $quizTypeCatId;
    }
    
    /**
     * 获取运营配置
     * @return array
     */
    public static function loadOperationConfig()
    {
        $operationConfigModel = new OperationConf();
        $data = $operationConfigModel->getConfig();
        return $data;
    }
    
    /**
     * 获取运营配置项
     * @param $key
     * @return array|mixed
     */
    public static function getOperationConfigItem($key)
    {
        $configContent = self::loadOperationConfig();
        if (!$configContent) {
            throw new \RuntimeException("no config");
        }
        $key = strtolower(trim($key, "/"));
        $keyList = explode("/", $key);
        $value = $configContent;
        foreach ($keyList as $k) {
            if (!isset($value[$k])) {
                throw new \OutOfRangeException("{$key} configure not found");
            }
            $value = $value[$k];
        }
        return $value;
    }
}