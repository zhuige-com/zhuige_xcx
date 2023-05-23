<script>
	/*
	 * 追格小程序 v1.7.0
	 * 作者: 追格
	 * 文档: https://www.zhuige.com/docs/zg.html
	 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
	 * github: https://github.com/zhuige-com/zhuige_xcx
	 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
	 */

	import Vue from 'vue'
	import Util from '@/utils/util';
	import Api from '@/utils/api';
	import Rest from '@/utils/rest';
	import store from '@/store/index.js'

	export default {
		globalData: {
			appName: '',
			appDesc: '',
			appLogo: '',
			noticeRedDot: 0,
			
			videoId: '',
			videoContext: undefined,
		},

		onLaunch() {
			Rest.post(Api.URL('setting', 'global')).then(res => {
				getApp().globalData.appName = res.data.title;
				getApp().globalData.appDesc = res.data.desc;
				getApp().globalData.appLogo = res.data.logo;

				getApp().globalData.noticeRedDot = res.data.notify_count;
				Util.setNoticeRedDot();
			}, err => {
				console.log(err)
			});
			
			let cart = Util.loadCart();
			if (!cart) {
				cart = [];
			}
			store.commit('cartSet', cart);
		},

		onShow() {

		},

		onHide() {

		}
	}
</script>

<style lang="scss">
	/*每个页面公共css */
	@import "style/main.css";
	@import "style/list.css";

	@keyframes show {
		0% {
			transform: translateY(-50px);
		}

		60% {
			transform: translateY(40upx);
		}

		100% {
			transform: translateY(0px);
		}
	}

	@-webkit-keyframes show {
		0% {
			transform: translateY(-50px);
		}

		60% {
			transform: translateY(40upx);
		}

		100% {
			transform: translateY(0px);
		}
	}
</style>
