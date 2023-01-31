<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
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
	wp_add_dashboard_widget('zhuige_xcx_dashboard_widget', '追格小程序', 'zhuige_xcx_custom_dashboard');
}

add_action('wp_dashboard_setup', 'zhuige_xcx_add_dashboard_widgets');
