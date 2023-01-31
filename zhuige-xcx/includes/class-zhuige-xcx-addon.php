<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
 */

class ZhuiGe_Xcx_Addon
{

	/**
	 * 已启用的插件
	 */
	public static $addons = [];

	/**
	 * 数据库
	 */
	public static $sqls = [];

	/**
	 * 函数
	 */
	public static $funcs = [];

	/**
	 * 文章类型
	 */
	public static $post_types = [];

	/**
	 * 后台配置
	 */
	public static $admins = [];

	/**
	 * 数据接口
	 */
	public static $rests = [];

	/**
	 * AJAX
	 */
	public static $ajaxs = [];

	/**
	 * 后台增删改查
	 */
	public static $cruds = [];

	/**
	 * 用户属性
	 */
	public static $users = [];

	/**
	 * 已安装的插件
	 */
	public static $install_addons = [];

	/**
	 * 获取配置
	 */
	public static function load()
	{
		$filePath = ZHUIGE_XCX_ADDONS_DIR . 'addons.json';
		if (!file_exists($filePath)) {
			return;
		}

		$content = file_get_contents($filePath);

		$addons = json_decode($content, true);

		if (isset($addons['addon'])) {
			ZhuiGe_Xcx_Addon::$addons = $addons['addon'];
		}

		if (isset($addons['sql'])) {
			ZhuiGe_Xcx_Addon::$sqls = $addons['sql'];
		}

		if (isset($addons['func'])) {
			ZhuiGe_Xcx_Addon::$funcs = $addons['func'];
		}

		if (isset($addons['post_type'])) {
			ZhuiGe_Xcx_Addon::$post_types = $addons['post_type'];
		}

		if (isset($addons['admin'])) {
			ZhuiGe_Xcx_Addon::$admins = $addons['admin'];
		}

		if (isset($addons['rest'])) {
			ZhuiGe_Xcx_Addon::$rests = $addons['rest'];
		}

		if (isset($addons['ajax'])) {
			ZhuiGe_Xcx_Addon::$ajaxs = $addons['ajax'];
		}

		if (isset($addons['crud'])) {
			ZhuiGe_Xcx_Addon::$cruds = $addons['crud'];
		}

		if (isset($addons['user'])) {
			ZhuiGe_Xcx_Addon::$users = $addons['user'];
		}
	}

	/**
	 * 保存配置
	 */
	public static function save()
	{
		$content = json_encode([
			'addon' => ZhuiGe_Xcx_Addon::$addons,
			'sql' => ZhuiGe_Xcx_Addon::$sqls,
			'func' => ZhuiGe_Xcx_Addon::$funcs,
			'post_type' => ZhuiGe_Xcx_Addon::$post_types,
			'admin' => ZhuiGe_Xcx_Addon::$admins,
			'rest' => ZhuiGe_Xcx_Addon::$rests,
			'ajax' => ZhuiGe_Xcx_Addon::$ajaxs,
			'crud' => ZhuiGe_Xcx_Addon::$cruds,
			'user' => ZhuiGe_Xcx_Addon::$users,
		]);

		file_put_contents(ZHUIGE_XCX_ADDONS_DIR . 'addons.json', $content);
	}

	/**
	 * 启用插件
	 */
	public static function active($addon)
	{
		$filePath = ZHUIGE_XCX_ADDONS_DIR . $addon . '/config.json';
		if (!file_exists($filePath)) {
			return;
		}

		$content = file_get_contents($filePath);

		$config = json_decode($content, true);

		if (!in_array($addon, ZhuiGe_Xcx_Addon::$addons)) {
			array_push(ZhuiGe_Xcx_Addon::$addons, $addon);
		}

		if (isset($config['sql'])) {
			global $wpdb;

			$charset_collate = '';
			if (!empty($wpdb->charset)) {
				$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
			}

			if (!empty($wpdb->collate)) {
				$charset_collate .= " COLLATE {$wpdb->collate}";
			}

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');


			foreach ($config['sql'] as $sql) {
				$file_path = ZHUIGE_XCX_ADDONS_DIR . $sql;
				if (file_exists($file_path)) {
					require_once($file_path);
				}

				if (!in_array($sql, ZhuiGe_Xcx_Addon::$sqls)) {
					array_push(ZhuiGe_Xcx_Addon::$sqls, $sql);
				}
			}
		}

		if (isset($config['func'])) {
			foreach ($config['func'] as $func) {
				if (!in_array($func, ZhuiGe_Xcx_Addon::$funcs)) {
					array_push(ZhuiGe_Xcx_Addon::$funcs, $func);
				}
			}
		}

		if (isset($config['post_type'])) {
			foreach ($config['post_type'] as $post_type) {
				if (!in_array($post_type, ZhuiGe_Xcx_Addon::$post_types)) {
					array_push(ZhuiGe_Xcx_Addon::$post_types, $post_type);
				}
			}
		}

		if (isset($config['admin'])) {
			foreach ($config['admin'] as $admin) {
				if (!in_array($admin, ZhuiGe_Xcx_Addon::$admins)) {
					array_push(ZhuiGe_Xcx_Addon::$admins, $admin);
				}
			}
		}

		if (isset($config['rest'])) {
			foreach ($config['rest'] as $rest) {
				if (!in_array($rest, ZhuiGe_Xcx_Addon::$rests)) {
					array_push(ZhuiGe_Xcx_Addon::$rests, $rest);
				}
			}
		}

		if (isset($config['ajax'])) {
			foreach ($config['ajax'] as $ajax) {
				if (!in_array($ajax, ZhuiGe_Xcx_Addon::$ajaxs)) {
					array_push(ZhuiGe_Xcx_Addon::$ajaxs, $ajax);
				}
			}
		}

		if (isset($config['crud'])) {
			foreach ($config['crud'] as $crud) {
				if (!in_array($crud, ZhuiGe_Xcx_Addon::$cruds)) {
					array_push(ZhuiGe_Xcx_Addon::$cruds, $crud);
				}
			}
		}

		if (isset($config['user'])) {
			foreach ($config['user'] as $user) {
				if (!in_array($user, ZhuiGe_Xcx_Addon::$users)) {
					array_push(ZhuiGe_Xcx_Addon::$users, $user);
				}
			}
		}

		ZhuiGe_Xcx_Addon::save();
	}

