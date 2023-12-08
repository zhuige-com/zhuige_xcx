<?php {
     // 发帖提醒
    $table_at_users_notify = $wpdb->prefix . 'zhuige_xcx_at_users_notify';
    $at_users_notify_sql = "CREATE TABLE IF NOT EXISTS `$table_at_users_notify` (
        `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
        `from_id` bigint(20) NOT NULL COMMENT '发帖人',
        `to_id` bigint(20) NOT NULL COMMENT '提醒的人',
        `topic_id` BIGINT(20) NOT NULL COMMENT '帖子ID',
        `isread` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否已读:0=未读,1=已读',
        `createtime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
        PRIMARY KEY (`id`)
    ) $charset_collate;";
    dbDelta($at_users_notify_sql);
}
