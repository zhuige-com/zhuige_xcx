<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
 */

if (!function_exists('zhuige_xcx_import_image2attachment')) {
    //把图片添加到媒体库
    function zhuige_xcx_import_image2attachment($file, $post_id = 0, $import_date = 'current', $qrcode = false)
    {
        if ($qrcode && !ZhuiGe_Xcx::option_value('zhuige_switch_oss')) {
            return new WP_Error('zhuige', 'zhuige');
        }

        set_time_limit(0);

        // Initially, Base it on the -current- time.
        $time = current_time('mysql', 1);
        // Next, If it's post to base the upload off:
        if ('post' == $import_date && $post_id > 0) {
            $post = get_post($post_id);
            if ($post && substr($post->post_date_gmt, 0, 4) > 0) {
                $time = $post->post_date_gmt;
            }
        } elseif ('file' == $import_date) {
            $time = gmdate('Y-m-d H:i:s', @filemtime($file));
        }

        // A writable uploads dir will pass this test. Again, there's no point overriding this one.
        if (!(($uploads = wp_upload_dir($time)) && false === $uploads['error'])) {
            return new WP_Error('upload_error', $uploads['error']);
        }

        $wp_filetype = wp_check_filetype($file, null);

        extract($wp_filetype);

        if ((!$type || !$ext) && !current_user_can('unfiltered_upload')) {
            return new WP_Error('wrong_file_type', __('Sorry, this file type is not permitted for security reasons.', 'add-from-server'));
        }

        // Is the file allready in the uploads folder?
        // WP < 4.4 Compat: ucfirt
        if (preg_match('|^' . preg_quote(ucfirst(wp_normalize_path($uploads['basedir'])), '|') . '(.*)$|i', $file, $mat)) {

            $filename = basename($file);
            $new_file = $file;

            $url = $uploads['baseurl'] . $mat[1];

            $attachment = get_posts(array('post_type' => 'attachment', 'meta_key' => '_wp_attached_file', 'meta_value' => ltrim($mat[1], '/')));
            if (!empty($attachment)) {
                return new WP_Error('file_exists', __('Sorry, That file already exists in the WordPress media library.', 'add-from-server'));
            }

            // Ok, Its in the uploads folder, But NOT in WordPress's media library.
            if ('file' == $import_date) {
                $time = @filemtime($file);
                if (preg_match("|(\d+)/(\d+)|", $mat[1], $datemat)) { // So lets set the date of the import to the date folder its in, IF its in a date folder.
                    $hour = $min = $sec = 0;
                    $day = 1;
                    $year = $datemat[1];
                    $month = $datemat[2];

                    // If the files datetime is set, and it's in the same region of upload directory, set the minute details to that too, else, override it.
                    if ($time && wp_date('Y-m', $time) == "$year-$month") {
                        list($hour, $min, $sec, $day) = explode(';', wp_date('H;i;s;j', $time));
                    }

                    $time = mktime($hour, $min, $sec, $month, $day, $year);
                }
                $time = gmdate('Y-m-d H:i:s', $time);

                // A new time has been found! Get the new uploads folder:
                // A writable uploads dir will pass this test. Again, there's no point overriding this one.
                if (!(($uploads = wp_upload_dir($time)) && false === $uploads['error'])) {
                    return new WP_Error('upload_error', $uploads['error']);
                }
                $url = $uploads['baseurl'] . $mat[1];
            }
        } else {
            $filename = wp_unique_filename($uploads['path'], basename($file));

            // copy the file to the uploads dir
            $new_file = $uploads['path'] . '/' . $filename;
            if (false === @copy($file, $new_file))
                return new WP_Error('upload_error', sprintf(__('The selected file could not be copied to %s.', 'add-from-server'), $uploads['path']));

            // Set correct file permissions
            $stat = stat(dirname($new_file));
            $perms = $stat['mode'] & 0000666;
            @chmod($new_file, $perms);
            // Compute the URL
            $url = $uploads['url'] . '/' . $filename;

            if ('file' == $import_date) {
                $time = gmdate('Y-m-d H:i:s', @filemtime($file));
            }
        }

        // Apply upload filters
        $return = apply_filters('wp_handle_upload', array('file' => $new_file, 'url' => $url, 'type' => $type), 'upload');
        $new_file = $return['file'];
        $url = $return['url'];
        $type = $return['type'];

        $title = preg_replace('!\.[^.]+$!', '', basename($file));
        $content = $excerpt = '';

        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        if (preg_match('#^audio#', $type)) {
            $meta = wp_read_audio_metadata($new_file);

            if (!empty($meta['title'])) {
                $title = $meta['title'];
            }

            if (!empty($title)) {

                if (!empty($meta['album']) && !empty($meta['artist'])) {
                    /* translators: 1: audio track title, 2: album title, 3: artist name */
                    $content .= sprintf(__('"%1$s" from %2$s by %3$s.', 'add-from-server'), $title, $meta['album'], $meta['artist']);
                } elseif (!empty($meta['album'])) {
                    /* translators: 1: audio track title, 2: album title */
                    $content .= sprintf(__('"%1$s" from %2$s.', 'add-from-server'), $title, $meta['album']);
                } elseif (!empty($meta['artist'])) {
                    /* translators: 1: audio track title, 2: artist name */
                    $content .= sprintf(__('"%1$s" by %2$s.', 'add-from-server'), $title, $meta['artist']);
                } else {
                    $content .= sprintf(__('"%s".', 'add-from-server'), $title);
                }
            } elseif (!empty($meta['album'])) {

                if (!empty($meta['artist'])) {
                    /* translators: 1: audio album title, 2: artist name */
                    $content .= sprintf(__('%1$s by %2$s.', 'add-from-server'), $meta['album'], $meta['artist']);
                } else {
                    $content .= $meta['album'] . '.';
                }
            } elseif (!empty($meta['artist'])) {

                $content .= $meta['artist'] . '.';
            }

            if (!empty($meta['year']))
                $content .= ' ' . sprintf(__('Released: %d.'), $meta['year']);

            if (!empty($meta['track_number'])) {
                $track_number = explode('/', $meta['track_number']);
                if (isset($track_number[1]))
                    $content .= ' ' . sprintf(__('Track %1$s of %2$s.', 'add-from-server'), number_format_i18n($track_number[0]), number_format_i18n($track_number[1]));
                else
                    $content .= ' ' . sprintf(__('Track %1$s.', 'add-from-server'), number_format_i18n($track_number[0]));
            }

            if (!empty($meta['genre']))
                $content .= ' ' . sprintf(__('Genre: %s.', 'add-from-server'), $meta['genre']);

            // Use image exif/iptc data for title and caption defaults if possible.
        } elseif (0 === strpos($type, 'image/') && $image_meta = @wp_read_image_metadata($new_file)) {
            if (trim($image_meta['title']) && !is_numeric(sanitize_title($image_meta['title']))) {
                $title = $image_meta['title'];
            }

            if (trim($image_meta['caption'])) {
                $excerpt = $image_meta['caption'];
            }
        }

        if ($time) {
            $post_date_gmt = $time;
            $post_date = $time;
        } else {
            $post_date = current_time('mysql');
            $post_date_gmt = current_time('mysql', 1);
        }

        // Construct the attachment array
        $attachment = array(
            'post_mime_type' => $type,
            'guid' => $url,
            'post_parent' => $post_id,
            'post_title' => $title,
            'post_name' => $title,
            'post_content' => $content,
            'post_excerpt' => $excerpt,
            'post_date' => $post_date,
            'post_date_gmt' => $post_date_gmt
        );

        $attachment = apply_filters('afs-import_details', $attachment, $file, $post_id, $import_date);

        // WP < 4.4 Compat: ucfirt
        $new_file = str_replace(ucfirst(wp_normalize_path($uploads['basedir'])), $uploads['basedir'], $new_file);

        // Save the data
        $id = wp_insert_attachment($attachment, $new_file, $post_id);
        if (!is_wp_error($id)) {
            $data = wp_generate_attachment_metadata($id, $new_file);
            wp_update_attachment_metadata($id, $data);
            if (isset($data['file'])) {
                $filename = $data['file'];
            }
        }
        // update_post_meta( $id, '_wp_attached_file', $uploads['subdir'] . '/' . $filename );

        return basename($filename);
    }
}

