<template>
	<view class="content">
		<view class="zhuige-social-list zhuige-mini-custom">
			<template v-if="topics && topics.length>0">
				<view v-for="(topic, index) in topics" :key="index">
					<zhuige-topic v-if="topic.post_type=='zhuige_bbs_topic'" :topic="topic">
					</zhuige-topic>
					<view v-else class="zhuige-block" :class="topic.post_type" @click="clickPost(topic)">

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
										{{topic.is_end?'已结束':(topic.my_enroll?'已报名':( '立即报名' + (topic.cost>0?'(￥'+topic.cost+')':'') ))}}
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
							<view class="zhuige-social-poster-blcok" @click="openLink('/pages/user/home/home?user_id=' + topic.author.user_id)">
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
									<view v-for="(item, ito) in topic.options" :key="ito" class="zhuige-vote-option"
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
						
						<!-- 商家名片 -->
						<template v-else-if="topic.post_type=='zhuige_business_card'">
							<view class="zhuige-sp-list-block sp-left">
								<view class="zhuige-sp-img">
									<image mode="aspectFill" :src="topic.logo"></image>
								</view>
								<view class="zhuige-sp-text">
									<view class="zhuige-sp-title">
										<text v-if="topic.stick" class="tag">推广</text>
										<text>{{topic.title}}</text>
									</view>
									<view class="zhuige-sp-info">
										<text>{{topic.excerpt}}</text>
									</view>
									<view class="zhuige-sp-opt">
										<!-- 评分 -->
										<view class="zhuige-sp-star">
											<uni-rate :value="topic.score" size="12" active-color="#ff6146"></uni-rate>
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
											<image mode="aspectFill" :src="topic.user.avatar"></image>
										</view>
										<view class="zhuige-social-poster-info">
											<view>
												<text>{{topic.user.nickname}}</text>
												<image v-if="topic.user.certify && topic.user.certify.status==1" mode="aspectFill"
													:src="topic.user.certify.icon">
												</image>
												<image v-if="topic.user.vip && topic.user.vip.status==1" class="zhuige-social-vip" mode="aspectFill"
													:src="topic.user.vip.icon">
												</image>
											</view>
											<view>
												<text>{{topic.time}}</text>
												<text v-if="topic.user.certify && topic.user.certify.status==1">/</text>
												<text v-if="topic.user.certify && topic.user.certify.status==1">{{topic.user.certify.name}}</text>
											</view>
										</view>
									</view>
								</view>
								<!-- 
								<view class="zhuige-social-cont">
									{{topic.excerpt}}
								</view> -->
						
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
						
								<!-- 产品分类及帖子数据信息 -->
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
						
						<!-- 圈子 -->
						<template v-else-if="topic.post_type=='zhuige_bbs_forum'">
							<view class="zhugie-info-block left-side">
								<view class="zhugie-info-image">
									<image mode="aspectFill" :src="topic.logo" />
								</view>
								<view class="zhugie-info-text">
									<view class="zhugie-info-title">{{topic.title}}</view>
									<view>
										<text>{{topic.user_count}}成员</text>
										<text style="padding: 0 12rpx;">/</text>
										<text>{{topic.post_count}}动态</text>
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
				</view>
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
	 * Copyright © 2022-2024 www.zhuige.com All rights reserved.
	 */

	import Util from '@/utils/util';
	import Alert from '@/utils/alert';
	import Api from '@/utils/api';
	import Rest from '@/utils/rest';

	import ZhuigeTopic from "@/components/zhuige-topic";
	import ZhuigeNodata from "@/components/zhuige-nodata";

	export default {
		components: {
			ZhuigeTopic,
			ZhuigeNodata
		},
		
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
	.zhuige-mini-custom {
		padding-top: 20rpx;
	}
	
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
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;
		flex-wrap: nowrap;
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
	
</style>