<template>
	<view class="content">
		<uni-nav-bar title="我的" :fixed="true" :statusBar="true" :border="false" :opacity="nav_opacity" :placeholder="false">
		</uni-nav-bar>

		<!-- 顶部背景 -->
		<view class="zhuige-mine-bg">
			<image mode="aspectFill" :src="background"></image>
		</view>

		<!--内容区 -->
		<view class="zhuige-mine-block">
			<!-- 用户数据块 -->
			<view class="zhuige-mine-data">
				<view class="zhuige-mine-data-user">
					<view @click="clickAvatar" class="zhuige-mine-login-user">
						<view class="zhuige-mine-login-user-avatar">
							<image mode="aspectFill" :src="avatar"></image>
						</view>
						<view class="zhuige-mine-login-user-info">
							<view>
								<text>{{nickname}}</text>
								<image v-if="vip && vip.status==1" mode="aspectFill" :src="vip.icon"></image>
							</view>
							<view v-if="certify">
								<template v-if="certify.status == 1">
									<image mode="aspectFill" :src="certify.icon"></image>
									<text>{{certify.name}}</text>
								</template>
								<template v-else>
									<image class="no-certify" mode="aspectFill" src="@/static/lvv.png"></image>
									<text>未认证</text>
								</template>
							</view>
							<!-- <view v-if="vip && vip.status==1"><image mode="aspectFill" :src="vip.icon"></image></view> -->
						</view>
					</view>
					<view @click="openLink('/pages/user/home/home')" class="my-site-link">我的主页</view>
				</view>
				<view class="zhuige-mine-data-count">
					<view @click="openLink('/pages/user/home/home?tab=publish')">
						<text>{{post_count}}</text>
						<view>动态</view>
					</view>
					<view @click="openLink('/pages/user/friend/friend?tab=fans')">
						<text>{{fans_count}}</text>
						<view>粉丝</view>
					</view>
					<view @click="openLink('/pages/user/friend/friend?tab=follow')">
						<text>{{follow_count}}</text>
						<view>关注</view>
					</view>
					<view @click="openLink('/pages/user/home/home?tab=publish')">
						<text>{{likeme_count}}</text>
						<view>获赞</view>
					</view>
				</view>
			</view>

			<!-- 幻灯片 -->
			<view v-if="slides && slides.length>0" class="zhugie-mini-ad">
				<zhuige-swiper :items="slides" type="zhuige-mini-swiper"></zhuige-swiper>
			</view>

			<!-- 自定义图标 -->
			<view v-for="(menu, index) in menus" :key="index" class="zhuige-mine-data">
				<view class="zhuige-block">
					<view class="zhuige-block-head">
						<view>{{menu.title}}</view>
					</view>
					<zhuige-icons type="wrap" :items="menu.items"></zhuige-icons>
				</view>
			</view>

		</view>

		<!-- 版权信息 -->
		<view v-if="copyright" class="zhuige-mine-copyinfo">
			<view>
				<image mode="aspectFill" :src="copyright.logo"></image>
			</view>
			<view>
				<text>{{copyright.text}}</text>
			</view>
			<!-- 本小程序商用必须保留前后端代码版权信息（若需去版权请联系微信：jianbing2011） -->
			
			<!-- 备案信息 -->
			<view v-if="beian_icp || beian_ga" class="zhuige-mine-recordinfo">
				<view v-if="beian_icp" @click="openLink(beian_icp.link)">
					<image v-if="beian_icp.logo" :src="beian_icp.logo"></image>
					<text>
						{{beian_icp.sn}}
					</text>
				</view>
				<view v-if="beian_ga" @click="openLink(beian_ga.link)">
					<image v-if="beian_ga.logo" :src="beian_ga.logo"></image>
					<text>
						{{beian_ga.sn}}
					</text>
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

	import Util from '@/utils/util';
	import Auth from '@/utils/auth';
	import Alert from '@/utils/alert';
	import Api from '@/utils/api';
	import Rest from '@/utils/rest';

	import ZhuigeIcons from "@/components/zhuige-icons";
	import ZhuigeSwiper from "@/components/zhuige-swiper";

	export default {
		components: {
			ZhuigeIcons,
			ZhuigeSwiper
		},
		
		data() {
			return {
				isLogin: false,
				avatar: '/static/avatar.jpg',
				nickname: '立即登录',
				certify: undefined,
				vip: undefined,

				post_count: 0,
				fans_count: 0,
				follow_count: 0,
				likeme_count: 0,

				ad: undefined,

				nav_opacity: 0,

				background: undefined,

				slides: [],

				menus: [],

				copyright: undefined,
				
				beian_icp: undefined,
				beian_ga: undefined,
			}
		},

		onLoad(options) {
			Util.addShareScore(options.source);

			this.loadSetting();
		},

		onShow() {
			this.isLogin = !!(Auth.getUser());

			if (this.isLogin) {
				Rest.post(Api.URL('user', 'my_statistics'), {}).then(res => {
					this.post_count = res.data.post_count;
					this.fans_count = res.data.fans_count;
					this.follow_count = res.data.follow_count;
					this.likeme_count = res.data.likeme_count;

					if (res.data.nickname) {
						this.nickname = res.data.nickname;
					} else { //用户token未验证通过，则清空
						uni.clearStorageSync();
					}

					if (res.data.avatar) {
						this.avatar = res.data.avatar;
					}

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
		},

		onPageScroll(e) {
			this.nav_opacity = (e.scrollTop > 255 ? 255 : e.scrollTop) / 255;
			if (e.scrollTop > 20) {
				uni.setNavigationBarColor({
					frontColor: '#000000',
					backgroundColor: '#ffffff',
				})
			} else {
				uni.setNavigationBarColor({
					frontColor: '#ffffff',
					backgroundColor: '#ffffff'
				})
			}
		},

		onShareAppMessage() {
			return {
				title: '我的-' + getApp().globalData.appName,
				path: Util.addShareSource('pages/tabs/mine/mine?n=n')
			};
		},

		// #ifdef MP-WEIXIN
		onShareTimeline() {
			return {
				title: '我的-' + getApp().globalData.appName,
			};
		},
		// #endif

		methods: {
			/**
			 * 点击打开链接
			 */
			openLink(link) {
				Util.openLink(link);
			},

			/**
			 * 点击头像
			 */
			clickAvatar() {
				if (Auth.getUser()) {
					Util.openLink('/pages/user/info/info')
				} else {
					Util.openLink('/pages/user/login/login')
				}
			},

			/**
			 * 加载配置
			 */
			loadSetting() {
				Rest.post(Api.URL('setting', 'mine')).then(res => {
					this.background = res.data.background;
					this.slides = res.data.slides;
					this.menus = res.data.menus;

					if (res.data.copyright) {
						this.copyright = res.data.copyright;
					}
					
					if (res.data.beian_icp) {
						this.beian_icp = res.data.beian_icp;
					}
					
					if (res.data.beian_ga) {
						this.beian_ga = res.data.beian_ga;
					}
				}, err => {
					console.log(err)
				});
			}
		}
	}
</script>

<style>
	.zhuige-mine-bg {
		width: 100%;
		height: 540rpx;
		position: relative;
	}

	.zhuige-mine-bg image {
		height: 100%;
		width: 100%;
		border-radius: 0 0 32rpx 32rpx;
	}

	.zhugie-mini-ad {
		margin-bottom: 20rpx;
	}

	.zhuige-mine-block {
		padding: 20rpx;
		margin-top: -170rpx;
		position: relative;
		z-index: 3;
	}

	.zhuige-mine-data {
		background: #FFFFFF;
		border-radius: 12rpx;
		margin-bottom: 20rpx;
		overflow: hidden;
	}

	.zhuige-mine-data-user {
		padding: 30rpx;
		display: flex;
		align-items: center;
		justify-content: space-between;
	}

	.zhuige-mine-login-user {
		display: flex;
		align-items: center;
	}

	.zhuige-mine-login-user-avatar {
		height: 96rpx;
		width: 96rpx;
		flex: 0 0 96rpx;
	}

	.zhuige-mine-login-user-avatar image {
		height: 100%;
		width: 100%;
		border-radius: 50%;
	}

	.zhuige-mine-login-user-info {
		padding-left: 20rpx;
		flex: 1;
	}

	.zhuige-mine-login-user-info view:nth-child(1) {
		font-size: 36rpx;
		font-weight: 600;
		line-height: 1.6em;
		display: flex;
		align-items: center;
	}

	.zhuige-mine-login-user-info view:nth-child(1) image {
		height: 12px;
		width: 26px;
		min-width: 26px;
		margin-left: 8rpx;
	}

	.zhuige-mine-login-user-info view:nth-child(2) {
		display: flex;
		align-items: center;
	}

	.zhuige-mine-login-user-info view:nth-child(2) image {
		height: 12px;
		width: 12px;
	}

	.zhuige-mine-login-user-info view:nth-child(2) image.no-certify {
		filter: grayscale(100%);
		opacity: .7;
	}

	.zhuige-mine-login-user-info view:nth-child(2) text {
		font-size: 22rpx;
		font-weight: 300;
		margin-left: 8rpx;
	}

	.my-site-link {
		height: 52rpx;
		line-height: 52rpx;
		border-radius: 26rpx 0 0 26rpx;
		background: #010101;
		font-size: 24rpx;
		color: #FFFFFF;
		font-weight: 300;
		padding: 0 20rpx 0 30rpx;
		margin-right: -30rpx;
		min-width: 48px;
	}

	.zhuige-mine-data-count {
		display: flex;
		align-items: center;
		justify-content: center;
		padding: 30rpx;
		border-top: 1rpx solid #EEEEEE;
	}

	.zhuige-mine-data-count>view {
		width: 25%;
		border-right: 1rpx solid #EEEEEE;
		text-align: center;
	}

	.zhuige-mine-data-count>view:last-child {
		border: none;
	}

	.zhuige-mine-data-count>view text {
		font-size: 40rpx;
		font-weight: 600;
		height: 1.6em;
		line-height: 1.6em;
	}

	.zhuige-mine-data-count>view view {
		height: 1.8em;
		line-height: 1.8em;
		font-weight: 400;
		font-size: 26rpx;
	}

	.zhuige-mine-copyinfo {
		padding: 40rpx;
		text-align: center;
	}

	.zhuige-mine-copyinfo view {
		margin-bottom: 20rpx;
	}

	.zhuige-mine-copyinfo image {
		height: 120rpx;
		width: 120rpx;
		border-radius: 50%;
	}

	.zhuige-mine-copyinfo text {
		font-size: 24rpx;
		font-weight: 400;
		color: #999999;
	}
	.zhuige-icon .view {
		width: auto!important;
	}

	.zhuige-mine-data .zhuige-icon > view,
	.zhuige-mine-data .zhuige-icon > button {
		width: 25%;
		position: relative;
		padding: 30rpx 0;
	}
	.zhuige-mine-data .zhuige-icon .image {
		height: 64rpx !important;
		width: 64rpx !important;
		margin-bottom: -4rpx;
	}

	.zhuige-mine-data .zhuige-icon .text {
		color: #666666 !important;
	}

	.zhuige-mine-data .zhuige-icon view:nth-child(4n+1)::after {
		content: ' ';
		width: 400%;
		border-top: 1rpx solid #EEEEEE;
		position: absolute;
		height: 1px;
		top: 2rpx;
		right: -300%;
	}

	.zhuige-mine-data .zhuige-icon view uni-badge {
		position: absolute;
		z-index: 3;
		right: 40rpx;
		top: 40rpx;
	}

	.zhuige-mini-image-ad {
		height: 200rpx;
	}

	.zhuige-mine-data .zhuige-block-head {
		position: relative;
		margin-bottom: -8rpx;
		border-bottom: 10rpx solid #FFFFFF;
		z-index: 2;
	}

	.zhuige-mini-swiper {
		height: 160rpx !important;
	}

	.zhuige-dots-left .wx-swiper-dots.wx-swiper-dots-horizontal {
		bottom: 20rpx !important;
	}
	.zhuige-mine-recordinfo, .zhuige-mine-recordinfo view {
		display: flex;
		align-items: center;
		justify-content: center;
		height: 32rpx;
		line-height: 32rpx;
	}
	.zhuige-mine-recordinfo > view:nth-child(1) {
		margin-right: 12rpx;
	}
	.zhuige-mine-recordinfo view image {
		margin: 0 8rpx 0 0;
		height: 32rpx;
		width: 32rpx;
	}
	.zhuige-mine-recordinfo view text {
		font-size: 24rpx;
		font-weight: 400;
		color: #999999;
	}
</style>