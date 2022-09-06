<template>
	<view class="content">
		<!-- 顶部大图 -->
		<view v-if="user" class="zhuige-home-bg">
			<image mode="aspectFill" :src="user.cover"></image>
		</view>

		<!-- 用户信息 -->
		<view v-if="user" class="zhuige-user-card">
			<view class="zhuige-block">
				<view class="zhuige-user-title">
					<!-- 头像及认证 -->
					<view class="zhuige-user-info-block">
						<view>
							<image mode="aspectFill" :src="user.avatar"></image>
							<image v-if="user.vip" mode="aspectFill" src="/static/lvv.png"></image>
						</view>
						<view>
							<text>{{user.nickname}}</text>
							
							<!-- 
							<image v-if="user.certify && user.certify.status==1" mode="aspectFill" :src="user.certify.icon"></image>
						 -->
						</view>
					</view>
					<!-- 操作相关 -->
					<view class="zhuige-user-info-btn">
						<!-- <view>
							<uni-icons type="weixin" size="24"></uni-icons>
						</view> -->
						<button open-type="share">
							<uni-icons type="redo-filled" size="24"></uni-icons>
						</button>
						<template v-if="!user.is_me">
							<template v-if="user.is_follow">
								<view class="follow active" @click="clickFollowUser">{{user.is_fans?'互关':'已关注'}}</view>
							</template>
							<view v-else @click="clickFollowUser" class="follow">+关注</view>
						</template>
					</view>
				</view>
				<view v-if="stat" class="zhuige-user-count">
					<view @click="openLink('/pages/user/friend/friend?user_id=' + user.user_id + '&tab=follow')">
						<text>关注</text>
						<view>{{stat.follow_count}}</view>
					</view>
					<view @click="openLink('/pages/user/friend/friend?user_id=' + user.user_id + '&tab=fans')">
						<text>粉丝</text>
						<view>{{stat.fans_count}}</view>
					</view>
					<view>
						<text>圈子</text>
						<view>{{stat.forum_count}}</view>
					</view>
					<view>
						<text>获赞</text>
						<view>{{stat.likeme_count}}</view>
					</view>
				</view>
				<view v-if="user" class="zhuige-user-line">
					<image mode="aspectFill" src="../../../static/pen.png"></image>
					<text>签名：</text>
					<text>{{user.sign}}</text>
				</view>
				<view v-if="user.certify && user.certify.status==1" class="zhuige-user-line">
					<image mode="aspectFill" :src="user.certify.icon"></image>
					<text>认证：</text>
					<text>{{user.certify.name}}</text>
				</view>
			</view>
		</view>

		<!-- 推荐 -->
		<view v-if="rec_ad" class="zhuige-user-market">
			<zhuige-scroll-ad ext-ad-class="zhuige-scroll-goods-mini" :title="rec_ad.title" :items="rec_ad.items">
			</zhuige-scroll-ad>
		</view>

		<!-- tab -->
		<view class="zhuige-home-tab">
			<view class="zhuige-block">
				<zhuige-tab :tabs="tabs" :cur-tab="cur_tab" @clickTab="clickTab"></zhuige-tab>
			</view>
		</view>

		<!-- 帖子列表 -->
		<view class="zhuige-home-list">
			<template v-if="posts && posts.length>0">
				<zhuige-topic v-for="(topic, index) in posts" :key="index" :topic="topic"></zhuige-topic>
			</template>
			<template v-else-if="loaded">
				<zhuige-nodata :buttons="false" :tip="noDataTip"></zhuige-nodata>
			</template>
		</view>

		<uni-load-more v-if="posts && posts.length>0" :status="loadMore"></uni-load-more>

	</view>
</template>

