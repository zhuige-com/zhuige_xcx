<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
 */

class ZhuiGe_Xcx
{
	//分页 每页数量
	const POSTS_PER_PAGE = 10;

	protected $loader;

	public static $rest_controllers = [];

	public static $post_types = [
		['id' => 'page', 'name' => '页面', 'link' => '/pages/base/page/page']
	];

	public static $require_login_uris = [];

	/**
	 * 获取配置
	 */
	public static function option_value($key, $default = '')
	{
		static $options = false;
		if (!$options) {
			$options = get_option('zhuige-xcx');
		}

		if (isset($options[$key]) && !empty($options[$key])) {
			return $options[$key];
		}

		return $default;
	}

	/**
	 * 图片配置项url
	 */
	public static function option_image_url($image, $default = '')
	{
		if ($image && isset($image['url']) && $image['url']) {
			return $image['url'];
		} else {
			if ($default) {
				return ZHUIGE_XCX_BASE_URL . "public/images/$default";
			} else {
				return $default;
			}
		}
	}

	/**
	 * 分类属性
	 */
	public static function cat_property($cat_id, $key, $default = '')
	{
		$options = get_term_meta($cat_id, 'zhuige-xcx-category', true);
		if (isset($options[$key]) && !empty($options[$key])) {
			return $options[$key];
		}

		return $default;
	}

	/**
	 * 文章属性
	 */
	public static function post_property($post_id, $key, $default = '')
	{
		$options = get_post_meta($post_id, 'zhuige-xcx-post-opt', true);
		if (isset($options[$key]) && !empty($options[$key])) {
			return $options[$key];
		}

		return $default;
	}

	/**
	 * 微信 token
	 */
	public static function get_wx_token()
	{
		$access_token = get_option('zhuige-xcx-wx-access-token');
		if ($access_token && isset($access_token['expires_in']) && $access_token['expires_in'] > time()) {
			return $access_token;
		}

		$wechat = ZhuiGe_Xcx::option_value('basic_wechat');
		$app_id = '';
		$app_secret = '';
		if ($wechat) {
			$app_id = $wechat['appid'];
			$app_secret = $wechat['secret'];
		}

		if (empty($app_id) || empty($app_secret)) {
			return false;
		}

		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$app_id&secret=$app_secret";
		$body = wp_remote_get($url);
		if (!is_array($body) || is_wp_error($body) || $body['response']['code'] != '200') {
			return false;
		}
		$access_token = json_decode($body['body'], TRUE);

		$access_token['expires_in'] = $access_token['expires_in'] + time() - 200;

		update_option('zhuige-xcx-wx-access-token', $access_token);

		return $access_token;
	}

	/**
	 * QQ token
	 */
	public static function get_qq_token()
	{
		$access_token = get_option('zhuige-xcx-qq-access-token');
		if ($access_token && isset($access_token['expires_in']) && $access_token['expires_in'] > time()) {
			return $access_token;
		}

		$qq = ZhuiGe_Xcx::option_value('basic_qq');
		$app_id = '';
		$app_secret = '';
		if ($qq) {
			$app_id = $qq['appid'];
			$app_secret = $qq['secret'];
		}

		if (empty($app_id) || empty($app_secret)) {
			return false;
		}

		$url = "https://api.q.qq.com/api/getToken?grant_type=client_credential&appid=$app_id&secret=$app_secret";
		$body = wp_remote_get($url);
		if (!is_array($body) || is_wp_error($body) || $body['response']['code'] != '200') {
			return false;
		}
		$access_token = json_decode($body['body'], TRUE);

		$access_token['expires_in'] = $access_token['expires_in'] + time() - 200;

		update_option('zhuige-xcx-qq-access-token', $access_token);

		return $access_token;
	}

	/**
	 * 百度 token
	 */
	public static function get_bd_token()
	{
		$access_token = get_option('zhuige-xcx-bd-access-token');
		if ($access_token && isset($access_token['expires_in']) && $access_token['expires_in'] > time()) {
			return $access_token;
		}

		$baidu = ZhuiGe_Xcx::option_value('basic_baidu');
		$app_id = '';
		$app_secret = '';
		if ($baidu) {
			$app_id = $baidu['appid'];
			$app_secret = $baidu['secret'];
		}

		if (empty($app_id) || empty($app_secret)) {
			return false;
		}

		$url = "https://openapi.baidu.com/oauth/2.0/token?grant_type=client_credentials&client_id=$app_id&client_secret=$app_secret&scope=smartapp_snsapi_base
		";
		$body = wp_remote_get($url);
		if (!is_array($body) || is_wp_error($body) || $body['response']['code'] != '200') {
			return false;
		}
		$access_token = json_decode($body['body'], TRUE);

		$access_token['expires_in'] = $access_token['expires_in'] + time() - 200;

		update_option('zhuige-xcx-bd-access-token', $access_token);

		return $access_token;
	}

