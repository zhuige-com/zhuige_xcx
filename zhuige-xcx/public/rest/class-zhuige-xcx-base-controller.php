<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
 */

class ZhuiGe_Xcx_Base_Controller extends WP_REST_Controller
{
	public $routes = [];

	public function __construct()
	{
		$this->namespace = 'zhuige';
	}

	public function register_routes()
	{
		foreach ($this->routes as $key => $value) {
			$route = '/' . $this->module . '/' . $key;

			if (is_array($value)) {
				$callback = $value['callback'];

				$methods = WP_REST_Server::CREATABLE;
				if (isset($value['method']) && $value['method'] == 'get') {
					$methods = WP_REST_Server::READABLE;
				}

				if (isset($value['auth']) && $value['auth'] == 'login') {
					ZhuiGe_Xcx::$require_login_uris[] = '/wp-json/' . $this->namespace . $route;
				}
			} else {
				$callback = $value;
				$methods = WP_REST_Server::CREATABLE;
			}

			register_rest_route($this->namespace, $route, [
				[
					'callback' => [$this, $callback],
					'methods' => $methods,
				]
			]);
		}
	}

	/**
	 * 组合返回值
	 */
	public function make_response($code, $msg, $data = null)
	{
		$response = [
			'code' => $code,
			'message' => $msg,
		];

		if ($data !== null) {

			if (ZhuiGe_Xcx_Addon::is_active('zhuige-ban_words')) {
				$find = ZhuiGe_Xcx::option_value('ban_words_find');
				$find = preg_split('/[,;\r\n]+/', trim($find, ",;\r\n"));

				$replace = ZhuiGe_Xcx::option_value('ban_words_replace');
				$replace = trim($replace, ",;\r\n");

				if (!empty($find) && !empty($replace)) {
					zhuige_ban_words_replace($find, $replace, $data);
				}
			}

			$response['data'] = $data;
		}

		return $response;
	}

	/**
	 * 组合返回值 成功
	 */
	public function success($data = null)
	{
		$message = '操作成功！';
		if (is_string($data)) {
			$message = $data;
			$data = null;
		}

		return $this->make_response(0, $message, $data);
	}

	/**
	 * 组合返回值 失败
	 */
	public function error($msg = '', $code = 1)
	{
		return $this->make_response($code, $msg);
	}

	/**
	 * 获取参数的方便方法
	 */
	public function param($request, $param, $default = false)
	{
		if (isset($request[$param])) {
			return sanitize_text_field(wp_unslash($request[$param]));
		}

		return $default;
	}

	/**
	 * 获取参数 INT
	 */
	public function param_int($request, $param, $default = 0)
	{
		return (int)($this->param($request, $param, $default));
	}

	/**
	 * 检查敏感内容
	 */
	public function msg_sec_check($content, $os = 'wx')
	{
		if ($os == 'qq') {
			return $this->qq_msg_sec_check($content);
		} else {
			return $this->wx_msg_sec_check($content);
		}
	}

	/**
	 * 微信-检查敏感内容
	 */
	public function wx_msg_sec_check($content)
	{
		$wx_session = ZhuiGe_Xcx::get_wx_token();
		if (!$wx_session) {
			return false;
		}

		$access_token = $wx_session['access_token'];

		$api = 'https://api.weixin.qq.com/wxa/msg_sec_check?access_token=' . $access_token;

		$args = array(
			'method'  => 'POST',
			'body' 	  => json_encode(['content' => $content], JSON_UNESCAPED_UNICODE),
			'headers' => array(
				'Content-Type' => 'application/json'
			),
			'cookies' => array()
		);

		$res = wp_remote_post($api, $args);
		if (is_wp_error($res)) {
			return false;
		}

		if ($res['response']['code'] == 200) {
			$body = json_decode($res['body'], TRUE);
			if ($body['errcode'] == 0) {
				return true;
			}
		}

		return false;
	}

