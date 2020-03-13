<?php
namespace Lib\Util;

class DBUtil
{
    /*
     * 获取玩家所在的库
     * 遍历信息时使用
     */
    public static function getUserDbItems()
    {
        $dbConf = Config::loadConfig('database');
        $items = [];
        foreach ($dbConf as $key => $val) {
            if (preg_match('/^db\d+/', $key, $match)){
                $items[] = $key;
            }
        }
        return $items;
    }

    /*
     * 根据uid分库获取库的编号
     */
    public static function getDbNo($uid)
    {
        $uid = max(intval($uid), 1);
        $struct = Config::loadConfig('struct');
        return (int) floor($uid / $struct['shard_size']) + 1;
    }

    /*
     * 根据uid分库获取库名
     */
    public static function getDbName($uid)
    {
        return 'db' . self::getDbNo($uid);
    }

    /*
     * 检查该uid所在库是否在维护
     * @param number $uid
     * @return boolean
     */
    public static function uidInMaintenance($uid)
    {
        $db = self::getDbName($uid);
        return self::inMaintenance($db);
    }

    /*
     * 检查$db是否在维护中
     * @param string $db
     * @return boolean
     */
    public static function inMaintenance($db)
    {

    }
}