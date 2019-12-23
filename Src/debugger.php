<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
/**
 * 调试函数，支持中断，非中断情况可调用多次，支持多个变量
 * 用法：dump($var1,$var2...); 中断用法：dump($var1,$var2...,1)
 */
if (!function_exists("dump")) {
	function dump() {
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
 * 格式化打印函数，支持中断
 */
if (!function_exists("printr")) {
	function printr() {
		$args = func_get_args();
		$last = array_slice($args, -1, 1);
		$die = $last[0] === 1;
		if ($die) {
			$args = array_slice($args, 0, -1);
		}
		echo "<pre>";
		foreach ($args as $arg) {
			call_user_func_array('print_r', array($arg));
		}
		$info = debug_backtrace();
		echo '<br><br>';
		echo $info[0]['file'] . ' [' . $info[0]['line'] . '] </pre>';
		if ($die) {
			die;
		}
	}
}

/**
 * 打印执行过程
 */
if (!function_exists("trace")) {
	function trace() {
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
	function methods($obj) {
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
	function vars($obj) {
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
	function pr() {
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
/**
 *调试函数
 */
if (!function_exists("trace")) {
	function trace() {
		$args = func_get_args();
		$last = array_slice($args, -1, 1);
		$die = $last[0] === 1;
		echo "\r\n\r\n" . '<pre style="background-color:#ddd; font-size:12px">' . "\r\n";
		debug_print_backtrace();
		if ($die) {
			die;
		}
	}
}
/**
 * 读取php文件，返回php54格式的（短格式）的字符串
 */
if (!function_exists("convertArraysToSquareBrackets")) {
	function convertArraysToSquareBrackets($file) {
		$code = file_get_contents($file);
		$out = '';
		$brackets = [];
		$tokens = token_get_all($code);
		for ($i = 0; $i < count($tokens); $i++) {
			$token = $tokens[$i];
			if ($token === '(') {
				$brackets[] = false;
			} elseif ($token === ')') {
				$token = array_pop($brackets) ? ']' : ')';
			} elseif (is_array($token) && $token[0] === T_ARRAY) {
				$a = $i + 1;
				if (isset($tokens[$a]) && $tokens[$a][0] === T_WHITESPACE) {
					$a++;
				}
				if (isset($tokens[$a]) && $tokens[$a] === '(') {
					$i = $a;
					$brackets[] = true;
					$token = '[';
				}
			}
			$out .= is_array($token) ? $token[1] : $token;
		}
		return $out;
	}
}
/**
 * 数组格式化成PHP54的ARRAY
 */
if (!function_exists("var_export54")) {
	function var_export54($var, $indent = "") {
		switch (gettype($var)) {
			case "string":
				return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
			case "array":
				$indexed = array_keys($var) === range(0, count($var) - 1);
				$r = [];
				foreach ($var as $key => $value) {
					$r[] = "$indent    " . ($indexed ? "" : var_export54($key) . " => ") . var_export54($value, "$indent    ");
				}
				return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
			case "boolean":
				return $var ? "TRUE" : "FALSE";
			default:
				return var_export($var, true);
		}
	}
}