<script>
	import Util from '@/utils/util';
	import Alert from '@/utils/alert';
	import Api from '@/utils/api';
	import Rest from '@/utils/rest';

	import ZhuigeTopic from "@/components/zhuige-topic";
	import ZhuigeScrollAd from "@/components/zhuige-scroll-ad";
	import ZhuigeTab from "@/components/zhuige-tab";
	import ZhuigeNodata from "@/components/zhuige-nodata";

	export default {
		data() {
			this.user_id = undefined;

			this.loginReload = false;

			return {
				user: undefined,
				stat: undefined,
				rec_ad: undefined,

				tabs: [{
						id: 'like',
						title: '点赞'
					},
					{
						id: 'fav',
						title: '收藏'
					},
					{
						id: 'comment',
						title: '评论'
					},
					{
						id: 'publish',
						title: '作品'
					},
				],
				cur_tab: 'like',

				posts: [],
				loadMore: 'more',
				loaded: false,
				
				noDataTip: '哇哦，什么也没有',
			}
		},

		components: {
			ZhuigeTopic,
			ZhuigeScrollAd,
			ZhuigeTab,
			ZhuigeNodata
		},

		onLoad(options) {
			Util.addShareScore(options.source);

			if (options.user_id) {
				this.user_id = options.user_id;
			}

			if (options.tab) {
				this.cur_tab = options.tab;
			}

			this.refresh();

			uni.$on('zhuige_event_follow_user', this.onFlollowUser);
			uni.$on('zhuige_event_user_like', this.onUserLike);
		},

		onUnload() {
			uni.$off('zhuige_event_user_like', this.onUserLike);
			uni.$off('zhuige_event_follow_user', this.onFlollowUser);
		},

		onShow() {
			if (this.loginReload) {
				this.loadData();
			}
		},

		onReachBottom() {
			if (this.loadMore == 'more') {
				this.loadPosts(false);
			}
		},

		onPullDownRefresh() {
			this.refresh();
		},

		onShareAppMessage() {
			let path = 'pages/user/home/home?n=n';

			if (this.user_id) {
				path += '&user_id=' + this.user_id
			}

			return {
				title: '个人主页-' + getApp().globalData.appName,
				path: Util.addShareSource(path)
			};
		},

		// #ifdef MP-WEIXIN
		onShareTimeline() {
			return {
				title: '个人主页-' + getApp().globalData.appName,
			};
		},
		// #endif

		methods: {
			//----- event start -----
			onFlollowUser(data) {
				if (data.from && data.from == 'user_home') {
					this.loadData();
				} else {
					this.loginReload = true;
				}
			},
			
			/**
			 * 点赞事件
			 */
			onUserLike(data) {
				if (this.posts && this.posts.length>0) {
					this.posts.forEach((topic) => {
						if (topic.id == data.post_id) {
							topic.like_count = data.like_count;
						}
					})
				}
			},
			// ------- event end ---------

			/**
			 * 刷新
			 */
			refresh() {
				this.loadData();
				this.loadPosts(true);
			},

			/**
			 * 点击打开链接
			 */
			openLink(link) {
				Util.openLink(link);
			},

			/**
			 * 点击切换TAB
			 */
			clickTab(tab) {
				this.cur_tab = tab.id;

				this.loadPosts(true);
			},

			/**
			 * 点击关注作者
			 */
			clickFollowUser() {
				Rest.post(Api.URL('user', 'follow_user'), {
					user_id: this.user_id
				}).then(res => {
					if (res.code == 0) {
						uni.$emit('zhuige_event_follow_user', {
							user_id: this.user_id,
							is_follow: res.data.is_follow,
							from: 'user_home'
						});
					} else {
						Alert.toast(res.message);
					}
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 加载数据
			 */
			loadData() {
				let params = {};
				if (this.user_id) {
					params.user_id = this.user_id;
				}

				Rest.post(Api.URL('user', 'home'), params).then(res => {
					this.user = res.data.user;
					this.stat = res.data.stat;
					if (res.data.rec_ad) {
						this.rec_ad = res.data.rec_ad;
					}

					uni.stopPullDownRefresh();
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 加载文章
			 */
			loadPosts(refresh) {
				Rest.post(Api.URL('posts', 'list_user'), {
					user_id: this.user_id,
					tab: this.cur_tab,
					offset: refresh ? 0 : this.posts.length
				}).then(res => {
					this.posts = refresh ? res.data.posts : this.posts.concat(res.data.posts);
					this.loadMore = 'nomore';
					this.loaded = true;
					this.noDataTip = res.data.tip;
				}, err => {
					console.log(err)
				});
			}
		}
	}
</script>

<style>
	page {
		background: #f5f5f5;
	}

	.zhuige-home-bg {
		height: 400rpx;
		width: 100%;
		position: relative;
	}

	.zhuige-home-bg image {
		height: 100%;
		width: 100%;
	}

	.zhuige-home-bg view {
		position: absolute;
		z-index: 2;
		height: 560rpx;
		width: 100%;
		background: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.7));
	}

	.zhuige-user-card {
		padding: 0 20rpx;
		margin-top: -100rpx;
		position: relative;
		z-index: 3;
	}

	.zhuige-user-title {
		display: flex;
		align-items: center;
		justify-content: space-between;
		position: relative;
		padding: 10rpx;
	}

	.zhuige-user-info-block {
		margin-top: -100rpx;
		position: relative;
		z-index: 6;
		padding-bottom: 20rpx;
	}

	.zhuige-user-info-block view:nth-child(1) {
		position: relative;
	}

	.zhuige-user-info-block view:nth-child(1) image:nth-child(1) {
		height: 128rpx;
		width: 128rpx;
		border-radius: 50%;
	}

	.zhuige-user-info-block view:nth-child(1) image:nth-child(2) {
		height: 64rpx;
		width: 64rpx;
		position: absolute;
		z-index: 9;
		right: 10rpx;
		bottom: 20rpx;
	}

	.zhuige-user-info-block view:nth-child(2) {
		display: flex;
		align-items: center;
		justify-content: center;
		line-height: 1.4em;
	}

	.zhuige-user-info-block view:nth-child(2) text {
		font-size: 36rpx;
		font-weight: 600;
	}

	.zhuige-user-info-block view:nth-child(2) image {
		height: 36rpx;
		width: 36rpx;
		margin-left: 12rpx;
	}

	.zhuige-user-info-btn {
		display: flex;
		align-items: center;
		margin-bottom: 20rpx;
	}

	.zhuige-user-info-btn view,
	.zhuige-user-info-btn button {
		height: 80rpx;
		line-height: 80rpx;
		text-align: center;
		width: 80rpx;
		padding: 0;
		border-radius: 40rpx;
		background: #f5f5f5;
		margin-left: 12rpx;
	}

	.zhuige-user-info-btn button::after {
		border: none;
	}

	.zhuige-user-info-btn view.follow {
		width: 160rpx;
		font-size: 28rpx;
		font-weight: 400;
		background: #010101;
		color: #ffffff;
	}

	.zhuige-user-info-btn view.active {
		background: #f5f5f5;
		color: #999999;
	}

	.zhuige-user-count {
		border-top: 1rpx solid #EEEEEE;
		padding: 30rpx 10rpx;
		display: flex;
		align-items: center;
	}

	.zhuige-user-count>view {
		width: 25%;
		display: flex;
		align-items: baseline;
	}

	.zhuige-user-count>view text {
		font-size: 26rpx;
		font-weight: 300;
	}

	.zhuige-user-count>view view {
		font-size: 46rpx;
		font-weight: 600;
		margin-left: 4rpx;
	}

	.zhuige-user-line {
		padding: 30rpx 10rpx;
		border-top: 1rpx solid #EEEEEE;
		display: flex;
		align-items: center;
	}

	.zhuige-user-line image {
		height: 28rpx;
		width: 28rpx;
		margin-right: 8rpx;
	}

	.zhuige-user-line text {
		font-size: 26rpx;
		font-weight: 400;
		margin-left: 4rpx;
	}

	.zhuige-user-market,
	.zhuige-home-list,
	.zhuige-home-tab {
		padding: 0 20rpx;
	}

	.zhuige-user-market {
		padding-bottom: 20rpx;
	}
</style>
