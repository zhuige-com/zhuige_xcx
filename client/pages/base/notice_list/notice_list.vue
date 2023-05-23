<template>
	<view class="content">
		<view class="zhuige-notice-list">

			<template v-if="notifys.length>0">
				<view class="zhuige-block">
					<view v-for="(notify, index) in notifys" :key="index" class="zhuige-notice">
						<view class="zhuige-notice-avatar">
							<image @click="clickLink('/pages/user/home/home?user_id=' + notify.from.user_id)"
								mode="aspectFill" :src="notify.from.avatar"></image>
						</view>
						<view class="zhuige-notice-step">
							<uni-badge v-if="notify.isread==0" text=" " :is-dot="true" size="small" type="error">
							</uni-badge>
						</view>
						<view @click="clickContent(notify)" class="zhuige-notice-text">
							<view class="zhuige-notice-title">
								<text>{{notify.from.nickname}}</text>
								<text>{{notify.time}}</text>
							</view>
							<view class="zhuige-notice-data">
								<text>{{notify.content}}</text>
							</view>
							<!-- 
								@的提示分为评论@和帖子@，仅文字区分
								在评论中@了你，帖子名称帖子名称帖子名称帖子名称
								在帖子中@了你，帖子名称帖子名称帖子名称帖子名称
							 -->
						</view>
					</view>
				</view>
				<uni-load-more :status="loadMore"></uni-load-more>
			</template>
			<template v-else>
				<view v-if="loaded" class="zhuige-block">
					<zhuige-nodata></zhuige-nodata>
				</view>
			</template>

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
	import Api from '@/utils/api';
	import Rest from '@/utils/rest';

	import ZhuigeNodata from "@/components/zhuige-nodata";

	export default {
		data() {
			return {
				type: '',
				notifys: [],
				loadMore: 'more',
				loaded: false,

				like_count: 0,
				favorite_count: 0,
				comment_count: 0,
				follow_count: 0,
				ait_count: 0,
			};
		},

		components: {
			ZhuigeNodata
		},

		onLoad(options) {
			Util.addShareScore(options.source);

			if (!options.type) {
				uni.reLaunch({
					url: '/pages/tabs/index/index'
				})
				return;
			}
			this.type = options.type;

			if (this.type == 'like') {
				uni.setNavigationBarTitle({
					title: '【点赞】通知'
				})
			} else if (this.type == 'favorite') {
				uni.setNavigationBarTitle({
					title: '【收藏】通知'
				})
			} else if (this.type == 'comment') {
				uni.setNavigationBarTitle({
					title: '【评论】通知'
				})
			} else if (this.type == 'follow') {
				uni.setNavigationBarTitle({
					title: '【粉丝】通知'
				})
			}

			this.loadNotifys();
		},

		onShow() {
			console.log('onShow');
		},

		onReachBottom() {
			if (this.loadMore == 'more') {
				this.loadNotifys();
			}
		},

		onPullDownRefresh() {
			this.refresh();
		},

		onShareAppMessage() {
			return {
				title: '通知-' + getApp().globalData.appName,
				path: Util.addShareSource('pages/base/notice_list/notice_list?type=' + this.type)
			};
		},

		// #ifdef MP-WEIXIN		
		onShareTimeline() {
			return {
				title: '通知-' + getApp().globalData.appName,
			};
		},
		// #endif

		methods: {
			/**
			 * 刷新
			 */
			refresh() {
				this.notifys = [];
				this.loadMore = 'more';
				this.loaded = false;
				this.loadNotifys();
			},

			/**
			 * 点击链接
			 */
			clickLink(link) {
				Util.openLink(link);
			},

			/**
			 * 清理通知
			 */
			clickClear() {
				Rest.post(Api.URL('user', 'notify_clear'), {}).then(res => {
					this.refresh();
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 点击通知
			 */
			clickContent(notify) {
				if (notify.link) {
					Util.openLink(notify.link);
				}
				notify.isread = 1;

				Rest.post(Api.URL('user', 'notify_read'), {
					notify_id: notify.id
				}).then(res => {
					console.log(res);
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 加载数据
			 */
			loadNotifys() {
				if (this.loadMore == 'loading') {
					return;
				}
				this.loadMore = 'loading'

				Rest.post(Api.URL('user', 'notify'), {
					offset: this.notifys.length,
					type: this.type
				}).then(res => {
					this.notifys = this.notifys.concat(res.data.notifys);
					this.loadMore = res.data.more;
					this.loaded = true;

					uni.stopPullDownRefresh();
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

	.zhuige-notice-list {
		padding: 0 20rpx;
	}

	.zhuige-notice {
		display: flex;
		align-items: center;
		padding: 40rpx 0;
		border-bottom: 1rpx solid #DDDDDD;
		position: relative;
	}

	.zhuige-notice:last-of-type {
		border: none;
	}

	.zhuige-notice-avatar {
		height: 80rpx;
		width: 80rpx;
	}

	.zhuige-notice-avatar image {
		height: 100%;
		width: 100%;
		border-radius: 50%;
	}

	.zhuige-notice-text {
		padding-left: 30rpx;
		width: 540rpx;
	}

	.zhuige-notice-title {
		height: 2em;
		line-height: 1.8em;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.zhuige-notice-title text:nth-child(1) {
		font-size: 32rpx;
		font-weight: 600;
	}

	.zhuige-notice-title text:nth-child(2) {
		font-size: 24rpx;
		font-weight: 300;
		color: #999999;
		margin-left: 20rpx;
	}

	.zhuige-notice-data {
		font-size: 28rpx;
		font-weight: 400;
		line-height: 1.6em;
		color: #666666;
		display: -webkit-box;
		text-overflow: ellipsis;
		-webkit-box-orient: vertical;
		-webkit-line-clamp: 1;
		overflow: hidden;
	}

	.zhuige-notice-step {
		position: absolute;
		z-index: 3;
		left: 80rpx;
		top: 14rpx;
	}
</style>