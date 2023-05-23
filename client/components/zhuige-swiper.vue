<template>
	<!--
	基础轮播图 首页、专栏轮播, 
	需要调整轮播点位置，swiper增加 zhuige-dots-left 即可。
	市集列表小轮播共用此结构,但需要根据瀑布流模块高度传参，
	class="zhuige-swiper"后增加style=“height: 高度变量”
	-->

	<!-- 带间距播图 发现、资讯轮播, 后增加 zhuige-space-swiper
	需要调整轮播点位置，swiper增加 zhuige-dots-left 即可
	
	previous-margin 和 next-margin 为轮播图间距默认0，修改为30rpx
	
	-->

	<!-- 市集圆角轮播图 ,
	基础 zhuige-swiper 后增加 zhuige-cover-swiper 
	需要调整轮播点位置，swiper增加 zhuige-dots-left 即可
	-->

	<!-- 活动、商城轮播图 基础 zhuige-swiper 后增加 zhuige-height-swiper 即可 -->

	<!-- 我的轮播图 基础 zhuige-swiper 后增加 zhuige-mini-swiper 即可 -->

	<view class="zhuige-swiper" :class="type">
		<swiper indicator-dots="true" autoplay="autoplay" circular="ture" class="zhuige-swiper-group zhuige-dots-left"
			:previous-margin="previousMargin" :next-margin="nextMargin" indicator-color="rgba(255,255,255, 0.3)"
			indicator-active-color="rgba(255,255,255, 0.8)" interval="5000" duration="150" easing-function="linear">
			<!-- 
				indicator-color 和 indicator-active-color 为轮播点颜色，如果需要调整，只能在页面修改参数，或者通过后台传参
				previous-margin 和 next-margin 为轮播图间距默认0，如果需要调整，只能在页面修改参数，或者通过后台传参
			-->
			<swiper-item class="zhuige-swiper-item" v-for="(item, index) in items" :key="index">
				<view class="zhuige-swiper-block" @click="clickItem(index)">
					<view class="zhuige-swiper-title" v-if="item.title">{{item.title}}</view>
					<image class="zhuige-swiper-cover" mode="aspectFill" :src="item.image"></image>
				</view>
			</swiper-item>
		</swiper>
	</view>
</template>

<script>
	import Util from '@/utils/util';

	export default {
		name: "zhuige-swiper",

		props: {
			type: {
				type: String,
				default: ""
			},
			items: {
				type: Array,
				default: []
			},
			previousMargin: {
				type: String,
				default: '0rpx'
			},
			nextMargin: {
				type: String,
				default: '0rpx'
			},
		},

		data() {
			return {

			};
		},

		methods: {
			clickItem(index) {
				console.log(this.items[index].link)
				if (this.items[index].link) {
					Util.openLink(this.items[index].link);
				} else {
					let urls = [];
					this.items.forEach(image => {
						urls.push(image.image);
					});

					uni.previewImage({
						current: index,
						urls: urls,
					})
				}
			}
		}
	}
</script>

<style>
	/* =========== 轮播图 =========== */

	/* --- 自定义轮播图指示点 可以修改形状，尺寸 --- */
	.zhuige-swiper-group .wx-swiper-dot,
	.uni-swiper-dot {
		height: 4px;
		width: 4px;
		border-radius: 4px;
	}

	.zhuige-swiper-group .wx-swiper-dot.wx-swiper-dot-active,
	.uni-swiper-dot-active {
		width: 12px;
		border-radius: 4px;
	}

	/* 如需要强化当前活动轮播状态，调整该参数即可 */

	/* 播图指示点定位左下角 */
	.zhuige-dots-left .wx-swiper-dots.wx-swiper-dots-horizontal {
		width: 90%;
		text-align: left;
		padding-left: 0rpx;
		bottom: 30rpx;
	}

	/* 基础轮播图 */
	.zhuige-swiper {
		height: 360rpx;
		width: 100%;
	}

	.zhuige-swiper .zhuige-swiper-group,
	.zhuige-swiper .zhuige-swiper-item,
	.zhuige-swiper-block,
	.zhuige-swiper-cover {
		height: 100%;
		width: 100%;
	}

	.zhuige-swiper .zhuige-swiper-block {
		position: relative;
	}

	.zhuige-swiper .zhuige-swiper-block .zhuige-swiper-title {
		position: absolute;
		height: 1em;
		line-height: 1em;
		left: 30rpx;
		bottom: 90rpx;
		font-size: 36rpx;
		font-weight: 600;
		color: #FFFFFF;
	}

	.zhuige-swiper-cover {
		border-radius: 12rpx;
	}

	/* 带间距轮播图 */
	.zhuige-space-swiper {
		height: 320rpx;
		width: 100%;
	}

	.zhuige-space-swiper .zhuige-swiper-item .zhuige-swiper-cover {
		width: 99%;
		margin: 0 0.5%;
		transition: all 0.1s linear;
	}

	.zhuige-space-swiper .zhuige-dots-left .wx-swiper-dots.wx-swiper-dots-horizontal {
		padding-left: 50rpx;
	}

	/* 高轮播图 无标题 直角图 */
	.zhuige-height-swiper {
		height: 720rpx;
		width: 100%;
	}

	.zhuige-height-swiper .zhuige-swiper-cover {
		border-radius: 0rpx;
	}

	.zhuige-height-swiper .zhuige-swiper-block .zhuige-swiper-title {
		display: none;
	}

	/* 高轮播图 遮罩底部大圆角 */
	.zhuige-cover-swiper {
		height: 720rpx;
		width: 100%;
	}

	.zhuige-cover-swiper .zhuige-swiper-group {
		border-radius: 0 0 120rpx 120rpx;
		overflow: hidden;
	}

	.zhuige-cover-swiper .zhuige-swiper-cover {
		border-radius: 0rpx;
	}

	.zhuige-cover-swiper .zhuige-dots-left .wx-swiper-dots.wx-swiper-dots-horizontal {
		bottom: 130rpx;
	}

	.zhuige-cover-swiper .zhuige-swiper-block .zhuige-swiper-title {
		bottom: 160rpx;
		font-size: 32rpx;
	}


	/* 矮轮播图 无标题 */
	.zhuige-mini-swiper {
		height: 200rpx;
		width: 100%;
	}

	.zhuige-mini-swiper .zhuige-swiper-block .zhuige-swiper-title {
		display: none;
	}
</style>