<template>
	<view class="content">
		<view class="zhuige-verify-box">
			<!-- 用户信息 -->
			<view class="zhuige-verify-info">
				<button open-type="chooseAvatar" @chooseavatar="onChooseAvatar" class="zhuige-verify-line">
					<view>
						<image mode="aspectFill" :src="avatar"></image>
					</view>
					<view class="zhuige-verify-text">点击更换头像</view>
				</button>
				<view class="zhuige-verify-line">
					<view class="zhuige-verify-text">
						<template v-if="tip">
							完善头像昵称后才能{{tip}}
						</template>
						<template v-else>
							你这么帅，不换个昵称吗
						</template>
					</view>
					
					<view>
						<input type="nickname" v-model="nickname" @blur="onNicknameBlur" placeholder="请输入昵称" />
					</view>
				</view>
			</view>

			<!-- 底部大按钮 -->
			<view class="zhuige-verify-button">
				<view @click="clickSubmit">提交</view>
				<view @click="openLink('/pages/user/info/info')">完善详细资料</view>
				<!-- <view @click="clickBack">跳过</view> -->
			</view>
		</view>
	</view>

</template>

<script>
	/*
	 * 追格小程序
	 * 作者: 追格
	 * 文档: https://www.zhuige.com/docs/zg.html
	 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
	 * github: https://github.com/zhuige-com/zhuige_xcx
	 * Copyright © 2022-2024 www.zhuige.com All rights reserved.
	 */

	import Util from '@/utils/util';
	import Auth from '@/utils/auth';
	import Alert from '@/utils/alert';
	import Api from '@/utils/api';
	import Rest from '@/utils/rest';

	export default {
		components: {
			
		},
		
		data() {
			return {
				nickname: '',
				avatar: '/static/avatar.jpg',
				
				//登录后 重新加载
				loginReload: false,
				
				tip: ''
			}
		},

		onLoad(options) {
			Util.addShareScore(options.source);
			
			if (options.tip) {
				this.tip = options.tip;
			}
			this.loadData();
			uni.$on('zhuige_event_user_login', this.onSetReload);
		},
		
		onUnload() {
			uni.$off('zhuige_event_user_login', this.onSetReload);
		},
		
		onShow() {
			if (this.loginReload) {
				this.loginReload = false;
		
				this.loadData();
			}
		},

		methods: {
			/**
			 * 需要重新加载事件
			 */
			onSetReload(data) {
				this.loginReload = true;
			},
			
			/**
			 * 点击打开链接
			 */
			openLink(link) {
				Util.openLink(link);
			},

			/**
			 * 选择头像
			 */
			onChooseAvatar(e) {
				Rest.upload(Api.URL('other', 'upload'), e.detail.avatarUrl).then(oo => {
					this.avatar = oo.data.src;
				}, err => {
					Alert.error(err);
				});
			},
			
			/**
			 * 设置昵称
			 */
			onNicknameBlur(e) {
				this.nickname = e.detail.value;
			},

			/**
			 * 点击提交
			 */
			clickSubmit() {
				Rest.post(Api.URL('user', 'init_info'), {
					nickname: this.nickname,
					avatar: this.avatar
				}).then(res => {
					if (res.code == 0) {
						Alert.success(res.message);
						setTimeout(() => {
							Util.navigateBack();
						}, 1500)
					} else {
						Alert.error(res.message);
					}
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 点击返回
			 */
			clickBack() {
				Util.navigateBack()
			},
			
			/**
			 * 加载数据
			 */
			loadData() {
				Rest.post(Api.URL('user', 'get_init_info'), {}).then(res => {
					if (res.code == 0) {
						this.nickname = res.data.nickname;
						this.avatar = res.data.avatar;
						
						if (this.tip) {
							Alert.toast('完善头像昵称后才能' + this.tip)
						}
					} else {
						Alert.error(res.message);
					}
				}, err => {
					console.log(err)
				});
			}
		}
	}
</script>

<style>
	.content {
		position: fixed;
		height: 100%;
		width: 100%;
		overflow-y: scroll;
	}

	.zhuige-info-form-box {
		padding: 20rpx;
		margin-bottom: 160rpx;
	}

	.zhuige-verify-box {
		padding: 80rpx 120rpx;
		text-align: center;
	}

	.zhuige-verify-info {
		padding-bottom: 80rpx;
	}

	.zhuige-verify-info button,
	.zhuige-verify-info button::after {
		border: none;
		background: none;
	}

	.zhuige-verify-line {
		padding: 30rpx 0;
		border-bottom: 1rpx solid #DDDDDD;
	}

	.zhuige-verify-line image {
		height: 180rpx;
		width: 180rpx;
		border-radius: 50%;
		margin-bottom: 10rpx;
	}

	.zhuige-verify-text {
		font-size: 30rpx;
		font-weight: 500;
	}

	.zhuige-verify-line input {
		font-size: 30rpx;
		height: 72rpx;
		line-height: 72rpx;
	}

	.zhuige-verify-button {
		display: flex;
		align-items: center;
		flex-wrap: wrap;
		justify-content: center;
	}

	.zhuige-verify-button view {
		width: 360rpx;
		text-align: center;
		height: 96rpx;
		line-height: 96rpx;
		border-radius: 96rpx;
		font-size: 28rpx;
		font-weight: 400;
		color: #999999;
		margin-bottom: 40rpx;
	}

	.zhuige-verify-button view:nth-child(1) {
		color: #FFFFFF;
		background: #010101;
		font-size: 32rpx;
	}
</style>