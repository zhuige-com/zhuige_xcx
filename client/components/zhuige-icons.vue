<template>
	<view>
		<!-- 
		自定义图标模块 zhuige-icon 基础结构一致
		滚动模式 class 增加 zhuige-scroll-icon，增加scroll-view
		自动换行模式 class 增加 zhuige-wrap-icon
		-->

		<!-- 自定义图标模块 自动换行模式 -->
		<view v-if="type=='wrap'" class="zhuige-icon zhuige-wrap-icon">
			<view v-for="(item, index) in items" :key="index">
				<view v-if="item.type && item.type=='clear'" class="view" @click="clickClear()">
					<image class="image" mode="aspectFill" :src="item.image"></image>
					<text class="text">{{item.title}}</text>
				</view>
				<view v-else-if="item.type && item.type=='score'" class="view" @click="clickScore()">
					<image class="image" mode="aspectFill" :src="item.image"></image>
					<text class="text">{{item.title}}</text>
				</view>
				<template v-else-if="item.type && item.type=='contact'">
					<!-- #ifdef MP-WEIXIN -->
					<button open-type="contact" class="button-view">
						<image class="image" mode="aspectFill" :src="item.image"></image>
						<text class="text">{{item.title}}</text>
					</button>
					<!-- #endif -->
				</template>
				<view v-else @click="openLink(item.link)">
					<uni-badge v-if="item.badage" text="12" size="normal" type="error" absolute="rightTop" />
					<image class="image" mode="aspectFill" :src="item.image"></image>
					<text class="text">{{item.title}}</text>
				</view>
			</view>
		</view>

		<!-- 自定义图标模块 滚动模式 -->
		<view v-else class="zhuige-icon zhuige-scroll-icon">
			<scroll-view class="scroll-view" scroll-x="true">
				<view v-for="(item, index) in items" :key="index" class="view">
					<view v-if="item.type && item.type=='clear'" @click="clickClear()">
						<image class="image" mode="aspectFill" :src="item.image"></image>
						<text class="text">{{item.title}}</text>
					</view>
					<view v-else-if="item.type && item.type=='score'" @click="clickScore()">
						<image class="image" mode="aspectFill" :src="item.image"></image>
						<text class="text">{{item.title}}</text>
					</view>
					<template v-else-if="item.type && item.type=='contact'">
						<!-- #ifdef MP-WEIXIN -->
						<button open-type="contact">
							<image class="image" mode="aspectFill" :src="item.image"></image>
							<text class="text">{{item.title}}</text>
						</button>
						<!-- #endif -->
					</template>
					<view v-else @click="openLink(item.link)">
						<uni-badge v-if="item.badage" text="12" size="normal" type="error" absolute="rightTop" />
						<image class="image" mode="aspectFill" :src="item.image"></image>
						<text class="text">{{item.title}}</text>
					</view>
				</view>
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

			// 打分评价
			clickScore() {
				var plugin = requirePlugin("wxacommentplugin");
				plugin.openComment({
					success: (res) => {
						// console.log('plugin.openComment success', res)
						let lastTime = wx.getStorageSync('zhuige_comment_plugin_last_time');
						if (!lastTime) {
							lastTime = 0;
						}
						
						let now = new Date().getTime();
						let legal = ((now - lastTime) > 30 * 86400000);
						if (legal) {
							wx.setStorageSync('zhuige_comment_plugin_last_time', now)
						}
						
						uni.showToast({
							icon: 'none',
							title: (legal ? '感谢评价' : '您近期已评价过')
						});
					},
					fail: (res) => {
						// console.log('plugin.openComment fail', res)
						if (res.errCode != -3) {
							uni.showToast({
								icon: 'none',
								title: '评价功能暂不可用'
							});
						}
					}
				})
			}
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