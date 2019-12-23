<?php
namespace Lib;

/**
 * 游戏错误码
 */

class ErrorCode
{
	const RIGHT=0;  //正确
    const ERROR = 1000; //服务器错误（通常是内部错误）
    const ERROR_PARAMS = 1001; //参数错误
    const NOT_LOGGED_IN = 1002; //未登录
    const NOT_ENOUGH_GOLD = 1003; //账户余额不足
    const ODDS_CHANGED = 1004; //赔率发生变化
    const BET_DISABLE = 1005; //不可下注
    const ERROR_PASSWORD = 1006; //密码不正确
    const MULTI_BET_ERROR = 1007; //串投异常处理
    const IP_NUM_LIMIT = 1010; //同一IP注册数量达到限制
    const ERROR_CSRF = 1011; //csrf不匹配

    //支付相关错误 1100-1200
    const ERROR_PAY_PARAMS = 1100; //支付参数错误

    //邮箱验证相关错误 1200-1300
    const ERROR_INVALID_EMAIL = 1200;  //邮件格式错误
    const ERROR_EMAIL_NOT_FOUND = 1201; //邮箱不存在
    const ERROR_LIMIT_EVERYDAY_MAX_NUM = 1202; //不满足限制条件
    const ERROR_EMAIL_SEND = 1203; //邮件发送失败
    const ERROR_EMAIL_USED = 1204; //邮箱已经被使用

    //兑换相关错误 1300-1400
    const ERROR_EXCHANGE = 1300; //商品兑换错误
    const ERROR_NOT_ON_SALE = 1301; //已经不再销售
    const ERROR_LACK_OF_STOCK = 1302; //库存不足

    //第三方账号
    const ERROR_THIRD_BIND_REPEAT = 1400; //已经绑定过第三方账号
    const ERROR_THIRD_HAS_BEEN_USED = 1401; //第三方账号已经被使用

    //连续登陆奖励
    const ERROR_CONTINUOUS_LOGIN_ALREADY_RECEIVED = 1500; //已经领取
    const ERROR_CONTINUOUS_LOGIN_DAY_WRONG = 1501;  //参数错误

    //任务相关
    const ERROR_TASK_REWARD_NOT_FINISHED = 1600; //任务未完成
    const ERROR_TASK_REWARD_ALREADY_RECEIVED = 1601;  //已经领取
    
    //用户相关限制相关
    const ERROR_USER_RESTRICT_PERMISSION=1701; //账户限制
    
    //活动相关
    const ERROR_ACT_TIME_LESS_THAN_START_TIME=1801; //当前时间小于开始时间
    const ERROR_ACT_TIME_GREATER_THAN_END_TIME=1802; //当前时间大于结束时间
    
    //Redis相关
    const ERROR_REDIS_CONN_FAIL=1901; //连接失败
    const ERROR_REDIS_GET_LOCK_FAIL=1902; //获取锁失败
    
}