<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
 */

function zhuige_xcx_bbs_create_custom_post_type()
{
    /**
     * 圈子
     */
    $zhuige_bbs_forum_labels = array(
        'name'               => '追格圈子',
        'singular_name'      => '追格圈子', 'post type 单个 item 时的名称，因为英文有复数',
        'add_new'            => '新建圈子', '添加新内容的链接名称',
        'add_new_item'       => '新建一个圈子',
        'edit_item'          => '编辑圈子',
        'new_item'           => '新圈子',
        'all_items'          => '所有圈子',
        'view_item'          => '查看圈子',
        'search_items'       => '搜索圈子',
        'not_found'          => '没有找到有关圈子',
        'not_found_in_trash' => '回收站里面没有相关圈子',
        'parent_item_colon'  => '',
        'menu_name'          => '追格圈子'
    );
    $zhuige_bbs_forum_args = array(
        'labels'        => $zhuige_bbs_forum_labels,
        'description'   => '我们网站的圈子信息',
        'public'        => true,
        // 'show_in_menu'  => 'custompage',
        // 'menu_position' => 5,
        'supports'      => array('title', 'editor', 'author'),
        'has_archive'   => true
    );
    register_post_type('zhuige_bbs_forum', $zhuige_bbs_forum_args);

    //追格圈子-圈子属性
    $zhuige_bbs_forum_option = 'zhuige-bbs-forum-option';
    CSF::createMetabox($zhuige_bbs_forum_option, array(
        'title'        => '追格圈子-圈子设置',
        'post_type'    => 'zhuige_bbs_forum',
        // 'show_restore' => true,
    ));

    $forum_options = [
        array(
            'id'      => 'logo',
            'type'    => 'media',
            'title'   => 'LOGO',
            'library' => 'image',
        ),

        array(
            'id'      => 'background',
            'type'    => 'media',
            'title'   => '头部背景',
            'library' => 'image',
        ),

        array(
            'id'    => 'notice',
            'type'  => 'textarea',
            'title' => '公告',
            'subtitle' => '',
        ),

        array(
            'id'     => 'ad_link',
            'type'   => 'fieldset',
            'title'  => '推荐',
            'fields' => array(

                array(
                    'id'      => 'title',
                    'type'    => 'text',
                    'title'   => '文案',
                ),

                array(
                    'id'    => 'link',
                    'type'  => 'text',
                    'title' => '链接',
                    'default' => 'https://www.zhuige.com'
                ),

            ),
        ),

        array(
            'id'     => 'location',
            'type'   => 'fieldset',
            'title'  => '位置',
            'fields' => array(
                array(
                    'id'    => 'marker',
                    'type'  => 'text',
                    'title' => '标志物',
                ),
                array(
                    'id'    => 'address',
                    'type'  => 'text',
                    'title' => '地址',
                ),
                array(
                    'id'    => 'longitude',
                    'type'  => 'text',
                    'title' => '经度',
                ),
                array(
                    'id'    => 'latitude',
                    'type'  => 'text',
                    'title' => '纬度',
                ),
            ),
        ),

        array(
            'id'     => 'ad_menu',
            'type'   => 'group',
            'title'  => '底部菜单',
            'fields' => array(

                array(
                    'id'    => 'title',
                    'type'  => 'text',
                    'title' => '标题',
                ),

                array(
                    'id'    => 'link',
                    'type'  => 'text',
                    'title' => '链接',
                    'default' => 'https://www.zhuige.com'
                ),

                array(
                    'id'    => 'switch',
                    'type'  => 'switcher',
                    'title' => '启用',
                    'default' => ''
                ),

            ),
        ),
    ];

    if (ZhuiGe_Xcx_Addon::is_active('zhuige-ads')) {
        $forum_options[] = array(
            'id'       => 'ad_imgs',
            'type'     => 'fieldset',
            'title'    => '图片广告',
            'fields'   => array(
                array(
                    'id'          => 'title',
                    'type'        => 'text',
                    'title'       => '标题',
                    'placeholder' => '标题'
                ),
                array(
                    'id'     => 'items',
                    'type'   => 'group',
                    'title'  => '菜单项',
                    'fields' => array(
                        array(
                            'id'          => 'title',
                            'type'        => 'text',
                            'title'       => '标题',
                            'placeholder' => '标题'
                        ),
                        array(
                            'id'          => 'badge',
                            'type'        => 'text',
                            'title'       => '角标',
                            'placeholder' => '角标'
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
                            'id'          => 'price',
                            'type'        => 'text',
                            'title'       => '价格',
                            'placeholder' => '价格'
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
                    'id'    => 'switch',
                    'type'  => 'switcher',
                    'title' => '开启/停用',
                    'default' => '1'
                ),
            )
        );
    }

    CSF::createSection($zhuige_bbs_forum_option, array(
        'fields' => $forum_options
    ));


    /**
     * 圈子分类
     */
    $zhuige_bbs_forum_cat_labels = array(
        'name'              => '分类', 'taxonomy 名称',
        'singular_name'     => '分类', 'taxonomy 单数名称',
        'search_items'      => '搜索分类',
        'all_items'         => '所有分类',
        'parent_item'       => '该分类的上级分类',
        'parent_item_colon' => '该分类的上级分类：',
        'edit_item'         => '编辑分类',
        'update_item'       => '更新分类',
        'add_new_item'      => '添加新的分类',
        'new_item_name'     => '分类',
        'menu_name'         => '分类',
    );
    $zhuige_bbs_forum_cat_args = array(
        'hierarchical' => true,
        'labels' => $zhuige_bbs_forum_cat_labels,
        // 'show_ui'           => true,
        // 'show_admin_column' => true,
        // 'query_var'         => true,
        // 'rewrite'           => array( 'slug' => 'zhuige_bbs_forum_cat' ),
    );
    register_taxonomy('zhuige_bbs_forum_cat', 'zhuige_bbs_forum', $zhuige_bbs_forum_cat_args);


    /**
     * 圈子帖子
     */
    $zhuige_bbs_topic_labels = array(
        'name'               => '追格圈子帖子',
        'singular_name'      => '追格圈子帖子', 'post type 单个 item 时的名称，因为英文有复数',
        'add_new'            => '新建圈子帖子', '添加新内容的链接名称',
        'add_new_item'       => '新建一个圈子帖子',
        'edit_item'          => '编辑圈子帖子',
        'new_item'           => '新圈子帖子',
        'all_items'          => '所有圈子帖子',
        'view_item'          => '查看圈子帖子',
        'search_items'       => '搜索圈子帖子',
        'not_found'          => '没有找到有关圈子帖子',
        'not_found_in_trash' => '回收站里面没有相关圈子帖子',
        'parent_item_colon'  => '',
        'menu_name'          => '追格圈子帖子'
    );
    $zhuige_bbs_topic_args = array(
        'labels'        => $zhuige_bbs_topic_labels,
        'description'   => '我们网站的圈子帖子信息',
        'public'        => true,
        // 'show_in_menu'  => 'custompage',
        // 'menu_position' => 5,
        'supports'      => array('title', 'editor', 'thumbnail', 'excerpt', 'comments', 'author'),
        'has_archive'   => true
    );
    register_post_type('zhuige_bbs_topic', $zhuige_bbs_topic_args);

    $zhuige_bbs_topic_option_u = 'zhuige-bbs-topic-option-u';
    CSF::createMetabox($zhuige_bbs_topic_option_u, array(
        'title'        => '追格圈子帖子设置',
        'post_type'    => 'zhuige_bbs_topic',
        'data_type'    => 'unserialize',
        // 'show_restore' => true,
    ));

    CSF::createSection($zhuige_bbs_topic_option_u, array(
        'fields' => array(

            array(
                'id'          => 'zhuige_bbs_forum_id',
                'type'        => 'select',
                'title'       => '圈子',
                'placeholder' => '选择圈子',
                'chosen'      => true,
                // 'multiple'    => true,
                // 'sortable'    => true,
                'ajax'        => true,
                'options'     => 'posts',
                'query_args'  => array(
                    'post_type'  => 'zhuige_bbs_forum',
                ),
            ),

        )
    ));

    //追格圈子-帖子属性
    $zhuige_bbs_topic_option = 'zhuige-bbs-topic-option';
    CSF::createMetabox($zhuige_bbs_topic_option, array(
        'title'        => '追格圈子帖子设置',
        'post_type'    => 'zhuige_bbs_topic',
        // 'show_restore' => true,
    ));

    CSF::createSection($zhuige_bbs_topic_option, array(
        'fields' => array(
            array(
                'id'          => 'type',
                'type'        => 'select',
                'title'       => '类型',
                'placeholder' => '选择类型',
                'options'     => array(
                    'image'    => '图片',
                    'video'    => '视频',
                ),
            ),

            array(
                'id'     => 'images',
                'type'   => 'group',
                'title'  => '图片',
                'fields' => array(

                    array(
                        'id'      => 'image',
                        'type'    => 'media',
                        'title'   => '图片',
                        'library' => 'image',
                    ),

                ),
                'dependency' => array('type', '==', 'image'),
            ),

            array(
                'id'          => 'video',
                'type'        => 'media',
                'title'       => '视频',
                'placeholder' => '视频',
                'dependency' => array('type', '==', 'video'),
            ),

            array(
                'id'      => 'video_cover',
                'type'    => 'media',
                'title'   => '视频封面',
                'library' => 'image',
                'dependency' => array('type', '==', 'video'),
            ),

            array(
                'id'     => 'location',
                'type'   => 'fieldset',
                'title'  => '位置',
                'fields' => array(
                    array(
                        'id'    => 'marker',
                        'type'  => 'text',
                        'title' => '标志物',
                    ),
                    array(
                        'id'    => 'address',
                        'type'  => 'text',
                        'title' => '地址',
                    ),
                    array(
                        'id'    => 'longitude',
                        'type'  => 'text',
                        'title' => '经度',
                    ),
                    array(
                        'id'    => 'latitude',
                        'type'  => 'text',
                        'title' => '纬度',
                    ),
                ),
            ),
        )
    ));

    /**
     * 帖子话题
     */
    $zhuige_bbs_topic_tag_labels = array(
        'name'              => '话题', 'taxonomy 名称',
        'singular_name'     => '话题', 'taxonomy 单数名称',
        'search_items'      => '搜索话题',
        'all_items'         => '所有话题',
        'parent_item'       => '该话题的上级话题',
        'parent_item_colon' => '该话题的上级话题：',
        'edit_item'         => '编辑话题',
        'update_item'       => '更新话题',
        'add_new_item'      => '添加新的话题',
        'new_item_name'     => '话题',
        'menu_name'         => '话题',
        'separate_items_with_commas' => '多个话题请用英文逗号（,）分开',
        'choose_from_most_used' => '从常用话题中选择',
        'not_found' => '未找到话题'
    );
    $zhuige_bbs_topic_tag_args = array(
        'hierarchical' => false,
        'labels' => $zhuige_bbs_topic_tag_labels,
        // 'show_ui'           => true,
        // 'show_admin_column' => true,
        // 'query_var'         => true,
        // 'rewrite'           => array( 'slug' => 'zhuige_bbs_topic_tag' ),
    );
    register_taxonomy('zhuige_bbs_topic_tag', 'zhuige_bbs_topic', $zhuige_bbs_topic_tag_args);

    //分类封面
    $topic_tag_options = 'zhuige_bbs_topic_tag_options';
    CSF::createTaxonomyOptions($topic_tag_options, array(
        'taxonomy' => 'zhuige_bbs_topic_tag',
    ));
    CSF::createSection($topic_tag_options, array(
        'fields' => array(
            array(
                'id'      => 'logo',
                'type'    => 'media',
                'title'   => 'LOGO',
                'library' => 'image',
            ),

            array(
                'id'    => 'badge',
                'type'  => 'text',
                'title' => '角标',
            ),

            array(
                'id'      => 'cover',
                'type'    => 'media',
                'title'   => '封面',
                'library' => 'image',
            ),

            array(
                'id'    => 'switch',
                'type'  => 'switcher',
                'title' => '启用',
                'title' => '是否在话题聚合中显示',
                'default' => ''
            ),
        )
    ));
}

// ZhuiGe_Xcx::$post_types[] = ['id' => 'zhuige_bbs_forum', 'name' => '圈子', 'link' => '/pages/bbs/forum/forum'];
ZhuiGe_Xcx::$post_types[] = ['id' => 'zhuige_bbs_topic', 'name' => '帖子', 'link' => '/pages/bbs/detail/detail'];
add_action('init', 'zhuige_xcx_bbs_create_custom_post_type');
