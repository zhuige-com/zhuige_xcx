<?php {
    // 文章兑换购买记录
    $table_post_cost_log = $wpdb->prefix . 'zhuige_xcx_post_cost_log';
    $cms_sql = "CREATE TABLE IF NOT EXISTS `$table_post_cost_log` (
        `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
        `trade_no` varchar(50) NOT NULL COMMENT '订单号',
        `user_id` bigint(20) NOT NULL COMMENT '用户ID',
        `post_type` varchar(20) NOT NULL COMMENT '文章类型',
        `post_id` bigint(20) UNSIGNED NOT NULL COMMENT '文章ID',
        `price` decimal(10,2) NOT NULL COMMENT '价格',
        `type` enum('money','score') DEFAULT 'money' COMMENT '类型',
        `status` enum('unpay','finish','cancel') DEFAULT 'unpay' COMMENT '状态',
        `createtime` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
        PRIMARY KEY (`id`)
    ) $charset_collate;";
    dbDelta($cms_sql);
}
