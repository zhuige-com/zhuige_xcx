<template>
	<view>
		<!-- 项目分类tab(居中) -->
		<view v-if="type=='simple'" class="zhuige-tab zhuige-center-tab">
			<view class="zhuige-tab-nav">
				<view class="view" v-for="(tab, index) in tabs" :key="index" :class="curTab==tab.id?'active':''"
					@click="clickTab(tab)">
					{{tab.title}}
				</view>
			</view>
		</view>

		<view v-else class="zhuige-tab">
			<view class="zhuige-tab-nav">
				<scroll-view class="scroll-view" scroll-x scroll-with-animation scroll-left="scrollLeft"
					show-scrollbar="false">
					<view class="view" v-for="(tab, index) in tabs" :key="index" :class="curTab==tab.id?'active':''"
						@click="clickTab(tab)">
						{{tab.title}}
					</view>
				</scroll-view>
			</view>
			<view v-if="opt" class="zhuige-tab-opt">
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

	.zhuige-tab-nav .scroll-view *::-webkit-scrollbar {
		display: none;
	}

	.zhuige-tab-nav .view {
		height: 100rpx;
		line-height: 100rpx;
		display: inline-flex;
		justify-content: center;
		align-items: center;
		padding: 0 40rpx;
		text-align: center;
		position: relative;
	}

	.zhuige-tab-nav .view:last-of-type {
		margin-right: 70rpx;
	}

	.zhuige-tab-nav .view.active {
		font-weight: 600;
		font-size: 32rpx;
		transition: all 0.1s ease-in;
	}

	.zhuige-tab-nav .view.active::after {
		position: absolute;
		content: "";
		width: 30rpx;
		height: 8rpx;
		border-radius: 8rpx;
		background: #333333;
		top: 80rpx;
	}

	.zhuige-tab-opt {
		position: absolute;
		z-index: 5;
		top: 0;
		right: 0;
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

	.zhuige-center-tab .zhuige-tab-nav .view:last-of-type {
		margin: 0;
	}
</style>