if (!function_exists('zhuige_xcx_import_image_attachment_meta')) {
    //把图片添加到媒体库
    function zhuige_xcx_import_image_attachment_meta($file, $post_id = 0, $import_date = 'current')
    {
        set_time_limit(0);

        // Initially, Base it on the -current- time.
        $time = current_time('mysql', 1);
        // Next, If it's post to base the upload off:
        if ('post' == $import_date && $post_id > 0) {
            $post = get_post($post_id);
            if ($post && substr($post->post_date_gmt, 0, 4) > 0) {
                $time = $post->post_date_gmt;
            }
        } elseif ('file' == $import_date) {
            $time = gmdate('Y-m-d H:i:s', @filemtime($file));
        }

        // A writable uploads dir will pass this test. Again, there's no point overriding this one.
        if (!(($uploads = wp_upload_dir($time)) && false === $uploads['error'])) {
            return new WP_Error('upload_error', $uploads['error']);
        }

        $wp_filetype = wp_check_filetype($file, null);

        extract($wp_filetype);

        if ((!$type || !$ext) && !current_user_can('unfiltered_upload')) {
            return new WP_Error('wrong_file_type', __('Sorry, this file type is not permitted for security reasons.', 'add-from-server'));
        }

        // Is the file allready in the uploads folder?
        // WP < 4.4 Compat: ucfirt
        if (preg_match('|^' . preg_quote(ucfirst(wp_normalize_path($uploads['basedir'])), '|') . '(.*)$|i', $file, $mat)) {

            $filename = basename($file);
            $new_file = $file;

            $url = $uploads['baseurl'] . $mat[1];

            $attachment = get_posts(array('post_type' => 'attachment', 'meta_key' => '_wp_attached_file', 'meta_value' => ltrim($mat[1], '/')));
            if (!empty($attachment)) {
                return new WP_Error('file_exists', __('Sorry, That file already exists in the WordPress media library.', 'add-from-server'));
            }

            // Ok, Its in the uploads folder, But NOT in WordPress's media library.
            if ('file' == $import_date) {
                $time = @filemtime($file);
                if (preg_match("|(\d+)/(\d+)|", $mat[1], $datemat)) { // So lets set the date of the import to the date folder its in, IF its in a date folder.
                    $hour = $min = $sec = 0;
                    $day = 1;
                    $year = $datemat[1];
                    $month = $datemat[2];

                    // If the files datetime is set, and it's in the same region of upload directory, set the minute details to that too, else, override it.
                    if ($time && wp_date('Y-m', $time) == "$year-$month") {
                        list($hour, $min, $sec, $day) = explode(';', wp_date('H;i;s;j', $time));
                    }

                    $time = mktime($hour, $min, $sec, $month, $day, $year);
                }
                $time = gmdate('Y-m-d H:i:s', $time);

                // A new time has been found! Get the new uploads folder:
                // A writable uploads dir will pass this test. Again, there's no point overriding this one.
                if (!(($uploads = wp_upload_dir($time)) && false === $uploads['error'])) {
                    return new WP_Error('upload_error', $uploads['error']);
                }
                $url = $uploads['baseurl'] . $mat[1];
            }
        } else {
            $filename = wp_unique_filename($uploads['path'], basename($file));

            // copy the file to the uploads dir
            $new_file = $uploads['path'] . '/' . $filename;
            if (false === @copy($file, $new_file))
                return new WP_Error('upload_error', sprintf(__('The selected file could not be copied to %s.', 'add-from-server'), $uploads['path']));

            // Set correct file permissions
            $stat = stat(dirname($new_file));
            $perms = $stat['mode'] & 0000666;
            @chmod($new_file, $perms);
            // Compute the URL
            $url = $uploads['url'] . '/' . $filename;

            if ('file' == $import_date) {
                $time = gmdate('Y-m-d H:i:s', @filemtime($file));
            }
        }

        // Apply upload filters
        $return = apply_filters('wp_handle_upload', array('file' => $new_file, 'url' => $url, 'type' => $type), 'upload');
        $new_file = $return['file'];
        $url = $return['url'];
        $type = $return['type'];

        $title = preg_replace('!\.[^.]+$!', '', basename($file));
        $content = $excerpt = '';

        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        if (preg_match('#^audio#', $type)) {
            $meta = wp_read_audio_metadata($new_file);

            if (!empty($meta['title'])) {
                $title = $meta['title'];
            }

            if (!empty($title)) {

                if (!empty($meta['album']) && !empty($meta['artist'])) {
                    /* translators: 1: audio track title, 2: album title, 3: artist name */
                    $content .= sprintf(__('"%1$s" from %2$s by %3$s.', 'add-from-server'), $title, $meta['album'], $meta['artist']);
                } elseif (!empty($meta['album'])) {
                    /* translators: 1: audio track title, 2: album title */
                    $content .= sprintf(__('"%1$s" from %2$s.', 'add-from-server'), $title, $meta['album']);
                } elseif (!empty($meta['artist'])) {
                    /* translators: 1: audio track title, 2: artist name */
                    $content .= sprintf(__('"%1$s" by %2$s.', 'add-from-server'), $title, $meta['artist']);
                } else {
                    $content .= sprintf(__('"%s".', 'add-from-server'), $title);
                }
            } elseif (!empty($meta['album'])) {

                if (!empty($meta['artist'])) {
                    /* translators: 1: audio album title, 2: artist name */
                    $content .= sprintf(__('%1$s by %2$s.', 'add-from-server'), $meta['album'], $meta['artist']);
                } else {
                    $content .= $meta['album'] . '.';
                }
            } elseif (!empty($meta['artist'])) {

                $content .= $meta['artist'] . '.';
            }

            if (!empty($meta['year']))
                $content .= ' ' . sprintf(__('Released: %d.'), $meta['year']);

            if (!empty($meta['track_number'])) {
                $track_number = explode('/', $meta['track_number']);
                if (isset($track_number[1]))
                    $content .= ' ' . sprintf(__('Track %1$s of %2$s.', 'add-from-server'), number_format_i18n($track_number[0]), number_format_i18n($track_number[1]));
                else
                    $content .= ' ' . sprintf(__('Track %1$s.', 'add-from-server'), number_format_i18n($track_number[0]));
            }

            if (!empty($meta['genre']))
                $content .= ' ' . sprintf(__('Genre: %s.', 'add-from-server'), $meta['genre']);

            // Use image exif/iptc data for title and caption defaults if possible.
        } elseif (0 === strpos($type, 'image/') && $image_meta = @wp_read_image_metadata($new_file)) {
            if (trim($image_meta['title']) && !is_numeric(sanitize_title($image_meta['title']))) {
                $title = $image_meta['title'];
            }

            if (trim($image_meta['caption'])) {
                $excerpt = $image_meta['caption'];
            }
        }

        if ($time) {
            $post_date_gmt = $time;
            $post_date = $time;
        } else {
            $post_date = current_time('mysql');
            $post_date_gmt = current_time('mysql', 1);
        }

        // Construct the attachment array
        $attachment = array(
            'post_mime_type' => $type,
            'guid' => $url,
            'post_parent' => $post_id,
            'post_title' => $title,
            'post_name' => $title,
            'post_content' => $content,
            'post_excerpt' => $excerpt,
            'post_date' => $post_date,
            'post_date_gmt' => $post_date_gmt
        );

        $attachment = apply_filters('afs-import_details', $attachment, $file, $post_id, $import_date);

        // WP < 4.4 Compat: ucfirt
        $new_file = str_replace(ucfirst(wp_normalize_path($uploads['basedir'])), $uploads['basedir'], $new_file);

        // Save the data
        $id = wp_insert_attachment($attachment, $new_file, $post_id);
        if (is_wp_error($id)) {
            return $id;
        }

        $data = wp_generate_attachment_metadata($id, $new_file);
        wp_update_attachment_metadata($id, $data);

        $data['id'] = $id;

        return $data;
    }
}

