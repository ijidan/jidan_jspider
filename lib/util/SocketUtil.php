<?php

namespace Lib\Util;

use App\Models\SocketBusiness;
use GatewayWorker\Lib\Gateway;
use Lib\MC;


/**
 * SOCKET
 * Class SocketBusiness
 * @package App\Models
 */
class SocketUtil {

	/**
	 * '构造函数
	 * SocketBusiness constructor.
	 */
	public function __construct() {
		$registerAddress = Config::getConfigItem("socket/register_address");
		Gateway::$registerAddress = $registerAddress;
	}

	/**
	 * 绑定用户
	 * @param $clientId
	 * @param $userId
	 * @return bool
	 */
	public function bindUid($clientId, $userId) {
		// client_id与uid绑定
		Gateway::bindUid($clientId, $userId);
		$this->bindClientToMC($clientId, $userId);
	}

	/**
	 * 解绑
	 * @param $clientId
	 */
	public function unBindUid($clientId) {
		$userId = $this->getUidByClientId($clientId);
		if ($userId) {
			Gateway::unbindUid($userId, $clientId);
		}
		$this->unbindClientFromMC($clientId);
		$this->clearPushByClientId($clientId);
	}

	/**
	 * 获取所有客户端
	 * @return array
	 */
	public function getAllClientIdList() {
		$allClientInfo = Gateway::getAllClientInfo();
		return $allClientInfo ? array_keys($allClientInfo) : [];
	}

	/**
	 * 获取所有在线用户
	 * @return array
	 */
	public function getAllOnlineUser() {
		$allClientInfo = Gateway::getAllClientInfo();
		if ($allClientInfo) {
			$userIdClientIdMap = [];
			foreach ($allClientInfo as $clientId => $clientInfo) {
				$userId = $this->getUidByClientId($clientId);
				if ($userId) {
					$userIdClientIdMap[$userId] = $clientId;
				}
			}
			return $userIdClientIdMap;
		}
		return [];
	}

	/**
	 * 获取所有在线用户的UID
	 * @return array|int
	 */
	public function getAllOnlineUserIdList() {
		$userIdClientIdMap = $this->getAllOnlineUser();
		if ($userIdClientIdMap) {
			return array_keys($userIdClientIdMap);
		}
		return [];
	}


	/**
	 * 绑定到MC
	 * @param $clientId
	 * @param $userId
	 */
	private function bindClientToMC($clientId, $userId) {
		$key = $this->getMCBindKey($clientId);
		MC::set($key, $userId);
	}

	/**
	 * 从MC解绑
	 * @param $clientId
	 */
	private function unbindClientFromMC($clientId) {
		$key = $this->getMCBindKey($clientId);
		MC::delete($key);
	}

	/**
	 * 获取用户ID
	 * @param $clientId
	 * @return int
	 */
	public function getUidByClientId($clientId) {
		$key = $this->getMCBindKey($clientId);
		$userId = MC::get($key);
		return intval($userId);
	}


	/**
	 * 替换推送
	 * @param $clientId
	 * @param string $pushIdStr
	 */
	public function setPushByClientId($clientId, $pushIdStr = "") {
		MC::set($clientId, $pushIdStr);
		if (strpos($pushIdStr, SocketBusiness::PUSH_TYPE_ROULETTE) !== false) {
			SocketBusiness::pushRouletteMessage([$clientId]);
			SocketBusiness::pushRouletteStatMessage([$clientId]);
		}
	}

	/**
	 * 清除推送
	 * @param $clientId
	 */
	public function clearPushByClientId($clientId) {
		MC::delete($clientId);
	}

	/**
	 * 是否推送
	 * @param $clientId
	 * @param $pushId
	 * @return bool
	 */
	public function isPushByClientId($clientId, $pushId) {
		$pushStr = MC::get($clientId);
		if (!$pushStr) {
			return false;
		}
		return strpos($pushStr . "", $pushId . "") !== false;
	}

	/**
	 * 获取用户
	 * @param $group
	 * @return array
	 */
	public function getUserIdListByGroup($group) {
		$clientIdList = $this->getClientIdListByGroup($group);
		if (!$clientIdList) {
			return [];
		}
		$userIdList = [];
		foreach ($clientIdList as $clientId) {
			$userId = $this->getUidByClientId($clientId);
			array_push($userIdList, $userId);
		}
		return $userIdList;
	}

	/**
	 * 获取群组成员
	 * @param $group
	 * @return array
	 */
	public function getClientIdListByGroup($group) {
		$groupSessionList = Gateway::getClientSessionsByGroup($group);
		if (!$groupSessionList) {
			return [];
		}
		return array_keys($groupSessionList);
	}

	/**
	 * 获取绑定的KEY
	 * @param $clientId
	 * @return string
	 */
	private function getMCBindKey($clientId) {
		return "bind_" . $clientId;
	}

