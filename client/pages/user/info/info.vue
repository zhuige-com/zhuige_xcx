<template>
	<view class="content">
		<view class="zhuige-info-form-box">
			<!-- 顶部大图及头像 -->
			<view class="zhuige-info-header">
				<!-- 顶部大图 -->
				<image mode="aspectFill" :src="cover"></image>
				<!-- 头像 -->
				<image @click="clickAvatar" mode="aspectFill" :src="avatar"></image>
				<!-- 控制入口 -->
				<view @click="clickCover" class="avatar-opt">
					<view>
						<uni-icons type="camera" size="18"></uni-icons>
					</view>
					<view>修改背景图</view>
				</view>
			</view>
			<!-- 认证表单 -->
			<view class="zhuige-info-form">
				<view v-if="certify || vip" class="zhuige-info-line form-title">
					<template v-if="certify && certify.status==1">
						<view>认证</view>
						<view>
							<image mode="aspectFill" :src="certify.icon"></image>
							<text>{{certify.name}}</text>
						</view>
					</template>

					<view v-if="vip && vip.status==1">
						<view class="zhugie-vip-style">
							<image mode="aspectFill" :src="vip.icon"></image>
							<text>有效期至：{{vip.expire}}</text>
						</view>
					</view>
				</view>
				<view class="zhuige-info-line">
					<view>昵称</view>
					<view>
						<input type="text" v-model="nickname" placeholder="请输入昵称" />
					</view>
				</view>
				<view class="zhuige-info-line">
					<view>电话</view>
					<view v-if="mobile" @click="clickMobile">
						{{mobile}}
					</view>
					<view v-else @click="openLink('/pages/user/login/login?type=mobile')">
						点击绑定手机号
					</view>
				</view>
				<view class="zhuige-info-line">
					<view>签名/介绍</view>
					<view>
						<input type="text" v-model="sign" placeholder="一句话介绍一下自己吧" />
					</view>
				</view>
				<view class="zhuige-info-line">
					<view>微信二维码</view>
					<view>
						<view @click="clickWeiXin" class="zhugie-info-image-upload">
							<template v-if="weixin">
								<uni-icons class="clear" type="clear" size="24"></uni-icons>
								<image mode="aspectFill" :src="weixin"></image>
							</template>
							<uni-icons class="plus" v-else type="plusempty" color="#999999" size="40"></uni-icons>
						</view>
					</view>
				</view>
				<view class="zhuige-info-line">
					<view>鼓励码（用户用户赞赏）</view>
					<view>
						<view @click="clickReward" class="zhugie-info-image-upload">
							<template v-if="reward">
								<uni-icons class="clear" type="clear" size="24"></uni-icons>
								<image mode="aspectFill" :src="reward"></image>
							</template>
							<uni-icons class="plus" v-else type="plusempty" color="#999999" size="40"></uni-icons>
						</view>
					</view>
				</view>
			</view>
		</view>

		<!-- 底部大按钮 -->
		<view @click="clickSubmit()" class="zhuige-base-button">
			<view>提交</view>
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
	 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
	 */

	import Util from '@/utils/util';
	import Auth from '@/utils/auth';
	import Alert from '@/utils/alert';
	import Api from '@/utils/api';
	import Rest from '@/utils/rest';

	export default {
		data() {
			return {
				cover: '',
				avatar: '',
				nickname: '',
				mobile: '',
				sign: '',
				weixin: '',
				reward: '',
				certify: undefined,
				vip: undefined,
			}
		},

		onLoad(options) {
			Util.addShareScore(options.source);

			if (!Auth.getUser()) {
				uni.reLaunch({
					url: '/pages/tabs/index/index'
				})
				return;
			}

			this.loadData();

			uni.$on('zhuige_event_user_mobile', this.onSetMobile);
		},

		onUnload() {
			uni.$off('zhuige_event_user_mobile', this.onSetMobile);
		},

		onShareAppMessage() {
			return {
				title: getApp().globalData.appName,
				path: Util.addShareSource('pages/tabs/index/index')
			};
		},

		methods: {
			// ------ event --------
			/**
			 * 设置手机号事件
			 */
			onSetMobile(data) {
				this.mobile = data.mobile;
			},
			// ------ event --------

			/**
			 * 点击打开链接
			 */
			openLink(link) {
				Util.openLink(link);
			},

			/**
			 * 点击提交
			 */
			clickSubmit() {
				let params = {
					cover: this.cover,
					avatar: this.avatar,
					nickname: this.nickname,
					sign: this.sign,
					weixin: this.weixin,
					reward: this.reward
				}
				Rest.post(Api.URL('user', 'set_info'), params).then(res => {
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
			 * 点击设置封面
			 */
			clickCover() {
				uni.chooseImage({
					count: 1,
					sizeType: ['compressed'],
					success: (res) => {
						let path = res.tempFilePaths[0];
						Rest.upload(Api.URL('other', 'upload'), path).then(oo => {
							this.cover = oo.data.src;
						}, err => {
							Alert.error(err);
						});
					}
				});
			},

			/**
			 * 点击设置头像
			 */
			clickAvatar() {
				uni.chooseImage({
					count: 1,
					sizeType: ['compressed'],
					success: (res) => {
						let path = res.tempFilePaths[0];
						Rest.upload(Api.URL('other', 'upload'), path).then(oo => {
							this.avatar = oo.data.src;
						}, err => {
							Alert.error(err);
						});
					}
				});
			},

			/**
			 * 点击重新绑定手机号
			 */
			clickMobile() {
				uni.showModal({
					title: '提示',
					content: '要重新绑定手机号吗？',
					success: (res) => {
						if (res.confirm) {
							Util.openLink('/pages/user/login/login?type=mobile')
						}
					}
				});
			},

			/**
			 * 点击设置微信
			 */
			clickWeiXin() {
				uni.chooseImage({
					count: 1,
					sizeType: ['compressed'],
					success: (res) => {
						let path = res.tempFilePaths[0];
						Rest.upload(Api.URL('other', 'upload'), path).then(oo => {
							this.weixin = oo.data.src;
						}, err => {
							Alert.error(err);
						});
					}
				});
			},

			/**
			 * 点击设置赞赏码
			 */
			clickReward() {
				uni.chooseImage({
					count: 1,
					sizeType: ['compressed'],
					success: (res) => {
						let path = res.tempFilePaths[0];
						Rest.upload(Api.URL('other', 'upload'), path).then(oo => {
							this.reward = oo.data.src;
						}, err => {
							Alert.error(err);
						});
					}
				});
			},

			/**
			 * 加载数据
			 */
			loadData() {
				Rest.post(Api.URL('user', 'get_info')).then(res => {
					this.cover = res.data.cover;
					this.avatar = res.data.avatar;
					this.nickname = res.data.nickname;
					this.mobile = res.data.mobile;
					this.sign = res.data.sign;
					this.weixin = res.data.weixin;
					this.reward = res.data.reward;

					if (res.data.certify) {
						this.certify = res.data.certify;
					}

					if (res.data.vip) {
						this.vip = res.data.vip;
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

	.zhuige-info-header {
		height: 480rpx;
		width: 100%;
		position: relative;
	}

	.zhuige-info-header image:nth-child(1) {
		height: 100%;
		width: 100%;
		border-radius: 12rpx 12rpx 0 0;
	}

	.zhuige-info-header image:nth-child(2) {
		position: absolute;
		z-index: 3;
		right: 30rpx;
		bottom: -80rpx;
		height: 160rpx;
		width: 160rpx;
		border-radius: 50%;
		border: 1rpx solid #EEEEEE;
	}

	.zhuige-info-header .avatar-opt {
		position: absolute;
		z-index: 3;
		left: 30rpx;
		bottom: 30rpx;
		display: flex;
		align-items: center;
	}

	.zhuige-info-header .avatar-opt view:nth-child(1) {
		height: 60rpx;
		width: 60rpx;
		line-height: 60rpx;
		text-align: center;
		border-radius: 50%;
		background: rgba(255, 255, 255, .6);
	}

	.zhuige-info-header .avatar-opt view:nth-child(2) {
		padding-left: 16rpx;
		font-size: 26rpx;
		font-weight: 300;
		color: rgba(255, 255, 255, .6);
	}

	.zhuige-info-form {
		padding: 0 30rpx;
		background: #FFFFFF;
		border-radius: 0 0 12rpx 12rpx;
	}

	.zhuige-info-line {
		padding: 30rpx 0;
		border-top: 1rpx solid #EEEEEE;
	}

	.zhuige-info-line>view {
		font-size: 28rpx;
		font-weight: 300;
	}

	.zhuige-info-line>view:nth-child(1) {
		font-size: 30rpx;
		font-weight: 600;
		margin-bottom: 20rpx;
	}

	.zhuige-info-line>view:nth-child(2) input {
		width: 100%;
		font-size: 28rpx;
		font-weight: 300;
	}

	.form-title>view:nth-child(2),
	.form-title>view:nth-child(3) {
		display: flex;
		align-items: center;
		color: #999999;
	}

	.form-title>view:nth-child(2)>image {
		width: 32rpx;
		height: 32rpx;
		margin-right: 12rpx;
	}

	.form-title>view:nth-child(3)>image {
		width: 64rpx;
		height: 32rpx;
		margin-right: 12rpx;
	}

	.zhugie-info-image-upload {
		height: 240rpx;
		width: 240rpx;
		text-align: center;
		border-radius: 12rpx;
		border: 1rpx solid #DDDDDD;
		position: relative;
	}

	.zhugie-info-image-upload uni-icons.plus {
		line-height: 240rpx;
	}

	.zhugie-info-image-upload uni-icons.clear {
		position: absolute;
		z-index: 3;
		right: -20rpx;
		top: -50rpx;
	}

	.zhugie-info-image-upload image {
		height: 100%;
		width: 100%;
		border-radius: 12rpx;
	}

	.zhugie-vip-style {
		display: flex;
		align-items: center;
	}

	.zhugie-vip-style image {
		height: 12px;
		width: 26px;
		margin-rihgt: 12rpx;
	}

	.zhugie-vip-style text {
		font-size: 28rpx;
		font-weight: 400;
	}
</style>