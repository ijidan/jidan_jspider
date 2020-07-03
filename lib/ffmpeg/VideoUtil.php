<?php

namespace Lib\Ffmpeg;

use Alchemy\BinaryDriver\Listeners\DebugListener as DebugListener;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Filters\Video\ExtractMultipleFramesFilter;
use FFMpeg\Format\Video\X264;
use FFMpeg\Media\Video;
use getID3;
use getid3_exception;
use InvalidArgumentException;
use Lib\Util\ConsoleUtil;
use Symfony\Component\Console\Output\OutputInterface;


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
		'timeout'          => 0, // The timeout for the underlying process
		'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
	);

	/**
	 * @var Video $video
	 */
	private $video;

	/**
	 * @var $output
	 */
	private $output;

	/**
	 * 构造函数
	 * VideoUtil constructor.
	 * @param $file
	 * @param array $config
	 * @param OutputInterface|null $output
	 */
	public function __construct($file, array $config = [], OutputInterface $output = null) {
		$this->file = $file;
		$this->output = $output;
		if ($config) {
			$this->config = array_merge($this->config, $config);
		}
		$ffm = $this->buildFFM();
		$this->video = $ffm->open($file);
	}

	/**
	 * 构造FFM
	 * @return FFMpeg
	 */
	private function buildFFM() {
		$ffm = FFMpeg::create($this->config);
		$ffm->getFFMpegDriver()->listen(new DebugListener());
		if ($this->output) {
			$consoleUtil = new ConsoleUtil($this->output);
			$ffm->getFFMpegDriver()->on('debug', function ($message) use ($consoleUtil) {
				$consoleUtil->info($message);
			});
		}
		return $ffm;
	}

	/**
	 * 构造 format
	 * @return X264
	 */
	private function buildFormat() {
		$format = new X264('libfdk_aac');
		if ($this->output) {
			$consoleUtil = new ConsoleUtil($this->output);
			$format->on('progress', function ($video, $format, $percentage) use ($consoleUtil) {
				$consoleUtil->info("$percentage % done.");
			});
		}
		return $format;
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
		$format = new X264('libfdk_aac');
		$format->setAudioCodec("libmp3lame");
		$this->video->filters()->extractMultipleFrames($map[$everySecond], $destImageFolder)->synchronize();
		$this->video->save($format, 'libfdk_aac');
	}

	/**
	 * 裁剪
	 * @return bool
	 * @throws \getid3_exception
	 */
	public function clip() {
		//文件信息
		$fileInfo = $this->getFileInfo();
		$fileBaseName = $fileInfo['basename'];
		$fileFormat = $fileInfo['fileformat'];
		$fileSize = $fileInfo['filesize'];
		$playtimeSeconds = $fileInfo['playtime_seconds'];

		//文件数量
		$fileSizeMB = $fileSize / 1024 / 1024;
		$fileChunkSizeMB = 33;
		$fileChunkNum = intval(ceil($fileSizeMB / $fileChunkSizeMB));
		//计算时长
		$playTimeChunk = intval(ceil($playtimeSeconds / $fileChunkNum));

		//分隔视频
		for ($i = 1; $i <= $fileChunkNum; $i++) {
			$startSeconds = ($i - 1) * $playTimeChunk;
			$duration = $i == $fileChunkNum ? null : TimeCode::fromSeconds($playTimeChunk);
			$this->video->filters()->clip(TimeCode::fromSeconds($startSeconds), $duration);
			//添加水印
			$watermarkPath=$this->getWatermarkPath();
			$pos=$this->getWatermarkPosition();
			$this->video->filters()->watermark($watermarkPath, $pos);
			//格式
			$format = $this->buildFormat();
			$chunkFileName = "{$fileBaseName}_{$i}.{$fileFormat}";
			$destFile = BASE_DIR . '/video/' . $chunkFileName;
			$this->video->save($format, $destFile);
		}
		return true;
	}

	/**
	 * 添加水印
	 * @return bool
	 * @throws \getid3_exception
	 */
	public function watermark(){
		return $this->addWatermark();
	}

	/**
	 * 获取文件信息
	 * @param null $file
	 * @return array
	 * @throws \getid3_exception
	 */
	private function getFileInfo($file = null) {
		$getID3 = new getID3();
		$fileInfo = $getID3->analyze($file ?: $this->file);
		$fileName = $fileInfo['filename'];
		$fileFormat = $fileInfo['fileformat'];
		$fileBaseName = str_replace('.' . $fileFormat, '', $fileName);
		return array_merge($fileInfo, ['basename' => $fileBaseName]);
	}

	/**
	 * 添加水印
	 * @param null $file
	 * @return bool
	 * @throws getid3_exception
	 */
	private function addWatermark($file = null) {
		$watermarkPath=$this->getWatermarkPath();
		$pos=$this->getWatermarkPosition();
		if ($file) {
			$ffm = $this->buildFFM();
			$video = $ffm->open($file);
		} else {
			$video = $this->video;
		}
		$video->filters()->watermark($watermarkPath, $pos);
		//文件信息
		$fileInfo = $this->getFileInfo($file);
		$fileBaseName = $fileInfo['basename'];
		$fileFormat = $fileInfo['fileformat'];
		//目标文件
		$destFile = BASE_DIR . '/video/' . "{$fileBaseName}_watermark.{$fileFormat}";
		//保存
		$format = $this->buildFormat();
		$video->save($format, $destFile);
		return true;
	}

	/**
	 * 水印路径
	 * @return string
	 */
	private function getWatermarkPath(){
		$watermarkPath = BASE_DIR.'/kz_logo_v2.png';
		return $watermarkPath;
	}

	/**
	 * 获取定位
	 * @return array
	 */
	private function getWatermarkPosition(){
		$absolute = ['x' => 50, 'y' => 100];
		$relative = [
			'position' => 'relative',
			'top'   => 20,
			'right'    => 20
		];
		return $relative;
	}
}