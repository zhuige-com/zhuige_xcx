import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const store = new Vuex.Store({
	state: {
		status_bar_height: 0,
		content_height: 0,
	},

	getters: {

	},

	mutations: {
		setStatusBarHeight(state, height) {
			state.status_bar_height = height;
		},
		setContentHeight(state, height) {
			state.content_height = height;
		},
	},

	actions: {

	},

})

export default store
