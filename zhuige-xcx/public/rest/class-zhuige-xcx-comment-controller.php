<?php

/*
 * 追格小程序
 * Author: 追格
 * Help document: https://www.zhuige.com
 * Copyright © 2022 www.zhuige.com All rights reserved.
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

		if (ZhuiGe_Xcx::option_value('comment_mobile_switch')) {
			$mobile = get_user_meta($user_id, 'zhuige_xcx_user_mobile', true);
			if (empty($mobile)) {
				return $this->error('', 'require_mobile');
			}
		}

		$post_id = $this->param_int($request, 'post_id', 0);
		$parent_id = $this->param_int($request, 'parent_id', 0);
		$reply_id = $this->param_int($request, 'reply_id', 0);
		$content = $this->param($request, 'content', '');
		if (empty($post_id) || empty($content)) {
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

		if ($reply_id) {
			add_comment_meta($comment_id, 'zhuige_xcx_reply_user_id', $reply_id);
		}

		//通知
		// --------------------------------------------------
		$post = get_post($post_id);
		$to_id = $post->post_author;

		global $wpdb;
		$table_notify = $wpdb->prefix . 'zhuige_xcx_notify';
		$now_time = time();
		$wpdb->insert($table_notify, [
			'type' => 'comment',
			'from_id' => $user_id,
			'to_id' => $to_id,
			'post_id' => $post_id,
			'isread' => 0,
			'time' => $now_time
		]);
		// --------------------------------------------------

		//添加积分
		if (function_exists('zhuige_xcx_add_user_score_by_task')) {
			zhuige_xcx_add_user_score_by_task('comment', $post->post_type . ',' . $post_id);
		}

		if ($comment_approved) {
			return $this->success();
		} else {
			return $this->error('评论审核后，他人可见', 100);
		}
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
