<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
 */

class ZhuiGe_Xcx_Setting_Controller extends ZhuiGe_Xcx_Base_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->module = 'setting';
		$this->routes = [
			'global' => 'get_global',
			'home' => 'get_home',
			'create' => 'get_create',
			'login' => 'get_login',
			'search' => 'get_search',
			'mine' => 'get_mine',
			'about' => 'get_about',
		];
	}

	/**
	 * 获取配置 全局
	 */
	public function get_global($request)
	{
		//小程序名称
		$data['title'] = ZhuiGe_Xcx::option_value('basic_title', '追格圈子');

		//描述
		$data['desc'] = ZhuiGe_Xcx::option_value('basic_desc', '');

		// LOGO
		$basic_logo = ZhuiGe_Xcx::option_value('basic_logo');
		$data['logo'] = ZhuiGe_Xcx::option_image_url($basic_logo, 'zhuige2.png');

		// 是否有通知
		$my_user_id = get_current_user_id();
		$notify_count = 0;
		if ($my_user_id) {
			global $wpdb;
			$table_notify = $wpdb->prefix . 'zhuige_xcx_notify';
			$notify_count = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(`id`) FROM `$table_notify` WHERE `to_id`=%d AND `post_status`='publish' AND `isread`=0",
					$my_user_id
				)
			);

			if ($notify_count == 0 && ZhuiGe_Xcx_Addon::is_active('zhuige-system_notice')) {
				$table_system_notice_notify = $wpdb->prefix . 'zhuige_xcx_system_notice_notify';
				$notify_count = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT COUNT(`id`) FROM `$table_system_notice_notify` WHERE `user_id`=%d AND `isread`='0'",
						$my_user_id
					)
				);
			}

			if ($notify_count == 0 && ZhuiGe_Xcx_Addon::is_active('zhuige-message')) {
				$table_message = $wpdb->prefix . 'zhuige_xcx_message';
				$notify_count = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT COUNT(`id`) FROM `$table_message` WHERE `to_id`=%d AND `isread`='0'",
						$my_user_id
					)
				);
			}
		}
		$data['notify_count'] = (int)$notify_count;

		return $this->success($data);
	}

	/**
	 * 获取配置 首页
	 */
	public function get_home($request)
	{
		//小程序名称
		$data['title'] = ZhuiGe_Xcx::option_value('basic_title', '追格圈子');

		//描述
		$data['desc'] = ZhuiGe_Xcx::option_value('basic_desc', '');

		// LOGO
		$basic_logo = ZhuiGe_Xcx::option_value('basic_logo');
		$data['logo'] = ZhuiGe_Xcx::option_image_url($basic_logo, 'zhuige2.png');

		// 幻灯片
		$slides_org = ZhuiGe_Xcx::option_value('rec_slide');
		$slides = [];
		if (is_array($slides_org)) {
			foreach ($slides_org as $item) {
				if ($item['switch'] && $item['image'] && $item['image']['url']) {
					$slides[] = [
						'title' => $item['title'],
						'image' => $item['image']['url'],
						'link' => $item['link'],
					];
				}
			}
		}
		$data['slides'] = $slides;

		//图标导航
		$icon_nav_org = ZhuiGe_Xcx::option_value('rec_nav');
		$icon_navs = [];
		if (is_array($icon_nav_org)) {
			foreach ($icon_nav_org as $item) {
				if ($item['switch'] && $item['image'] && $item['image']['url']) {
					$icon_navs[] = [
						'image' => $item['image']['url'],
						'link' => $item['link'],
						'title' => $item['title'],
					];
				}
			}
		}
		$data['icons'] = $icon_navs;


		// 热门话题
		$home_subject_hot = ZhuiGe_Xcx::option_value('home_subject_hot');
		if (!empty($home_subject_hot)) {
			$terms = get_terms(['taxonomy' => 'zhuige_bbs_topic_tag', 'hide_empty' => false, 'include' => $home_subject_hot, 'orderby' => 'include']);

			$subject_hots = [];
			foreach ($terms as $term) {
				$options = get_term_meta($term->term_id, 'zhuige_bbs_topic_tag_options', true);
				$subject_hots[] = [
					'id' => $term->term_id,
					'name' => $term->name,
					'badge' => $options['badge']
				];
			}

			$data['subject_hots'] = $subject_hots;
			$data['subject_hot_width'] = ZhuiGe_Xcx::option_value('home_subject_hot_width');
		}


		// 自定义广告
		if (ZhuiGe_Xcx_Addon::is_active('zhuige-ads')) {
			// 文字链接
			$home_ad_hot_link = ZhuiGe_Xcx::option_value('home_ad_hot_link');
			if ($home_ad_hot_link && $home_ad_hot_link['switch']) {
				if ($home_ad_hot_link['icon'] && $home_ad_hot_link['icon']['url']) {
					$hot_link['icon'] = $home_ad_hot_link['icon']['url'];
				}
				if (is_array($home_ad_hot_link['items'])) {
					$hot_link['items'] = $home_ad_hot_link['items'];
				}

				$data['hot_link'] = $hot_link;
			}

			// 图片链接
			$home_ad_imgs_top = ZhuiGe_Xcx::option_value('home_ad_imgs_top');
			if ($home_ad_imgs_top && $home_ad_imgs_top['switch']) {
				$imgs_top['title'] = $home_ad_imgs_top['title'];
				$items = [];
				foreach ($home_ad_imgs_top['items'] as $item) {
					if ($item['switch']) {
						$items[] = [
							'title' => $item['title'],
							'badge' => $item['badge'],
							'image' => $item['image']['url'],
							'link' => $item['link'],
						];
					}
				}
				$imgs_top['items'] = $items;

				$data['imgs_top'] = $imgs_top;
			}

			// 图片链接 - 帖子中
			$home_ad_imgs_embed = ZhuiGe_Xcx::option_value('home_ad_imgs_embed');
			if ($home_ad_imgs_embed && $home_ad_imgs_embed['switch']) {
				$imgs_embed['title'] = $home_ad_imgs_embed['title'];
				$items = [];
				foreach ($home_ad_imgs_embed['items'] as $item) {
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
				$imgs_embed['items'] = $items;
				$imgs_embed['position'] = $home_ad_imgs_embed['position'];

				$data['imgs_embed'] = $imgs_embed;
			}

			//弹框广告
			$home_ad_pop = ZhuiGe_Xcx::option_value('home_ad_pop');
			if ($home_ad_pop && $home_ad_pop['switch'] && $home_ad_pop['image'] && $home_ad_pop['image']['url']) {
				$data['pop_ad'] = [
					'image' => $home_ad_pop['image']['url'],
					'link' => $home_ad_pop['link'],
					'interval' => $home_ad_pop['interval'],
				];
			}
		}


		// 微信广告
		if (ZhuiGe_Xcx_Addon::is_active('zhuige-traffic')) {
			$traffic_index = ZhuiGe_Xcx::option_value('traffic_index');
			if ($traffic_index && $traffic_index['switch_chp']) {
				$data['traffic_chp'] = $traffic_index['ad_chp'];
			}

			$traffic_index_list = ZhuiGe_Xcx::option_value('traffic_index_list');
			if ($traffic_index_list && $traffic_index_list['switch']) {
				unset($traffic_index_list['switch']);
				$data['traffic_list'] = $traffic_index_list;
			}
		}


		// 推荐用户
		$rec_user = ZhuiGe_Xcx::option_value('home_rec_user');
		if ($rec_user && $rec_user['switch']) {
			$users = [];
			global $wpdb;
			$my_user_id = get_current_user_id();
			$table_follow_user = $wpdb->prefix . 'zhuige_xcx_follow_user';
			foreach ($rec_user['users'] as $user_id) {
				$user = [
					'user_id' => $user_id,
					'nickname' => get_user_meta($user_id, 'nickname', true),
					'avatar' => get_user_meta($user_id, 'zhuige_xcx_user_avatar', true),
					'post_count' => zhuige_xcx_user_post_count($user_id),
					'fans_count' => zhuige_xcx_user_fans_count($user_id),
				];

				if (function_exists('zhuige_xcx_certify_is_certify')) {
					$user['certify'] = zhuige_xcx_certify_is_certify($user_id);
				}

				if (function_exists('zhuige_xcx_vip_is_vip')) {
					$user['vip'] = zhuige_xcx_vip_is_vip($user_id);
				}

				$follow_user_id_exist = 0;
				if ($my_user_id) {
					$follow_user_id_exist = $wpdb->get_var(
						$wpdb->prepare(
							"SELECT id FROM `$table_follow_user` WHERE user_id=%d AND follow_user_id=%d",
							$my_user_id,
							$user_id
						)
					);
				}
				$user['is_follow'] = ($follow_user_id_exist ? 1 : 0);

				$users[] = $user;
			}
			$rec_user['users'] = $users;

			$data['rec_user'] = $rec_user;
		}

		// 是否显示tab
		$data['tab_switch'] = (int)(ZhuiGe_Xcx::option_value('rec_list_tab_switch'));

		// tab设置
		$tab_limit = ZhuiGe_Xcx::option_value('rec_list_tab_limit');
		if (!empty($tab_limit)) {
			$tabs = [['id' => 'any', 'title' => '全部']];
			$cur_tab = 'any';
			foreach ($tab_limit as $tab) {
				$type_info = $this->get_post_type_info($tab);
				$tabs[] = [
					'id' => $tab,
					'title' => $type_info['name']
				];
			}

			$data['tabs'] = $tabs;
			$data['cur_tab'] = $cur_tab;

			$data['tab_type'] = 1;
		} else {
			$data['tab_type'] = 2;
		}

		$data['list_switch'] = (int)(ZhuiGe_Xcx::option_value('rec_list_switch'));

		// 推荐文章
		$tab_limit = ZhuiGe_Xcx::option_value('rec_list_tab_limit');

		$home_rec_post = ZhuiGe_Xcx::option_value('home_rec_post');
		$rec_posts = [];
		if (is_array($home_rec_post)) {
			foreach ($home_rec_post as &$rec_post) {
				if ($rec_post['switch'] != '1') {
					continue;
				}

				$post_type = $rec_post['post_type'];

				$post_ids = [];
				if ($post_type == 'zhuige_column') {
					$post_ids = $rec_post['column_ids'];
				} else if ($post_type == 'zhuige_activity') {
					$post_ids = $rec_post['activity_ids'];
				} else if ($post_type == 'zhuige_bbs_topic') {
					$post_ids = $rec_post['bbs_topic_ids'];
				} else if ($post_type == 'zhuige_res') {
					$post_ids = $rec_post['resource_ids'];
				} else if ($post_type == 'zhuige_goods') {
					$post_ids = $rec_post['goods_ids'];
				} else if ($post_type == 'post') {
					$post_ids = $rec_post['post_ids'];
				} else if ($post_type == 'page') {
					$post_ids = $rec_post['page_ids'];
				} else if ($post_type == 'zhuige_bbs_forum') {
					$post_ids = $rec_post['forum_ids'];
				}

				$args = [
					'post__in' => $post_ids,
					'orderby' => 'post__in',
					'post_type' => $post_type,
					'ignore_sticky_posts' => 1,
				];

				$query = new WP_Query();
				$result = $query->query($args);
				$items = [];
				foreach ($result as $post) {
					if ($post->post_type == 'zhuige_column' && function_exists('zhuige_column_format')) {
						$post_type_info = $this->get_post_type_info($post->post_type);
						$item = zhuige_column_format($post);
						$item['post_type'] = $post->post_type;
						$item['more_link'] = $post->more_link;
						$item['post_type_name'] = $post_type_info['name'];
						$item['link'] = $post_type_info['link'];
						$items[] = $item;
					} else if ($post->post_type == 'post' && function_exists('zhuige_cms_post_format')) {
						$post_type_info = $this->get_post_type_info($post->post_type);
						$item = zhuige_cms_post_format($post);
						$item['post_type'] = $post->post_type;
						$item['more_link'] = $post->more_link;
						$item['post_type_name'] = $post_type_info['name'];
						$item['link'] = $post_type_info['link'];
						$items[] = $item;
					} else if ($post->post_type == 'zhuige_res' && function_exists('zhuige_res_post_format')) {
						$post_type_info = $this->get_post_type_info($post->post_type);
						$item = zhuige_res_post_format($post);
						$item['post_type'] = $post->post_type;
						$item['more_link'] = $post->more_link;
						$item['post_type_name'] = $post_type_info['name'];
						$item['link'] = $post_type_info['link'];
						$items[] = $item;
					} else if ($post->post_type == 'zhuige_activity' && function_exists('zhuige_activity_format')) {
						$post_type_info = $this->get_post_type_info($post->post_type);
						$item = zhuige_activity_format($post);
						$item['post_type'] = $post->post_type;
						$item['more_link'] = $post->more_link;
						$item['post_type_name'] = $post_type_info['name'];
						$item['link'] = $post_type_info['link'];
						$items[] = $item;
					} else if ($post->post_type == 'zhuige_goods' && function_exists('zhuige_goods_format')) {
						$post_type_info = $this->get_post_type_info($post->post_type);
						$item = zhuige_goods_format($post);
						$item['post_type'] = $post->post_type;
						$item['more_link'] = $post->more_link;
						$item['post_type_name'] = $post_type_info['name'];
						$item['link'] = $post_type_info['link'];
						$items[] = $item;
					} else if ($post->post_type == 'zhuige_bbs_forum') {
						$options = get_post_meta($post->ID, 'zhuige-bbs-forum-option', true);
						$logo = ZhuiGe_Xcx::option_image_url($options['logo'], 'placeholder.jpg');
						$user_count = zhuige_bbs_forum_user_count($post->ID);
						$items[] = [
							'title' => $post->post_title,
							'image' => $logo,
							'subtitle' => "成员 $user_count",
							'link' => '/pages/bbs/forum/forum?forum_id=' . $post->ID,
						];
					} else if ($post->post_type == 'zhuige_bbs_topic' && function_exists('zhuige_bbs_topic_thumb')) {
						$post_type_info = $this->get_post_type_info($post->post_type);
						$items[] = [
							'post_type' => $post->post_type,
							'more_link' => $post->more_link,
							'badge' => $post_type_info['name'],
							'link' => $post_type_info['link'] . '?id=' . $post->ID,
							'title' => wp_trim_words(zhuige_xcx_get_post_excerpt($post), 11, '...'),
							'image' => zhuige_bbs_topic_thumb($post->ID, true),
						];
					} else {
						$post_type_info = $this->get_post_type_info($post->post_type);
						$items[] = [
							'post_type' => $post->post_type,
							'more_link' => $post->more_link,
							'badge' => $post_type_info['name'],
							'link' => $post_type_info['link'] . '?id=' . $post->ID,
							'title' => $post->post_title,
							'image' => zhuige_xcx_get_one_post_thumbnail($post, true),
						];
					}
				}
				$rec_post['items'] = $items;

				if ($post->post_type == 'zhuige_activity') {
					$rec_post['banner'] = $rec_post['banner']['url'];
				} else {
					unset($rec_post['banner']);
					unset($rec_post['subtitle']);
				}
				unset($rec_post['switch']);

				$rec_posts[] = $rec_post;
			}
		}

		$data['rec_posts'] = $rec_posts;

		//首页分享头图
		$rec_home_thumb = ZhuiGe_Xcx::option_value('rec_home_thumb');
		if ($rec_home_thumb && $rec_home_thumb['url']) {
			$data['thumb'] = $rec_home_thumb['url'];
		}

		if (ZhuiGe_Xcx::option_value('home_gray_switch')) {
			$data['style'] = ZhuiGe_Xcx::option_value('home_gray_css');
		}

		return $this->success($data);
	}

	/**
	 * 发帖页 配置
	 */
	public function get_create($request)
	{
		$data = [];

		// 图片广告
		$create_ad_image = ZhuiGe_Xcx::option_value('create_ad_image');
		if ($create_ad_image && $create_ad_image['switch']) {
			$data['ad'] = [
				'image' => $create_ad_image['image']['url'],
				'link' => $create_ad_image['link']
			];
		}

		//图标导航
		$create_items_org = ZhuiGe_Xcx::option_value('create_items');
		$items = [];
		if (is_array($create_items_org)) {
			foreach ($create_items_org as $item) {
				if ($item['switch'] && $item['image'] && $item['image']['url']) {
					$i = [
						'image' => $item['image']['url'],
						'link' => $item['link'],
						'title' => $item['title'],
					];

					if (stripos($item['link'], 'bbs/post/post') !== false) {
						$i['require_mobile'] = (ZhuiGe_Xcx::option_value('bbs_topic_mobile_switch') ? 1 : 0);
						$i['require_mobile_tip'] = '发帖';
					} else if (stripos($item['link'], 'bbs/forum-create/forum-create') !== false) {
						$i['require_mobile'] = (ZhuiGe_Xcx::option_value('bbs_forum_mobile_switch') ? 1 : 0);
						$i['require_mobile_tip'] = '建圈';
					} else if (stripos($item['link'], 'vote/post/post') !== false) {
						$i['require_mobile'] = (ZhuiGe_Xcx::option_value('vote_mobile_switch') ? 1 : 0);
						$i['require_mobile_tip'] = '发起投票';
					} else if (stripos($item['link'], 'contribution/post-edit/post-edit') !== false) {
						$i['require_mobile'] = (ZhuiGe_Xcx::option_value('contribution_mobile_switch') ? 1 : 0);
						$i['require_mobile_tip'] = '投稿';
					}

					$items[] = $i;
				}
			}
		}
		$data['items'] = $items;

		return $this->success($data);
	}

	/**
	 * 登录页 配置
	 */
	public function get_login($request)
	{
		$data = [];

		$login_bg = ZhuiGe_Xcx::option_value('login_bg');
		$data['background'] = ZhuiGe_Xcx::option_image_url($login_bg, 'placeholder.jpg');

		$login_logo = ZhuiGe_Xcx::option_value('login_logo');
		$data['logo'] = ZhuiGe_Xcx::option_image_url($login_logo, 'placeholder.jpg');

		$data['title'] = ZhuiGe_Xcx::option_value('login_title');

		$login_yhxy = ZhuiGe_Xcx::option_value('login_yhxy');
		if ($login_yhxy) {
			$data['yhxy'] = '/pages/base/page/page?page_id=' . $login_yhxy;
		}

		$login_yszc = ZhuiGe_Xcx::option_value('login_yszc');
		if ($login_yszc) {
			$data['yszc'] = '/pages/base/page/page?page_id=' . $login_yszc;
		}

		return $this->success($data);
	}

	/**
	 * 搜索配置
	 */
	public function get_search($request)
	{
		$data = ['hots' => []];
		$hot_search = ZhuiGe_Xcx::option_value('hot_search');
		if (!empty($hot_search)) {
			$data['hots'] = explode(',', $hot_search);
		}

		return $this->success($data);
	}

	/**
	 * 我的配置
	 */
	public function get_mine($request)
	{
		$data = [];

		$my_bg = ZhuiGe_Xcx::option_value('my_bg');
		$data['background'] = ZhuiGe_Xcx::option_image_url($my_bg, 'placeholder.jpg');

		// 幻灯片
		$slides_org = ZhuiGe_Xcx::option_value('my_slide');
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

		$my_user_id = get_current_user_id();

		$data['menus'] = $this->_get_mine_menu($my_user_id);

		$copyright = ZhuiGe_Xcx::option_value('copyright');
		if ($copyright['switch']) {
			if (empty($copyright['text'])) {
				$copyright['text'] = '本小程序基于追格（zhuige.com）搭建';
			}
			$copyright['logo'] = ZhuiGe_Xcx::option_image_url($copyright['logo'], 'placeholder.jpg');
			$data['copyright'] = $copyright;
		} else {
			$data['copyright'] = ['text' => '本小程序基于追格（zhuige.com）搭建'];
		}

		return $this->success($data);
	}

	/**
	 * 关于页面
	 */
	public function get_about($request)
	{
		$pages = [];
		$about_pages = ZhuiGe_Xcx::option_value('about_pages');
		if (!empty($about_pages)) {
			$query = new WP_Query();
			$posts = $query->query([
				'posts_per_page' => -1,
				'post_type' => 'page',
				'ignore_sticky_posts' => 1,
				'post__in' => $about_pages,
				'orderby' => 'post__in',
			]);
			foreach ($posts as $post) {
				$pages[] = [
					'id' => $post->ID,
					'title' => $post->post_title,
					'class' => ''
				];
			}
		}

		return $this->success(['pages' => $pages]);
	}

	/**
	 * 我的 菜单
	 */
	private function _get_mine_menu($my_user_id)
	{
		global $wpdb;
		$table_forum = $wpdb->prefix . 'zhuige_xcx_forum';

		$menus = [];
		$my_menu = ZhuiGe_Xcx::option_value('my_menu');
		if (is_array($my_menu) && count($my_menu) > 0) {
			foreach ($my_menu as $menu) {
				if (!$menu['items'] || !is_array($menu['items'])) {
					continue;
				}

				$newItems = [];
				$items = $menu['items'];
				foreach ($items as &$item) {
					if ($item['switch']) {
						if (
							stripos($item['link'], '/pages/bbs/forum-manage/forum-manage') !== false
							|| stripos($item['link'], '/pages/user/admin/admin') !== false
						) {
							//如果是管理链接，但没有管理权限，则直接跳过
							if (!$my_user_id) {
								continue;
							}

							$super_admin = zhuige_xcx_is_client_admin($my_user_id);
							if (!$super_admin) {
								$admin_count = (int)($wpdb->get_var(
									$wpdb->prepare(
										"SELECT COUNT(`id`) FROM $table_forum WHERE `user_id`=%d",
										$my_user_id
									)
								));
								if ($admin_count == 0) {
									continue;
								}
							}
						}

						$item['image'] = ZhuiGe_Xcx::option_image_url($item['icon'], 'placeholder.jpg');
						$newItems[] = $item;
					}
				}

				$menus[] = [
					'title' => $menu['title'],
					'items' => $newItems,
				];
			}
		}

		return $menus;
	}

	/**
	 * 获取文章类型信息
	 */
	private function get_post_type_info($post_type)
	{
		foreach (ZhuiGe_Xcx::$post_types as $item) {
			if ($item['id'] == $post_type) {
				return $item;
			}
		}

		return ['name' => '未知', 'link' => '/pages/base/page/page'];
	}
}

ZhuiGe_Xcx::$rest_controllers[] = new ZhuiGe_Xcx_Setting_Controller();
