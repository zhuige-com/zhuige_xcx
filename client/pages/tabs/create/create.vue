<template>
	<view class="content">
		<!-- 发布页广告 -->
		<view v-if="ad" class="zhuige-post-ad" @click="openLink(ad.link)">
			<view :class="{active:active}">
				<image mode="widthFix" :src="ad.image"></image>
			</view>
		</view>

		<view class="zhuige-post-icon-set">
			<view class="zhuige-post-bar">

				<view v-for="(item, index) in items" :key="index" @click="clickPost(item)" class="zhuige-post-icon"
					:class="{active:active}">
					<view>
						<image class="image" mode="aspectFill" :src="item.image"></image>
					</view>
					<text>{{item.title}}</text>
				</view>

			</view>
		</view>

		<view :class="{animation:do_animation}"></view>
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
	import Alert from '@/utils/alert';
	import Api from '@/utils/api';
	import Rest from '@/utils/rest';

	import ZhuigeIcons from "@/components/zhuige-icons";

	export default {
		data() {
			this.cache = false;
			return {
				ad: undefined,
				items: [],
				active: false,
			}
		},

		components: {
			ZhuigeIcons
		},

		onLoad(options) {
			Util.addShareScore(options.source);
		},

		onShow() {
			if (this.cache && this.cache.length > 0) {
				this.items = this.cache;
				setTimeout(() => {
					this.active = true;
				}, 100);
			} else {
				this.loadSetting();
			}
		},

		onHide() {
			this.active = false;
			this.cache = this.items;
			this.items = [];
		},

		onShareAppMessage() {
			return {
				title: '发布-' + getApp().globalData.appName,
				path: Util.addShareSource('pages/tabs/create/create?n=n')
			};
		},

		// #ifdef MP-WEIXIN
		onShareTimeline() {
			return {
				title: '发布-' + getApp().globalData.appName,
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
			 * 点击发布
			 */
			clickPost(item) {
				if (item.require_mobile && !Util.checkMobile(item.require_mobile_tip)) {
					return;
				}

				Util.openLink(item.link);
			},

			/**
			 * 加载配置
			 */
			loadSetting() {
				Rest.post(Api.URL('setting', 'create')).then(res => {
					if (res.data.ad) {
						this.ad = res.data.ad;
					}

					this.items = res.data.items;
					setTimeout(() => {
						this.active = true;
					}, 100);
				}, err => {
					console.log(err)
				});
			}
		}
	}
</script>

<style lang="scss">
	.content {
		position: fixed;
		height: 100%;
		width: 100%;
		overflow-y: scroll;
	}

	.zhuige-post-icon-set {
		position: absolute;
		width: 100%;
		padding: 0 10upx;
		box-sizing: border-box;
		bottom: 0;
		left: 0;
		text-align: center;
	}

	.zhuige-post-bar {
		padding: 20rpx 0 0;
		z-index: 5;
		position: relative;
		display: flex;
		flex-wrap: wrap;
		align-items: center;
		justify-content: center;
	}

	.zhuige-post-icon {
		width: 20%;
		opacity: 0;
		transition: all 0.5s ease-out;
		margin-bottom: 20rpx;
	}

	.active {
		opacity: 1;
		transform: translateY(-40px);
		transition: .5s cubic-bezier(0.6, 2, 0.3, 0.8);
	}

	.zhuige-post-icon view {
		height: 96rpx;
		width: 96rpx;
		line-height: 96rpx;
		border-radius: 50%;
		margin: 0 auto 10rpx;

		display: flex;
		align-items: center;
		justify-content: center;

		image {
			width: 80rpx;
			height: 80rpx;
		}
	}

	.zhuige-post-icon text {
		display: block;
		font-size: 24rpx;
	}

	.zhuige-post-icon:nth-child(1) {
		transition-delay: 0.01s;
	}

	.zhuige-post-icon:nth-child(2) {
		transition-delay: 0.05s;
	}

	.zhuige-post-icon:nth-child(3) {
		transition-delay: 0.1s;
	}

	.zhuige-post-icon:nth-child(4) {
		transition-delay: 0.15s;
	}

	.zhuige-post-icon:nth-child(5) {
		transition-delay: 0.2s;
	}

	.zhuige-post-icon:nth-child(6) {
		transition-delay: 0.35s;
	}

	.zhuige-post-icon:nth-child(7) {
		transition-delay: 0.40s;
	}

	.zhuige-post-icon:nth-child(8) {
		transition-delay: 0.45s;
	}

	.zhuige-post-icon:nth-child(9) {
		transition-delay: 0.50s;
	}

	.zhuige-post-icon:nth-child(10) {
		transition-delay: 0.55s;
	}

	.zhuige-post-ad {
		display: flex;
		align-items: center;
		justify-content: center;
		padding-top: 320rpx;
	}

	.zhuige-post-ad view {
		width: 720rpx;
		height: 180rpx;
	}

	.zhuige-post-ad image {
		width: 100%;
		height: auto;
		border-radius: 12rpx;
	}
</style>