<template>
	<view>
		<!-- 用户列表推荐 滚动 带顶部标题，推荐用户 -->
		<view v-if="type=='scroll'" class="zhuige-user-list">
			<view class="zhuige-block-head">
				<view>{{title}}</view>
				<view>滑动查看</view>
			</view>
			<view class="zhuige-recom-user">
				<scroll-view scroll-x scroll-with-animation scroll-left="scrollLeft" show-scrollbar="false">
					<!-- 用户基础信息块 -->
					<view v-for="(user, index) in users" :key="index"
						@click="openLink('/pages/user/home/home?user_id=' + user.user_id)" class="zhuige-user-block">
						<view class="zhuige-user-avatar">
							<image mode="aspectFill" :src="user.avatar"></image>
							<image v-if="user.certify && user.certify.status==1" mode="aspectFill"
								:src="user.certify.icon"></image>
							<!-- <image v-if="user.vip && user.vip.status==1" mode="aspectFill" :src="user.vip.icon"></image> -->
						</view>
						<view class="zhuige-user-name">{{user.nickname}}</view>
						<view class="zhuige-user-info">
							<text>作品 {{user.post_count}}</text>/
							<text>粉丝 {{user.fans_count}}</text>
						</view>
						<view v-if="buttons" class="zhuige-user-opt">
							<view v-if="user.is_follow==1" class="active">已关注</view>
							<view v-else>+ 关注</view>
						</view>
					</view>
				</scroll-view>
			</view>
		</view>

		<!-- 用户列表好友 自动换行 关注列表-->
		<view v-else-if="type=='wrap'" class="zhuige-user-list">
			<view class="zhuige-bound-user">
				<view v-for="(user, index) in users" :key="index" class="zhuige-user-block">
					<!-- 用户基础信息块 -->
					<view class="zhuige-user-avatar">
						<image mode="aspectFill" :src="user.avatar"></image>
						<image v-if="user.certify && user.certify.status==1" mode="aspectFill" :src="user.certify.icon">
						</image>
						<image v-if="user.vip && user.vip.status==1" mode="aspectFill" :src="user.vip.icon"></image>
					</view>
					<view class="zhuige-user-name">{{user.nickname}}</view>
					<view class="zhuige-user-info">
						<text>作品 {{user.post_count}}</text>/
						<text>粉丝 {{user.fans_count}}</text>
					</view>
					<view v-if="buttons" class="zhuige-user-opt">
						<view v-if="user.is_follow==1" class="active">已关注</view>
						<view v-else>+ 关注</view>
					</view>
				</view>
			</view>
		</view>

	</view>
</template>

<script>
	import Util from '@/utils/util';

	export default {
		name: "zhuige-user-list",

		props: {
			type: {
				type: String,
				default: "wrap"
			},
			extClass: {
				type: String,
				default: ""
			},
			title: {
				type: String,
				default: "热门用户"
			},
			users: {
				type: Array,
				default: [],
			},
			buttons: {
				type: Boolean,
				default: true,
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

</style>