	public static function user_avatar($user_id)
	{
		$avatar = get_user_meta($user_id, 'zhuige_xcx_user_avatar', true);
		if (empty($avatar)) {
			$avatar = ZHUIGE_XCX_BASE_URL . 'public/images/placeholder.jpg';
		}
		return $avatar;
	}

	public function __construct()
	{
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	private function load_dependencies()
	{
		require_once ZHUIGE_XCX_BASE_DIR . 'includes/class-zhuige-xcx-loader.php';
		require_once ZHUIGE_XCX_BASE_DIR . 'includes/class-zhuige-xcx-i18n.php';
		require_once ZHUIGE_XCX_BASE_DIR . 'admin/class-zhuige-xcx-admin.php';
		require_once ZHUIGE_XCX_BASE_DIR . 'public/class-zhuige-xcx-public.php';

		/**
		 * rest api
		 */
		require_once ZHUIGE_XCX_BASE_DIR . 'public/rest/class-zhuige-xcx-base-controller.php';
		require_once ZHUIGE_XCX_BASE_DIR . 'public/rest/class-zhuige-xcx-setting-controller.php';
		require_once ZHUIGE_XCX_BASE_DIR . 'public/rest/class-zhuige-xcx-user-controller.php';
		require_once ZHUIGE_XCX_BASE_DIR . 'public/rest/class-zhuige-xcx-other-controller.php';
		require_once ZHUIGE_XCX_BASE_DIR . 'public/rest/class-zhuige-xcx-post-controller.php';
		require_once ZHUIGE_XCX_BASE_DIR . 'public/rest/class-zhuige-xcx-comment-controller.php';

		// 加载插件 REST API
		foreach (ZhuiGe_Xcx_Addon::$rests as $rest) {
			$file_path = ZHUIGE_XCX_ADDONS_DIR . $rest;
			if (file_exists($file_path)) {
				require_once($file_path);
			}
		}

		/**
		 * ajax
		 */
		require_once ZHUIGE_XCX_BASE_DIR . 'includes/class-zhuige-xcx-ajax.php';
		// 加载插件 ajax
		foreach (ZhuiGe_Xcx_Addon::$ajaxs as $ajax) {
			$file_path = ZHUIGE_XCX_ADDONS_DIR . $ajax;
			if (file_exists($file_path)) {
				require_once($file_path);
			}
		}

		/**
		 * 后台管理
		 */
		require_once ZHUIGE_XCX_BASE_DIR . 'admin/codestar-framework/codestar-framework.php';

		$this->loader = new ZhuiGe_Xcx_Loader();
	}

	private function set_locale()
	{
		$plugin_i18n = new ZhuiGe_Xcx_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	private function define_admin_hooks()
	{
		if (!is_admin()) {
			return;
		}

		$admin = new ZhuiGe_Xcx_Admin();

		$this->loader->add_action('admin_enqueue_scripts', $admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $admin, 'enqueue_scripts');

		$this->loader->add_action('init', $admin, 'create_menu', 0);
		$this->loader->add_action('admin_init', $admin, 'admin_init');
		$this->loader->add_action('admin_menu', $admin, 'admin_menu', 20);
	}

	private function define_public_hooks()
	{
		$public = new ZhuiGe_Xcx_Public();

		$this->loader->add_action('init', $public, 'plugin_init');

		$this->loader->add_action('wp_enqueue_scripts', $public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $public, 'enqueue_scripts');

		$this->loader->add_action('trashed_post', $public, 'trashed_post');
		$this->loader->add_action('untrashed_post', $public, 'untrashed_post');
		$this->loader->add_action('deleted_post', $public, 'deleted_post');

		$this->loader->add_action('transition_post_status', $public, 'transition_post_status', 10, 3);
		$this->loader->add_action('transition_comment_status', $public, 'transition_comment_status', 10, 3);

		foreach (ZhuiGe_Xcx::$rest_controllers as $control) {
			$this->loader->add_action('rest_api_init', $control, 'register_routes');
		}
	}

	public function run()
	{
		$this->loader->run();
	}
}
