<?php

namespace Lib\Ffmpeg;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Media\Video;
use InvalidArgumentException;



/**
 * 视频工具类
 * Class VideoUtil
 * @package Lib\Net
 */
class VideoUtil {

	/**
	 * 文件
	 * @var
	 */
	private $file;

	/**
	 * 配置
	 * @var array
	 */
	private $config = array(
		'ffmpeg.binaries' => '/usr/local/bin/ffmpeg',
		'ffprobe.binaries' => '/usr/local/bin/ffprobe'
	);

	/**
	 * @var Video $video
	 */
	private $video;

	/**
	 * 构造函数
	 * VideoUtil constructor.
	 * @param $file
	 * @param array $config
	 */
	public function __construct($file,array $config = []) {
		$this->file=$file;
		if($config){
			$this->config=array_merge($this->config,$config);
		}
		$ffm=FFMpeg::create($this->config);
		$this->video=$ffm->open($file);
	}

	/**
	 * 提取图片
	 * @param $second
	 * @param $destImageFile
	 */
	public function extractImage($second,$destImageFile){
		if($second<=0){
			throw new InvalidArgumentException('描述必须大于0');
		}
		$frame=$this->video->frame(TimeCode::fromSeconds($second));
		$frame->save($destImageFile);
	}
}