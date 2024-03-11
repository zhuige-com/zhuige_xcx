<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2024 www.zhuige.com All rights reserved.
 */

class ZhuiGe_Xcx_At_Users_Controller extends ZhuiGe_Xcx_Base_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->module = 'at_users';

		$this->routes = [
			'notifys' => ['callback' => 'get_notifys', 'auth' => 'login'],
			'read' => ['callback' => 'read', 'auth' => 'login'],
		];
	}

	/**
	 * 获取 @ 我的信息
	 */
	public function get_notifys($request)
	{
		$my_user_id = get_current_user_id();

		$offset = $this->param_int($request, 'offset', 0);
		$per_page_count = ZhuiGe_Xcx::POSTS_PER_PAGE;

		global $wpdb;
		$table_at_users_notify = $wpdb->prefix . 'zhuige_xcx_at_users_notify';
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM `$table_at_users_notify` WHERE `to_id`=%d ORDER BY `createtime` DESC LIMIT %d, %d",
				$my_user_id,
				$offset,
				$per_page_count
			), ARRAY_A
		);

		$notifys = [];
		foreach ($results as $item) {
			$notify = [
				'id' => $item['id'],
				'from_id' => $item['from_id'],
				'from_nickname' => get_user_meta($item['from_id'], 'nickname', true),
				'from_avatar' => ZhuiGe_Xcx::user_avatar($item['from_id']),
				'topic_id' => $item['topic_id'],
				'isread' => $item['isread'],
				'time' => zhuige_xcx_time_stamp_beautify($item['createtime'])
			];

			$post = get_post($item['topic_id']);
			$notify['excerpt'] = zhuige_xcx_get_post_excerpt($post);

			$notifys[] = $notify;
		}

		return $this->success([
			'notifys' => $notifys,
			'more' => (count($notifys) >= $per_page_count ? 'more' : 'nomore')
		]);
	}

	/**
	 * 设置已读
	 */
	public function read($request)
	{
		$notify_id = $this->param_int($request, 'notify_id', 0);
		if (empty($notify_id)) {
			return $this->error('缺少参数');
		}

		global $wpdb;
		$table_at_users_notify = $wpdb->prefix . 'zhuige_xcx_at_users_notify';
		$wpdb->update($table_at_users_notify, ['isread' => '1'], ['id' => $notify_id, 'to_id' => get_current_user_id()]);

		return $this->success();
	}

}

ZhuiGe_Xcx::$rest_controllers[] = new ZhuiGe_Xcx_At_Users_Controller();
