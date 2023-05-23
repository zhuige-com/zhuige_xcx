<template>
	<view class="zhuige-block">
		<!-- 用户信息 -->
		<view v-if="topic && topic.author" class="zhuige-social-poster-blcok">
			<view class="zhuige-social-poster"
				@click="openLink('/pages/user/home/home?user_id=' + topic.author.user_id)">
				<view class="zhuige-social-poster-avatar">
					<image mode="aspectFill" :src="topic.author.avatar"></image>
				</view>
				<view class="zhuige-social-poster-info">
					<view>
						<text>{{topic.author.nickname}}</text>
						<image v-if="topic.author.certify && topic.author.certify.status==1" mode="aspectFill"
							:src="topic.author.certify.icon">
						</image>
						<image v-if="topic.author.vip && topic.author.vip.status==1" class="zhuige-social-vip"
							mode="aspectFill" :src="topic.author.vip.icon">
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
			<view v-if="trash" class="zhuige-social-opt social-dell">
				<uni-icons type="trash" color="#FF6146" size="16" @click="clickTrashTopic(topic)"></uni-icons>
			</view>
		</view>

		<!-- 话题 + 正文 -->
		<view class="zhuige-social-cont">
			<!-- 正文 -->
			<template v-if="topic && topic.subjects.length>0" class="zhuige-social-cont">
				<text v-for="(subject, subjectIndex) in topic.subjects" :key="subjectIndex" class="zhuige-social-tag"
					@click="openLink('/pages/bbs/list/list?subject_id=' + subject.id + '&title=' + subject.name)">#{{subject.name}}</text>
			</template>

			<!-- 正文信息 -->
			<template v-if="topic && topic.excerpt" class="zhuige-social-cont">
				<text v-if="topic.stick" class="zhuige-social-top">置顶</text>
				<text @click="clickDetail">{{topic.excerpt}}</text>
			</template>
		</view>

		<!-- 图片信息 -->
		<template v-if="topic">
			<view v-if="topic.type=='image'" class="zhuige-social-img">
				<view v-if="topic.images.length==1" class="zhugie-img one-img">
					<view class="img-box" v-for="(image, imageIndex) in topic.images" :key="imageIndex">
						<image class="one-img-cover" mode="aspectFill" :src="image.image.url"
							@click="clickImages(imageIndex)" :style="{
								width: parseInt(image.image.width)>parseInt(image.image.height)?'480rpx':parseInt(image.image.width)/parseInt(image.image.height)*480 + 'rpx', 
								height: parseInt(image.image.width)<parseInt(image.image.height)?'480rpx':parseInt(image.image.height)/parseInt(image.image.width)*480 + 'rpx'
							}">
						</image>
					</view>
				</view>
				<view v-else class="zhugie-img" :class="imageClass[topic.images.length]">
					<view :class="'img-box ' + 'img-box-' + (imageIndex + 1)"
						v-for="(image, imageIndex) in topic.images" :key="imageIndex">
						<!-- 
							class="img-cover-length" length为变量，对应多图时各数量的图片序号
							如：3图的时候对应输出img-cover-1，img-cover-2，img-cover-3
						  -->
						<image class="img-cover" mode="aspectFill" :src="image.image.url"
							@click="clickImages(imageIndex)"></image>
					</view>
				</view>
			</view>
			<view v-if="topic.type=='video'">
				<video :id="'topic_list_' + topic.id" :src="topic.video.url" :poster="topic.video_cover.url"
					object-fit="cover" @play="videoPlay" :style="{
					width: parseInt(topic.video.width)>parseInt(topic.video.height)?'674rpx':parseInt(topic.video.width)/parseInt(topic.video.height)*674 + 'rpx',
					height: parseInt(topic.video.width)<parseInt(topic.video.height)?'674rpx':parseInt(topic.video.height)/parseInt(topic.video.width)*674 + 'rpx'
				}"></video>
			</view>
		</template>

		<!-- 地址信息 -->
		<view v-if="topic.location && topic.location.marker" @click="clickDetail" class="zhuige-social-address">
			<view>
				<uni-icons type="location-filled" size="20"></uni-icons>
				<view>{{topic.location.marker}}</view>
			</view>
		</view>

		<!-- 圈子及帖子数据信息 -->
		<view v-if="topic" class="zhuige-social-data">
			<view v-if="topic.forum" @click="openLink('/pages/bbs/forum/forum?forum_id=' + topic.forum.id)">
				<image mode="aspectFill" src="/static/quan.png"></image>
				<view>{{topic.forum.name}}</view>
			</view>
			<view @click="clickDetail">
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

		<!-- 帖子回复信息 -->
		<view v-if="topic && topic.comments.length>0" @click="clickDetail" class="zhuige-social-simple-reply">
			<view v-for="(comment, index) in topic.comments" :key="index">
				<text>{{comment.user.nickname}}</text>
				<text>：<template v-if="comment.reply">@{{comment.reply.nickname}}</template> {{comment.content}}</text>
			</view>
		</view>
	</view>
