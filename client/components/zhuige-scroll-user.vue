<template>
	<view class="zhuige-scroll-user">
		<scroll-view scroll-x="true">
			<!-- 群主增加 coterie-master -->
			<view v-for="(user, index) in users" :key="index"
				@click="openLink('/pages/user/home/home?user_id=' + user.user_id)" class="zhuige-scroll-user-block"
				:class="user.owner==1 ? 'coterie-master' : ''">
				<view class="zhuige-user-avatar">
					<image mode="aspectFill" :src="user.avatar"></image>
					<image v-if="user.certify && user.certify.status==1" mode="aspectFill" :src="user.certify.icon">
					</image>
					<!-- <image v-if="user.vip && user.vip.status==1" mode="aspectFill" :src="user.vip.icon"></image> -->
				</view>
				<view class="zhuige-user-name">{{user.nickname}}</view>
			</view>
		</scroll-view>
	</view>
</template>

<script>
	import Util from '@/utils/util';

	export default {
		name: "zhuige-scroll-user",

		props: {
			prop: {
				type: String,
				default: "prop"
			},
			users: {
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
		}
	}
</script>

<style>

</style>