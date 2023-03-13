import App from './App'
import store from './store'

// #ifndef VUE3
import Vue from 'vue'
Vue.config.productionTip = false
App.mpType = 'app'
Vue.prototype.$store = store
const app = new Vue({
	store,
    ...App
})
app.$mount()
// #endif

// #ifdef VUE3
import { createSSRApp } from 'vue'
export function createApp() {
  const app = createSSRApp(App)
  app.use(store)
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
