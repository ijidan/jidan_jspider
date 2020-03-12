<?php
/**
 * 调试函数
 */
if (!function_exists("dump")) {
    function dump()
    {
        echo "\r\n\r\n" . '<pre style="background-color:#ddd; font-size:12px">' . "\r\n";
        $args = func_get_args();
        $last = array_slice($args, -1, 1);
        $die = $last[0] === 1;
        if ($die) {
            $args = array_slice($args, 0, -1);
        }
        if ($args) {
            call_user_func_array('var_dump', $args);
        }
        $info = debug_backtrace();
        echo $info[0]['file'] . ' [' . $info[0]['line'] . "] \r\n</pre>";
        if ($die) {
            die;
        }
    }
}
/**
 * 打印执行过程
 */
if (!function_exists("trace")) {
    function trace()
    {
        $args = func_get_args();
        $last = array_slice($args, -1, 1);
        $die = $last[0] === 1;
        echo "<pre>";
        debug_print_backtrace();
        echo "</pre>";
        $info = debug_backtrace();
        echo '<br><br>';
        echo $info[0]['file'] . ' [' . $info[0]['line'] . '] </pre>';
        if ($die) {
            die;
        }
    }
}
/**
 * 获取类或者对象的方法
 */
if (!function_exists("methods")) {
    function methods($obj)
    {
        if (is_object($obj)) {
            $class = new ReflectionClass($obj);
            $class = $class->getName();
        } else {
            $class = $obj;
        }
        $m = get_class_methods($class);
        return $m;
    }
}
/**
 * 获取类或者对象的属性
 */
if (!function_exists("vars")) {
    function vars($obj)
    {
        if (is_object($obj)) {
            $class = new ReflectionClass($obj);
            $class = $class->getName();
        } else {
            $class = $obj;
        }
        $v = get_class_vars($class);
        return $v;
    }
}
/**
 * 格式化输出print_r
 */
if (!function_exists("pr")) {
    function pr()
    {
        echo "\r\n\r\n" . '<pre style="background-color:#ddd; font-size:12px">' . "\r\n";
        $args = func_get_args();
        $last = array_slice($args, -1, 1);
        $die = $last[0] === 1;
        if ($die) {
            $args = array_slice($args, 0, -1);
        }
        if ($args) {
            foreach ($args as $arg) {
                print_r($arg);
                echo str_repeat('-', 50) . "\n";
            }
        }
        $info = debug_backtrace();
        echo $info[0]['file'] . ' [' . $info[0]['line'] . "] \r\n</pre>";
        if ($die) {
            die;
        }
    }
}