<?php

/*
 * 追格小程序
 * Author: 追格
 * Help document: https://www.zhuige.com
 * Copyright © 2022 www.zhuige.com All rights reserved.
 */

class ZhuiGe_Xcx_Bbs_Topic_Controller extends ZhuiGe_Xcx_Base_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->module = 'bbs';

		$this->routes = [
			'topic_detail' => 'get_topic_detail',

			'topic_create' => ['callback' => 'topic_create', 'auth' => 'login'],

			'topic_list_subject' => 'get_topic_list_subject',
			'topic_list_forum' => 'get_topic_list_forum',
		];
	}

	/**
	 * 新建帖子
	 */
	public function topic_create($request)
	{
		$my_user_id = get_current_user_id();

		// $black_user_ids = ZhuiGe_Xcx::option_value('auth_other_user_topic');
		// $black_user_ids = explode(',', $black_user_ids);
		// if (in_array($my_user_id, $black_user_ids)) {
		// 	return $this->error('你被禁止发帖了');
		// }

		if (ZhuiGe_Xcx::option_value('bbs_topic_mobile_switch')) {
			$mobile = get_user_meta($my_user_id, 'zhuige_xcx_user_mobile', true);
			if (empty($mobile)) {
				return $this->error('', 'require_mobile');
			}
		}

		$forum_id = $this->param_int($request, 'forum_id', 0);
		if (empty($forum_id)) {
			return $this->error('请选择版块');
		}

		$content = $this->param($request, 'content', '');
		if (empty($content)) {
			return $this->error('内容不可为空');
		}

		$marker = $this->param($request, 'marker', '');
		$address = $this->param($request, 'address', '');
		$longitude = $this->param($request, 'longitude', '');
		$latitude = $this->param($request, 'latitude', '');

		$status = 'pending'; //必须人工审核，以防垃圾信息
		// $status = 'publish';
		$post = array(
			'post_title' => $my_user_id . '的帖子',
			'post_content' => html_entity_decode($content),
			'post_status' => $status,
			'post_type' => 'zhuige_bbs_topic',
			'post_author' => $my_user_id
		);

		$post_id = wp_insert_post($post);
		if (!$post_id) {
			return $this->error('系统异常');
		}

		$type = $this->param($request, 'type', 'image');
		$options = [];
		$options['type'] = $type;

		if ($type == 'image') {
			$images_json = $this->param($request, 'images', '');
			$images = json_decode($images_json, true);
			$options['images'] = $images;
		} else if ($type == 'video') {
			$video_json = $this->param($request, 'video', '');
			$video = json_decode($video_json, true);
			$options['video'] = $video;

			$video_cover_json = $this->param($request, 'video_cover', '');
			$video_cover = json_decode($video_cover_json, true);
			$options['video_cover'] = $video_cover;
		}

		// 位置
		$options['location'] = [
			'marker' => $marker,
			'address' => $address,
			'longitude' => $longitude,
			'latitude' => $latitude,
		];
		update_post_meta($post_id, 'zhuige-bbs-topic-option', $options);

		// 选择的版块
		update_post_meta($post_id, 'zhuige_bbs_forum_id', $forum_id);

		// 选择的话题
		global $wpdb;
		$subjects = $this->param($request, 'subjects', '');
		if (!empty($subjects)) {
			$subjects = explode('-0-', $subjects);
			if (is_array($subjects)) {
				$table_posts = $wpdb->prefix . 'posts';
				$subject_ids = [];
				foreach ($subjects as $subject) {
					$subject_id = $wpdb->get_var($wpdb->prepare("SELECT `id` FROM $table_posts WHERE `post_title`=%s", $subject));
					if ($subject_id) {
						$subject_ids[] = $subject_id;
					} else {
						$subject_id = wp_insert_post(['post_title' => $subject, 'post_content' => $subject, 'post_type' => 'zhuige_bbs_subject']);
						$subject_ids[] = $subject_id;
					}
				}

				update_post_meta($post_id, 'zhuige_bbs_subject_ids', $subject_ids);
			}
		}

		//标签
		$subjects = $this->param($request, 'subjects', '');
		if (!empty($subjects)) {
			$subjects = explode('-0-', $subjects);
			wp_set_post_terms($post_id, $subjects, 'zhuige_bbs_topic_tag');
		}

		return $this->success(['post_id' => $post_id, 'status' => $status, 'options' => $options]);
	}

	/**
	 * 最新的帖子
	 */
	public function get_topic_detail($request)
	{
		$topic_id = $this->param_int($request, 'topic_id', 0);
		if (!$topic_id) {
			return $this->error('缺少参数');
		}

		$my_user_id = get_current_user_id();

		$post = get_post($topic_id);

		//添加文章浏览记录
		global $wpdb;
		$table_post_view = $wpdb->prefix . 'zhuige_xcx_post_view';
		$post_view_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT `id` FROM `$table_post_view` WHERE `user_id`=%d AND `post_id`=%d",
				$my_user_id,
				$topic_id
			)
		);
		if (!$post_view_id) {
			$wpdb->insert($table_post_view, [
				'user_id' => $my_user_id,
				'post_id' => $topic_id,
				'post_status' => 'publish',
				'time' => time()
			]);
		}

		// 文章浏览数
		$post_views = (int) get_post_meta($topic_id, 'zhuige_views', true);
		if (!update_post_meta($topic_id, 'zhuige_views', ($post_views + 1))) {
			add_post_meta($topic_id, 'zhuige_views', 1, true);
		}

		$topic = [
			'id' => $post->ID,
			'title' => $post->post_title,
			'content' => apply_filters('the_content', $post->post_content),
			'comment_count' => $post->comment_count,
		];

		$options = get_post_meta($post->ID, 'zhuige-bbs-topic-option', true);
		if ($options['type'] == 'image') {
			$topic['images'] = $options['images'];
		} else if ($options['type'] == 'video') {
			$topic['video'] = $options['video'];
			$topic['video_cover'] = $options['video_cover'];
		}

		$topic['type'] = $options['type'];

		//获取标签
		$tags = [];
		$terms = get_the_terms($post->ID, 'zhuige_bbs_topic_tag');
		foreach ($terms as $term) {
			$tags[] = [
				'id' => $term->term_id,
				'name' => $term->name
			];
		}
		$topic['subjects'] = $tags;

		// 作者信息
		$user_id = $post->post_author;
		$author = [
			'user_id' => $user_id,
			'nickname' => get_user_meta($user_id, 'nickname', true),
			'avatar' => ZhuiGe_Xcx::user_avatar($user_id),
			'reward' => get_user_meta($user_id, 'zhuige_xcx_user_reward', true)
		];
		if (function_exists('zhuige_xcx_certify_is_certify')) {
			$author['certify'] = zhuige_xcx_certify_is_certify($user_id);
		}
		// “我”是否关注了作者
		$table_follow_user = $wpdb->prefix . 'zhuige_xcx_follow_user';
		$follow_user_id_exist = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM `$table_follow_user` WHERE user_id=%d AND follow_user_id=%d",
				$my_user_id,
				$user_id
			)
		);
		$author['is_follow'] = ($follow_user_id_exist ? 1 : 0);
		$topic['author'] = $author;

		// 所属圈子
		$forum_id = get_post_meta($topic_id, 'zhuige_bbs_forum_id', true);
		$forum = get_post($forum_id);
		if ($forum) {
			$forum_options = get_post_meta($forum->ID, 'zhuige-bbs-forum-option', true);
			$forum_arr = [
				'id' => $forum->ID,
				'name' => $forum->post_title,
				'logo' => ZhuiGe_Xcx::option_image_url($forum_options['logo'], 'placeholder.jpg')
			];

			$table_zhuige_bbs_forum_users = $wpdb->prefix . 'zhuige_bbs_forum_users';
			$forum_arr['user_count'] = zhuige_bbs_forum_user_count($forum_id);
			$forum_arr['post_count'] = zhuige_bbs_forum_topic_count($forum_id);

			if ($my_user_id) {
				$follow_forum_id_exist = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT id FROM `$table_zhuige_bbs_forum_users` WHERE `user_id`=%d AND `forum_id`=%d",
						$my_user_id,
						$forum_id
					)
				);
				$forum_arr['is_follow'] = ($follow_forum_id_exist ? 1 : 0);
			} else {
				$forum_arr['is_follow'] = 0;
			}

			$topic['forum'] = $forum_arr;
		}

		// 位置信息
		$topic['location'] = $options['location'];

		// 点赞数量
		// 点赞列表
		$table_post_like = $wpdb->prefix . 'zhuige_xcx_post_like';
		$users = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT user_id FROM `$table_post_like` WHERE post_id=%d ORDER BY id DESC",
				$topic_id
			)
		);
		$like_list = [];
		if (!empty($users)) {
			foreach ($users as $user) {
				$like_list[] = [
					'user_id' => $user->user_id,
					'avatar' => ZhuiGe_Xcx::user_avatar($user->user_id),
				];
			}
		}
		$topic['like_list'] = $like_list;
		// “我”是否已点赞
		if ($my_user_id) {
			$table_post_like = $wpdb->prefix . 'zhuige_xcx_post_like';
			$post_like_id_exist = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT id FROM `$table_post_like` WHERE `user_id`=%d AND `post_id`=%d",
					$my_user_id,
					$topic_id
				)
			);
			$topic['is_like'] = ($post_like_id_exist ? 1 : 0);
		} else {
			$topic['is_like'] = 0;
		}

		// 收藏数量
		$topic['fav_count'] = (int) get_post_meta($post->ID, 'fav_count', true);
		// “我”是否已收藏
		if ($my_user_id) {
			$table_post_favorite = $wpdb->prefix . 'zhuige_xcx_post_favorite';
			$post_favorite_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT id FROM `$table_post_favorite` WHERE user_id=%d AND post_id=%d",
					$my_user_id,
					$topic_id
				)
			);
			$topic['is_favorite'] = ($post_favorite_id ? 1 : 0);
		} else {
			$topic['is_favorite'] = 0;
		}

		// 发布时间
		$topic['time'] = zhuige_xcx_time_beautify($post->post_date_gmt);

		// 相关帖子推荐
		$topic['recs'] = $this->_get_list_by_tag($tags, $topic_id);

		// 评论
		$topic['comments'] = zhuige_xcx_get_comment_tree($post->ID, 0, 5);
		// 我是否已评论过
		if ($my_user_id) {
			$table_comments = $wpdb->prefix . 'comments';
			$post_comment_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(`comment_ID`) FROM `$table_comments` WHERE `comment_author`=%d AND `comment_post_ID`=%d",
					$my_user_id,
					$topic_id
				)
			);
			$topic['is_comment'] = ($post_favorite_id ? 1 : 0);
		} else {
			$topic['is_comment'] = 0;
		}

		// 广告
		$top_img_ad = ZhuiGe_Xcx::option_value('bbs_detail_top_img_ad');
		if ($top_img_ad && $top_img_ad['switch'] && $top_img_ad['image']['url']) {
			$topic['top_img_ad'] = [
				'image' => $top_img_ad['image']['url'],
				'link' => $top_img_ad['link']
			];
		}

		$bottom_img_ad = ZhuiGe_Xcx::option_value('bbs_detail_bottom_img_ad');
		if ($bottom_img_ad && $bottom_img_ad['switch'] && $bottom_img_ad['image']['url']) {
			$topic['bottom_img_ad'] = [
				'image' => $bottom_img_ad['image']['url'],
				'link' => $bottom_img_ad['link']
			];
		}

		$weixin_ad = ZhuiGe_Xcx::option_value('bbs_detail_weixin_ad');
		if ($weixin_ad && $weixin_ad['switch']) {
			$topic['weixin_ad'] = $weixin_ad['code'];
		}

		// 热门推荐
		$detail_rec_ad = ZhuiGe_Xcx::option_value('bbs_detail_rec_ad');
		if ($detail_rec_ad && $detail_rec_ad['switch']) {
			$rec_ad = [];
			$rec_ad['title'] = $detail_rec_ad['title'];
			$items = [];
			foreach ($detail_rec_ad['items'] as $item_ad) {
				if ($item_ad['switch'] && $item_ad['image'] && $item_ad['image']['url']) {
					$items[] = [
						'title' => $item_ad['title'],
						'image' => $item_ad['image']['url'],
						'link' => $item_ad['link'],
						'badge' => $item_ad['badge'],
					];
				}
			}
			$rec_ad['items'] = $items;

			$topic['rec_ad'] = $rec_ad;
		}

		// 海报配置
		$poster = [];
		$detail_poster = ZhuiGe_Xcx::option_value('bbs_detail_poster');
		$poster['background'] = ZhuiGe_Xcx::option_image_url($detail_poster['background'], 'placeholder.jpg');
		$poster['title'] = $detail_poster['title'];
		if ($options['type'] == 'image') {
			$poster['thumb'] = $options['images'][0]['image']['url'];
		} else if ($options['type'] == 'video') {
			$poster['thumb'] = ZhuiGe_Xcx::option_image_url($detail_poster['thumb_video'], 'placeholder.jpg');
		}

		$data = ['topic' => $topic, 'poster' => $poster];

		$data['is_report'] = ZhuiGe_Xcx_Addon::is_active('zhuige-report') ? 1 : 0;

		return $this->success($data);
	}

	/**
	 * 话题下的帖子列表
	 */
	public function get_topic_list_subject($request)
	{
		$subject_id = $this->param_int($request, 'subject_id', 0);
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
					'field'    => 'term_id',
					'terms'    => $subject_id,
				),
			),
		];

		$query = new WP_Query();
		$result = $query->query($args);
		$topics = [];
		foreach ($result as $post) {
			if ($post->post_type == 'zhuige_bbs_topic') {
				$topics[] = zhuige_bbs_topic_format($post);
			} else {
				$topics[] = $post;
			}
		}

		$data = [
			'topics' => $topics,
			'more' => (count($result) >= ZhuiGe_Xcx::POSTS_PER_PAGE ? 'more' : 'nomore')
		];

		if ($offset == 0) {
			$term = get_term($subject_id);

			$subject = [
				'id' => $subject_id,
				'name' => $term->name,
				'count' => $term->count,
			];

			$options = get_term_meta($subject_id, 'zhuige_bbs_topic_tag_options', true);
			$subject['logo'] = ZhuiGe_Xcx::option_image_url($options['logo'], 'placeholder.jpg');
			$subject['cover'] = ZhuiGe_Xcx::option_image_url($options['cover'], 'placeholder.jpg');

			$data['subject'] = $subject;
		}

		return $this->success($data);
	}

	/**
	 * 版块下文章
	 */
	public function get_topic_list_forum($request)
	{
		$offset = $this->param_int($request, 'offset', 0);
		$forum_id = $this->param_int($request, 'forum_id', 0);

		$args = [
			'posts_per_page' => ZhuiGe_Xcx::POSTS_PER_PAGE,
			'offset' => $offset,
			'orderby' => 'date',
			'post_type' => 'zhuige_bbs_topic',
			'ignore_sticky_posts' => 1,
			'meta_query' => [
				[
					'key'     => 'zhuige_bbs_forum_id',
					'value'   => $forum_id,
					'compare' => '=',
				]
			]
		];

		$query = new WP_Query();
		$result = $query->query($args);
		$topics = [];
		foreach ($result as $post) {
			if ($post->post_type == 'zhuige_bbs_topic') {
				$item = zhuige_bbs_topic_format($post);
				$item['comments'] = zhuige_xcx_get_comments($post->ID, 0, 1);
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
	 * 通TAG帖子查询
	 */
	private function _get_list_by_tag($tags, $topic_id)
	{
		if (!is_array($tags) || empty($tags)) {
			return [];
		}

		$tag_ids = [];
		foreach ($tags as $tag) {
			$tag_ids[] = $tag['id'];
		}

		$args = [
			'posts_per_page' => 4,
			'offset' => 0,
			'orderby' => 'date',
			'post_type' => 'zhuige_bbs_topic',
			'ignore_sticky_posts' => 1,
			'post__not_in' => [$topic_id],
			'tax_query' => array(
				array(
					'taxonomy' => 'zhuige_bbs_topic_tag',
					'field' => 'id',
					'terms' => $tag_ids,
					'operator' => 'IN'
				),
			),
		];

		$query = new WP_Query();
		$result = $query->query($args);
		$topics = [];
		foreach ($result as $post) {
			if ($post->post_type == 'zhuige_bbs_topic') {
				$topics[] = zhuige_bbs_topic_format($post);
			} else {
				$topics[] = $post;
			}
		}

		return $topics;
	}
}

ZhuiGe_Xcx::$rest_controllers[] = new ZhuiGe_Xcx_Bbs_Topic_Controller();
