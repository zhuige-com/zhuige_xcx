<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
 */

class ZhuiGe_Xcx_Other_Controller extends ZhuiGe_Xcx_Base_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->module = 'other';
		$this->routes = [
			'upload' => 'upload',
			'upload2' => 'upload2',
			'uploadv' => 'uploadv',
		];
	}

	/**
	 * 上传图片
	 */
	public function upload($request)
	{
		$image = $_FILES['image'];

		$upload_dir = wp_upload_dir(null, false);
		$res = $this->uploadOne($image, $upload_dir['path']);
		if (is_wp_error($res)) {
			return $this->error('上传失败');
		}

		if (isset($res['error'])) {
			return $this->error($res['error']);
		}

		$path = $upload_dir['path'] . '/' . $res['file'];
		$url = $upload_dir['url'] . '/' . $res['file'];
		if (file_exists($path)) {
			$size = getimagesize($path);
		} else {
			$size = getimagesize($url);
		}
		return $this->success([
			'src' => $url,
			'width' => $size[0],
			'height' => $size[1]
		]);
	}

	/**
	 * 上传图片
	 */
	public function upload2($request)
	{
		$image = $_FILES['image'];

		$upload_dir = wp_upload_dir(null, false);
		$res = $this->uploadOne2($image, $upload_dir['path']);
		if (isset($res['error'])) {
			return $this->error($res['error']);
		}

		$url = $upload_dir['url'] . '/' . basename($res['file']);
		if (isset($res['sizes']) && isset($res['sizes']['thumbnail'])) {
			$thumbnail = $upload_dir['url'] . '/' . basename($res['sizes']['thumbnail']['file']);
		} else {
			$thumbnail = $url;
		}

		$data = [
			'id' => $res['id'],
			'title' => isset($res['title']) ? $res['title'] : '',
			'description' => '',
			'alt' => '',
			'url' => $url,
			'thumbnail' => $thumbnail,
			'width' => $res['width'],
			'height' => $res['height'],
		];

		return $this->success($data);
	}

	/**
	 * 上传视频
	 */
	public function uploadv($request)
	{
		$image = $_FILES['image'];

		$upload_dir = wp_upload_dir(null, false);
		$res = $this->uploadVideo($image, $upload_dir['path']);
		if (is_wp_error($res)) {
			return $this->error('上传失败');
		}

		if (isset($res['error'])) {
			return $this->error($res['error']);
		}

		$data = [
			'id' => '',
			'title' => $res['file'],
			'description' => '',
			'alt' => '',
			'url' => $upload_dir['url'] . '/' . $res['file'],
			'thumbnail' => '',
			'width' => 0,
			'height' => 0,
		];

		return $this->success($data);
	}

	/**
	 * @desc 单文件上传
	 * @param string $file,上传文件信息数组
	 * @param string $path,上传路径
	 * @param int $max = 20M,最大上传大小
	 * @return bool|string,成功返回文件名，失败返回false
	 */
	private function uploadOne($file, $path, $max = 20000000)
	{
		//判定文件有效性
		if (!isset($file['error']) || count($file) != 5) {
			return ['error' => '错误的上传文件！'];
		}

		//路径判定
		if (!is_dir($path)) {
			return ['error' => '存储路径不存在！'];
		}

		$ext = strrchr($file['name'], '.');
		if ($ext != '.jpg' && $ext != '.jpeg' && $ext != '.png' && $ext != '.gif') {
			return ['error' => '错误的上传文件！'];
		}

		//判定文件是否正确上传
		switch ($file['error']) {
			case 1:
			case 2:
				return ['error' => '文件超过服务器允许大小！'];
			case 3:
				return ['error' => '文件只有部分被上传！'];
			case 4:
				return ['error' => '没有选中要上传的文件！'];
			case 6:
			case 7:
				return ['error' => '服务器错误！'];
		}

		//判定文件类型
		if (!in_array($file['type'], array('image/jpg', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/gif'))) {
			return ['error' => '当前上传的文件类型不允许！'];
		}

		//判定业务大小
		if ($file['size'] > $max) {
			return ['error' => '当前上传的文件超过允许的大小！当前允许的大小是：' . (string) ($max / 1000000) . 'M'];
		}

		//获取随机名字
		$filename = $this->getRandomName($file['name']);

		//移动上传的临时文件到指定目录
		$filepath = $path . '/' . $filename;
		if (move_uploaded_file($file['tmp_name'], $filepath)) {
			// if (!$this->msg_sec_media($filepath)) {
			// 	return ['error' => '请勿发布敏感信息'];
			// }

			$res = zhuige_xcx_import_image2attachment($filepath);
			if (!is_wp_error($res)) {
				$filename = $res;
			}

			//成功
			return ['file' => $filename];
		} else {
			//失败
			return ['error' => '文件移动失败！'];
		}
	}

	/**
	 * @desc 单文件上传
	 * @param string $file,上传文件信息数组
	 * @param string $path,上传路径
	 * @param int $max = 20M,最大上传大小
	 * @return bool|string,成功返回文件名，失败返回false
	 */
	private function uploadOne2($file, $path, $max = 20000000)
	{
		//判定文件有效性
		if (!isset($file['error']) || count($file) != 5) {
			return ['error' => '错误的上传文件！'];
		}

		//路径判定
		if (!is_dir($path)) {
			return ['error' => '存储路径不存在！'];
		}

		$ext = strrchr($file['name'], '.');
		if ($ext != '.jpg' && $ext != '.jpeg' && $ext != '.png' && $ext != '.gif') {
			return ['error' => '错误的上传文件！'];
		}

		//判定文件是否正确上传
		switch ($file['error']) {
			case 1:
			case 2:
				return ['error' => '文件超过服务器允许大小！'];
			case 3:
				return ['error' => '文件只有部分被上传！'];
			case 4:
				return ['error' => '没有选中要上传的文件！'];
			case 6:
			case 7:
				return ['error' => '服务器错误！'];
		}

		//判定文件类型
		if (!in_array($file['type'], array('image/jpg', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/gif'))) {
			return ['error' => '当前上传的文件类型不允许！'];
		}

		//判定业务大小
		if ($file['size'] > $max) {
			return ['error' => '当前上传的文件超过允许的大小！当前允许的大小是：' . (string) ($max / 1000000) . 'M'];
		}

		//获取随机名字
		$filename = $this->getRandomName($file['name']);

		//移动上传的临时文件到指定目录
		$filepath = $path . '/' . $filename;
		if (move_uploaded_file($file['tmp_name'], $filepath)) {
			// if (!$this->msg_sec_media($filepath)) {
			// 	return ['error' => '请勿发布敏感信息'];
			// }

			$res = zhuige_xcx_import_image_attachment_meta($filepath);
			if (is_wp_error($res)) {
				return ['error' => '导入媒体库失败'];
			} else {
				return $res;
			}
		} else {
			//失败
			return ['error' => '文件移动失败！'];
		}
	}

	/**
	 * @desc 单文件上传
	 * @param string $file,上传文件信息数组
	 * @param string $path,上传路径
	 * @param int $max = 100M,最大上传大小
	 * @return bool|string,成功返回文件名，失败返回false
	 */
	private function uploadVideo($file, $path, $max = 100000000)
	{
		//判定文件有效性
		if (!isset($file['error']) || count($file) != 5) {
			return ['error' => '错误的上传文件！'];
		}

		//路径判定
		if (!is_dir($path)) {
			return ['error' => '存储路径不存在！'];
		}

		//取出源文件后缀
		$ext = strrchr($file['name'], '.');
		if ($ext != '.mp4') {
			return ['error' => '错误的上传文件！'];
		}

		//判定文件是否正确上传
		switch ($file['error']) {
			case 1:
			case 2:
				return ['error' => '文件超过服务器允许大小！'];
			case 3:
				return ['error' => '文件只有部分被上传！'];
			case 4:
				return ['error' => '没有选中要上传的文件！'];
			case 6:
			case 7:
				return ['error' => '服务器错误！'];
		}
		//判定文件类型
		if (!in_array($file['type'], array('video/x-m4v', 'video/mp4', 'video/3gpp'))) {
			return ['error' => $file['type']];
		}
		//判定业务大小
		if ($file['size'] > $max) {
			return ['error' => '当前上传的文件超过允许的大小！当前允许的大小是：' . (string) ($max / 1000000) . 'M'];
		}
		//获取随机名字
		$filename = $this->getRandomName($file['name']);
		//移动上传的临时文件到指定目录
		$filepath = $path . '/' . $filename;
		if (move_uploaded_file($file['tmp_name'], $filepath)) {
			// if (!$this->msg_sec_media($filepath)) {
			// 	return ['error' => '请勿发布敏感信息'];
			// }

			$res = zhuige_xcx_import_image2attachment($filepath);
			if (!is_wp_error($res)) {
				$filename = $res;
			}

			//成功
			return ['file' => $filename];
		} else {
			//失败
			return ['error' => '文件移动失败！'];
		}
	}

	/**
	 * @desc 获取随机文件名
	 * @param string $filename,文件原名
	 * @param string $prefix,前缀
	 * @return string,返回新文件名
	 */
	private function getRandomName($filename, $prefix = 'jq')
	{
		//取出源文件后缀
		$ext = strrchr($filename, '.');
		//构建新名字
		$new_name = $prefix . time();
		//增加随机字符（6位大写字母）
		for ($i = 0; $i < 6; $i++) {
			$new_name .= chr(mt_rand(65, 90));
		}
		//返回最终结果
		return $new_name . $ext;
	}
}

ZhuiGe_Xcx::$rest_controllers[] = new ZhuiGe_Xcx_Other_Controller();
