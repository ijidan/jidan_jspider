<?php
namespace Tools\Events;

use Lib\BaseLogger;
use Lib\Util\Config;
use Lib\Util\EmailUtil;

class BaseEvent {
	protected $logger;

	public function __construct($loggerName = '') {
		//设置logger
		$this->logger = BaseLogger::instance('events');

		//捕获异常
		set_exception_handler(array($this, 'exceptionHandler'));

		//捕获错误
		set_error_handler(array($this, 'errorHandler'));
	}

	public function exceptionHandler($exception) {
		$context = [
			'file' => $exception->getFile(),
			'line' => $exception->getLine(),
			'code' => $exception->getCode()
		];
		$this->logger->addError($exception->getMessage(), $context);
		$this->sendEmail($exception->getMessage(), $context);
	}

	public function errorHandler($errNo, $errStr, $errFile, $errLine) {
		$context = [
			'file' => $errFile,
			'line' => $errLine,
			'code' => $errNo
		];

		switch ($errNo) {
			case E_PARSE:
			case E_WARNING:
			case E_USER_WARNING:
				$this->logger->addWarning($errStr, $context);
				break;
			case E_ERROR:
			case E_USER_ERROR:
				$this->logger->addError($errStr, $context);
				$this->sendEmail($errStr, $context);
				break;
		}
	}

	/**
	 * 发送邮件
	 * @param $title
	 * @param array $context
	 */
	protected function sendEmail($title, array $context = []) {
		$msg = '';
		foreach ($context as $key => $val) {
			$msg .= "{$key} : {$val}<br/>";
		}
		$emailConfig = Config::loadConfig('email');
		if ($emailConfig['email_notify']['enable'] && $emailConfig['email_notify']['list']) {
			$emailUtil = new EmailUtil($emailConfig['common']);
			$emailUtil->multiSend($emailConfig['email_notify']['list'], $title, $msg);
		}
	}
}