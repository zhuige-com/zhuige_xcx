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

		onShareAppMessage() {
			return {
				title: getApp().globalData.appName,
				path: Util.addShareSource('pages/base/webview/webview?src=' + encodeURIComponent(this.src))
			};
		},

		// #ifdef MP-WEIXIN
		onShareTimeline() {
			return {
				title: getApp().globalData.appName
			};
		},

		onAddToFavorites(res) {
			return {
				title: getApp().globalData.appName
			};
		},
		// #endif
	}
</script>

<style lang="scss">

</style>
