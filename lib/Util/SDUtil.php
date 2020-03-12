<?php

namespace Lib\Util;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\Console\Output\OutputInterface;
use Tools\Models\SdList;
use Tools\Models\SdProduct;
use Naucon\File\File;


/**
 * 网站工具类
 * Class SDUtil
 * @package Lib\Util
 */
class SDUtil {
	/**
	 * 上传图片
	 * @param $url
	 * @param OutputInterface|null $output
	 * @return bool
	 */
	public static function uploadImage($url, OutputInterface $output = null) {
		$catName = str_replace("-", " ", $url);
		$dirName = str_replace("-", "_", $url);
		$dir = "/vagrant/$dirName";
		$where = "url='" . $url . "'";
		$record = SdList::findOne($where);
		if (!$record) {
			$output->writeln("[$url] not exist");
		}
		$catId = $record["id"];
		return self::doUpload($dir, $dirName, $catId, $catName, $output);
	}

	/**
	 * 更新类别
	 * @param $url
	 * @param OutputInterface|null $output
	 * @return bool
	 */
	public static function updateCat($url, OutputInterface $output = null) {
		$cat = $url;
		$catName = str_replace("-", " ", $cat);
		$type = "product";

		$data = [
			"name"   => $catName,
			"ename"  => $catName,
			"pid"    => 1,
			"bid"    => 1,
			"title"  => $catName,
			"etitle" => $catName,
			"url"    => $cat,

			"keywords"     => $catName,
			"ekeywords"    => $catName,
			"description"  => $catName,
			"edescription" => $catName,

			"nav"       => 1,
			"type"      => $type,
			"link"      => "",
			"elink"     => "",
			"contents"  => "",
			"econtents" => ""
		];

		$where = "url='" . $cat . "'";
		$record = SdList::findOne($where);
		if ($record) {
			$data["sort"] = $record["sort"];
			SdList::update($data, $where);
			if ($output) {
				$output->writeln("更新：$cat");
			}
		} else {
			$lastProduct = SdList::findOne("type='$type'", [], "id", "DESC");
			$data["sort"] = $lastProduct ? $lastProduct["sort"] + 1 : 1;
			SdList::insert($data);
			if ($output) {
				$output->writeln("新增：$cat");
			}
		}
		return true;
	}

