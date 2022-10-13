<template>
	<view class="content">
		<uni-nav-bar :fixed="true" :statusBar="true" background-color="#F5F5F5">
			<view class="zhuige-top-bar">
				<template slot="left">
					<view v-if="logo" class="zhuige-top-logo">
						<image mode="heightFix" :src="logo"></image>
					</view>
				</template>
				<view class="zhuige-top-search" @click="openLink('/pages/base/search/search')">
					<uni-icons type="search" color="#999999" size="18"></uni-icons>
					<text>关键词...</text>
				</view>
			</view>
		</uni-nav-bar>

		<view v-if="slides && slides.length>0" class="zhuige-wide-box">
			<zhuige-swiper :items="slides"></zhuige-swiper>
		</view>

		<view v-if="icons && icons.length>0" class="zhuige-wide-box">
			<zhuige-icons type="scroll" :items="icons"></zhuige-icons>
		</view>

		<!-- 自定义滚动广告 圈子推荐 -->
		<zhuige-scroll-ad v-if="rec_ad" ext-ad-class="zhuige-scroll-coterie" :title="rec_ad.title"
			:items="rec_ad.items">
		</zhuige-scroll-ad>

		<zhuige-scroll-ad v-if="rec_forum" ext-ad-class="zhuige-scroll-coterie" :title="rec_forum.title"
			:items="rec_forum.forums">
		</zhuige-scroll-ad>

		<zhuige-tab v-if="lastLoaded || followLoaded" type="simple" :tabs="tabs" :cur-tab="cur_tab"
			@clickTab="clickTab"></zhuige-tab>

		<view v-if="cur_tab=='last'" class="zhuige-tab-block">
			<!-- 圈子列表 近期tab -->
			<view class="zhuige-social-list">
				<template v-if="lastTopics && lastTopics.length>0">
					<template v-for="(topic, index) in lastTopics">

						<!-- 用户列表推荐 滚动 -->
						<zhuige-user-list v-if="rec_user && rec_user.position==index" type="scroll"
							:title="rec_user.title" :users="rec_user.users" ext-class="zhuige-user-followed">
						</zhuige-user-list>

						<zhuige-topic :key="index" :topic="topic"></zhuige-topic>
					</template>
				</template>
				<template v-else-if="lastLoaded">
					<zhuige-nodata></zhuige-nodata>
				</template>
			</view>
		</view>

		<view v-else-if="cur_tab=='follow'" class="zhuige-tab-block">
			<!-- 关注的用户 -->
			<view class="zhugie-follow-box">
				<zhuige-user-list v-if="followUser && followUser.users.length>0" type="scroll" :title="followUser.title"
					:users="followUser.users" :buttons="false" ext-class="zhuige-user-followed"></zhuige-user-list>
			</view>
			<view class="zhuige-social-list-head">好友话题</view>

			<view class="zhuige-social-list">

				<template v-if="followTopics && followTopics.length>0">
					<zhuige-topic v-for="(topic, index) in followTopics" :key="index" :topic="topic"></zhuige-topic>
				</template>
				<template v-else-if="followLoaded">
					<zhuige-nodata></zhuige-nodata>
				</template>

			</view>
		</view>

		<uni-load-more v-if="cur_tab=='last' && lastTopics && lastTopics.length>0" :status="lastLoadMore">
		</uni-load-more>

		<uni-load-more v-if="cur_tab=='follow' && followTopics && followTopics.length>0" :status="followLoadMore">
		</uni-load-more>

	</view>
</template>

