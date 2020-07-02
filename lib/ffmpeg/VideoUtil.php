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
		dump($fileInfo,1);
		$data=$this->video->getStreams()->all();
		pr($data,1);
		dump(get_class_methods(get_class($this->video)),1);
		$this->video->filters()->clip(TimeCode::fromSeconds(30), TimeCode::fromSeconds(15));
//		$this->video->filters()->resize(new Dimension(320, 240), ResizeFilter::RESIZEMODE_INSET, true);
		$format = new X264('libfdk_aac');
		if($this->output){
			$consoleUtil=new ConsoleUtil();
			$format->on('progress', function ($video, $format, $percentage) use($consoleUtil) {
				$consoleUtil->info("$percentage % done.");
			});
		}
		return $this->video->save($format, BASE_DIR.'/cut_and_resize.mp4');
	}
}