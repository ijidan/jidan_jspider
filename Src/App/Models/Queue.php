<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/9
 */

namespace App\Models;


use Lib\BaseModel;
use MongoDB\BSON\ObjectID;

/**
 * 队列
 * Class Queue
 * @package App\Models
 */
class Queue extends BaseModel
{
    const STATUS_PENDING = 0;
    const STATUS_RUNNING = 1;

    const TYPE_EMAIL_NOTIFY = 1; //邮件通知
    const TYPE_WECHAT_NOTIFY = 2; //微信通知

    private $schema = [
        '_id'          => '', //ID
        'type'         => '', //类型
        'data'         => [], //具体数据
        'extra' => [
            'uid'      => 0,  //用户id
            'openid'   => '', //微信账号
        ],
        'time_created' => 0, //创建时间
        'priority' => 0, //优先级
        'status' => 0
    ];

    /**
     * 构造函数
     * AdminUser constructor.
     */
    public function __construct()
    {
        parent::__construct('logger', 'queue');
    }

    /**
     *
     * @param $uid
     * @param $data
     */
    public static function pushWXMessage($uid, $data)
    {
        //检测用户是否关注公众号
        $userAuth = new UserAuth();
        $user = $userAuth->findOne(['uid' => $uid, 'oauth_name' => UserAuth::SUPPORT_TYPE_WEIXIN]);
        if (empty($user)) {
            return;
        }
        $openid = $user['oauth_user']['openid'];
        if (empty($openid)) {
            return;
        }
        //发送消息
        self::push(self::TYPE_WECHAT_NOTIFY, $data, [
            'uid' => $uid,
            'openid' => $openid,
        ]);
    }

	/**
	 * PUSh
	 * @param $type
	 * @param array $data
	 * @param array $extra
	 */
    public static function push($type, $data = [], $extra = [])
    {
        $queueModel = new Queue();
        $queueModel->insertOne([
            'type' => $type,
            'data' => $data,
            'extra' => $extra,
            'time_created' => NOW,
            'priority' => 0,
            'status' => 0
        ]);
    }

    /**
     * 更新队列状态
     * @param $id
     * @param $status
     */
    public static function changeStatus($id, $status)
    {
        $objectId = is_string($id) ? new ObjectID($id) : $id;
        $queueModel = new Queue();
        $queueModel->updateOne(['_id' => $objectId], ['$set' => ['status' => $status]]);
    }

	/**
	 * 表前缀
	 * @return string
	 */
	public function getTablePrefix() {
		// TODO: Implement getTablePrefix() method.
	}

	/**
	 * 表名
	 * @return mixed
	 */
	public function getTableName() {
		// TODO: Implement getTableName() method.
	}

	/**
	 * 主键
	 * @return string
	 */
	public function getPrimaryKey() {
		// TODO: Implement getPrimaryKey() method.
	}
}