<?php
namespace Lib;

use Monolog\Logger;
use Monolog\Handler\MongoDBHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\JsonFormatter;
use Monolog\Processor\MemoryPeakUsageProcessor;
use Monolog\Processor\WebProcessor;
use Lib\Util\Config;

/**
 * 日志
 * Class BaseLogger
 * @package Lib
 */
class BaseLogger
{
	const CHANNEL_APP = 'app';
	const CHANNEL_MOBILE = 'mobile';
	const CHANNEL_PARTNER = 'partner';
	const CHANNEL_TOOLS = 'tools';
	const CHANNEL_EVENTS = 'events';
	const CHANNEL_BUSINESS_SERVICE="business_service";
	const CHANNEL_PUSH_MSG="act_push_match_result";
	const CHANNEL_OFFICIAL_ACCOUNT = "official_account";
	const CHANNEL_ACT_SUPPORT = "act_support";
	const CHANNEL_ACT_NEW_YEAR_BOX = "new_year_box";
	const CHANNEL_USER_BOX = "user_box";
	const CHANNEL_PHONE_CODE = "phone_code";

	//日志记录至数据库
	const DB = 'logger';
	const DB_COLLECTION = 'logger';

	public static $channels = [
		self::CHANNEL_APP,
		self::CHANNEL_MOBILE,
		self::CHANNEL_PARTNER,
		self::CHANNEL_TOOLS,
		self::CHANNEL_EVENTS
	];

	/**
	 * 实例
	 * @param $channel
	 * @return Logger
	 * @throws \Exception
	 */
    public static function instance($channel)
    {
        $level = Logger::INFO;
        //存Mongo
        $dbKey = self::DB;
        $collection = self::DB_COLLECTION;
        //$database = Config::loadConfig('database')[$dbKey]['database'];
        //$mongodb = BaseModel::dbInstance($dbKey);
        //$mongodbHandler = new MongoDBHandler($mongodb, $database, $collection, $level);
        //存文件
        $file = BASE_DIR . '/storage/' . $channel .'/' . date('Y-m-d') . '.log';
        $streamHandler = new StreamHandler($file, $level);
        $streamHandler->setFormatter(new JsonFormatter());
        //初始化Logger
        $logger = new Logger($channel);
        $logger->pushProcessor(new MemoryPeakUsageProcessor());
        $logger->pushProcessor(new WebProcessor());
        $logger->pushHandler($streamHandler);
        //$logger->pushHandler($mongodbHandler);
        //$logger->pushHandler(new ErrorLogHandler());
        return $logger;
    }

	/**
	 * 每日错误汇总统计
	 * @param \Exception $exception
	 */
    public static function dailyError(\Exception $exception)
    {
        $date = date('Y-m-d');
        $refs = [
            'msg' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'code' => $exception->getCode()
        ];
        $md5 = md5($date . serialize($refs));
        $where = ['_id' => $md5,];
        $update = [
            '$set' => [
                'msg' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'code' => $exception->getCode(),
                'date' => $date
            ],
            '$inc' => [
                'count' => 1
            ]
        ];
        //$logger = new BaseModel('logger', 'logger_daily');
        //$logger->updateOne($where, $update, ['upsert' => true]);
    }
}