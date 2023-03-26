(function ($) {
    'use strict';
    $(function () {

        /**
         * 下载安装插件
         */
        function install_plugin(alias, license_code='') {
            // 下载代码
            // 解压到插件目录
            layer.msg('安装中~')
            $.post("/wp-admin/admin-ajax.php",
                {
                    action: 'admin_zhuige_xcx_plugins_market_install',
                    alias: alias,
                    code2: license_code,
                },
                function (res, status) {
                    layer.closeAll();

                    if (status != 'success' || !res.success) {
                        layer.msg('系统异常');
                        return;
                    }

                    if (res.data.code == 100) {
                        layer.confirm("请先购买此插件", { icon: 3, title: '提示' }, function (index) {
                            window.open(res.data.data.url);
                        });
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
        }

        /**
         * 安装插件
         */
        $(document).on('click', '.btn-zhuige-xcx-plugins-market-install', function () {
            if (!zhuige_xcx_plugins_market_code) {
                $('.div-zhuige-xcx-plugins-market-login').show()
                return;
            }

            let free = $(this).data('free');
            let alias = $(this).data('alias');

            if (free == '0') {
                $('.btn-zhuige-xcx-plugins-plugin-detail-url').attr('href', 'https://www.zhuige.com/product/' + alias + '.html');
                $('.input-zhuige-xcx-plugins-plugin-alias').val(alias);
                $('.div-zhuige-xcx-plugins-license-code').show()
            } else {
                install_plugin(alias, '');
            }
        });

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

            install_plugin(alias, license_code);
        });

        $(document).on('click', '.btn-zhuige-xcx-plugins-license-code-hide', function () {
            $('.div-zhuige-xcx-plugins-license-code').hide()
        });
        

        /**
         * 加载插件数据
         */
        function load_plugins_market_list() {
            var loading = layer.load();
            $.post("/wp-admin/admin-ajax.php",
                {
                    action: 'admin_zhuige_xcx_plugins_market_list',
                    start: $('.zhuige-market-view').length,
                    free: (zhuige_xcx_plugins_market_free == '100' ? '' : zhuige_xcx_plugins_market_free),
                },
                function (res, status) {
                    layer.close(loading);
                    // console.log(res);
                    
                    if (status != 'success' || !res.success) {
                        return;
                    }

                    let plugins = res.data.products;
                    let content = '';
                    for (let i = 0; i < plugins.length; i++) {
                        let plugin = plugins[i];

                        let element = '';

                        element += '<div class="zhuige-market-view">';
                        element += '<div class="zhuige-market-bg">';
                        element += '<div class="zhuige-market-cover">';
                        element += '<a href="javascript:void(0)" data-alias="' + plugin.alias + '" class="btn-click-view-plugin-detail" title="' + plugin.title + '">';

                        if (plugin.new_version) {
                            element += '<strong>新版</strong>';
                        }

                        element += '<img src="' + plugin.cover + '" />';
                        element += '</a>';
                        element += '</div>';
                        element += '<div class="zhuige-market-info">';
                        element += '<div class="zhuige-market-text">';
                        element += '<div>';
                        element += '<a href="javascript:void(0)" data-alias="' + plugin.alias + '" class="market-title btn-click-view-plugin-detail">' + plugin.title + '</a>';

                        for (let i = 0; i < plugin.tags.length; i++) {
                            // element += '<a href="#">' + plugin.tags[i] + '</a>';
                            element += '<span>' + plugin.tags[i] + '</span>';
                        }

                        element += '</div>';
                        element += '<div>' + plugin.subtitle + '</div>';
                        element += '<div>';
                        if (plugin.promote) {
                            element += '<text>' + plugin.promote + '</text>';
                        }
                        element += '<em>￥</em>';
                        element += '<strong>' + plugin.price + '</strong>';
                        element += '<cite>原价' + plugin.original + '</cite>';
                        element += '</div>';
                        element += '</div>';
                        element += '<div class="zhuige-market-btn">';
                        element += '<text>版本 V ' + plugin.version + '</text>';

                        if (plugin.installed) {
                            if (plugin.new_version) {
                                element += '<a href="javascript:void(0)" title="更新" data-alias="' + plugin.alias + '" class="update btn-zhuige-xcx-plugins-market-update">更新</a>';
                            } else {
                                element += '<a href="javascript:void(0)" title="已安装" class="">已安装</a>';
                            }
                        } else {
                            element += '<a href="javascript:void(0)" title="安装" data-free="' + plugin.free + '" data-alias="' + plugin.alias + '" class="setup btn-zhuige-xcx-plugins-market-install">安装</a>';
                        }

                        // element += '<a href="' + plugin.url + '" target="_blank" title="详细介绍">详细介绍</a>';
                        element += '</div>';
                        element += '</div>';
                        element += '</div>';
                        element += '</div>';

                        content += element;
                    }
                    $('.zhuige-market-list').append(content);
                    $('.zhuige-market-list').addClass('slide-top');

                    let ads = res.data.ads;
                    content = '';
                    for (let i = 0; i < ads.length; i++) {
                        let element = '';

                        element += '<li style="margin:0;">';
                        element += '<a href="' + ads[i].link + '" target="_blank" title="' + ads[i].title + '">' + ads[i].title + '</a>';
                        element += '</li>';

                        content += element;
                    }
                    $('.zhuige-plugins-market-ads').append(content);

                    if (ads.length > 1) {
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
                    
                    if (res.data.notice) {
                        let notice = $('.zhuige-plugins-market-notice');
                        notice.html(res.data.notice);
                        notice.addClass('slide-down');
                        notice.show();
                    }
                });
        }
        load_plugins_market_list();

    });

})(jQuery);
