<template>
	<view class="content">
		<!-- 搜索块 -->
		<view class="zhuige-search">
			<view class="zhuige-block">
				<view class="zhuige-search-form">
					<view class="zhuige-search-input">
						<view>
							<uni-icons type="search" color="#999999" size="18"></uni-icons>
							<input type="text" v-model="keywords" placeholder="关键词" @confirm="onSearch" />
							<uni-icons v-if="keywords" @click="clickClear" type="close" color="#999999" size="18">
							</uni-icons>
						</view>
					</view>
					<view @click="onSearch">搜索</view>
				</view>

				<view v-if="historys && historys.length>0" class="zhuige-search-tags">
					<view v-for="(item, index) in historys" :key="index" @click="search(item)">{{item}}</view>
					<view class="tags-del" @click="clickClearHistory()">删除记录</view>
				</view>
			</view>
		</view>

		<!-- 热门 -->
		<view v-if="hots && hots.length>0" class="zhuige-search-hot">
			<view class="zhuige-block">
				<view class="zhuige-search-hot-title">热门搜索</view>
				<view v-for="(item, index) in hots" :key="index" @click="search(item)">{{item}}</view>
			</view>
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

	import Constant from '@/utils/constants';
	import Util from '@/utils/util';
	import Alert from '@/utils/alert';
	import Api from '@/utils/api';
	import Rest from '@/utils/rest';

	export default {
		data() {
			return {
				keywords: '',

				historys: [],

				hots: [],
			}
		},

		onLoad(options) {
			Util.addShareScore(options.source);

			uni.getStorage({
				key: Constant.ZHUIGE_SEARCH_KEY,
				success: (res) => {
					this.historys = res.data;
				}
			});

			this.loadSetting();
		},

		onShareAppMessage() {
			return {
				title: '搜索-' + getApp().globalData.appName,
				path: Util.addShareSource('pages/base/search/search?n=n')
			};
		},

		// #ifdef MP-WEIXIN
		onShareTimeline() {
			return {
				title: '搜索-' + getApp().globalData.appName,
			};
		},
		// #endif

		methods: {
			/**
			 * 清空搜索框
			 */
			clickClear() {
				this.keywords = '';
			},

			/**
			 * 搜索
			 */
			onSearch() {
				this.search(this.keywords);
			},

			/**
			 * 清理搜索历史
			 */
			clickClearHistory() {
				uni.showModal({
					title: '提示',
					content: '确定要清除吗？',
					success: (res) => {
						if (res.confirm) {
							uni.setStorage({
								key: Constant.ZHUIGE_SEARCH_KEY,
								data: [],
								success: () => {
									this.historys = [];
								}
							});
						}
					}

				});
			},

			/**
			 * 搜索
			 */
			search(keyword) {
				if (keyword == '') {
					Alert.toast('关键词不能为空');
					return;
				}

				this.logHistory(keyword);

				uni.navigateTo({
					url: '/pages/base/list/list?keyword=' + keyword + '&title=搜索【' + keyword + '】'
				});
			},

			/**
			 * 加载搜索历史
			 */
			logHistory(keyword) {
				uni.getStorage({
					key: Constant.ZHUIGE_SEARCH_KEY,
					success: (res) => {
						let keys = [keyword];
						for (let i = 0; i < res.data.length && keys.length < Constant
							.ZHUIGE_SEARCH_MAX_COUNT; i++) {
							if (keyword == res.data[i]) {
								continue;
							}
							keys.push(res.data[i]);
						}

						this.historys = keys;

						uni.setStorage({
							data: keys,
							key: Constant.ZHUIGE_SEARCH_KEY
						});
					},
					fail: (e) => {
						let keys = [keyword];
						this.historys = keys;
						uni.setStorage({
							data: keys,
							key: Constant.ZHUIGE_SEARCH_KEY
						});
					}
				});
			},

			/**
			 * 加载配置
			 */
			loadSetting() {
				Rest.post(Api.URL('setting', 'search'), {}).then(res => {
					this.hots = res.data.hots;
				}, err => {
					console.log(err)
				});
			}
		}
	}
</script>

<style>
	page {
		background-color: #F5F5F5;
	}
</style>