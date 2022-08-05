<template>
	<view class="zhuige-treble-ad-box">
		<view class="zhuige-block-head">
			<view>自定义广告标题</view>
			<view>描述信息</view>
		</view>
		<view class="zhuige-treble-ad">
			<view>
				<text>角标</text>
				<image mode="aspectFill" src="/static/logo.png"></image>
				<view>
					<view>广告标题广告标题广告标题广告标题</view>
				</view>
			</view>
			<view>
				<text>角标</text>
				<image mode="aspectFill" src="/static/logo.png"></image>
				<view>
					<view>广告标题广告标题广告标题广告标题</view>
				</view>
			</view>
			<view>
				<image mode="aspectFill" src="/static/logo.png"></image>
				<view>
					<view>广告标题广告标题广告标题广告标题</view>
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
		padding: 20rpx;
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
		height: 490rpx;
		border-radius: 12rpx 0 0 12rpx;
	}

	.zhuige-treble-ad>view:nth-child(2) {
		height: 240rpx;
		border-radius: 0 12rpx 0 0;
	}

	.zhuige-treble-ad>view:nth-child(3) {
		position: absolute;
		height: 240rpx;
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
		height: 60rp;
		line-height: 60rpx;
		padding: 0 24rpx;
		border-radius: 0 0 8rpx 8rpx;
		background: #010101;
		color: #FFFFFF;
		font-size: 22rpx;
		font-weight: 300;
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
	}
</style>