	/**
	 * 关闭插件
	 */
	public static function deactive($addon)
	{
		$filePath = ZHUIGE_XCX_ADDONS_DIR . $addon . '/config.json';
		if (!file_exists($filePath)) {
			return;
		}

		$content = file_get_contents($filePath);

		$config = json_decode($content, true);

		ZhuiGe_Xcx_Addon::$addons = ZhuiGe_Xcx_Addon::minus(ZhuiGe_Xcx_Addon::$addons, [$addon]);

		if (isset($config['sql'])) {
			ZhuiGe_Xcx_Addon::$sqls = ZhuiGe_Xcx_Addon::minus(ZhuiGe_Xcx_Addon::$sqls, $config['sql']);
		}

		if (isset($config['func'])) {
			ZhuiGe_Xcx_Addon::$funcs = ZhuiGe_Xcx_Addon::minus(ZhuiGe_Xcx_Addon::$funcs, $config['func']);
		}

		if (isset($config['post_type'])) {
			ZhuiGe_Xcx_Addon::$post_types = ZhuiGe_Xcx_Addon::minus(ZhuiGe_Xcx_Addon::$post_types, $config['post_type']);
		}

		if (isset($config['admin'])) {
			ZhuiGe_Xcx_Addon::$admins = ZhuiGe_Xcx_Addon::minus(ZhuiGe_Xcx_Addon::$admins, $config['admin']);
		}

		if (isset($config['rest'])) {
			ZhuiGe_Xcx_Addon::$rests = ZhuiGe_Xcx_Addon::minus(ZhuiGe_Xcx_Addon::$rests, $config['rest']);
		}

		if (isset($config['ajax'])) {
			ZhuiGe_Xcx_Addon::$ajaxs = ZhuiGe_Xcx_Addon::minus(ZhuiGe_Xcx_Addon::$ajaxs, $config['ajax']);
		}

		if (isset($config['crud'])) {
			ZhuiGe_Xcx_Addon::$cruds = ZhuiGe_Xcx_Addon::minus(ZhuiGe_Xcx_Addon::$cruds, $config['crud']);
		}

		if (isset($config['user'])) {
			ZhuiGe_Xcx_Addon::$users = ZhuiGe_Xcx_Addon::minus(ZhuiGe_Xcx_Addon::$users, $config['user']);
		}

		ZhuiGe_Xcx_Addon::save();
	}

	/**
	 * 判断某个插件是否已安装并激活
	 */
	public static function is_active($addon)
	{
		return in_array($addon, ZhuiGe_Xcx_Addon::$addons);
	}

	/**
	 * 两个数组相减
	 */
	public static function minus($a, $b)
	{
		if (!is_array($a) || !is_array($b)) {
			return [];
		}

		$res = [];
		foreach ($a as $item) {
			if (!in_array($item, $b)) {
				$res[] = $item;
			}
		}

		return $res;
	}

	/**
	 * 是否已安装
	 */
	public static function is_installed($test)
	{
		if (empty($install_addons)) {
			$addons = scandir(ZHUIGE_XCX_ADDONS_DIR);
			foreach ($addons as $addon) {
				if ($addon == '.' || $addon == '..' || !is_dir(ZHUIGE_XCX_ADDONS_DIR . $addon)) {
					continue;
				}

				$install_addons[] = $addon;
			}
		}

		return in_array($test, $install_addons);
	}
}
