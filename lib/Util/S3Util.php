<?php
/**
 * User: paolo
 * Date: 2016/6/22
 */
namespace Lib\Util;

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;

class S3Util
{
    private $bucket = '';
    private $prefix = 'assets/';
    private $s3Client;

    public function __construct()
    {
        $structConf = Config::loadConfig('struct');
        $s3Conf = $structConf['s3_client'];

        $this->bucket = $s3Conf['bucket'];
        $credentials = new Credentials($s3Conf['key'], $s3Conf['secret']);
        $config = [
            'version' => 'latest',
            'region' => $s3Conf['region'],
            'credentials' => $credentials
        ];
        $this->s3Client = new S3Client($config);
    }

    /*
     * 上传文件
     */
    public function uploadFile($sourceFile, $targetFile, $contentType)
    {
        return $this->s3Client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $this->prefix . $targetFile,
            'SourceFile' => $sourceFile,
            'ContentType' => $contentType,
            'ACL' => 'public-read',
        ]);
    }

    /**
     * 获取文件MIME-Type
     * @param $filename
     * @return String
     */
    public static function getMIMEType($filename)
    {
        /*
        $finfo = new \finfo();
        return $finfo->file($filename, FILEINFO_MIME_TYPE);
        */
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $filename);
    }

    /*
     * 上传目录
     */
    public function uploadDir($fromDir)
    {
        return $this->s3Client->uploadDirectory($fromDir, $this->bucket, $this->prefix);
    }

    /*
     * 删除单个文件
     */
    public function deleteFile($key)
    {
        return $this->s3Client->deleteObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
        ]);
    }

    /*
     * 删除多个文件
     */
    public function deleteFiles($keys)
    {
        return $this->s3Client->deleteObjects([
            'Bucket' => $this->bucket,
            'Objects' => array_map(function($key) {
                return ['Key' => $key];
            }, $keys)
        ]);
    }
}