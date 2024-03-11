<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2024 www.zhuige.com All rights reserved.
 */

//
// 黑白色样式
//
CSF::createSection($prefix, array(
    'id'    => 'style_gray',
    'title' => '黑白色样式',
    'icon'  => 'fas fa-plus-circle',
));

CSF::createSection($prefix, array(
    'parent' => 'style_gray',
    'title' => '黑白色样式',
    'icon'  => 'fas fa-map-marker',
    'fields' => array(

        array(
            'id'    => 'home_gray_switch',
            'type'  => 'switcher',
            'title' => '开启/停用',
            'subtitle' => '是否开启首页黑白色样式',
            'default' => '0'
        ),

        array(
            'id'    => 'home_gray_css',
            'type'  => 'code_editor',
            'title' => '附加CSS',
            'settings' => array(
                'theme'  => 'mbo',
                'mode'   => 'css',
            ),
            'default' => 'filter: grayscale(90%);',
            'dependency'  => array('home_gray_switch', '==', '1'),
        ),

    )
));
