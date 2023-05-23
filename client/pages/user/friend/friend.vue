<template>
	<view class="content">
		<view class="zhuige-friends">
			<view class="zhuige-friends-box">
				<zhuige-tab :tabs="tabs" :cur-tab="cur_tab" @clickTab="clickTab"></zhuige-tab>
			</view>

			<view v-if="cur_tab=='follow'" class="zhuige-block">
				<!-- 用户卡 基于发帖用户信息，增加用户列表专用 zhuige-friends-block -->
				<template v-if="follows.length>0">
					<view v-for="(user, index) in follows" :key="index"
						@click="openLink('/pages/user/home/home?user_id=' + user.user_id)"
						class="zhuige-social-poster-blcok zhuige-friends-block">
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
									<image class="zhuige-social-vip" v-if="user.vip && user.vip.status==1"
										mode="aspectFit" :src="user.vip.icon"></image>
								</view>
								<view>
									<text>作品 {{user.post_count}}</text>
									<text>/</text>
									<text>粉丝 {{user.fans_count}}</text>
								</view>
							</view>
						</view>
						<view class="zhuige-social-opt" @click.stop="clickFollowUser(user.user_id)">
							<view v-if="user.is_follow && user.is_fans">已互关</view>
							<view v-else>已关注</view>
						</view>
					</view>
				</template>
				<template v-else>
					<zhuige-nodata v-if="loadedFollow" :tip="noDataTip"></zhuige-nodata>
				</template>
			</view>

			<uni-load-more v-if="cur_tab=='follow' && follows.length>0" :status="loadMoreFollow"></uni-load-more>

			<view v-if="cur_tab=='fans'" class="zhuige-block">
				<!-- 用户卡 基于发帖用户信息，增加用户列表专用 zhuige-friends-block -->
				<template v-if="fans.length>0">
					<view v-for="(user, index) in fans" :key="index"
						@click="openLink('/pages/user/home/home?user_id=' + user.user_id)"
						class="zhuige-social-poster-blcok zhuige-friends-block">
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
									<image class="zhuige-social-vip" v-if="user.vip && user.vip.status==1"
										mode="aspectFit" :src="user.vip.icon"></image>
								</view>
								<view>
									<text>作品 {{user.post_count}}</text>
									<text>/</text>
									<text>粉丝 {{user.fans_count}}</text>
								</view>
							</view>
						</view>
						<view class="zhuige-social-opt" @click.stop="clickFollowUser(user.user_id)">
							<view v-if="user.is_follow && user.is_fans">互关</view>
							<view v-else class="active">+关注</view>
						</view>
					</view>
				</template>
				<template v-else>
					<zhuige-nodata v-if="loadedFan" :tip="noDataTip"></zhuige-nodata>
				</template>
			</view>

			<uni-load-more v-if="cur_tab=='fans' && fans.length>0" :status="loadMoreFan"></uni-load-more>

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

	import ZhuigeTab from "@/components/zhuige-tab";
	import ZhuigeNodata from "@/components/zhuige-nodata";

	export default {
		data() {
			this.loginReload = false;

			return {
				tabs: [{
						id: 'follow',
						title: '关注'
					},
					{
						id: 'fans',
						title: '粉丝'
					},
				],
				cur_tab: 'follow',

				loadMoreFollow: 'more',
				follows: [],
				loadedFollow: false,

				loadMoreFan: 'more',
				fans: [],
				loadedFan: false,

				noDataTip: '哇哦，什么也没有',
			}
		},

		components: {
			ZhuigeTab,
			ZhuigeNodata
		},

		onLoad(options) {
			Util.addShareScore(options.source);

			if (options.user_id) {
				this.user_id = options.user_id;
			}

			if (options.tab == 'fans') {
				this.cur_tab = 'fans';
				this.loadFans();
			} else {
				this.loadFollows();
			}

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

				this.refresh();
			}

			// #ifdef MP-BAIDU
			swan.setPageInfo({
				title: '粉丝/关注_' + getApp().globalData.appName,
				keywords: getApp().globalData.appKeywords,
				description: getApp().globalData.appDesc,
			});
			// #endif
		},

		onShareAppMessage() {
			let path = 'pages/user/friend/friend?n=n';
			if (this.user_id) {
				path += '&user_id=' + this.user_id;
			}
			return {
				title: '粉丝/关注-' + getApp().globalData.appName,
				path: Util.addShareSource(path)
			};
		},

		// #ifdef MP-WEIXIN
		onShareTimeline() {
			return {
				title: '粉丝/关注-' + getApp().globalData.appName,
			};
		},
		// #endif

		onReachBottom() {
			if (this.cur_tab == 'fans') {
				if (this.loadMoreFan == 'more') {
					this.loadFans();
				}
			} else { //follow
				if (this.loadMoreFollow == 'more') {
					this.loadFollows();
				}
			}
		},

		methods: {
			//----- event start -----
			/**
			 * 关注用户事件
			 */
			onFlollowUser(data) {
				this.refresh();
			},

			/**
			 * 重新加载事件
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
			 * 刷新
			 */
			refresh() {
				this.follows = [];
				this.loadMoreFollow = 'more';
				this.loadedFollow = false;

				this.fans = [];
				this.loadMoreFan = 'more';
				this.loadedFan = false;

				if (this.cur_tab == 'fans') {
					this.loadFans();
				} else { //follow
					this.loadFollows();
				}
			},

			/**
			 * 切换TAB
			 */
			clickTab(tab) {
				this.cur_tab = tab.id;

				if (this.cur_tab == 'fans') {
					if (this.fans.length == 0) {
						this.loadFans();
					}
				} else { //follow
					if (this.follows.length == 0) {
						this.loadFollows();
					}
				}
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
			 * 加载关注者
			 */
			loadFollows() {
				if (this.loadMoreFollow == 'loading') {
					return;
				}
				this.loadMoreFollow = 'loading';

				let params = {
					offset: this.follows.length
				};
				if (this.user_id) {
					params.user_id = this.user_id;
				}
				Rest.post(Api.URL('user', 'my_follows'), params).then(res => {
					this.follows = this.follows.concat(res.data.users);
					this.loadMoreFollow = res.data.more;
					this.loadedFollow = true;

					if (res.data.tip) {
						this.noDataTip = res.data.tip;
					} else {
						this.noDataTip = undefined;
					}
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 加载粉丝
			 */
			loadFans() {
				if (this.loadMoreFan == 'loading') {
					return;
				}
				this.loadMoreFan = 'loading';

				let params = {
					offset: this.fans.length
				};
				if (this.user_id) {
					params.user_id = this.user_id;
				}
				Rest.post(Api.URL('user', 'my_fans'), params).then(res => {
					this.fans = this.fans.concat(res.data.users);
					this.loadMoreFan = res.data.more;
					this.loadedFan = true;

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

	.zhuige-friends {
		padding: 0 20rpx;
	}

	.zhuige-friends-block {
		padding: 30rpx 0;
		border-bottom: 1rpx solid #EEEEEE;
	}

	.zhuige-friends-block:last-of-type {
		border: none;
	}

	.zhuige-social-opt {
		display: flex;
		flex-direction: row-reverse;
		width: 22% !important;
	}

	.zhuige-friends-block .zhuige-social-opt view {
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

	.zhuige-friends-block .zhuige-social-opt view.active {
		background: #010101;
		color: #FFFFFF;
	}

	.zhuige-friends-box {
		background: #FFFFFF;
		border-radius: 12rpx;
		margin-bottom: 20rpx;
	}
</style>