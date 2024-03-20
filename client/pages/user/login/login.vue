<template>
	<view class="content">
		<view class="scroll-container">
			<view class="scroll-item">
				<image mode="aspectFill" :src="background"></image>
			</view>
			<view class="scroll-item">
				<image mode="aspectFill" :src="background"></image>
			</view>
		</view>
		
		<view class="zhuige-login-box">
			<view class="zhuige-login">
				<view class="zhuige-logo">
					<image mode="aspectFit" :src="logo"></image>
					<view v-if="title">{{title}}</view>
					<view v-if="type=='mobile' && tip">绑定手机号才能{{tip}}</view>
				</view>

				<view class="zhuige-login-btn">
					<template v-if="type=='login'">
						<!-- #ifdef H5 -->
						<view style="color: white;">H5 平台可修改public/rest/class-zhuige-xcx-user-controller.php 函数 test_login
							用作测试</view>
						<view style="color: white;">H5 平台仅可用作测试，部分功能尚未适配</view>
						<!-- #endif -->
						<button class="zhuige-button" @click="clickLogin">登录</button>
					</template>
					<template v-if="type=='mobile'">
						<!-- #ifdef MP-WEIXIN -->
						<button type="default" class="zhuige-button" open-type="getPhoneNumber"
							@getphonenumber="getPhoneNumber">绑定手机号</button>
						<!-- #endif -->

						<!-- #ifndef MP-WEIXIN -->
						该平台下的手机绑定功能暂未实现
						<!-- #endif -->
					</template>

					<view @click="clickWalk" class="zhuige-button">随便逛逛</view>

					<view v-if="type!='mobile'" class="zhuige-login-tip">
						<template v-if="yhxy || yszc">
							<label @click="clickAgreeLicense">
								<radio :checked="argeeLicense" color="#ff4400" style="transform:scale(0.7)" />
								我已阅读并同意
							</label>
							<text class="link" v-if="yhxy" @click="openLink(yhxy)">《用户协议》</text>
							<template v-if="yhxy && yszc">及</template>
							<text class="link" v-if="yszc" @click="openLink(yszc)">《隐私条款》</text>
						</template>
						<template v-else>
							请在后台设置《用户协议》和《隐私条款》
						</template>
					</view>
				</view>
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
		components: {
			
		},
		
		data() {
			return {
				type: 'login',
				tip: undefined,

				background: '',
				logo: '',
				title: '',

				argeeLicense: false,
				yhxy: undefined,
				yszc: undefined,
			}
		},

		onLoad(options) {
			Util.addShareScore(options.source);

			if (options.type) {
				this.type = options.type;
			}

			if (options.tip) {
				this.tip = options.tip;
			}

			let nav_title = (this.type == 'login' ? '登录' : '绑定手机号');
			uni.setNavigationBarTitle({
				title: nav_title
			})

			this.title = getApp().globalData.appName;

			// #ifdef MP-WEIXIN || MP-BAIDU
			uni.login({
				success: (res) => {
					this.code = res.code;
				}
			});
			// #endif

			this.loadSetting()
		},

		onShareAppMessage() {
			return {
				title: getApp().globalData.appName,
				path: Util.addShareSource('pages/tabs/index/index?n=n')
			};
		},

		methods: {
			/**
			 * 点击打开链接
			 */
			openLink(link) {
				Util.openLink(link)
			},

			/**
			 * 点击同意协议
			 */
			clickAgreeLicense() {
				this.argeeLicense = !this.argeeLicense;
			},

			/**
			 * 点击登录
			 */
			clickLogin() {
				if (!this.argeeLicense) {
					Alert.toast('请阅读并同意《用户协议》及《隐私条款》');
					return;
				}

				// #ifdef H5
				Rest.post(Api.URL('user', 'test_login')).then(res => {
					if (res.code == 0) {
						Auth.setUser(res.data);
						uni.$emit('zhuige_event_user_login', {});
						Util.navigateBack();
					} else {
						Alert.toast(res.message);
					}
				}, err => {
					console.log(err)
				});
				return;
				// #endif

				// #ifdef MP-WEIXIN
				this.login('微信用户', '');
				return;
				// #endif

				Alert.toast('此平台尚未适配');
			},

			/**
			 * 登录
			 */
			login(nickname, avatar) {
				let params = {
					code: this.code,
					nickname: nickname,
					avatar: avatar
				};

				let source = uni.getStorageSync(Constant.ZHUIGE_SOURCE_USER_ID);
				if (source) {
					params.from_user_id = source;
				}

				// #ifdef MP-WEIXIN
				params.channel = 'weixin';
				// #endif

				// #ifdef MP-BAIDU
				params.channel = 'baidu';
				// #endif

				Rest.post(Api.URL('user', 'login'), params).then(res => {
					if (res.code != 0) {
						Auth.setUser(undefined);
						uni.$emit('zhuige_event_user_login', {});

						// Alert.toast(res.message);
						
						// setTimeout(() => {
						// 	uni.reLaunch({
						// 		url: '/pages/tabs/index/index'
						// 	})
						// }, 1000)
						
						uni.showModal({
							content: res.message
						})
						
						uni.reLaunch({
							url: '/pages/tabs/index/index'
						})
					} else {
						Auth.setUser(res.data);
						uni.$emit('zhuige_event_user_login', {});

						if (res.data.first && res.data.first == 1) {
							uni.redirectTo({
								url: '/pages/user/verify/verify'
							})
						} else {
							Util.navigateBack();
						}
					}
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 随便看看
			 */
			clickWalk() {
				Util.navigateBack()
			},

			/**
			 * 加载配置
			 */
			loadSetting() {
				Rest.post(Api.URL('setting', 'login')).then(res => {
					this.background = res.data.background;
					this.logo = res.data.logo;
					this.title = res.data.title;
					this.yhxy = res.data.yhxy;
					this.yszc = res.data.yszc;
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 获取手机号
			 */
			getPhoneNumber(e) {
				if (e.detail.errMsg != 'getPhoneNumber:ok') {
					Alert.toast(e.detail.errMsg)
					return;
				}
				
				Rest.post(Api.URL('user', 'set_mobile2'), {
					code: e.detail.code
				}).then(res => {
					Alert.toast(res.message)
				
					// 更新本地缓存的信息
					let user = Auth.getUser();
					user.mobile = res.data.mobile;
					Auth.setUser(user);
				
					uni.$emit('zhuige_event_user_mobile', {
						mobile: res.data.mobile
					});
					Util.navigateBack();
				})
			}
		}
	}
</script>

<style>
	.content {
		position: absolute;
		height: 100%;
		width: 100%;
		top: 0;
		left: 0;
	}
	.zhuige-login-box {
		position: absolute;
		z-index: 99;
		top: 0;
		left: 0;
	}
	.zhuige-login {
		position: fixed;
		height: 100%;
		width: 100%;
		text-align: center;
		background-size: cover;
		background-position: center;
	}

	.zhuige-logo {
		padding-top: 360rpx;
	}

	.zhuige-logo image {
		height: 160rpx;
		width: 160rpx;
		border-radius: 50%;
	}

	.zhuige-logo view {
		line-height: 4rem;
		font-size: 38rpx;
		font-weight: 500;
		color: #FFFFFF;
	}

	.zhuige-logo view:nth-child(3) {
		font-size: 28rpx;
		font-weight: 400;
	}

	.zhuige-login-btn {
		position: absolute;
		width: 100%;
		bottom: 140rpx;
	}

	.zhuige-button {
		margin: 0 100rpx;
		line-height: 100rpx;
		height: 100rpx;
		border-radius: 100rpx;
		font-size: 32rpx;
		font-weight: 500;
		margin-bottom: 1.2rem;
		border: 1rpx solid #EEEEEE;
		background: none;
		color: #FFFFFF;
	}

	.zhuige-login-tip {
		font-size: 22rpx;
		color: #FFFFFF;
	}

	.zhuige-login-tip text.link {
		color: #EEEEEE;
		text-decoration: underline;
	}
	
	
.scroll-container {
  overflow: hidden;
  position: relative;
  width: 100%;
  height: 100%; /* 设置滚动容器的高度 */
}
 
.scroll-item {
  position: absolute;
  width: 100%;
  height: 100%;
  animation: scrollUp 18s linear infinite; /* 设置动画名称，时长，速率函数和循环次数 */
}
.scroll-item:nth-child(2) {
	top: 100%;
}
.scroll-item image {
	height: 100%;
	width: 100%;
}
/* 背景动画 */

 @-webkit-keyframes scrollUp {
            0% {
                -webkit-transform: translateY(0);
                transform: translateY(0);
            }
            100% {
                -webkit-transform: translateY(-100%);
                transform: translateY(-100%);
            }
        }

        @-moz-keyframes scrollUp {
            0% {
                -moz-transform: translateY(0);
                transform: translateY(0);
            }
            100% {
                -moz-transform: translateY(-100%);
                transform: translateY(-100%);
            }
        }

        @-o-keyframes scrollUp {
            0% {
                -o-transform: translateY(0);
                transform: translateY(0);
            }
            100% {
                -o-transform: translateY(-100%);
                transform: translateY(-100%);
            }
        }

        @keyframes scrollUp {
            0% {
                -webkit-transform: translateY(0);
                -moz-transform: translateY(0);
                -o-transform: translateY(0);
                transform: translateY(0);
            }
            100% {
                -webkit-transform: translateY(-100%);
                -moz-transform: translateY(-100%);
                -o-transform: translateY(-100%);
                transform: translateY(-100%);
            }
        }
</style>