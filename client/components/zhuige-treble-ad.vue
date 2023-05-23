<template>
	<view class="zhuige-treble-ad-box">
		<view v-if="title || description" class="zhuige-block-head">
			<view v-if="title">{{title}}</view>
			<view v-if="description">{{description}}</view>
		</view>
		<view class="zhuige-treble-ad">
			<view v-for="(item, index) in items" :key="index" @click="openLink(item.link)">
				<text v-if="item.badge">{{item.badge}}</text>
				<image mode="aspectFill" :src="item.image"></image>
				<view>
					<view>{{item.title}}</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import Util from '@/utils/util';

	export default {
		name: "zhuige-treble-ad",

		props: {
			prop: {
				type: String,
				default: "prop"
			},
			title: {
				type: String,
				default: ""
			},
			description: {
				type: String,
				default: ""
			},
			items: {
				type: Array,
				default: []
			},
		},

		data() {
			return {

			};
		},

		methods: {
			openLink(link) {
				Util.openLink(link);
			},
		}
	}
</script>

<style>
	/* =========== 三图广告 =========== */
	.zhuige-treble-ad-box {
		padding: 20rpx 20rpx 30rpx;
		border-radius: 12rpx;
		background: #FFFFFF;

	}

	.zhuige-treble-ad {
		display: flex;
		justify-content: space-between;
		position: relative;
	}

	.zhuige-treble-ad>view {
		position: relative;
		overflow: hidden;
		width: 49.4%;
	}

	.zhuige-treble-ad>view:nth-child(1) {
		height: 410rpx;
		border-radius: 12rpx 0 0 12rpx;
	}

	.zhuige-treble-ad>view:nth-child(2) {
		height: 200rpx;
		border-radius: 0 12rpx 0 0;
	}

	.zhuige-treble-ad>view:nth-child(3) {
		position: absolute;
		height: 200rpx;
		bottom: 0;
		right: 0;
		border-radius: 0 0 12rpx 0;
	}

	.zhuige-treble-ad>view>image {
		height: 100%;
		width: 100%;
	}

	.zhuige-treble-ad>view>text {
		position: absolute;
		top: 0;
		left: 30rpx;
		height: 48rpx;
		line-height: 48rpx;
		padding: 0 20rpx;
		border-radius: 0 0 8rpx 8rpx;
		background: #FF6146;
		color: #FFFFFF;
		font-size: 22rpx;
		font-weight: 400;
	}

	.zhuige-treble-ad>view>view {
		position: absolute;
		z-index: 3;
		bottom: 0;
		left: 0;
		width: 100%;
		background: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.7));
		padding: 40rpx 0 20rpx;
	}

	.zhuige-treble-ad>view>view>view {
		color: #FFFFFF;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
		padding: 0 30rpx;
		font-size: 28rpx;
		font-weight: 600;
	}
</style>