/**
 * 美化时间格式
 */
if (!function_exists('zhuige_xcx_time_beautify')) {
    function zhuige_xcx_time_beautify($time)
    {
        $origin_time = mysql2date('G', $time);
        return zhuige_xcx_time_stamp_beautify($origin_time);
    }
}

/**
 * 美化时间戳
 */
if (!function_exists('zhuige_xcx_time_stamp_beautify')) {
    function zhuige_xcx_time_stamp_beautify($time)
    {
        $dur = time() - $time;
        if ($dur < 60) {
            return '刚刚';
        } else if ($dur < 3600) {
            return floor($dur / 60) . '分钟前';
        } else if ($dur < 86400) {
            return floor($dur / 3600) . '小时前';
        } else if ($dur < 604800) { //7天内
            return floor($dur / 86400) . '天前';
        } else {
            return wp_date("Y-m-d", $time);
        }
    }
}

/**
 * 字符串转时间
 */
if (!function_exists('zhuige_xcx_strtotime')) {
    function zhuige_xcx_strtotime($string)
    {
        return date_create($string, wp_timezone())->getTimestamp();
    }
}

/**
 * 获取缩略图
 */
if (!function_exists('zhuige_xcx_get_post_thumbnails')) {
    function zhuige_xcx_get_post_thumbnails($post, $default = true)
    {
        $thumbnails = [];
        if (has_post_thumbnail($post->ID)) {
            $thumbnail_id = get_post_thumbnail_id($post->ID);
            if ($thumbnail_id) {
                $attachment = wp_get_attachment_image_src($thumbnail_id, 'full');
                if ($attachment) {
                    $thumbnails[] = $attachment[0];
                }
            }
        }

        // $post = get_post($post_id);
        $post_content = $post->post_content;
        preg_match_all('|<img.*?src=[\'"](.*?)[\'"].*?>|i', do_shortcode($post_content), $matches);
        if ($matches && isset($matches[1])) {
            if (isset($matches[1][0])) {
                $thumbnails[] = $matches[1][0];
            }

            if (isset($matches[1][1])) {
                $thumbnails[] = $matches[1][1];
            }

            if (isset($matches[1][2]) && count($thumbnails) < 3) {
                $thumbnails[] = $matches[1][2];
            }
        }

        if (!empty($thumbnails)) {
            return $thumbnails;
        }

        if ($default) {
            $cms_list_thumb = ZhuiGe_Xcx::option_value('cms_list_thumb');
            $thumbnails[] = ZhuiGe_Xcx::option_image_url($cms_list_thumb, 'placeholder.jpg');
        }

        return $thumbnails;
    }
}

