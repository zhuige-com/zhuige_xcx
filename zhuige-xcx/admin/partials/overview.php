<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
 */

$content = '感谢使用追格小程序！ <br/><br/> 微信客服：jianbing2011 (加开源群、问题咨询、项目定制、购买咨询) <br/><br/> <a href="https://www.zhuige.com" target="_blank">追格官网</a>';
if (stripos($_SERVER["REQUEST_URI"], 'zhuige-xcx')) {
    $res = wp_remote_get("https://www.zhuige.com/api/ad/wordpress?id=zhuige_xcx", ['timeout' => 1, 'sslverify' => false]);
    if (!is_wp_error($res) && $res['response']['code'] == 200) {
        $data = json_decode($res['body'], TRUE);
        if ($data['code'] == 1) {
            $content = $data['data'];
        }
    }

    $res = wp_remote_get("https://www.zhuige.com/api/plugins/version?plugin=zhuige_xcx&version=" . ZHUIGE_XCX_VERSION, ['timeout' => 1, 'sslverify' => false]);
    if (!is_wp_error($res) && $res['response']['code'] == 200) {

        $data = json_decode($res['body'], TRUE);
        $is_old = false;
        if ($data['code'] == 1 && isset($data['data']) && isset($data['data']['version']) && $data['data']['version'] != ZHUIGE_XCX_VERSION) {
            $is_old = true;
        }

        $update = '';
        $update .= '<div style="padding: 10px; background: #f5f5f5;">';

        if ($is_old) {
            $update .= '<div style="padding: 10px 0;">';
            $update .= '<h2 style="height: 2em; line-height: 2em; font-size: 18px; font-weight: 500; color: #1d2327; padding: 0; margin: 0;">';
            $update .= '追格小程序（后端）框架在线升级';
            $update .= '</h2>';
            $update .= '<p style="height: 1.8em; line-height: 1.8em; font-size: 12px; font-weight: 400; color: #1d2327; margin: 0; min-height: 30px;">';
            $update .= '你可在此在线更新升级“追格小程序后端框架”。';
            $update .= '</p>';
            $update .= '</div>';
        }


        $update .= '<div style="padding: 10px 0;">';
        $update .= '<h2 style="height: 2em; line-height: 2em; font-size: 18px; font-weight: 500; color: #1d2327; padding: 0; margin: 0;">';
        $update .= '当前版本：<strong>' . $data['data']['version'] . '</strong>';
        $update .= '</h2>';
        $update .= '<p style="height: 1.8em; line-height: 1.8em; font-size: 12px; font-weight: 400; color: #1d2327; margin: 0; min-height: 30px;">';
        $update .= '发布时间：' . $data['data']['time'];
        $update .= '</p>';
        $update .= '</div>';

        if ($is_old) {
            $update .= '<div style="padding: 10px 0;">';
            $update .= '<h2 style="height: 2em; line-height: 2em; font-size: 18px; font-weight: 500; color: #1d2327; padding: 0; margin: 0;">';
            $update .= '你当前使用的版本：' . ZHUIGE_XCX_VERSION;
            $update .= '</h2>';
            $update .= '<p style="height: 1.8em; line-height: 1.8em; font-size: 12px; font-weight: 400; color: #1d2327; margin: 0; min-height: 30px;">';
            $update .= '<a style="display: flex; height: 30px; width: 100px; line-height: 30px; color: #FFFFFF; justify-content: center; border-radius: 3px; background: #1d2327; text-align: center; text-decoration: none;"';
            $update .= 'class="button-zhuige-xcx-update" href="javascript:void(0)" title="立即更新">立即更新</a>';
            $update .= '</p>';
            $update .= '</div>';
        }

        if ($is_old) {
            $update .= '<div style="padding: 10px 0;">';
            $update .= '<h2 style="height: 2em; line-height: 2em; font-size: 18px; font-weight: 500; color: #1d2327; padding: 0; margin: 0;">';
            $update .= '常见问题';
            $update .= '</h2>';
            $update .= '<p style="height: 1.8em; line-height: 1.8em; font-size: 12px; font-weight: 400; color: #1d2327; margin: 0; min-height: 30px;">';
            $update .= '1、追格官网/用户社区：<a href="https://www.zhuige.com" target="_blank" title="追格官网/用户社区">https://www.zhuige.com</a>';
            $update .= '</p>';
            $update .= '<p style="height: 1.8em; line-height: 1.8em; font-size: 12px; font-weight: 400; color: #1d2327; margin: 0; min-height: 30px;">';
            $update .= '2、更新升级注意事项：<a href="https://www.zhuige.com/docs/zg/213.html" target="_blank" title="更新升级注意事项">https://www.zhuige.com/docs/zg/213.html</a>';
            $update .= '</p>';
            $update .= '<p style="height: 1.8em; line-height: 1.8em; font-size: 12px; font-weight: 400; color: #1d2327; margin: 0; min-height: 30px;">';
            $update .= '3、更新日志：<a href="https://www.zhuige.com/docs/zg/215.html" target="_blank" title="更新日志">https://www.zhuige.com/docs/zg/215.html</a>';
            $update .= '</p>';
            $update .= '</div>';
        }

        $update .= '</div>';

        $content = $update . $content;
    }
}

//
// 概要
//
CSF::createSection($prefix, array(
    'title'  => '概要',
    'icon'   => 'fas fa-rocket',
    'fields' => array(

        array(
            'type'    => 'content',
            'content' => $content,
        ),

    )
));
