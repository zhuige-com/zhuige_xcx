<template>
	<view class="content">
		<view class="zhuige-social-list">
			<template v-if="topics && topics.length>0">
				<zhuige-topic v-for="(topic, index) in topics" :key="index" :topic="topic"></zhuige-topic>
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
				if (this.topics && this.topics.length>0) {
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
					url = Api.URL('posts', 'list_search');
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
