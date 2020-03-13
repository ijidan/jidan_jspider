<?php
namespace Tools\Controllers;

use Lib\ErrorCode;
use Lib\Paginate;
use Lib\Util\Config;
use Lib\Util\CommonUtil;
use Upload\Storage\FileSystem;


/**
 * 上传接口
 * Class UploadController
 * @package Tools\Controllers
 */
class CommonController extends IndexController {
	/**
	 * 上传图片
	 * @return \Lib\BaseController
	 */
	public function uploadImg() {
		$imgDir = $this->getImgDir();
		$storage = new FileSystem($imgDir);
		$file = new \Upload\File('file', $storage);
		// Optionally you can rename the file on upload
		$new_filename = uniqid();
		$file->setName($new_filename);
		// Validate file upload
		// MimeType List => http://www.iana.org/assignments/media-types/media-types.xhtml
		$imgMimeTypeList = [
			"image/gif",
			"image/jpeg",
			"image/png",
			"image/tiff",
			"image/vnd.wap.wbmp",
			"image/x-icon",
			"image/x-jng",
			"image/x-ms-bmp",
			"image/svg+xml",
			"image/webp",
		];
		$file->addValidations(array(
			//new \Upload\Validation\Mimetype('image/png'),
			//You can also add multi mimetype validation
			new \Upload\Validation\Mimetype($imgMimeTypeList),
			// Ensure file is no larger than 5M (use "B", "K", M", or "G")
			new \Upload\Validation\Size('5M')
		));
		try {
			$file->upload();
			$url = $file->getNameWithExtension();
			$imgUrl = $this->getImgUrl();
			$url = $imgUrl . $url;
			return $this->jsonSuccess("success", ["src" => $url, "value" => $url]);
		} catch (\Exception $e) {
			$errors = $file->getErrors();
			$errMsg = is_array($errors) ? join(";", $errors) : $errors;
			return $this->jsonFail(ErrorCode::ERROR, $errMsg);
		}
	}

	/**
	 * 上传文件
	 * @return \Slim\Http\Response
	 */
	public function uploadKindEditor() {
		$imgDir = $this->getImgDir();
		$storage = new FileSystem($imgDir);
		$file = new \Upload\File('imgFile', $storage);
		$new_filename = uniqid();
		$file->setName($new_filename);
		try {
			$file->upload();
			$url = $file->getNameWithExtension();
			$imgUrl = $this->getImgUrl();
			$url = $imgUrl . $url;
			return $this->response->withJson(["error" => 0, "url" => $url], 200);
		} catch (\Exception $e) {
			dump("1",1);
			$errors = $file->getErrors();
			$errMsg = is_array($errors) ? join(";", $errors) : $errors;
			return $this->response->withJson(["error" => 1, "message" => $errMsg], 200);
		}
	}

	/**
	 * 获取文件目录
	 * @return mixed
	 */
	private function getImgDir() {
		$imgDir = Config::getConfigItem("struct/img_dir");
		return $imgDir;
	}

	/**
	 *获取IMG URL
	 * @return mixed
	 */
	private function getImgUrl() {
		$imgUrl = Config::getConfigItem("struct/img_url");
		return $imgUrl;
	}
}