/**
 * 获取一个缩略图
 */
if (!function_exists('zhuige_xcx_get_one_post_thumbnail')) {
    function zhuige_xcx_get_one_post_thumbnail($post, $default = true)
    {
        if (has_post_thumbnail($post->ID)) {
            $thumbnail_id = get_post_thumbnail_id($post->ID);
            if ($thumbnail_id) {
                $attachment = wp_get_attachment_image_src($thumbnail_id, 'full');
                if ($attachment) {
                    return $attachment[0];
                }
            }
        }

        // $post = get_post($post_id);
        $post_content = $post->post_content;
        preg_match_all('|<img.*?src=[\'"](.*?)[\'"].*?>|i', do_shortcode($post_content), $matches);
        if ($matches && isset($matches[1]) && isset($matches[1][0])) {
            return $matches[1][0];
        }

        if ($default) {
            $cms_list_thumb = ZhuiGe_Xcx::option_value('cms_list_thumb');
            return ZhuiGe_Xcx::option_image_url($cms_list_thumb, 'placeholder.jpg');
        }

        return '';
    }
}

/**
 * 文章摘要
 */
if (!function_exists('zhuige_xcx_get_post_excerpt')) {
    function zhuige_xcx_get_post_excerpt($post)
    {
        $content = apply_filters('the_content', $post->post_content);
        if (mb_strlen($content) < 100) {
            return wp_strip_all_tags($content);
        }

        if ($post->post_excerpt) {
            return wp_strip_all_tags(wp_trim_words($post->post_excerpt, 50, '...'));
        } else {
            return wp_strip_all_tags(wp_trim_words($content, 50, '...'));
        }
    }
}

