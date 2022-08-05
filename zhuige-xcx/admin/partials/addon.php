<?php

/*
 * 追格小程序
 * Author: 追格
 * Help document: https://www.zhuige.com
 * Copyright © 2022 www.zhuige.com All rights reserved.
 */

// 加载插件
function zhuige_xcx_load_addons()
{
    $content = "<ol>";

    $addons = scandir(ZHUIGE_XCX_ADDONS_DIR);
    foreach ($addons as $addon) {
        if ($addon == '.' || $addon == '..' || !is_dir(ZHUIGE_XCX_ADDONS_DIR . $addon)) {
            continue;
        }

        $addon_config_path = ZHUIGE_XCX_ADDONS_DIR . $addon . '/config.json';
        if (!file_exists($addon_config_path)) {
            continue;
        }

        $config = json_decode(file_get_contents($addon_config_path), true);

        $active = in_array($addon, ZhuiGe_Xcx_Addon::$addons);

        $link = $active ? '<a href="javascript:void(0)" data-addon="' . $addon . '" class="btn-zhuige-addon-deactive">禁用</a>'
            : '<a href="javascript:void(0)" data-addon="' . $addon . '" class="btn-zhuige-addon-active">启用</a>';

        $content .= '<li>' . $config['name'] . $link . '</li>';
    }

    $content .= '</ol>';

    return $content;
}

//
// 插件
//
CSF::createSection($prefix, array(
    'title'  => '插件',
    'icon'   => 'fas fa-rocket',
    'fields' => array(

        array(
            'type'    => 'content',
            'content' => zhuige_xcx_load_addons(),
        ),

    )
));
