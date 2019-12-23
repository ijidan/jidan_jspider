<?php
namespace Lib\Util;

use PHPMailer;

/**
 * Class EmailUtil
 * @package Lib\Util
 */
class EmailUtil {
    
    private $charSet = "UTF-8";
    
    private $SMTPHost = "";
    private $SMTPPort = 25;
    private $SMTPSecure = 'ssl';
    
    private $fromName = "";  //发件人名称
    private $fromAddress = ""; //发件人邮箱
    private $fromPassword = "";
    
    private $error;
    
    /**
     * 构造函数
     * EmailUtil constructor.
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $this->checkConfig($config);
        $this->SMTPHost = $config["smtp_host"];
        if (isset($config["smtp_port"])) {
            $this->SMTPPort = $config["smtp_port"];
        }
        if (isset($config['smtp_secure'])) {
            $this->SMTPSecure = $config['smtp_secure'];
        }

        if (isset($config["from_name"])) {
            $this->fromName = $config["from_name"];
        } else {
            $address = explode("@", $config["from_address"]);
            $this->fromName = $address[0];
        }
        $this->fromAddress = $config["from_address"];
        $this->fromPassword = $config["from_password"];
        if (isset($config["charset"])) {
            $this->charSet = $config["charset"];
        }
    }

	/**
	 * 发送邮件
	 * @param string $toAddress 收件人
	 * @param string $subject 主题
	 * @param string $content 内容
	 * @param string $toAddressName 收件人名称
	 * @return bool
	 * @throws \phpmailerException
	 */
    public function send($toAddress, $subject, $content, $toAddressName = "")
    {
        return $this->multiSend(array($toAddress), $subject, $content, array($toAddressName));
    }

	/**
	 *批量发送邮件
	 * @param array $toAddressList
	 * @param $subject
	 * @param $content
	 * @param array $toAddressNameList
	 * @return bool
	 * @throws \phpmailerException
	 */
    public function multiSend(array $toAddressList, $subject, $content, array $toAddressNameList = array())
    {
        $mailer = $this->buildMailer();
        foreach ($toAddressList as $idx => $toAddress) {
            $toAddressName = isset($toAddressNameList[$idx]) ? $toAddressNameList[$idx] : "";
            if(!filter_var($toAddress,FILTER_VALIDATE_EMAIL)){
                throw new \InvalidArgumentException("email error");
            }
            $mailer->addAddress($toAddress, $toAddressName);
        }
        $mailer->Subject = $subject;
        $mailer->Body = $content;
        if ($mailer->send()) {
            $this->error = "";
            return true;
        }
        $this->error = $mailer->ErrorInfo;
        return false;
    }
    
    /**
     * 配置邮件发送对象
     * @return PHPMailer
     */
    private function buildMailer()
    {
        $mailer = new PHPMailer();
        $mailer->isSMTP();
        $mailer->SMTPAuth = true;
        $mailer->Host = $this->SMTPHost;
        $mailer->SMTPSecure = $this->SMTPSecure;
        $mailer->Port = $this->SMTPPort;
        $mailer->CharSet = $this->charSet;
        $mailer->FromName = $this->fromName;
        $mailer->Username = $this->fromAddress;
        $mailer->Password = $this->fromPassword;
        $mailer->From = $this->fromAddress;
        $mailer->isHTML(true);
        return $mailer;
    }
    
    /**
     * 检查邮件配置
     * @param array $config
     */
    private function checkConfig(array $config)
    {
        $mustConfig = [
            "smtp_host",
            "from_address",
            "from_password"
        ];
        foreach ($mustConfig as $val) {
            if (!isset($config[$val]) || $config[$val] == '') {
                throw new \InvalidArgumentException("param error");
            }
        }
    }
    
    /**
     * 获取错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }
    
}