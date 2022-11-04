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

		<template v-for="(post, index) in rec_posts">
			<view class="zhuige-mini-group" :key="index">
				<!-- 文章自定义 -->
				<view v-if="post.post_type=='post'" class="zhuige-mini-custom">
					<view class="zhuige-block">
						<view class="zhuige-block-head">
							<view>{{post.title}}</view>
							<view v-if="post.more_link" @click="clickLink(post.more_link)">查看更多</view>
						</view>
						<view v-for="(item, inx) in post.items" :key="inx" class="zhugie-info-block left-side"
							@click="clickPost(item)">
							<view v-if="item.thumbnails && item.thumbnails.length>0" class="zhugie-info-image">
								<text v-if="item.badge">{{item.badge}}</text>
								<image mode="aspectFill" :src="item.thumbnails[0]" />
							</view>
							<view class="zhugie-info-text">
								<view class="zhugie-info-title">{{item.title}}</view>
								<view class="zhuige-info-post">
									<view class="zhuige-info-data">
										<text
											v-if="item.read_limit=='cost' && (!is_ios || (is_ios && item.cost_ios_switch=='1'))"
											class="pay">￥{{item.cost_price}}</text>
										<text v-if="item.read_limit=='score'" class="pay">{{item.cost_score}}积分</text>
										<text>浏览 {{item.views}}</text>
										<text>点赞 {{item.likes}}</text>
									</view>
								</view>
							</view>
						</view>
					</view>
				</view>

				<!-- 活动自定义 -->
				<view v-else-if="post.post_type=='zhuige_activity'" class="zhuige-mini-custom-wide">
					<view class="zhuige-mini-act-cover" @click="clickLink(post.more_link)">
						<view>
							<view>{{post.title}}</view>
							<text>活动</text>
						</view>
						<view>{{post.subtitle}}</view>
						<image mode="aspectFill" :src="post.banner" />
					</view>
					<view class="zhuige-block">
						<view v-for="(item, inx) in post.items" :key="inx" class="zhugie-info-block left-side"
							@click="clickPost(item)">
							<view class="zhugie-info-image">
								<image mode="aspectFill" :src="item.thumbnail" />
							</view>
							<view class="zhuige-info-custom">
								<view class="zhugie-info-text">
									<view class="zhugie-info-title">{{item.title}}</view>
									<view class="zhuige-info-post">
										<view class="zhuige-info-data activity-time">
											{{item.time.from}} - {{item.time.to}}
										</view>
									</view>
								</view>
								<view class="zhugie-info-custom-opt">
									<view>{{item.is_end?'已结束':(item.my_enroll?'已报名':'报名')}}</view>
								</view>
							</view>
						</view>
					</view>
				</view>

				<!-- 积分商品自定义 -->
				<view v-else-if="post.post_type=='zhuige_goods'" class="zhuige-mini-custom-twins">
					<view class="zhuige-block">
						<view class="zhuige-block-head">
							<view>{{post.title}}</view>
							<view v-if="post.more_link" @click="clickLink(post.more_link)">查看更多</view>
						</view>
						<view class="zhuige-mini-twins">
							<view v-for="(item, inx) in post.items" :key="inx" class="zhugie-info-block"
								@click="clickPost(item)">
								<view class="zhugie-info-image">
									<image mode="aspectFill" :src="item.thumbnail" />
								</view>
								<view class="zhugie-info-text">
									<view class="zhugie-info-title">{{item.title}}</view>
									<view class="zhuige-info-post">
										<view class="zhuige-info-pay">
											<text>{{item.price}}</text>
											<text>积分</text>
										</view>
									</view>
								</view>
							</view>
						</view>
					</view>
				</view>

				<!-- 知识库定义 -->
				<view v-else-if="post.post_type=='zhuige_res'" class="zhuige-mini-custom">
					<view class="zhuige-block">
						<view class="zhuige-block-head">
							<view>{{post.title}}</view>
							<view v-if="post.more_link" @click="clickLink(post.more_link)">查看更多</view>
						</view>
						<view v-for="(item, inx) in post.items" :key="inx" class="zhugie-info-block left-side"
							@click="clickPost(item)">
							<view class="zhugie-info-image">
								<text v-if="item.badge">{{item.badge}}</text>
								<image mode="aspectFill" :src="item.thumbnail" />
							</view>
							<view class="zhuige-info-custom">
								<view class="zhugie-info-text">
									<view class="zhugie-info-title">{{item.title}}</view>
									<view class="zhuige-info-post">
										<view class="zhuige-info-data">
											<text
												v-if="item.limit=='cost' && (!is_ios || (is_ios && item.cost_ios_switch=='1'))"
												class="pay">￥ {{item.cost_price}}</text>
											<text v-if="item.limit=='score'" class="pay">{{item.cost_score}} 积分</text>
											<text>浏览 {{item.views}}</text>
											<text>点赞 {{item.likes}}</text>
										</view>
									</view>
								</view>
								<view class="zhugie-info-custom-opt">
									<view>下载</view>
								</view>
							</view>
						</view>
					</view>
				</view>

				<!-- 课程专栏自定义 -->
				<view v-else-if="post.post_type=='zhuige_column'" class="zhuige-mini-custom">
					<view class="zhuige-block">
						<view class="zhuige-block-head">
							<view>{{post.title}}</view>
							<view v-if="post.more_link" @click="clickLink(post.more_link)">查看更多</view>
						</view>
						<view class="zhuige-scroll-ad">
							<scroll-view scroll-x="true">
								<view v-for="(item, inx) in post.items" :key="inx" class="zhuige-scroll-ad-block"
									@click="clickPost(item)">
									<view>
										<image mode="aspectFill" :src="item.thumbnail" />
										<text v-if="item.badges && item.badges.length>0">{{item.badges[0]}}</text>
									</view>
									<view class="zhuige-scroll-ad-text">
										<view class="zhuige-scroll-ad-text-title">{{item.title}}</view>
										<view class="zhuige-scroll-ad-text-rate">
											<view class="zhuige-info-rate">
												<uni-rate :value="item.score" size="12" :activeColor="'#FF6146'" />
												<view>{{item.score}}</view>
											</view>
											<view class="rate-num">/ {{item.views}}人已学</view>
										</view>
									</view>
								</view>
							</scroll-view>
						</view>
					</view>
				</view>
				<!-- 帖子 -->
				<zhuige-scroll-ad v-else-if="post.post_type=='zhuige_bbs_topic'"
					ext-ad-class="zhuige-scroll-coterie zhuige-topic-scroll" :title="post.title" :items="post.items">
				</zhuige-scroll-ad>
				<!-- 其他 -->
				<zhuige-scroll-ad v-else ext-ad-class="zhuige-scroll-coterie" :title="post.title" :items="post.items">
				</zhuige-scroll-ad>
			</view>
		</template>

		<view v-if="tab_switch==1" class="zhuige-block-tab" :style="'top:' + tab_sticky_top + 'px;padding-top: 0rpx;'">
			<zhuige-tab v-if="tab_type==1" type="scroll" :tabs="tabs" :cur-tab="cur_tab" @clickTab="clickTab">
			</zhuige-tab>
			<zhuige-tab v-if="tab_type==2 && lastLoaded || followLoaded" type="simple" :tabs="tabs" :cur-tab="cur_tab"
				@clickTab="clickTab"></zhuige-tab>
		</view>

		<template v-if="list_switch==1">
			<view v-if="tab_type==1 || (tab_type==2 && cur_tab=='last')" class="zhuige-tab-block">
				<!-- 圈子列表 近期tab -->
				<view class="zhuige-social-list">
					<template v-if="lastTopics && lastTopics.length>0">
						<template v-for="(topic, index) in lastTopics">

							<!-- 用户列表推荐 滚动 -->
							<zhuige-user-list v-if="rec_user && rec_user.position==index" type="scroll"
								:title="rec_user.title" :users="rec_user.users" ext-class="zhuige-user-followed">
							</zhuige-user-list>

							<zhuige-topic v-if="topic.post_type=='zhuige_bbs_topic'" :key="index" :topic="topic">
							</zhuige-topic>
							<view v-else :key="index" class="zhuige-block" :class="topic.post_type"
								@click="clickPost(topic)">

								<!-- 活动报名 -->
								<template v-if="topic.post_type=='zhuige_activity'">
									<view class="zhugie-info-block">
										<view class="zhugie-info-title">{{topic.title}}</view>
										<view class="zhuige-act-timeline-text">
											<text>{{topic.time.from}}</text>
											<text>-</text>
											<text>{{topic.time.to}}</text>
										</view>
										<view class="zhugie-info-image">
											<image mode="aspectFill" :src="topic.thumbnail" />
										</view>
										<view class="zhuige-act-timeline-sub">
											<view class="zhuige-act-tags">
												<text v-for="(badge, badgeIndex) in topic.badges"
													:key="badgeIndex">{{badge}}</text>
											</view>
											<!-- 活动报名状态-->
											<view class="zhuige-act-btn"
												:class="topic.is_end?'act-end':(topic.my_enroll?'act-enroll':'')">
												{{topic.is_end?'已结束':(topic.my_enroll?'已报名':'立即报名')}}
											</view>
										</view>
									</view>
								</template>
								<!-- 文章 -->
								<template v-else-if="topic.post_type=='post'">
									<!-- 无图 -->
									<template v-if="topic.thumbnails.length==0">
										<view class="zhugie-info-text">
											<view class="zhugie-info-title">{{topic.title}}</view>
											<view class="zhuige-info-post">
												<view class="zhuige-info-data">
													<text
														v-if="topic.read_limit=='cost' && (!is_ios || (is_ios && topic.cost_ios_switch=='1'))"
														class="pay">￥{{topic.cost_price}}</text>
													<text v-if="topic.read_limit=='score'"
														class="pay">{{topic.cost_score}}积分</text>
													<text>浏览 {{topic.views}}</text>
													<text>点赞 {{topic.likes}}</text>
												</view>
											</view>
										</view>
									</template>
									<template v-if="topic.thumbnails.length<3">
										<!-- 大图 -->
										<view v-if="index%5==4" class="zhugie-info-block">
											<view class="zhugie-info-text">
												<view class="zhugie-info-title">{{topic.title}}</view>
											</view>

											<view class="zhugie-info-image">
												<text v-if="topic.badge">{{topic.badge}}</text>
												<image mode="aspectFill" :src="topic.thumbnails[0]" />
											</view>
											<view class="zhugie-info-text">
												<view class="zhuige-info-post">
													<view class="zhuige-info-data">
														<text
															v-if="topic.read_limit=='cost' && (!is_ios || (is_ios && topic.cost_ios_switch=='1'))"
															class="pay">￥{{topic.cost_price}}</text>
														<text v-if="topic.read_limit=='score'"
															class="pay">{{topic.cost_score}}积分</text>
														<text>浏览 {{topic.views}}</text>
														<text>点赞 {{topic.likes}}</text>
													</view>
												</view>
											</view>
										</view>
										<!-- 右图 -->
										<view v-else class="zhugie-info-block right-side">
											<view class="zhugie-info-image">
												<text v-if="topic.badge">{{topic.badge}}</text>
												<image mode="aspectFill" :src="topic.thumbnails[0]" />
											</view>
											<view class="zhugie-info-text">
												<view class="zhugie-info-title">{{topic.title}}</view>
												<view class="zhuige-info-post">
													<view class="zhuige-info-data">
														<text
															v-if="topic.read_limit=='cost' && (!is_ios || (is_ios && topic.cost_ios_switch=='1'))"
															class="pay">￥{{topic.cost_price}}</text>
														<text v-if="topic.read_limit=='score'"
															class="pay">{{topic.cost_score}}积分</text>
														<text>浏览 {{topic.views}}</text>
														<text>点赞 {{topic.likes}}</text>
													</view>
												</view>
											</view>
										</view>
									</template>
									<!-- 左图 -->
									<!-- <view class="zhugie-info-block left-side">
										<view class="zhugie-info-image">
											<text>vip免费</text>
											<image mode="aspectFill" :src="topic.thumbnails[0]" />
										</view>
										<view class="zhugie-info-text">
											<view class="zhugie-info-title">{{topic.title}}</view>
											<view class="zhuige-info-post">
												<view class="zhuige-info-data">
													<text class="pay">1 积分</text>
													<text>浏览 {{topic.views}}</text>
													<text>点赞 {{topic.likes}}</text>
												</view>
											</view>
										</view>
									</view> -->
									<!-- 3图 -->
									<view v-if="topic.thumbnails.length>=3" class="zhugie-info-block">
										<view class="zhugie-info-text">
											<view class="zhugie-info-title">{{topic.title}}</view>
										</view>
										<view class="zhugie-info-image image-treble">
											<text v-if="topic.badge">{{topic.badge}}</text>
											<image mode="aspectFill" :src="topic.thumbnails[0]" />
											<image mode="aspectFill" :src="topic.thumbnails[1]" />
											<image mode="aspectFill" :src="topic.thumbnails[2]" />
										</view>
										<view class="zhugie-info-text">
											<view class="zhuige-info-post">
												<view class="zhuige-info-data">
													<text
														v-if="topic.read_limit=='cost' && (!is_ios || (is_ios && topic.cost_ios_switch=='1'))"
														class="pay">￥{{topic.cost_price}}</text>
													<text v-if="topic.read_limit=='score'"
														class="pay">{{topic.cost_score}}积分</text>
													<text>浏览 {{topic.views}}</text>
													<text>点赞 {{topic.likes}}</text>
												</view>
											</view>
										</view>
									</view>
								</template>
								<!-- 课程 -->
								<template v-else-if="topic.post_type=='zhuige_column'">
									<view class="zhugie-info-block left-side">
										<view class="zhugie-info-image">
											<image mode="aspectFill" :src="topic.thumbnail" />
										</view>
										<view class="zhugie-info-text">
											<view class="zhugie-info-title">{{topic.title}}</view>
											<view class="zhuige-info-data">
												<text v-for="(badge, badgeIndex) in topic.badges"
													:key="badgeIndex">{{badge}}</text>
											</view>
											<view class="zhuige-info-post">
												<view class="zhuige-info-rate">
													<uni-rate :value="topic.score" size="12" :activeColor="'#FF6146'" />
													<view>{{topic.score}}</view>
												</view>

												<template v-if="!is_ios || (is_ios && topic.ios_price_switch==1)">
													<view v-if="topic.limit=='cost'" class="info-money">
														<text>￥</text>
														<text>{{topic.cost_price}}</text>
													</view>
												</template>

												<view v-if="topic.limit=='score'" class="info-point">
													<text>{{topic.cost_score}}</text>
													<text>积分</text>
												</view>
											</view>
										</view>
									</view>
								</template>
								<!-- 知识库 -->
								<template v-else-if="topic.post_type=='zhuige_res'">
									<view class="zhugie-info-block left-side">
										<view class="zhugie-info-image">
											<image mode="aspectFill" :src="topic.thumbnail" />
										</view>
										<view class="zhugie-info-text">
											<view class="zhugie-info-title">{{topic.title}}</view>
											<view class="zhuige-info-data">
												<text>浏览 {{topic.views}}</text>
												<text>点赞 {{topic.likes}}</text>
												<text
													v-if="topic.limit=='cost' && (!is_ios || (is_ios && topic.cost_ios_switch=='1'))"
													class="pay">￥ {{topic.cost_price}}</text>
												<text v-if="topic.limit=='score'"
													class="pay">{{topic.cost_score}}积分</text>
											</view>
										</view>
									</view>
								</template>
								<!-- 积分商品 -->
								<template v-else-if="topic.post_type=='zhuige_goods'">
									<view class="zhugie-info-block left-side">
										<view class="zhugie-info-image">
											<image mode="aspectFill" :src="topic.thumbnail" />
										</view>
										<view class="zhugie-info-text">
											<view class="zhugie-info-title">{{topic.title}}</view>
											<view class="zhuige-info-post zhuige-info-opt">
												<view class="info-point">
													<text>{{topic.price}}</text>
													<text>积分</text>
												</view>
												<uni-icons type="plus-filled" color="#ff6146" size="26"></uni-icons>
											</view>
										</view>
									</view>
								</template>
								<template v-else>
									<view class="zhugie-info-block left-side">
										<view class="zhugie-info-image">
											<image mode="aspectFill" :src="topic.thumbnail" />
										</view>
										<view class="zhugie-info-text">
											<view class="zhugie-info-title">{{topic.title}}</view>
										</view>
									</view>
								</template>
							</view>
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
					<zhuige-user-list v-if="followUser && followUser.users.length>0" type="scroll"
						:title="followUser.title" :users="followUser.users" :buttons="false"
						ext-class="zhuige-user-followed"></zhuige-user-list>
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

			<uni-load-more v-if="(tab_type==1 || (tab_type==2 && cur_tab=='last')) && lastTopics && lastTopics.length>0"
				:status="lastLoadMore">
			</uni-load-more>

			<uni-load-more v-if="cur_tab=='follow' && followTopics && followTopics.length>0" :status="followLoadMore">
			</uni-load-more>
		</template>

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
				// tab_type 1：tab是文章类型；2：tab是帖子（近期、关注）
				tab_type: 2,
				tab_sticky_top: 70,

				slides: [],
				icons: [],
				rec_user: undefined,
				rec_forum: undefined,
				// rec_ad: undefined,

				rec_posts: [],

				// 分享截图
				thumb: undefined,

				// 是否显示tab
				tab_switch: 0,
				// 是否显示list
				list_switch: 0,

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

				is_ios: false,
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

			this.is_ios = (uni.getSystemInfoSync().platform == "ios");

			let menuButtonInfo = uni.getMenuButtonBoundingClientRect();
			this.tab_sticky_top = menuButtonInfo.top + menuButtonInfo.height + 8;

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
			if (this.list_switch != 1) {
				return
			}

			if ((this.tab_type == 1 || (this.tab_type == 2 && this.cur_tab == 'last')) && this.lastLoadMore == 'more') {
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
			 * 点击打开链接
			 */
			clickLink(link) {
				Util.openLink(link);
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

				if (this.tab_type == 1 || (this.tab_type == 2 && this.cur_tab == 'last')) {
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

				if (this.tab_type == 1 || (this.tab_type == 2 && this.cur_tab == 'last' && !this.lastLoaded)) {
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

					if (res.data.rec_posts) {
						this.rec_posts = res.data.rec_posts;
					}

					if (res.data.tabs) {
						this.tabs = res.data.tabs;
					}

					if (res.data.cur_tab) {
						this.cur_tab = res.data.cur_tab;
					}

					if (res.data.tab_type) {
						this.tab_type = res.data.tab_type;
					}

					this.tab_switch = res.data.tab_switch;
					this.list_switch = res.data.list_switch;

					if (res.data.rec_posts) {
						this.rec_posts = res.data.rec_posts;
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

				Rest.post(Api.URL('posts', 'list_last2'), {
					offset: refresh ? 0 : this.lastTopics.length,
					post_type: this.cur_tab
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
		padding: 20rpx 0 60rpx;
	}

	.zhuige-scroll-icon .text {
		color: #666666 !important;
	}

	.zhuige-icon .view {
		width: 18.6% !important;
	}

	.zhuige-social-list .zhugie-info-block .zhuige-info-data text:nth-child(1) {
		margin-left: 0;
	}

	.zhuige-mini-custom .zhugie-info-block {
		border-top: 1rpx solid #DDDDDD;
	}

	.zhuige-mini-custom .zhuige-block,
	.zhuige-mini-custom-twins .zhuige-block {
		padding: 12rpx 20rpx 14rpx;
	}

	.zhuige-mini-custom .zhugie-info-block:nth-child(2) {
		border: none;
		padding-top: 0;
	}

	.zhuige-mini-custom .zhugie-info-block {
		padding: 20rpx 0;
	}
</style>