	/**
	 * 重命名
	 * @param $dirName
	 * @param string $shortName
	 * @param int $length
	 * @param OutputInterface|null $output
	 * @throws \Naucon\File\Exception\FileException
	 */
	public static function doRename($dirName, $shortName = "", $length = 3, OutputInterface $output = null) {
		$docsPath = BASE_DIR . DIRECTORY_SEPARATOR . "/docs/$dirName";
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($docsPath));
		/** @var SplFileInfo $file */
		$idx = 1;
		foreach ($files as $file) {
			if ($file->isFile()) {
				$pathName = $file->getPathname();
				$ext = $file->getExtension();
				$fullFileName = CommonUtil::fillZero($idx, $length);
				$fileObject_ = new File($pathName);
				$fullName = "SDP-${shortName}-{$fullFileName}.{$ext}";
				$fileObject_->rename($fullName);
				$idx++;
			}
		}
		if ($output) {
			$output->writeln("done");
		}
	}

	/**
	 * 重命名power bank
	 * @param OutputInterface|null $output
	 * @throws \Naucon\File\Exception\FileException
	 */
	public static function renamePowerBank(OutputInterface $output = null) {
		$docsPath = BASE_DIR . DIRECTORY_SEPARATOR . "/docs/power_bank";
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($docsPath));
		/** @var SplFileInfo $file */
		$idx = 1;
		foreach ($files as $file) {
			if ($file->isFile()) {
				$pathName = $file->getPathname();
				$ext = $file->getExtension();
				$fullFileName = CommonUtil::fillZero($idx, 3);
				$fileObject_ = new File($pathName);
				$fullName = "SDP-PB-{$fullFileName}.{$ext}";
				$fileObject_->rename($fullName);
				$idx++;
			}
		}
		if ($output) {
			$output->writeln("done");
		}
	}

	/**
	 * 移动PVC
	 * @param OutputInterface|null $output
	 * @throws \Naucon\File\Exception\FileException
	 */
	public static function movePVC(OutputInterface $output = null) {
		$docsPath = BASE_DIR . DIRECTORY_SEPARATOR . "/docs/bbb";
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($docsPath));
		$targetPath = "/data/pvc_inflatables";
		/** @var SplFileInfo $file */
		foreach ($files as $file) {
			if ($file->isFile()) {
				$pathName = $file->getPathname();
				self::doMove($pathName, $targetPath, $output);
			}
		}
		if ($output) {
			$output->writeln("done");
		}
	}

	/**
	 * 生成图片
	 * @param OutputInterface|null $output
	 * @throws \Naucon\File\Exception\FileException
	 */
	public static function movePlushToys(OutputInterface $output = null) {
		$docsPath = BASE_DIR . DIRECTORY_SEPARATOR . "/docs/plush_toys";
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($docsPath));
		$targetPath = "/data/plush_toys";
		$keyword = "SDP-PT-";
		/** @var SplFileInfo $file */
		foreach ($files as $file) {
			if ($file->isFile()) {
				$pathName = $file->getPathname();
				if (strpos($pathName, $keyword) !== false) {
					self::doMove($pathName, $targetPath, $output);
				}
			}
		}
		if ($output) {
			$output->writeln("done");
		}
	}

	/**
	 * 生成图片
	 * @param OutputInterface|null $output
	 * @throws \Naucon\File\Exception\FileException
	 */
	public static function genPlushToys(OutputInterface $output = null) {
		$docsPath = BASE_DIR . DIRECTORY_SEPARATOR . "/docs/plush_toys";
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($docsPath));
		$targetPath = "/data/plush_toys";
		$idx = 1;
		$primaryKeyword = "主图";
		$optionKeyword = "选项";
		/** @var SplFileInfo $file */
		foreach ($files as $file) {
			if ($file->isFile()) {
				$pathName = $file->getPathname();
				$done = true;
				if (strpos($pathName, $optionKeyword) !== false) {
					self::doGenPlushToys($pathName, $idx, $targetPath, $output);
					$idx++;
					$done = true;
				}
				if (strpos($pathName, $primaryKeyword) !== false) {
					$parentPathName = dirname(dirname($pathName));
					$optionPath = $parentPathName . DIRECTORY_SEPARATOR . $optionKeyword;
					$optionFile = new File($optionPath);
					if (!$optionFile->exists()) {
						self::doGenPlushToys($pathName, $idx, $targetPath, $output);
						$idx++;
						$done = true;
					}
				}
				if ($done && $output) {
					//$output->writeln("done:$pathName");
				}
			}
		}
		if ($output) {
			$output->writeln("done");
		}
	}

	/**
	 * 执行过程
	 * @param $pathName
	 * @param $idx
	 * @param $targetPath
	 * @throws \Naucon\File\Exception\FileException
	 */
	public static function doMove($pathName, $targetPath, OutputInterface $output = null) {
		$fileObject_ = new File($pathName);
		$ext = $fileObject_->getExtension();
		if (in_array($ext, ["jpg", "jpeg", "png", "gif"])) {
			$fileObject_->move($targetPath);
		}
		if ($output) {
			$output->writeln("move done:$pathName");
		}
	}

	/**
	 * 执行过程
	 * @param $pathName
	 * @param $idx
	 * @param $targetPath
	 * @throws \Naucon\File\Exception\FileException
	 */
	public static function doGenPlushToys($pathName, $idx, $targetPath, OutputInterface $output = null) {
		$fileObject_ = new File($pathName);
		$ext = $fileObject_->getExtension();
		$fullFileName = CommonUtil::fillZero($idx, 5);
		if (in_array($ext, ["jpg", "jpeg", "png", "gif"])) {
			$fullName = "SDP-PT-{$fullFileName}.{$ext}";
			try {
				$fileObject_->rename($fullName);
			} catch (\Exception $e) {
				if ($output) {
					$output->writeln("error:" . $e->get);
				}
			}
			//$fileObject_->copy($targetPath);
		}
	}

	/**
	 * 生产开瓶器文件
	 * @param OutputInterface|null $output
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
	 */
	public static function genOpener(OutputInterface $output = null) {
		$doc = BASE_DIR . DIRECTORY_SEPARATOR . "docs" . DIRECTORY_SEPARATOR . "opener_quotation_yjyh.xls";
		$destinationDir = BASE_DIR . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "bottle_opener" . DIRECTORY_SEPARATOR;
		ExcelUtil::readImage($doc, $destinationDir);
		if ($output) {
			$output->writeln("done");
		}
	}

	/**
	 * mugs
	 * @param OutputInterface|null $output
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
	 */
	public static function genMugs(OutputInterface $output = null) {
		$doc = BASE_DIR . DIRECTORY_SEPARATOR . "docs" . DIRECTORY_SEPARATOR . "mugs.xls";
		$destinationDir = BASE_DIR . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "mugs" . DIRECTORY_SEPARATOR;
		ExcelUtil::readImage($doc, $destinationDir, "SDP-MU-");
		if ($output) {
			$output->writeln("done");
		}
	}

	/**
	 * 上传移动电源
	 * @param OutputInterface|null $output
	 * @return bool
	 */
	public static function uploadPolo(OutputInterface $output = null) {
		$dir = "/vagrant/lanke/Uploads/polo";
		$catId = 58;
		$catName = "polo";
		$dirName = "polo";
		return self::doUpload($dir, $dirName, $catId, $catName, $output);
	}

	/**
	 * 上传移动电源
	 * @param OutputInterface|null $output
	 * @return bool
	 */
	public static function uploadPowerBank(OutputInterface $output = null) {
		$dir = "/vagrant/lanke/Uploads/power_bank";
		$catId = 57;
		$catName = "power bank";
		$dirName = "power_bank";
		return self::doUpload($dir, $dirName, $catId, $catName, $output);
	}

	/**
	 * mugs
	 * @param OutputInterface|null $output
	 * @return bool
	 */
	public static function uploadMugs(OutputInterface $output = null) {
		$dir = "/vagrant/lanke/Uploads/mugs";
		$catId = 56;
		$catName = "mugs";
		$dirName = "mugs";
		return self::doUpload($dir, $dirName, $catId, $catName, $output);
	}

	/**
	 * 上传毛绒玩具
	 * @param OutputInterface|null $output
	 * @return bool
	 */
	public static function uploadPVC(OutputInterface $output = null) {
		$dir = "/vagrant/lanke/Uploads/pvc_inflatables";
		$catId = 55;
		$catName = "pvc inflatables";
		$dirName = "pvc_inflatables";
		return self::doUpload($dir, $dirName, $catId, $catName, $output);
	}

	/**
	 * 上传毛绒玩具
	 * @param OutputInterface|null $output
	 * @return bool
	 */
	public static function uploadPlushToys(OutputInterface $output = null) {
		$dir = "/vagrant/lanke/Uploads/plush_toys";
		$catId = 54;
		$catName = "plush toys";
		$dirName = "plush_toys";
		return self::doUpload($dir, $dirName, $catId, $catName, $output);
	}

	/**
	 * 上传开瓶器
	 * @param OutputInterface|null $output
	 * @return bool
	 */
	public static function uploadOpener(OutputInterface $output = null) {
		$dir = "/vagrant/bottle_opener";
		$catId = 53;
		$catName = "bottle opener";
		$dirName = "bottle_opener";
		return self::doUpload($dir, $dirName, $catId, $catName, $output);
	}

	/***
	 * 上传USB Sticker
	 * @param OutputInterface|null $output
	 * @return bool
	 */
	public static function uploadUS(OutputInterface $output = null) {
		$dir = "/vagrant/usb_stick";
		$catId = 52;
		$catName = "usb stick";
		$dirName = "usb_stick";
		return self::doUpload($dir, $dirName, $catId, $catName, $output);
	}

	/**
	 * 执行上传过程
	 * @param $dir
	 * @param $dirName
	 * @param $catId
	 * @param $catName [
	 * @param OutputInterface|null $output
	 * @return bool
	 */
	public static function doUpload($dir, $dirName, $catId, $catName, OutputInterface $output = null) {
		$di = new \DirectoryIterator($dir);
		foreach ($di as $idx => $item) {
			$pathName = $item->getPathname();
			$fileName = $item->getFilename();
			$fileInfo = $item->getFileInfo();
			$docName = str_replace(strrchr($fileName, "."), "", $fileName);
			$docNameInt = CommonUtil::extractNumber($docName);
			$sort = $docNameInt > 0 ? $docNameInt : $idx + 1;

			$contents = '<img src="/Uploads/' . $dirName . '/' . $fileName . '" alt="" />';
			if (!$docName) {
				continue;
			}
			$data = [
				"name"         => "$docName",
				"title"        => "$catName:$docName",
				"url"          => "$docName",
				"keywords"     => "$catName:$docName",
				"description"  => "$catName:$docName",
				"contents"     => $contents,
				"ename"        => "$docName",
				"etitle"       => "$catName:$docName",
				"ekeywords"    => "$catName:$docName",
				"edescription" => "$catName:$docName",
				"econtents"    => $contents,
				"pid"          => "$catId",
				"bid"          => $sort,
				"photo"        => "$dirName/$fileName",
				"thumb"        => "$dirName/$fileName",
				"property1"    => "",
				"property2"    => "",
				"property3"    => "",
				"property4"    => "",
				"eproperty1"   => "",
				"eproperty2"   => "",
				"eproperty3"   => "",
				"eproperty4"   => "",
				"sort"         => "$sort",
				"featured"     => "0"
			];
			$where = "url='" . $docName . "'";
			$checkRe = SdProduct::find($where);
			if ($checkRe) {
				SdProduct::update($data, $where);
				if ($output) {
					$output->writeln("更新：$docName");
				}
			} else {
				SdProduct::insert($data);
				if ($output) {
					$output->writeln("新增：$docName");
				}
			}
		}
		return true;
	}

}