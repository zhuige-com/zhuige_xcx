<template>
	<view class="content">
		<uni-nav-bar leftIcon="back" @clickLeft="clickBack" title="个人主页" :fixed="true" :statusBar="true"
			:opacity="nav_opacity" :placeholder="false">
		</uni-nav-bar>

		<view v-if="nav_opacity<0.01" class="zhuige-nav-back" :style="{top: statusBarHeight + 'px'}">
			<uni-icons type="back" size="24" color="#FFFFFF"></uni-icons>
		</view>

		<!-- 顶部大图 -->
		<view v-if="user" class="zhuige-home-bg">
			<view class="zhuige-home-cover">
				<image class="zhuige-hover-bgimg" mode="aspectFill" :src="user.cover"></image>
			</view>

			<!-- 用户信息 -->
			<view v-if="user" class="zhuige-user-card">
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
						<!-- 新增私信 -->
						<view v-if="btn_message"
							@click="openLink('/pages/message/detail/detail?user_id=' + user.user_id)" class="msg">
							私信
						</view>

						<template v-if="!user.is_me">
							<template v-if="user.is_follow">
								<view class="follow active" @click="clickFollowUser">{{user.is_fans?'互关':'已关注'}}</view>
							</template>
							<view v-else @click="clickFollowUser" class="follow">+关注</view>
						</template>

						<!-- 分享先隐藏 -->
						<!-- <button open-type="share">
							<uni-icons type="redo-filled" size="24"></uni-icons>
						</button> -->

					</view>

				</view>

				<!-- 统计 -->

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
					<view>
						<image mode="aspectFill" src="@/static/icon_sg.png"></image>
						<text>签名：</text>
					</view>
					<text>{{user.sign}}</text>
				</view>
				<view v-if="user.certify && user.certify.status==1" class="zhuige-user-line">
					<view>
						<image mode="aspectFill" :src="user.certify.icon"></image>
						<!-- <image mode="aspectFill" src="@/static/icon_id.png"></image> -->
						<text>认证：</text>
					</view>
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

		<!-- 闲置物品 瀑布流列表 -->
		<template v-if="cur_tab=='idle'">
			<view v-if="lists[0].length>0" class="zhuige-waterfall">
				<view v-for="(list, listIndex) in lists" :key="listIndex" :id="'list-' + listIndex"
					class="zhuige-waterfall-list">
					<view v-for="(item, index) in list" :key="item.id"
						@click="openLink('/pages/idle-shop/detail/detail?id=' + item.id)" class="zhuige-waterfall-block"
						:style="item.height?'opacity: 1;':''">
						<view class="zhuige-waterfall-img">
							<view v-if="item.stick" class="waterfall-mark">推广</view>
							<image mode="aspectFill" :src="item.thumb" :data-a="listIndex" :data-b="index"
								@load="onImageLoad" :style="item.height?('height:' + item.height + 'rpx;'):''"></image>
						</view>
						<view class="zhuige-waterfall-text">
							<view class="title">{{item.title}}</view>
							<view v-if="item.excerpt" class="excerpt">{{item.excerpt}}</view>
						</view>
						<view v-if="item.tags && item.tags.length>0" class="zhuige-waterfall-tags">
							<text v-for="(tag, itag) in item.tags" :key="itag">{{tag.name}}</text>
						</view>
						<view class="zhuige-waterfall-footer">
							<view class="zhuige-waterfall-price">
								<text>￥</text>
								<text>{{item.price}}</text>
							</view>
						</view>
					</view>
				</view>
			</view>
			<template v-else>
				<zhuige-nodata v-if="loaded"></zhuige-nodata>
			</template>

			<uni-load-more v-if="lists[0].length>0" :status="loadMore"></uni-load-more>
		</template>

		<!-- 帖子列表 -->
		<template v-else>
			<view class="zhuige-home-list">
				<template v-if="posts && posts.length>0">
					<view v-for="(topic, index) in posts" :key="index">
						<zhuige-topic v-if="topic.post_type=='zhuige_bbs_topic'" :topic="topic"
							:trash="cur_tab=='publish' && user.delete_topic==1"
							:promotion="cur_tab=='publish' && user.is_me==1" @deleteTopic="onDeleteTopic">
						</zhuige-topic>
						<view v-else class="zhuige-block" :class="topic.post_type"
							@click="clickPost(topic)">
							<!-- 投票 -->
							<template v-if="topic.post_type=='zhuige_vote'">
								<!-- 用户信息 -->
								<view class="zhuige-social-poster-blcok"
									@click="openLink('/pages/user/home/home?user_id=' + topic.author.user_id)">
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
													v-if="topic.author.vip && topic.author.vip.status==1"
													mode="aspectFill" :src="topic.author.vip.icon">
												</image>
											</view>
											<view>
												<text>{{topic.time}}</text>
												<text
													v-if="topic.author.certify && topic.author.certify.status==1">/</text>
												<text
													v-if="topic.author.certify && topic.author.certify.status==1">{{topic.author.certify.name}}</text>
											</view>
										</view>
									</view>
									<view class="zhuige-social-opt social-dell"
										v-if="cur_tab=='publish' && user.delete_vote==1"
										@click.stop="clickDeleteVote(topic)">
										删除
									</view>
									<view v-if="cur_tab=='publish' && user.is_me==1"
										@click.stop="clickPromotion(topic.id)"
										class="zhuige-social-opt social-dell social-ad">
										推广
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
											<view v-if="topic.my_vote==1 || topic.is_end==1"
												class="zhuige-vote-count-num">
												{{topic.option_a.rate}}%{{topic.option_a.xuan==1?'（已投）':''}}
											</view>
											<image class="zhuige-vote-img" mode="aspectFill"
												:src="topic.option_a.image">
											</image>
										</view>
										<view class="zhuige-vote-count">
											<view v-if="topic.my_vote==1 || topic.is_end==1"
												class="zhuige-vote-count-num">
												{{topic.option_b.rate}}%{{topic.option_b.xuan==1?'（已投）':''}}
											</view>
											<image class="zhuige-vote-img" mode="aspectFill"
												:src="topic.option_b.image">
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
										<view v-for="(item, ito) in topic.options" :key="ito"
											class="zhuige-vote-option" :class="item.xuan==1?'vote-check':''">
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

							<!-- 商家名片 -->
							<template v-else-if="topic.post_type=='zhuige_business_card'">
								<view class="zhuige-sp-list-block sp-left">
									<view class="zhuige-sp-img">
										<image mode="aspectFill" :src="topic.logo"></image>
									</view>
									<view class="zhuige-sp-text">
										<view class="zhuige-sp-title">
											
											<view class="sp-minfo">
												<text v-if="topic.stick" class="tag">推广</text>
												<text>{{topic.title}}</text>
											</view>
											<view class="sp-mopt">
												<view class="zhuige-social-opt social-dell"
													v-if="cur_tab=='publish' && user.delete_business_card==1"
													@click.stop="clickDeleteBusinessCard(topic.id)">
													删除
												</view>
												<view v-if="cur_tab=='publish' && user.is_me==1"
													@click.stop="clickPromotion(topic.id)"
													class="zhuige-social-opt social-dell social-ad">
													推广
												</view>										
											</view>
											
										</view>
										<view class="zhuige-sp-info">
											<text>{{topic.excerpt}}</text>
										</view>
										<view class="zhuige-sp-opt">
											<!-- 评分 -->
											<view class="zhuige-sp-star">
												<uni-rate :value="topic.score" size="12"
													active-color="#ff6146"></uni-rate>
											</view>
											<!-- 标签 -->
											<view class="zhuige-sp-tags">
												<text v-for="(tag, itag) in topic.tags" :key="itag">{{tag.name}}</text>
											</view>
										</view>
									</view>
									
								</view>
							</template>
							
							<!-- 百科词条 -->
							<template v-else-if="topic.post_type=='zhuige_wiki'">
								<view class="zhugie-info-block left-side">
									<view class="zhugie-info-image">
										<image mode="aspectFill" :src="topic.thumb"></image>
									</view>
									<view class="zhugie-info-text">
										<view class="zhugie-info-title">
											{{topic.title}}
										</view>
										<view class="zhugie-info-summary">
											{{topic.excerpt}}
										</view>
										<view class="zhuige-info-post">
											<view class="zhuige-info-data">
												<text v-for="(tag, itag) in topic.tags" :key="itag"
													@click.stop="clickTag(tag)">{{tag.name}}</text>
											</view>
										</view>
									</view>
								</view>
							</template>

							<!-- 闲置物品 -->
							<template v-else-if="topic.post_type=='zhuige_idle_goods'">
								<view class="zhuige_idle">
									<view class="zhuige-social-poster-blcok">
										<view class="zhuige-social-poster">
											<view class="zhuige-social-poster-avatar">
												<image mode="aspectFill" :src="topic.user.avatar">
												</image>
											</view>
											<view class="zhuige-social-poster-info">
												<view>
													<text>{{topic.user.nickname}}</text>
													<image v-if="topic.user.certify && topic.user.certify.status==1"
														mode="aspectFill" :src="topic.user.certify.icon">
													</image>
													<image v-if="topic.user.vip && topic.user.vip.status==1"
														class="zhuige-social-vip" mode="aspectFill"
														:src="topic.user.vip.icon">
													</image>
												</view>
												<view>
													<text>{{topic.time}}</text>
													<text
														v-if="topic.user.certify && topic.user.certify.status==1">/</text>
													<text
														v-if="topic.user.certify && topic.user.certify.status==1">{{topic.user.certify.name}}</text>
												</view>
											</view>
										</view>
										
										<view class="zhuige-social-opt social-dell"
											v-if="cur_tab=='publish' && user.delete_idle_shop==1"
											@click.stop="clickDeleteIdleGoods(topic.id)">
											删除
										</view>
										<view v-if="cur_tab=='publish' && user.is_me==1"
											@click.stop="clickPromotion(topic.id)"
											class="zhuige-social-opt social-dell social-ad">
											推广
										</view>
									</view>
									
									<view class="zhuige-social-cont">
										{{topic.excerpt}}
									</view>

									<view class="zhuige-idle-block">
										<view class="zhuige-idle-block-img">
											<view v-if="topic.stick" class="mark">推广</view>
											<image mode="aspectFill" :src="topic.thumb"></image>
										</view>
										<view class="zhuige-idle-block-text">{{topic.title}}</view>
										<view class="zhuige-idle-block-foot">
											<view class="">
												<text>￥</text>
												<text>{{topic.price}}</text>
											</view>
											<view>
												<text v-for="(tag, itag) in topic.tags" :key="itag">{{tag.name}}</text>
											</view>
										</view>
									</view>

									<!-- 产品分类及数据 -->
									<view class="zhuige-social-data">
										<view>
											<image mode="aspectFill" src="@/static/idle.png"></image>
											<view>{{topic.cat.name}}</view>
										</view>
										<view>
											<view>
												<uni-icons type="hand-up-filled" size="18"></uni-icons>
												<text>{{topic.like_count}}</text>
											</view>
											<view>
												<uni-icons type="chatboxes-filled" size="18"></uni-icons>
												<text>{{topic.comment_count}}</text>
											</view>
										</view>
									</view>
								</view>
							</template>

						</view>
					</view>
				</template>
				<template v-else-if="loaded">
					<zhuige-nodata :tip="noDataTip"></zhuige-nodata>
				</template>
			</view>

			<uni-load-more v-if="posts && posts.length>0" :status="loadMore"></uni-load-more>
		</template>
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

	import ZhuigeTopic from "@/components/zhuige-topic";
	import ZhuigeScrollAd from "@/components/zhuige-scroll-ad";
	import ZhuigeTab from "@/components/zhuige-tab";
	import ZhuigeNodata from "@/components/zhuige-nodata";

	export default {
		components: {
			ZhuigeTopic,
			ZhuigeScrollAd,
			ZhuigeTab,
			ZhuigeNodata
		},

		data() {
			this.user_id = undefined;

			this.loginReload = false;

			// 是否从参数传来tab
			this.opt_tab = undefined;

			// 瀑布流
			this.list = [];
			this.heights = [];

			return {
				user: undefined,
				stat: undefined,
				rec_ad: undefined,

				tabs: [],
				cur_tab: undefined,

				// 瀑布流
				lists: [
					[],
					[]
				],

				posts: [],
				loadMore: 'more',
				loaded: false,

				noDataTip: '哇哦，什么也没有',

				// 是否显示私信按钮
				btn_message: false,

				nav_opacity: 0,
				statusBarHeight: 0,
			}
		},

		onLoad(options) {
			Util.addShareScore(options.source);

			if (options.user_id) {
				this.user_id = options.user_id;
			}

			if (options.tab) {
				this.opt_tab = options.tab;
			}

			this.statusBarHeight = uni.getSystemInfoSync().statusBarHeight;

			// this.refresh();
			this.loadData(true);

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
			 * 图片加载完成事件
			 */
			onImageLoad(e) {
				let scale = e.detail.height / e.detail.width;
				if (scale > 1.2) {
					scale = 1.2;
				} else if (scale < 0.8) {
					scale = 0.8;
				}
				let height = 345 * scale;
				this.lists[e.currentTarget.dataset.a][e.currentTarget.dataset.b].height = height;

				let id = this.lists[e.currentTarget.dataset.a][e.currentTarget.dataset.b].id;
				let loaded = false;
				for (let i = 0; i < this.heights.length; i++) {
					if (this.heights[i].id == id) {
						loaded = true;
						break;
					}

				}
				if (!loaded) {
					this.heights.push({
						id: id,
						height: height
					});
				}

				// #ifndef VUE3
				this.$forceUpdate();
				// #endif
			},

			/**
			 * 返回上一页
			 */
			clickBack() {
				Util.navigateBack();
			},

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

			clickPromotion(post_id) {
				Util.openLink('/pages/promotion/pay/pay?id=' + post_id);
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
			loadData(posts = false) {
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

					// if (res.data.tab_idle) {
					// 	this.tabs.unshift({
					// 		id: 'idle',
					// 		title: '好货'
					// 	})

					// 	if (!this.opt_tab) {
					// 		this.cur_tab = 'idle';
					// 	}
					// }

					this.tabs = res.data.tabs;
					if (this.opt_tab) {
						this.cur_tab = this.opt_tab;
					} else {
						this.cur_tab = this.tabs[0].id;
					}

					if (posts) {
						this.loadPosts(true);
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
					if (this.cur_tab == 'idle') {
						let list = res.data.list;
						for (let i = 0; i < list.length; i++) {
							for (let j = 0; j < this.heights.length; j++) {
								if (list[i].id == this.heights[j].id) {
									list[i].height = this.heights[j].height;
									break;
								}
							}
						}
						this.list = refresh ? list : this.list.concat(list);
						this.loadMore = res.data.more;
						this.loaded = true;

						if (refresh) {
							for (let j = 0; j < this.lists.length; j++) {
								this.lists[j] = [];
							}
						}

						for (let i = 0; i < this.list.length; i++) {

							let dist = 0;
							for (let j = 0; j < this.lists.length - 1; j++) {
								if (this.lists[j].length > this.lists[j + 1].length) {
									dist = j + 1;
								}
							}

							this.lists[dist].push(this.list[i]);
						}

						// #ifndef VUE3
						this.$forceUpdate();
						// #endif
					} else {
						this.posts = refresh ? res.data.posts : this.posts.concat(res.data.posts);
						this.loadMore = res.data.more;
						this.loaded = true;
						this.noDataTip = res.data.tip;
					}
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 删除帖子
			 */
			onDeleteTopic(topic) {
				uni.showModal({
					title: '提示',
					content: '真的要删除帖子吗？',
					success: (res2) => {
						if (res2.cancel) {
							return;
						}

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
					}
				});
			},

			/**
			 * 删除投票
			 */
			clickDeleteVote(vote_id) {
				uni.showModal({
					title: '提示',
					content: '真的要删除投票吗？',
					success: (res2) => {
						if (res2.cancel) {
							return;
						}

						Rest.post(Api.URL('vote', 'delete'), {
							vote_id: vote_id
						}).then(res => {
							if (res.code != 0) {
								Alert.error(res.message);
								return;
							}

							let newPosts = [];
							this.posts.forEach(ele => {
								if (vote_id != ele.id) {
									newPosts.push(ele);
								}
							})
							this.posts = newPosts;
						}, err => {
							console.log(err)
						});
					}
				});
			},

			/**
			 * 删除商家名片
			 */
			clickDeleteBusinessCard(card_id) {
				uni.showModal({
					title: '提示',
					content: '真的要删除商家名片吗？',
					success: (res2) => {
						if (res2.cancel) {
							return;
						}

						Rest.post(Api.URL('bcard', 'delete'), {
							card_id: card_id
						}).then(res => {
							if (res.code != 0) {
								Alert.error(res.message);
								return;
							}

							let newPosts = [];
							this.posts.forEach(ele => {
								if (card_id != ele.id) {
									newPosts.push(ele);
								}
							})
							this.posts = newPosts;
						}, err => {
							console.log(err)
						});
					}
				});
			},

			/**
			 * 删除闲置物品
			 */
			clickDeleteIdleGoods(goods_id) {
				uni.showModal({
					title: '提示',
					content: '真的要删除闲置物品吗？',
					success: (res2) => {
						if (res2.cancel) {
							return;
						}

						Rest.post(Api.URL('idle', 'delete'), {
							goods_id: goods_id
						}).then(res => {
							if (res.code != 0) {
								Alert.error(res.message);
								return;
							}

							let newPosts = [];
							this.posts.forEach(ele => {
								if (card_id != ele.id) {
									newPosts.push(ele);
								}
							})
							this.posts = newPosts;
						}, err => {
							console.log(err)
						});
					}
				});
			}
		}
	}
</script>

<style>
	page {
		background: #f5f5f5;
	}

	.zhuige-nav-back {
		position: fixed;
		top: 0;
		padding: 0 20rpx;
		z-index: 99;
	}

	.zhuige-home-bg {
		width: 100%;
		position: relative;
		overflow: hidden;
		padding-bottom: 60rpx;
	}

	.zhuige-home-bg .zhuige-home-cover {
		width: 100%;
		height: 480rpx;
		position: relative;
	}

	.zhuige-home-bg image.zhuige-hover-bgimg {
		height: 100%;
		width: 100%;
	}

	.zhuige-user-card {
		position: relative;
		z-index: 9;
		margin-top: -30rpx;
		padding: 20rpx 30rpx 10rpx;
		border-radius: 30rpx 30rpx 0 0;
		background: #FFFFFF;
	}

	.zhuige-user-title {
		display: flex;
		align-items: flex-end;
		justify-content: space-between;
		padding-bottom: 30rpx;
	}

	.zhuige-user-info-block {
		margin-top: -90rpx;
	}

	.zhuige-user-info-block>view:nth-child(1) image {
		width: 128rpx;
		height: 128rpx;
		border-radius: 50%;
	}

	.zhuige-user-info-block>view:nth-child(2) {
		display: flex;
		align-items: center;
	}

	.zhuige-user-info-block>view:nth-child(2) text {
		font-size: 36rpx;
		font-weight: 500;
		margin-right: 8rpx;
	}

	.zhuige-user-info-block>view:nth-child(2) image {
		width: 64rpx;
		height: 28rpx;
	}

	.zhuige-user-info-block+view {
		display: flex;
		padding-bottom: 20rpx;
	}

	.zhuige-user-info-block+view view {
		height: 64rpx;
		line-height: 64rpx;
		padding: 0 40rpx;
		border-radius: 64rpx;
		margin-left: 20rpx;
		background: #F1F3F5;
		color: #333333;
		font-size: 26rpx;
		font-weight: 400;
	}

	.zhuige-user-info-block+view view:last-child {
		background: #333333;
		color: #FFFFFF;
	}

	.zhuige-user-info-block+view view:last-child.active {
		background: #F1F3F5;
		color: #333333;
	}


	.zhuige-user-info-btn {
		display: flex;
		align-items: center;
		padding: 20rpx 0;
	}

	.zhuige-user-info-btn view,
	.zhuige-user-info-btn button {
		height: 80rpx;
		line-height: 80rpx;
		text-align: center;
		width: 90rpx;
		padding: 0;
		border-radius: 40rpx;
		background: #F3F2F3;
		margin-left: 12rpx;
		font-size: 28rpx;
		font-weight: 400;
	}

	.zhuige-user-info-btn view text {
		color: #333333;
	}

	.zhuige-user-info-btn button::after {
		border: none;
	}

	.zhuige-user-info-btn view.follow {
		background: #f5f5f5;
		/* color: #ffffff; */
		width: 110rpx;
	}

	.zhuige-user-info-btn view.active {
		background: #111111;
		color: #ffffff;
	}

	.zhuige-user-count {
		display: flex;
		align-items: baseline;
		padding: 30rpx 0;
		border-top: 1px dotted #DDDDDD;
	}

	.zhuige-user-count>view {
		width: 25%;
		display: flex;
		align-items: baseline;
	}

	.zhuige-user-count>view text {
		font-size: 24rpx;
		font-weight: 400;
	}

	.zhuige-user-count>view view {
		font-size: 40rpx;
		font-weight: 600;
		margin-left: 8rpx;
	}

	.zhuige-user-line {
		display: flex;
		align-items: center;
		padding: 30rpx 0;
		border-top: 1px dotted #DDDDDD;
	}

	.zhuige-user-line view {
		display: flex;
		align-items: center;
	}

	.zhuige-user-line view image {
		height: 32rpx;
		width: 17px;
		margin-right: 8rpx;
	}

	.zhuige-idele-text-line view text {
		font-size: 28rpx;
		font-weight: 600;
	}

	.zhuige-user-line>text {
		font-size: 26rpx;
		font-weight: 500;
		color: #444444
	}

	.zhuige-user-line text:last-of-type {
		white-space: normal;
		max-width: 80%;
	}

	.zhuige-home-tab .zhuige-block {
		margin-bottom: 0;
		border-radius: 24rpx 24rpx 0 0;
	}

	.zhuige-home-tab .zhuige-tab {
		border-bottom: 1rpx solid #DDDDDD !important;
		padding-bottom: 20rpx !important;
	}

	.zhuige-user-market {
		padding-bottom: 20rpx;
	}

	.zhuige-user-info-block-text {
		display: flex;
		align-items: center;
		flex-wrap: nowrap;
		padding: 10rpx;
	}

	.zhuige-user-info-block-text text {
		font-size: 36rpx;
		font-weight: 600;
		height: 1.4em;
		line-height: 1.4em;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.zhuige-user-info-block-text image {
		height: 28rpx;
		width: 56rpx;
		min-width: 56rpx;
		margin-left: 12rpx;
	}

	.zhuige-home-tab {
		z-index: 19;
		position: relative;
		margin-top: -40rpx;
	}

	.zhuige-home-list .zhuige-block {
		padding: 20rpx 40rpx !important;
	}

	/* 瀑布流 start */
	.zhuige-waterfall {
		display: flex;
		flex-direction: row;
		flex-wrap: nowrap;
		justify-content: flex-start;
		align-items: flex-start;
		align-content: flex-start;
		padding: 10rpx 30rpx 20rpx;
		box-sizing: border-box;
		background: linear-gradient(to bottom, #ffffff 20%, #f5f5f5 40%, #f5f5f5 100%);
	}

	.zhuige-waterfall-list {
		box-sizing: border-box;
		padding: 0 10rpx;
		width: 50%;
	}

	.zhuige-waterfall-block {
		background-color: #ffffff;
		border-radius: 12rpx;
		font-size: 28rpx;
		color: #555555;
		margin-bottom: 20rpx;
		/* opacity: 0; */
		/*初始不透明度为0，图片都看不见*/
		/* transition: opacity 500ms linear; */
		/*--重点--定义一个关于透明度的transition*/
	}

	.zhuige-waterfall-img {
		position: relative;
	}

	.zhuige-waterfall-img image {
		width: 100%;
		height: 200rpx;
		display: inherit;
		border-radius: 12rpx 12rpx 0 0;
		/* transition: height 500ms linear; */
	}

	.waterfall-mark {
		position: absolute;
		z-index: 3;
		font-size: 22rpx;
		height: 48rpx;
		line-height: 48rpx;
		padding: 0 24rpx;
		border-radius: 6rpx 0 6rpx 0;
		top: 0;
		left: 0;
		background: #FF6146;
		color: #FFFFFF;
	}

	.zhuige-waterfall-poster {
		position: absolute;
		z-index: 3;
		left: 20rpx;
		bottom: 20rpx;
		display: flex;
		align-items: center;
	}

	.zhuige-waterfall-poster image {
		height: 48rpx;
		width: 48rpx;
		border-radius: 50%;
		margin-right: 12rpx;
	}

	.zhuige-waterfall-poster text {
		font-size: 24rpx;
		font-weight: 300;
		color: #FFFFFF;
	}

	.zhuige-waterfall-text {
		margin-top: 12rpx;
		padding: 0 16rpx;
	}

	.zhuige-waterfall-text view {
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.zhuige-waterfall-text view.title {
		font-size: 30rpx;
		font-weight: 600;
		color: #333333;
	}

	.zhuige-waterfall-text view.excerpt {
		font-size: 26rpx;
		font-weight: 400;
		color: #555555;
	}

	.zhuige-waterfall-tags {
		margin-top: 6rpx;
		display: flex;
		flex-wrap: wrap;
		overflow: hidden;
		height: 40rpx;
		padding: 0 16rpx;
	}

	.zhuige-waterfall-tags text {
		height: 40rpx;
		line-height: 40rpx;
		padding: 0 12rpx;
		margin-right: 8rpx;
		border-radius: 6rpx;
		background: #f5f5f5;
		font-size: 22rpx;
		font-weight: 400;
		color: #333333;
	}

	.zhuige-waterfall-tags text:nth-child(1) {
		background: #333333;
		color: #FFFFFF;
	}

	.zhuige-waterfall-price {
		display: flex;
		flex-wrap: nowrap;
		align-items: center;
		overflow: hidden;
		padding: 0 16rpx 16rpx;
	}

	.zhuige-waterfall-price text:nth-child(1) {
		font-size: 24rpx;
		font-weight: 300;
		color: #333333;
	}

	.zhuige-waterfall-price text:nth-child(2) {
		font-size: 36rpx;
		font-weight: 600;
		color: #333333;
		margin-left: 8rpx;
	}

	.zhuige-waterfall-price .zhuige-waterfall-vip-price {
		border: 1rpx solid #FF6146;
		border-radius: 5rpx;
		height: 1.4em;
		line-height: 1.4em;
		padding: 0 3px;
		color: #FF6146;
		font-size: 18rpx;
		margin-left: 12rpx;
	}

	.zhuige-waterfall-price .zhuige-waterfall-orig-price {
		font-size: 24rpx;
		font-weight: 300;
		color: #999999;
		margin-left: 16rpx;
		text-decoration: line-through;
	}

	/* 瀑布流 end */


	.zhuige-sp-list-block {
		display: flex;
		align-items: center;
		padding: 6rpx;
		background: #FFFFFF;
		border-radius: 12rpx;
	}

	.zhuige-sp-list-block:last-of-type {
		border: none;
	}


	.zhuige-sp-title {
		height: 2em;
		line-height: 1.4em;
		display: flex;
		align-items: center;
		flex-wrap: nowrap;
		justify-content: space-between;
	}
	
	.zhuige-sp-title .sp-minfo{
		display: flex;
		align-items: center;
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;	
		height: 1.4em;
		line-height: 1.4em;
		max-width: 70%;
	}
	.zhuige-sp-title .sp-mopt {
		display: flex;
		align-items: center;
		min-width: 148rpx;
	}

	.zhuige-sp-title text {
		font-size: 32rpx;
		font-weight: 500;
	}

	.zhuige-sp-title text.tag {
		font-size: 20rpx;
		font-weight: 400;
		color: #FFFFFF;
		background: #FF6146;
		margin-right: 8rpx;
		line-height: 1rem;
		padding: 1rpx 8rpx;
		border-radius: 4rpx;
	}

	.zhuige-sp-title text.sptitle {
		width: 100%;
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;
	}


	.zhuige-sp-info {
		font-weight: 400;
		color: #666666;
		height: 2em;
		line-height: 2em;
		text-overflow: ellipsis;
		overflow: hidden;
	}

	.zhuige-sp-tags {
		display: flex;
		align-items: center;
		overflow: hidden;
	}

	.zhuige-sp-tags text {
		height: 36rpx;
		line-height: 36rpx;
		font-size: 20rpx;
		font-weight: 300;
		background: #f5f5f5;
		padding: 0 12rpx;
		margin-left: 8rpx;
		border-radius: 6rpx;
		white-space: nowrap;
	}

	.sp-left {
		flex: 0 0 180rpx;
	}

	.sp-left .zhuige-sp-img {
		height: 180rpx;
		width: 180rpx;
		position: relative;
	}

	.sp-left .zhuige-sp-img image {
		height: 180rpx;
		width: 180rpx;
		border-radius: 6rpx;
	}

	.sp-left .zhuige-sp-text {
		padding-left: 20rpx;
		width: 100%;
		overflow: hidden;
	}

	.zhuige-sp-opt {
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding-top: 10rpx;
	}

	.zhuige-sp-star {
		display: flex;
		align-items: center;
	}

	/**/
	.zhuige-idle-block {
		width: 320px;
		background: #F5F5F5;
		border-radius: 6px;
		position: relative;
		margin-bottom: 20rpx;
	}

	.zhuige-idle-block-img {
		width: 100%;
		height: 280rpx;
		position: relative;
	}

	.zhuige-idle-block-img image {
		height: 100%;
		width: 100%;
		border-radius: 6px 6px 0 0;
	}

	.zhuige-idle-block-img .mark {
		position: absolute;
		top: 0;
		left: 0;
		height: 56rpx;
		line-height: 56rpx;
		padding: 0 24rpx;
		border-radius: 6px 0 6px 0;
		font-size: 26rpx;
		font-weight: 400;
		color: #FFFFFF;
		background: #FF4400;
	}

	.zhuige-idle-block-text {
		padding: 30rpx 20rpx 10rpx;
		height: 1em;
		line-height: 1em;
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;
	}

	.zhuige-idle-block-foot {
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding-bottom: 10rpx;
	}

	.zhuige-idle-block-foot>view {
		display: flex;
		align-items: center;
		padding: 0 20rpx;
	}

	.zhuige-idle-block-foot>view:nth-child(1) text {
		font-size: 36rpx;
		font-weight: 600;
		color: #FF4400;
	}

	.zhuige-idle-block-foot>view:nth-child(1) text:nth-child(1) {
		font-size: 26rpx;
		font-weight: 300;
	}

	.zhuige-idle-block-foot>view:nth-child(2) text {
		height: 40rpx;
		line-height: 40rpx;
		padding: 0 12rpx;
		margin-right: 8rpx;
		border-radius: 6rpx;
		font-size: 22rpx;
		border: 1px solid #999999;
		font-weight: 400;
		color: #555555;
	}

	.zhuige-idle-block-foot>view:nth-child(2) text:nth-child(1) {
		background: #333333;
		color: #FFFFFF;
		border: 1px solid #333333;
	}
	.zhugie-info-summary {
		display: -webkit-box;
		-webkit-line-clamp: 1;
		-webkit-box-orient: vertical;
		overflow: hidden;
		line-height: 1.8em;
		margin-bottom: 16rpx;
		color: #555555;
	}
	.zhugie-info-title {
		padding: 0;		
		line-height: 1.6em;
	}
	
</style>