<?php
namespace Tools\Models;

/**
 * socket Business
 * Class SocketBusiness
 * @package Tools\Models
 */
class SocketBusiness{

	/** 用户相关事件 */
	const EVENT_USER_PULL_SERVICE=101; //用户拉取客服列表
	const EVENT_USER_SEND_MSG=102;//用户发送消息

	const EVENT_SERVICE_SEND_MSG=201;// 客服发送消息

	/** socket相关事件 */
	const EVENT_SOCKET_CONN=901; //socket连接成功
	const EVENT_SOCKET_PUSH_SERVICE=902; //推送客服列表
	const EVENT_SOCKET_SEND_MSG=903; //socket发送消息

	public static function sendMessage2Service($serviceId,$message){
		$message=trim($message);
		if(!$message){
		}
	}
}