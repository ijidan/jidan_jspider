<?php
namespace Lib\Util;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class EmailUtil
 * @package Lib\Util
 */
class FileUtil {
	/**
	 * 递归遍历文件夹
	 * @param $dir
	 */
	public static function foreachDir($dir){
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
		foreach ( $files as $file ) {
			echo $file->__toString()."\r\n";
		}
	}
}