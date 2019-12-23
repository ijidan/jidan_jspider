<?php
/**
 * 图像处理exif_imagetype函数需要exif扩展支持
 * 在未安装的时候用其它方式代替
 */
if (!function_exists("exif_imagetype")) {
    function exif_imagetype($file)
    {
        list($width, $height, $type) = @getimagesize($file);
        return $type;
    }
}