<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
 */

class ZhuiGe_Xcx_Bbs_Forum_Controller extends ZhuiGe_Xcx_Base_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->module = 'bbs';

		$this->routes = [
			'setting_forum' => 'get_setting_forum',
			'setting_subject' => 'get_setting_subject',
			'setting_forum_create_pre' => ['callback' => 'setting_forum_create_pre', 'auth' => 'login'],

			'forum_cats' => 'get_forum_cats',

			'list_my' => 'get_list_my',
			'list_rec' => 'get_list_rec',
			'list_cat' => 'get_list_cat',

			'forum_create' => ['callback' => 'forum_create', 'auth' => 'login'],

			'forum_detail' => 'get_forum_detail',
			'forum_users' => 'get_forum_users'
		];
	}

	/**
	 * 圈子 配置
	 */
	public function get_setting_forum($request)
	{
		// 幻灯片
		$slides_org = ZhuiGe_Xcx::option_value('bbs_slide');
		$slides = [];
		if (is_array($slides_org)) {
			foreach ($slides_org as $item) {
				if ($item['switch'] && $item['image'] && $item['image']['url']) {
					$slides[] = [
						'image' => $item['image']['url'],
						'link' => $item['link'],
					];
				}
			}
		}
		$data['slides'] = $slides;

		// 导航
		$term_args = [
			'taxonomy' => 'zhuige_bbs_forum_cat',
			'hide_empty' => false
		];
		$cat_ids = ZhuiGe_Xcx::option_value('bbs_nav_cat');
		if (!empty($cat_ids)) {
			$term_args['include'] = $cat_ids;
			$term_args['orderby'] = 'include';
		}
		$terms = get_terms($term_args);
		$tabs = [];
		$my_user_id = get_current_user_id();
		if ($my_user_id) {
			$tabs[] = ['id' => 'my', 'name' => '我的圈子'];
			$data['cur_tab'] = 'my';
		} else {
			$data['cur_tab'] = 'rec';
		}
		$tabs[] = ['id' => 'rec', 'name' => '推荐圈子'];
		foreach ($terms as $term) {
			$tabs[] = [
				'id' => $term->term_id,
				'name' => $term->name
			];
		}
		$data['tabs'] = $tabs;


		// 是否显示创建圈子按钮
		$is_show_create_forum = 1;
		if (ZhuiGe_Xcx_Addon::is_active('zhuige-auth')) {
			$auth_create_forum = ZhuiGe_Xcx::option_value('auth_create_forum');
			$is_show_create_forum = ($auth_create_forum != 'none') ? 1 : 0;
		}
		$data['is_show_create_forum'] = $is_show_create_forum;


		return $this->success($data);
	}

	/**
	 * 话题 配置
	 */
	public function get_setting_subject($request)
	{
		$hots = [];
		$bbs_subject_hot = ZhuiGe_Xcx::option_value('bbs_subject_hot');
		if (!empty($bbs_subject_hot)) {
			$terms = get_terms(['taxonomy' => 'zhuige_bbs_topic_tag', 'hide_empty' => false, 'include' => $bbs_subject_hot, 'orderby' => 'include']);
			foreach ($terms as $term) {
				$hots[] = [
					'id' => $term->term_id,
					'name' => $term->name,
					'class' => ''
				];
			}
		}

		$recs = [];
		$bbs_subject_rec = ZhuiGe_Xcx::option_value('bbs_subject_rec');
		if (!empty($bbs_subject_rec)) {
			$terms = get_terms(['taxonomy' => 'zhuige_bbs_topic_tag', 'hide_empty' => false, 'include' => $bbs_subject_rec, 'orderby' => 'include']);
			foreach ($terms as $term) {
				$recs[] = [
					'id' => $term->term_id,
					'name' => $term->name,
					'class' => ''
				];
			}
		}

		$alls = [];
		$terms = get_terms(['taxonomy' => 'zhuige_bbs_topic_tag', 'hide_empty' => false]);
		foreach ($terms as $term) {
			$options = get_term_meta($term->term_id, 'zhuige_bbs_topic_tag_options', true);
			if ($options['switch']) {
				$alls[] = [
					'id' => $term->term_id,
					'name' => $term->name,
					'class' => ''
				];
			}
		}

		return $this->success([
			'hots' => $hots,
			'recs' => $recs,
			'alls' => $alls,
		]);
	}

	/**
	 * 创建圈子的设置
	 */
	public function setting_forum_create_pre($request)
	{
		$my_user_id = get_current_user_id();
		if (function_exists('zhuige_auth_is_black') && zhuige_auth_is_black($my_user_id)) {
			return $this->error('操作太频繁了~');
		}

		if (!$this->_auth_create_forum($my_user_id)) {
			return $this->error('无创建圈子权限~');
		}

		if (ZhuiGe_Xcx::option_value('bbs_forum_mobile_switch')) {
			$mobile = get_user_meta($my_user_id, 'zhuige_xcx_user_mobile', true);
			if (empty($mobile)) {
				return $this->error('', 'require_mobile');
			}
		}

		// 分类
		$term_args = [
			'taxonomy' => 'zhuige_bbs_forum_cat',
			'hide_empty' => false
		];
		$terms = get_terms($term_args);
		if (empty($terms)) {
			return $this->error('请在后台添加圈子分类~');
		}
		$tabs = [];
		foreach ($terms as $term) {
			$tabs[] = [
				'id' => $term->term_id,
				'name' => $term->name
			];
		}
		$data['cats'] = $tabs;

		// 协议
		$licence = ZhuiGe_Xcx::option_value('bbs_forum_create_licence');
		if ($licence) {
			$data['licence'] = '/pages/base/page/page?page_id=' . $licence;
		}

		return $this->success($data);
	}

	/**
	 * 创建圈子
	 */
	public function forum_create($request)
	{
		$my_user_id = get_current_user_id();

		if (function_exists('zhuige_auth_is_black') && zhuige_auth_is_black($my_user_id)) {
			return $this->error('操作太频繁了~');
		}

		if (!$this->_auth_create_forum($my_user_id)) {
			return $this->error('无创建圈子权限~');
		}

		if (ZhuiGe_Xcx::option_value('bbs_forum_mobile_switch')) {
			$mobile = get_user_meta($my_user_id, 'zhuige_xcx_user_mobile', true);
			if (empty($mobile)) {
				return $this->error('', 'require_mobile');
			}
		}

		$logo = $this->param($request, 'logo', '');
		if (!$logo) {
			return $this->error('请设置圈子logo');
		}

		$name = $this->param($request, 'name', '');
		if (!$name) {
			return $this->error('请输入圈子名字');
		}

		$brief = $this->param($request, 'brief', '');
		if (!$brief) {
			return $this->error('请输入圈子简介');
		}

		// 检查敏感信息
		$os = $this->param($request, 'os', 'wx');
		if (!$this->msg_sec_check($name . $brief, $os)) {
			return $this->error('请勿发布敏感信息');
		}

		$cat_id = (int)($this->param($request, 'cat_id', ''));
		if (!$cat_id) {
			return $this->error('请选择分类');
		}

		$status = 'pending'; //必须人工审核，以防垃圾信息
		// $status = 'publish';
		$post = array(
			'post_title' 	=> $name,
			'post_content' 	=> $brief,
			'post_status' 	=> $status,
			'post_type' 	=> 'zhuige_bbs_forum',
			'post_author' 	=> $my_user_id,
			'tax_input'   	=> array(
				"zhuige_bbs_forum_cat" => array($cat_id)
			),
		);

		$post_id = wp_insert_post($post);
		if (!$post_id) {
			return $this->error('系统异常');
		}

		$marker = $this->param($request, 'marker', '');
		$address = $this->param($request, 'address', '');
		$longitude = $this->param($request, 'longitude', '0');
		$latitude = $this->param($request, 'latitude', '0');
		$options['location'] = [
			'marker' => $marker,
			'address' => $address,
			'longitude' => $longitude,
			'latitude' => $latitude,
		];

		$options['logo'] = json_decode($logo, true);
		$options['notice'] = $brief;
		update_post_meta($post_id, 'zhuige-bbs-forum-option', $options);

		return $this->success();
	}

	/**
	 * 所有圈子分类
	 */
	public function get_forum_cats($request)
	{
		// 导航
		$term_args = [
			'taxonomy' => 'zhuige_bbs_forum_cat',
			'hide_empty' => false
		];
		$cat_ids = ZhuiGe_Xcx::option_value('bbs_nav_cat');
		if (!empty($cat_ids)) {
			$term_args['include'] = $cat_ids;
			$term_args['orderby'] = 'include';
		}
		$terms = get_terms($term_args);
		$tabs = [];
		foreach ($terms as $term) {
			$tabs[] = [
				'id' => $term->term_id,
				'name' => $term->name
			];

			if (!isset($data['cur_tab'])) {
				$data['cur_tab'] = $term->term_id;
			}
		}
		$data['tabs'] = $tabs;

		return $this->success($data);
	}

	/**
	 * 获取我的圈子
	 */
	public function get_list_my($request)
	{
		$stat = $this->param_int($request, 'stat', 0);

		$my_user_id = get_current_user_id();
		if (!$my_user_id) {
			return $this->success(['forums' => []]);
		}

		$forums = [];

		// 我创建的圈子
		$query = new WP_Query();
		$result = $query->query([
			'posts_per_page' => -1,
			'post_type' => ['zhuige_bbs_forum'],
			'author' => $my_user_id
		]);
		foreach ($result as $item) {
			$forums[] = $this->_format_forum($item, $stat);
		}

		// 我加入的圈子
		global $wpdb;
		$table_forum_users = $wpdb->prefix . 'zhuige_bbs_forum_users';
		$forum_ids = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT `forum_id` FROM `$table_forum_users` WHERE user_id=%d",
				$my_user_id
			),
			ARRAY_A
		);

		if (!empty($forum_ids)) {
			$forum_ids = array_column($forum_ids, 'forum_id');
			$result = $query->query([
				'posts_per_page' => -1,
				'post_type' => ['zhuige_bbs_forum'],
				'post__in' => $forum_ids
			]);

			foreach ($result as $item) {
				$forums[] = $this->_format_forum($item, $stat);
			}
		}

		return $this->success(['forums' => $forums]);
	}

	/**
	 * 获取推荐的圈子
	 */
	public function get_list_rec($request)
	{
		$stat = $this->param_int($request, 'stat', 0);

		$query = new WP_Query();
		$result = $query->query([
			'posts_per_page' => -1,
			'post_type' => ['zhuige_bbs_forum'],
			'post__in' => ZhuiGe_Xcx::option_value('bbs_forum_rec'),
			'orderby' => 'post__in'
		]);

		$forums = [];
		foreach ($result as $item) {
			$forums[] = $this->_format_forum($item, $stat);
		}

		return $this->success(['forums' => $forums]);
	}

	/**
	 * 获取指定分类下的圈子
	 */
	public function get_list_cat($request)
	{
		$cat_id = $this->param_int($request, 'cat_id', 0);
		$stat = $this->param_int($request, 'stat', 0);

		if (empty($cat_id)) {
			return $this->error('缺少参数');
		}

		$args = [
			'posts_per_page' => -1,
			'orderby' => 'date',
			'post_type' => ['zhuige_bbs_forum'],
			'tax_query' => array(
				array(
					'taxonomy' => 'zhuige_bbs_forum_cat', //(字符串) - 自定义分类法
					'field' => 'id',
					'terms' => array($cat_id),
					'include_children' => true,
					'operator' => 'IN'
				),
			),
		];

		$query = new WP_Query();
		$result = $query->query($args);

		$forums = [];
		foreach ($result as $item) {
			$forums[] = $this->_format_forum($item, $stat);
		}

		return $this->success(['forums' => $forums]);
	}

	/**
	 * 圈子详情
	 */
	public function get_forum_detail($request)
	{
		$forum_id = $this->param_int($request, 'forum_id', 0);

		if (empty($forum_id)) {
			return $this->error('缺少参数');
		}

		$post = get_post($forum_id);

		$forum = [
			'id' => $post->ID,
			'name' => $post->post_title
		];

		global $wpdb;
		$table_zhuige_bbs_forum_users = $wpdb->prefix . 'zhuige_bbs_forum_users';

		$forum['user_count'] = zhuige_bbs_forum_user_count($forum_id);
		$forum['post_count'] = zhuige_bbs_forum_topic_count($forum_id);

		$my_user_id = get_current_user_id();
		if ($my_user_id) {
			$follow_forum_id_exist = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT id FROM `$table_zhuige_bbs_forum_users` WHERE `user_id`=%d AND `forum_id`=%d",
					$my_user_id,
					$forum_id
				)
			);
			$forum['is_follow'] = ($follow_forum_id_exist ? 1 : 0);
		} else {
			$forum['is_follow'] = 0;
		}

		// 圈子成员
		$users = [];
		$owner = [
			'user_id' => $post->post_author,
			'nickname' => get_user_meta($post->post_author, 'nickname', true),
			'avatar' => ZhuiGe_Xcx::user_avatar($post->post_author),
			'owner' => 1,
		];

		if (function_exists('zhuige_xcx_certify_is_certify')) {
			$owner['certify'] = zhuige_xcx_certify_is_certify($post->post_author);
		}

		if (function_exists('zhuige_xcx_vip_is_vip')) {
			$owner['vip'] = zhuige_xcx_vip_is_vip($post->post_author);
		}

		$users[] = $owner;

		$user_ids = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT `user_id` FROM `$table_zhuige_bbs_forum_users` WHERE `forum_id`=%d ORDER BY `id` DESC LIMIT 10",
				$forum_id
			)
		);
		$user_ids = array_column($user_ids, 'user_id');
		foreach ($user_ids as $user_id) {
			$item = [
				'user_id' => $user_id,
				'nickname' => get_user_meta($user_id, 'nickname', true),
				'avatar' => ZhuiGe_Xcx::user_avatar($user_id),
				'owner' => 0
			];

			if (function_exists('zhuige_xcx_certify_is_certify')) {
				$item['certify'] = zhuige_xcx_certify_is_certify($user_id);
			}

			if (function_exists('zhuige_xcx_vip_is_vip')) {
				$item['vip'] = zhuige_xcx_vip_is_vip($user_id);
			}

			$users[] = $item;
		}
		$forum['users'] = $users;


		$options = get_post_meta($forum_id, 'zhuige-bbs-forum-option', true);

		$forum['logo'] = ZhuiGe_Xcx::option_image_url($options['logo'], 'placeholder.jpg');
		$forum['background'] = ZhuiGe_Xcx::option_image_url($options['background'], 'placeholder.jpg');
		$forum['notice'] = $options['notice'];
		$forum['ad_link'] = $options['ad_link'];
		$forum['location'] = $options['location'];

		$ad_custom = [];
		foreach ($options['ad_custom'] as $item) {
			if ($item['switch']) {
				$ad_custom[] = [
					'title' => $item['title'],
					'image' => ZhuiGe_Xcx::option_image_url($item['image']),
					'badge' => $item['badge'],
					'link' => $item['link'],
				];
			}
		}
		$forum['ad_custom'] = $ad_custom;

		$ad_menu = [];
		foreach ($options['ad_menu'] as $item) {
			if ($item['switch']) {
				$ad_menu[] = [
					'title' => $item['title'],
					'link' => $item['link'],
				];
			}
		}
		$forum['ad_menu'] = $ad_menu;

		//广告
		$forum_ad_imgs = $options['ad_imgs'];
		if ($forum_ad_imgs && $forum_ad_imgs['switch']) {
			$ad_imgs['title'] = $forum_ad_imgs['title'];
			$items = [];
			foreach ($forum_ad_imgs['items'] as $item) {
				if ($item['switch']) {
					$items[] = [
						'title' => $item['title'],
						'badge' => $item['badge'],
						'image' => $item['image']['url'],
						'link' => $item['link'],
						'price' => $item['price'],
					];
				}
			}
			$ad_imgs['items'] = $items;

			$forum['ad_imgs'] = $ad_imgs;
		}

		$data = ['forum' => $forum];

		// 微信广告
		if (ZhuiGe_Xcx_Addon::is_active('zhuige-traffic')) {
			$traffic_forum_list = ZhuiGe_Xcx::option_value('traffic_forum_list');
			if ($traffic_forum_list && $traffic_forum_list['switch']) {
				unset($traffic_forum_list['switch']);
				$data['traffic_list'] = $traffic_forum_list;
			}
		}

		return $this->success($data);
	}

	/**
	 * 圈子成员
	 */
	public function get_forum_users($request)
	{
		$forum_id = $this->param_int($request, 'forum_id', 0);
		if (!$forum_id) {
			return $this->error('缺少参数');
		}

		$offset = $this->param_int($request, 'offset', 0);

		$data = [];

		global $wpdb;
		$owner_id = 0;
		if ($offset == 0) {
			$post = get_post($forum_id);
			$owner_id = $post->post_author;
			$owner = [
				'user_id' => $owner_id,
				'nickname' => get_user_meta($owner_id, 'nickname', true),
				'avatar' => ZhuiGe_Xcx::user_avatar($owner_id),
			];

			if (function_exists('zhuige_xcx_certify_is_certify')) {
				$owner['certify'] = zhuige_xcx_certify_is_certify($owner_id);
			}

			$my_user_id = get_current_user_id();
			$table_follow_user = $wpdb->prefix . 'zhuige_xcx_follow_user';
			if ($my_user_id) {
				$follow_user = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT id FROM `$table_follow_user` WHERE user_id=%d AND follow_user_id=%d",
						$owner_id,
						$my_user_id
					)
				);
				$owner['is_fans'] = ($follow_user ? 1 : 0);

				$follow_user = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT id FROM `$table_follow_user` WHERE user_id=%d AND follow_user_id=%d",
						$my_user_id,
						$owner_id
					)
				);
				$owner['is_follow'] = ($follow_user ? 1 : 0);
			} else {
				$owner['is_fans'] = 0;
				$owner['is_follow'] = 0;
			}

			$data['owner'] = $owner;
		}

		$users = [];
		$table_zhuige_bbs_forum_users = $wpdb->prefix . 'zhuige_bbs_forum_users';
		$user_ids = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT `user_id` FROM `$table_zhuige_bbs_forum_users` WHERE forum_id=%d ORDER BY `id` DESC LIMIT %d, %d",
				$forum_id,
				$offset,
				ZhuiGe_Xcx::POSTS_PER_PAGE
			),
			ARRAY_A
		);

		foreach ($user_ids as $user_id) {
			if ($user_id['user_id'] == $owner_id) {
				continue;
			}

			$user = [
				'user_id' => $user_id['user_id'],
				'nickname' => get_user_meta($user_id['user_id'], 'nickname', true),
				'avatar' => ZhuiGe_Xcx::user_avatar($user_id['user_id']),
			];

			if (function_exists('zhuige_xcx_certify_is_certify')) {
				$user['certify'] = zhuige_xcx_certify_is_certify($user_id['user_id']);
			}

			if ($my_user_id) {
				$follow_user = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT id FROM `$table_follow_user` WHERE user_id=%d AND follow_user_id=%d",
						$user_id['user_id'],
						$my_user_id
					)
				);
				$user['is_fans'] = ($follow_user ? 1 : 0);

				$follow_user = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT id FROM `$table_follow_user` WHERE user_id=%d AND follow_user_id=%d",
						$my_user_id,
						$user_id['user_id']
					)
				);
				$user['is_follow'] = ($follow_user ? 1 : 0);
			} else {
				$user['is_fans'] = 0;
				$user['is_follow'] = 0;
			}

			$users[] = $user;
		}

		$data['users'] = $users;
		$data['more'] = (count($users) >= ZhuiGe_Xcx::POSTS_PER_PAGE ? 'more' : 'nomore');

		return $this->success($data);
	}


	/**
	 * 格式化圈子
	 */
	private function _format_forum($item, $stat)
	{
		$forum = [
			'id' => $item->ID,
			'title' => $item->post_title,
		];

		$options = get_post_meta($item->ID, 'zhuige-bbs-forum-option', true);
		$forum['logo'] = ZhuiGe_Xcx::option_image_url($options['logo'], 'placeholder.jpg');

		if ($stat) {
			$forum['user_count'] = zhuige_bbs_forum_user_count($item->ID);
			$forum['post_count'] = zhuige_bbs_forum_topic_count($item->ID);
		}

		return $forum;
	}

	/**
	 * 是否有创建圈子的权限
	 */
	private function _auth_create_forum($user_id)
	{
		if (ZhuiGe_Xcx_Addon::is_active('zhuige-auth')) {
			$auth_create_forum = ZhuiGe_Xcx::option_value('auth_create_forum');
			if ($auth_create_forum == 'all') {
				return 1;
			} else if ($auth_create_forum == 'vip') {
				if ($user_id && function_exists('zhuige_xcx_vip_is_vip')) {
					$cerify = zhuige_xcx_vip_is_vip($user_id);
					if ($cerify['status'] == 1) {
						return 1;
					}
				}
			}

			return 0;
		} else {
			return 1;
		}
	}
}

ZhuiGe_Xcx::$rest_controllers[] = new ZhuiGe_Xcx_Bbs_Forum_Controller();
