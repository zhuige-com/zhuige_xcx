<template>
	<view class="content">

		<!-- 用户卡 基于发帖用户信息，增加用户列表专用 zhuige-users-block -->
		<template v-if="users.length>0">
			<view v-for="(user, index) in users" :key="index"
				@click="openLink('/pages/user/home/home?user_id=' + user.user_id)"
				class="zhuige-social-poster-blcok zhuige-users-block">
				<view class="zhuige-social-poster">
					<view class="zhuige-social-poster-avatar">
						<image mode="aspectFill" :src="user.avatar"></image>
					</view>
					<view class="zhuige-social-poster-info">
						<view>
							<text>{{user.nickname}}</text>
							<!-- 图2 认证-->
							<image v-if="user.certify && user.certify.status==1" mode="aspectFill"
								:src="user.certify.icon"></image>
							<!-- 图1 vip-->
							<image class="zhuige-social-vip" v-if="user.vip && user.vip.status==1" mode="aspectFit"
								:src="user.vip.icon"></image>
						</view>
						<view>
							<text>动态 {{user.post_count}}</text>
							<text>/</text>
							<text>粉丝 {{user.fans_count}}</text>
						</view>
					</view>
				</view>
				<view class="zhuige-social-opt" @click.stop="clickFollowUser(user.user_id)">
					<view v-if="user.is_follow==1 && user.is_fans==1">已互关</view>
					<view v-else-if="user.is_follow==1 && user.is_fans==0">已关注</view>
					<view v-else-if="user.is_follow==0 && user.is_fans==1" class="active">回关</view>
					<view v-else class="active">+关注</view>
				</view>
			</view>
		</template>
		<template v-else>
			<zhuige-nodata v-if="loaded" :tip="noDataTip"></zhuige-nodata>
		</template>

		<uni-load-more v-if="users.length>0" :status="loadMore"></uni-load-more>
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
	import Alert from '@/utils/alert';
	import Api from '@/utils/api';
	import Rest from '@/utils/rest';

	import ZhuigeTab from "@/components/zhuige-tab";
	import ZhuigeNodata from "@/components/zhuige-nodata";

	export default {
		components: {
			ZhuigeTab,
			ZhuigeNodata
		},
		
		data() {
			return {
				search: '',
				
				users: [],
				loadMore: 'more',
				loaded: false,

				noDataTip: '哇哦，什么也没有',
			}
		},

		onLoad(options) {
			Util.addShareScore(options.source);

			if (options.search) {
				this.search = options.search;
				uni.setNavigationBarTitle({
					title: '搜索【' + this.search + '】'
				})
			}

			this.loadUsers();
			
			uni.$on('zhuige_event_follow_user', this.onFlollowUser);
		},
		
		onUnload() {
			uni.$off('zhuige_event_follow_user', this.onFlollowUser);
		},

		onShow() {

		},

		onShareAppMessage() {
			let path = 'pages/user/list/list?n=n';
			if (this.search) {
				path += '&search=' + this.search;
			}
			return {
				title: '用户搜索-' + getApp().globalData.appName,
				path: Util.addShareSource(path)
			};
		},

		// #ifdef MP-WEIXIN
		onShareTimeline() {
			return {
				title: '用户搜索-' + getApp().globalData.appName,
			};
		},
		// #endif

		onReachBottom() {
			if (this.loadMore == 'more') {
				this.loadUsers();
			}
		},

		methods: {
			/**
			 * 关注用户事件
			 */
			onFlollowUser(data) {
				for (let i=0; i<this.users.length; i++) {
					if (this.users[i].user_id == data.user_id) {
						this.users[i].is_follow = data.is_follow;
						break;
					}
				}
			},
			
			/**
			 * 点击打开链接
			 */
			openLink(link) {
				Util.openLink(link);
			},

			/**
			 * 刷新
			 */
			refresh() {
				this.users = [];
				this.loadMore = 'more';
				this.loaded = false;

				this.loadUsers();
			},

			/**
			 * 点击关注用户
			 */
			clickFollowUser(user_id) {
				Rest.post(Api.URL('user', 'follow_user'), {
					user_id: user_id
				}).then(res => {
					if (res.code == 0) {
						uni.$emit('zhuige_event_follow_user', {
							user_id: user_id,
							is_follow: res.data.is_follow
						});
					} else {
						Alert.toast(res.message);
					}
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 加载用户
			 */
			loadUsers() {
				if (this.loadMore == 'loading') {
					return;
				}
				this.loadMore = 'loading';

				Rest.post(Api.URL('user', 'search'), {
					offset: this.users.length,
					search: this.search
				}).then(res => {
					this.users = this.users.concat(res.data.users);
					this.loadMore = res.data.more;
					this.loaded = true;

					if (res.data.tip) {
						this.noDataTip = res.data.tip;
					} else {
						this.noDataTip = undefined;
					}
				}, err => {
					console.log(err)
				});
			},

		}
	}
</script>

<style>
	page {
		background: #f5f5f5;
	}
	
	.content {
		padding: 0 20rpx;
	}

	.zhuige-users-block {
		padding: 30rpx 0;
		border-bottom: 1rpx solid #EEEEEE;
	}

	.zhuige-users-block:last-of-type {
		border: none;
	}

	.zhuige-social-opt {
		display: flex;
		flex-direction: row-reverse;
		width: 22% !important;
	}

	.zhuige-users-block .zhuige-social-opt view {
		height: 60rpx;
		line-height: 60rpx;
		width: 140rpx;
		text-align: center;
		background: #F3f3f3;
		font-size: 28rpx;
		font-weight: 300;
		border-radius: 60rpx;
		color: #999999;
	}

	.zhuige-users-block .zhuige-social-opt view.active {
		background: #010101;
		color: #FFFFFF;
	}
</style>