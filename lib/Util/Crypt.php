<?php
namespace Lib\Util;

/*
 * 简单的加密/解密(AES)算法
 */
class Crypt
{
    const METHOD = 'AES-256-CBC';

    public static function encrypt($data, $key = 'salt')
    {
        if (mb_strlen($key, '8bit') !== 32) {
            //throw new Exception("Needs a 256-bit key!");
        }

        $ivSize = openssl_cipher_iv_length(self::METHOD);
        $iv = openssl_random_pseudo_bytes($ivSize);

        $result = $iv . openssl_encrypt($data, self::METHOD, $key, OPENSSL_RAW_DATA, $iv);
        return CommonUtil::base64_url_encode($result);
    }

    public static function decrypt($cipher, $key = 'salt')
    {
        if (mb_strlen($key, '8bit') !== 32) {
            //throw new Exception("Needs a 256-bit key!");
        }

        $cipher = CommonUtil::base64_url_decode($cipher);

        $ivSize = openssl_cipher_iv_length(self::METHOD);
        $iv = mb_substr($cipher, 0, $ivSize, '8bit');

        $cipher = mb_substr($cipher, $ivSize, null, '8bit');
        return openssl_decrypt($cipher, self::METHOD, $key, OPENSSL_RAW_DATA, $iv);
    }
}