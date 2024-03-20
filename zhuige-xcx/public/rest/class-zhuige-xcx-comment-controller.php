<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2024 www.zhuige.com All rights reserved.
 */

class ZhuiGe_Xcx_Comment_Controller extends ZhuiGe_Xcx_Base_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->module = 'comment';
		$this->routes = [
			'index' => 'comment_index',
			'add' => ['callback' => 'comment_add', 'auth' => 'login'],
			'delete' => ['callback' => 'comment_delete', 'auth' => 'login'],
		];
	}

	/**
	 * 文章的评论列表
	 */
	public function comment_index($request)
	{
		$post_id = $this->param_int($request, 'post_id', 0);
		if (empty($post_id)) {
			return $this->error('缺少参数');
		}

		$offset = $this->param_int($request, 'offset', 0);

		$comments = zhuige_xcx_get_comment_tree($post_id, $offset);

		return $this->success([
			'comments' => $comments,
			'more' => (count($comments) >= ZhuiGe_Xcx::POSTS_PER_PAGE ? 'more' : 'nomore')
		]);
	}

	/**
	 * 添加评论
	 */
	public function comment_add($request)
	{
		$user_id = get_current_user_id();

		if (!ZhuiGe_Xcx::option_value('comment_switch')) {
			return $this->error('评论已关闭');
		}

		if (function_exists('zhuige_auth_is_black') && zhuige_auth_is_black($user_id)) {
			return $this->error('评论太频繁了~');
		}

		$post_id = $this->param_int($request, 'post_id', 0);
		if (empty($post_id)) {
			return $this->error('缺少参数');
		}

		$score = $this->param_int($request, 'score', 0);

		global $wpdb;

		// 只允许打一次分
		if ($score) {
			// 是否已经评价过
			$table_comments = $wpdb->prefix . 'comments';
			$comment_count = $wpdb->get_var(
				// $wpdb->prepare(
				"SELECT COUNT(`comment_ID`) FROM `$table_comments` WHERE `comment_post_ID`=$post_id AND `user_id`=$user_id"
				// )
			);
			if ($comment_count) {
				return $this->error('已经评论过了~');
			}
		}


		$post = get_post($post_id);

		$is_user_vip = false;
		if (function_exists('zhuige_xcx_vip_is_vip')) {
			$cerify = zhuige_xcx_vip_is_vip($user_id);
			$is_user_vip = ($cerify['status'] == 1);
		}

		if (ZhuiGe_Xcx_Addon::is_active('zhuige-auth')) {
			$auth_comment = false;
			if ($post->post_type == 'zhuige_bbs_topic') {
				$auth_comment_topic = ZhuiGe_Xcx::option_value('auth_comment_topic');
				$auth_comment = ($auth_comment_topic == 'all' || ($auth_comment_topic == 'vip' && $is_user_vip));
			} else if ($post->post_type == 'post') {
				$auth_comment_cms = ZhuiGe_Xcx::option_value('auth_comment_cms');
				$auth_comment = ($auth_comment_cms == 'all' || ($auth_comment_cms == 'vip' && $is_user_vip));
			} else if ($post->post_type == 'zhuige_res') {
				$auth_comment_resource = ZhuiGe_Xcx::option_value('auth_comment_resource');
				$auth_comment = ($auth_comment_resource == 'all' || ($auth_comment_resource == 'vip' && $is_user_vip));
			} else if ($post->post_type == 'zhuige_vote') {
				$auth_comment_vote = ZhuiGe_Xcx::option_value('auth_comment_vote');
				$auth_comment = ($auth_comment_vote == 'all' || ($auth_comment_vote == 'vip' && $is_user_vip));
			} else if ($post->post_type == 'zhuige_business_card') {
				$auth_comment_business_card = ZhuiGe_Xcx::option_value('auth_comment_business_card');
				$auth_comment = ($auth_comment_business_card == 'all' || ($auth_comment_business_card == 'vip' && $is_user_vip));
			} else if ($post->post_type == 'zhuige_wiki') {
				$auth_comment_wiki = ZhuiGe_Xcx::option_value('auth_comment_wiki');
				$auth_comment = ($auth_comment_wiki == 'all' || ($auth_comment_wiki == 'vip' && $is_user_vip));
			} else if ($post->post_type == 'zhuige_idle_goods') {
				$auth_comment_idle_shop = ZhuiGe_Xcx::option_value('auth_comment_idle_shop');
				$auth_comment = ($auth_comment_idle_shop == 'all' || ($auth_comment_idle_shop == 'vip' && $is_user_vip));
			}

			if (!$auth_comment) {
				return $this->error('无评论权限');
			}
		}

		// 评论是否要求头像昵称
		if (ZhuiGe_Xcx::option_value('comment_avatar_switch')) {
			if (!zhuige_xcx_is_set_avatar($user_id)) {
				return $this->error('', 'require_avatar');
			}
		}

		// 评论是否要求手机号
		if (ZhuiGe_Xcx::option_value('comment_mobile_switch')) {
			if (!zhuige_xcx_is_set_mobile($user_id)) {
				return $this->error('', 'require_mobile');
			}
		}


		$parent_id = $this->param_int($request, 'parent_id', 0);
		$reply_id = $this->param_int($request, 'reply_id', 0);
		$content = $this->param($request, 'content', '');
		if (empty($content)) {
			return $this->error('缺少参数');
		}

		$os = $this->param($request, 'os', 'wx');
		if (!$this->msg_sec_check($content, $os)) {
			return $this->error('请勿发布敏感信息');
		}

		$comment_approved = 0; // 必须人工审核，以防垃圾信息
		// $comment_approved = 1; 
		$comment_id = wp_insert_comment([
			'comment_post_ID' => $post_id,
			'comment_content' => $content,
			'comment_parent' => $parent_id,
			'comment_approved' => $comment_approved,
			'user_id' => $user_id,
		]);

		
		add_comment_meta($comment_id, 'zhuige_xcx_score', $score, true);

		
		$table_notify = $wpdb->prefix . 'zhuige_xcx_notify';

		// 评论通知
		if ($comment_approved == 1) {
			$wpdb->insert($table_notify, [
				'type' => 'comment',
				'from_id' => $user_id,
				'to_id' => $post->post_author,
				'post_id' => $post_id,
				'isread' => 0,
				'time' => time()
			]);
		}
		

		if ($reply_id) {
			add_comment_meta($comment_id, 'zhuige_xcx_reply_user_id', $reply_id);

			// 评论通知
			if ($comment_approved == 1) {
				$parent_comment = get_comment($parent_id);
				$wpdb->insert($table_notify, [
					'type' => 'reply',
					'from_id' => $user_id,
					'to_id' => $parent_comment->user_id,
					'post_id' => $post_id,
					'isread' => 0,
					'time' => time()
				]);
			}
		}

		//添加积分
		if (function_exists('zhuige_xcx_add_user_score_by_task')) {
			zhuige_xcx_add_user_score_by_task('comment', $post->post_type . ',' . $post_id);
		}

		return $this->success('审核后，他人可见');
	}

	/**
	 * 删除评论
	 */
	public function comment_delete($request)
	{
		$comment_id = $this->param_int($request, 'comment_id', 0);
		if (empty($comment_id)) {
			return $this->error('缺少参数');
		}

		global $wpdb;
		$table_comments = $wpdb->prefix . 'comments';
		$wpdb->query("DELETE FROM $table_comments WHERE comment_ID=$comment_id OR comment_parent=$comment_id");

		return $this->success();
	}
}

ZhuiGe_Xcx::$rest_controllers[] = new ZhuiGe_Xcx_Comment_Controller();
