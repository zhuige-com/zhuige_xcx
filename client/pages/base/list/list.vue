<template>
	<view class="content">
		<view class="zhuige-social-list zhuige-mini-custom">
			<template v-if="topics && topics.length>0">
				<template v-for="(topic, index) in topics">
					<zhuige-topic v-if="topic.post_type=='zhuige_bbs_topic'" :key="index" :topic="topic">
					</zhuige-topic>
					<view v-else :key="index" class="zhuige-block" :class="topic.post_type" @click="clickPost(topic)">

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
							<template v-else-if="topic.thumbnails.length<3">
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
							<view v-else-if="topic.thumbnails.length>=3" class="zhugie-info-block">
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
										<text v-if="topic.limit=='score'" class="pay">{{topic.cost_score}}积分</text>
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
						<!-- 投票 -->
						<template v-else-if="topic.post_type=='zhuige_vote'">
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
			<template v-else-if="loaded">
				<zhuige-nodata></zhuige-nodata>
			</template>
		</view>

		<uni-load-more v-if="topics && topics.length>0" :status="loadMore"></uni-load-more>
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
	import ZhuigeNodata from "@/components/zhuige-nodata";

	export default {
		data() {
			this.title = '列表';

			this.tag_id = undefined;
			this.keyword = undefined;

			return {
				topics: [],
				loadMore: 'more',
				loaded: false,
			}
		},

		components: {
			ZhuigeTopic,
			ZhuigeNodata
		},

		onLoad(options) {
			Util.addShareScore(options.source);

			if (options.title) {
				this.title = decodeURIComponent(options.title);
				uni.setNavigationBarTitle({
					title: this.title
				})
			}

			if (options.tag_id) {
				this.tag_id = options.tag_id;
			} else if (options.keyword) {
				this.keyword = options.keyword;
			}

			this.loadTopics(true);

			uni.$on('zhuige_event_user_like', this.onUserLike);
		},

		onUnload() {
			uni.$off('zhuige_event_user_like', this.onUserLike);
		},

		onReachBottom() {
			if (this.loadMore == 'more') {
				this.loadTopics(false);
			}
		},

		onPullDownRefresh() {
			this.loadTopics(true);
		},

		onShareAppMessage() {
			let path = 'pages/base/list/list?title=' + this.title;

			if (this.tag_id) {
				path += '&tag_id=' + this.tag_id;
			}

			if (this.keyword) {
				path += '&keyword=' + this.keyword;
			}

			return {
				title: this.title + '-' + getApp().globalData.appName,
				path: Util.addShareSource(path)
			};
		},

		// #ifdef MP-WEIXIN
		onShareTimeline() {
			return {
				title: this.title + '-' + getApp().globalData.appName,
			};
		},
		// #endif

		methods: {
			// ------- event start ---------
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
			// ------- event end ---------

			/**
			 * 点击打开链接
			 */
			clickLink(link) {
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
			 * 加载文章
			 */
			loadTopics(refresh) {
				if (this.loadMore == 'loading') {
					return;
				}
				this.loadMore = 'loading';

				let url = '';
				let params = {
					offset: refresh ? 0 : this.topics.length
				};

				if (this.tag_id) {
					url = Api.URL('posts', 'list_tag');
					params.tag_id = this.tag_id;
				} else if (this.keyword) {
					url = Api.URL('posts', 'list_search2');
					params.keyword = this.keyword;
				}

				Rest.post(url, params).then(res => {
					this.topics = refresh ? res.data.topics : this.topics.concat(res.data.topics);
					this.loadMore = res.data.more;
					this.loaded = true;

					uni.stopPullDownRefresh();
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
</style>