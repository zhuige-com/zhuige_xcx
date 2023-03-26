<template>
	<view class="content">
		<template v-if="is_login">
			<!-- 清理提示 -->
			<view class="zhuige-none-tips" @click="clickClear"
				v-if="system_count>0 || like_count>0 || favorite_count>0 || comment_count>0 || follow_count>0 || ait_count>0">
				<text>清理未读消息</text>
			</view>

			<!-- 消息列表 -->
			<view class="zhuige-message">
				<view class="zhuige-block">
					<!-- 消息列表行 -->
					<view @click="clickType('like')" class="zhuige-message-line">
						<view class="zhuige-message-info">
							<view class="zhuige-message-icon">
								<uni-icons type="hand-up-filled" color="#010101" size="24"></uni-icons>
							</view>
							<view class="zhuige-message-text">
								<view>
									<text>点赞</text>
									<text v-if="like_count>0">{{like_count}}</text>
								</view>
								<view v-if="like_count>0">你有{{like_count}}条消息未读</view>
								<view v-else>没有消息就是好消息</view>
							</view>
						</view>
						<view class="zhuige-message-act">
							<uni-icons type="right" color="#BBBBBB" size="16"></uni-icons>
						</view>
					</view>
					<view @click="clickType('favorite')" class="zhuige-message-line">
						<view class="zhuige-message-info">
							<view class="zhuige-message-icon">
								<uni-icons type="star-filled" color="#010101" size="24"></uni-icons>
							</view>
							<view class="zhuige-message-text">
								<view>
									<text>收藏</text>
									<text v-if="favorite_count>0">{{favorite_count}}</text>
								</view>
								<view v-if="favorite_count>0">你有{{favorite_count}}条消息未读</view>
								<view v-else>没有消息就是好消息</view>
							</view>
						</view>
						<view class="zhuige-message-act">
							<uni-icons type="right" color="#BBBBBB" size="16"></uni-icons>
						</view>
					</view>
					<view @click="clickType('comment')" class="zhuige-message-line">
						<view class="zhuige-message-info">
							<view class="zhuige-message-icon">
								<uni-icons type="chat-filled" color="#010101" size="24"></uni-icons>
							</view>
							<view class="zhuige-message-text">
								<view>
									<text>评论</text>
									<text v-if="comment_count>0">{{comment_count}}</text>
								</view>
								<view v-if="comment_count>0">你有{{comment_count}}条消息未读</view>
								<view v-else>没有消息就是好消息</view>
							</view>
						</view>
						<view class="zhuige-message-act">
							<uni-icons type="right" color="#BBBBBB" size="16"></uni-icons>
						</view>
					</view>
					<view @click="clickType('follow')" class="zhuige-message-line">
						<view class="zhuige-message-info">
							<view class="zhuige-message-icon">
								<uni-icons type="staff-filled" color="#010101" size="24"></uni-icons>
							</view>
							<view class="zhuige-message-text">
								<view>
									<text>粉丝</text>
									<text v-if="follow_count>0">{{follow_count}}</text>
								</view>
								<view v-if="follow_count>0">你有{{follow_count}}条消息未读</view>
								<view v-else>没有消息就是好消息</view>
							</view>
						</view>
						<view class="zhuige-message-act">
							<uni-icons type="right" color="#BBBBBB" size="16"></uni-icons>
						</view>
					</view>
					<view v-if="ait_msg" @click="clickType('ait')" class="zhuige-message-line">
						<view class="zhuige-message-info">
							<view class="zhuige-message-icon">
								<text>@</text>
							</view>
							<view class="zhuige-message-text">
								<view>
									<text>@我的</text>
									<text v-if="ait_count>0">{{ait_count}}</text>
								</view>
								<view v-if="ait_count>0">你有{{ait_count}}条消息未读</view>
								<view v-else>没有消息就是好消息</view>
							</view>
						</view>
						<view class="zhuige-message-act">
							<uni-icons type="right" color="#BBBBBB" size="16"></uni-icons>
						</view>
					</view>

					<!-- 新增私信入口 -->
					<view v-if="message" @click="clickType('message')" class="zhuige-message-line">
						<view class="zhuige-message-info">
							<view class="zhuige-message-icon">
								<uni-icons type="chatboxes-filled" color="#010101" size="24"></uni-icons>
							</view>
							<view class="zhuige-message-text">
								<view>
									<text>私信</text>
									<text v-if="message_count>0">{{message_count}}</text>
								</view>
								<view v-if="message_count>0">你有{{message_count}}条私信未读</view>
								<view v-else>还没有人给你发私信呢</view>
							</view>
						</view>
						<view class="zhuige-message-act">
							<uni-icons type="right" color="#BBBBBB" size="16"></uni-icons>
						</view>
					</view>
					
					<view v-if="sys_msg" @click="clickType('system')" class="zhuige-message-line">
						<view class="zhuige-message-info">
							<view class="zhuige-message-icon">
								<uni-icons type="notification-filled" color="#010101" size="24"></uni-icons>
							</view>
							<view class="zhuige-message-text">
								<view>
									<text>系统消息</text>
									<text v-if="system_count>0">{{system_count}}</text>
								</view>
								<view v-if="system_count>0">你有{{system_count}}条消息未读</view>
								<view v-else>没有消息就是好消息</view>
							</view>
						</view>
						<view class="zhuige-message-act">
							<uni-icons type="right" color="#BBBBBB" size="16"></uni-icons>
						</view>
					</view>
				</view>
			</view>
		</template>
		<template v-else>
			<view class="zhugie-notice">
				<zhuige-nodata :buttons="true"></zhuige-nodata>
			</view>
		</template>
	</view>
