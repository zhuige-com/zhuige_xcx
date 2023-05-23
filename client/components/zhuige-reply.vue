<template>
	<!-- 评价列表块 -->
	<view v-if="item" class="zhuige-reply-block">
		<view class="zhuige-reply-header">
			<view class="zhugie-reply-user" @click="openLink('/pages/user/home/home?user_id=' + item.user.user_id)">
				<view>
					<image mode="aspectFill" :src="item.user.avatar"></image>
				</view>
				<view>
					<view>
						<text>{{item.user.nickname}}</text>
						<image class="zhuige-certify" v-if="item.user.certify && item.user.certify.status==1"
							mode="aspectFill" :src="item.user.certify.icon"></image>
						<image v-if="item.user.vip && item.user.vip.status==1" class="zhuige-replay-vip"
							mode="aspectFill" :src="item.user.vip.icon"></image>
					</view>
					<view>{{item.time}}</view>
				</view>
			</view>
			<view @click="clickReply(item.id, 0)">
				<uni-icons type="chatbubble" color="#999999" size="20"></uni-icons>
			</view>
		</view>
		<view class="zhuige-reply-body">
			<text v-if="item.reply">@{{item.reply.nickname}}</text>
			{{item.content}}
		</view>
		<view class="zhuige-reply-sub">
			<view v-for="(reply, index) in item.replys" :key="index" class="zhuige-reply-block">
				<view class="zhuige-reply-header">
					<view class="zhugie-reply-user"
						@click="openLink('/pages/user/home/home?user_id=' + reply.user.user_id)">
						<view>
							<image mode="aspectFill" :src="reply.user.avatar"></image>
						</view>
						<view>
							<view>
								<text>{{reply.user.nickname}}</text>
								<image v-if="reply.user.certify && reply.user.certify.status==1" mode="aspectFill"
									:src="reply.user.certify.icon"></image>
								<image v-if="reply.user.vip && reply.user.vip.status==1" class="zhuige-replay-vip"
									mode="aspectFill" :src="reply.user.vip.icon"></image>
							</view>
							<view>{{reply.time}}</view>
						</view>
					</view>
					<view @click="clickReply(item.id, reply.user.user_id)">
						<uni-icons type="chatbubble" color="#999999" size="20"></uni-icons>
					</view>
				</view>
				<view class="zhuige-reply-body">
					<text v-if="reply.reply">@{{reply.reply.nickname}}</text>
					{{reply.content}}
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import Util from '@/utils/util';

	export default {
		name: "zhuige-reply",

		props: {
			prop: {
				type: String,
				default: "prop"
			},
			item: {
				type: Object,
				default: undefined,
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

			clickReply(comment_id, user_id) {
				this.$emit("clickReply", {
					comment_id: comment_id,
					user_id: user_id
				});
			}
		}
	}
</script>

<style>

</style>