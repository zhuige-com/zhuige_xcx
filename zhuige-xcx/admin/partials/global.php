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
// 全局
//
CSF::createSection($prefix, array(
    'id'    => 'global',
    'title' => '全局',
    'icon'  => 'fas fa-plus-circle',
));


/**
 * 已注册的文章种类
 */
$zhuige_xcx_post_types = [];
foreach (ZhuiGe_Xcx::$post_types as $post_type) {
    $zhuige_xcx_post_types[$post_type['id']] = $post_type['name'];
}

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
            'id'     => 'basic_wxpay',
            'type'   => 'fieldset',
            'title'  => '微信支付',
            'fields' => array(
                array(
                    'id'          => 'mchid',
                    'type'        => 'text',
                    'title'       => '商户号',
                    'placeholder' => '商户号'
                ),
                array(
                    'id'          => 'key',
                    'type'        => 'text',
                    'title'       => '支付密钥',
                    'placeholder' => '支付密钥'
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

        array(
            'id'    => 'zhuige_switch_oss',
            'type'  => 'switcher',
            'title' => '小程序码导入媒体库',
            'subtitle' => '使用七牛/阿里/腾讯OSS的需要开启',
            'default' => ''
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

        // 热门推荐
        array(
            'id'     => 'home_rec_post',
            'type'   => 'group',
            'title'  => '推荐文章',
            'fields' => array(

                array(
                    'id'          => 'title',
                    'type'        => 'text',
                    'title'       => '标题',
                    'placeholder' => '标题'
                ),

                array(
                    'id'          => 'more_link',
                    'type'        => 'text',
                    'title'       => '更多链接',
                    'placeholder' => '链接'
                ),

                array(
                    'id'          => 'post_type',
                    'type'        => 'select',
                    'title'       => '文章类型',
                    'options'     => array_merge(['zhuige_bbs_forum' => '圈子'], $zhuige_xcx_post_types),
                    'default' => 'post'
                ),

                array(
                    'id'          => 'subtitle',
                    'type'        => 'text',
                    'title'       => '副标题',
                    'placeholder' => '副标题',
                    'dependency' => array('post_type', '==', 'zhuige_activity'),
                ),

                array(
                    'id'      => 'banner',
                    'type'    => 'media',
                    'title'   => 'Banner',
                    'library' => 'image',
                    'dependency' => array('post_type', '==', 'zhuige_activity'),
                ),

                array(
                    'id'          => 'column_ids',
                    'type'        => 'select',
                    'title'       => '推荐课程',
                    'placeholder' => '选择课程',
                    'chosen'      => true,
                    'multiple'    => true,
                    'sortable'    => true,
                    'ajax'        => true,
                    'options'     => 'posts',
                    'query_args'  => array(
                        'post_type'  => 'zhuige_column',
                    ),
                    'dependency' => array('post_type', '==', 'zhuige_column'),
                ),

                array(
                    'id'          => 'activity_ids',
                    'type'        => 'select',
                    'title'       => '推荐活动',
                    'placeholder' => '选择活动',
                    'chosen'      => true,
                    'multiple'    => true,
                    'sortable'    => true,
                    'ajax'        => true,
                    'options'     => 'posts',
                    'query_args'  => array(
                        'post_type'  => 'zhuige_activity',
                    ),
                    'dependency' => array('post_type', '==', 'zhuige_activity'),
                ),

                array(
                    'id'          => 'bbs_topic_ids',
                    'type'        => 'select',
                    'title'       => '推荐帖子',
                    'placeholder' => '选择帖子',
                    'chosen'      => true,
                    'multiple'    => true,
                    'sortable'    => true,
                    'ajax'        => true,
                    'options'     => 'posts',
                    'query_args'  => array(
                        'post_type'  => 'zhuige_bbs_topic',
                    ),
                    'dependency' => array('post_type', '==', 'zhuige_bbs_topic'),
                ),

                array(
                    'id'          => 'forum_ids',
                    'type'        => 'select',
                    'title'       => '推荐圈子',
                    'placeholder' => '选择圈子',
                    'chosen'      => true,
                    'multiple'    => true,
                    'sortable'    => true,
                    'ajax'        => true,
                    'ajax'        => true,
                    'options'     => 'posts',
                    'query_args'  => array(
                        'post_type'  => 'zhuige_bbs_forum',
                    ),
                    'dependency' => array('post_type', '==', 'zhuige_bbs_forum'),
                ),

                array(
                    'id'          => 'resource_ids',
                    'type'        => 'select',
                    'title'       => '推荐知识库',
                    'placeholder' => '选择知识库',
                    'chosen'      => true,
                    'multiple'    => true,
                    'sortable'    => true,
                    'ajax'        => true,
                    'options'     => 'posts',
                    'query_args'  => array(
                        'post_type'  => 'zhuige_res',
                    ),
                    'dependency' => array('post_type', '==', 'zhuige_res'),
                ),

                array(
                    'id'          => 'goods_ids',
                    'type'        => 'select',
                    'title'       => '推荐积分商品',
                    'placeholder' => '选择积分商品',
                    'chosen'      => true,
                    'multiple'    => true,
                    'sortable'    => true,
                    'ajax'        => true,
                    'options'     => 'posts',
                    'query_args'  => array(
                        'post_type'  => 'zhuige_goods',
                    ),
                    'dependency' => array('post_type', '==', 'zhuige_goods'),
                ),

                array(
                    'id'          => 'post_ids',
                    'type'        => 'select',
                    'title'       => '推荐文章',
                    'placeholder' => '选择文章',
                    'chosen'      => true,
                    'multiple'    => true,
                    'sortable'    => true,
                    'ajax'        => true,
                    'options'     => 'posts',
                    'dependency' => array('post_type', '==', 'post'),
                ),

                array(
                    'id'          => 'page_ids',
                    'type'        => 'select',
                    'title'       => '推荐页面',
                    'placeholder' => '选择页面',
                    'chosen'      => true,
                    'multiple'    => true,
                    'sortable'    => true,
                    'ajax'        => true,
                    'ajax'        => true,
                    'options'     => 'pages',
                    'dependency' => array('post_type', '==', 'page'),
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
            'id'    => 'rec_list_tab_switch',
            'type'  => 'switcher',
            'title' => '是否显示列表上tab',
            'default' => '1'
        ),

        array(
            'id'          => 'rec_list_tab_limit',
            'type'        => 'select',
            'title'       => '列表上tab项目',
            'placeholder' => '列表上tab项目',
            'chosen'      => true,
            'multiple'    => true,
            'sortable'    => true,
            'options'     => $zhuige_xcx_post_types,
        ),

        array(
            'id'    => 'rec_list_switch',
            'type'  => 'switcher',
            'title' => '是否显示列表',
            'default' => '1'
        ),

        array(
            'id'          => 'rec_list_limit',
            'type'        => 'select',
            'title'       => '列表中文章种类',
            'placeholder' => '列表中文章种类',
            'chosen'      => true,
            'multiple'    => true,
            // 'sortable'    => true,
            'options'     => $zhuige_xcx_post_types,
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
            'ajax'        => true,
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
            'ajax'        => true,
            'options'     => 'pages',
            'placeholder' => '选择一个页面',
        ),

    )
));

//
// 搜索页
//
CSF::createSection($prefix, array(
    'parent' => 'global',
    'title' => '搜索页面',
    'icon'  => 'fas fa-map-marker',
    'fields' => array(

        array(
            'id'          => 'search_list_limit',
            'type'        => 'select',
            'title'       => '列表中文章种类',
            // 'placeholder' => '选择阅读限制种类',
            'chosen'      => true,
            'multiple'    => true,
            // 'sortable'    => true,
            'options'     => $zhuige_xcx_post_types,
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
