<?php

/**
 * Plugin Name:       追格小程序
 * Plugin URI:        https://www.zhuige.com/
 * Description:       追格小程序是一个积木式小程序搭建框架。
 * Version:           1.7.0
 * Author:            追格
 * Author URI:        https://www.zhuige.com/
 * Text Domain:       zhuige-xcx
 */

if (!defined('WPINC')) {
	die;
}

define('ZHUIGE_XCX', 'zhuige-xcx');
define('ZHUIGE_XCX_VERSION', '1.7.0');
define('ZHUIGE_XCX_BASE_NAME', plugin_basename(__FILE__));
define('ZHUIGE_XCX_BASE_DIR', plugin_dir_path(__FILE__));
define('ZHUIGE_XCX_BASE_URL', plugin_dir_url(__FILE__));
define('ZHUIGE_XCX_ADDONS_DIR', ZHUIGE_XCX_BASE_DIR . 'addons/');

function activate_zhuige_xcx()
{
	require_once ZHUIGE_XCX_BASE_DIR . 'includes/class-zhuige-xcx-activator.php';
	ZhuiGe_Xcx_Activator::activate();
}

function deactivate_zhuige_xcx()
{
	require_once ZHUIGE_XCX_BASE_DIR . 'includes/class-zhuige-xcx-deactivator.php';
	ZhuiGe_Xcx_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_zhuige_xcx');
register_deactivation_hook(__FILE__, 'deactivate_zhuige_xcx');

function zhuige_xcx_action_links($actions)
{
	$actions[] = '<a href="admin.php?page=zhuige-xcx">设置</a>';
	$actions[] = '<a href="https://zhuige.com/bbs/forum/1.html" target="_blank">技术支持</a>';
    return $actions;
}
add_filter('plugin_action_links_' . ZHUIGE_XCX_BASE_NAME, 'zhuige_xcx_action_links');

// 用户登录检查
add_filter('rest_authentication_errors', function ($error) {
	if (!empty($error)) {
		return $error;
	}

	if (in_array($_SERVER['REQUEST_URI'], ZhuiGe_Xcx::$require_login_uris)) {
		if (!is_user_logged_in()) {
			return new WP_Error('user_not_login', '用户未登录', '');
		}
	}

	return true;
});

require ZHUIGE_XCX_BASE_DIR . 'includes/class-zhuige-xcx.php';
require ZHUIGE_XCX_BASE_DIR . 'includes/zhuige-market.php';
require ZHUIGE_XCX_BASE_DIR . 'includes/class-zhuige-xcx-addon.php';
ZhuiGe_Xcx_Addon::load();

require ZHUIGE_XCX_BASE_DIR . 'includes/zhuige-xcx-function.php';
require ZHUIGE_XCX_BASE_DIR . 'includes/zhuige-xcx-user-column.php';
require ZHUIGE_XCX_BASE_DIR . 'includes/zhuige-xcx-dashboard.php';
require ZHUIGE_XCX_BASE_DIR . 'includes/zhuige-xcx-plugins.php';

foreach (ZhuiGe_Xcx_Addon::$post_types as $post_type) {
	$file_path = ZHUIGE_XCX_ADDONS_DIR . $post_type;
	if (file_exists($file_path)) {
		require_once($file_path);
	}
}

foreach (ZhuiGe_Xcx_Addon::$cruds as $crud) {
	$file_path = ZHUIGE_XCX_ADDONS_DIR . $crud;
	if (file_exists($file_path)) {
		require_once($file_path);
	}
}

require ZHUIGE_XCX_BASE_DIR . 'includes/zhuige-xcx-user-property.php';
foreach (ZhuiGe_Xcx_Addon::$users as $user) {
	$file_path = ZHUIGE_XCX_ADDONS_DIR . $user;
	if (file_exists($file_path)) {
		require_once($file_path);
	}
}

function run_zhuige_xcx()
{
	$plugin = new ZhuiGe_Xcx();
	$plugin->run();
}
run_zhuige_xcx();
