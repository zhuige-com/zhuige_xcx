<template>
	<view class="content">

		<!-- @ 消息列表 -->
		<view v-if="notifys.length>0" class="zhuige-block">
			<view v-for="(notify, index) in notifys" :key="index" class="zhuige-msg">
				<view class="zhuige-msg-avatar" @click="openLink('/pages/user/home/home?user_id=' + notify.from_id)">
					<image mode="aspectFill" :src="notify.from_avatar"></image>
				</view>
				<view v-if="notify.isread=='0'" class="zhuige-notice-step">
					<uni-badge text=" " :is-dot="true" size="small" type="error"></uni-badge>
				</view>
				<view class="zhuige-msg-text" @click="clickNotify(notify)">
					<view class="zhuige-msg-title">
						<view>
							<text>{{notify.from_nickname}}</text>
							<text>{{notify.time}}</text>
						</view>
					</view>
					<view class="zhuige-msg-data">
						<text>在帖子中@了你，{{notify.excerpt}}</text>
					</view>
				</view>
			</view>
		</view>

		<template v-if="notifys.length>0">
			<uni-load-more :status="loadMore"></uni-load-more>
		</template>

		<!-- 无数据时 -->
		<view v-if="loaded && notifys.length==0" class="zhuige-block">
			<zhuige-nodata></zhuige-nodata>
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
	 * Copyright © 2022-2024 www.zhuige.com All rights reserved.
	 */
	
	import Util from '@/utils/util';
	import Api from '@/utils/api';
	import Rest from '@/utils/rest';

	import ZhuigeNodata from "@/components/zhuige-nodata";

	export default {
		components: {
			ZhuigeNodata
		},
		
		data() {
			return {
				notifys: [],
				loadMore: 'more',
				loaded: false,
			}
		},

		onLoad(options) {
			Util.addShareScore(options.source);
			
			this.loadData();
		},

		onReachBottom() {
			if (this.loadMore == 'more') {
				this.loadData();
			}
		},

		methods: {
			/**
			 * 点击打开链接
			 */
			openLink(link) {
				Util.openLink(link);
			},

			/**
			 * 点击通知
			 */
			clickNotify(notify) {
				Rest.post(Api.URL('at_users', 'read'), {
					notify_id: notify.id
				}).then(res => {
					notify.isread = '1';
				}, err => {
					console.log(err)
				});

				Util.openLink('/pages/bbs/detail/detail?id=' + notify.topic_id)
			},

			/**
			 * 加载数据
			 */
			loadData() {
				Rest.post(Api.URL('at_users', 'notifys'), {
					offset: this.notifys.length
				}).then(res => {
					if (res.code == 0) {
						this.notifys = this.notifys.concat(res.data.notifys);
						this.loadMore = res.data.more;
					}

					this.loaded = true;
				}, err => {
					console.log(err);
				});
			}
		}
	}
</script>

<style>
	page {
		background-color: #F5F5F5;
	}

	.content {
		padding: 0 20rpx;
	}

	.zhuige-msg-list {
		padding: 0 20rpx;
	}

	.zhuige-msg {
		display: flex;
		align-items: center;
		padding: 40rpx 0;
		border-bottom: 1rpx solid #DDDDDD;
		position: relative;
	}

	.zhuige-msg:last-of-type {
		border: none;
	}

	.zhuige-msg-avatar {
		height: 80rpx;
		width: 80rpx;
	}

	.zhuige-msg-avatar image {
		height: 100%;
		width: 100%;
		border-radius: 50%;
	}

	.zhuige-msg-text {
		padding-left: 30rpx;
		width: 540rpx;
	}

	.zhuige-msg-title {
		height: 2em;
		line-height: 1.8em;
		display: flex;
		align-items: center;
		justify-content: space-between;
	}

	.zhuige-msg-title view:nth-child(1) {
		display: flex;
		align-items: center;
	}

	.zhuige-msg-title view:nth-child(1) text {
		margin-right: 8rpx;
		font-size: 28rpx;
		font-weight: 600;
	}

	.zhuige-msg-title view:nth-child(1) text:nth-child(2) {
		font-size: 24rpx;
		font-weight: 400;
		color: #999999;
	}

	.zhuige-msg-title view:nth-child(2) {
		font-size: 24rpx;
		font-weight: 400;
		color: #999999;
	}

	.zhuige-msg-data {
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