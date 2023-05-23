<template>
	<view>
		<!-- 
		自定义图标模块 zhuige-icon 基础结构一致
		滚动模式 class 增加 zhuige-scroll-icon，增加scroll-view
		自动换行模式 class 增加 zhuige-wrap-icon
		-->

		<!-- 自定义图标模块 自动换行模式 -->
		<view v-if="type=='wrap'" class="zhuige-icon zhuige-wrap-icon">
			<template v-for="(item, index) in items">
				<view v-if="item.type && item.type=='clear'" :key="index" class="view" @click="clickClear()">
					<image class="image" mode="aspectFill" :src="item.image"></image>
					<text class="text">{{item.title}}</text>
				</view>
				<template v-else-if="item.type && item.type=='contact'">
					<!-- #ifdef MP-WEIXIN -->
					<button :key="index" open-type="contact" class="button-view">
						<image class="image" mode="aspectFill" :src="item.image"></image>
						<text class="text">{{item.title}}</text>
					</button>
					<!-- #endif -->
				</template>
				<view v-else :key="index" class="view" @click="openLink(item.link)">
					<uni-badge v-if="item.badage" text="12" size="normal" type="error" absolute="rightTop" />
					<image class="image" mode="aspectFill" :src="item.image"></image>
					<text class="text">{{item.title}}</text>
				</view>
			</template>
		</view>

		<!-- 自定义图标模块 滚动模式 -->
		<view v-else class="zhuige-icon zhuige-scroll-icon">
			<scroll-view class="scroll-view" scroll-x="true">
				<template v-for="(item, index) in items">
					<view v-if="item.type && item.type=='clear'" :key="index" class="view" @click="clickClear()">
						<image class="image" mode="aspectFill" :src="item.image"></image>
						<text class="text">{{item.title}}</text>
					</view>
					<template v-else-if="item.type && item.type=='contact'">
						<!-- #ifdef MP-WEIXIN -->
						<button :key="index" open-type="contact">
							<image class="image" mode="aspectFill" :src="item.image"></image>
							<text class="text">{{item.title}}</text>
						</button>
						<!-- #endif -->
					</template>
					<view v-else :key="index" class="view" @click="openLink(item.link)">
						<uni-badge v-if="item.badage" text="12" size="normal" type="error" absolute="rightTop" />
						<image class="image" mode="aspectFill" :src="item.image"></image>
						<text class="text">{{item.title}}</text>
					</view>
				</template>
			</scroll-view>
		</view>
	</view>
</template>

<script>
	import Util from '@/utils/util';

	export default {
		name: "zhuige-icons",

		props: {
			type: {
				type: String,
				default: "wrap"
			},
			items: {
				type: Array,
				default: []
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

			//清理缓存
			clickClear() {
				uni.showModal({
					title: '提示',
					content: '清除缓存 需要重新登录',
					success(res) {
						if (res.confirm) {
							uni.clearStorageSync();
							uni.showToast({
								title: '清除完毕'
							});

							uni.reLaunch({
								url: '/pages/tabs/index/index'
							});
						}
					}

				});
			},
		}
	}
</script>

<style>
	/* =========== 自定义图标模块 =========== */
	.zhuige-icon .view {
		width: 20%;
		text-align: center;
		padding-bottom: 10rpx;
	}

	.zhuige-icon .button-view {
		background: none;
		border: none;
		margin: 0;
		padding: 30rpx 0;
		line-height: normal;
	}

	.zhuige-icon .button-view .image {
		padding-bottom: 10rpx;
	}

	.zhuige-icon .button-view::after {
		border: none;
	}

	.zhuige-icon .image {
		height: 90rpx;
		width: 90rpx;
	}

	.zhuige-icon .text {
		display: block;
		line-height: 0.8em;
		font-size: 26rpx;
		color: #666666;
	}

	/* --- 滚动模式 --- */
	.zhuige-scroll-icon {
		white-space: nowrap;
	}

	.zhuige-scroll-icon .scroll-view .view {
		display: inline-block;
	}

	/* --- 自动换行模式 --- */
	.zhuige-wrap-icon {
		display: flex;
		flex-wrap: wrap;
	}
</style>