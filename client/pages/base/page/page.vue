<template>
	<view class="content">
		<view class="zhuige-page-box">
			<view class="zhuige-block">
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

	export default {
		data() {
			this.page_id = undefined;

			return {
				post: undefined,
			}
		},

		onLoad(options) {
			Util.addShareScore(options.source);

			if (options.id) {
				options.page_id = options.id;
			}

			if (!options.page_id) {
				uni.reLaunch({
					url: '/pages/tabs/index/index'
				})
				return;
			}

			this.page_id = options.page_id;

			uni.$on('linktap', this.onMPHtmlLink);
		},

		onUnload() {
			uni.$off('linktap', this.onMPHtmlLink);
		},

		onShow() {
			this.loadPost();
		},

		onShareAppMessage() {
			return {
				title: this.post.title + '-' + getApp().globalData.appName,
				path: Util.addShareSource('pages/base/page/page?page_id=' + this.page_id)
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
			 * 文章内连接
			 */
			onMPHtmlLink(data) {
				if (data['data-link']) {
					Util.openLink(data['data-link']);
				}
			},

			/**
			 * 加载文章
			 */
			loadPost() {
				Rest.post(Api.URL('posts', 'page'), {
					page_id: this.page_id
				}).then(res => {
					this.post = res.data;
					uni.setNavigationBarTitle({
						title: this.post.title
					})

					// #ifdef MP-BAIDU
					swan.setPageInfo({
						title: this.post.title + '_' + getApp().globalData.appName,
						keywords: getApp().globalData.appKeywords,
						description: getApp().globalData.appDesc,
					});
					// #endif
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

	.zhuige-page-box {
		padding: 0 20rpx 20rpx;
	}

	.zhuige-block {
		padding: 30rpx;
	}
</style>