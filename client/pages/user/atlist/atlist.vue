<template>
	<view class="content">

		<view class="zhuige-friends-msg">
			<view class="zhuige-block">
				<checkbox-group @change="checkboxChange">

					<!-- 单行end -->
					<label v-for="item in items" :key="item.user_id">
						<view class="zhuige-friends-check">
							<view class="zhuige-friends-check-act">
								<checkbox :value="item.user_id" :checked="item.checked" />
							</view>
							<view class="zhuige-friends-check-info">
								<view>
									<image mode="aspectFill" :src="item.avatar"></image>
								</view>
								<text>{{item.nickname}}</text>
							</view>
						</view>
					</label>

				</checkbox-group>
			</view>
		</view>

		<!-- 浮动确认按钮 -->
		<view class="zhuige-base-button" @click="clickOK">
			<view>确定</view>
		</view>
	</view>
</template>

<script>
	/*
	 * 追格小程序
	 * 作者: 追格
	 * 文档: https://www.zhuige.com/docs/zg.html
	 * gitee: https://gitee.com/zhuige_com/zhuige_xcx
	 * github: https://github.com/zhuige-com/zhuige_xcx
	 * Copyright © 2022-2024 www.zhuige.com All rights reserved.
	 */

	import Util from '@/utils/util';
	import Alert from '@/utils/alert';
	import Api from '@/utils/api';
	import Rest from '@/utils/rest';

	import ZhuigeNodata from "@/components/zhuige-nodata";

	export default {
		components: {
			ZhuigeNodata
		},
		
		data() {
			this.atlist = [];
			
			return {
				loadMore: 'more',
				items: [],
				loaded: false,
			}
		},

		onLoad(options) {
			Util.addShareScore(options.source);
			
			if (options.atlist) {
				this.atlist = options.atlist.split(',');
			}
		},

		onShow() {
			this.loadData();
		},

		onReachBottom() {
			if (this.loadMore == 'more') {
				this.loadData();
			}
		},

		methods: {
			/**
			 * 选择用户
			 */
			checkboxChange(e) {
				this.atlist = e.detail.value;
			},
			
			/**
			 * 确定
			 */
			clickOK(e) {
				uni.$emit('atlistChange', this.atlist);
				Util.navigateBack();
			},

			/**
			 * 加载数据
			 */
			loadData() {
				Rest.post(Api.URL('user', 'my_friends'), {
					offset: this.items.length
				}).then(res => {
					let users = res.data.users;
					for (let i=0; i<users.length; i++) {
						for (let j=0; j<this.atlist.length; j++) {
							if (users[i].user_id == this.atlist[j]) {
								users[i].checked = 1;
								break;
							}
						}
					}
					
					this.items = this.items.concat(users);
					this.loadMore = res.data.more;
					this.loaded = true;

					console.log(res);
				}, err => {
					console.log(err)
				});
			}
			
		}
	}
</script>

<style>
	.zhuige-friends-msg {
		padding: 0 20px;
	}

	.zhuige-friends-check {
		padding: 30rpx 0;
		display: flex;
		align-items: center;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
		border-bottom: 1rpx solid #EEEEEE;
	}

	label:last-of-type .zhuige-friends-check {
		border: none;
	}

	.zhuige-friends-check-act {
		margin-right: 12rpx;
	}

	.zhuige-friends-check-info {
		display: flex;
		align-items: center;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.zhuige-friends-check-info view {
		flex: 0 0 96rpx;
		height: 96rpx;
		width: 96rpx;
	}

	.zhuige-friends-check-info image {
		height: 96rpx;
		width: 96rpx;
		border-radius: 50%;
	}

	.zhuige-friends-check-info text {
		font-size: 28rpx;
		font-weight: 600;
		color: #010101;
		margin-left: 20rpx;
	}

</style>