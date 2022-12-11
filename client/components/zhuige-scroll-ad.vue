<template>
	<!-- 自定义滚动广告 -->
	<!-- 自定义滚动广告 发现,话题聚合 class 增加 zhuige-scroll-nobox -->
	<!-- 自定义滚动广告 商城 class 增加 zhuige-scroll-mall -->
	<!-- 自定义滚动广告 集市 class 增加 zhuige-scroll-market -->
	<view class="zhuige-scroll-ad-box" :class="boxClass"
		:style="background ? 'background: url(' + background + ') no-repeat center; background-size: cover;' : ''">
		<view class="zhuige-block-head">
			<view>{{title}}</view>
			<view>滑动查看</view>
		</view>
		<!-- 圈子推荐 class 增加 zhuige-scroll-coterie -->
		<!-- 自定义滚动广告 大商品 首页用; 基础不变，在 zhuige-scroll-ad 后增加 zhuige-scroll-goods -->
		<!-- 自定义滚动广告 小商品 圈子列表,帖子详情,个人主页
			 基础不变，在 zhuige-scroll-ad 后增加 zhuige-scroll-goods -->
		<view class="zhuige-scroll-ad" :class="extAdClass">
			<scroll-view class="zhuige-scroll-group" scroll-x="true">
				<view v-for="(item, index) in items" :key="index" @click="openLink(item.link)"
					class="zhuige-scroll-ad-block">
					<view class="zhuige-scroll-ad-cover">
						<image class="cover-image" mode="aspectFill" :src="item.image"></image>
						<text v-if="item.badge" class="cover-text">{{item.badge}}</text>
					</view>
					<view class="zhuige-scroll-ad-info">
						<view v-if="item.title" class="title-info">{{item.title}}</view>
						<view v-if="item.subtitle" class="subtitle-info">{{item.subtitle}}</view>
						<view v-if="item.price" class="price-info">
							<text class="price-unit">￥</text>
							<text class="item-price">{{item.price}}</text>
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
			boxClass: {
				type: String,
				default: ""
			},
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
			background: {
				type: String,
				default: ''
			}
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

	.zhuige-scroll-ad-cover {
		position: relative;
		height: 240rpx;
		width: 100%;
	}

	.cover-image {
		height: 100%;
		width: 100%;
		border-radius: 8rpx;
	}

	.cover-text {
		position: absolute;
		top: 0;
		left: 30rpx;
		height: 48rp;
		line-height: 48rpx;
		padding: 0 24rpx;
		border-radius: 0 0 8rpx 8rpx;
		background: #010101;
		color: #FFFFFF;
		font-size: 22rpx;
		font-weight: 400;
	}

	.zhuige-scroll-ad-info {
		position: absolute;
		z-index: 3;
		bottom: 0;
		left: 0;
		width: 100%;
		background: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.7));
		padding: 40rpx 0 20rpx;
		border-radius: 0 0 12rpx 12rpx;
	}

	.title-info, .subtitle-info, .price-info  {
		color: #FFFFFF;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
		padding: 0 30rpx;
	}

	.title-info {
		font-size: 26rpx;
		font-weight: 500;
		height: 1.4em;
		line-height: 1.4em;
	}

	.subtitle-info {
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

	.zhuige-scroll-goods-mini .zhuige-scroll-ad-cover {
		height: 240rpx;
	}

	.zhuige-scroll-goods-mini .zhuige-scroll-ad-info {
		position: relative;
		background: none;
		padding-top: 20rpx;
	}

	.zhuige-scroll-goods-mini .title-info, .zhuige-scroll-goods-mini .subtitle-info {
		color: #010101;
		padding: 0;
	}

	.zhuige-scroll-goods-mini .price-info {
		color: #ff4400;
	}

	.zhuige-scroll-goods-mini .price-info .item-price {
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

	.zhuige-scroll-coterie .zhuige-scroll-group,
	.zhuige-scroll-coterie .zhuige-scroll-ad-cover {
		height: 240rpx;
	}
	
	.zhuige-topic-scroll .zhuige-scroll-ad-block {
		width: 42%!important;
	}
	.zhuige-topic-scroll .zhuige-scroll-ad-block .cover-text {
		display: none;
	}
		
</style>
