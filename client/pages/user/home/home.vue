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
						</view>
						<view>
							<text>{{user.nickname}}</text>
							<image v-if="user.vip && user.vip.status==1" mode="aspectFit" :src="user.vip.icon"></image>
						</view>
					</view>
					<!-- 操作相关 -->
					<view class="zhuige-user-info-btn">
						<!-- 分享先隐藏 -->
						<!-- <button open-type="share">
							<uni-icons type="redo-filled" size="24"></uni-icons>
						</button> -->

						<!-- 新增私信 -->
						<view v-if="btn_message"
							@click="openLink('/pages/message/detail/detail?user_id=' + user.user_id)">
							<uni-icons type="chatboxes-filled" color="#010101" size="24"></uni-icons>
						</view>

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
					<image mode="aspectFill" src="/static/pen.png"></image>
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
				<template v-for="(topic, index) in posts">
					<zhuige-topic v-if="topic.post_type=='zhuige_bbs_topic'" :key="index" :topic="topic"
						:trash="cur_tab=='publish' && user.delete_topic==1" @deleteTopic="onDeleteTopic">
					</zhuige-topic>
					<view v-else :key="index" class="zhuige-block" :class="topic.post_type" @click="clickPost(topic)">
						<!-- 投票 -->
						<template v-if="topic.post_type=='zhuige_vote'">
							<!-- 用户信息 -->
							<view class="zhuige-social-poster-blcok">
								<view class="zhuige-social-poster">
									<view class="zhuige-social-poster-avatar">
										<image mode="aspectFill" :src="topic.author.avatar"></image>
									</view>
									<view class="zhuige-social-poster-info">
										<view>
											<text>{{topic.author.nickname}}</text>
											<image v-if="topic.author.certify && topic.author.certify.status==1"
												mode="aspectFill" :src="topic.author.certify.icon">
											</image>
											<image class="zhuige-social-vip"
												v-if="topic.author.vip && topic.author.vip.status==1" mode="aspectFill"
												:src="topic.author.vip.icon">
											</image>
										</view>
										<view>
											<text>{{topic.time}}</text>
											<text v-if="topic.author.certify && topic.author.certify.status==1">/</text>
											<text
												v-if="topic.author.certify && topic.author.certify.status==1">{{topic.author.certify.name}}</text>
										</view>
									</view>
								</view>
								<view class="zhuige-social-opt social-dell"
									v-if="cur_tab=='publish' && user.delete_vote==1"
									@click.stop="clickDeleteVote(topic)">
									<uni-icons type="trash" color="#FF6146"></uni-icons>
								</view>
							</view>

							<!-- 话题 + 正文 -->
							<view class="zhuige-social-cont">
								<!-- 正文信息 -->
								<text>{{topic.excerpt}}</text>
							</view>

							<!-- pk模块 -->
							<view v-if="topic.type=='pk'" class="zhuige-pkvote">
								<view class="zhuige-vote-info">
									<view class="zhuige-vote-msg">
										<text class="zhuige-vote-num">{{topic.count}}</text>
										人参与投票，截止时间
										<text class="zhuige-vote-num">{{topic.deadline}}</text>
									</view>
									<text v-if="topic.is_end==1" class="vote-end">(已结束)</text>
								</view>
								<view class="zhuige-vote-data">
									<view class="zhuige-vote-count">
										<view v-if="topic.my_vote==1 || topic.is_end==1" class="zhuige-vote-count-num">
											{{topic.option_a.rate}}%{{topic.option_a.xuan==1?'（已投）':''}}
										</view>
										<image class="zhuige-vote-img" mode="aspectFill" :src="topic.option_a.image">
										</image>
									</view>
									<view class="zhuige-vote-count">
										<view v-if="topic.my_vote==1 || topic.is_end==1" class="zhuige-vote-count-num">
											{{topic.option_b.rate}}%{{topic.option_b.xuan==1?'（已投）':''}}
										</view>
										<image class="zhuige-vote-img" mode="aspectFill" :src="topic.option_b.image">
										</image>
									</view>
								</view>
								<view class="zhuige-vote-opt">
									<view class="vote-opt1box">
										<view class="vote-option1">{{topic.option_a.title}}</view>
									</view>
									<view class="vote-opt2box">
										<view class="vote-option2">{{topic.option_b.title}}</view>
									</view>
								</view>
							</view>
							<!-- pk模块 end -->

							<!-- 投票模块 -->
							<view v-if="topic.type=='single' || topic.type=='multi'" class="zhuige-pklist">
								<view class="zhuige-vote-info">
									<view class="zhuige-vote-msg">
										<template v-if="topic.type=='single'">(单选)</template>
										<template v-else-if="topic.type=='multi'">(多选)</template>
										<text class="zhuige-vote-num">{{topic.count}} </text>
										人参与投票，截止时间
										<text class="zhuige-vote-num"> {{topic.deadline}}</text>
									</view>
									<text v-if="topic.is_end==1" class="vote-end">(已结束)</text>
								</view>
								<view class="zhuige-vote-list">
									<view v-for="(item, index) in topic.options" :key="index" class="zhuige-vote-option"
										:class="item.xuan==1?'vote-check':''">
										<view class="zhuige-vote-option-text">
											{{item.title}}
											<text v-if="item.xuan==1" class="active">已投</text>
										</view>
										<view v-if="topic.my_vote==1 || topic.is_end==1"
											class="zhuige-vote-option-count">{{item.count}} 票 {{item.rate}}%
										</view>
									</view>
								</view>
							</view>
							<!-- 投票模块 end -->
						</template>
					</view>
				</template>
			</template>
			<template v-else-if="loaded">
				<zhuige-nodata :tip="noDataTip"></zhuige-nodata>
			</template>
		</view>

		<uni-load-more v-if="posts && posts.length>0" :status="loadMore"></uni-load-more>

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

				// 是否显示私信按钮
				btn_message: false,
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
			/**
			 * 关注用户事件
			 */
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
				if (this.posts && this.posts.length > 0) {
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
			 * 点击文章
			 */
			clickPost(post) {
				if (post.driect_link_switch == '1') {
					Util.openLink(post.driect_link);
				} else {
					Util.openLink(post.link + '?id=' + post.id);
				}
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

					if (res.data.btn_message) {
						this.btn_message = res.data.btn_message;
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
					this.loadMore = res.data.more;
					this.loaded = true;
					this.noDataTip = res.data.tip;
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 删除帖子
			 */
			onDeleteTopic(topic) {
				Rest.post(Api.URL('bbs', 'topic_delete'), {
					topic_id: topic.id
				}).then(res => {
					if (res.code != 0) {
						Alert.error(res.message);
						return;
					}

					let newPosts = [];
					this.posts.forEach(ele => {
						if (topic.id != ele.id) {
							newPosts.push(ele);
						}
					})
					this.posts = newPosts;
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 删除投票
			 */
			clickDeleteVote(vote) {
				Rest.post(Api.URL('vote', 'delete'), {
					vote_id: vote.id
				}).then(res => {
					if (res.code != 0) {
						Alert.error(res.message);
						return;
					}

					let newPosts = [];
					this.posts.forEach(ele => {
						if (vote.id != ele.id) {
							newPosts.push(ele);
						}
					})
					this.posts = newPosts;
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
		max-width: 380rpx;
		flex-wrap: nowrap;
	}

	.zhuige-user-info-block view:nth-child(2) text {
		font-size: 36rpx;
		font-weight: 600;
		height: 1.4em;
		line-height: 1.4em;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.zhuige-user-info-block view:nth-child(2) image {
		height: 28rpx;
		width: 56rpx;
		min-width: 56rpx;
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