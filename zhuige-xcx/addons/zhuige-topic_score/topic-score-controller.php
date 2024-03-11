<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2024 www.zhuige.com All rights reserved.
 */

class ZhuiGe_Xcx_Topic_Score_Controller extends ZhuiGe_Xcx_Base_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->module = 'topic_score';

		$this->routes = [
			'exchange' => ['callback' => 'exchange', 'auth' => 'login'],
		];
	}

	/**
	 * 积分兑换文章
	 */
	public function exchange($request)
	{
		// 商品（文章）信息 -- start --
		$post_id = $this->param_int($request, 'post_id');
		if (!$post_id) {
			return $this->error('请选择帖子');
		}

		$topic = get_post($post_id);
		if (!$topic) {
			return $this->error('找不到帖子');
		}

		$limit = get_post_meta($post_id, 'zhuige_bbs_topic_limit', true);
		if ($limit != 'score') {
			return $this->error('不是积分帖子');
		}

		$score = (int)(get_post_meta($post_id, 'zhuige_bbs_topic_score', true));
		if ($score <= 0) {
			return $this->error('积分配置异常');
		}

		// 商品信息 -- end --


		$my_user_id = get_current_user_id();
		$my_score = (int)(get_user_meta($my_user_id, 'zhuige_score', true));
		if ($my_score < (int)($score)) {
			return $this->error('积分不够');
		}

		update_user_meta($my_user_id, 'zhuige_score', $my_score - $score);


		$post_type = 'zhuige_bbs_topic';

		global $wpdb;

		$trade_no = 'ZG_' . $my_user_id . '_' . time();
		$table_post_cost_log = $wpdb->prefix . 'zhuige_xcx_post_cost_log';
		$wpdb->insert($table_post_cost_log, [
			'trade_no' => $trade_no,
			'user_id' => $my_user_id,
			'post_type' => $post_type,
			'post_id' => $post_id,
			'price' => $score,
			'type' => 'score',
			'status' => 'finish',
			'createtime' => time()
		]);

		$table_score_bills = $wpdb->prefix . 'zhuige_xcx_score_bills';
		$wpdb->insert($table_score_bills, [
			'user_id' => $my_user_id,
			'action' => $post_type . '_ex',
			'extra' => $post_id,
			'score' => -$score,
			'time' => time()
		]);

		//给作者增加积分 -- start
		$author_score = (int)(get_user_meta($topic->post_author, 'zhuige_score', true));
		update_user_meta($topic->post_author, 'zhuige_score', $author_score + $score);

		$wpdb->insert($table_score_bills, [
			'user_id' => $topic->post_author,
			'action' => $post_type . '_fee',
			'extra' => $wpdb->insert_id,
			'score' => $score,
			'time' => time()
		]);
		//给作者增加积分 -- end

		
		return $this->success([]);
	}

}

ZhuiGe_Xcx::$rest_controllers[] = new ZhuiGe_Xcx_Topic_Score_Controller();
