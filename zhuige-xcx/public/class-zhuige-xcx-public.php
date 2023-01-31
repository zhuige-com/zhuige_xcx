<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
 */

class ZhuiGe_Xcx_Public
{

	public function plugin_init()
	{
		$token = '';
		if (isset($_GET['token'])) {
			$token = sanitize_text_field(wp_unslash($_GET['token']));
		} else if (isset($_POST['token'])) {
			$token = sanitize_text_field(wp_unslash($_POST['token']));
		} else {
			$json = json_decode(file_get_contents('php://input'), TRUE);
			if ($json && isset($json['token'])) {
				$token = $json['token'];
			}
		}

		if (empty($token) || $token == 'false') {
			return;
		}

		global $wpdb;
		$table_usermeta = $wpdb->prefix . 'usermeta';
		$user_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT user_id FROM `$table_usermeta` WHERE  meta_key='zhuige_xcx_user_token' AND meta_value=%s",
				$token
			)
		);

		if ($user_id) {
			wp_set_current_user($user_id);
		}
	}

	public function enqueue_styles()
	{
		wp_enqueue_style(ZHUIGE_XCX, ZHUIGE_XCX_BASE_URL . 'public/css/zhuige-xcx-public.css', array(), ZHUIGE_XCX_VERSION, 'all');
	}

	public function enqueue_scripts()
	{
		wp_enqueue_script(ZHUIGE_XCX, ZHUIGE_XCX_BASE_URL . 'public/js/zhuige-xcx-public.js', array('jquery'), ZHUIGE_XCX_VERSION, false);
	}

	public function trashed_post($post_id)
	{
		global $wpdb;

		$data = ['post_status' => get_post_status($post_id)];
		$where = ['post_id' => $post_id];

		$table_post_view = $wpdb->prefix . 'zhuige_xcx_post_view';
		$wpdb->update($table_post_view, $data, $where);

		$table_post_like = $wpdb->prefix . 'zhuige_xcx_post_like';
		$wpdb->update($table_post_like, $data, $where);

		$table_post_favorite = $wpdb->prefix . 'zhuige_xcx_post_favorite';
		$wpdb->update($table_post_favorite, $data, $where);

		$where = ['post_id' => $post_id];
		$table_notify = $wpdb->prefix . 'zhuige_xcx_notify';
		$wpdb->update($table_notify, $data, $where);

		if (ZhuiGe_Xcx_Addon::is_active('zhuige-system_notice')) {
			$table_system_notice_notify = $wpdb->prefix . 'zhuige_xcx_system_notice_notify';
			$wpdb->delete($table_system_notice_notify, ['post_id' => $post_id], ['%d']);
		}
	}

	public function untrashed_post($post_id)
	{
		global $wpdb;

		$data = ['post_status' => get_post_status($post_id)];
		$where = ['post_id' => $post_id];

		$table_post_view = $wpdb->prefix . 'zhuige_xcx_post_view';
		$wpdb->update($table_post_view, $data, $where);

		$table_post_like = $wpdb->prefix . 'zhuige_xcx_post_like';
		$wpdb->update($table_post_like, $data, $where);

		$table_post_favorite = $wpdb->prefix . 'zhuige_xcx_post_favorite';
		$wpdb->update($table_post_favorite, $data, $where);

		$where = ['post_id' => $post_id];
		$table_notify = $wpdb->prefix . 'zhuige_xcx_notify';
		$wpdb->update($table_notify, $data, $where);
	}

	public function deleted_post($post_id)
	{
		global $wpdb;

		$table_post_view = $wpdb->prefix . 'zhuige_xcx_post_view';
		$wpdb->query("DELETE FROM `$table_post_view` WHERE post_id=$post_id");

		$table_post_like = $wpdb->prefix . 'zhuige_xcx_post_like';
		$wpdb->query("DELETE FROM `$table_post_like` WHERE post_id=$post_id");

		$table_post_favorite = $wpdb->prefix . 'zhuige_xcx_post_favorite';
		$wpdb->query("DELETE FROM `$table_post_favorite` WHERE post_id=$post_id");

		$table_notify = $wpdb->prefix . 'zhuige_xcx_notify';
		$wpdb->query("DELETE FROM `$table_notify` WHERE post_id=$post_id");

		$table_vote_log = $wpdb->prefix . 'zhuige_xcx_vote_log';
		$wpdb->query("DELETE FROM `$table_vote_log` WHERE vote_id=$post_id");

		if (ZhuiGe_Xcx_Addon::is_active('zhuige-system_notice')) {
			$table_system_notice_notify = $wpdb->prefix . 'zhuige_xcx_system_notice_notify';
			$wpdb->delete($table_system_notice_notify, ['post_id' => $post_id], ['%d']);
		}
	}

	function transition_post_status($new_status, $old_status, $post)
	{
		// file_put_contents('post-status.txt', $post->post_title . '-' . $new_status . '-' . $old_status . PHP_EOL, FILE_APPEND);

		if (ZhuiGe_Xcx_Addon::is_active('zhuige-system_notice')) {
			global $wpdb;

			$result = false;
			if ($new_status == 'publish' && $old_status == 'pending') { // 审核通过
				$result = '1';
			} else if ($new_status == 'trash' && $old_status == 'pending') { // 审核未通过
				$result = '0';
			}

			if ($result !== false) {
				$table_system_notice_notify = $wpdb->prefix . 'zhuige_xcx_system_notice_notify';
				$wpdb->insert($table_system_notice_notify, [
					'type' => 'post',
					'notice_id' => 0,
					'post_id' => $post->ID,
					'result' => $result,
					'user_id' => $post->post_author,
					'isread' => '0',
					'createtime' => time()
				]);
			}
		}
	}

	public function transition_comment_status($new_status, $old_status, $comment)
	{
		global $wpdb;

		$table_plus_notify = $wpdb->prefix . 'zhuige_xcx_notify';
		$wpdb->update(
			$table_plus_notify,
			['post_status' => ($new_status == 'approved' ? 'publish' : 'trash')],
			['type' => 'comment', 'post_id' => $comment->comment_post_ID]
		);

		if (ZhuiGe_Xcx_Addon::is_active('zhuige-system_notice')) {
			$result = false;
			if ($new_status == 'approved' && $old_status == 'unapproved') { // 审核通过
				$result = '1';
			} else if (($new_status == 'spam' || $new_status == 'trash') && $old_status == 'unapproved') { // 审核未通过
				$result = '0';
			}

			if ($result !== false) {
				$post = get_post($comment->comment_post_ID);
				$table_system_notice_notify = $wpdb->prefix . 'zhuige_xcx_system_notice_notify';
				$wpdb->insert($table_system_notice_notify, [
					'type' => 'comment',
					'notice_id' => 0,
					'post_id' => $post->ID,
					'comment_id' => $comment->comment_ID,
					'result' => $result,
					'user_id' => $comment->user_id,
					'isread' => '0',
					'createtime' => time()
				]);
			}
		}
	}
}
