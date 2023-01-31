<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
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
