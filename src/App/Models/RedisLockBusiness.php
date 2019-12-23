<?php

namespace App\Models;

use Lib\ErrorCode;
use Lib\Util\Config;
use malkusch\lock\mutex\PHPRedisMutex;
use Redis;

/**
 * 加锁相关业务
 * Class RedisLockBusiness
 * @package App\Models
 */
class RedisLockBusiness {
    
    const LOCK_NAME_USER_CHECKOUT="u_checkout"; //兑换
    const LOCK_NAME_USER_BET_SINGLE="u_single"; //单投
    const LOCK_NAME_USER_BET_MULTI="u_multi"; //串投
    const LOCK_NAME_USER_BET_FOLLOWING="u_following"; //跟投
    
    
    /**
     * 执行业务逻辑
     * @param $lockName
     * @param callable $businessFun
     * @param string $redisClientId
     * @return mixed
     * @throws \Exception
     */
    public static function synchronized($lockName,callable $businessFun,$redisClientId="common"){
        $redisConfig = Config::getConfigItem("redis/{$redisClientId}");
        $redis = new Redis();
        $conn=$redis->connect($redisConfig["host"],$redisConfig["port"]);
        if(!$conn){
            throw new \Exception("RD server conn error",ErrorCode::ERROR_REDIS_CONN_FAIL);
        }
        $mutex = new PHPRedisMutex([$redis], $lockName,5);
        $ex=0;
        try{
            $re=$mutex->synchronized($businessFun);
            if($re instanceof \Exception){
                $ex=$re;
                throw $re;
            }
            return $re;
        }catch (\Exception $e){
            if($e->getCode()==$ex->getCode()){
                throw $e;
            }else{
                throw new \Exception("RD server are not available",ErrorCode::ERROR_REDIS_CONN_FAIL);
            }
        }
       
    }
    
    /**
     * 获取锁名称
     * @param $lockName
     * @param int $uid
     * @return string
     */
    public static function getLockName($lockName,$uid=0){
        return $uid ? $lockName."_".$uid  : $lockName;
    }
}