<template>
	<view class="content">
		<view class="zhuige-recommen">
			<view class="zhuige-block">
				<template v-if="comments && comments.length>0">
					<zhuige-reply v-for="(comment, index) in comments" :key="index" :item="comment">
					</zhuige-reply>
				</template>
				<template v-else-if="loaded">
					<zhuige-nodata></zhuige-nodata>
				</template>
			</view>

			<uni-load-more v-if="comments && comments.length>0" :status="loadMore"></uni-load-more>
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

	import ZhuigeReply from "@/components/zhuige-reply";
	import ZhuigeNodata from "@/components/zhuige-nodata";

	export default {
		data() {
			this.post_id = undefined;

			return {
				comments: [],
				loadMore: 'more',
				loaded: false,
			}
		},

		components: {
			ZhuigeReply,
			ZhuigeNodata
		},

		onLoad(options) {
			Util.addShareScore(options.source);

			if (!options.post_id) {
				uni.reLaunch({
					url: '/pages/tabs/index/index'
				})
				return;
			}
			this.post_id = options.post_id;

			this.loadComments(true);
		},

		onReachBottom() {
			if (this.loadMore == 'more') {
				this.loadComments(false);
			}
		},

		onShareAppMessage() {
			return {
				title: '所有评论-' + getApp().globalData.appName,
				path: Util.addShareSource('pages/base/commnets/commnets?post_id=' + this.post_id)
			};
		},

		// #ifdef MP-WEIXIN		
		onShareTimeline() {
			return {
				title: '所有评论-' + getApp().globalData.appName
			};
		},
		// #endif

		methods: {
			/**
			 * 加载评论
			 */
			loadComments(refresh) {
				if (this.loadMore == 'loading') {
					return;
				}
				this.loadMore = 'loading';

				Rest.post(Api.URL('comment', 'index'), {
					post_id: this.post_id,
					offset: this.comments.length
				}).then(res => {
					this.comments = refresh ? res.data.comments : this.comments.concat(res.data.comments);
					this.loadMore = res.data.more;
					this.loaded = true;
				}, err => {
					console.log(err)
				});
			}
		}
	}
</script>

<style>
	page {
		background: #f5f5f5;
	}

	.zhuige-recommen {
		padding: 0 20rpx 60rpx;
	}
</style>