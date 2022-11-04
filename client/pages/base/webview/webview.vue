<template>
	<view>
		<web-view :src="src"></web-view>
	</view>
</template>

<script>
	import Util from '@/utils/util';

	export default {
		data() {
			return {
				src: 'https://www.zhuige.com/'
			};
		},

		onLoad(options) {
			Util.addShareScore(options.source);

			if (options.src) {
				this.src = decodeURIComponent(options.src);
			}
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
