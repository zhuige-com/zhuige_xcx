(function ($) {
    'use strict';
    $(function () {

        $('.btn-zhuige-xcx-plugins-market-login-show').click(function () {
            $('.div-zhuige-xcx-plugins-market-login').show()
        });

        $('.btn-zhuige-xcx-plugins-market-login-hide').click(function () {
            $('.div-zhuige-xcx-plugins-market-login').hide()
        });

        /**
         * 登录插件市场
         */
         function login_plugins_market(plugins_market_code, alert) {
            if (!plugins_market_code) {
                return;
            }

            $.post("/wp-admin/admin-ajax.php",
                {
                    action: 'admin_zhuige_xcx_plugins_market_login',
                    code: plugins_market_code,
                },
                function (res, status) {
                    if (status != 'success' || !res.success) {
                        if (alert) {
                            layer.msg('登录失败');
                        }

                        if (zhuige_xcx_plugins_market_code) {
                            zhuige_xcx_plugins_market_code = undefined;
                        }
                        return;
                    }

                    $('.div-zhuige-xcx-plugins-market-login').hide()
                    $('.btn-zhuige-xcx-plugins-market-login-show').hide();
                    $('.li-zhuige-xcx-plugins-market-nickname').html(
                        '<a href="https://www.zhuige.com/ucenter.html" target="_blank">' + res.data.nickname + '</a>'
                        + '(<a href="javascript:void(0)" class="btn-zhuige-xcx-plugins-market-logout">注销</a>)'
                    );
                    $('.li-zhuige-xcx-plugins-market-nickname').show();
                    // window.location.reload();
                    zhuige_xcx_plugins_market_code = plugins_market_code;
                });
        }

        if (zhuige_xcx_plugins_market_code) {
            login_plugins_market(zhuige_xcx_plugins_market_code)
        }
        $(document).on('click', '.btn-zhuige-xcx-plugins-market-login-submit', function () {
            login_plugins_market($('.input-zhuige-xcx-plugins-market-code').val(), true)
        });

        // 注销
        $(document).on('click', '.btn-zhuige-xcx-plugins-market-logout', function () {
            $.post("/wp-admin/admin-ajax.php",
                {
                    action: 'admin_zhuige_xcx_plugins_market_logout'
                },
                function (res, status) {
                    if (status != 'success' || !res.success) {
                        layer.msg('网络错误~');
                        return;
                    }
                    
                    window.location.reload();
                });
        });

        /**
         * 清除新版本通知
         */
         $(document).on('click', '.btn-click-view-plugin-detail', function () {
            let addon = $(this).data('alias');
            window.open('https://www.zhuige.com/product/' + addon + '.html');

            $.post("/wp-admin/admin-ajax.php",
                {
                    action: 'admin_zhuige_xcx_plugins_market_clear_version',
                    addon: addon,
                },
                function (res, status) {
                    if (status != 'success' || !res.success) {
                        return;
                    }

                    window.location.reload();
                });
        });

        /**
         * 更新插件
         */
        $(document).on('click', '.btn-zhuige-xcx-plugins-market-update', function () {
            if (!zhuige_xcx_plugins_market_code) {
                $('.div-zhuige-xcx-plugins-market-login').show()
                return;
            }

            // 下载代码
            // 解压到插件目录
            let alias = $(this).data('alias');
            let layer_confirm = layer.confirm('将覆盖旧插件，如有修改，务必先备份！现在更新吗？', { icon: 3, title: '提示' }, function (index) {
                layer.close(layer_confirm);
                layer.msg('更新中~')
                $.post("/wp-admin/admin-ajax.php",
                    {
                        action: 'admin_zhuige_xcx_plugins_market_update',
                        alias: alias,
                    },
                    function (res, status) {
                        layer.closeAll();

                        if (status != 'success' || !res.success) {
                            layer.msg('系统异常');
                            return;
                        }

                        if (res.data.code == 100) {
                            window.open(res.data.data.url);
                            return;
                        } else if (res.data.code == 200) {
                            let layer_confirm = layer.confirm(res.data.msg, { icon: 3, title: '提示' }, function (index) {
                                layer.close(layer_confirm);
                                window.open('https://www.zhuige.com/license_code.html');
                            })
                            return;
                        } else if (res.data.code == 0) {
                            layer.msg(res.data.msg);
                            return;
                        }

                        window.location.reload();
                    }
                );
            })
        });

    });

})(jQuery);
