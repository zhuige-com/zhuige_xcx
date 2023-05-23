<template>
	<view class="content">

		<view class="zhugie-user-box">
			<view v-if="owner" class="zhuige-block zhuige-forum-master">
				<view class="zhuige-block-head">
					<view>圈主</view>
				</view>
				<view class="zhuige-forum-user-list">
					<view class="zhuige-forum-user" @click="clickUser(owner.user_id)">
						<view>
							<view>
								<image mode="aspectFill" :src="owner.avatar"></image>
							</view>
							<text>{{owner.nickname}}</text>
						</view>
						<view @click.stop="clickFollowUser(owner.user_id)">
							<view class="active" v-if="owner.is_follow && owner.is_fans">互关</view>
							<view class="active" v-else-if="owner.is_follow">已关注</view>
							<view v-else>+关注</view>
						</view>
					</view>
				</view>
			</view>
			<view class="zhuige-block">
				<view class="zhuige-block-head">
					<view>圈内成员</view>
				</view>
				<view class="zhuige-forum-user-list">
					<view v-for="(user, index) in users" :key="index" @click="clickUser(user.user_id)"
						class="zhuige-forum-user">
						<view>
							<view>
								<image mode="aspectFill" :src="user.avatar"></image>
							</view>
							<text>{{user.nickname}}</text>
						</view>
						<view @click.stop="clickFollowUser(user.user_id)">
							<view class="active" v-if="user.is_follow && user.is_fans">互关</view>
							<view class="active" v-else-if="user.is_follow">已关注</view>
							<view v-else>+关注</view>
						</view>
					</view>
				</view>
			</view>

			<uni-load-more :status="loadMore"></uni-load-more>
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

	export default {
		data() {
			this.loginReload = false;

			return {
				forum_id: undefined,

				owner: undefined,

				users: [],
				loadMore: 'more',
			};
		},

		onLoad(options) {
			Util.addShareScore(options.source);

			if (options.forum_id) {
				this.forum_id = options.forum_id;
			} else {
				this.forum_id = 1;
			}

			this.loadUsers(true);

			uni.$on('zhuige_event_user_login', this.onSetReload);
			uni.$on('zhuige_event_follow_user', this.onFlollowUser);
		},

		onUnload() {
			uni.$off('zhuige_event_follow_user', this.onFlollowUser);
			uni.$off('zhuige_event_user_login', this.onSetReload);
		},

		onShow() {
			if (this.loginReload) {
				this.loginReload = false;

				this.loadUsers(true);
			}
		},

		onReachBottom() {
			if (this.loadMore == 'more') {
				this.loadUsers(false);
			}
		},

		onShareAppMessage() {
			return {
				title: '圈子成员-' + getApp().globalData.appName,
				path: Util.addShareSource('pages/bbs/forum-users/forum-users?forum_id=' + this.forum_id)
			};
		},

		// #ifdef MP-WEIXIN
		onShareTimeline() {
			return {
				title: '圈子成员-' + getApp().globalData.appName,
			};
		},
		// #endif

		methods: {
			//----- event start -----
			/**
			 * 关注用户事件
			 */
			onFlollowUser(data) {
				if (this.owner && this.owner.user_id == data.user_id) {
					this.owner.is_follow = data.is_follow;
				}

				if (this.users && this.users.length > 0) {
					this.users.forEach(user => {
						if (user.user_id == data.user_id) {
							user.is_follow = data.is_follow;
						}
					})
				}
			},

			/**
			 * 登录事件
			 */
			onSetReload(data) {
				this.loginReload = true;
			},
			//----- event end -----

			/**
			 * 打开用户主页
			 */
			clickUser(user_id) {
				Util.openLink('/pages/user/home/home?user_id=' + user_id);
			},

			/**
			 * 关注用户
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
			 * 加载圈子成员
			 */
			loadUsers(refresh) {
				if (this.loadMore == 'loading') {
					return;
				}
				this.loadMore = 'loading';

				Rest.post(Api.URL('bbs', 'forum_users'), {
					forum_id: this.forum_id,
					offset: (refresh ? 0 : this.users.length)
				}).then(res => {
					if (res.data.owner) {
						this.owner = res.data.owner;
					}

					this.users = (refresh ? res.data.users : this.users.concat(res.data.users));
					this.loadMore = res.data.more;
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

	.zhuige-forum-master {
		margin-bottom: 16rpx;
	}

	.zhuige-forum-user-list {
		padding: 0;
	}

	.zhuige-forum-user {
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 30rpx 0;
		border-top: 1rpx solid #EEEEEE;
	}

	.zhuige-forum-user:first-of-type {
		border: none;
		padding-top: 0;
	}

	.zhuige-forum-user>view:nth-child(1) {
		display: flex;
		align-items: center;
	}

	.zhuige-forum-user>view:nth-child(1) view {
		height: 72rpx;
		width: 72rpx;
		margin-right: 12rpx;
	}

	.zhuige-forum-user>view:nth-child(1) view image {
		height: 72rpx;
		width: 72rpx;
		border-radius: 50%;
	}

	.zhuige-forum-user>view:nth-child(1) view text {
		font-size: 32rpx;
		font-weight: 600;
	}

	.zhuige-forum-user>view:nth-child(2) {
		font-size: 28rpx;
	}

	.zhuige-forum-user>view:nth-child(2).active {
		color: #999999;
	}

	.zhugie-user-box {
		padding: 0 20rpx;
	}
</style>