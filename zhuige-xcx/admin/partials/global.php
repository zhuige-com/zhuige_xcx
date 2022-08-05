<?php

/*
 * 追格小程序
 * Author: 追格
 * Help document: https://www.zhuige.com
 * Copyright © 2022 www.zhuige.com All rights reserved.
 */

//
// 全局
//
CSF::createSection($prefix, array(
    'id'    => 'global',
    'title' => '全局',
    'icon'  => 'fas fa-plus-circle',
));

//
// 基础设置
//
CSF::createSection($prefix, array(
    'parent' => 'global',
    'title' => '基础设置',
    'icon'  => 'fas fa-map-marker',
    'fields' => array(

        array(
            'id'          => 'basic_title',
            'type'        => 'text',
            'title'       => '标题',
            'placeholder' => '标题'
        ),

        array(
            'id'          => 'basic_desc',
            'type'        => 'text',
            'title'       => '描述',
            'placeholder' => '描述'
        ),

        array(
            'id'      => 'basic_logo',
            'type'    => 'media',
            'title'   => 'LOGO设置',
            'library' => 'image',
            'after' => '<a href="https://www.zhuige.com/docs/zg/216.html" target="_blank">图片规格建议</a>',
        ),

        array(
            'id'     => 'basic_wechat',
            'type'   => 'fieldset',
            'title'  => '微信小程序',
            'fields' => array(
                array(
                    'id'    => 'appid',
                    'type'  => 'text',
                    'title' => 'AppID',
                ),
                array(
                    'id'    => 'secret',
                    'type'  => 'text',
                    'title' => 'AppSecret',
                ),
            ),
        ),

        array(
            'id'    => 'user_email_suffix',
            'type'  => 'text',
            'title' => '用户邮箱',
            'subtitle' => '新用户邮箱后缀',
            'default' => 'zhuige.com'
        ),

        array(
            'id'    => 'hot_search',
            'type'  => 'text',
            'title' => '热门搜索',
            'subtitle' => '英文逗号分隔',
        ),
    )
));

