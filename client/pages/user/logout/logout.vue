<template>
	<view class="content">
		<view class="zhuige-wide-box">
			<view class="zhuige-logout-info">
				<mp-html :content="explain" />
			</view>
			<view class="zhuige-logout-form">
				<view @click="clickCheckRead">
					<label>
						<radio :checked="checkRead" color="#111111" style="transform:scale(0.7)" />
						我已阅读并知晓了注销申请须知
					</label>
				</view>
				<view @click="clickLogout">立即注销</view>
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

	import Constant from '@/utils/constants';
	import Util from '@/utils/util';
	import Auth from "@/utils/auth";
	import Alert from '@/utils/alert';
	import Api from '@/utils/api';
	import Rest from '@/utils/rest';

	export default {
		data() {
			return {
				explain: '',
				checkRead: false,
			}
		},

		onLoad(options) {
			if (!Auth.getUser()) {
				uni.reLaunch({
					url: '/pages/tabs/index/index'
				})
				return;
			}
			
			Util.addShareScore(options.source);

			this.loadSetting()
		},

		methods: {
			clickCheckRead() {
				this.checkRead = !this.checkRead;
			},

			clickLogout() {
				if (!this.checkRead) {
					Alert.toast('阅读并同意注销须知后，才能注销');
					return;
				}

				uni.showModal({
					title: '提示',
					content: '确定要注销吗？',
					success: (res) => {
						if (res.cancel) {
							return;
						}

						this.logout();
					}
				});
			},

			/**
			 * 加载配置
			 */
			loadSetting() {
				Rest.post(Api.URL('setting', 'logout')).then(res => {
					this.explain = res.data.explain;
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 注销
			 */
			logout() {
				Rest.post(Api.URL('user', 'logout')).then(res => {
					if (res.code != 0) {
						Alert.toast(res.message);
						return;
					}

					Util.navigateBack();
				}, err => {
					console.log(err)
				});
			}
		}
	}
</script>

<style>
	/* =========== 账号注销 =========== */
	.zhuige-logout-info {
		padding: 20px;
		background: #FFFFFF;
		border-radius: 12rpx;
	}

	.zhuige-logout-info,
	.zhuige-logout-info view {
		line-height: 2.2em;
		font-size: 28rpx;
	}

	.zhuige-logout-info rich-text {
		display: block;
		line-height: 2.2em;
		font-size: 28rpx;
		margin-bottom: 1em;
	}

	.zhuige-logout-info view image {
		width: 100%;
	}

	.zhuige-logout-info view.zhuige-logout-title {
		font-size: 33rpx;
		font-weight: 500;
	}

	.zhuige-logout-form {
		text-align: center;
		padding: 10px;
	}

	.zhuige-logout-form label {
		font-size: 26rpx;
		font-weight: 300;
	}

	.zhuige-logout-form view:nth-child(1) {
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.zhuige-logout-form view:nth-child(2) {
		width: 60%;
		text-align: center;
		height: 96rpx;
		line-height: 96rpx;
		border-radius: 96rpx;
		font-size: 32rpx;
		font-weight: 400;
		color: #FFFFFF;
		background: #010101;
		margin: 10px auto;
	}
</style>