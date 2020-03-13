<?php
namespace Lib\Views;

/**
 * Mustache Helper
 * usage: {{#helper.stringify}} str {{/helper.stringify}}
 */
class Helper
{
    /*
     * 对象转换成字符串
     */
    public function stringify()
    {
        return function($val) {
            return json_encode($val);
        };
    }

    /*
     * 转大写
     */
    public function uppercase()
    {
        return function($val) {
            return strtoupper($val);
        };
    }
}