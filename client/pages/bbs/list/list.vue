<template>
	<view class="content">
		<!-- 话题头部信息，当通过搜索进入list时，该模块不显示 -->
		<view v-if="subject" class="zhuige-topic-header">
			<view class="zhuige-topic-bg">
				<view class="zhuige-topic-mask"></view>
				<image mode="aspectFill" :src="subject.cover"></image>
			</view>

			<view class="zhuige-classify-block">
				<view>
					<image mode="aspectFill" :src="subject.logo"></image>
				</view>
				<view class="zhuige-classify-text">
					<view>{{subject.name}}</view>
					<view>
						<text>{{subject.count}} 作品</text>
					</view>
				</view>
			</view>
		</view>

		<!-- 帖子列表 -->
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

			this.subject_id = undefined;

			return {
				subject: undefined,

				topics: [],
				loadMore: 'more',
				loaded: false,

				// #ifdef MP-WEIXIN
				traffic_list: undefined,
				// #endif
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

			if (options.subject_id) {
				this.subject_id = options.subject_id;
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
			let path = 'pages/bbs/list/list?n=n';
			if (this.title) {
				path += '&title=' + this.title;
			}

			if (this.subject_id) {
				path += '&subject_id=' + this.subject_id;
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
			 * 加载帖子
			 */
			loadTopics(refresh) {
				let url = '';
				let params = {
					offset: refresh ? 0 : this.topics.length
				};

				if (this.subject_id) {
					url = Api.URL('bbs', 'topic_list_subject');
					params.subject_id = this.subject_id;
				}

				Rest.post(url, params).then(res => {
					if (res.data.subject) {
						this.subject = res.data.subject;
					}

					// #ifdef MP-WEIXIN
					if (res.data.traffic_list) {
						this.traffic_list = res.data.traffic_list;
					}
					// #endif

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
		background: #f5f5f5;
	}

	.zhuige-social-list {
		margin-top: -80rpx;
		position: relative;
		z-index: 9;
	}
</style>