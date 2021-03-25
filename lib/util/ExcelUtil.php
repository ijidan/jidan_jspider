<?php

namespace Lib\Util;

use DirectoryIterator;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
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
	 * 字符串
	 */
	const STR = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

	/**
	 * 文件路劲
	 */
	const FILE_DIR_PATH =   '../storage/excel/';

	/**
	 * 生成EXCEL文件
	 * @param $fileName
	 * @param array $headers
	 * @param array $contentList
	 * @param array $contentIdx
	 * @return string
	 * @throws \PHPExcel_Exception
	 */
	public static function genExcel($fileName, array $headers, array $contentList, array $contentIdx = []) {
		self::confirmDir();
		self::cleanDir();;
		$contentList = $contentIdx ? self::getContentList($contentList, $contentIdx) : $contentList;
		$objPHPExcel = new PHPExcel();
		$objSheet = $objPHPExcel->getActiveSheet();//获取到sheet
		$objSheet->setTitle($fileName);//设置sheet名字
		$objSheet->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//设置excel文件默认水平垂直方向居中
		$objSheet->getDefaultRowDimension()->setRowHeight(30);//设置默认行高
		$objSheet->getRowDimension(1)->setRowHeight(50);//设置第一行行高

		$num = count($headers);
		$xColList = self::getXColIdx($num);
		//设置标题
		foreach ($headers as $hIdx => $hValue) {
			$cellIdx = $xColList[$hIdx] . "1";
			$objSheet->setCellValue($cellIdx, $hValue);
		}
		//设置内容
		foreach ($contentList as $contentIdx => $content) {
			$rowIdx = $contentIdx + 2;
			$contentItem = array_values($content);
			foreach ($contentItem as $cIdx => $cValue) {
				$objSheet->setCellValue($xColList[$cIdx] . $rowIdx, $cValue);
			}
		}
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');//生成excel文件 如：Excel2007

		$filePath = self::FILE_DIR_PATH . $fileName . '.xls';
		$objWriter->save($filePath);
		return $filePath;
	}

	/**
	 * 获取内容
	 * @param array $contentList
	 * @param array $contentIdx
	 * @return array
	 */
	private static function getContentList(array $contentList, array $contentIdx) {
		$filteredContentList = [];
		foreach ($contentIdx as $contentIdxItem){
			foreach ($contentList as $idx => $content) {
				foreach ($content as $key => $value) {
					if($key==$contentIdxItem){
						$filteredContentList[$idx][$key] = $value;
					}
				}
			}
		}
		return $filteredContentList;
	}

	/**
	 * 获取索引
	 * @param $num
	 * @return array
	 */
	private static function getXColIdx($num = -1) {
		$colList = [];
		self::computeColSingleCharacter($colList);
		self::computeColDoubleCharacter($colList);
		return $num > 0 ? array_slice($colList, 0, $num) : $colList;
	}

	/**
	 * 单字母
	 * @param array $colList
	 */
	private static function computeColSingleCharacter(array &$colList) {
		$strLen = strlen(self::STR);
		for ($i = 0; $i < $strLen; $i++) {
			$value1 = self::STR[$i];
			$col = $value1;
			array_push($colList, $col);
		}
	}

	/**
	 * 双字母
	 * @param array $colList
	 */
	private static function computeColDoubleCharacter(array &$colList) {
		$strLen = strlen(self::STR);
		for ($i = 0; $i < $strLen; $i++) {
			for ($j = 0; $j < $strLen; $j++) {
				$value1 = self::STR[$i];
				$value2 = self::STR[$j];
				$col = $value1 . $value2;
				array_push($colList, $col);
			}
		}

	}


	/**
	 * 检查文件夹
	 */
	private static function confirmDir() {
		$mod = 0777;
		if (!is_dir(self::FILE_DIR_PATH)) {
			mkdir(self::FILE_DIR_PATH, $mod, true);
		}
	}

	/**
	 * 清空文件夹
	 */
	private static function cleanDir() {
		foreach ([self::FILE_DIR_PATH] as $dir) {
			foreach (new DirectoryIterator($dir) as $fileInfo) {
				if ($fileInfo->isFile()) {
					$filePathName = $fileInfo->getPathname();
					unlink($filePathName);
				}
			}
		}

	}

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