/**
 * 统计用户的文章数量
 */
if (!function_exists('zhuige_xcx_user_post_count')) {
    function zhuige_xcx_user_post_count($user_id)
    {
        global $wpdb;
        $table_posts = $wpdb->prefix . 'posts';
        $post_count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT count(id) FROM `$table_posts` WHERE `post_author`=%d AND `post_status`='publish' AND `post_type`='zhuige_bbs_topic'",
                $user_id
            )
        );

        return $post_count;
    }
}

/**
 * 统计用户粉丝的数量
 */
if (!function_exists('zhuige_xcx_user_fans_count')) {
    function zhuige_xcx_user_fans_count($user_id)
    {
        global $wpdb;
        $table_follow_user = $wpdb->prefix . 'zhuige_xcx_follow_user';
        $fans_count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT count(id) FROM `$table_follow_user` WHERE follow_user_id=%d",
                $user_id
            )
        );

        return $fans_count;
    }
}

/**
 * 统计用户关注的数量
 */
if (!function_exists('zhuige_xcx_user_follow_count')) {
    function zhuige_xcx_user_follow_count($user_id)
    {
        global $wpdb;
        $table_follow_user = $wpdb->prefix . 'zhuige_xcx_follow_user';
        $follow_count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT count(id) FROM `$table_follow_user` WHERE user_id=%d",
                $user_id
            )
        );

        return $follow_count;
    }
}

