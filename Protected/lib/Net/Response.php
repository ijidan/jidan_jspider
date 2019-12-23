<?php

namespace Lib\Net;

/**
 * 响应类
 * Class Response
 * @package YWJLib\net
 */
class Response extends RetCode {
    
    private $response_string = '';
    public $code = self::UNKNOWN;
    public $message = '';
    public $prompt = '';
    public $data = null;
    
    const HTTP_STATUS_CODE_SUCCESS = 200; //请求成功返回码
    
    /**
     * 构造函数
     * Response constructor.
     * @param int $code 返回码
     * @param string $message 信息提示
     * @param null $data 数据
     * @param string $prompt
     */
    public function __construct($code = self::SUCCESS, $message = '', $data = null, $prompt = '')
    {
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
        $this->prompt = $prompt;
    }
    
    /**
     * 是否成功
     * @return bool
     */
    public function success()
    {
        return $this->code == self::SUCCESS;
    }
    
    /**
     * 是否失败
     * @return bool
     */
    public function fail()
    {
        return $this->code != self::SUCCESS;
    }
    
    /**
     * 设置数据
     * @param $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
    
    /**
     * 获取数据
     * @return null
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * 获取返回码
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }
    
    /**
     * 获取信息
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
    
    /**
     * 获取提示
     * @return string
     */
    public function getPrompt()
    {
        return $this->prompt;
    }
    
    public function __toString()
    {
        return $this->response_string;
    }
}