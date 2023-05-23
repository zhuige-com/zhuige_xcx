<template>
	<view class="zhuige-block">
		<!-- 无内容提示 -->
		<view class="zhuige-none-tips">
			<image class="image" mode="aspectFill" src="/static/404.png"></image>
			<template v-if="buttons && !isLogin">
				<view class="view">什么也没有，登录后再看看</view>
				<view class="zhuige-form-btn">
					<view @click="openLink('/pages/user/login/login')" class="view">登录</view>
				</view>
			</template>
			<view v-else class="view">{{tip}}</view>
		</view>
	</view>
</template>

<script>
	import Util from '@/utils/util';
	import Auth from '@/utils/auth';

	export default {
		name: "zhuige-nodata",

		props: {
			tip: {
				type: String,
				default: '哇哦，什么也没有~'
			},
			buttons: {
				type: Boolean,
				default: false
			}
		},

		data() {
			return {
				isLogin: false,
			};
		},

		created() {
			this.isLogin = !!Auth.getUser();
		},

		methods: {
			openLink(link) {
				Util.openLink(link);
			},
		}
	}
</script>

<style>
	.zhuige-none-tips {
		padding: 40rpx;
		text-align: center;
	}

	.zhuige-none-tips .image {
		height: 200rpx;
		width: 200rpx;
		margin-bottom: 40rpx;
	}

	.zhuige-none-tips>.view {
		font-size: 26rpx;
		font-weight: 300;
		color: #999999;
		padding: 20rpx;
	}

	.zhuige-form-btn {
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.zhuige-form-btn .view {
		font-size: 26rpx;
		font-weight: 300;
		color: #FFFFFF;
		background: #010f01;
		padding: 0 60rpx;
		height: 80rpx;
		line-height: 80rpx;
		border-radius: 40rpx;
	}
</style>