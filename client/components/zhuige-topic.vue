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
						<image v-if="topic.author.vip" mode="aspectFill" src="/static/vv-1.png">
						</image>
						<image v-if="topic.author.certify" mode="aspectFill" src="/static/lvv.png">
						</image>
					</view>
					<view>
						<text>{{topic.time}}</text>
						<text v-if="topic.author.certify">/</text>
						<text v-if="topic.author.certify">认证信息</text>
					</view>
				</view>
			</view>
			<!--<view class="zhuige-social-opt">
				<uni-icons type="bars" color="#999999" size="22"></uni-icons>
			</view>-->
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
					<view v-for="(image, imageIndex) in topic.images" :key="imageIndex">
						<image mode="aspectFill" :src="image.image.url" @click="clickImages(imageIndex)" :style="{
								width: parseInt(image.image.width)>parseInt(image.image.height)?'674rpx':parseInt(image.image.width)/parseInt(image.image.height)*674 + 'rpx', 
								height: parseInt(image.image.width)<parseInt(image.image.height)?'674rpx':parseInt(image.image.height)/parseInt(image.image.width)*674 + 'rpx'
							}">
						</image>
					</view>
				</view>
				<view v-else class="zhugie-img" :class="imageClass[topic.images.length]">
					<view v-for="(image, imageIndex) in topic.images" :key="imageIndex">
						<image mode="aspectFill" :src="image.image.url" @click="clickImages(imageIndex)"></image>
					</view>
				</view>
			</view>
			<view v-if="topic.type=='video'">
				<video :src="topic.video.url" :poster="topic.video_cover.url" object-fit="cover" :style="{
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
			}
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
		/* width: 620rpx; */
	}

	.zhugie-img view {
		/* background: #EEEEEE; */
		/* 调试用，上线去掉 */
	}

	.zhugie-img view image {
		width: 100%;
		height: 100%;
	}

	/* 
	.one-img {
		justify-content: center;
	}

	.one-img view {
		height: 674rpx;
		width: 674rpx;
	}

 */
	.one-img image {
		border-radius: 12rpx;
	}

	.two-img {
		justify-content: space-between;
		align-items: center;
	}

	.two-img view {
		height: 332rpx;
		width: 332rpx;
	}

	.two-img view:nth-child(1) image {
		border-radius: 12rpx 0 0 12rpx;
	}

	.two-img view:nth-child(2) image {
		border-radius: 0 12rpx 12rpx 0;
	}

	.three-img {
		justify-content: space-between;
		flex-wrap: wrap;
	}

	.three-img view {
		height: 332rpx;
		width: 332rpx;
	}

	.three-img view:nth-child(1) {
		height: 446rpx;
		width: 674rpx;
		margin-bottom: 10rpx;
	}

	.three-img view:nth-child(1) image {
		border-radius: 12rpx 12rpx 0 0;
	}

	.three-img view:nth-child(2) image {
		border-radius: 0 0 0 12rpx;
	}

	.three-img view:nth-child(3) image {
		border-radius: 0 0 12rpx 0;
	}

	.four-img {
		justify-content: space-between;
		flex-wrap: wrap;
	}

	.four-img view {
		height: 218rpx;
		width: 218rpx;
	}

	.four-img view:nth-child(1) {
		height: 446rpx;
		width: 674rpx;
		margin-bottom: 10rpx;
	}

	.four-img view:nth-child(1) image {
		border-radius: 12rpx 12rpx 0 0;
	}

	.four-img view:nth-child(2) image {
		border-radius: 0 0 0 12rpx;
	}

	.four-img view:nth-child(4) image {
		border-radius: 0 0 12rpx 0;
	}

	.five-img {
		justify-content: space-between;
		flex-wrap: wrap;
	}

	.five-img view {
		height: 332rpx;
		width: 332rpx;
	}

	.five-img view:nth-child(1) {
		height: 446rpx;
		width: 446rpx;
		margin-bottom: 10rpx;
	}

	.five-img view:nth-child(2) {
		height: 218rpx;
		width: 218rpx;
		margin-top: -238rpx;
	}

	.five-img view:nth-child(3) {
		height: 218rpx;
		width: 218rpx;
		position: absolute;
		right: 0;
		top: 228rpx;
	}

	.five-img view:nth-child(1) image {
		border-radius: 12rpx 0 0 0;
	}

	.five-img view:nth-child(2) image {
		border-radius: 0 12rpx 0 0;
	}

	.five-img view:nth-child(4) image {
		border-radius: 0 0 0 12rpx;
	}

	.five-img view:nth-child(5) image {
		border-radius: 0 0 12rpx 0;
	}

	.six-img {
		justify-content: space-between;
		flex-wrap: wrap;
	}

	.six-img view {
		height: 218rpx;
		width: 218rpx;
	}

	.six-img view:nth-child(1) {
		height: 446rpx;
		width: 446rpx;
		margin-bottom: 10rpx;
	}

	.six-img view:nth-child(2) {
		margin-top: -238rpx;
	}

	.six-img view:nth-child(3) {
		position: absolute;
		right: 0;
		top: 228rpx;
	}

	.six-img view:nth-child(1) image {
		border-radius: 12rpx 0 0 0;
	}

	.six-img view:nth-child(2) image {
		border-radius: 0 12rpx 0 0;
	}

	.six-img view:nth-child(4) image {
		border-radius: 0 0 0 12rpx;
	}

	.six-img view:nth-child(6) image {
		border-radius: 0 0 12rpx 0;
	}

	.seven-img {
		justify-content: space-between;
		flex-wrap: wrap;
	}

	.seven-img view {
		height: 218rpx;
		width: 218rpx;
		margin-bottom: 10rpx;
	}

	.seven-img view:nth-child(1) {
		height: 446rpx;
		width: 674rpx;
	}

	.seven-img view:nth-child(5),
	.seven-img view:nth-child(6),
	.seven-img view:nth-child(7) {
		margin: 0;
	}

	.seven-img view:nth-child(1) image {
		border-radius: 12rpx 12rpx 0 0;
	}

	.seven-img view:nth-child(5) image {
		border-radius: 0 0 0 12rpx;
	}

	.seven-img view:nth-child(7) image {
		border-radius: 0 0 12rpx 0;
	}

	.eight-img {
		justify-content: space-between;
		flex-wrap: wrap;
	}

	.eight-img view {
		height: 218rpx;
		width: 218rpx;
		margin-bottom: 10rpx;
	}

	.eight-img view:nth-child(6),
	.eight-img view:nth-child(7),
	.eight-img view:nth-child(8) {
		margin: 0;
	}

	.eight-img view:nth-child(1),
	.eight-img view:nth-child(2) {
		height: 332rpx;
		width: 332rpx;
	}

	.eight-img view:nth-child(1) image {
		border-radius: 12rpx 0 0 0;
	}

	.eight-img view:nth-child(2) image {
		border-radius: 0 12rpx 0 0;
	}

	.eight-img view:nth-child(6) image {
		border-radius: 0 0 0 12rpx;
	}

	.eight-img view:nth-child(8) image {
		border-radius: 0 0 12rpx 0;
	}

	.nine-img {
		justify-content: space-between;
		flex-wrap: wrap;
	}

	.nine-img view {
		height: 218rpx;
		width: 218rpx;
		margin-bottom: 10rpx;
	}

	.nine-img view:nth-child(7),
	.nine-img view:nth-child(8),
	.nine-img view:nth-child(9) {
		margin: 0;
	}

	.nine-img view:nth-child(1) image {
		border-radius: 12rpx 0 0 0;
	}

	.nine-img view:nth-child(3) image {
		border-radius: 0 12rpx 0 0;
	}

	.nine-img view:nth-child(7) image {
		border-radius: 0 0 0 12rpx;
	}

	.nine-img view:nth-child(9) image {
		border-radius: 0 0 12rpx 0;
	}
</style>
