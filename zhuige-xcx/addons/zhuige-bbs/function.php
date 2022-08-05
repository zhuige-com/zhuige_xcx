<?php

/*
 * 追格小程序
 * Author: 追格
 * Help document: https://www.zhuige.com
 * Copyright © 2022 www.zhuige.com All rights reserved.
 */

/**
 * 帖子格式化
 */
if (!function_exists('zhuige_bbs_topic_format')) {
	function zhuige_bbs_topic_format($post)
	{
		$item = [
			'id' => $post->ID,
			'excerpt' => zhuige_xcx_get_post_excerpt($post),
			'comment_count' => $post->comment_count
		];

		$options = get_post_meta($post->ID, 'zhuige-bbs-topic-option', true);
		if ($options['type'] == 'image') {
			$item['images'] = $options['images'];
		} else if ($options['type'] == 'video') {
			$item['video'] = $options['video'];
			$item['video_cover'] = $options['video_cover'];
		}

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

		if (ZhuiGe_Xcx::option_value('rec_home_comment')) {
			$item['comments'] = zhuige_xcx_get_comments($post->ID, 0, 1);
		}

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