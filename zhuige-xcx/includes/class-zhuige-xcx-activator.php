<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
 */

class ZhuiGe_Xcx_Activator
{
    public static function activate()
    {
        global $wpdb;

        $charset_collate = '';
        if (!empty($wpdb->charset)) {
            $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
        }

        if (!empty($wpdb->collate)) {
            $charset_collate .= " COLLATE {$wpdb->collate}";
        }

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        //粉丝表
        $table_follow_user = $wpdb->prefix . 'zhuige_xcx_follow_user';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_follow_user` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
            `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
            `follow_user_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关注用户',
            `time` int(10) UNSIGNED DEFAULT '0' COMMENT '创建时间',
             PRIMARY KEY (`id`)
        ) $charset_collate;";
        dbDelta($sql);

        //消息通知
        /**
         * like - 点赞
         * favorite - 收藏
         * comment - 评论
         * follow - 关注
         * reply - 回复
         * ait - 发帖@
         * system - 系统通知
         */
        $table_notify = $wpdb->prefix . 'zhuige_xcx_notify';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_notify` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
            `type` varchar(12) NOT NULL DEFAULT '' COMMENT '类型',
            `from_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
            `to_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
            `post_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '文章ID',
            `post_status` varchar(20) NOT NULL DEFAULT 'publish' COMMENT '文章状态',
            `isread` int(1) NOT NULL DEFAULT '0' COMMENT '是否已读',
            `time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '时间',
             PRIMARY KEY (`id`)
        ) $charset_collate;";
        dbDelta($sql);


        //收藏过的文章
        $table_post_favorite = $wpdb->prefix . 'zhuige_xcx_post_favorite';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_post_favorite` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
            `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
            `post_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '文章ID',
            `post_status` varchar(20) NOT NULL DEFAULT 'publish' COMMENT '文章状态',
            `time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
            PRIMARY KEY (`id`)
        ) $charset_collate;";
        dbDelta($sql);


        //点赞过的文章
        $table_post_like = $wpdb->prefix . 'zhuige_xcx_post_like';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_post_like` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
            `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
            `post_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '文章ID',
            `post_status` varchar(20) NOT NULL DEFAULT 'publish' COMMENT '文章状态',
            `time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '时间',
            PRIMARY KEY (`id`)
        ) $charset_collate;";
        dbDelta($sql);


        //浏览过的文章
        $table_post_view = $wpdb->prefix . 'zhuige_xcx_post_view';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_post_view` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
            `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
            `post_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '文章ID',
            `post_status` varchar(20) NOT NULL DEFAULT 'publish' COMMENT '文章状态',
            `time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '时间',
            PRIMARY KEY (`id`)
        ) $charset_collate;";
        dbDelta($sql);

        // 加载插件SQL
        foreach (ZhuiGe_Xcx_Addon::$sqls as $sql) {
            $file_path = ZHUIGE_XCX_ADDONS_DIR . $sql;
            if (file_exists($file_path)) {
                require_once($file_path);
            }
        }
    }
}
