<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
 */

class ZhuiGe_Xcx_Admin
{
    public function enqueue_styles()
    {
        wp_enqueue_style(ZHUIGE_XCX, ZHUIGE_XCX_BASE_URL . 'admin/css/zhuige-xcx-admin.css', array(), ZHUIGE_XCX_VERSION, 'all');
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script('zhuige-layer', ZHUIGE_XCX_BASE_URL . "admin/js/layer/layer.js", array('jquery'), '1.0.0', true);
        wp_enqueue_script(ZHUIGE_XCX, ZHUIGE_XCX_BASE_URL . 'admin/js/zhuige-xcx-admin.js', array('jquery', 'zhuige-layer'), ZHUIGE_XCX_VERSION, false);

        if (function_exists('zhuige_xcx_widget_shortcode')) {
            wp_enqueue_script(ZHUIGE_XCX . '_edit_extend', ZHUIGE_XCX_BASE_URL . 'addons/zhuige-block/zhuige-block-edit-extend.js', array('quicktags'), ZHUIGE_XCX_VERSION, false);
        }

        if (ZhuiGe_Xcx_Addon::is_active('zhuige-wxmall')) {
            wp_enqueue_script(ZHUIGE_XCX . '_wxmall', ZHUIGE_XCX_BASE_URL . 'addons/zhuige-wxmall/mall-admin.js', array('jquery'), ZHUIGE_XCX_VERSION, false);
        }

        if (ZhuiGe_Xcx_Addon::is_active('zhuige-system_notice')) {
            wp_enqueue_script(ZHUIGE_XCX . '_system_notice', ZHUIGE_XCX_BASE_URL . 'addons/zhuige-system_notice/system-notice-admin.js', array('jquery'), ZHUIGE_XCX_VERSION, false);
        }
    }

    public function create_menu()
    {
        $prefix = ZHUIGE_XCX;

        CSF::createOptions($prefix, array(
            'framework_title'  => '追格小程序 <small>by <a href="https://www.zhuige.com" target="_blank" title="追格">www.zhuige.com</a></small>',
            'menu_title' => '追格小程序',
            'menu_slug'  => $prefix,
            'menu_position' => 2,
            'show_bar_menu' => false,
            'show_sub_menu' => false,
            'footer_credit' => 'Thank you for creating with <a href="https://www.zhuige.com/" target="_blank">追格</a>',
            'menu_icon' => 'dashicons-layout',
        ));

        $base_dir = plugin_dir_path(__FILE__);
        $base_url = plugin_dir_url(__FILE__);
        require_once $base_dir . 'partials/overview.php';
        require_once $base_dir . 'partials/global.php';

        foreach (ZhuiGe_Xcx_Addon::$admins as $admin) {
            $file_path = ZHUIGE_XCX_ADDONS_DIR . $admin;
            if (file_exists($file_path)) {
                require_once($file_path);
            }
        }

        //
        // 备份
        //
        CSF::createSection($prefix, array(
            'title'       => '备份',
            'icon'        => 'fas fa-shield-alt',
            'fields'      => array(
                array(
                    'type' => 'backup',
                ),
            )
        ));

        //分类封面
        $prefix_category = 'zhuige-xcx-category';
        CSF::createTaxonomyOptions($prefix_category, array(
            'taxonomy' => 'category',
        ));
        CSF::createSection($prefix_category, array(
            'fields' => array(
                array(
                    'id'      => 'cover',
                    'type'    => 'media',
                    'title'   => '封面',
                    'library' => 'image',
                ),
            )
        ));

        function zhuige_xcx_admin_save_after($data, $option)
        {
            $user_ids = $data['auth_black_list'];
            if (is_array($user_ids)) {
                foreach ($user_ids as $user_id) {
                    update_user_meta($user_id, 'zhuige_xcx_user_token', '');
                }
            }
        }
        add_action("csf_{$prefix}_save_after", 'zhuige_xcx_admin_save_after', 10, 2);
    }

    public function admin_init()
    {
        $this->handle_external_redirects();
    }

    public function admin_menu()
    {
        add_submenu_page('zhuige-xcx', '', '安装文档', 'manage_options', 'zhuige_xcx_plus_setup', array(&$this, 'handle_external_redirects'));
        add_submenu_page('zhuige-xcx', '', '常见问题', 'manage_options', 'zhuige_xcx_plus_qa', array(&$this, 'handle_external_redirects'));
        add_submenu_page('zhuige-xcx', '', '页面路径', 'manage_options', 'zhuige_xcx_plus_path', array(&$this, 'handle_external_redirects'));
        add_submenu_page('zhuige-xcx', '', '初装/更新必看', 'manage_options', 'zhuige_xcx_plus_new_user', array(&$this, 'handle_external_redirects'));
    }

    public function handle_external_redirects()
    {
        if (empty($_GET['page'])) {
            return;
        }

        if ('zhuige_xcx_plus_setup' === $_GET['page']) {
            wp_redirect('https://www.zhuige.com/docs/zg.html');
            die;
        }

        if ('zhuige_xcx_plus_qa' === $_GET['page']) {
            wp_redirect('https://www.zhuige.com/docs/zg/208.html');
            die;
        }

        if ('zhuige_xcx_plus_path' === $_GET['page']) {
            wp_redirect('https://www.zhuige.com/docs/zg/209.html');
            die;
        }

        if ('zhuige_xcx_plus_new_user' === $_GET['page']) {
            wp_redirect('https://www.zhuige.com/docs/zg/213.html');
            die;
        }
    }
}
