<?php

namespace Lib\Ffmpeg;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\FFMpeg;
use FFMpeg\Filters\Video\ExtractMultipleFramesFilter;
use FFMpeg\Filters\Video\ResizeFilter;
use FFMpeg\Format\Video\X264;
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
		'ffmpeg.binaries'  => '/usr/local/bin/ffmpeg',
		'ffprobe.binaries' => '/usr/local/bin/ffprobe',
		'timeout'          => 3600*24, // The timeout for the underlying process
		'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
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
	public function __construct($file, array $config = []) {
		$this->file = $file;
		if ($config) {
			$this->config = array_merge($this->config, $config);
		}
		$ffm = FFMpeg::create($this->config);
		$this->video = $ffm->open($file);
	}

	/**
	 * 提取图片
	 * @param $second
	 * @param $destImageFile
	 */
	public function extractImage($second, $destImageFile) {
		if ($second <= 0) {
			throw new InvalidArgumentException('描述必须大于0');
		}
		$frame = $this->video->frame(TimeCode::fromSeconds($second));
		$frame->save($destImageFile);
	}

	/**
	 * 提取多张图片
	 * @param $everySecond
	 * @param $destImageFolder
	 * @param $destImageFilePath
	 */
	public function extractImages($everySecond, $destImageFolder, $destImageFilePath) {
		$map = [
			1  => ExtractMultipleFramesFilter::FRAMERATE_EVERY_SEC,
			2  => ExtractMultipleFramesFilter::FRAMERATE_EVERY_2SEC,
			5  => ExtractMultipleFramesFilter::FRAMERATE_EVERY_5SEC,
			10 => ExtractMultipleFramesFilter::FRAMERATE_EVERY_10SEC,
			30 => ExtractMultipleFramesFilter::FRAMERATE_EVERY_30SEC,
			60 => ExtractMultipleFramesFilter::FRAMERATE_EVERY_60SEC
		];
		$format=new X264();
		$format->setAudioCodec("libmp3lame");
		$this->video->filters()->extractMultipleFrames($map[$everySecond], $destImageFolder)->synchronize();
		$this->video->save($format, $destImageFilePath);
	}

	/**
	 * 裁剪
	 */
	public function clip(){
		$this->video->filters()->clip(TimeCode::fromSeconds(30), TimeCode::fromSeconds(15));
		$this->video->filters()->resize(new Dimension(320, 240), ResizeFilter::RESIZEMODE_INSET, true);
		$this->video->save(new X264(), BASE_DIR.'/cut_and_resize.mp4');
	}
}