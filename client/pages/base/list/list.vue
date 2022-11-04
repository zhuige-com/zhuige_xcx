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
										<text v-for="(badge, badgeIndex) in topic.badges" :key="badgeIndex">{{badge}}</text>
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
					this.loadMore = 'nomore';
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
