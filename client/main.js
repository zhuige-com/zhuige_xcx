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

/**
 * ʹ���̳ǣ�΢��С�̵꣩ʱ�ſ�
 */
// const miniShopPlugin = requirePlugin('mini-shop-plugin');
// miniShopPlugin.initApp(getApp(), wx)
// if (miniShopPlugin) {
// 	miniShopPlugin.initHomePath('/pages/wxmall/index/index');
// }