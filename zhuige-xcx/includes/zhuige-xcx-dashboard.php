<?php

/**
 * 追格小程序
 * Author: 追格
 * Help document: https://www.zhuige.com
 * Copyright © 2022 www.zhuige.com All rights reserved.
 */

function zhuige_xcx_custom_dashboard()
{
	$content = '感谢使用追格小程序！ <br/><br/> 微信客服：jianbing2011 (加开源群、问题咨询、项目定制、购买咨询) <br/><br/> <a href="https://www.zhuige.com" target="_blank">追格官网</a>';
	$res = wp_remote_get("https://www.zhuige.com/api/ad/wordpress?id=zhuige_xcx", ['timeout' => 1, 'sslverify' => false]);
	if (!is_wp_error($res) && $res['response']['code'] == 200) {
		$data = json_decode($res['body'], TRUE);
		if ($data['code'] == 1) {
			$content = $data['data'];
		}
	}

	echo $content;
}

function zhuige_xcx_add_dashboard_widgets()
{
	wp_add_dashboard_widget('zhuige_xcx_dashboard_widget', '追格圈子', 'zhuige_xcx_custom_dashboard');
}

add_action('wp_dashboard_setup', 'zhuige_xcx_add_dashboard_widgets');
