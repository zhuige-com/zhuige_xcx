<?php

/*
 * 追格小程序
 * Author: 追格
 * Help document: https://www.zhuige.com
 * Copyright © 2022 www.zhuige.com All rights reserved.
 */

class ZhuiGe_Xcx_Post_Controller extends ZhuiGe_Xcx_Base_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->module = 'posts';
		$this->routes = [
			'page' => 'get_post_page',

			'list_last' => 'get_list_last',
			'list_follow' => 'get_list_follow',

			'list_tag' => 'get_list_tag',

			'list_search' => 'get_list_search',

			'list_user' => 'get_list_user',

			'wxacode' => 'get_wxacode',
			'qqacode' => 'get_qqacode',
			'bdacode' => 'get_bdacode',
		];
	}

	/**
	 * 最新的文章
	 */
	public function get_list_last($request)
	{
		$offset = $this->param_int($request, 'offset', 0);

		$args = [
			'posts_per_page' => ZhuiGe_Xcx::POSTS_PER_PAGE,
			'offset' => $offset,
			'orderby' => 'date',
			'post_type' => 'zhuige_bbs_topic',
			'ignore_sticky_posts' => 1,
		];

		$query = new WP_Query();
		$result = $query->query($args);
		$topics = [];
		foreach ($result as $post) {
			if ($post->post_type == 'zhuige_bbs_topic') {
				$item = zhuige_bbs_topic_format($post);
				$topics[] = $item;
			} else {
				$topics[] = $post;
			}
		}

		return $this->success([
			'topics' => $topics,
			'more' => (count($result) >= ZhuiGe_Xcx::POSTS_PER_PAGE ? 'more' : 'nomore')
		]);
	}

	/**
	 * 获取我关注的贴子
	 */
	public function get_list_follow($request)
	{
		$my_user_id = get_current_user_id();
		if (!$my_user_id) {
			return $this->success([
				'topics' => [],
				'more' => 'nomore'
			]);
		}

		$offset = $this->param_int($request, 'offset', 0);

		global $wpdb;
		$table_follow_user = $wpdb->prefix . 'zhuige_xcx_follow_user';
		$user_ids = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT `follow_user_id` FROM `$table_follow_user` WHERE `user_id`=%d ORDER BY `id` DESC LIMIT %d, %d",
				$my_user_id,
				$offset,
				ZhuiGe_Xcx::POSTS_PER_PAGE
			)
		);
		if (empty($user_ids)) {
			return $this->success([
				'topics' => [],
				'more' => 'nomore'
			]);
		}

		if ($offset == 0) {
			$follow_user_ids = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT `follow_user_id` FROM `$table_follow_user` WHERE `user_id`=%d ORDER BY `id` DESC LIMIT %d, %d",
					$my_user_id,
					$offset,
					ZhuiGe_Xcx::POSTS_PER_PAGE
				)
			);
			$follow_user = ['title' => '我的关注'];
			$users = [];
			foreach ($follow_user_ids as &$user_id) {
				$item = [
					'user_id' => $user_id->follow_user_id,
					'nickname' => get_user_meta($user_id->follow_user_id, 'nickname', true),
					'avatar' => ZhuiGe_Xcx::user_avatar($user_id->follow_user_id),
					'post_count' => zhuige_xcx_user_post_count($user_id->follow_user_id),
					'fans_count' => zhuige_xcx_user_fans_count($user_id->follow_user_id),
				];

				$users[] = $item;
			}
			$follow_user['users'] = $users;
		}

		$args = [
			'posts_per_page' => ZhuiGe_Xcx::POSTS_PER_PAGE,
			'offset' => $offset,
			'orderby' => 'date',
			'post_type' => 'zhuige_bbs_topic',
			'ignore_sticky_posts' => 1,
			'author__in' => array_column($user_ids, 'follow_user_id')
		];

		$query = new WP_Query();
		$result = $query->query($args);
		$topics = [];
		foreach ($result as $post) {
			if ($post->post_type == 'zhuige_bbs_topic') {
				$item = zhuige_bbs_topic_format($post);
				$topics[] = $item;
			} else {
				$topics[] = $post;
			}
		}

		return $this->success([
			'follow_user' => $follow_user,
			'topics' => $topics,
			'more' => (count($result) >= ZhuiGe_Xcx::POSTS_PER_PAGE ? 'more' : 'nomore')
		]);
	}

	/**
	 * 根据标签查询文章
	 */
	public function get_list_tag($request)
	{
		$tag_id = $this->param_int($request, 'tag_id', 0);
		$offset = $this->param_int($request, 'offset', 0);

		$args = [
			'posts_per_page' => ZhuiGe_Xcx::POSTS_PER_PAGE,
			'offset' => $offset,
			'orderby' => 'date',
			'post_type' => 'zhuige_bbs_topic',
			'ignore_sticky_posts' => 1,
			'tax_query' => array(
				array(
					'taxonomy' => 'zhuige_bbs_topic_tag',
					'field' => 'id',
					'terms' => array($tag_id),
					'include_children' => true,
					'operator' => 'IN'
				),
			),
		];

		$query = new WP_Query();
		$result = $query->query($args);
		$topics = [];
		foreach ($result as $post) {
			if ($post->post_type == 'zhuige_bbs_topic') {
				$item = zhuige_bbs_topic_format($post);
				$topics[] = $item;
			} else {
				$topics[] = $post;
			}
		}

		return $this->success([
			'topics' => $topics,
			'more' => (count($result) >= ZhuiGe_Xcx::POSTS_PER_PAGE ? 'more' : 'nomore')
		]);
	}

	/**
	 * 搜索文章
	 */
	public function get_list_search($request)
	{
		$keyword = $this->param($request, 'keyword', '');
		$offset = $this->param_int($request, 'offset', 0);

		$args = [
			'posts_per_page' => ZhuiGe_Xcx::POSTS_PER_PAGE,
			'offset' => $offset,
			'orderby' => 'date',
			'post_type' => 'zhuige_bbs_topic',
			'ignore_sticky_posts' => 1,
		];

		if (!empty($keyword)) {
			$args['s'] = $keyword;
		}

		$query = new WP_Query();
		$result = $query->query($args);
		$topics = [];
		foreach ($result as $post) {
			if ($post->post_type == 'zhuige_bbs_topic') {
				$item = zhuige_bbs_topic_format($post);
				$topics[] = $item;
			} else {
				$topics[] = $post;
			}
		}

		return $this->success([
			'topics' => $topics,
			'more' => (count($result) >= ZhuiGe_Xcx::POSTS_PER_PAGE ? 'more' : 'nomore')
		]);
	}

	/**
	 * 获取页面详情
	 */
	public function get_post_page($request)
	{
		$page_id = $this->param_int($request, 'page_id');
		if (!$page_id) {
			return $this->error('缺少参数');
		}

		global $wpdb;
		$table_post = $wpdb->prefix . 'posts';
		$result = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT post_title, post_content FROM `$table_post` WHERE ID=%d",
				$page_id
			)
		);
		if (!$result) {
			return $this->error('未找到文章');
		}
		$page['title'] = $result->post_title;
		$page['content'] = apply_filters('the_content', $result->post_content);

		// 页面查看钩子
		do_action('zhuige_xcx_page_view', [
			'page_id' => $page_id
		]);

		return $this->success($page);
	}

	/**
	 * 用户的文章
	 */
	public function get_list_user($request)
	{
		$user_id = $this->param_int($request, 'user_id', 0);
		$my_user_id = get_current_user_id();
		if (!$user_id) {
			$user_id = $my_user_id;
		}
		if (!$user_id) {
			return $this->error('缺少参数');
		}

		$tab = $this->param($request, 'tab', '');
		if (empty($tab)) {
			return $this->error('缺少参数');
		}

		$offset = $this->param_int($request, 'offset', 0);

		$topics = [];
		if ($tab == 'publish') {
			$args = [
				'posts_per_page' => ZhuiGe_Xcx::POSTS_PER_PAGE,
				'offset' => $offset,
				'post_type' => 'zhuige_bbs_topic',
				'ignore_sticky_posts' => 1,
				'author' => $user_id,
			];

			$query = new WP_Query();
			$result = $query->query($args);
			foreach ($result as $post) {
				if ($post->post_type == 'zhuige_bbs_topic') {
					$topics[] = zhuige_bbs_topic_format($post);
				} else {
					$topics[] = $post;
				}
			}

			return $this->success([
				'posts' => $topics,
				'more' => (count($result) >= ZhuiGe_Xcx::POSTS_PER_PAGE ? 'more' : 'nomore')
			]);
		}

		global $wpdb;
		$post_ids = [];
		if ($tab == 'like') {
			$table_post_like = $wpdb->prefix . 'zhuige_xcx_post_like';
			$post_ids = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT `post_id` FROM `$table_post_like` WHERE `post_status`='publish' AND `user_id`=%d ORDER BY `id` DESC LIMIT %d, %d",
					$user_id,
					$offset,
					ZhuiGe_Xcx::POSTS_PER_PAGE
				)
			);
		} else if ($tab == 'fav') {
			$table_post_favorite = $wpdb->prefix . 'zhuige_xcx_post_favorite';
			$post_ids = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT `post_id` FROM `$table_post_favorite` WHERE `post_status`='publish' AND `user_id`=%d ORDER BY `id` DESC LIMIT %d, %d",
					$user_id,
					$offset,
					ZhuiGe_Xcx::POSTS_PER_PAGE
				)
			);
		} else if ($tab == 'comment') {
			$table_comments = $wpdb->prefix . 'comments';
			$post_ids = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT `comment_post_ID` FROM `$table_comments` WHERE `comment_approved`='1' AND `user_id`=%d ORDER BY `comment_ID` DESC LIMIT %d, %d",
					$user_id,
					$offset,
					ZhuiGe_Xcx::POSTS_PER_PAGE
				)
			);
		}

		if (!empty($post_ids)) {
			$args = [
				'posts_per_page' => -1,
				'offset' => 0,
				'post_type' => 'zhuige_bbs_topic',
				'ignore_sticky_posts' => 1,
				'post__in' => array_column($post_ids, 'post_id'),
				'orderby' => 'post__in',
			];

			$query = new WP_Query();
			$result = $query->query($args);
			foreach ($result as $post) {
				if ($post->post_type == 'zhuige_bbs_topic') {
					$item = zhuige_bbs_topic_format($post);
					$topics[] = $item;
				} else {
					$topics[] = $post;
				}
			}
		}

		return $this->success([
			'post_ids' => $post_ids,
			'posts' => $topics,
			'more' => (count($result) >= ZhuiGe_Xcx::POSTS_PER_PAGE ? 'more' : 'nomore')
		]);
	}


	/**
	 * 获取小程序码
	 */
	public function get_wxacode($request)
	{
		$post_id = $this->param_int($request, 'post_id', 0);
		if (!$post_id) {
			return $this->error('缺少参数');
		}

		$uploads = wp_upload_dir();
		$qrcode_path = $uploads['basedir'] . '/zhuige_wxacode/';
		if (!is_dir($qrcode_path)) {
			mkdir($qrcode_path, 0755);
		}

		$qrcode = $qrcode_path . $post_id . '.png';
		$qrcode_link = $uploads['baseurl'] . '/zhuige_wxacode/' . $post_id . '.png';
		if (is_file($qrcode)) {
			return $this->success(['acode' => $qrcode_link]);
		}

		$wx_session = ZhuiGe_Xcx::get_wx_token();
		if (!$wx_session) {
			return $this->success(['acode' => ZHUIGE_XCX_BASE_URL . 'public/images/placeholder.jpg']);
		}

		$access_token = $wx_session['access_token'];

		$api = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=' . $access_token;

		$color = array(
			"r" => "0",  //这个颜色码自己到Photoshop里设
			"g" => "0",  //这个颜色码自己到Photoshop里设
			"b" => "0",  //这个颜色码自己到Photoshop里设
		);

		$post = get_post($post_id);
		if ($post->post_type == 'zhuige_bbs_topic') {
			$page = 'pages/bbs/detail/detail';
		} else {
			return $this->success(['acode' => ZHUIGE_XCX_BASE_URL . 'public/images/placeholder.jpg']);
		}

		$data = array(
			'scene' => $post_id, //TODO 自定义信息，可以填写诸如识别用户身份的字段，注意用中文时的情况
			'page' => $page, // 前端传过来的页面path,不能为空，最大长度 128 字节
			// 'width' => 200, // 尺寸 单位px 默认430
			'auto_color' => false, // 自动配置线条颜色，如果颜色依然是黑色，则说明不建议配置主色调
			'line_color' => $color, // auth_color 为 false 时生效，使用 rgb 设置颜色 例如 {"r":"xxx","g":"xxx","b":"xxx"},十进制表示
			'is_hyaline' => false, // 是否需要透明底色， is_hyaline 为true时，生成透明底色的小程序码
		);

		$args = array(
			'method'  => 'POST',
			'body' 	  => wp_json_encode($data),
			'headers' => array(),
			'cookies' => array()
		);

		$remote = wp_remote_post($api, $args);
		if (is_wp_error($remote)) {
			return $this->error('系统异常');
		}

		$content = wp_remote_retrieve_body($remote);
		if (strstr($content, 'errcode') !== false || strstr($content, 'errmsg') !== false) {
			// $json = json_decode($content, TRUE);
			// return $this->error($json['errmsg']);
			return $this->success(['acode' => ZHUIGE_XCX_BASE_URL . 'public/images/wxacode.jpg']);
		}

		//输出二维码
		file_put_contents($qrcode, $content);

		//同步到媒体库
		$res = zhuige_xcx_import_image2attachment($qrcode);
		if (!is_wp_error($res)) {
			$qrcode_link = $uploads['baseurl'] . '/zhuige_wxacode/' . $res;
		}

		return $this->success(['acode' => $qrcode_link]);
	}

	/**
	 * 获取QQ小程序码
	 */
	public function get_qqacode($request)
	{
		$post_id = (int)($this->param($request, 'post_id', 0));
		if (!$post_id) {
			return $this->error('缺少参数');
		}

		$uploads = wp_upload_dir();
		$qrcode_path = $uploads['basedir'] . '/zhuige_qqacode/';
		if (!is_dir($qrcode_path)) {
			mkdir($qrcode_path, 0755);
		}

		$qrcode = $qrcode_path . $post_id . '.png';
		$qrcode_link = $uploads['baseurl'] . '/zhuige_qqacode/' . $post_id . '.png';
		if (is_file($qrcode)) {
			return $this->success(['acode' => $qrcode_link]);
		}

		$qq_session = ZhuiGe_Xcx::get_qq_token();
		$access_token = $qq_session['access_token'];
		if (empty($access_token)) {
			return $this->error('获取二维码失败');
		}

		$api = 'https://api.q.qq.com/api/json/qqa/CreateMiniCode?access_token=' . $access_token;

		$post = get_post($post_id);
		if ($post->post_type == 'zhuige_bbs_topic') {
			$path = "pages/bbs/detail/detail?topic_id=$post_id";
		} else {
			return $this->success(['acode' => ZHUIGE_XCX_BASE_URL . 'public/images/placeholder.jpg']);
		}

		$qq = ZhuiGe_Xcx::option_value('basic_qq');
		$data = array(
			'appid' => $qq ? $qq['appid'] : '',
			'path' => $path,
		);

		$args = array(
			'method'  => 'POST',
			'body' 	  => wp_json_encode($data),
			'headers' => array(
				'Content-Type' => 'application/json'
			),
			'cookies' => array()
		);

		$remote = wp_remote_post($api, $args);
		if (is_wp_error($remote)) {
			return $this->error('系统异常');
		}

		$content = wp_remote_retrieve_body($remote);
		if (strstr($content, 'errcode') !== false || strstr($content, 'errmsg') !== false) {
			return $this->success(['acode' => ZHUIGE_XCX_BASE_URL . 'public/images/qqacode.jpg']);
		}

		//输出二维码
		file_put_contents($qrcode, $content);

		//同步到媒体库
		$res = zhuige_xcx_import_image2attachment($qrcode);
		if (!is_wp_error($res)) {
			$qrcode_link = $uploads['baseurl'] . '/zhuige_qqacode/' . $res;
		}

		return $this->success(['acode' => $qrcode_link]);
	}

	/**
	 * 获取百度小程序码
	 */
	public function get_bdacode($request)
	{
		$post_id = (int)($this->param($request, 'post_id', 0));
		if (!$post_id) {
			return $this->error('缺少参数');
		}

		$uploads = wp_upload_dir();
		$qrcode_path = $uploads['basedir'] . '/zhuige_bdacode/';
		if (!is_dir($qrcode_path)) {
			mkdir($qrcode_path, 0755);
		}

		$qrcode = $qrcode_path . $post_id . '.png';
		$qrcode_link = $uploads['baseurl'] . '/zhuige_bdacode/' . $post_id . '.png';
		if (is_file($qrcode)) {
			return $this->success(['acode' => $qrcode_link]);
		}

		$wx_session = ZhuiGe_Xcx::get_bd_token();
		$access_token = $wx_session['access_token'];
		if (empty($access_token)) {
			return $this->error('获取二维码失败');
		}

		$api = 'https://openapi.baidu.com/rest/2.0/smartapp/qrcode/getunlimited?access_token=' . $access_token;

		$post = get_post($post_id);
		if ($post->post_type == 'zhuige_bbs_topic') {
			$path = "pages/bbs/detail/detail?topic_id=$post_id";
		} else {
			return $this->success(['acode' => ZHUIGE_XCX_BASE_URL . 'public/images/placeholder.jpg']);
		}

		$data = array(
			'path' => $path,
			// 'width' => 430, 尺寸 默认430
			// 'mf' => 1 是否包含logo 1001不包含 默认包含
		);

		$args = array(
			'method'  => 'POST',
			'body' 	  => $data,
			'headers' => array(),
			'cookies' => array()
		);

		$remote = wp_remote_post($api, $args);
		if (is_wp_error($remote)) {
			return $this->error('系统异常');
		}

		$content = wp_remote_retrieve_body($remote);
		if (strstr($content, 'errno') !== false || strstr($content, 'errmsg') !== false) {
			return $this->success(['acode' => ZHUIGE_XCX_BASE_URL . 'public/images/placeholder.jpg']);
		}

		//输出二维码
		file_put_contents($qrcode, $content);

		//同步到媒体库
		$res = zhuige_xcx_import_image2attachment($qrcode);
		if (!is_wp_error($res)) {
			$qrcode_link = $uploads['baseurl'] . '/zhuige_bdacode/' . $res;
		}

		return $this->success(['acode' => $qrcode_link]);
	}
}

ZhuiGe_Xcx::$rest_controllers[] = new ZhuiGe_Xcx_Post_Controller();