</template>

<script>
	import Util from '@/utils/util';

	export default {
		name: "zhuige-topic",

		props: {
			prop: {
				type: String,
				default: "prop"
			},
			topic: {
				type: Object,
				default: undefined
			},
			trash: {
				type: Boolean,
				default: false
			}
		},

		data() {
			return {
				imageClass: ['', 'one-img', 'two-img', 'three-img', 'four-img', 'five-img', 'six-img', 'seven-img',
					'eight-img', 'nine-img'
				]
			};
		},

		methods: {
			openLink(link) {
				Util.openLink(link);
			},

			clickImages(index) {
				let urls = [];
				this.topic.images.forEach(image => {
					urls.push(image.image.url);
				});

				uni.previewImage({
					current: index,
					urls: urls,
				})
			},

			clickDetail() {
				Util.openLink('/pages/bbs/detail/detail?topic_id=' + this.topic.id);
			},

			clickTrashTopic(topic) {
				this.$emit("deleteTopic", topic);
			},

			videoPlay(e) {
				let videoId = e.currentTarget.id;
				if (getApp().globalData.videoId !== videoId && getApp().globalData.videoContext) {
					getApp().globalData.videoContext.stop();
				}
				getApp().globalData.videoId = videoId;
				////创建控制视频标签的实例对象
				getApp().globalData.videoContext = wx.createVideoContext(getApp().globalData.videoId, this);
			},
		}
	}
</script>

