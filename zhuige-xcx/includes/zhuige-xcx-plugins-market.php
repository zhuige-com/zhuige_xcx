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

if (!defined('ZHUIGE_XCX_PLUGINS_MARKET')) {
    define('ZHUIGE_XCX_PLUGINS_MARKET', 1);

    function zhuige_xcx_plugins_market()
    {
        $code = get_user_meta(get_current_user_id(), 'zhuige-xcx-plugins-market-code', true);
        echo "<script>var zhuige_xcx_plugins_market_code = " . ($code ? "'$code'" : "false") . ";</script>";
        $free = isset($_GET['free']) ? sanitize_text_field(wp_unslash($_GET['free'])) : "100";
        echo "<script>var zhuige_xcx_plugins_market_free = '$free';</script>";

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
                        <li class="<?php echo $free == '100' ? 'activ' : ''; ?>">
                            <a href="<?php echo admin_url('admin.php?page=zhuige_xcx_plugins'); ?>" title="全部插件">全部插件</a>
                        </li>
                        <li class="<?php echo $free == '0' ? 'activ' : ''; ?>">
                            <a href="<?php echo admin_url('admin.php?page=zhuige_xcx_plugins&free=0'); ?>" title="付费">付费</a>
                        </li>
                        <li class="<?php echo $free == '1' ? 'activ' : ''; ?>">
                            <a href="<?php echo admin_url('admin.php?page=zhuige_xcx_plugins&free=1'); ?>" title="免费">免费</a>
                        </li>
                    </ul>

                    <div class="zhuige-market-ad" style="height:24px;overflow:hidden;">
                        <div class="dashicons-before dashicons-megaphone" aria-hidden="true"></div>
                        <div style="height:24px;">
                            <ol class="zhuige-plugins-market-ads">

                            </ol>
                        </div>
                    </div>
                </div>

                <div class="zhuige-plugins-market-notice" style="display:none;"></div>

                <div class="zhuige-market-list">
                    <!-- 内容待填充 -->
                </div>

            </div>
        </div>
<?php
    }
}
