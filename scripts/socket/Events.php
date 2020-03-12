<?php

/**
 * 主逻辑
 * 主要是处理 onMessage onClose 三个方法
 */

use \GatewayWorker\Lib\Gateway;
use Lib\Util\SocketUtil;
use Tools\Models\AdminUser;
use Tools\Models\SocketBusiness;

class Events {
	/**
	 * 当客户端连上时间触发
	 * @param $clientId
	 */
	public static function onConnect($clientId) {
		$data = [
			"event_id"   => SocketBusiness::EVENT_SOCKET_CONN,
			"event_data" => [
				"client_id" => $clientId
			]
		];
		Gateway::sendToCurrentClient(\json_encode($data));
	}

	/**
	 * 有消息时
	 * @param $clientId
	 * @param $message
	 * @return null
	 */
	public static function onMessage($clientId, $message) {
		// 获取客户端请求
		file_put_contents("/data/events.log", $message . PHP_EOL, FILE_APPEND);

		$messageData = json_decode($message, true);
		$eventId = $messageData["event_id"];
		$eventData = $messageData["event_data"];

		switch ($eventId) {
			case SocketBusiness::EVENT_USER_PULL_SERVICE:
				$serviceList = AdminUser::getValidServiceList();
				$data = [
					"event_id"   => SocketBusiness::EVENT_SOCKET_PUSH_SERVICE,
					"event_data" => [
						"service_list" => $serviceList
					]
				];
				Gateway::sendToCurrentClient(\json_encode($data));
				break;
			case SocketBusiness::EVENT_USER_SEND_MSG:
				$serviceId = $eventData["service_id"];
				$message = $eventData["message"];
				$socketUtil = new SocketUtil();
				$serviceId = $socketUtil->buildServiceId($serviceId);
				$clientIdList = $socketUtil->getClientIdByUid($serviceId);
				if ($clientIdList) {
					$data = [
						"event_id"   => SocketBusiness::EVENT_SOCKET_SEND_MSG,
						"event_data" => [
							"message" => $message
						]
					];
					Gateway::sendToClient($clientIdList[0], \json_encode($data));
				}
				break;
			case SocketBusiness::EVENT_SERVICE_SEND_MSG:
				$userClientId = $eventData["client_id"];
				$message = $eventData["message"];
				$isOnline = Gateway::isOnline($userClientId);
				if ($isOnline) {
					$socketUtil = new SocketUtil();
					$serviceId = $socketUtil->getUidByClientId($userClientId);
					$serviceId = $socketUtil->computeServiceId($serviceId);
					$data = [
						"event_id"   => SocketBusiness::EVENT_SOCKET_SEND_MSG,
						"event_data" => [
							"service_id" => $serviceId,
							"message"    => $message
						]
					];
					Gateway::sendToClient($userClientId, \json_encode($data));
				}
				break;
		}
	}

	/**
	 * 当客户端断开连接时
	 * @param $clientId
	 */
	public static function onClose($clientId) {
		file_put_contents("/data/events.log", "close", FILE_APPEND);

		//       $socketUtil=new SocketUtil();
		//       $socketUtil->unBindUid($clientId);
	}
}
