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
				<!-- 轮播 -->
				<view v-if="cur_tab=='rec'" class="zhuige-classify-swiper">
					<zhuige-swiper type="zhuige-mini-swiper" :items="slides"></zhuige-swiper>
				</view>

				<!-- 圈子列表 -->
				<view class="zhuige-classify-side-list">
					<view class="zhuige-block">
						<view v-for="(forum, index) in forums" :key="index" @click="clickForum(forum.id)"
							class="zhuige-classify-block">
							<view>
								<image mode="aspectFill" :src="forum.logo"></image>
							</view>
							<view class="zhuige-classify-text">
								<view>{{forum.title}}</view>
								<view>
									<text>{{forum.user_count}}成员</text>
									<text>/</text>
									<text>{{forum.post_count}}作品</text>
								</view>
							</view>
						</view>

						<zhuige-nodata v-if="forums.length==0"></zhuige-nodata>
					</view>
				</view>
			</scroll-view>

			<!-- 创建圈子按钮 -->
			<view v-if="is_show_create_forum" @click="openLink('/pages/bbs/forum-create/forum-create')"
				class="zhuige-classify-creat">
				<view>建圈</view>
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

	import Util from '@/utils/util';
	import Alert from '@/utils/alert';
	import Api from '@/utils/api';
	import Rest from '@/utils/rest';

	import ZhuigeSwiper from "@/components/zhuige-swiper";
	import ZhuigeNodata from "@/components/zhuige-nodata";

	export default {
		data() {
			this.loginReload = false;

			return {
				slides: [],

				tabs: [],
				cur_tab: undefined,

				is_show_create_forum: undefined,

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

			uni.$on('zhuige_event_user_login', this.onSetReload);
			uni.$on('zhuige_event_user_join_forum', this.onSetReload);
		},

		onShow() {
			if (this.loginReload) {
				this.loginReload = false;

				this.loadForumCats();
			}
		},

		onUnload() {
			uni.$off('zhuige_event_user_join_forum', this.onSetReload);
			uni.$off('zhuige_event_user_login', this.onSetReload);
		},

		onShareAppMessage() {
			return {
				title: '圈子-' + getApp().globalData.appName,
				path: Util.addShareSource('pages/tabs/forum/forum?n=n')
			};
		},

		// #ifdef MP-WEIXIN
		onShareTimeline() {
			return {
				title: '圈子-' + getApp().globalData.appName,
			};
		},
		// #endif

		methods: {
			//----- event start -----
			/**
			 * 登录事件
			 */
			onSetReload(data) {
				this.loginReload = true;
			},
			//----- event end -----

			/**
			 * 点击打开链接
			 */
			openLink(link) {
				Util.openLink(link);
			},

			/**
			 * 点击切换分类
			 */
			clickTab(tab) {
				this.cur_tab = tab.id;

				this.loadForums(this.cur_tab);
			},

			/**
			 * 点击圈子
			 */
			clickForum(id) {
				Util.openLink('/pages/bbs/forum/forum?forum_id=' + id);
			},

			/**
			 * 加载圈子
			 */
			loadForums(id) {
				let url = '';
				if (id == 'my') {
					url = Api.URL('bbs', 'list_my');
				} else if (id == 'rec') {
					url = Api.URL('bbs', 'list_rec');
				} else {
					url = Api.URL('bbs', 'list_cat');
				}

				Rest.post(url, {
					cat_id: id,
					stat: 1
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
				Rest.post(Api.URL('bbs', 'setting_forum')).then(res => {
					this.slides = res.data.slides;
					this.tabs = res.data.tabs;

					if (!this.cur_tab) {
						this.cur_tab = res.data.cur_tab;
					}

					if (res.data.is_show_create_forum) {
						this.is_show_create_forum = res.data.is_show_create_forum;
					}

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
		font-weight: 400;
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
		height: 96rpx;
		width: 96rpx;
		line-height: 96rpx;
		border-radius: 50%;
		text-align: center;
		font-size: 28rpx;
		font-weight: 400;
		color: #FFFFFF;
	}
</style>