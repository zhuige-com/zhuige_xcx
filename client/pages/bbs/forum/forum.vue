<template>
	<view class="content">
		<uni-nav-bar leftIcon="back" @clickLeft="clickBack" :title="title" :fixed="true" :statusBar="true"
			:opacity="nav_opacity" :placeholder="false">
		</uni-nav-bar>
		
		<!-- #ifndef MP-BAIDU -->
		<view v-if="nav_opacity<0.01" class="zhuige-nav-back" :style="{top: statusBarHeight + 'px'}">
			<uni-icons type="back" size="24" color="#FFFFFF"></uni-icons>
		</view>
		<!-- #endif -->

		<!-- 圈子头部背景 -->
		<view v-if="forum" class="zhuige-topic-header">
			<view class="zhuige-topic-bg">
				<view class="zhuige-topic-mask"></view>
				<image mode="aspectFill" :src="forum.background"></image>
			</view>
		</view>

		<view class="zhuige-coterie-info">
			<!-- 圈子头部信息 -->
			<view class="zhuige-block">
				<view class="zhuige-classify-box">
					<view class="zhuige-classify-block">
						<view>
							<image mode="aspectFill" :src="forum.logo"></image>
						</view>
						<view class="zhuige-classify-text">
							<view>{{forum.name}}</view>
							<view>
								<text>{{forum.user_count}}成员</text>
								<text>/</text>
								<text>{{forum.post_count}}作品</text>
							</view>
						</view>
					</view>
					<view @click="clickJoinForum" class="zhuige-classify-join">
						<view v-if="forum.is_follow">已加入</view>
						<view v-else>+加入</view>
					</view>
				</view>

				<!-- 公告/推荐/位置 -->
				<view v-if="forum.notice" class="zhuige-coterie-line" @click="clickNotice">
					<view :style="'white-space: ' + (show_notice?'inherit':'nowrap') + ';'">
						<text>公告</text>
						<text>{{forum.notice}}</text>
					</view>
					<view>
						<uni-icons :type="show_notice?'bottom':'right'" color="#aaaaaa" size="20"></uni-icons>
					</view>
				</view>

				<view v-if="forum.ad_link && forum.ad_link.title" class="zhuige-coterie-line"
					@click="openLink(forum.ad_link.link)">
					<view>
						<text>推荐</text>
						<text class="single-line">{{forum.ad_link.title}}</text>
					</view>
					<view>
						<uni-icons type="right" color="#aaaaaa" size="20"></uni-icons>
					</view>
				</view>

				<view v-if="forum.location && (forum.location.marker || forum.location.address)" @click="clickLocation"
					class="zhuige-coterie-line">
					<view>
						<text>位置</text>
						<text>{{forum.location.marker}}</text>
					</view>
					<view>
						<uni-icons type="right" color="#aaaaaa" size="20"></uni-icons>
					</view>
				</view>
			</view>
		</view>

		<view v-if="forum && forum.users.length>0" class="zhuige-coterie-user">
			<view class="zhuige-block">
				<view class="zhuige-block-head">
					<view>圈子成员</view>
					<view @click="openLink('/pages/bbs/forum-users/forum-users?forum_id=' + forum.id)">查看更多</view>
				</view>

				<!-- 用户列表模块 滚动模式 -->
				<zhuige-scroll-user :users="forum.users"></zhuige-scroll-user>
			</view>
		</view>

		<view v-if="forum && forum.ad_imgs" class="zhuige-cust-wide-block">
			<zhuige-scroll-ad boxClass="zhuige-scroll-goods" :title="forum.ad_imgs.title" :items="forum.ad_imgs.items">
			</zhuige-scroll-ad>
		</view>

		<!-- 自定义滚动广告 -->
		<view v-if="forum && forum.ad_custom && forum.ad_custom.length>0" class="zhuige-detail-ad">
			<zhuige-scroll-ad ext-ad-class="zhuige-scroll-goods-mini" :items="forum.ad_custom"></zhuige-scroll-ad>
		</view>

		<view class="zhuige-tab-block">
			<!-- 圈子列表 近期tab -->
			<view class="zhuige-social-list">
				<template v-if="topics && topics.length>0">
					<template v-for="(topic, index) in topics">
						<!-- #ifdef MP-WEIXIN -->
						<view class="zhuige-block zhuige-ad-cust"
							v-if="traffic_list && traffic_list.frequency>0 && (index+1)%traffic_list.frequency==0">
							<view class="zhuige-ad-cust-title">{{traffic_list.title}}</view>
							<ad-custom :unit-id="traffic_list.ad"></ad-custom>
							<view class="zhuige-ad-cust-footer">
								<text>{{traffic_list.desc}}</text>
							</view>
						</view>
						<!-- #endif -->
						<zhuige-topic :key="index" :topic="topic"></zhuige-topic>
					</template>
				</template>
				<template v-else>
					<zhuige-nodata v-if="loaded"></zhuige-nodata>
				</template>
			</view>

			<uni-load-more v-if="topics && topics.length>0" :status="loadMore"></uni-load-more>
		</view>

		<!-- 浮动发布 -->
		<view @click="clickPost" class="zhuige-coterie-post">
			<uni-icons type="plusempty" color="#FFFFFF" size="28"></uni-icons>
		</view>

		<!-- 底部自定义菜单 -->
		<view v-if="forum && forum.ad_menu && forum.ad_menu.length>0" class="zhuige-coterie-menu">
			<view v-for="(item, index) in forum.ad_menu" :key="index" @click="openLink(item.link)">{{item.title}}</view>
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

	import ZhuigeTopic from "@/components/zhuige-topic";
	import ZhuigeScrollUser from "@/components/zhuige-scroll-user";
	import ZhuigeScrollAd from "@/components/zhuige-scroll-ad";
	import ZhuigeTab from "@/components/zhuige-tab";
	import ZhuigeNodata from "@/components/zhuige-nodata";

	export default {
		data() {
			this.forum_id = undefined;

			// 是否需要重新加载圈子信息
			this.reloadDetail = false;

			return {
				nav_opacity: 0,
				statusBarHeight: 0,

				show_notice: false,

				forum: undefined,

				topics: [],
				loadMore: 'more',
				loaded: false,

				// #ifdef MP-WEIXIN
				traffic_list: undefined,
				// #endif
			}
		},

		computed: {
			title() {
				if (this.forum) {
					return this.forum.name;
				}

				return '圈子';
			}
		},

		components: {
			ZhuigeTopic,
			ZhuigeScrollUser,
			ZhuigeScrollAd,
			ZhuigeTab,
			ZhuigeNodata
		},

		onLoad(options) {
			if (options.id) {
				options.forum_id = options.id;
			}

			if (!options.forum_id) {
				uni.reLaunch({
					url: '/pages/tabs/index/index'
				})
				return;
			}

			Util.addShareScore(options.source);

			this.statusBarHeight = uni.getSystemInfoSync().statusBarHeight;

			this.forum_id = options.forum_id;
			this.refresh();

			uni.$on('zhuige_event_user_join_forum', this.onSetReloadDetail);
			uni.$on('zhuige_event_user_like', this.onUserLike);
		},

		onUnload() {
			uni.$off('zhuige_event_user_like', this.onUserLike);
			uni.$off('zhuige_event_user_join_forum', this.onSetReloadDetail);
		},

		onShow() {
			if (this.reloadDetail) {
				this.reloadDetail = false;

				this.loadDetail();
			}
		},

		onPageScroll(e) {
			this.nav_opacity = (e.scrollTop > 255 ? 255 : e.scrollTop) / 255;
			if (e.scrollTop > 20) {
				uni.setNavigationBarColor({
					frontColor: '#000000',
					backgroundColor: '#ffffff',
				})
			} else {
				uni.setNavigationBarColor({
					frontColor: '#ffffff',
					backgroundColor: '#ffffff'
				})
			}
		},

		onReachBottom() {
			if (this.loadMore == 'more') {
				this.loadTopics(false);
			}
		},

		onPullDownRefresh() {
			this.refresh();
		},

		onShareAppMessage() {
			return {
				title: this.forum.name + '-' + getApp().globalData.appName,
				path: Util.addShareSource('pages/bbs/forum/forum?forum_id=' + this.forum_id)
			};
		},

		// #ifdef MP-WEIXIN
		onShareTimeline() {
			return {
				title: this.forum.name + '-' + getApp().globalData.appName,
			};
		},
		// #endif

		methods: {
			//----- event start -----
			/**
			 * 登录事件
			 */
			onSetReloadDetail(data) {
				this.reloadDetail = true;
			},

			/**
			 * 点赞事件
			 */
			onUserLike(data) {
				if (this.topics && this.topics.length > 0) {
					this.topics.forEach((topic) => {
						if (topic.id == data.post_id) {
							topic.like_count = data.like_count;
						}
					})
				}
			},
			//----- event end -----

			/**
			 * 刷新
			 */
			refresh() {
				this.loadDetail();
				this.loadTopics(true);
			},

			/**
			 * 返回上一页
			 */
			clickBack() {
				Util.navigateBack();
			},

			/**
			 * 打开链接
			 */
			openLink(link) {
				Util.openLink(link);
			},

			/**
			 * 公告
			 */
			clickNotice() {
				this.show_notice = !this.show_notice;
			},

			/**
			 * 点击加入圈子
			 */
			clickJoinForum() {
				Rest.post(Api.URL('user', 'follow_forum'), {
					forum_id: this.forum_id,
					user: 1,
				}).then(res => {
					if (res.code == 0) {
						this.forum.is_follow = res.data.is_follow;
						this.forum.user_count = res.data.user_count;
						this.forum.users = res.data.users;

						uni.$emit('zhuige_event_user_join_forum', {
							forum_id: this.forum_id,
							user_count: res.data.user_count,
						});
						this.reloadDetail = false;
					} else {
						Alert.toast(res.message);
					}
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 点击发布
			 */
			clickPost() {
				Util.openLink('/pages/bbs/post/post?type=image&fid=' + this.forum.id + '&fname=' + this.forum.name);
			},

			/**
			 * 点击打开位置
			 */
			clickLocation() {
				uni.openLocation({
					name: this.forum.location.marker,
					address: this.forum.location.address,
					latitude: parseFloat(this.forum.location.latitude),
					longitude: parseFloat(this.forum.location.longitude),
					success: () => {
						console.log('success');
					}
				});
			},

			/**
			 * 加载圈子详情
			 */
			loadDetail() {
				Rest.post(Api.URL('bbs', 'forum_detail'), {
					forum_id: this.forum_id
				}).then(res => {
					this.forum = res.data.forum;

					// #ifdef MP-WEIXIN
					if (res.data.traffic_list) {
						this.traffic_list = res.data.traffic_list;
					}
					// #endif

					uni.stopPullDownRefresh();
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 加载帖子
			 */
			loadTopics(refresh) {
				let url = '';
				let params = {
					offset: refresh ? 0 : this.topics.length
				};

				url = Api.URL('bbs', 'topic_list_forum');
				params.forum_id = this.forum_id;
				Rest.post(url, params).then(res => {
					this.topics = refresh ? res.data.topics : this.topics.concat(res.data.topics);
					this.loadMore = res.data.more;
					this.loaded = true;
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

	.content {
		padding-bottom: 180rpx;
	}

	.zhuige-nav-back {
		position: fixed;
		top: 0;
		padding: 0 20rpx;
		z-index: 99;
	}

	.zhuige-topic-header,
	.zhuige-topic-bg,
	.zhuige-topic-mask {
		height: 480rpx;
	}

	.zhuige-topic-bg image {
		height: 560rpx;
	}

	.zhuige-classify-box {
		margin-top: -100rpx;
	}

	.zhuige-classify-join {
		margin-top: -84rpx;
	}

	.zhuige-classify-text view {
		color: #FFFFFF;
		height: 70rpx;
	}

	.zhuige-coterie-line {
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 12rpx 0;
		border-bottom: 1rpx solid #EEEEEE;
	}

	.zhuige-coterie-line:last-of-type {
		border: none;
	}

	.zhuige-coterie-line view:nth-child(1) {
		width: 92%;
		display: flex;
		align-items: center;
	}

	.zhuige-coterie-line view:nth-child(1) text:nth-child(1) {
		font-size: 22rpx;
		font-weight: 300;
		color: #FFFFFF;
		background: #010101;
		padding: 0 24rpx;
		height: 2em;
		line-height: 2em;
		border-radius: 36rpx;
		margin-right: 12rpx;
		white-space: nowrap;
	}

	.zhuige-coterie-line view:nth-child(1) text:nth-child(2) {
		font-size: 28rpx;
		font-weight: 400;
		padding: 4rpx 0;
		width: 660rpx;
		overflow: hidden;
		text-overflow: ellipsis;
	}

	.single-line {
		white-space: nowrap;
	}

	.zhuige-detail-ad {
		padding: 0 20rpx;
	}

	.zhuige-coterie-post {
		position: fixed;
		right: 30rpx;
		bottom: 280rpx;
		height: 80rpx;
		line-height: 80rpx;
		width: 80rpx;
		border-radius: 80rpx;
		text-align: center;
		background: #010101;
	}

	.zhuige-coterie-menu {
		display: flex;
		position: fixed;
		align-items: center;
		bottom: 0;
		z-index: 19;
		width: 100%;
		height: 80rpx;
		line-height: 60rpx;
		padding: 20rpx 0 40rpx;
		justify-content: center;
		background: #FFFFFF;
		box-shadow: 0rpx 0rpx 6rpx rgba(66, 66, 66, 0.1);
	}

	.zhuige-coterie-menu view {
		width: 25%;
		text-align: center;
		font-size: 28rpx;
		font-weight: 400;
		border-right: 1rpx solid #EEEEEE;
	}

	.zhuige-user-name {
		font-size: 26rpx;
		font-weight: 400;
	}

	.zhuige-cust-wide-block {
		padding: 0 20rpx 20rpx;
	}

	.zhuige-cust-wide-block .zhuige-scroll-ad {
		margin-top: -20rpx;
	}

	.zhuige-cust-wide-block .zhuige-scroll-ad-block {
		width: 36% !important;
		vertical-align: text-top;
	}

	.zhuige-cust-wide-block .zhuige-scroll-ad-block .zhuige-scroll-ad-cover {
		height: 240rpx !important;
	}

	.zhuige-cust-wide-block .zhuige-scroll-ad-info .title-info {
		font-size: 28rpx !important;
		font-weight: 600 !important;
	}

	.zhuige-cust-wide-block .price-unit {
		display: none;
	}

	.zhuige-cust-wide-block .price-info {
		padding: 0 !important;
	}

	.zhuige-cust-wide-block .price-info .item-price {
		font-size: 26rpx !important;
		color: #ff4400;
	}

	.zhuige-cust-wide-block .zhuige-scroll-ad-info {
		padding-bottom: 0 !important;
	}
</style>