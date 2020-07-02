<?php

namespace Lib\Ffmpeg;

use Alchemy\BinaryDriver\Listeners\DebugListener as DebugListener;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Filters\Video\ExtractMultipleFramesFilter;
use FFMpeg\Format\Video\X264;
use FFMpeg\Media\Video;
use getID3;
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
	public function __construct($file, array $config = [],OutputInterface $output=null) {
		$this->file = $file;
		$this->output=$output;
		if ($config) {
			$this->config = array_merge($this->config, $config);
		}
		$ffm = FFMpeg::create($this->config);
		$ffm->getFFMpegDriver()->listen(new DebugListener());
		$ffm->getFFMpegDriver()->on('debug', function ($message) {
			echo $message."\n";
		});
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
		$format=new X264('libfdk_aac');
		$format->setAudioCodec("libmp3lame");
		$this->video->filters()->extractMultipleFrames($map[$everySecond], $destImageFolder)->synchronize();
		$this->video->save($format, 'libfdk_aac');
	}
	/**
	 * 裁剪
	 */
	public function clip(){
		$getID3=new getID3();
		$fileInfo=$getID3->analyze($this->file);
		//文件名
		$fileName=$fileInfo['filename'];
		$fileFormat=$fileInfo['fileformat'];
		$fileBaseName=str_replace('.'.$fileFormat,'',$fileName);

		$fileSize=$fileInfo['filesize'];
		$playtimeSeconds=$fileInfo['playtime_seconds'];

		//文件数量
		$fileSizeMB=intval(ceil($fileSize/1024/1024));
		$fileChunkSizeMB=30;
		$fileChunkNum=intval(ceil($fileSizeMB/$fileChunkSizeMB));
		//计算时长
		$playTimeChunk=intval(ceil($playtimeSeconds/$fileChunkNum));
		//分隔视频
		for($i=1;$i<=1;$i++){
			$startSeconds=($i-1)*$playTimeChunk;
			$this->video->filters()->clip(TimeCode::fromSeconds($startSeconds), TimeCode::fromSeconds($playTimeChunk));
			$format = new X264('libfdk_aac');
			if($this->output){
				$consoleUtil=new ConsoleUtil($this->output);
				$format->on('progress', function ($video, $format, $percentage) use($consoleUtil) {
					$consoleUtil->info("$percentage % done.");
				});
			}
			$chunkFileName="{$fileBaseName}_{$i}.{$fileFormat}";
			$this->video->save($format, BASE_DIR.'/'.$chunkFileName);
		}
		return true;
	}
}