<script>
	import Util from '@/utils/util';
	import Alert from '@/utils/alert';
	import Api from '@/utils/api';
	import Rest from '@/utils/rest';

	import ZhuigeTopic from "@/components/zhuige-topic";
	import ZhuigeSwiper from "@/components/zhuige-swiper";
	import ZhuigeIcons from "@/components/zhuige-icons";
	import ZhuigeUserList from "@/components/zhuige-user-list";
	import ZhuigeScrollAd from "@/components/zhuige-scroll-ad";
	import ZhuigeTab from "@/components/zhuige-tab";
	import ZhuigeNodata from "@/components/zhuige-nodata";

	export default {
		data() {
			this.loginReload = false;

			return {
				logo: undefined,

				tabs: [{
						id: 'last',
						title: '近期'
					},
					{
						id: 'follow',
						title: '关注'
					},
				],
				cur_tab: 'last',

				slides: [],
				icons: [],
				rec_user: undefined,
				rec_forum: undefined,
				rec_ad: undefined,

				// 分享截图
				thumb: undefined,

				// 最新帖子
				lastTopics: [],
				lastLoadMore: 'more',
				lastLoaded: false,

				//关注的人
				followUser: undefined,

				// 关注的人的帖子
				followTopics: [],
				followLoadMore: 'more',
				followLoaded: false,
			}
		},

		components: {
			ZhuigeTopic,
			ZhuigeSwiper,
			ZhuigeIcons,
			ZhuigeUserList,
			ZhuigeScrollAd,
			ZhuigeTab,
			ZhuigeNodata
		},

		onLoad(options) {
			Util.addShareScore(options.source);

			this.refresh();

			uni.$on('zhuige_event_user_login', this.onSetReload);
			uni.$on('zhuige_event_follow_user', this.onFlollowUser);
			uni.$on('zhuige_event_user_like', this.onUserLike);
		},

		onUnload() {
			uni.$off('zhuige_event_user_like', this.onUserLike);
			uni.$off('zhuige_event_follow_user', this.onFlollowUser);
			uni.$off('zhuige_event_user_login', this.onSetReload);
		},

		onShow() {
			if (this.loginReload) {
				this.loginReload = false;

				this.refresh();
			}
		},

		onPullDownRefresh() {
			this.refresh();
		},

		onReachBottom() {
			if (this.cur_tab == 'last' && this.lastLoadMore == 'more') {
				this.loadLastTopic(false);
			} else if (this.cur_tab == 'follow' && this.followLoadMore == 'more') {
				this.loadFollowTopic(false);
			}
		},

		onShareAppMessage() {
			let params = {
				title: getApp().globalData.appName + '-' + getApp().globalData.appDesc,
				path: Util.addShareSource('pages/tabs/index/index?n=n')
			};

			if (this.thumb) {
				params.imageUrl = this.thumb;
			}

			return params;
		},

		// #ifdef MP-WEIXIN
		onShareTimeline() {
			return {
				title: getApp().globalData.appName + '-' + getApp().globalData.appDesc,
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

			/**
			 * 关注事件
			 */
			onFlollowUser(data) {
				if (this.rec_user && this.rec_user.users && this.rec_user.users.length > 0) {
					this.rec_user.users.forEach(user => {
						if (user.user_id == data.user_id) {
							user.is_follow = data.is_follow;
						}
					})
				}

				if (this.cur_tab == 'follow') {
					this.loadFollowTopic(true);
				} else {
					this.followLoaded = false;
				}
			},

			/**
			 * 点赞事件
			 */
			onUserLike(data) {
				console.log(data);
				if (this.lastTopics && this.lastTopics.length > 0) {
					this.lastTopics.forEach((topic) => {
						if (topic.id == data.post_id) {
							topic.like_count = data.like_count;
						}
					})
				}

				if (this.followTopics && this.followTopics.length > 0) {
					this.followTopics.forEach((topic) => {
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
				this.loadSetting();

				this.lastLoaded = false;
				this.followLoaded = false;

				if (this.cur_tab == 'last') {
					this.loadLastTopic(true);
				} else if (this.cur_tab == 'follow') {
					this.loadFollowTopic(true);
				}
			},

			/**
			 * 点击打开链接
			 */
			openLink(link) {
				Util.openLink(link);
			},

			/**
			 * 近期-关注 tab切换
			 */
			clickTab(tab) {
				this.cur_tab = tab.id;

				if (this.cur_tab == 'last' && !this.lastLoaded) {
					this.loadLastTopic(true);
				} else if (this.cur_tab == 'follow' && !this.followLoaded) {
					this.loadFollowTopic(true);
				}
			},

			/**
			 * 加载配置
			 */
			loadSetting() {
				Rest.post(Api.URL('setting', 'home')).then(res => {
					this.logo = res.data.logo;
					this.slides = res.data.slides;
					this.icons = res.data.icons;

					if (res.data.rec_user) {
						this.rec_user = res.data.rec_user;
					}

					if (res.data.rec_forum) {
						this.rec_forum = res.data.rec_forum;
					}

					if (res.data.rec_ad) {
						this.rec_ad = res.data.rec_ad;
					}

					if (res.data.thumb) {
						this.thumb = res.data.thumb;
					}

					uni.stopPullDownRefresh();
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 加载最新帖子
			 */
			loadLastTopic(refresh) {
				if (this.lastLoadMore == 'loading') {
					return;
				}
				this.lastLoadMore = 'loading'

				Rest.post(Api.URL('posts', 'list_last'), {
					offset: refresh ? 0 : this.lastTopics.length
				}).then(res => {
					this.lastTopics = refresh ? res.data.topics : this.lastTopics.concat(res.data.topics);
					this.lastLoadMore = res.data.more;
					this.lastLoaded = true;
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 加载关注人的帖子
			 */
			loadFollowTopic(refresh) {
				if (this.followLoadMore == 'loading') {
					return;
				}
				this.followLoadMore = 'loading'

				Rest.post(Api.URL('posts', 'list_follow'), {
					offset: refresh ? 0 : this.followTopics.length
				}).then(res => {
					if (res.data.follow_user) {
						this.followUser = res.data.follow_user;
					}
					this.followTopics = refresh ? res.data.topics : this.followTopics.concat(res.data.topics);
					this.followLoadMore = res.data.more;
					this.followLoaded = true;
				}, err => {
					console.log(err)
				});
			},
		}
	}
</script>

<style lang="scss">
	.zhuige-nav-logo {
		display: flex;
		align-items: center;
		margin-right: 15rpx;

		image {
			height: 48rpx;
			width: 128rpx;
		}
	}

	.zhuige-nav-search {
		display: flex;
		align-items: center;
		width: 80%;
		height: 32px;
		padding-left: 20rpx;
		color: #999999;
		font-size: 28rpx;
		border: 1rpx solid #999999;
		border-radius: 16px;
	}

	.zhugie-follow-box {
		padding: 0 20rpx;
	}

	.zhuige-tab-block {
		padding-bottom: 60rpx;
	}

	.zhuige-tab {
		padding: 10rpx 0 20rpx !important;
	}

	.zhuige-scroll-icon .text {
		color: #666666 !important;
	}

	.zhuige-icon .view {
		width: 18.6% !important;
	}
</style>
