<template>
	<view>
		<web-view v-if="src" :src="src"></web-view>
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
				src: ''
			};
		},

		onLoad(options) {
			if (!options.src) {
				uni.reLaunch({
					url: '/pages/tabs/index/index'
				})
				return;
			}

			Util.addShareScore(options.source);

			this.src = decodeURIComponent(options.src);
		},

		onShareAppMessage(options) {
			return {
				title: getApp().globalData.appName,
				path: Util.addShareSource('pages/base/webview/webview?src=' + encodeURIComponent(options.webViewUrl))
			};
		}

	}
</script>

<style>

</style>