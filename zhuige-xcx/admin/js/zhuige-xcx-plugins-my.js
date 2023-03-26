(function ($) {
    'use strict';
    $(function () {

        function active_addon(addon, code2) {
            $.post("/wp-admin/admin-ajax.php",
                {
                    action: 'admin_active_addon',
                    addon: addon,
                    code2: code2
                },
                function (res, status) {
                    if (status != 'success' || !res.success) {
                        layer.msg('系统异常');
                        return;
                    }

                    // console.log(res);

                    if (res.data.code == 1) {
                        window.location.reload();
                    } else if (res.data.code == 100) {
                        layer.msg(res.data.msg);
                        $('.div-zhuige-xcx-plugins-market-login').show()
                    } else if (res.data.code == 101) {
                        // layer.msg(res.data.msg);
                        $('.btn-zhuige-xcx-plugins-plugin-detail-url').attr('href', 'https://www.zhuige.com/product/' + addon + '.html');
                        $('.input-zhuige-xcx-plugins-plugin-alias').val(addon);
                        $('.div-zhuige-xcx-plugins-license-code').show()
                    }
                });
        }

        $(document).on('click', '.btn-zhuige-xcx-plugins-license-code-submit', function () {
            let alias = $('.input-zhuige-xcx-plugins-plugin-alias').val();
            if (!alias || alias.length == 0) {
                layer.msg('系统错误');
                return;
            }

            let license_code = $('.input-zhuige-xcx-plugins-license-code').val();
            if (!license_code || license_code.length == 0) {
                layer.msg('请输入插件授权码');
                return;
            }

            $('.div-zhuige-xcx-plugins-license-code').hide()

            active_addon(alias, license_code);
        });

        $(document).on('click', '.btn-zhuige-xcx-plugins-license-code-hide', function () {
            $('.div-zhuige-xcx-plugins-license-code').hide()
        });

        //激活追格插件
        $('.btn-zhuige-addon-active').click(function () {
            let addon = $(this).data('addon');
            active_addon(addon, '')
        });

        //禁用追格插件
        $('.btn-zhuige-addon-deactive').click(function () {
            $.post("/wp-admin/admin-ajax.php",
                {
                    action: 'admin_deactive_addon',
                    addon: $(this).data('addon')
                },
                function (res, status) {
                    window.location.reload();
                });
        });

        /**
         * 删除插件
         */
        $(document).on('click', '.btn-zhuige-xcx-plugins-market-delete', function () {
            let alias = $(this).data('alias');
            let layer_confirm = layer.confirm('真的要删除吗？', { icon: 3, title: '提示' }, function (index) {
                layer.close(layer_confirm);
                layer.msg('删除中~')
                $.post("/wp-admin/admin-ajax.php",
                    {
                        action: 'admin_zhuige_xcx_plugins_market_delete',
                        alias: alias,
                    },
                    function (res, status) {
                        layer.closeAll();

                        if (status != 'success' || !res.success) {
                            layer.msg('系统异常');
                            return;
                        }

                        window.location.reload();
                    }
                );
            })
        });

        /**
         * 广告滚动
         */
        if ($('.zhuige-plugins-market-ads li').length > 1) {
            // $('.zhuige-plugins-market-ads').scrollQ({
            //     line: 1,
            //     scrollNum: 1,
            //     scrollTime: 2000
            // });

            setInterval(() => {
                let slideCon = $('.zhuige-plugins-market-ads');
                let slideHeight = slideCon.parent().height();
                slideCon.stop(true, true).animate({
                    marginTop: (-slideHeight) + "px"
                },
                500,
                function() {
                    $(this).css({
                        marginTop: "0px"
                    }).find("li:first").appendTo(this);
                });
            }, 2000)
        }
        
    });

})(jQuery);
