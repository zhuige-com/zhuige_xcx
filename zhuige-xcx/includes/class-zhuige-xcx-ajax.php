<?php

/*
 * 追格小程序
 * Author: 追格
 * Help document: https://www.zhuige.com
 * Copyright © 2022 www.zhuige.com All rights reserved.
 */

defined('ABSPATH') or die("Direct access to the script does not allowed");

class ZhuiGe_Xcx_AJAX
{
    public function __construct()
    {
        if (is_admin()) {
            //激活追格插件
            add_action('wp_ajax_admin_active_addon', array($this, 'ajax_active_addon'));

            //禁用追格插件
            add_action('wp_ajax_admin_deactive_addon', array($this, 'ajax_deactive_addon'));
        }
    }

    /**
     * 激活插件
     */
    public function ajax_active_addon()
    {
        $addon = isset($_POST['addon']) ? sanitize_text_field(wp_unslash($_POST['addon'])) : '';
        ZhuiGe_Xcx_Addon::active($addon);

        wp_send_json_success();
        die();
    }

    /**
     * 禁用插件
     */
    public function ajax_deactive_addon()
    {
        $addon = isset($_POST['addon']) ? sanitize_text_field(wp_unslash($_POST['addon'])) : '';
        ZhuiGe_Xcx_Addon::deactive($addon);

        wp_send_json_success();
        die();
    }
}

$zhuige_xcx_ajax = new ZhuiGe_Xcx_AJAX();
