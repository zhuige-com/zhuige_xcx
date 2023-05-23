<template>
	<view style="width: 100vw; height: 100vh;">
		<channel-video style="width: 100vw; height: 100vh;" object-fit="contain" v-if="feed_id && finder_user_name"
			:feed-id="feed_id" :finder-user-name="finder_user_name" autoplay="true"></channel-video>
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

	export default {
		data() {
			return {
				feed_id: '',
				finder_user_name: ''
			};
		},

		onLoad(options) {
			if (options.id && options.name) {
				this.feed_id = options.id;
				this.finder_user_name = options.name;
			} else {
				uni.reLaunch({
					url: '/pages/tabs/index/index'
				})
			}
		},

		onShareAppMessage() {
			return {
				title: '视频播放-' + getApp().globalData.appName,
				path: Util.addShareSource('pages/base/channel_video/channel_video?id=' + this.feed_id + '&name=' +
					this.finder_user_name)
			};
		},

		// #ifdef MP-WEIXIN		
		onShareTimeline() {
			return {
				title: '视频播放-' + getApp().globalData.appName
			};
		},
		// #endif
	}
</script>

<style>

</style>