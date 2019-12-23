<?php

namespace Lib\Util;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


/**
 * Excel工具
 * Class ExcelUtil
 * @package Lib\Util
 */
class ExcelUtil {

	/**
	 * 获取文件扩展
	 * @param $mimeType
	 * @return mixed
	 */
	public static function getImageExt($mimeType) {
		$map = [
			'image/jpg'  => "jpg",
			'image/jpeg' => "jpg",
			'image/gif'  => "gif",
			'image/png'  => "png"
		];
		return $map[$mimeType];
	}


	/**
	 * 读取图片
	 * @param $sourceFile
	 * @param $destinationDir
	 * @param string $prefix
	 * @param int $length
	 * @return array
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
	 */
	public static function readImage($sourceFile, $destinationDir, $prefix = "SDP-BO-", $length = 3) {
		/** @var Spreadsheet $excel */
		$excel = IOFactory::load($sourceFile);
		/** @var Worksheet $sheet */
		$sheet = $excel->getActiveSheet();
		$collection=$sheet->getDrawingCollection();
		$imgData = array();
		/** @var MemoryDrawing $img */
		foreach ($collection as $idx => $img) {
			list ($startColumn, $startRow) = Coordinate::coordinateFromString($img->getCoordinates());//获取列与行号
			$imageFileName = strtoupper($prefix . CommonUtil::fillZero($idx + 1, $length));
			$mimeType = $img->getMimeType();
			$ext = self::getImageExt($mimeType);
			$res = $img->getImageResource();
			$destinationFile = $destinationDir . $imageFileName . "." . $ext;
			switch ($mimeType) {//处理图片格式
				case 'image/jpg':
				case 'image/jpeg':
					imagejpeg($res, $destinationFile);
					break;
				case 'image/gif':
					imagegif($res, $destinationFile);
					break;
				case 'image/png':
					imagepng($res, $destinationFile);
					break;
			}
			$imgData[$startRow][$startColumn] = $imageFileName;//追加到数组中去
		}
		return $imgData;
	}


}