	/**
	 * QQ-检查敏感内容
	 */
	public function qq_msg_sec_check($content)
	{
		$qq_session = ZhuiGe_Xcx::get_qq_token();
		if (!$qq_session) {
			return false;
		}

		$access_token = $qq_session['access_token'];

		$api = 'https://api.q.qq.com/api/json/security/MsgSecCheck?access_token=' . $access_token;

		$qq = ZhuiGe_Xcx::option_value('basic_qq');
		if (!$qq || empty($qq['appid'])) {
			return false;
		}

		$args = array(
			'method'  => 'POST',
			'body' 	  => json_encode(['appid' => $qq['appid'], 'content' => $content], JSON_UNESCAPED_UNICODE),
			'headers' => array(
				'Content-Type' => 'application/json'
			),
			'cookies' => array()
		);

		$res = wp_remote_post($api, $args);
		if (is_wp_error($res)) {
			return false;
		}

		if ($res['response']['code'] == 200) {
			$body = json_decode($res['body'], TRUE);
			if ($body['errCode'] == 0) {
				return true;
			}
		}

		return false;
	}

	/**
	 * 内容检查
	 */
	public function msg_sec_media($img, $os = 'wx')
	{
		if ($os == 'qq') {
			return $this->qq_msg_sec_media($img);
		} else {
			return $this->wx_msg_sec_media($img);
		}
	}

	/**
	 * 微信 图片敏感内容检测
	 */
	public function wx_msg_sec_media($img)
	{
		$obj = new CURLFile(realpath($img));
		$obj->setMimeType("image/jpeg");
		$file['media'] = $obj;

		$wx_session = ZhuiGe_Xcx::get_wx_token();
		if (!$wx_session) {
			return false;
		}

		$access_token = $wx_session['access_token'];

		$api = "https://api.weixin.qq.com/wxa/img_sec_check?access_token=$access_token";

		// $args = array(
		// 	'method'  => 'POST',
		// 	'body' 	  => ['media' => $obj],
		// 	'headers' => array(
		// 		'Content-Type' => 'multipart/form-data'
		// 	),
		// 	'cookies' => array()
		// );

		// $res = wp_remote_post($api, $args);
		// if (is_wp_error($res)) {
		// 	return false;
		// }

		// file_put_contents('888.txt', json_encode($res));

		// if ($res['response']['code'] == 200) {
		// 	$body = json_decode($res['body'], TRUE);
		// 	if ($body['errcode'] == 0) {
		// 		return true;
		// 	}
		// }

		$res = $this->http_request($api, $file);
		// file_put_contents('888.txt', json_encode($res));
		$data = json_decode($res, TRUE);
		if ($data['errcode'] == 0) {
			return true;
		}

		return false;
	}

	/*QQ 图片敏感内容检测*/
	public function qq_msg_sec_media($img)
	{
		$obj = new CURLFile(realpath($img));
		$obj->setMimeType("image/jpeg");
		$file['media'] = $obj;

		$qq_session = ZhuiGe_Xcx::get_qq_token();
		if (!$qq_session) {
			return false;
		}

		$access_token = $qq_session['access_token'];

		$api = "https://api.q.qq.com/api/json/security/ImgSecCheck?access_token=$access_token";

		$qq = ZhuiGe_Xcx::option_value('basic_qq');
		if (!$qq || empty($qq['appid'])) {
			return false;
		}

		$args = array(
			'method'  => 'POST',
			'body' 	  => ['appid' => $qq['appid'], 'media' => $obj],
			'headers' => array(
				'Content-Type' => 'multipart/form-data'
			),
			'cookies' => array()
		);

		$res = wp_remote_post($api, $args);
		if (is_wp_error($res)) {
			return false;
		}

		// $data = json_decode($res, TRUE);
		// if ($data['errCode'] == 0) {
		// 	return true;
		// }

		// file_put_contents('6677.txt', json_encode($res));

		if ($res['response']['code'] == 200) {
			$body = json_decode($res['body'], TRUE);
			if ($body['errCode'] == 0) {
				return true;
			}
		}

		return false;
	}

	//HTTP请求（支持HTTP/HTTPS，支持GET/POST）
	private function http_request($url, $data = null)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

		if (!empty($data)) {
			curl_setopt($curl, CURLOPT_POST, TRUE);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		$output = curl_exec($curl);
		curl_close($curl);
		//file_put_contents('/tmp/heka_weixin.' . wp_date("Ymd") . '.log', wp_date('Y-m-d H:i:s') . "\t" . $output . "\n", FILE_APPEND);
		return $output;
	}
}
