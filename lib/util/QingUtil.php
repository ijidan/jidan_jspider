<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/18
 */

namespace Lib\Util;

use DirectoryIterator;
use QingStor\SDK\Service\QingStor;
use QingStor\SDK\Config as QingConfig;

/**
 * 青云工具类
 * Class QingUtil
 * @package Lib\Util
 */
class QingUtil
{
    const PREFIX = 'assets/'; //cdn目录前缀

    private $bucket;

    public function __construct()
    {
        $struct = Config::loadConfig('struct');
        $qyConfig = $struct['qing_cloud'];
        $config = new QingConfig($qyConfig['qy_access_key_id'],$qyConfig['qy_secret_access_key']);
        $service = new QingStor($config);
        $this->bucket = $service->Bucket($qyConfig['bucket'], $qyConfig['zone']);
    }

    public function uploadFile($sourceFile, $targetFile, $contentType)
    {
        if (!\file_exists($sourceFile) || !\is_file($sourceFile)) {
            throw new \InvalidArgumentException("${$sourceFile} not found");
        }
        return $this->bucket->putObject($targetFile, [
            'body' => file_get_contents($sourceFile),
            'Content-Type' => $contentType,
        ]);
    }

    /**
     * 删除一个对象
     * @param $objectKey
     */
    public function deleteObject($objectKey)
    {
        $this->bucket->deleteObject($objectKey);
    }

    /**
     * 列出所有的Key
     * @return mixed
     */
    public function listAllObjectKeys()
    {
        $objectList = $this->bucket->listObjects();
        $keys = $objectList->keys;
        if (!is_array($keys) || !$keys) {
            return [];
        }
        return array_column($keys, "key");
    }
}