</template>

<script>
	/*
	 * 追格小程序
	 * 作者: 追格
	 * 文档: https://www.zhuige.com/docs/zg.html
	 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
	 * github: https://github.com/zhuige-com/zhuige_xcx
	 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
	 */

	import Util from '@/utils/util';
	import Auth from '@/utils/auth';
	import Alert from '@/utils/alert';
	import Api from '@/utils/api';
	import Rest from '@/utils/rest';

	import ZhuigeNodata from "@/components/zhuige-nodata";

	export default {
		data() {
			return {
				is_login: false,
				ait_msg: false,

				sys_msg: 0,
				system_count: 0,
				
				like_count: 0,
				favorite_count: 0,
				comment_count: 0,
				follow_count: 0,
				ait_count: 0,
				
				message: 0,
				message_count: 0,
			}
		},

		components: {
			ZhuigeNodata
		},

		onLoad(options) {
			Util.addShareScore(options.source);
		},

		onShow() {
			this.is_login = !!Auth.getUser();
			if (this.is_login) {
				this.loadData();
			}
		},

		onShareAppMessage() {
			return {
				title: '站内提醒-' + getApp().globalData.appName,
				path: Util.addShareSource('pages/tabs/notice/notice?n=n')
			};
		},

		// #ifdef MP-WEIXIN
		onShareTimeline() {
			return {
				title: '站内提醒-' + getApp().globalData.appName,
			};
		},
		// #endif

		methods: {
			/**
			 * 点击打开链接
			 */
			openLink(link) {
				Util.openLink(link);
			},

			/**
			 * 点击通知
			 */
			clickType(type) {
				if (type == 'system') {
					Util.openLink('/pages/sys_notice/list/list');
				} else if (type == 'message') {
					Util.openLink('/pages/message/list/list');
				} else {
					Util.openLink('/pages/base/notice_list/notice_list?type=' + type);
				}
			},

			/**
			 * 点击清理通知
			 */
			clickClear() {
				Rest.post(Api.URL('user', 'notify_clear'), {}).then(res => {
					this.loadData();
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 加载数据
			 */
			loadData() {
				Rest.post(Api.URL('user', 'notify_read')).then(res => {
					if (res.code == 0) {
						this.like_count = res.data.like_count;
						this.favorite_count = res.data.favorite_count;
						this.comment_count = res.data.comment_count;
						this.follow_count = res.data.follow_count;
						this.ait_count = res.data.ait_count;
						
						this.sys_msg = res.data.sys_msg;
						this.system_count = res.data.system_count;

						if (res.data.ait_msg) {
							this.ait_msg = res.data.ait_msg;
						}
						
						this.message = res.data.message;
						this.message_count = res.data.message_count;

						if (this.system_count > 0 || this.like_count > 0 || this.favorite_count > 0 || this
							.comment_count > 0 || this.follow_count > 0 || this.ait_count > 0) {
							getApp().globalData.noticeRedDot = 1;
						} else {
							getApp().globalData.noticeRedDot = 0;
						}
						Util.setNoticeRedDot();
					}
				}, err => {
					console.log(err)
				});
			}
		}
	}
</script>

<style>
	page {
		background: #f5f5f5;
	}

	.zhuige-message {
		padding: 0 20rpx;
	}

	.zhuige-message-line {
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 30rpx 0px;
		border-bottom: 1rpx solid #EEEEEE;
	}

	.zhuige-message-line:last-of-type {
		border: none;
	}

	.zhuige-message-info {
		display: flex;
		align-items: center;
	}

	.zhuige-message-icon {
		height: 104rpx;
		line-height: 104rpx;
		text-align: center;
		width: 104rpx;
		border-radius: 50%;
		background: #f5f5f5;
	}

	.zhuige-message-text {
		padding-left: 20rpx;
	}

	.zhuige-message-text view:nth-child(1) {
		display: flex;
		align-items: center;
		margin-bottom: -12rpx;
	}

	.zhuige-message-text view:nth-child(1) text:nth-child(1) {
		font-size: 32rpx;
		font-weight: 600;
	}

	.zhuige-message-text view:nth-child(1) text:nth-child(2) {
		font-size: 20rpx;
		font-weight: 300;
		color: #FFFFFF;
		height: 36rpx;
		line-height: 36rpx;
		border-radius: 18rpx;
		padding: 0 12rpx;
		background: #FD6531;
		margin-left: 12rpx;
	}

	.zhuige-message-text view:nth-child(2) {
		font-size: 28rpx;
		font-weight: 400;
		color: #666666;
	}

	.zhugie-notice {
		position: fixed;
		height: 100%;
		width: 100%;
		background: #FFFFFF;
	}
</style>
