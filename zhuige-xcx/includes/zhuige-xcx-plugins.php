<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
 */

if (!defined('ABSPATH')) {
	exit;
}

if (!defined('ZHUIGE_XCX_PLUGINS')) {
	define('ZHUIGE_XCX_PLUGINS', 1);

	if (!class_exists('WP_List_Table')) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
	}

	require dirname(__FILE__) . '/zhuige-xcx-plugins-market.php';
	require dirname(__FILE__) . '/zhuige-xcx-plugins-my.php';

	add_action('admin_menu', 'zhuige_xcx_plugins_market_add_menu_items');

	function zhuige_xcx_plugins_market_badge()
	{
		$new_plugin_count = 0;

		$filePath = ZHUIGE_XCX_ADDONS_DIR . 'cache_version.json';
		if (file_exists($filePath)) {
			if (filemtime($filePath) + 10 > time()) {
				$version_content = file_get_contents($filePath);
				$versions = json_decode($version_content, true);
				foreach ($versions as &$version) {
					if ($version['local_version'] != $version['version']) {
						$new_plugin_count++;
					}
				}
				return $new_plugin_count;
			}
		}

		$response = wp_remote_get("https://www.zhuige.com/api/plugins/plugins_version");

		if (is_wp_error($response) || $response['response']['code'] != 200) {
			return false;
		}

		$data = json_decode($response['body'], TRUE);
		$datadata = $data['data'];
		$products = $datadata['products'];

		if (!file_exists($filePath)) {
			foreach ($products as &$product) {
				$product['local_version'] = $product['version'];
			}
			file_put_contents($filePath, json_encode($products));
			return false;
		}

		$version_content = file_get_contents($filePath);
		$versions = json_decode($version_content, true);

		$new_plugin_count = 0;
		$new_plugins = [];
		foreach ($products as $product) {
			$found = false;
			foreach ($versions as &$version) {
				if ($product['alias'] == $version['alias']) {
					$found = true;

					$version['version'] = $product['version'];

					if ($version['local_version'] != $version['version']) {
						$new_plugin_count++;
					}

					break;
				}
			}

			if ($found) {
				continue;
			}

			$new_plugin_count++;
			$new_plugins[] = [
				'id' => $product['id'],
				'alias' => $product['alias'],
				'version' => $product['version'],
				'local_version' => $product['version'],
			];
		}

		file_put_contents($filePath, json_encode(array_merge($versions, $new_plugins)));

		return $new_plugin_count;
	}

	function zhuige_xcx_plugins_market_add_menu_items()
	{
		$badge = zhuige_xcx_plugins_market_badge();

		add_menu_page(
			'追格插件管理',			 // Page title.
			'追格插件管理' . ($badge ? '<span class="awaiting-mod">' . $badge . '</span>' : ''),			 // Menu title.
			'manage_options',		// Capability.
			'zhuige_xcx_plugins',	// Menu slug.
			'',						// Callback function.
			'',						// 菜单图标
			3						// position
		);

		$zhuige_xcx_plugins_market_hook = add_submenu_page(
			'zhuige_xcx_plugins',	// parent slug
			'插件市场',				 // Page title.
			'插件市场',				 // Menu title.
			'manage_options',		// Capability.
			'zhuige_xcx_plugins',	// Menu slug.
			'zhuige_xcx_plugins_market'	// Callback function.
		);

		add_action('admin_print_scripts-' . $zhuige_xcx_plugins_market_hook, 'enqueue_script_zhuige_xcx_plugins_market');
		add_action('admin_print_styles-' . $zhuige_xcx_plugins_market_hook, 'enqueue_style_zhuige_xcx_plugins_market');

		$zhuige_xcx_plugins_my_hook = add_submenu_page(
			'zhuige_xcx_plugins',	//parent slug
			'我的插件',				// Page title.
			'我的插件',				// Menu title.
			'manage_options',		// Capability.
			'zhuige_xcx_plugins_my',	// Menu slug.
			'zhuige_xcx_plugins_my'	// Callback function.
		);

		add_action('admin_print_scripts-' . $zhuige_xcx_plugins_my_hook, 'enqueue_script_zhuige_xcx_plugins_my');
		add_action('admin_print_styles-' . $zhuige_xcx_plugins_my_hook, 'enqueue_style_zhuige_xcx_plugins_my');
	}

	//加载js - 插件市场
	function enqueue_script_zhuige_xcx_plugins_market()
	{
		wp_enqueue_script('zhuige-scroll-jquery', ZHUIGE_XCX_BASE_URL . "admin/js/zhuige-xcx-scroll-jquery.js", array('jquery'), '1.0.0', true);
		wp_enqueue_script('zhuige-layer', ZHUIGE_XCX_BASE_URL . "admin/js/layer/layer.js", array('jquery'), '1.0.0', true);
		wp_enqueue_script('zhuige-plugins-js', ZHUIGE_XCX_BASE_URL . "admin/js/zhuige-xcx-plugins.js", array('jquery', 'zhuige-scroll-jquery', 'zhuige-layer'), '1.0.0', true);
		wp_enqueue_script('zhuige-plugins-market-js', ZHUIGE_XCX_BASE_URL . "admin/js/zhuige-xcx-plugins-market.js", array('jquery', 'zhuige-scroll-jquery', 'zhuige-layer'), '1.0.0', true);
	}

	//加载css - 插件市场
	function enqueue_style_zhuige_xcx_plugins_market()
	{
		wp_enqueue_style('zhuige-plugins-market-css', ZHUIGE_XCX_BASE_URL . "admin/css/zhuige-xcx-plugins.css");
	}

	//加载js - 我的插件
	function enqueue_script_zhuige_xcx_plugins_my()
	{
		wp_enqueue_script('zhuige-scroll-jquery', ZHUIGE_XCX_BASE_URL . "admin/js/zhuige-xcx-scroll-jquery.js", array('jquery'), '1.0.0', true);
		wp_enqueue_script('zhuige-layer', ZHUIGE_XCX_BASE_URL . "admin/js/layer/layer.js", array('jquery'), '1.0.0', true);
		wp_enqueue_script('zhuige-plugins-js', ZHUIGE_XCX_BASE_URL . "admin/js/zhuige-xcx-plugins.js", array('jquery', 'zhuige-scroll-jquery', 'zhuige-layer'), '1.0.0', true);
		wp_enqueue_script('zhuige-plugins-my-js', ZHUIGE_XCX_BASE_URL . "admin/js/zhuige-xcx-plugins-my.js", array('jquery', 'zhuige-scroll-jquery', 'zhuige-layer'), '1.0.0', true);
	}

	//加载css - 我的插件
	function enqueue_style_zhuige_xcx_plugins_my()
	{
		wp_enqueue_style('zhuige-plugins-market-css', ZHUIGE_XCX_BASE_URL . "admin/css/zhuige-xcx-plugins.css");
	}
}
