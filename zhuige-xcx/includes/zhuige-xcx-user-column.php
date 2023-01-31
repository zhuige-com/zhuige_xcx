<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
 */

add_filter('manage_users_columns', 'zhuige_xcx_manage_user_columns', 10, 2);
add_action('manage_users_custom_column', 'zhuige_xcx_manage_user_custom_columnns', 10, 3);

function zhuige_xcx_manage_user_columns($columns)
{
	unset($columns['name']);

	$new_columns = array();
	$new_columns['cb'] = $columns['cb'];
	$new_columns['username'] = $columns['username'];
	$new_columns['mobile'] = '手机号';
	$new_columns['jqnickname'] = '昵称';
	$new_columns['jqchannel'] = '渠道';

	if (ZhuiGe_Xcx_Addon::is_active('zhuige-certify')) {
		$new_columns['jqcertify'] = '追格认证';
	}

	if (ZhuiGe_Xcx_Addon::is_active('zhuige-vip')) {
		$new_columns['jqvip'] = '追格VIP';
	}

	if (ZhuiGe_Xcx_Addon::is_active('zhuige-score')) {
		$new_columns['jqscore'] = '追格积分';
	}

	unset($columns['cb']);
	unset($columns['username']);

	return array_merge($new_columns, $columns);
}

function zhuige_xcx_manage_user_custom_columnns($value, $column_name, $user_id)
{
	if ('mobile' == $column_name) {
		$value = get_user_meta($user_id, 'zhuige_xcx_user_mobile', true);
	} else if ('jqnickname' == $column_name) {
		$value = get_user_meta($user_id, 'nickname', true);
	} else if ('jqchannel' == $column_name) {
		$channel = get_user_meta($user_id, 'zhuige_channel', true);
		if ('weixin' == $channel) {
			$value = '微信';
		} else if ('qq' == $channel) {
			$value = 'QQ';
		} else if ('baidu' == $channel) {
			$value = '百度';
		} else {
			$value = '';
		}
	}

	if (ZhuiGe_Xcx_Addon::is_active('zhuige-certify') && 'jqcertify' == $column_name) {
		global $wpdb;
		$table_user_certify = $wpdb->prefix . 'zhuige_xcx_user_certify';
		$certify = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT `status`,`certify_name` FROM `$table_user_certify` WHERE user_id=%d",
				$user_id
			),
			ARRAY_A
		);

		if ($certify) {
			if ($certify['status'] == 0) {
				$status = '认证中';
			} else if ($certify['status'] == 1) {
				$status = '未通过';
			} else if ($certify['status'] == 2) {
				$status = '已通过(' . $certify['certify_name'] . ')';
			} else {
				$status = '未知';
			}
		} else {
			$status = '未申请';
		}

		$url = add_query_arg(['page' => 'zhuige_user_certify', 'action' => 'edit', 'user_id'  => $user_id], 'admin.php');
		$value = sprintf('<a href="%1$s">%2$s</a>', esc_url(wp_nonce_url($url, 'edit_' . $user_id)), $status);

		return $value;
	}

	if (ZhuiGe_Xcx_Addon::is_active('zhuige-vip') && 'jqvip' == $column_name) {
		$vip = get_user_meta($user_id, 'zhuige_xcx_vip', true);
		if (!$vip) {
			$value = '';
		} else {
			if (time() > intval($vip)) {
				$value = '';
			} else {
				$value = wp_date('Y-m-d H:i:s', $vip);
			}
		}

		return $value;
	}

	if (ZhuiGe_Xcx_Addon::is_active('zhuige-score') && 'jqscore' == $column_name) {
		$score = get_user_meta($user_id, 'zhuige_score', true);
		if (!$score) {
			$score = 0;
		}
		$value = $score;

		return $value;
	}

	return $value;
}

add_filter('get_avatar', 'zhuige_xcx_get_avatar', 10, 2);
function zhuige_xcx_get_avatar($avatar, $id_or_email, $size = 96, $default = '', $alt = '', $args = null)
{
	$zg_avatar = get_user_meta($id_or_email, 'zhuige_xcx_user_avatar', true);
	if ($zg_avatar) {
		return "<img src='$zg_avatar' class='avatar avatar-32 photo' height='32' width='32'>";
	} else {
		return $avatar;
	}
}