//获取最后的评论
if (!function_exists('zhuige_xcx_get_comments')) {
    function zhuige_xcx_get_comments($post_id, $offset = null, $count = ZhuiGe_Xcx::POSTS_PER_PAGE)
    {
        global $wpdb;
        $table_comments = $wpdb->prefix . 'comments';
        $fields = 'comment_ID, comment_author, comment_date, comment_date_gmt, comment_content, comment_approved, comment_parent, user_id';
        $where = "comment_post_ID=$post_id";

        $my_user_id = get_current_user_id();
        if ($my_user_id) {
            $where = $where . " AND (comment_approved=1 OR user_id=$my_user_id)";
        } else {
            $where = $where . " AND comment_approved=1";
        }

        $limit = '';
        if ($offset !== null) {
            $limit = "LIMIT $offset, $count";
        }

        $result = $wpdb->get_results(
            // $wpdb->prepare(
            "SELECT $fields FROM `$table_comments` WHERE $where ORDER BY comment_ID DESC $limit"
            // )
        );
        $comments = [];
        foreach ($result as $comment) {
            $nickname = get_user_meta($comment->user_id, 'nickname', true);
            if (!$nickname) {
                $nickname = $comment->comment_author;
            }

            $item = [
                'id' => $comment->comment_ID,
                'user' => [
                    'user_id' => $comment->user_id,
                    'nickname' => $nickname,
                    'avatar' => ZhuiGe_Xcx::user_avatar($comment->user_id),
                    'is_me' => ($comment->user_id == $my_user_id) ? 1 : 0,
                    // 'certify' => $this->is_certify($comment->user_id),
                ],
                'content' => $comment->comment_content,
                'approved' => $comment->comment_approved,
                'time' => zhuige_xcx_time_beautify($comment->comment_date_gmt),
            ];

            if ($comment->comment_parent != 0) {
                $parent = get_comment($comment->comment_parent);
                $item['reply'] = [
                    'id' => $parent->user_id,
                    'nickname' => get_user_meta($parent->user_id, 'nickname', true)
                ];
            }

            $comments[] = $item;
        }

        return $comments;
    }
}

