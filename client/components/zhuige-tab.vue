<template>
	<view>
		<!-- 项目分类tab(居中) -->
		<view v-if="type=='simple'" class="zhuige-tab zhuige-center-tab">
			<view class="zhuige-tab-nav">
				<view class="zhuige-tab-box" v-for="(tab, index) in tabs" :key="index"
					:class="curTab==tab.id?'active':''" @click="clickTab(tab)">
					{{tab.title}}
				</view>
			</view>
		</view>

		<view v-else class="zhuige-tab">
			<view class="zhuige-tab-nav">
				<scroll-view class="zhuige-tab-scroll" scroll-x scroll-with-animation scroll-left="scrollLeft"
					show-scrollbar="false">
					<view class="zhuige-tab-box" v-for="(tab, index) in tabs" :key="index"
						:class="curTab==tab.id?'active':''" @click="clickTab(tab)">
						{{tab.title}}
					</view>
				</scroll-view>
			</view>
			<view v-if="opt" class="zhuige-tab-opt" @click="clickTabOpt">
				<uni-icons type="bars" size="22"></uni-icons>
			</view>
		</view>
	</view>
</template>

<script>
	import Util from '@/utils/util';

	export default {
		name: "zhuige-tab",

		props: {
			type: {
				type: String,
				default: "simple"
			},
			tabs: {
				type: Array,
				default: []
			},
			curTab: {
				type: String,
				default: ''
			},
			opt: {
				type: Boolean,
				default: false
			}
		},

		data() {
			return {

			};
		},

		methods: {
			clickTab(tab) {
				this.$emit("clickTab", tab);
			},

			clickTabOpt() {
				this.$emit("clickTabOpt", {});
			}
		}
	}
</script>

<style>
	/* =========== tab导航 =========== */
	.zhuige-tab {
		padding: 0 30rpx;
		position: relative;
		background: #FFFFFF;
		border-radius: 12rpx;
	}

	.zhuige-tab-nav {
		white-space: nowrap;
	}

	.zhuige-tab-scroll *::-webkit-scrollbar {
		display: none;
	}

	.zhuige-tab-box {
		height: 100rpx;
		line-height: 100rpx;
		display: inline-flex;
		justify-content: center;
		align-items: center;
		padding: 0 32rpx;
		text-align: center;
		position: relative;
	}

	.zhuige-tab-box:last-of-type {
		margin-right: 70rpx;
	}

	.zhuige-tab-box:first-of-type {
		margin-left: -14rpx;
	}

	.active {
		font-weight: 600;
		font-size: 32rpx;
		transition: all 0.1s ease-in;
	}

	.active::after {
		position: absolute;
		content: "";
		width: 32rpx;
		height: 8rpx;
		border-radius: 8rpx;
		background: #333333;
		top: 80rpx;
		left: 50%;
		margin-left: -16rpx;
	}

	.zhuige-tab-opt {
		position: absolute;
		z-index: 5;
		top: 0;
		right: 10rpx;
		height: 100rpx;
		padding-left: 60rpx;
		width: 60rpx;
		line-height: 100rpx;
		background: linear-gradient(to right, rgba(255, 255, 255, 0), rgba(255, 255, 255, .7)30%, rgba(255, 255, 255, 1)50%, rgba(255, 255, 255, 1)100%);
	}

	/* --- 居中tab --- */
	.zhuige-center-tab {
		background: none;
	}

	.zhuige-center-tab .zhuige-tab-nav {
		display: flex;
		justify-content: center;
	}

	.zhuige-center-tab .zhuige-tab-nav .zhuige-tab-box:last-of-type {
		margin: 0;
	}
</style>