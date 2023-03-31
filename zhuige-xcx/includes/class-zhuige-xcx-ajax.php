<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
 */

defined('ABSPATH') or die("Direct access to the script does not allowed");

class ZhuiGe_Xcx_AJAX
{
    public function __construct()
    {
        if (is_admin()) {
            // 激活追格插件
            add_action('wp_ajax_admin_active_addon', array($this, 'ajax_active_addon'));

            // 禁用追格插件
            add_action('wp_ajax_admin_deactive_addon', array($this, 'ajax_deactive_addon'));

            // 登录追格插件市场
            add_action('wp_ajax_admin_zhuige_xcx_plugins_market_login', array($this, 'ajax_zhuige_xcx_plugins_market_login'));

            // 注销
            add_action('wp_ajax_admin_zhuige_xcx_plugins_market_logout', array($this, 'ajax_zhuige_xcx_plugins_market_logout'));

            // 加载插件数据
            add_action('wp_ajax_admin_zhuige_xcx_plugins_market_list', array($this, 'ajax_zhuige_xcx_plugins_market_list'));

            // 下载-安装插件
            add_action('wp_ajax_admin_zhuige_xcx_plugins_market_install', array($this, 'ajax_zhuige_xcx_plugins_market_install'));

            // 下载-更新插件
            add_action('wp_ajax_admin_zhuige_xcx_plugins_market_update', array($this, 'ajax_zhuige_xcx_plugins_market_update'));

            // 删除插件
            add_action('wp_ajax_admin_zhuige_xcx_plugins_market_delete', array($this, 'ajax_zhuige_xcx_plugins_market_delete'));

            // 消除新版本通知
            add_action('wp_ajax_admin_zhuige_xcx_plugins_market_clear_version', array($this, 'ajax_zhuige_xcx_plugins_market_clear_version'));

            // 更新框架
            add_action('wp_ajax_admin_zhuige_xcx_update', array($this, 'ajax_zhuige_xcx_update'));

            // 追格市场
            add_action('wp_ajax_admin_zhuige_market', array($this, 'ajax_zhuige_market'));
        }
    }