//获取最后的评论
if (!function_exists('zhuige_xcx_get_comment_tree')) {
    function zhuige_xcx_get_comment_tree($post_id, $offset = null, $count = ZhuiGe_Xcx::POSTS_PER_PAGE, $parent = 0)
    {
        global $wpdb;
        $table_comments = $wpdb->prefix . 'comments';
        $fields = 'comment_ID, comment_author, comment_date, comment_date_gmt, comment_content, comment_approved, comment_parent, user_id';
        $where = "comment_post_ID=$post_id AND comment_parent=$parent";

        $my_user_id = get_current_user_id();
        // if ($my_user_id) {
        //     $where = $where . " AND (comment_approved=1 OR user_id=$my_user_id)";
        // } else {
        //     $where = $where . " AND comment_approved=1";
        // }
        $where = $where . " AND comment_approved=1";

        $limit = '';
        if ($offset !== null) {
            $limit = "LIMIT $offset, $count";
        }

        $result = $wpdb->get_results(
            // $wpdb->prepare(
            "SELECT $fields FROM `$table_comments` WHERE $where ORDER BY comment_ID DESC $limit"
            // )
        );
        $comments = [];
        foreach ($result as $comment) {
            $nickname = get_user_meta($comment->user_id, 'nickname', true);
            if (!$nickname) {
                $nickname = $comment->comment_author;
            }

            $item = [
                'id' => $comment->comment_ID,
                'content' => $comment->comment_content,
                'approved' => $comment->comment_approved,
                'time' => zhuige_xcx_time_beautify($comment->comment_date_gmt),
            ];

            $user = [
                'user_id' => $comment->user_id,
                'nickname' => $nickname,
                'avatar' => ZhuiGe_Xcx::user_avatar($comment->user_id),
                'is_me' => ($comment->user_id == $my_user_id) ? 1 : 0,
            ];

            if (function_exists('zhuige_xcx_certify_is_certify')) {
                $user['certify'] = zhuige_xcx_certify_is_certify($comment->user_id);
            }

            if (function_exists('zhuige_xcx_vip_is_vip')) {
                $user['vip'] = zhuige_xcx_vip_is_vip($comment->user_id);
            }

            $item['user'] = $user;

            $reply_user_id = (int)(get_comment_meta($comment->comment_ID, 'zhuige_xcx_reply_user_id', true));
            if ($reply_user_id) {
                $item['reply'] = [
                    'id' => $reply_user_id,
                    'nickname' => get_user_meta($reply_user_id, 'nickname', true)
                ];
            }

            $item['replys'] = zhuige_xcx_get_comment_tree($post_id, $offset, $count, $comment->comment_ID);

            $comments[] = $item;
        }

        return $comments;
    }
}

/**
 * 下载文件到服务器
 */
if (!function_exists('zhuige_xcx_download_file')) {
    function zhuige_xcx_download_file($url, $path)
    {
        $newfname = $path;
        $file = fopen($url, 'rb');
        if ($file) {
            $newf = fopen($newfname, 'wb');
            if ($newf) {
                while (!feof($file)) {
                    fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
                }
            }
        }

        if ($file) {
            fclose($file);
        }

        if ($newf) {
            fclose($newf);
        }
    }
}

/**
 * 删除目录
 */
if (!function_exists('zhuige_xcx_delete_dir')) {
    function zhuige_xcx_delete_dir($path)
    {
        //如果是目录则继续
        if (is_dir($path)) {
            //扫描一个文件夹内的所有文件夹和文件并返回数组
            $p = scandir($path);
            //如果 $p 中有两个以上的元素则说明当前 $path 不为空
            if (count($p) > 2) {
                foreach ($p as $val) {
                    //排除目录中的.和..
                    if ($val != "." && $val != "..") {
                        //如果是目录则递归子目录，继续操作
                        if (is_dir($path . $val)) {
                            //子目录中操作删除文件夹和文件
                            zhuige_xcx_delete_dir($path . $val . '/');
                        } else {
                            //如果是文件直接删除
                            @unlink($path . $val);
                        }
                    }
                }
            }
        }

        //删除目录
        return rmdir($path);
    }
}

/**
 * 获取作者信息
 */
if (!function_exists('zhuige_xcx_author_info')) {
    function zhuige_xcx_author_info($user_id)
    {
        $author = [
            'user_id' => $user_id,
            'nickname' => get_user_meta($user_id, 'nickname', true),
            'avatar' => ZhuiGe_Xcx::user_avatar($user_id),
            'reward' => get_user_meta($user_id, 'zhuige_xcx_user_reward', true)
        ];
        if (function_exists('zhuige_xcx_certify_is_certify')) {
            $author['certify'] = zhuige_xcx_certify_is_certify($user_id);
        }
        if (function_exists('zhuige_xcx_vip_is_vip')) {
            $author['vip'] = zhuige_xcx_vip_is_vip($user_id);
        }

        return $author;
    }
}

foreach (ZhuiGe_Xcx_Addon::$funcs as $func) {
    $file_path = ZHUIGE_XCX_ADDONS_DIR . $func;
    if (file_exists($file_path)) {
        require_once($file_path);
    }
}
