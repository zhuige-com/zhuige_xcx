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

if (!defined('ZHUIGE_XCX_PLUGINS_MY')) {
    define('ZHUIGE_XCX_PLUGINS_MY', 1);

    function zhuige_xcx_plugins_my()
    {
        $addons = scandir(ZHUIGE_XCX_ADDONS_DIR);

        $code = get_user_meta(get_current_user_id(), 'zhuige-xcx-plugins-market-code', true);
        echo "<script>var zhuige_xcx_plugins_market_code = " . ($code ? "'$code'" : "false") . ";</script>";

        require_once ZHUIGE_XCX_BASE_DIR . 'includes/zhuige-xcx-plugins-inc.php';
?>

        <div class="zhuige-market">
            <div class="zhuige-market-nav">
                <h1>追格插件（模块）市场</h1>
                <ul>
                    <li class="btn-zhuige-xcx-plugins-market-login-show">
                        <a href="javascript:void(0)" title="登录">登录</a>
                    </li>
                    <li class="li-zhuige-xcx-plugins-market-nickname" style="display: none;">

                    </li>
                    <li>
                        <a href="https://www.zhuige.com/" target="_blank" title="追格官网">追格官网</a>
                    </li>
                    <li>
                        <a href="https://www.zhuige.com/docs.html" target="_blank" title="产品文档">产品文档</a>
                    </li>
                    <li>
                        <a href="https://www.zhuige.com/bbs.html" target="_blank" title="用户社区">用户社区</a>
                    </li>
                    <li>
                        <a href="https://www.zhuige.com/product.html" target="_blank" title="更多产品">更多产品</a>
                    </li>
                </ul>
            </div>

            <div class="zhuige-market-box">

                <div class="zhuige-market-type">
                    <ul>
                        <li class="activ">
                            <a href="javascript:void(0)" title="已安装插件">已安装插件</a>
                        </li>
                    </ul>

                    <?php
                    $response = wp_remote_get("https://www.zhuige.com/api/plugins/ads");
                    $ads = [];
                    if (!is_wp_error($response) && $response['response']['code'] == 200) {
                        $data = json_decode($response['body'], TRUE);
                        $ads = $data['data']['ads'];
                    }

                    if (count($ads) > 0) {
                    ?>

                        <div class="zhuige-market-ad" style="height:24px;overflow:hidden;">
                            <div class="dashicons-before dashicons-megaphone" aria-hidden="true"></div>
                            <div style="height:24px;">
                                <ol class="zhuige-plugins-market-ads">
                                    <?php
                                    foreach ($ads as $ad) {
                                        echo '<li><a href="' . $ad['link'] . '" target="_blank" title="' . $ad['title'] . '">' . $ad['title'] . '</a></li>';
                                    }
                                    ?>
                                </ol>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>

                <div class="zhuige-market-list slide-top">
                    <!-- 结构块-->
                    <?php
                    $filePath = ZHUIGE_XCX_ADDONS_DIR . 'cache_version.json';
                    $version_content = file_get_contents($filePath);
                    $versions = json_decode($version_content, true);

                    foreach ($addons as $addon) {
                        if ($addon == '.' || $addon == '..' || !is_dir(ZHUIGE_XCX_ADDONS_DIR . $addon)) {
                            continue;
                        }

                        $addon_config_path = ZHUIGE_XCX_ADDONS_DIR . $addon . '/config.json';
                        if (!file_exists($addon_config_path)) {
                            continue;
                        }

                        $config = json_decode(file_get_contents($addon_config_path), true);

                        $active = in_array($addon, ZhuiGe_Xcx_Addon::$addons);

                        $link = $active ? '<a href="javascript:void(0)" data-addon="' . $addon . '" class="btn-zhuige-addon-deactive unpack">禁用</a>'
                            : '<a href="javascript:void(0)" data-addon="' . $addon . '" class="btn-zhuige-addon-active">启用</a>';

                        $addon_screenshot_path = ZHUIGE_XCX_ADDONS_DIR . $addon . '/screenshot.png';
                        if (!file_exists($addon_screenshot_path)) {
                            $addon_screenshot_path = ZHUIGE_XCX_BASE_URL . "public/images/placeholder.jpg";
                        } else {
                            $addon_screenshot_path = ZHUIGE_XCX_BASE_URL . "addons/" . $addon . "/screenshot.png";
                        }

                        $new_version = false;
                        foreach ($versions as $version) {
                            if ($version['alias'] == $addon && $config['version'] != $version['version']) {
                                $new_version = true;
                            }
                        }
                    ?>
                        <div class="zhuige-market-view">
                            <div class="zhuige-market-bg">
                                <div class="zhuige-market-cover">
                                    <a href="javascript:void(0)" data-alias="<?php echo $addon; ?>" class="btn-click-view-plugin-detail" title="详细介绍">
                                        <?php if ($new_version) { ?>
                                            <cite>有新版本可用</cite>
                                        <?php } ?>
                                        <img src="<?php echo $addon_screenshot_path; ?>" />
                                    </a>
                                </div>
                                <div class="zhuige-market-info">
                                    <div class="zhuige-market-text">
                                        <div>
                                            <a href="javascript:void(0)" data-alias="<?php echo $addon; ?>" class="market-title btn-click-view-plugin-detail"><?php echo $config['name']; ?></a>
                                            <?php
                                            foreach ($config['keywords'] as $keyword) {
                                                echo '<span>' . $keyword . '</span>';
                                            }
                                            ?>
                                        </div>
                                        <div><?php echo $config['description']; ?></div>
                                        <div>
                                            <?php echo $link; ?>
                                            <a href="<?php echo $config['help']; ?>" target="_blank" title="">帮助文档</a>
                                            <?php if ($config['setting']) { ?>
                                                <a href="<?php echo admin_url($config['setting']); ?>" target="_blank" title="设置">设置</a>
                                            <?php } ?>
                                            <?php if (!$active) { ?>
                                                <a href="javascript:void(0)" data-alias="<?php echo $addon; ?>" class="btn-zhuige-xcx-plugins-market-delete" title="删除">删除</a>
                                            <?php } ?>
                                            <?php if ($new_version) { ?>
                                                <a href="javascript:void(0)" title="更新" data-alias="<?php echo $addon; ?>" class="btn-zhuige-xcx-plugins-market-update">更新</a>
                                            <?php } ?>
                                            <?php if (file_exists(ZHUIGE_XCX_BASE_DIR . 'addons/' . $addon . '/client.zip')) { ?>
                                                <a href="<?php echo ZHUIGE_XCX_BASE_URL . 'addons/' . $addon . '/client.zip' ?>" target="_blank">前端代码</a>
                                            <?php } else { ?>
                                                <a href="https://www.zhuige.com/license_code.html" target="_blank">前端代码</a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="zhuige-market-btn">
                                        <text>版本 V <?php echo $config['version']; ?></text>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
<?php
    }
}