    /**
     * 激活插件
     */
    public function ajax_active_addon()
    {
        $addon = isset($_POST['addon']) ? sanitize_text_field(wp_unslash($_POST['addon'])) : '';
        $code2 = isset($_POST['code2']) ? sanitize_text_field(wp_unslash($_POST['code2'])) : '';
        if (empty($code2)) {
            $code2 = get_option("zhuige_xcx_" . $addon . "_code");
        }

        $code = get_user_meta(get_current_user_id(), 'zhuige-xcx-plugins-market-code', true);

        $response = wp_remote_post("https://www.zhuige.com/api/plugins/plugin_active", array(
            'method'      => 'POST',
            'body'        => array(
                'code' => $code,
                'code2' => $code2,
                'alias' => $addon,
                'domain' => $_SERVER['HTTP_HOST']
            )
        ));

        if (is_wp_error($response) || $response['response']['code'] != 200) {
            wp_send_json_error();
        }

        $data = json_decode($response['body'], TRUE);

        if ($data['code'] != 1) {
            wp_send_json_success($data);
            die;
        }

        ZhuiGe_Xcx_Addon::active($addon);

        if (!empty($code2)) {
            update_option("zhuige_xcx_" . $addon . "_code", $code2);
        }

        wp_send_json_success($data);
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

    /**
     * 登录追格小程序插件市场
     */
    public function ajax_zhuige_xcx_plugins_market_login()
    {
        $code = isset($_POST['code']) ? sanitize_text_field(wp_unslash($_POST['code'])) : '';
        if (empty($code)) {
            wp_send_json_error();
            die();
        }

        $response = wp_remote_post("https://www.zhuige.com/api/plugins/market_login", array(
            'method'      => 'POST',
            'body'        => array(
                'code' => $code,
                'domain' => $_SERVER['HTTP_HOST']
            )
        ));

        if (is_wp_error($response) || $response['response']['code'] != 200) {
            wp_send_json_error();
        }

        $data = json_decode($response['body'], TRUE);

        if ($data['code'] == 1) {
            update_user_meta(get_current_user_id(), 'zhuige-xcx-plugins-market-code', $code);

            wp_send_json_success($data['data']);
        } else {
            update_user_meta(get_current_user_id(), 'zhuige-xcx-plugins-market-code', '');
            wp_send_json_error();
        }

        die();
    }

    /**
     * 从追格小程序插件市场注销
     */
    public function ajax_zhuige_xcx_plugins_market_logout()
    {
        update_user_meta(get_current_user_id(), 'zhuige-xcx-plugins-market-code', '');
        wp_send_json_success([]);

        die();
    }

    /**
     * ajax_zhuige_xcx_plugins_market_list
     */
    public function ajax_zhuige_xcx_plugins_market_list()
    {
        $start = isset($_POST['start']) ? sanitize_text_field(wp_unslash($_POST['start'])) : 0;
        $free = isset($_POST['free']) ? sanitize_text_field(wp_unslash($_POST['free'])) : '';

        $response = wp_remote_post("https://www.zhuige.com/api/plugins/list", array(
            'method'      => 'POST',
            'body'        => array(
                'start' => $start,
                'free' => $free,
            )
        ));

        if (is_wp_error($response) || $response['response']['code'] != 200) {
            wp_send_json_error();
        }

        $data = json_decode($response['body'], TRUE);
        $datadata = $data['data'];

        $filePath = ZHUIGE_XCX_ADDONS_DIR . 'cache_version.json';
        $version_content = file_get_contents($filePath);
        $versions = json_decode($version_content, true);

        foreach ($datadata['products'] as &$plugin) {
            $plugin['installed'] = ZhuiGe_Xcx_Addon::is_installed($plugin['alias']);

            foreach ($versions as $version) {
                if ($version['alias'] == $plugin['alias']) {
                    $plugin['new_version'] = ($version['local_version'] != $version['version'] ? 1 : 0);
                }
            }
        }

        if ($data['code'] == 1) {
            wp_send_json_success($datadata);
        } else {
            wp_send_json_error();
        }

        die();
    }

    /**
     * 下载安装插件
     */
    public function ajax_zhuige_xcx_plugins_market_install()
    {
        $alias = isset($_POST['alias']) ? sanitize_text_field(wp_unslash($_POST['alias'])) : '';
        if (empty($alias)) {
            wp_send_json_error();
        }

        $code2 = isset($_POST['code2']) ? sanitize_text_field(wp_unslash($_POST['code2'])) : '';

        $code = get_user_meta(get_current_user_id(), 'zhuige-xcx-plugins-market-code', true);
        if (empty($code)) {
            wp_send_json_error();
        }

        $response = wp_remote_post("https://www.zhuige.com/api/plugins/download_check", array(
            'method'      => 'POST',
            'body'        => array(
                'alias' => $alias,
                'code' => $code,
                'code2' => $code2,
                'domain' => $_SERVER['HTTP_HOST'],
            )
        ));

        if (is_wp_error($response) || $response['response']['code'] != 200) {
            wp_send_json_error();
        }

        $data = json_decode($response['body'], TRUE);

        if ($data['code'] == 1) {
            update_option("zhuige_xcx_" . $alias . "_code", $code2);

            $file_path = ZHUIGE_XCX_ADDONS_DIR . $alias;
            zhuige_xcx_download_file($data['data']['url'], $file_path . '.zip');


            if (class_exists('ZipArchive')) {
                $zip = new ZipArchive;
                if ($zip->open($file_path . '.zip') === TRUE) {
                    $zip->extractTo("../wp-content/plugins/zhuige-xcx/addons/$alias");
                    $zip->close();
                }
            } else {
                WP_Filesystem();
                unzip_file($file_path . '.zip',  "../wp-content/plugins/zhuige-xcx/addons/$alias");
            }


            @unlink($file_path . '.zip');
        }

        wp_send_json_success($data);
    }

    /**
     * 下载更新插件
     */
    public function ajax_zhuige_xcx_plugins_market_update()
    {
        $alias = isset($_POST['alias']) ? sanitize_text_field(wp_unslash($_POST['alias'])) : '';
        if (empty($alias)) {
            wp_send_json_error();
        }

        $code = get_user_meta(get_current_user_id(), 'zhuige-xcx-plugins-market-code', true);
        if (empty($code)) {
            wp_send_json_error();
        }

        $code2 = get_option("zhuige_xcx_" . $alias . "_code");

        $response = wp_remote_post("https://www.zhuige.com/api/plugins/download_check", array(
            'method'      => 'POST',
            'body'        => array(
                'alias' => $alias,
                'code' => $code,
                'code2' => $code2,
                'domain' => $_SERVER['HTTP_HOST'],
            )
        ));

        if (is_wp_error($response) || $response['response']['code'] != 200) {
            wp_send_json_error();
        }

        $data = json_decode($response['body'], TRUE);

        if ($data['code'] == 1) {
            $file_path = ZHUIGE_XCX_ADDONS_DIR . $alias;
            zhuige_xcx_download_file($data['data']['url'], $file_path . '.zip');

            // 删除原插件
            $is_addon_active = ZhuiGe_Xcx_Addon::is_active($alias);
            if ($is_addon_active) {
                ZhuiGe_Xcx_Addon::deactive($alias);
            }


            if (class_exists('ZipArchive')) {
                $zip = new ZipArchive;
                if ($zip->open($file_path . '.zip') === TRUE) {
                    $zip->extractTo("../wp-content/plugins/zhuige-xcx/addons/$alias");
                    $zip->close();
                }
            } else {
                WP_Filesystem();
                unzip_file($file_path . '.zip',  "../wp-content/plugins/zhuige-xcx/addons/$alias");
            }


            @unlink($file_path . '.zip');

            // 启用新插件
            if ($is_addon_active) {
                ZhuiGe_Xcx_Addon::active($alias);
            }

            // 清除提示红点
            $this->_clear_badge($alias);
        }

        wp_send_json_success($data);
    }

    /**
     * 删除插件
     */
    public function ajax_zhuige_xcx_plugins_market_delete()
    {
        $alias = isset($_POST['alias']) ? sanitize_text_field(wp_unslash($_POST['alias'])) : '';
        if (empty($alias)) {
            wp_send_json_error();
        }

        if (ZhuiGe_Xcx_Addon::is_active($alias)) {
            ZhuiGe_Xcx_Addon::deactive($alias);
        }

        zhuige_xcx_delete_dir(ZHUIGE_XCX_ADDONS_DIR . $alias . '/');

        wp_send_json_success([]);
    }

    /**
     * 更新框架
     */
    public function ajax_zhuige_xcx_update()
    {
        $code = get_user_meta(get_current_user_id(), 'zhuige-xcx-plugins-market-code', true);
        if (empty($code)) {
            wp_send_json_error();
        }

        $plugin = 'zhuige-xcx';
        $response = wp_remote_post("https://www.zhuige.com/api/plugins/update_check", array(
            'method'      => 'POST',
            'body'        => array(
                'plugin' => $plugin,
                'code' => $code,
                'domain' => $_SERVER['HTTP_HOST'],
            )
        ));

        if (is_wp_error($response) || $response['response']['code'] != 200) {
            wp_send_json_error();
        }

        $data = json_decode($response['body'], TRUE);

        if ($data['code'] == 1) {
            $file_path = WP_PLUGIN_DIR . '/' . $plugin . '.zip';
            zhuige_xcx_download_file($data['data']['url'], $file_path);


            if (class_exists('ZipArchive')) {
                $zip = new ZipArchive;
                if ($zip->open($file_path) === TRUE) {
                    $zip->extractTo(WP_PLUGIN_DIR . '/' . $plugin);
                    $zip->close();
                }
            } else {
                WP_Filesystem();
                unzip_file($file_path, WP_PLUGIN_DIR . '/' . $plugin);
            }


            @unlink($file_path);
        }

        wp_send_json_success($data);
    }

    /**
     * 追格市场
     */
    public function ajax_zhuige_market()
    {
        $action = isset($_POST["zgaction"]) ? sanitize_text_field(wp_unslash($_POST["zgaction"])) : '';

        if ($action == 'get_list') { // 查询产品
            $cat = isset($_POST["cat"]) ? (int)($_POST["cat"]) : 0;
            $params = [];
            if ($cat) {
                $params['cat'] = $cat;
            }

            $free = isset($_POST["free"]) ? sanitize_text_field($_POST["free"]) : '';
            if ($free !== '') {
                $params['free'] = $free;
            }

            $init = isset($_POST["init"]) ? (int)($_POST["init"]) : 0;
            if ($init == 1) {
                $params['init'] = $init;
            }

            $response = wp_remote_post("https://www.zhuige.com/api/market/list", array(
                'method'      => 'POST',
                'body'        => $params
            ));

            if (is_wp_error($response) || $response['response']['code'] != 200) {
                wp_send_json_error();
            }

            $data = json_decode($response['body'], TRUE);
            $datadata = $data['data'];

            if ($data['code'] == 1) {
                wp_send_json_success($datadata);
            } else {
                wp_send_json_error();
            }
        }

        die;
    }

    /**
     * ajax_zhuige_xcx_plugins_market_clear_version
     */
    public function ajax_zhuige_xcx_plugins_market_clear_version()
    {
        $addon = isset($_POST['addon']) ? sanitize_text_field(wp_unslash($_POST['addon'])) : '';
        if (empty($addon)) {
            wp_send_json_error();
            die();
        }

        // $filePath = ZHUIGE_XCX_ADDONS_DIR . 'cache_version.json';
        // $version_content = file_get_contents($filePath);
        // $versions = json_decode($version_content, true);

        // foreach ($versions as &$version) {
        //     if ($version['alias'] == $addon) {
        //         $version['local_version'] = $version['version'];
        //         break;
        //     }
        // }

        // file_put_contents($filePath, json_encode($versions));

        $this->_clear_badge($addon);

        wp_send_json_success();

        die();
    }

    private function _clear_badge($addon)
    {
        $filePath = ZHUIGE_XCX_ADDONS_DIR . 'cache_version.json';
        $version_content = file_get_contents($filePath);
        $versions = json_decode($version_content, true);

        foreach ($versions as &$version) {
            if ($version['alias'] == $addon) {
                $version['local_version'] = $version['version'];
                break;
            }
        }

        file_put_contents($filePath, json_encode($versions));
    }
}

$zhuige_xcx_ajax = new ZhuiGe_Xcx_AJAX();
