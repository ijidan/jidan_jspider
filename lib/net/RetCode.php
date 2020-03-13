<?php

namespace Lib\Net;

/**
 * 返回码
 * Class RetCode
 * @package Lib\Net
 */
abstract class RetCode {
    const SUCCESS = 0;  //成功
    const UNKNOWN = 1;  //未知错误
    const REQ_FAIL = 2; //请求失败
    const JSON_PARSE_FAIL = 3;  //json解析失败
    const RESPONSE_EMPTY = 4;   //返回结果为空
}