<style>
	/* =========== 9图展示 =========== */
	.zhugie-img {
		display: flex;
		align-items: center;
		position: relative;
		margin: 10rpx 0;
		width: 674rpx;
	}

	.zhugie-img .img-box .img-cover {
		width: 100%;
		height: 100%;
	}

	.one-img .one-img-cover {
		border-radius: 12rpx;
	}

	.two-img {
		justify-content: space-between;
		align-items: center;
	}

	.two-img .img-box-1,
	.two-img .img-box-2 {
		height: 332rpx;
		width: 332rpx;
	}

	.two-img .img-box-1 .img-cover {
		border-radius: 12rpx 0 0 12rpx;
	}

	.two-img .img-box-2 .img-cover {
		border-radius: 0 12rpx 12rpx 0;
	}

	.three-img {
		justify-content: space-between;
		flex-wrap: wrap;
	}

	.three-img .img-box-1,
	.three-img .img-box-2,
	.three-img .img-box-3 {
		height: 332rpx;
		width: 332rpx;
	}

	.three-img .img-box-1 {
		height: 446rpx;
		width: 674rpx;
		margin-bottom: 10rpx;
	}

	.three-img .img-box-1 .img-cover {
		border-radius: 12rpx 12rpx 0 0;
	}

	.three-img .img-box-2 .img-cover {
		border-radius: 0 0 0 12rpx;
	}

	.three-img .img-box-3 .img-cover {
		border-radius: 0 0 12rpx 0;
	}

	.four-img {
		justify-content: space-between;
		flex-wrap: wrap;
	}

	.four-img .img-box-1,
	.four-img .img-box-2,
	.four-img .img-box-3,
	.four-img .img-box-4 {
		height: 218rpx;
		width: 218rpx;
	}

	.four-img .img-box-1 {
		height: 446rpx;
		width: 674rpx;
		margin-bottom: 10rpx;
	}

	.four-img .img-box-1 .img-cover {
		border-radius: 12rpx 12rpx 0 0;
	}

	.four-img .img-box-2 .img-cover {
		border-radius: 0 0 0 12rpx;
	}

	.four-img .img-box-3 .img-cover {
		border-radius: 0 0 12rpx 0;
	}

	.five-img {
		justify-content: space-between;
		flex-wrap: wrap;
	}

	.five-img .img-box-1,
	.five-img .img-box-2,
	.five-img .img-box-3,
	.five-img .img-box-4,
	.five-img .img-box-5 {
		height: 332rpx;
		width: 332rpx;
	}

	.five-img .img-box-1 {
		height: 446rpx;
		width: 446rpx;
		margin-bottom: 10rpx;
	}

	.five-img .img-box-2 {
		height: 218rpx;
		width: 218rpx;
		margin-top: -238rpx;
	}

	.five-img .img-box-3 {
		height: 218rpx;
		width: 218rpx;
		position: absolute;
		right: 0;
		top: 228rpx;
	}

	.five-img .img-box-1 .img-cover {
		border-radius: 12rpx 0 0 0;
	}

	.five-img .img-box-2 .img-cover {
		border-radius: 0 12rpx 0 0;
	}

	.five-img .img-box-4 .img-cover {
		border-radius: 0 0 0 12rpx;
	}

	.five-img .img-box-5 .img-cover {
		border-radius: 0 0 12rpx 0;
	}

	.six-img {
		justify-content: space-between;
		flex-wrap: wrap;
	}

	.six-img .img-box-1,
	.six-img .img-box-2,
	.six-img .img-box-3,
	.six-img .img-box-4,
	.six-img .img-box-5,
	.six-img .img-box-6 {
		height: 218rpx;
		width: 218rpx;
	}

	.six-img .img-box-1 {
		height: 446rpx;
		width: 446rpx;
		margin-bottom: 10rpx;
	}

	.six-img .img-box-2 {
		margin-top: -238rpx;
	}

	.six-img .img-box-3 {
		position: absolute;
		right: 0;
		top: 228rpx;
	}

	.six-img .img-box-1 .img-cover {
		border-radius: 12rpx 0 0 0;
	}

	.six-img .img-box-2 .img-cover {
		border-radius: 0 12rpx 0 0;
	}

	.six-img .img-box-4 .img-cover {
		border-radius: 0 0 0 12rpx;
	}

	.six-img .img-box-6 .img-cover {
		border-radius: 0 0 12rpx 0;
	}

	.seven-img {
		justify-content: space-between;
		flex-wrap: wrap;
	}

	.seven-img .img-box-1,
	.seven-img .img-box-2,
	.seven-img .img-box-3,
	.seven-img .img-box-4,
	.seven-img .img-box-5,
	.seven-img .img-box-6,
	.seven-img .img-box-7 {
		height: 218rpx;
		width: 218rpx;
		margin-bottom: 10rpx;
	}

	.seven-img .img-box-1 {
		height: 446rpx;
		width: 674rpx;
	}

	.seven-img .img-box-5,
	.seven-img .img-box-6,
	.seven-img .img-box-7 {
		margin: 0;
	}

	.seven-img .img-box-1 .img-cover {
		border-radius: 12rpx 12rpx 0 0;
	}

	.seven-img .img-box-5 .img-cover {
		border-radius: 0 0 0 12rpx;
	}

	.seven-img .img-box-7 .img-cover {
		border-radius: 0 0 12rpx 0;
	}

	.eight-img {
		justify-content: space-between;
		flex-wrap: wrap;
	}

	.eight-img .img-box-1,
	.eight-img .img-box-2,
	.eight-img .img-box-3,
	.eight-img .img-box-4,
	.eight-img .img-box-5,
	.eight-img .img-box-6,
	.eight-img .img-box-7,
	.eight-img .img-box-8 {
		height: 218rpx;
		width: 218rpx;
		margin-bottom: 10rpx;
	}

	.eight-img .img-box-6,
	.eight-img .img-box-7,
	.eight-img .img-box-8 {
		margin: 0;
	}

	.eight-img .img-box-1,
	.eight-img .img-box-2 {
		height: 332rpx;
		width: 332rpx;
	}

	.eight-img .img-box-1 .img-cover {
		border-radius: 12rpx 0 0 0;
	}

	.eight-img .img-box-2 .img-cover {
		border-radius: 0 12rpx 0 0;
	}

	.eight-img .img-box-6 .img-cover {
		border-radius: 0 0 0 12rpx;
	}

	.eight-img .img-box-8 .img-cover {
		border-radius: 0 0 12rpx 0;
	}

	.nine-img {
		justify-content: space-between;
		flex-wrap: wrap;
	}

	.nine-img .img-box-1,
	.nine-img .img-box-2,
	.nine-img .img-box-3,
	.nine-img .img-box-4,
	.nine-img .img-box-5,
	.nine-img .img-box-6,
	.nine-img .img-box-7,
	.nine-img .img-box-8,
	.nine-img .img-box-9 {
		height: 218rpx;
		width: 218rpx;
		margin-bottom: 10rpx;
	}

	.nine-img .img-box-7,
	.nine-img .img-box-8,
	.nine-img .img-box-9 {
		margin: 0;
	}

	.nine-img .img-box-1 .img-cover {
		border-radius: 12rpx 0 0 0;
	}

	.nine-img .img-box-3 .img-cover {
		border-radius: 0 12rpx 0 0;
	}

	.nine-img .img-box-7 .img-cover {
		border-radius: 0 0 0 12rpx;
	}

	.nine-img .img-box-9 .img-cover {
		border-radius: 0 0 12rpx 0;
	}
</style>