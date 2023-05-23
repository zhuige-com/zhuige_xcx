<template>
	<view class="content">
		<view class="zhuige-single-nav">
			<zhuige-tab type="scroll" :tabs="pages" :cur-tab="page_id" @clickTab="clickTab"></zhuige-tab>
		</view>

		<view class="zhuige-single-view">
			<view v-if="post" class="zhuige-block">
				<mp-html :content="post.content" />
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
	 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
	 */

	import Util from '@/utils/util';
	import Alert from '@/utils/alert';
	import Api from '@/utils/api';
	import Rest from '@/utils/rest';

	import ZhuigeTab from "@/components/zhuige-tab";

	export default {
		data() {
			return {
				pages: [],
				page_id: undefined,
				post: undefined,
			}
		},

		components: {
			ZhuigeTab
		},

		onLoad(options) {
			Util.addShareScore(options.source);

			if (options.page_id) {
				this.page_id = options.page_id;
			}

			this.loadSetting();

			uni.$on('linktap', this.onMPHtmlLink);
		},

		onUnload() {
			uni.$off('linktap', this.onMPHtmlLink);
		},

		onShow() {
			// #ifdef MP-BAIDU
			swan.setPageInfo({
				title: '关于_' + getApp().globalData.appName,
				keywords: getApp().globalData.appKeywords,
				description: getApp().globalData.appDesc,
			});
			// #endif
		},

		onShareAppMessage() {
			return {
				title: this.post.title + '-' + getApp().globalData.appName,
				path: Util.addShareSource('pages/base/about/about?page_id=' + this.page_id)
			};
		},

		// #ifdef MP-WEIXIN		
		onShareTimeline() {
			return {
				title: this.post.title + '-' + getApp().globalData.appName
			};
		},
		// #endif

		methods: {
			/**
			 * 文章内链接点击
			 */
			onMPHtmlLink(data) {
				if (data['data-link']) {
					Util.openLink(data['data-link']);
				}
			},

			/**
			 * 切换TAB
			 */
			clickTab(tab) {
				this.page_id = tab.id;
				this.loadPage();
			},

			/**
			 * 加载配置
			 */
			loadSetting() {
				Rest.post(Api.URL('setting', 'about')).then(res => {
					this.pages = res.data.pages;
					if (!this.page_id && this.pages.length > 0) {
						this.page_id = this.pages[0].id;
					}
					this.loadPage();
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 加载文章
			 */
			loadPage() {
				Rest.post(Api.URL('posts', 'page'), {
					page_id: this.page_id
				}).then(res => {
					this.post = res.data;
				}, err => {
					console.log(err)
				});
			}
		}
	}
</script>

<style>
	page {
		background-color: #F5F5F5;
	}

	.zhuige-single-nav {
		position: -webkit-sticky;
		position: sticky;
		top: var(--window-top);
		z-index: 99;
	}

	.zhuige-single-nav,
	.zhuige-single-view {
		padding: 0 30rpx 30rpx;
	}

	.zhuige-single-view .zhuige-block {
		min-height: 560rpx;
		padding: 30rpx;
		line-height: 2.2em;
	}

	.zhuige-single-view channel-video {
		width: 100%;
		height: 480rpx;
		border-radius: 12rpx;
	}
</style>