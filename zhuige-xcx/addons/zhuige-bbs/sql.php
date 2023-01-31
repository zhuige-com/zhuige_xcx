<?php {
    //版块关注表
    $table_zhuige_bbs_forum_users = $wpdb->prefix . 'zhuige_bbs_forum_users';
    $bbs_sql = "CREATE TABLE IF NOT EXISTS `$table_zhuige_bbs_forum_users` (
        `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
        `forum_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '版块',
        `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
        `role` varchar(10) NOT NULL DEFAULT 'normal' COMMENT '角色',
        `time` int(10) UNSIGNED DEFAULT '0' COMMENT '创建时间',
        PRIMARY KEY (`id`)
    ) $charset_collate;";
    dbDelta($bbs_sql);
}
