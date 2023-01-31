<?php

/**
 * 追格小程序
 * 作者: 追格
 * 文档: https://www.zhuige.com/docs/zg.html
 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
 * github: https://github.com/zhuige-com/zhuige_xcx
 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
 */

if (!class_exists('ZhuiGe_User_Property')) {
	/**
	 * add field to user profiles
	 */
	class ZhuiGe_User_Property
	{
		private $user_id_being_edited;

		public function __construct()
		{
			add_action('show_user_profile', array($this, 'edit_user_profile'));
			add_action('edit_user_profile', array($this, 'edit_user_profile'));

			add_action('personal_options_update', array($this, 'edit_user_profile_update'));
			add_action('edit_user_profile_update', array($this, 'edit_user_profile_update'));
		}

		public function edit_user_profile($profileuser)
		{
?>
			<?php
			if (current_user_can('upload_files')) {
				wp_nonce_field('zhuige_property_nonce', '_zhuige_property_nonce', false);
			} else {
				echo '<div>无上传图片权限</div>';
			}
			?>

			<table class="form-table">
				<tr>
					<th><label for="zhuige-user-avatar">追格头像</label></th>
					<td style="width: 64px;" valign="top">
						<?php
						$user_avatar = get_user_meta($profileuser->ID, 'zhuige_xcx_user_avatar', true);
						if (empty($user_avatar)) {
							$user_avatar = ZHUIGE_XCX_BASE_URL . 'public/images/empty.jpg';
						}
						echo '<img src="' . $user_avatar . '" width="64" height="64" />';
						?>
					</td>
					<td>
						<input type="file" name="zhuige-user-avatar" id="zhuige-user-avatar" /><br />
						<?php
						if (empty($profileuser->zhuige_xcx_user_avatar))
							echo '<span class="description">上传图片，修改头像</span>';
						else
							echo '<input type="checkbox" name="zhuige-user-avatar-erase" value="1" />恢复默认头像<br />';
						?>
					</td>
				</tr>

				<tr>
					<th><label for="zhuige-user-weixin">微信二维码</label></th>
					<td style="width: 64px;" valign="top">
						<?php
						$user_weixin = get_user_meta($profileuser->ID, 'zhuige_xcx_user_weixin', true);
						if (empty($user_weixin)) {
							$user_weixin = ZHUIGE_XCX_BASE_URL . 'public/images/empty.jpg';
						}
						echo '<img src="' . $user_weixin . '" width="64" height="64" />';
						?>
					</td>
					<td>
						<input type="file" name="zhuige-user-weixin" id="zhuige-user-weixin" /><br />
						<?php
						if (empty($profileuser->zhuige_xcx_user_weixin))
							echo '<span class="description">上传图片，修改微信二维码</span>';
						else
							echo '<input type="checkbox" name="zhuige-user-weixin-erase" value="1" />删除微信图片<br />';
						?>
					</td>
				</tr>

				<tr>
					<th><label for="zhuige-user-reward">赞赏码</label></th>
					<td style="width: 64px;" valign="top">
						<?php
						$user_reward = get_user_meta($profileuser->ID, 'zhuige_xcx_user_reward', true);
						if (empty($user_reward)) {
							$user_reward = ZHUIGE_XCX_BASE_URL . 'public/images/empty.jpg';
						}
						echo '<img src="' . $user_reward . '" width="64" height="64" />';
						?>
					</td>
					<td>
						<input type="file" name="zhuige-user-reward" id="zhuige-user-reward" /><br />
						<?php
						if (empty($profileuser->zhuige_xcx_user_reward))
							echo '<span class="description">上传图片，修改赞赏码</span>';
						else
							echo '<input type="checkbox" name="zhuige-user-reward-erase" value="1" />删除赞赏码<br />';
						?>
					</td>
				</tr>

				<tr>
					<th><label for="zhuige-user-cover">主页封面</label></th>
					<td style="width: 64px;" valign="top">
						<?php
						$user_cover = get_user_meta($profileuser->ID, 'zhuige_xcx_user_cover', true);
						if (empty($user_cover)) {
							$user_cover = ZHUIGE_XCX_BASE_URL . 'public/images/empty.jpg';
						}
						echo '<img src="' . $user_cover . '" width="64" height="64" />';
						?>
					</td>
					<td>
						<input type="file" name="zhuige-user-cover" id="zhuige-user-cover" /><br />
						<?php
						if (empty($profileuser->zhuige_xcx_user_cover))
							echo '<span class="description">上传图片，修改主页封面</span>';
						else
							echo '<input type="checkbox" name="zhuige-user-cover-erase" value="1" />删除主页封面<br />';
						?>
					</td>
				</tr>

				<tr>
					<th><label for="zhuige-user-mobile">手机号</label></th>
					<td colspan="2">
						<input type="text" name="zhuige-user-mobile" id="zhuige-user-mobile" value="<?php echo get_user_meta($profileuser->ID, 'zhuige_xcx_user_mobile', true); ?>" class="regular-text" /><br />
						<span class="description">请输入手机号</span>
					</td>
				</tr>
				<tr>
					<th><label for="zhuige-user-sign">追格签名</label></th>
					<td colspan="2">
						<input type="text" name="zhuige-user-sign" id="zhuige-user-sign" value="<?php echo get_user_meta($profileuser->ID, 'zhuige_xcx_user_sign', true); ?>" class="regular-text" /><br />
						<span class="description">请输入追格签名</span>
					</td>
				</tr>
			</table>
			<script type="text/javascript">
				var form = document.getElementById('your-profile');
				form.encoding = 'multipart/form-data';
				form.setAttribute('enctype', 'multipart/form-data');
			</script>
<?php
		}

		public function edit_user_profile_update($user_id)
		{
			//security
			if (!isset($_POST['_zhuige_property_nonce']) || !wp_verify_nonce($_POST['_zhuige_property_nonce'], 'zhuige_property_nonce')) {
				return;
			}

			// front end (theme my profile etc) support
			if (!function_exists('wp_handle_upload')) {
				require_once(ABSPATH . 'wp-admin/includes/file.php');
			}

			$this->image_upload($user_id, 'zhuige-user-avatar', 'zhuige_xcx_user_avatar');
			$this->image_upload($user_id, 'zhuige-user-weixin', 'zhuige_xcx_user_weixin');
			$this->image_upload($user_id, 'zhuige-user-reward', 'zhuige_xcx_user_reward');
			$this->image_upload($user_id, 'zhuige-user-cover', 'zhuige_xcx_user_cover');

			update_user_meta($user_id, 'zhuige_xcx_user_mobile', $_POST['zhuige-user-mobile']);
			update_user_meta($user_id, 'zhuige_xcx_user_sign', $_POST['zhuige-user-sign']);
		}

		public function image_upload($user_id, $field, $meta_key)
		{
			if (!empty($_FILES[$field]['name'])) {
				// delete old images if successful
				$this->image_delete($user_id, $meta_key);

				$mimes = array(
					'jpg|jpeg|jpe' => 'image/jpeg',
					'gif' => 'image/gif',
					'png' => 'image/png',
					'bmp' => 'image/bmp',
					'tif|tiff' => 'image/tiff'
				);

				// need to be more secure since low privelege users can upload
				if (strstr($_FILES[$field]['name'], '.php'))
					wp_die('For security reasons, the extension ".php" cannot be in your file name.');

				// make user_id known to unique_filename_callback function
				$this->user_id_being_edited = $user_id;
				$image = wp_handle_upload($_FILES[$field], array(
					'mimes' => $mimes,
					'test_form' => false,
					'unique_filename_callback' => array($this, 'unique_filename_callback')
				));

				// handle failures
				if (empty($image['file'])) {
					// switch ($image['error']) {
					// 	case 'File type does not meet security guidelines. Try another.':
					// 		add_action('user_profile_update_errors', create_function('$a', '$a->add("image_error", "Please upload a valid image file for the image.");'));
					// 		break;
					// 	default:
					// 		add_action('user_profile_update_errors', create_function('$a', '$a->add("image_error", "There was an error uploading the image.");'));
					// }
					wp_die('图片上传错误');
					return;
				}

				$image_url = $image['url'];
				$dres = zhuige_xcx_import_image2attachment($image['file']);
				if (!is_wp_error($dres)) {
					$upload_dir = wp_upload_dir();
					$image_url = $upload_dir['url'] . '/' . $dres;
				}

				// save user information (overwriting old)
				update_user_meta($user_id, $meta_key, $image_url);
			} elseif (!empty($_POST[$field . '-erase'])) {
				$this->image_delete($user_id, $meta_key);
			}
		}

		public function image_delete($user_id, $meta_key)
		{
			$old_images = get_user_meta($user_id, $meta_key, true);
			$upload_path = wp_upload_dir();

			if (is_array($old_images)) {
				foreach ($old_images as $old_image) {
					$old_image_path = str_replace($upload_path['baseurl'], $upload_path['basedir'], $old_image);
					@unlink($old_image_path);
				}
			}

			delete_user_meta($user_id, $meta_key);
		}

		public function unique_filename_callback($dir, $name, $ext)
		{
			// $user = get_user_by('id', (int) $this->user_id_being_edited);
			// $name = $base_name = sanitize_file_name($user->display_name . $this->user_id_being_edited);
			$name = $base_name = $this->user_id_being_edited;
			$number = 1;

			while (file_exists($dir . "/$name$ext")) {
				$name = $base_name . '_' . $number;
				$number++;
			}

			return $name . $ext;
		}
	}
}

if (!isset($zhuige_user_property)) {
	$zhuige_user_property = new ZhuiGe_User_Property;
}
