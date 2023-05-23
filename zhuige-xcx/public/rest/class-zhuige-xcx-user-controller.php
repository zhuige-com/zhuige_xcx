<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
 */

class ZhuiGe_Xcx_User_Controller extends ZhuiGe_Xcx_Base_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->module = 'user';
		$this->routes = [
			'test_login' => 'test_login',
			'login' => 'user_login',

			'get_info' => ['callback' => 'get_info', 'auth' => 'login'],
			'set_info' => ['callback' => 'set_info', 'auth' => 'login'],
			'init_info' => ['callback' => 'init_info', 'auth' => 'login'],
			'set_mobile' => ['callback' => 'set_mobile', 'auth' => 'login'],

			'like' => ['callback' => 'user_like', 'auth' => 'login'],
			'favorite' => ['callback' => 'user_favorite', 'auth' => 'login'],

			'follow_user' => ['callback' => 'follow_user', 'auth' => 'login'],
			'follow_forum' => ['callback' => 'follow_forum', 'auth' => 'login'],

			'my_follows' => 'my_follows',
			'my_friends' => ['callback' => 'my_friends', 'auth' => 'login'],
			'my_fans' => 'my_fans',

			'my_statistics' => 'my_statistics',
			'home' => 'home',

			'notify' => ['callback' => 'get_notify', 'auth' => 'login'],
			'notify_read' => ['callback' => 'notify_read', 'auth' => 'login'],
			'notify_clear' => ['callback' => 'notify_clear', 'auth' => 'login'],

			'share_score' => 'share_score'
		];
	}

	/**
	 * 模拟登陆
	 */
	public function test_login($request)
	{
		// 如果使用H5测试，可放开此段代码
		// H5平台并未完整适配，比如绑定手机号功能

		// $user_id = 1;
		// $zhuige_xcx_user_token = $this->_generate_token();
		// update_user_meta($user_id, 'zhuige_xcx_user_token', $zhuige_xcx_user_token);
		// $user = array(
		// 	'user_id' => $user_id,
		// 	'nickname' => get_user_meta($user_id, 'nickname', true),
		// 	'token' => $zhuige_xcx_user_token,
		// 	'mobile' => get_user_meta($user_id, 'zhuige_xcx_user_mobile', true)
		// );

		// return $this->success($user);

		return $this->error('请修改后台代码后测试');
	}

	/**
	 *用户登录
	 */
	public function user_login($request)
	{
		$code = $this->param($request, 'code', '');
		$nickname = $this->param($request, 'nickname', '');
		$channel = $this->param($request, 'channel', '');
		if (empty($code) || empty($nickname) || empty($channel)) {
			return $this->error('缺少参数');
		}

		$session = false;
		if ('weixin' == $channel) {
			$session = $this->wx_code2openid($code);
		} else if ('qq' == $channel) {
			$session = $this->qq_code2openid($code);
		} else if ('baidu' == $channel) {
			$session = $this->bd_code2openid($code);
		}

		if (!$session) {
			return $this->error('授权失败');
		}


		$user = get_user_by('login', $session['openid']);
		$first = 0;
		if ($user) {
			$user_id = $user->ID;
			if (function_exists('zhuige_auth_is_black') && zhuige_auth_is_black($user_id)) {
				return $this->error('无登录权限~');
			}

			$nickname = get_user_meta($user->ID, 'nickname', true);
		} else {
			$user_id = wp_insert_user([
				'user_login' => $session['openid'],
				'nickname' => $nickname,
				'user_nicename' => $nickname,
				'display_name' => $nickname,
				'user_email' => $session['openid'] . '@' . ZhuiGe_Xcx::option_value('user_email_suffix'),
				'role' => 'subscriber',
				'user_pass' => wp_generate_password(16, false),
			]);

			if (is_wp_error($user_id)) {
				return $this->error($user_id->get_error_message());
			}

			//是否他人邀请
			$from_user_id = $this->param_int($request, 'from_user_id', '');
			if ($from_user_id) {
				update_user_meta($user_id, 'zhuige_from_user_id', $from_user_id);

				if (function_exists('zhuige_xcx_add_user_score')) {
					$invite = ZhuiGe_Xcx::option_value('score_invite');
					if ($invite && $invite['switch']) {
						$data['invite'] = $invite;

						zhuige_xcx_add_user_score($from_user_id, 'invite', $user_id, (int)($invite['score']));
					}
				}
			}

			//新用户钩子
			do_action('zhuige_xcx_user_register', [
				'user_id' => $user_id
			]);

			$first = 1;
		}

		update_user_meta($user_id, 'zhuige_channel', $channel);

		if ('weixin' == $channel) {
			update_user_meta($user_id, 'zhuige_wx_session_key', $session['session_key']);
		}

		if (isset($session['unionid']) && !empty($session['unionid'])) {
			update_user_meta($user_id, 'zhuige_unionid', $session['unionid']);
		}

		$zhuige_xcx_user_token = $this->_generate_token();
		update_user_meta($user_id, 'zhuige_xcx_user_token', $zhuige_xcx_user_token);

		$user = [
			'user_id' => $user_id,
			'nickname' => $nickname,
			'token' => $zhuige_xcx_user_token,
			'mobile' => get_user_meta($user_id, 'zhuige_xcx_user_mobile', true)
		];

		if ($first) {
			$user['first'] = $first;
		}

		return $this->success($user);
	}

	/**
	 * 微信登录
	 */
	private function wx_code2openid($code)
	{
		$wechat = ZhuiGe_Xcx::option_value('basic_wechat');
		$app_id = '';
		$app_secret = '';
		if ($wechat) {
			$app_id = $wechat['appid'];
			$app_secret = $wechat['secret'];
		}

		if (empty($app_id) || empty($app_secret)) {
			return false;
		}

		$params = [
			'appid' => $app_id,
			'secret' => $app_secret,
			'js_code' => $code,
			'grant_type' => 'authorization_code'
		];

		$result = wp_remote_get(add_query_arg($params, 'https://api.weixin.qq.com/sns/jscode2session'));
		if (
			!is_array($result)
			|| is_wp_error($result)
			|| $result['response']['code'] != '200'
			|| ($result['body'] && isset($result['body']['errcode']))
		) {
			file_put_contents('wx_login', json_encode($result));
			return false;
		}

		$body = stripslashes($result['body']);
		$session = json_decode($body, true);

		return $session;
	}

	/**
	 * QQ登录
	 */
	private function qq_code2openid($code)
	{
		$qq = ZhuiGe_Xcx::option_value('basic_qq');
		$app_id = '';
		$app_secret = '';
		if ($qq) {
			$app_id = $qq['appid'];
			$app_secret = $qq['secret'];
		}

		if (empty($app_id) || empty($app_secret)) {
			return false;
		}

		$params = [
			'appid' => $app_id,
			'secret' => $app_secret,
			'js_code' => $code,
			'grant_type' => 'authorization_code'
		];

		$result = wp_remote_get(add_query_arg($params, 'https://api.q.qq.com/sns/jscode2session'));
		if (!is_array($result) || is_wp_error($result) || $result['response']['code'] != '200') {
			return false;
		}

		// file_put_contents('qq_login', json_encode($result));

		$body = stripslashes($result['body']);
		$session = json_decode($body, true);

		return $session;
	}

	/**
	 * 百度登录
	 */
	private function bd_code2openid($code)
	{
		$baidu = ZhuiGe_Xcx::option_value('basic_baidu');
		$app_id = '';
		$app_secret = '';
		if ($baidu) {
			$app_id = $baidu['appid'];
			$app_secret = $baidu['secret'];
		}

		if (empty($app_id) || empty($app_secret)) {
			return false;
		}

		$params = [
			'client_id' => $app_id,
			'sk' => $app_secret,
			'code' => $code,
		];

		$result = wp_remote_get(add_query_arg($params, 'https://spapi.baidu.com/oauth/jscode2sessionkey'));
		if (!is_array($result) || is_wp_error($result) || $result['response']['code'] != '200') {
			return false;
		}

		// file_put_contents('bd_login', json_encode($result));

		$body = stripslashes($result['body']);
		$session = json_decode($body, true);

		return $session;
	}

	/**
	 * 用户信息 获取
	 */
	public function get_info($request)
	{
		$user_id = get_current_user_id();

		$cover = get_user_meta($user_id, 'zhuige_xcx_user_cover', true);
		if (empty($cover)) {
			$cover = ZHUIGE_XCX_BASE_URL . 'public/images/placeholder.jpg';
		}

		$data = [
			'user_id' => $user_id,
			'avatar' => ZhuiGe_Xcx::user_avatar($user_id),
			'nickname' => get_user_meta($user_id, 'nickname', true),
			'mobile' => get_user_meta($user_id, 'zhuige_xcx_user_mobile', true),
			'sign' => get_user_meta($user_id, 'zhuige_xcx_user_sign', true),
			'weixin' => get_user_meta($user_id, 'zhuige_xcx_user_weixin', true),
			'reward' => get_user_meta($user_id, 'zhuige_xcx_user_reward', true),
			'cover' => $cover
		];

		if (function_exists('zhuige_xcx_certify_is_certify')) {
			$data['certify'] = zhuige_xcx_certify_is_certify($user_id);
		}

		if (function_exists('zhuige_xcx_vip_is_vip')) {
			$data['vip'] = zhuige_xcx_vip_is_vip($user_id);
		}

		return $this->success($data);
	}

	/**
	 * 用户信息 设置
	 */
	public function set_info($request)
	{
		$user_id = get_current_user_id();

		$nickname = $this->param($request, 'nickname', '');
		$sign = $this->param($request, 'sign', '');
		$os = $this->param($request, 'os', 'wx');
		if (!$this->msg_sec_check($nickname . $sign, $os)) {
			return $this->error('请勿发布敏感信息');
		}

		if (!$nickname) {
			return $this->error('昵称不可为空');
		}
		update_user_meta($user_id, 'nickname', $nickname);
		update_user_meta($user_id, 'zhuige_xcx_user_sign', $sign);

		$cover = $this->param($request, 'cover', '');
		if (!empty($cover)) {
			update_user_meta($user_id, 'zhuige_xcx_user_cover', $cover);
		}

		$avatar = $this->param($request, 'avatar', '');
		if (!empty($avatar)) {
			update_user_meta($user_id, 'zhuige_xcx_user_avatar', $avatar);
		}

		$weixin = $this->param($request, 'weixin', '');
		update_user_meta($user_id, 'zhuige_xcx_user_weixin', $weixin);

		$reward = $this->param($request, 'reward', '');
		update_user_meta($user_id, 'zhuige_xcx_user_reward', $reward);

		return $this->success('设置成功');
	}

	/**
	 * 初始化昵称头像
	 */
	public function init_info($request)
	{
		$user_id = get_current_user_id();

		$nickname = $this->param($request, 'nickname', '');
		$os = $this->param($request, 'os', 'wx');
		if (!$this->msg_sec_check($nickname, $os)) {
			return $this->error('请勿发布敏感信息');
		}

		$avatar = $this->param($request, 'avatar', '');
		if (!empty($avatar)) {
			update_user_meta($user_id, 'zhuige_xcx_user_avatar', $avatar);
		}

		return $this->success('设置成功');
	}

	/**
	 * 用户 点赞 文章
	 */
	public function user_like($request)
	{
		$user_id = get_current_user_id();

		$post_id = $this->param_int($request, 'post_id', 0);
		if (empty($post_id)) {
			return $this->error('缺少参数');
		}

		global $wpdb;
		$table_post_like = $wpdb->prefix . 'zhuige_xcx_post_like';
		$post_like_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM `$table_post_like` WHERE user_id=%d AND post_id=%d",
				$user_id,
				$post_id
			)
		);

		$is_like = 0;
		$user = ['user_id' => $user_id];
		if ($post_like_id) {
			$is_like = 0;

			$wpdb->query("DELETE FROM `$table_post_like` WHERE id=$post_like_id");
		} else {
			$is_like = 1;
			$user['avatar'] = ZhuiGe_Xcx::user_avatar($user_id);

			$wpdb->insert($table_post_like, [
				'user_id' => $user_id,
				'post_id' => $post_id,
				'post_status' => 'publish',
				'time' => time()
			]);
		}

		//通知
		$post = get_post($post_id);
		$to_id = $post->post_author;
		$table_notify = $wpdb->prefix . 'zhuige_xcx_notify';
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM `$table_notify` WHERE `type`='like' AND `from_id`=%d AND `to_id`=%d AND `post_id`=%d",
				$user_id,
				$to_id,
				$post_id
			)
		);
		if ($is_like == 1) {
			$wpdb->insert($table_notify, [
				'type' => 'like',
				'from_id' => $user_id,
				'to_id' => $to_id,
				'post_id' => $post_id,
				'isread' => 0,
				'time' => time()
			]);
		}

		$like_count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT count(id) FROM `$table_post_like` WHERE post_id=%d",
				$post_id
			)
		);

		update_post_meta($post_id, 'like_count', $like_count);

		//添加积分
		if ($is_like == 1 && function_exists('zhuige_xcx_add_user_score_by_task')) {
			zhuige_xcx_add_user_score_by_task('like', $post->post_type . ',' . $post_id);
		}

		return $this->success([
			'is_like' => $is_like,
			'user' => $user,
			'like_count' => $like_count,
		]);
	}

	/**
	 * 用户 收藏 文章
	 */
	public function user_favorite($request)
	{
		$post_id = $this->param_int($request, 'post_id', 0);
		if (empty($post_id)) {
			return $this->error('缺少参数');
		}

		$user_id = get_current_user_id();

		global $wpdb;
		$table_post_favorite = $wpdb->prefix . 'zhuige_xcx_post_favorite';
		$post_favorite_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM `$table_post_favorite` WHERE user_id=%d AND post_id=%d",
				$user_id,
				$post_id
			)
		);

		$is_favorite = 0;
		if ($post_favorite_id) {
			$is_favorite = 0;

			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM `$table_post_favorite` WHERE id=%d",
					$post_favorite_id
				)
			);
		} else {
			$is_favorite = 1;

			$wpdb->insert($table_post_favorite, [
				'user_id' => $user_id,
				'post_id' => $post_id,
				'post_status' => 'publish',
				'time' => time()
			]);
		}

		//通知
		$post = get_post($post_id);
		$to_id = $post->post_author;
		$table_notify = $wpdb->prefix . 'zhuige_xcx_notify';
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM `$table_notify` WHERE `type`='favorite' AND `from_id`=%d AND `to_id`=%d AND `post_id`=%d",
				$user_id,
				$to_id,
				$post_id
			)
		);

		if ($is_favorite == 1) {
			$wpdb->insert($table_notify, [
				'type' => 'favorite',
				'from_id' => $user_id,
				'to_id' => $to_id,
				'post_id' => $post_id,
				'isread' => 0,
				'time' => time()
			]);
		}

		$favorites = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT count(id) FROM `$table_post_favorite` WHERE `post_id`=%d",
				$post_id
			)
		);
		update_post_meta($post_id, 'zhuige_favorites', $favorites);

		//添加积分
		if ($is_favorite == 1 && function_exists('zhuige_xcx_add_user_score_by_task')) {
			zhuige_xcx_add_user_score_by_task('favorite', $post->post_type . ',' . $post_id);
		}

		return $this->success(['is_favorite' => $is_favorite, 'favorites' => $favorites]);
	}

	/**
	 * 关注 用户
	 */
	public function follow_user($request)
	{
		$user_id = get_current_user_id();

		$follow_user_id = $this->param_int($request, 'user_id', 0);
		if (empty($follow_user_id)) {
			return $this->error('缺少参数');
		}

		if ($user_id == $follow_user_id) {
			return $this->error('自己不能关注自己');
		}

		global $wpdb;
		$table_follow_user = $wpdb->prefix . 'zhuige_xcx_follow_user';
		$follow_user_id_exist = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM `$table_follow_user` WHERE user_id=%d AND follow_user_id=%d",
				$user_id,
				$follow_user_id
			)
		);

		if ($follow_user_id_exist) {
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM `$table_follow_user` WHERE id=$follow_user_id_exist"
				)
			);
			$is_follow = 0;
		} else {
			$wpdb->insert($table_follow_user, [
				'user_id' => $user_id,
				'follow_user_id' => $follow_user_id,
				'time' => time(),
			]);
			$is_follow = 1;
		}

		//通知
		$table_notify = $wpdb->prefix . 'zhuige_xcx_notify';
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM `$table_notify` WHERE `type`='follow' AND `from_id`=%d AND `to_id`=%d",
				$user_id,
				$follow_user_id
			)
		);
		if ($is_follow == 1) {
			$wpdb->insert($table_notify, [
				'type' => 'follow',
				'from_id' => $user_id,
				'to_id' => $follow_user_id,
				'isread' => 0,
				'time' => time()
			]);
		}

		return $this->success(['is_follow' => $is_follow]);
	}

	/**
	 * 关注 模块
	 */
	public function follow_forum($request)
	{
		$forum_id = $this->param_int($request, 'forum_id', 0);
		if (empty($forum_id)) {
			return $this->error('缺少参数');
		}

		$my_user_id = get_current_user_id();

		$forum = get_post($forum_id);
		if ($my_user_id == $forum->post_author) {
			return $this->error('您是创建者');
		}

		global $wpdb;
		$table_zhuige_bbs_forum_users = $wpdb->prefix . 'zhuige_bbs_forum_users';
		$follow_forum_id_exist = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM `$table_zhuige_bbs_forum_users` WHERE `user_id`=%d AND `forum_id`=%d",
				$my_user_id,
				$forum_id
			)
		);

		if ($follow_forum_id_exist) {
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM `$table_zhuige_bbs_forum_users` WHERE id=%d",
					$follow_forum_id_exist
				)
			);
			$is_follow = 0;
		} else {
			$wpdb->insert($table_zhuige_bbs_forum_users, [
				'user_id' => $my_user_id,
				'forum_id' => $forum_id,
				'time' => time(),
			]);
			$is_follow = 1;
		}

		$users = [];
		$user = $this->param_int($request, 'user', 0);
		if ($user) {
			$owner = [
				'user_id' => $forum->post_author,
				'nickname' => get_user_meta($forum->post_author, 'nickname', true),
				'avatar' => ZhuiGe_Xcx::user_avatar($forum->post_author),
				'owner' => 1,
			];

			if (function_exists('zhuige_xcx_certify_is_certify')) {
				$owner['certify'] = zhuige_xcx_certify_is_certify($forum->post_author);
			}

			if (function_exists('zhuige_xcx_vip_is_vip')) {
				$owner['vip'] = zhuige_xcx_vip_is_vip($forum->post_author);
			}

			$users[] = $owner;

			$user_ids = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT `user_id` FROM `$table_zhuige_bbs_forum_users` WHERE forum_id=%d ORDER BY `id` DESC LIMIT 10",
					$forum_id
				)
			);
			$user_ids = array_column($user_ids, 'user_id');
			foreach ($user_ids as $user_id) {
				$user = [
					'user_id' => $user_id,
					'nickname' => get_user_meta($user_id, 'nickname', true),
					'avatar' => ZhuiGe_Xcx::user_avatar($user_id),
					'owner' => 0
				];

				if (function_exists('zhuige_xcx_certify_is_certify')) {
					$user['certify'] = zhuige_xcx_certify_is_certify($user_id);
				}

				if (function_exists('zhuige_xcx_vip_is_vip')) {
					$user['vip'] = zhuige_xcx_vip_is_vip($user_id);
				}

				$users[] = $user;
			}
		}

		$user_count = zhuige_bbs_forum_user_count($forum_id);

		//添加积分
		if ($is_follow == 1 && function_exists('zhuige_xcx_add_user_score_by_task')) {
			zhuige_xcx_add_user_score_by_task('forum_join', $forum_id);
		}

		return $this->success(['is_follow' => $is_follow, 'users' => $users, 'user_count' => (int)$user_count]);
	}

	/**
	 * 我的朋友 - 其实还是我关注的用户 - 在发帖时 @朋友 使用
	 */
	public function my_friends($request)
	{
		$my_user_id = get_current_user_id();

		global $wpdb;
		$table_follow_user = $wpdb->prefix . 'zhuige_xcx_follow_user';
		$users = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT follow_user_id FROM `$table_follow_user` WHERE user_id=%d ORDER BY id DESC",
				$my_user_id
			)
		);

		$data = [];
		foreach ($users as &$user) {
			$item = [
				'user_id' => $user->follow_user_id,
				'nickname' => get_user_meta($user->follow_user_id, 'nickname', true),
				'avatar' => ZhuiGe_Xcx::user_avatar($user->follow_user_id),
				'checked' => 0,
			];

			if (function_exists('zhuige_xcx_certify_is_certify')) {
				$item['certify'] = zhuige_xcx_certify_is_certify($user->follow_user_id);
			}

			if (function_exists('zhuige_xcx_vip_is_vip')) {
				$item['vip'] = zhuige_xcx_vip_is_vip($user->follow_user_id);
			}

			$data[] = $item;
		}

		$result = [];
		$result['users'] = $data;

		return $this->success($result);
	}

	/**
	 * 我关注的用户
	 */
	public function my_follows($request)
	{
		$user_id = $this->param_int($request, 'user_id', 0);
		$my_user_id = get_current_user_id();
		if (!$user_id) {
			$user_id = $my_user_id;
		}

		if ($my_user_id != $user_id && (int)(get_user_meta($user_id, 'zhuige_user_secret_follow', true)) == 1) {
			return $this->success(['tip' => '已关闭对他人可见', 'users' => [], 'more' => 'nomore']);
		}

		$offset = $this->param_int($request, 'offset', 0);

		$result = [];

		$per_page_count = 12;

		global $wpdb;
		$table_follow_user = $wpdb->prefix . 'zhuige_xcx_follow_user';
		$users = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT follow_user_id FROM `$table_follow_user` WHERE user_id=%d ORDER BY id DESC LIMIT %d, %d",
				$user_id,
				$offset,
				$per_page_count
			)
		);
		if (empty($users)) {
			$result['users'] = [];
			$result['more'] = 'nomore';
			return $this->success($result);
		}

		$data = [];
		foreach ($users as &$user) {
			$item = [
				'user_id' => $user->follow_user_id,
				'nickname' => get_user_meta($user->follow_user_id, 'nickname', true),
				'avatar' => ZhuiGe_Xcx::user_avatar($user->follow_user_id),
				'post_count' => zhuige_xcx_user_post_count($user->follow_user_id),
				'fans_count' => zhuige_xcx_user_fans_count($user->follow_user_id),
			];

			if (function_exists('zhuige_xcx_certify_is_certify')) {
				$item['certify'] = zhuige_xcx_certify_is_certify($user->follow_user_id);
			}

			if (function_exists('zhuige_xcx_vip_is_vip')) {
				$item['vip'] = zhuige_xcx_vip_is_vip($user->follow_user_id);
			}

			if ($my_user_id) {
				$item['is_follow'] = 1;

				$follow_user = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT id FROM `$table_follow_user` WHERE user_id=%d AND follow_user_id=%d",
						$user->follow_user_id,
						$my_user_id
					)
				);
				$item['is_fans'] = ($follow_user ? 1 : 0);
			} else {
				$item['is_follow'] = 0;
				$item['is_fans'] = 0;
			}

			$data[] = $item;
		}

		$result['users'] = $data;
		$result['more'] = (count($data) == $per_page_count ? 'more' : 'nomore');

		return $this->success($result);
	}

	/**
	 * 我的粉丝
	 */
	public function my_fans($request)
	{
		$user_id = $this->param_int($request, 'user_id', 0);
		$my_user_id = get_current_user_id();
		if (!$user_id) {
			$user_id = $my_user_id;
		}

		if ($my_user_id != $user_id && (int)(get_user_meta($user_id, 'zhuige_user_secret_fans', true)) == 1) {
			return $this->success(['tip' => '已关闭对他人可见', 'users' => [], 'more' => 'nomore']);
		}

		$offset = $this->param_int($request, 'offset', 0);

		$result = [];
		if ($offset == 0) {
			$result['follow_count'] = $this->_follow_count($user_id);
			$result['fan_count'] = $this->_fan_count($user_id);
		}

		$per_page_count = 12;

		global $wpdb;
		$table_follow_user = $wpdb->prefix . 'zhuige_xcx_follow_user';
		$users = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT `user_id` FROM `$table_follow_user` WHERE follow_user_id=%d ORDER BY id DESC LIMIT %d, %d",
				$user_id,
				$offset,
				$per_page_count
			)
		);
		if (empty($users)) {
			$result['users'] = [];
			$result['more'] = 'nomore';
			return $this->success($result);
		}

		$data = [];
		foreach ($users as &$user) {
			$item = [
				'user_id' => $user->user_id,
				'nickname' => get_user_meta($user->user_id, 'nickname', true),
				'avatar' => ZhuiGe_Xcx::user_avatar($user->user_id),
				'post_count' => zhuige_xcx_user_post_count($user->user_id),
				'fans_count' => zhuige_xcx_user_fans_count($user->user_id),
			];

			if (function_exists('zhuige_xcx_certify_is_certify')) {
				$item['certify'] = zhuige_xcx_certify_is_certify($user->user_id);
			}

			if (function_exists('zhuige_xcx_vip_is_vip')) {
				$item['vip'] = zhuige_xcx_vip_is_vip($user->user_id);
			}

			if ($my_user_id) {
				$follow_user = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT id FROM `$table_follow_user` WHERE `user_id`=%d AND `follow_user_id`=%d",
						$my_user_id,
						$user->user_id
					)
				);
				$item['is_follow'] = ($follow_user ? 1 : 0);
				$item['is_fans'] = 1;
			} else {
				$item['is_follow'] = 0;
				$item['is_fans'] = 0;
			}

			$data[] = $item;
		}

		$result['users'] = $data;
		$result['more'] = (count($data) == $per_page_count ? 'more' : 'nomore');

		return $this->success($result);
	}

	/**
	 * 我的小站
	 */
	public function home($request)
	{
		$user_id = $this->param_int($request, 'user_id', 0);
		$my_user_id = get_current_user_id();
		if (!$user_id && $my_user_id) {
			$user_id = $my_user_id;
		}

		//用户小站
		do_action('zhuige_xcx_user_site', [
			'user_id' => $user_id
		]);

		$user = [
			'user_id' => $user_id,
			'avatar' => ZhuiGe_Xcx::user_avatar($user_id),
			'nickname' => get_user_meta($user_id, 'nickname', true),
		];

		if (function_exists('zhuige_xcx_certify_is_certify')) {
			$user['certify'] = zhuige_xcx_certify_is_certify($user_id);
		}

		if (function_exists('zhuige_xcx_vip_is_vip')) {
			$user['vip'] = zhuige_xcx_vip_is_vip($user_id);
		}

		$sign = get_user_meta($user_id, 'zhuige_xcx_user_sign', true);
		if (empty($sign)) {
			$sign = '这个家伙很懒，什么都没写...';
		}
		$user['sign'] = $sign;

		$user_cover = get_user_meta($user_id, 'zhuige_xcx_user_cover', true);
		if (empty($user_cover)) {
			$user_cover = ZHUIGE_XCX_BASE_URL . "public/images/placeholder.jpg";
		}
		$user['cover'] = $user_cover;

		global $wpdb;
		if ($my_user_id) {
			$user['is_me'] = ($user_id == $my_user_id) ? 1 : 0;

			$table_follow_user = $wpdb->prefix . 'zhuige_xcx_follow_user';
			$follow_user = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT id FROM `$table_follow_user` WHERE user_id=%d AND follow_user_id=%d",
					$my_user_id,
					$user_id
				)
			);
			$user['is_follow'] = ($follow_user ? 1 : 0);

			$fans_user = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT id FROM `$table_follow_user` WHERE user_id=%d AND follow_user_id=%d",
					$user_id,
					$my_user_id
				)
			);
			$user['is_fans'] = ($fans_user ? 1 : 0);

			// 删除帖子-投票的权限
			$user['delete_topic'] = 0;
			$user['delete_vote'] = 0;
			if (ZhuiGe_Xcx_Addon::is_active('zhuige-auth')) {
				$auth = ZhuiGe_Xcx::option_value('auth_delete_topic');
				if ($auth == 'all' || ($auth == 'vip' && isset($user['vip']) && $user['vip']['status'] == 1)) {
					$user['delete_topic'] = 1;
				}

				$auth = ZhuiGe_Xcx::option_value('auth_delete_vote');
				if ($auth == 'all' || ($auth == 'vip' && isset($user['vip']) && $user['vip']['status'] == 1)) {
					$user['delete_vote'] = 1;
				}
			}
		} else {
			$user['is_me'] = 0;
			$user['is_follow'] = 0;
			$user['is_fans'] = 0;

			// 删除帖子-投票的权限
			$user['delete_topic'] = 0;
			$user['delete_vote'] = 0;
		}
		$data['user'] = $user;

		$table_follow_user = $wpdb->prefix . 'zhuige_xcx_follow_user';
		$follow_count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT count(id) FROM `$table_follow_user` WHERE user_id=%d",
				$user_id
			)
		);
		$stat['follow_count'] = (int)$follow_count;

		$fans_count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT count(id) FROM `$table_follow_user` WHERE follow_user_id=%d",
				$user_id
			)
		);
		$stat['fans_count'] = (int)$fans_count;

		$table_follow_forum = $wpdb->prefix . 'zhuige_bbs_forum_users';
		$forum_user_count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT count(`id`) FROM `$table_follow_forum` WHERE `user_id`=%d",
				$user_id
			)
		);
		$table_posts = $wpdb->prefix . 'posts';
		$forum_owner_count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT count(`ID`) FROM `$table_posts` WHERE `post_author`=%d AND `post_type`='zhuige_bbs_forum' AND `post_status`='publish'",
				$user_id
			)
		);
		$stat['forum_count'] = (int)$forum_user_count + (int)$forum_owner_count;

		$table_posts = $wpdb->prefix . 'posts';
		$table_post_like = $wpdb->prefix . 'zhuige_xcx_post_like';
		$likeme_count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(id) FROM `$table_post_like` WHERE post_id IN (SELECT `id` FROM `$table_posts` WHERE `post_author`=%d AND `post_status`='publish')",
				$user_id
			)
		);
		$stat['likeme_count'] = (int)$likeme_count;

		$data['stat'] = $stat;


		// 推荐广告
		$user_home_rec_ad = ZhuiGe_Xcx::option_value('user_home_rec_ad');

		if ($user_home_rec_ad && $user_home_rec_ad['switch']) {
			$rec_ad = [];
			$rec_ad['title'] = $user_home_rec_ad['title'];
			$items = [];
			foreach ($user_home_rec_ad['items'] as $item_ad) {
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

			$data['rec_ad'] = $rec_ad;
		}

		// 私信按钮
		$data['btn_message'] = ZhuiGe_Xcx_Addon::is_active('zhuige-message') ? 1 : 0;

		return $this->success($data);
	}

	/**
	 * 用户统计
	 */
	public function my_statistics($request)
	{
		$my_user_id = get_current_user_id();
		if (!$my_user_id) {
			return $this->success([
				'post_count' => 0,
				'fans_count' => 0,
				'follow_count' => 0,
				'likeme_count' => 0,
			]);
		}

		global $wpdb;

		$stat['post_count'] = zhuige_xcx_user_post_count($my_user_id);
		$stat['fans_count'] = zhuige_xcx_user_fans_count($my_user_id);
		$stat['follow_count'] = zhuige_xcx_user_follow_count($my_user_id);

		$table_posts = $wpdb->prefix . 'posts';
		$table_post_like = $wpdb->prefix . 'zhuige_xcx_post_like';
		$likeme_count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(id) FROM `$table_post_like` WHERE  post_id IN (SELECT `id` FROM `$table_posts` WHERE `post_author`=%d AND `post_status`='publish')",
				$my_user_id
			)
		);
		$stat['likeme_count'] = $likeme_count;

		$stat['nickname'] = get_user_meta($my_user_id, 'nickname', true);
		$stat['avatar'] = ZhuiGe_Xcx::user_avatar($my_user_id);

		if (function_exists('zhuige_xcx_certify_is_certify')) {
			$stat['certify'] = zhuige_xcx_certify_is_certify($my_user_id);
		}

		if (function_exists('zhuige_xcx_vip_is_vip')) {
			$stat['vip'] = zhuige_xcx_vip_is_vip($my_user_id);
		}

		return $this->success($stat);
	}

	private function _notify_count($my_user_id)
	{
		global $wpdb;

		if (ZhuiGe_Xcx_Addon::is_active('zhuige-system_notice')) {
			$table_system_notice_notify = $wpdb->prefix . 'zhuige_xcx_system_notice_notify';
			$data['system_count'] = (int)$wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(`id`) FROM `$table_system_notice_notify` WHERE `user_id`=%d AND `isread`='0'",
					$my_user_id
				)
			);
		} else {
			$data['system_count'] = 0;
		}

		if (ZhuiGe_Xcx_Addon::is_active('zhuige-message')) {
			$table_message = $wpdb->prefix . 'zhuige_xcx_message';
			$data['message_count'] = (int)$wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(`id`) FROM `$table_message` WHERE `to_id`=%d AND `isread`='0'",
					$my_user_id
				)
			);
		} else {
			$data['message_count'] = 0;
		}

		$table_notify = $wpdb->prefix . 'zhuige_xcx_notify';
		$data['like_count'] = (int)$wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(`id`) FROM `$table_notify` WHERE `type`='like' AND `to_id`=%d AND `post_status`='publish' AND `isread`=0",
				$my_user_id
			)
		);

		$data['favorite_count'] = (int)$wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(`id`) FROM `$table_notify` WHERE `type`='favorite' AND `to_id`=%d AND `post_status`='publish' AND `isread`=0",
				$my_user_id
			)
		);

		$data['comment_count'] = (int)$wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(`id`) FROM `$table_notify` WHERE `type`='comment' AND `to_id`=%d AND `post_status`='publish' AND `isread`=0",
				$my_user_id
			)
		);

		$data['follow_count'] = (int)$wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(`id`) FROM `$table_notify` WHERE `type`='follow' AND `to_id`=%d AND `post_status`='publish' AND `isread`=0",
				$my_user_id
			)
		);

		$data['ait_count'] = (int)$wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(`id`) FROM `$table_notify` WHERE (`type`='reply' OR `type`='ait') AND `to_id`=%d AND `post_status`='publish' AND `isread`=0",
				$my_user_id
			)
		);

		return $data;
	}

	/**
	 * 获取我的提醒
	 */
	public function get_notify($request)
	{
		$my_user_id = get_current_user_id();

		$offset = $this->param_int($request, 'offset', 0);
		$per_page_count = ZhuiGe_Xcx::POSTS_PER_PAGE;

		$type = $this->param($request, 'type', '');
		$subWhere = '';
		if ($type) {
			// if ($type == 'ait') {
			// 	$subWhere = " (`type`='reply' OR `type`='ait') AND ";
			// } else {
			$subWhere = " `type`='$type' AND ";
			// }
		}

		global $wpdb;
		$table_notify = $wpdb->prefix . 'zhuige_xcx_notify';
		$notifys = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM `$table_notify` WHERE $subWhere `to_id`=%d AND `post_status`='publish' ORDER BY id DESC LIMIT %d, %d",
				$my_user_id,
				$offset,
				$per_page_count
			),
			ARRAY_A
		);

		foreach ($notifys as &$notify) {
			$from_id = $notify['from_id'];

			// if ($notify['type'] == 'system') {
			// 	$notify['from'] = [
			// 		'user_id' => $from_id,
			// 		'nickname' => '系统消息',
			// 		'avatar' => ZhuiGe_Xcx::option_image_url(ZhuiGe_Xcx::option_value('system_notice_logo'), 'placeholder.jpg'),
			// 		'certify' => ['status' => 0]
			// 	];
			// } else {
			$notify['from'] = [
				'user_id' => $from_id,
				'nickname' => get_user_meta($from_id, 'nickname', true),
				'avatar' => get_user_meta($from_id, 'zhuige_xcx_user_avatar', true),
			];
			// }

			if ($notify['type'] == 'like' || $notify['type'] == 'favorite' || $notify['type'] == 'comment') {
				$post = get_post($notify['post_id']);
				if ($post->post_type == 'zhuige_bbs_topic') {
					$content = zhuige_xcx_get_post_excerpt($post);
					$notify['link'] = '/pages/bbs/detail/detail?id=' . $notify['post_id'];
				} else if ($post->post_type == 'zhuige_vote') {
					$content = zhuige_xcx_get_post_excerpt($post);
					$notify['link'] = '/pages/vote/detail/detail?id=' . $notify['post_id'];
				} else if ($post->post_type == 'post') {
					$content = $post->post_title;
					$notify['link'] = '/pages/cms/detail/detail?id=' . $notify['post_id'];
				} else if ($post->post_type == 'zhuige_res') {
					$content = $post->post_title;
					$notify['link'] = '/pages/resource/detail/detail?id=' . $notify['post_id'];
				} else {
					$content = $post->post_title;
				}
			}

			if ($notify['type'] == 'like') {
				$content = "点赞了你的：$content";
			} else if ($notify['type'] == 'favorite') {
				$content = "收藏了你的：$content";
			} else if ($notify['type'] == 'comment') {
				$content = "评论了你的：$content";
			} else if ($notify['type'] == 'follow') {
				$content = "关注了你";
				$notify['link'] = '/pages/user/home/home?user_id=' . $notify['from_id'];
			}
			// else if ($notify['type'] == 'reply') {
			// 	$content = "在评论中@了你：$content";
			// } else if ($notify['type'] == 'ait') {
			// 	$content = "在帖子中@了你：$content";
			// } else if ($notify['type'] == 'system') {
			// 	$table_system_notice = $wpdb->prefix . 'zhuige_xcx_system_notice';
			// 	$title = $wpdb->get_var($wpdb->prepare("SELECT `title` FROM `$table_system_notice` WHERE `id`=%d", $notify['post_id']));
			// 	$content = $title;
			// }
			$notify['content'] = $content;

			$notify['time'] = zhuige_xcx_time_stamp_beautify($notify['time']);

			unset($notify['from_id']);
		}

		$data['notifys'] = $notifys;
		$data['more'] = (count($notifys) == $per_page_count ? 'more' : 'nomore');
		return $this->success($data);
	}

	/**
	 * 获取我的提醒
	 */
	public function notify_read($request)
	{
		$my_user_id = get_current_user_id();

		$notify_id = $this->param_int($request, 'notify_id', 0);
		if ($notify_id) {
			global $wpdb;
			$table_notify = $wpdb->prefix . 'zhuige_xcx_notify';
			$wpdb->query($wpdb->prepare("UPDATE `$table_notify` SET `isread`=1 WHERE `id`=%d AND `to_id`=%d AND `isread`=0", $notify_id, $my_user_id));
		}

		$data = $this->_notify_count($my_user_id);

		$data['sys_msg'] = ZhuiGe_Xcx_Addon::is_active('zhuige-system_notice') ? 1 : 0;
		$data['ait_msg'] = 0;
		$data['message'] = ZhuiGe_Xcx_Addon::is_active('zhuige-message') ? 1 : 0;

		return $this->success($data);
	}

	/**
	 * 一键清理未读提醒
	 */
	public function notify_clear($request)
	{
		$my_user_id = get_current_user_id();

		global $wpdb;
		$table_notify = $wpdb->prefix . 'zhuige_xcx_notify';
		$wpdb->query($wpdb->prepare("UPDATE `$table_notify` SET `isread`=1 WHERE `to_id`=%d AND `isread`=0", $my_user_id));

		if (ZhuiGe_Xcx_Addon::is_active('zhuige-system_notice')) {
			$table_system_notice_notify = $wpdb->prefix . 'zhuige_xcx_system_notice_notify';
			$wpdb->update($table_system_notice_notify, ['isread' => '1'], ['isread' => '0', 'user_id' => $my_user_id]);
		}

		if (ZhuiGe_Xcx_Addon::is_active('zhuige-message')) {
			$table_message = $wpdb->prefix . 'zhuige_xcx_message';
			$wpdb->update($table_message, ['isread' => '1'], ['isread' => '0', 'to_id' => $my_user_id]);
		}

		return $this->success();
	}

	/**
	 * 设置手机号
	 */
	public function set_mobile($request)
	{
		$user_id = get_current_user_id();

		$code = $this->param($request, 'code', '');
		$encrypted_data = $this->param($request, 'encrypted_data', '');
		$iv = $this->param($request, 'iv', '');
		if (empty($code) || empty($encrypted_data) || empty($iv)) {
			return $this->error('缺少参数');
		}

		$os = $this->param($request, 'os', '');

		$mobile = '';
		if ($os == 'wx') {
			$wechat = ZhuiGe_Xcx::option_value('basic_wechat');
			$app_id = '';
			$app_secret = '';
			if ($wechat) {
				$app_id = $wechat['appid'];
				$app_secret = $wechat['secret'];
			}

			if (empty($app_id) || empty($app_secret)) {
				return $this->error('未配置微信小程序信息');
			}

			$session = $this->wx_code2openid($code);
			if (!$session) {
				return $this->error('授权失败');
			}

			$res = $this->weixin_decryptData($app_id, $session['session_key'], $encrypted_data, $iv, $data);
			if ($res != 0) {
				return $this->error('系统异常');
			}
			$dataMobile = json_decode($data, true);
			$mobile = $dataMobile['phoneNumber'];
		} else {
			return $this->error('暂不支持此平台');
		}
		update_user_meta($user_id, 'zhuige_xcx_user_mobile', $mobile);


		return $this->success(['mobile' => $mobile]);
	}

	/**
	 * 增加分享积分
	 */
	public function share_score($request)
	{
		$user_id = $this->param_int($request, 'source', '');
		if (empty($user_id)) {
			return $this->error('缺少参数');
		}

		//添加积分
		if (function_exists('zhuige_xcx_add_user_score_by_task')) {
			zhuige_xcx_add_user_score_by_task('share', '', $user_id);
		}

		return $this->success('请求成功');
	}

	/**
	 * 检验数据的真实性，并且获取解密后的明文.
	 * @param $encryptedData string 加密的用户数据
	 * @param $iv string 与用户数据一同返回的初始向量
	 * @param $data string 解密后的原文
	 *
	 * @return int 成功 0，失败返回对应的错误码
	 */
	private function weixin_decryptData($appid, $session, $encryptedData, $iv, &$data)
	{
		$ErrorCode = array(
			'OK'                => 0,
			'IllegalAesKey'     => -41001,
			'IllegalIv'         => -41002,
			'IllegalBuffer'     => -41003,
			'DecodeBase64Error' => -41004
		);

		if (strlen($session) != 24) {
			return array('code' => $ErrorCode['IllegalAesKey'], 'message' => 'session_key 长度不合法', 'session_key' => $session);
		}
		$aesKey = base64_decode($session);
		if (strlen($iv) != 24) {
			return array('code' => $ErrorCode['IllegalIv'], 'message' => 'iv 长度不合法', 'iv' => $iv);
		}
		$aesIV = base64_decode($iv);
		$aesCipher = base64_decode($encryptedData);
		$result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
		$data_decode = json_decode($result);
		if ($data_decode  == NULL) {
			return array('code' => $ErrorCode['IllegalBuffer'], 'message' => '解密失败，非法缓存');
		}
		if ($data_decode->watermark->appid != $appid) {
			return array('code' => $ErrorCode['IllegalBuffer'], 'message' => '解密失败，AppID 不正确');
		}
		$data = $result;
		return $ErrorCode['OK'];
	}

	private function _generate_token()
	{
		return md5(uniqid());
	}

	//粉丝数量
	private function _fan_count($user_id)
	{
		global $wpdb;
		$table_follow_user = $wpdb->prefix . 'zhuige_xcx_follow_user';
		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(id) FROM `$table_follow_user` WHERE `follow_user_id`=%d",
				$user_id
			)
		);
	}

	//关注数量
	private function _follow_count($user_id)
	{
		global $wpdb;
		$table_follow_user = $wpdb->prefix . 'zhuige_xcx_follow_user';
		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT  COUNT(id) FROM `$table_follow_user` WHERE `user_id`=%d",
				$user_id
			)
		);
	}

	/**
	 * 下载头像
	 */
	function download_wx_avatar($url, $user_id)
	{
		$header = [
			'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:45.0) Gecko/20100101 Firefox/45.0',
			'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
			'Accept-Encoding: gzip, deflate',
		];
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		$data = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		if ($code == 200) { //把URL格式的图片转成base64_encode格式的！      
			$imgBase64Code = "data:image/jpeg;base64," . base64_encode($data);
		}
		$img_content = $imgBase64Code; //图片内容  
		if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img_content, $result)) {
			$type = $result[2]; //得到图片类型png jpg gif

			$upload_dir = wp_upload_dir();
			$filename = 'zhuige_xcx_user_avatar_' . $user_id . ".{$type}";
			$filepath = $upload_dir['path'] . '/' . $filename;
			if (file_put_contents($filepath, base64_decode(str_replace($result[1], '', $img_content)))) {
				return ['path' => $filepath, 'url' => $upload_dir['url'] . '/' . $filename];
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}

ZhuiGe_Xcx::$rest_controllers[] = new ZhuiGe_Xcx_User_Controller();
