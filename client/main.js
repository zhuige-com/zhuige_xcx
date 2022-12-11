import App from './App'

// #ifndef VUE3
import Vue from 'vue'
Vue.config.productionTip = false
App.mpType = 'app'
const app = new Vue({
    ...App
})
app.$mount()
// #endif

// #ifdef VUE3
import { createSSRApp } from 'vue'
export function createApp() {
  const app = createSSRApp(App)
  return {
    app
  }
}
// #endif

// 设置微信小商店【店铺按钮】仅适用于调试库2.25.4以下版本
// // #ifdef MP-WEIXIN
// {
// 	const miniShopPlugin = requirePlugin('mini-shop-plugin');
// 	miniShopPlugin.initApp(getApp(), wx)
// 	if (miniShopPlugin) {
// 		miniShopPlugin.initHomePath('/pages/wxmall/index/index');
// 	}
// }
// // #endif