//
// 首页-推荐设置
//
CSF::createSection($prefix, array(
    'parent' => 'global',
    'title' => '首页-近期',
    'icon'  => 'fas fa-map-marker',
    'fields' => array(

        array(
            'id'     => 'rec_slide',
            'type'   => 'group',
            'title'  => '幻灯片',
            'fields' => array(
                array(
                    'id'          => 'title',
                    'type'        => 'text',
                    'title'       => '标题',
                    'placeholder' => '标题'
                ),
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
            'id'     => 'rec_nav',
            'type'   => 'group',
            'title'  => '导航项',
            'fields' => array(
                array(
                    'id'          => 'title',
                    'type'        => 'text',
                    'title'       => '标题',
                    'placeholder' => '标题'
                ),
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
            'id'     => 'home_rec_user',
            'type'   => 'fieldset',
            'title'  => '热门用户',
            'fields' => array(

                array(
                    'id'          => 'title',
                    'type'        => 'text',
                    'title'       => '标题',
                    'placeholder' => '标题'
                ),

                array(
                    'id'          => 'users',
                    'type'        => 'select',
                    'title'       => '推荐用户',
                    'placeholder' => '请选择用户',
                    'options'     => 'users',
                    'chosen'      => true,
                    'multiple'    => true,
                    'sortable'    => true,
                    'ajax'        => true,
                ),

                array(
                    'id'      => 'position',
                    'type'    => 'number',
                    'title'   => '位置',
                    'subtitle' => '显示在第几个帖子前',
                    'default' => 1,
                ),

                array(
                    'id'    => 'switch',
                    'type'  => 'switcher',
                    'title' => '开启/停用',
                    'default' => '1'
                ),
            )
        ),

        array(
            'id'     => 'home_rec_forum',
            'type'   => 'fieldset',
            'title'  => '热门圈子',
            'fields' => array(

                array(
                    'id'          => 'title',
                    'type'        => 'text',
                    'title'       => '标题',
                    'placeholder' => '标题'
                ),

                array(
                    'id'          => 'forums',
                    'type'        => 'select',
                    'title'       => '推荐圈子',
                    'placeholder' => '选择圈子',
                    'chosen'      => true,
                    'multiple'    => true,
                    'sortable'    => true,
                    'options'     => 'posts',
                    'query_args'  => array(
                        'post_type'  => 'zhuige_bbs_forum',
                    ),
                ),

                array(
                    'id'    => 'switch',
                    'type'  => 'switcher',
                    'title' => '开启/停用',
                    'default' => '1'
                ),
            )
        ),

        array(
            'id'    => 'rec_home_comment',
            'type'  => 'switcher',
            'title' => '开启/停用',
            'label' => '是否在列表显示评论',
            'default' => '1'
        ),

        array(
            'id'      => 'rec_home_thumb',
            'type'    => 'media',
            'title'   => '首页分享缩缩图',
            'library' => 'image',
            'after' => '<a href="https://www.zhuige.com/docs/zg/216.html" target="_blank">图片规格建议</a>',
        ),
    )
));

//
// 发布页面
//
CSF::createSection($prefix, array(
    'parent' => 'global',
    'title' => '发布页面',
    'icon'  => 'fas fa-map-marker',
    'fields' => array(

        array(
            'id'     => 'create_items',
            'type'   => 'group',
            'title'  => '导航项',
            'fields' => array(
                array(
                    'id'          => 'title',
                    'type'        => 'text',
                    'title'       => '标题',
                    'placeholder' => '标题'
                ),
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

    )
));

//
// 我的页面
//
CSF::createSection($prefix, array(
    'parent' => 'global',
    'title' => '我的页面',
    'icon'  => 'fas fa-map-marker',
    'fields' => array(
        array(
            'id'      => 'my_bg',
            'type'    => 'media',
            'title'   => '顶部背景图',
            'library' => 'image',
            'after' => '<a href="https://www.zhuige.com/docs/zg/216.html" target="_blank">图片规格建议</a>',
        ),

        array(
            'id'     => 'my_slide',
            'type'   => 'group',
            'title'  => '幻灯片',
            'fields' => array(
                array(
                    'id'       => 'link',
                    'type'     => 'text',
                    'title'    => '链接',
                    'default'  => 'https://www.zhuige.com',
                    'after' => '<a href="https://www.zhuige.com/docs/zg/209.html" target="_blank">如何获取链接</a>',
                ),
                array(
                    'id'      => 'image',
                    'type'    => 'media',
                    'title'   => '图片',
                    'library' => 'image',
                    'after' => '<a href="https://www.zhuige.com/docs/zg/216.html" target="_blank">图片规格建议</a>',
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
            'id'       => 'my_menu',
            'type'     => 'group',
            'title'    => '我的菜单',
            'subtitle' => '我的页面的菜单',
            'fields'   => array(
                array(
                    'id'    => 'title',
                    'type'  => 'text',
                    'title' => '标题',
                ),
                array(
                    'id'     => 'items',
                    'type'   => 'group',
                    'title'  => '菜单项',
                    'fields' => array(
                        array(
                            'id'    => 'title',
                            'type'  => 'text',
                            'title' => '标题',
                        ),
                        array(
                            'id'          => 'type',
                            'type'        => 'select',
                            'title'       => '类型',
                            'placeholder' => '选择类型',
                            'options'     => array(
                                'clear'     => '清除缓存',
                                'contact'   => '在线客服（微信）',
                                'link'      => '自定义链接',
                            ),
                        ),
                        array(
                            'id'      => 'icon',
                            'type'    => 'media',
                            'title'   => '图标',
                            'library' => 'image',
                            'after' => '<a href="https://www.zhuige.com/docs/zg/216.html" target="_blank">图片规格建议</a>',
                        ),
                        array(
                            'id'    => 'link',
                            'type'  => 'text',
                            'title' => '链接',
                            'dependency' => array('type', '==', 'link'),
                            'after' => '<a href="https://www.zhuige.com/docs/zg/209.html" target="_blank">如何获取链接</a>',
                        ),
                        array(
                            'id'    => 'switch',
                            'type'  => 'switcher',
                            'title' => '开启/停用',
                            'default' => '1'
                        ),
                    )
                ),
            ),
            'default' => array(
                array(
                    'title' => '官方服务',
                    'items' => array(
                        array('title' => '用户认证', 'type' => 'link', 'link' => '/pages/base/about/about', 'switch' => '1'),
                        array('title' => '消息订阅', 'type' => 'subscribe', 'switch' => '1'),
                        array('title' => '我的发布', 'type' => 'link', 'link' => '/pages/base/about/about', 'switch' => '1'),
                        array('title' => '我的消息', 'type' => 'link', 'link' => '/pages/base/about/about', 'switch' => '1'),
                    ),
                ),

                array(
                    'title' => '关于我们',
                    'items' => array(
                        array('title' => '免责声明', 'type' => 'link', 'link' => '/pages/base/about/about', 'switch' => '1'),
                        array('title' => '订单管理', 'type' => 'link', 'link' => '/pages/base/about/about', 'switch' => '1'),
                        array('title' => '清除缓存', 'type' => 'clear', 'switch' => '1'),
                        array('title' => '关于追格', 'type' => 'link', 'link' => '/pages/base/about/about', 'switch' => '1'),
                    ),
                ),
            )
        ),

        array(
            'id'     => 'copyright',
            'type'   => 'fieldset',
            'title'  => '版权信息',
            'fields' => array(
                array(
                    'id'      => 'logo',
                    'type'    => 'media',
                    'title'   => 'LOGO',
                    'library' => 'image',
                    'after' => '<a href="https://www.zhuige.com/docs/zg/216.html" target="_blank">图片规格建议</a>',
                ),

                array(
                    'id'    => 'switch',
                    'type'  => 'switcher',
                    'title' => '开启/停用',
                    'default' => '1'
                ),
            ),
        ),
    )
));

//
// 登录页
//
CSF::createSection($prefix, array(
    'parent' => 'global',
    'title' => '登录页面',
    'icon'  => 'fas fa-map-marker',
    'fields' => array(
        array(
            'id'      => 'login_bg',
            'type'    => 'media',
            'title'   => '顶部背景图',
            'library' => 'image',
            'after' => '<a href="https://www.zhuige.com/docs/zg/216.html" target="_blank">图片规格建议</a>',
        ),

        array(
            'id'      => 'login_logo',
            'type'    => 'media',
            'title'   => '登录页LOGO',
            'library' => 'image',
            'after' => '<a href="https://www.zhuige.com/docs/zg/216.html" target="_blank">图片规格建议</a>',
        ),

        array(
            'id'    => 'login_title',
            'type'  => 'text',
            'title' => '标题',
        ),

        array(
            'id'          => 'login_yhxy',
            'type'        => 'select',
            'title'       => '用户协议',
            'chosen'      => true,
            // 'multiple'    => true,
            // 'sortable'    => true,
            // 'ajax'        => true,
            'options'     => 'pages',
            'placeholder' => '选择一个页面',
        ),

        array(
            'id'          => 'login_yszc',
            'type'        => 'select',
            'title'       => '隐私政策',
            'chosen'      => true,
            // 'multiple'    => true,
            // 'sortable'    => true,
            // 'ajax'        => true,
            'options'     => 'pages',
            'placeholder' => '选择一个页面',
        ),

    )
));

//
// 关于页
//
CSF::createSection($prefix, array(
    'parent' => 'global',
    'title' => '关于页面',
    'icon'  => 'fas fa-map-marker',
    'fields' => array(

        array(
            'id'          => 'about_pages',
            'type'        => 'select',
            'title'       => '顶部导航',
            'chosen'      => true,
            'multiple'    => true,
            'sortable'    => true,
            'ajax'        => true,
            'options'     => 'pages',
            'placeholder' => '请选择页面',
        ),

    )
));

//
// 评论设置
//
CSF::createSection($prefix, array(
    'parent' => 'global',
    'title' => '评论设置',
    'icon'  => 'fas fa-map-marker',
    'fields' => array(

        array(
            'id'    => 'comment_switch',
            'type'  => 'switcher',
            'title' => '开启/停用',
            'subtitle' => '是否打开评论功能',
            'default' => '0'
        ),

        array(
            'id'    => 'comment_mobile_switch',
            'type'  => 'switcher',
            'title' => '开启/停用',
            'subtitle' => '评论是否要求绑定手机号',
            'default' => '0'
        ),

    )
));
