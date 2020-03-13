<?php

namespace Lib\Http;

/**
 * UA
 * Class Request
 * @package Lib\Net
 */
class UserAgent {

	/** @var UserAgent */
	private static $instance;

	/** @var array|mixed */
	private $userAgentList = [];

	/**
	 * UserAgent constructor.
	 */
	private function __construct() {
		$file = __DIR__ . DIRECTORY_SEPARATOR . 'fake_useragent.json';
		$content = file_get_contents($file);
		$this->userAgentList = \json_decode($content, true);
	}

	/**
	 * 单例
	 * @param bool $forceRecreate
	 * @return UserAgent
	 */
	protected static function getInstance($forceRecreate = false) {
		if (!(self::$instance instanceof self) || $forceRecreate) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * random
	 * @return mixed
	 */
	public static function random() {
		$ins = self::getInstance();
		$uaList = $ins->userAgentList['randomize'];
		$idx = array_rand($uaList, 1);
		$browser = $uaList[$idx];
		return self::getRandomUa($browser);
	}

	/**
	 * chrome
	 * @return UserAgent
	 */
	public static function chrome() {
		return self::getRandomUa('chrome');
	}

	/**
	 * opera
	 * @return UserAgent
	 */
	public static function opera() {
		return self::getRandomUa('opera');

	}

	/**
	 * firefox
	 * @return UserAgent
	 */
	public static function firefox() {
		return self::getRandomUa('firefox');

	}

	/**
	 * ie
	 * @return UserAgent
	 */
	public static function ie() {
		return self::getRandomUa('internetexplorer');

	}

	/**
	 * safari
	 * @return UserAgent
	 */
	public static function safari() {
		return self::getRandomUa('safari');

	}

	/**
	 * 随机取值
	 * @param string $browser
	 * @return mixed
	 */
	private static function getRandomUa($browser = 'chrome') {
		$ins = self::getInstance();
		$uaList = $ins->userAgentList['browsers'][$browser];
		$idx = array_rand($uaList, 1);
		return $uaList[$idx];

	}
}