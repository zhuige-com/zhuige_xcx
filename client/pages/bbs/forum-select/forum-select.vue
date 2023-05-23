<template>
	<view class="content">
		<view class="zhuige-classify">
			<!-- 左侧 -->
			<scroll-view class="zhuige-classify-key" scroll-y scroll-with-animation>
				<view v-for="(tab, index) in tabs" :key="index" :class="cur_tab==tab.id?'active':''"
					@click="clickTab(tab)"><text>{{tab.name}}</text></view>
			</scroll-view>

			<!-- 右侧 -->
			<scroll-view class="zhuige-classify-side" scroll-top="scrollTop" scroll-y scroll-with-animation>
				<!-- 圈子列表 -->
				<view class="zhuige-classify-side-list">
					<view class="zhuige-block">
						<!-- 圈子块 -->
						<view v-for="(forum, index) in forums" :key="index" @click="clickForum(forum)"
							class="zhuige-classify-block">
							<view>
								<image mode="aspectFill" :src="forum.logo"></image>
							</view>
							<view class="zhuige-classify-text">
								<view>{{forum.title}}</view>
							</view>
						</view>

						<zhuige-nodata v-if="forums.length==0"></zhuige-nodata>

					</view>
				</view>
			</scroll-view>
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

	import Util from '@/utils/util';
	import Alert from '@/utils/alert';
	import Api from '@/utils/api';
	import Rest from '@/utils/rest';

	import ZhuigeSwiper from "@/components/zhuige-swiper";
	import ZhuigeNodata from "@/components/zhuige-nodata";

	export default {
		data() {
			return {
				tabs: [],
				cur_tab: undefined,

				forums: [],
			}
		},

		components: {
			ZhuigeSwiper,
			ZhuigeNodata
		},

		onLoad(options) {
			Util.addShareScore(options.source);

			this.loadForumCats();
		},

		onShareAppMessage() {
			return {
				title: '选择圈子-' + getApp().globalData.appName,
				path: Util.addShareSource('pages/bbs/forum-select/forum-select')
			};
		},

		// #ifdef MP-WEIXIN
		onShareTimeline() {
			return {
				title: '选择圈子-' + getApp().globalData.appName,
			};
		},
		// #endif

		methods: {
			/**
			 * 切换圈子分类
			 */
			clickTab(tab) {
				this.cur_tab = tab.id;

				this.loadForums(this.cur_tab);
			},

			/**
			 * 点击选择圈子
			 */
			clickForum(forum) {
				uni.$emit('forumChange', {
					id: forum.id,
					name: forum.title
				});
				Util.navigateBack();
			},

			/**
			 * 按分类加载圈子
			 */
			loadForums(id) {
				Rest.post(Api.URL('bbs', 'list_cat'), {
					cat_id: id,
					stat: 0
				}).then(res => {
					this.forums = res.data.forums;
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 加载圈子分类
			 */
			loadForumCats() {
				Rest.post(Api.URL('bbs', 'forum_cats')).then(res => {
					this.tabs = res.data.tabs;

					this.cur_tab = res.data.cur_tab;

					this.loadForums(this.cur_tab);
				}, err => {
					console.log(err)
				});
			}
		}
	}
</script>

<style>
	.content {
		position: fixed;
		height: 100%;
		width: 100%;
	}

	.zhuige-classify {
		display: flex;
		height: 100%;
		overflow-y: scroll;
		width: 100%;
	}

	.zhuige-classify-key {
		width: 30%;
		background: #FFFFFF;
		border-radius: 0 12rpx 12rpx 0;
	}

	.zhuige-classify-key::-webkit-scrollbar {
		display: none;
	}

	.zhuige-classify-key view {
		height: 120rpx;
		font-size: 28rpx;
		display: flex;
		align-items: center;
		justify-content: left;
		padding-left: 40rpx;
		font-weight: 500;
	}

	.zhuige-classify-key view.active {
		background: #f5f5f5;
	}

	.zhuige-classify-key view text {
		color: #555555;
	}

	.zhuige-classify-key view.active text {
		font-weight: 600;
		font-size: 32rpx;
		color: #010101;
	}

	.zhuige-classify-side {
		width: 70%;
		height: 100%;
		overflow-y: scroll;
		background: #f5f5f5;
	}

	.zhuige-classify-swiper {
		padding: 0 20rpx 20rpx;
	}

	.zhuige-classify-creat {
		position: fixed;
		z-index: 19;
		right: 40rpx;
		bottom: 160rpx;
		background: #010101;
		height: 120rpx;
		width: 120rpx;
		line-height: 120rpx;
		border-radius: 50%;
		text-align: center;
		font-size: 32rpx;
		font-weight: 300;
		color: #FFFFFF;
	}
</style>