<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
 */

//
// 追格圈子
//
CSF::createSection($prefix, array(
    'id'    => 'bbs',
    'title' => '追格圈子',
    'icon'  => 'fas fa-plus-circle',
));

//
// 圈子
//
CSF::createSection($prefix, array(
    'parent' => 'bbs',
    'title' => '圈子',
    'icon'  => 'fas fa-map-marker',
    'fields' => array(

        array(
            'id'    => 'bbs_forum_mobile_switch',
            'type'  => 'switcher',
            'title' => '开启/停用',
            'subtitle' => '创建圈子是否要求绑定手机号',
            'default' => '0'
        ),

        array(
            'id'     => 'bbs_slide',
            'type'   => 'group',
            'title'  => '幻灯片',
            'fields' => array(
                array(
                    'id'      => 'image',
                    'type'    => 'media',
                    'title'   => '图片',
                    'library' => 'image',
                    'after' => '<a href="https://www.zhuige.com/docs/zg/216.html" target="_blank">图片规格建议</a>',
                ),

                array(
                    'id'    => 'link',
                    'type'  => 'text',
                    'title' => '链接',
                    'default' => 'https://www.zhuige.com',
                    'after' => '<a href="https://www.zhuige.com/docs/zg/209.html" target="_blank">如何获取链接</a>',
                ),

                array(
                    'id'    => 'switch',
                    'type'  => 'switcher',
                    'title' => '停用/启用',
                    'default' => '1'
                ),
            ),
        ),

        array(
            'id'          => 'bbs_nav_cat',
            'type'        => 'select',
            'title'       => '导航设置',
            'placeholder' => '选择分类',
            'chosen'      => true,
            'multiple'    => true,
            'sortable'    => true,
            'ajax'        => true,
            'options'     => 'categories',
            'query_args'  => array(
                'taxonomy'  => 'zhuige_bbs_forum_cat',
            ),
        ),

        array(
            'id'          => 'bbs_forum_rec',
            'type'        => 'select',
            'title'       => '推荐圈子',
            'placeholder' => '选择圈子',
            'chosen'      => true,
            'multiple'    => true,
            'sortable'    => true,
            'ajax'        => true,
            'options'     => 'posts',
            'query_args'  => array(
                'post_type'  => 'zhuige_bbs_forum',
            ),
        ),

        array(
            'id'          => 'bbs_forum_create_licence',
            'type'        => 'select',
            'title'       => '圈子创建协议',
            'chosen'      => true,
            // 'multiple'    => true,
            // 'sortable'    => true,
            'ajax'        => true,
            'options'     => 'pages',
            'placeholder' => '选择一个页面',
        ),
    )
));

//
// 话题页
//
$fields_subject = [
    array(
        'id'          => 'bbs_subject_rec',
        'type'        => 'select',
        'title'       => '推荐话题',
        'placeholder' => '选择话题',
        'chosen'      => true,
        'multiple'    => true,
        'sortable'    => true,
        'options'     => 'categories',
        'query_args'  => array(
            'taxonomy'  => 'zhuige_bbs_topic_tag',
        ),
    ),

    array(
        'id'          => 'bbs_subject_hot',
        'type'        => 'select',
        'title'       => '热门话题',
        'placeholder' => '选择话题',
        'chosen'      => true,
        'multiple'    => true,
        'sortable'    => true,
        'options'     => 'categories',
        'query_args'  => array(
            'taxonomy'  => 'zhuige_bbs_topic_tag',
        ),
    ),
];

if (ZhuiGe_Xcx_Addon::is_active('zhuige-subjects')) {
    $fields_subject[] = array(
        'id'          => 'home_subject_hot',
        'type'        => 'select',
        'title'       => '首页热门话题',
        'placeholder' => '选择话题',
        'chosen'      => true,
        'multiple'    => true,
        'sortable'    => true,
        'options'     => 'categories',
        'query_args'  => array(
            'taxonomy'  => 'zhuige_bbs_topic_tag',
        ),
    );

    $fields_subject[] = array(
        'id'          => 'home_subject_hot_width',
        'type'        => 'number',
        'title'       => '容器宽度',
        'class'       => 'text-class',
        'description' => '首页热门话题容器宽度，单位rpx',
    );
}

CSF::createSection($prefix, array(
    'parent' => 'bbs',
    'title' => '话题',
    'icon'  => 'fas fa-map-marker',
    'fields' => $fields_subject
));

//
// 贴子页
//
CSF::createSection($prefix, array(
    'parent' => 'bbs',
    'title' => '贴子',
    'icon'  => 'fas fa-map-marker',
    'fields' => array(

        array(
            'id'    => 'bbs_topic_mobile_switch',
            'type'  => 'switcher',
            'title' => '开启/停用',
            'subtitle' => '发帖是否要求绑定手机号',
            'default' => '0'
        ),

        array(
            'id'     => 'bbs_detail_top_img_ad',
            'type'   => 'fieldset',
            'title'  => '图文广告',
            'fields' => array(
                array(
                    'id'      => 'image',
                    'type'    => 'media',
                    'title'   => '图片',
                    'library' => 'image',
                    'after' => '<a href="https://www.zhuige.com/docs/zg/216.html" target="_blank">图片规格建议</a>',
                ),
                array(
                    'id'       => 'link',
                    'type'     => 'text',
                    'title'    => '链接',
                    'default'  => 'https://www.zhuige.com',
                    'after' => '<a href="https://www.zhuige.com/docs/zg/209.html" target="_blank">如何获取链接</a>',
                ),
                array(
                    'id'    => 'switch',
                    'type'  => 'switcher',
                    'title' => '开启/停用',
                    'default' => '1'
                ),
            ),
        ),

        array(
            'id'     => 'bbs_detail_bottom_img_ad',
            'type'   => 'fieldset',
            'title'  => '图文广告',
            'fields' => array(
                array(
                    'id'      => 'image',
                    'type'    => 'media',
                    'title'   => '图片',
                    'library' => 'image',
                    'after' => '<a href="https://www.zhuige.com/docs/zg/216.html" target="_blank">图片规格建议</a>',
                ),
                array(
                    'id'       => 'link',
                    'type'     => 'text',
                    'title'    => '链接',
                    'default'  => 'https://www.zhuige.com',
                    'after' => '<a href="https://www.zhuige.com/docs/zg/209.html" target="_blank">如何获取链接</a>',
                ),
                array(
                    'id'    => 'switch',
                    'type'  => 'switcher',
                    'title' => '开启/停用',
                    'default' => '1'
                ),
            ),
        ),

        array(
            'id'     => 'bbs_detail_poster',
            'type'   => 'fieldset',
            'title'  => '海报设置',
            'fields' => array(
                array(
                    'id'      => 'background',
                    'type'    => 'media',
                    'title'   => '背景图',
                    'library' => 'image',
                    'after' => '<a href="https://www.zhuige.com/docs/zg/216.html" target="_blank">图片规格建议</a>',
                ),

                array(
                    'id'    => 'title',
                    'type'  => 'text',
                    'title' => '显示名称',
                ),

                array(
                    'id'      => 'thumb_image',
                    'type'    => 'media',
                    'title'   => '图片默认头图',
                    'library' => 'image',
                    'after' => '<a href="https://www.zhuige.com/docs/zg/216.html" target="_blank">图片规格建议</a>',
                ),

                array(
                    'id'      => 'thumb_video',
                    'type'    => 'media',
                    'title'   => '视频默认头图',
                    'library' => 'image',
                    'after' => '<a href="https://www.zhuige.com/docs/zg/216.html" target="_blank">图片规格建议</a>',
                ),
            ),
        ),
    )
));
