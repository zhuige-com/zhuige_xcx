<?php

/*
 * 追格小程序
 * Author: 追格
 * Help document: https://www.zhuige.com
 * Copyright © 2022 www.zhuige.com All rights reserved.
 */

class ZhuiGe_Xcx_i18n
{
	public function load_plugin_textdomain()
	{
		load_plugin_textdomain(
			'zhuige-xcx',
			false,
			dirname(ZHUIGE_XCX_BASE_NAME) . '/languages/'
		);
	}
}
