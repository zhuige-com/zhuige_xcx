<template>
	<!-- 自定义滚动广告 -->
	<!-- 自定义滚动广告 发现,话题聚合 class 增加 zhuige-scroll-nobox -->
	<!-- 自定义滚动广告 商城 class 增加 zhuige-scroll-mall -->
	<!-- 自定义滚动广告 集市 class 增加 zhuige-scroll-market -->
	<view class="zhuige-scroll-ad-box">
		<view class="zhuige-block-head">
			<view>{{title}}</view>
			<view>滑动查看</view>
		</view>
		<!-- 圈子推荐 class 增加 zhuige-scroll-coterie -->
		<!-- 自定义滚动广告 大商品 首页用; 基础不变，在 zhuige-scroll-ad 后增加 zhuige-scroll-goods -->
		<!-- 自定义滚动广告 小商品 圈子列表,帖子详情,个人主页
			 基础不变，在 zhuige-scroll-ad 后增加 zhuige-scroll-goods -->
		<view class="zhuige-scroll-ad" :class="extAdClass">
			<scroll-view class="scroll-view" scroll-x="true">
				<view v-for="(item, index) in items" :key="index" @click="openLink(item.link)"
					class="zhuige-scroll-ad-block">
					<view class="view">
						<image class="image" mode="aspectFill" :src="item.image"></image>
						<text v-if="item.badge" class="text">{{item.badge}}</text>
					</view>
					<view>
						<view v-if="item.title" class="view">{{item.title}}</view>
						<view v-if="item.subtitle" class="view">{{item.subtitle}}</view>
						<view v-if="item.price" class="view">
							<text class="text">￥</text>
							<text class="text">{{item.price}}</text>
						</view>
					</view>
				</view>
			</scroll-view>
		</view>
	</view>
</template>

<script>
	import Util from '@/utils/util';

	export default {
		name: "zhuige-scroll-ad",

		props: {
			extAdClass: {
				type: String,
				default: ""
			},
			title: {
				type: String,
				default: "热门推荐"
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
	/* =========== 自定义滚动广告 =========== */

	.zhuige-scroll-ad-box {
		padding: 20rpx 0;
		border-radius: 12rpx;
		background: #FFFFFF;
	}

	.zhuige-scroll-ad-box .zhuige-block-head {
		padding: 0 20rpx;
	}

	.zhuige-scroll-ad {
		white-space: nowrap;
	}

	.zhuige-scroll-ad-block {
		display: inline-block;
		position: relative;
		width: 42%;
		margin-right: 12rpx;
	}

	.zhuige-scroll-ad-block:first-child {
		margin-left: 20rpx;
	}

	.zhuige-scroll-ad-block>.view:nth-child(1) {
		position: relative;
		height: 240rpx;
		width: 100%;
	}

	.zhuige-scroll-ad-block>.view:nth-child(1) .image {
		height: 100%;
		width: 100%;
		border-radius: 8rpx;
	}

	.zhuige-scroll-ad-block>.view:nth-child(1) .text {
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

	.zhuige-scroll-ad-block>view:nth-child(2) {
		position: absolute;
		z-index: 3;
		bottom: 0;
		left: 0;
		width: 100%;
		background: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.7));
		padding: 40rpx 0 20rpx;
		border-radius: 0 0 12rpx 12rpx;
	}

	.zhuige-scroll-ad-block>view:nth-child(2) .view {
		color: #FFFFFF;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
		padding: 0 30rpx;
	}

	.zhuige-scroll-ad-block>view:nth-child(2) .view:nth-child(1) {
		font-size: 28rpx;
		font-weight: 600;
		height: 1.4em;
		line-height: 1.4em;
	}

	.zhuige-scroll-ad-block>view:nth-child(2) .view:nth-child(2) {
		font-size: 24rpx;
		font-weight: 300;
		height: 1.4em;
		line-height: 1.4em;
	}


	/* --- 小商品 --- */
	.zhuige-scroll-goods-mini {}

	.zhuige-scroll-goods-mini .zhuige-scroll-ad-block {
		width: 36%;
	}

	.zhuige-scroll-goods-mini .zhuige-scroll-ad-block>.view:nth-child(1) {
		height: 240rpx;
	}

	.zhuige-scroll-goods-mini .zhuige-scroll-ad-block>.view:nth-child(2) {
		position: relative;
		background: none;
		padding-top: 20rpx;
	}

	.zhuige-scroll-goods-mini .zhuige-scroll-ad-block>.view:nth-child(2) .view {
		color: #010101;
		padding: 0;
	}

	.zhuige-scroll-goods-mini .zhuige-scroll-ad-block>.view:nth-child(2) .view:nth-child(2) {
		color: #ff4400;
	}

	.zhuige-scroll-goods-mini .zhuige-scroll-ad-block>.view:nth-child(2) .view .text:nth-child(2) {
		font-size: 36rpx;
		font-weight: 600;
	}

	/* --- 圈子 --- */
	.zhuige-scroll-coterie {
		padding-bottom: 20rpx;
	}

	.zhuige-scroll-coterie .zhuige-scroll-ad-block {
		width: 31%;
	}

	.zhuige-scroll-coterie scroll-view,
	.zhuige-scroll-coterie .zhuige-scroll-ad-block>view:nth-child(1) {
		height: 220rpx;
	}
</style>