	/**
	 * 构造客服ID
	 * @param $serviceId
	 * @return string
	 */
	public function buildServiceId($serviceId){
		return "service_".$serviceId;
	}

	/**
	 * 计算ID
	 * @param $buildServiceId
	 * @return mixed
	 */
	public function computeServiceId($buildServiceId){
		return str_replace("service_","",$buildServiceId);
	}


	/**
	 * =====================================重载=================================================
	 * 向所有客户端连接(或者 client_id_array 指定的客户端连接)广播消息
	 * @param string $message 向客户端发送的消息
	 * @param array $client_id_array 客户端 id 数组
	 * @param array $exclude_client_id 不给这些client_id发
	 * @param bool $raw 是否发送原始数据（即不调用gateway的协议的encode方法）
	 * @return void
	 */
	public function sendToAll($message, $client_id_array = null, $exclude_client_id = null, $raw = false) {
		Gateway::sendToAll($message, $client_id_array, $exclude_client_id, $raw);
	}

	/**
	 * 向某个客户端连接发消息
	 * @param int $client_id
	 * @param string $message
	 * @return bool
	 */
	public function sendToClient($client_id, $message) {
		return Gateway::sendToClient($client_id, $message);
	}

	/**
	 * 向当前客户端连接发送消息
	 * @param string $message
	 * @return bool
	 */
	public function sendToCurrentClient($message) {
		return Gateway::sendToCurrentClient($message);
	}

	/**
	 * 判断某个uid是否在线
	 * @param string $uid
	 * @return int 0|1
	 */
	public function isUidOnline($uid) {
		return Gateway::isUidOnline($uid);
	}

	/**
	 * 判断某个客户端连接是否在线
	 * @param int $client_id
	 * @return int 0|1
	 */
	public function isOnline($client_id) {
		return Gateway::isOnline($client_id);
	}

	/**
	 * 获取所有在线用户的session，client_id为 key
	 * @param string $group
	 * @return array
	 */
	public function getAllClientInfo($group = null) {
		return Gateway::getAllClientInfo($group);
	}

	/**
	 * 获取所有在线用户的session，client_id为 key
	 * @param string $group
	 * @return array
	 */
	public function getAllClientSessions($group = null) {
		return Gateway::getAllClientSessions($group);
	}

	/**
	 * 获取某个组的连接信息
	 * @param string $group
	 * @return array
	 */
	public function getClientInfoByGroup($group) {
		return Gateway::getClientInfoByGroup($group);
	}

	/**
	 * 获取某个组的连接信息
	 * @param string $group
	 * @return array
	 */
	public function getClientSessionsByGroup($group) {
		return Gateway::getClientSessionsByGroup($group);
	}

	/**
	 * 获取所有连接数
	 * @return int
	 */
	public function getAllClientCount() {
		return Gateway::getAllClientCount();
	}

	/**
	 * 获取某个组的在线连接数
	 * @param string $group
	 * @return int
	 */
	public function getClientCountByGroup($group = '') {
		return Gateway::getClientCountByGroup($group);
	}

	/**
	 * 获取与 uid 绑定的 client_id 列表
	 * @param string $uid
	 * @return array
	 */
	public function getClientIdByUid($uid) {
		return Gateway::getClientIdByUid($uid);
	}


	/**
	 * 关闭某个客户端
	 * @param int $client_id
	 * @return bool
	 */
	public function closeClient($client_id) {
		return Gateway::closeClient($client_id);
	}

	/**
	 * 踢掉当前客户端
	 * @return bool
	 * @throws Exception
	 */
	public function closeCurrentClient() {
		return Gateway::closeCurrentClient();
	}


	/**
	 * 将 client_id 加入组
	 * @param int $client_id
	 * @param int|string $group
	 * @return bool
	 */
	public function joinGroup($client_id, $group) {
		return Gateway::joinGroup($client_id, $group);
	}

	/**
	 * 将 client_id 离开组
	 * @param int $client_id
	 * @param int|string $group
	 * @return bool
	 */
	public function leaveGroup($client_id, $group) {
		return Gateway::leaveGroup($client_id, $group);
	}

	/**
	 * 向所有 uid 发送
	 * @param int|string|array $uid
	 * @param string $message
	 */
	public function sendToUid($uid, $message) {
		Gateway::sendToUid($uid, $message);
	}

	/**
	 * 向 group 发送
	 * @param int|string|array $group 组（不允许是 0 '0' false null array()等为空的值）
	 * @param string $message 消息
	 * @param array $exclude_client_id 不给这些client_id发
	 * @param bool $raw 发送原始数据（即不调用gateway的协议的encode方法）
	 */
	public function sendToGroup($group, $message, $exclude_client_id = null, $raw = false) {
		Gateway::sendToGroup($group, $message, $exclude_client_id, $raw = false);
	}
}