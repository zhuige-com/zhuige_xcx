<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2024 www.zhuige.com All rights reserved.
 */

/**
 * 帖子摘要
 */
if (!function_exists('zhuige_bbs_topic_excerpt')) {
	function zhuige_bbs_topic_excerpt($post, $maxlen = 50)
	{
		$content = str_replace("<br />", "\n", $post->post_content);
		$lines = explode("\n", $content);
		$excerpt = '';
		$len = 0;
		$cnt = 0;
		$suffix = false;
		foreach ($lines as $line) {
			$line = wp_strip_all_tags(apply_filters('the_content', $line));
			$line_len = mb_strlen($line);
			if (($len + $line_len) > $maxlen) {
				$line = wp_trim_words($line, $maxlen - $len, '...');
				$suffix = true;
			}

			if ($excerpt != '') {
				$excerpt = $excerpt . "\n";
			}
			$excerpt = $excerpt . $line;

			$len = $len + $line_len;
			if ($len > $maxlen) {
				return $excerpt;
			}

			$cnt = $cnt + 1;
			if ($cnt >= 3) {
				if (!$suffix) {
					$excerpt = $excerpt . "\n...";
				}
				return $excerpt;
			}
		}

		return $excerpt;
	}
}

/**
 * 帖子格式化
 */
if (!function_exists('zhuige_bbs_topic_format')) {
	function zhuige_bbs_topic_format($post)
	{
		$item = [
			'id' => $post->ID,
			'excerpt' => zhuige_bbs_topic_excerpt($post),
			'comment_count' => $post->comment_count
		];

		$limit = 'free';

		// 帖子积分 - 开启积分阅读全文
		if (ZhuiGe_Xcx_Addon::is_active('zhuige-topic_score')) {
			$limit = get_post_meta($post->ID, 'zhuige_bbs_topic_limit', true);
			if (empty($limit)) {
				$limit = 'free';
			}
		}

		global $wpdb;
		if ($limit == 'score') {
			$my_user_id = get_current_user_id();
			if ($my_user_id) {
				$table_post_cost_log = $wpdb->prefix . 'zhuige_xcx_post_cost_log';
				$cost_log = $wpdb->get_var($wpdb->prepare("SELECT COUNT(`id`) FROM $table_post_cost_log WHERE `user_id`=%d AND `post_id`=%d", $my_user_id, $post->ID));
				if ($cost_log) {
					$limit = 'free';
				}
			}
			
			if ($limit == 'score') {
				$score = (int)(get_post_meta($post->ID, 'zhuige_bbs_topic_score', true));
				if ($score == 0) {
					$limit = 'free';
				}
				$item['score'] = $score;
			}
		}

		$options = get_post_meta($post->ID, 'zhuige-bbs-topic-option', true);

		if ($limit == 'free') {
			// $item['excerpt'] = zhuige_xcx_get_post_excerpt($post);

			if ($options['type'] == 'image') {
				$item['images'] = $options['images'];
			} else if ($options['type'] == 'video') {
				$item['video'] = $options['video'];
				$item['video_cover'] = $options['video_cover'];
			}
		}

		$item['limit'] = $limit; 
		$item['type'] = $options['type'];

		//获取标签
		$tags = [];
		$terms = get_the_terms($post->ID, 'zhuige_bbs_topic_tag');
		foreach ($terms as $term) {
			$tags[] = [
				'id' => $term->term_id,
				'name' => $term->name
			];
		}
		$item['subjects'] = $tags;

		// 作者信息
		$user_id = $post->post_author;
		$author = [
			'user_id' => $user_id,
			'nickname' => get_user_meta($user_id, 'nickname', true),
			'avatar' => ZhuiGe_Xcx::user_avatar($user_id),
		];
		if (function_exists('zhuige_xcx_certify_is_certify')) {
			$author['certify'] = zhuige_xcx_certify_is_certify($user_id);
		}
		if (function_exists('zhuige_xcx_vip_is_vip')) {
			$author['vip'] = zhuige_xcx_vip_is_vip($user_id);
		}
		$item['author'] = $author;

		// 所属圈子
		$forum_id = get_post_meta($post->ID, 'zhuige_bbs_forum_id', true);
		$forum = get_post($forum_id);
		if ($forum) {
			$item['forum'] = [
				'id' => $forum->ID,
				'name' => $forum->post_title
			];
		}

		// 点赞数量
		$item['like_count'] = (int) get_post_meta($post->ID, 'like_count', true);

		// 发布时间
		$item['time'] = zhuige_xcx_time_beautify($post->post_date_gmt);

		// 位置信息
		$item['location'] = $options['location'];

		// 评论
		$comments = [];
		$bbs_list_comment = ZhuiGe_Xcx::option_value('bbs_list_comment');
		if ($bbs_list_comment && isset($bbs_list_comment['switch']) && $bbs_list_comment['switch']) {
			$comments = zhuige_xcx_get_comments($post->ID, 0, $bbs_list_comment['count']);
		}
		$item['comments'] = $comments;

		// @的人
		$at_users = [];
		$at_list = get_post_meta($post->ID, 'zhuige_bbs_topic_at_list', true);
		if (is_string($at_list)) {
			$at_user_ids = explode(',', $at_list);
			if (is_array($at_user_ids)) {
				foreach ($at_user_ids as $at_user_id) {
					if (empty($at_user_id)) {
						continue;
					}
					$at_users[] = [
						'user_id' => $at_user_id,
						'nickname' => get_user_meta($at_user_id, 'nickname', true)
					];
				}
			}
		}
		$item['at_users'] = $at_users;

		return $item;
	}
}

/**
 * 圈子用户数量
 */
if (!function_exists('zhuige_bbs_forum_user_count')) {
	function zhuige_bbs_forum_user_count($forum_id)
	{
		global $wpdb;
		$table_zhuige_bbs_forum_users = $wpdb->prefix . 'zhuige_bbs_forum_users';
		$user_count = $wpdb->get_var(
			"SELECT COUNT(`user_id`) FROM `$table_zhuige_bbs_forum_users` WHERE `forum_id`=$forum_id"
		);

		return (int)$user_count + 1;
	}
}

/**
 * 圈子帖子数量
 */
if (!function_exists('zhuige_bbs_forum_topic_count')) {
	function zhuige_bbs_forum_topic_count($forum_id)
	{
		global $wpdb;
		$table_post_meta = $wpdb->prefix . 'postmeta';
		$table_posts = $wpdb->prefix . 'posts';
		$post_count = $wpdb->get_var(
			"SELECT COUNT(`meta_id`) FROM `$table_post_meta` WHERE `meta_key`='zhuige_bbs_forum_id' AND `meta_value`='$forum_id' AND `post_id` IN (SELECT `ID` FROM `$table_posts` WHERE `post_status`='publish')"
		);

		return (int)$post_count;
	}
}

/**
 * 获取帖子缩略图
 */
if (!function_exists('zhuige_bbs_topic_thumb')) {
	function zhuige_bbs_topic_thumb($topic_id)
	{
		$thumb = '';

		$options = get_post_meta($topic_id, 'zhuige-bbs-topic-option', true);
		if ($options['type'] == 'image') {
			if (!empty($options['images'])) {
				$thumb = $options['images'][0]['image']['url'];
			}
		} else if ($options['type'] == 'video') {
			$thumb = $options['video_cover']['url'];
		}

		if (empty($thumb)) {
			$thumb = ZHUIGE_XCX_BASE_URL . 'public/images/placeholder.jpg';
		}

		return $thumb;
	}
}
