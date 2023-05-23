<template>
	<view class="content">
		<view class="zhuige-creat-forum">
			<view class="zhuige-creat-icon" @click="clickLogo()">
				<view v-if="logo">
					<image :src="logo.url"></image>
				</view>
				<view v-else>
					<uni-icons type="camera-filled" color="#FFF" size="30"></uni-icons>
				</view>
			</view>
			<view class="zhuige-creat-highline">
				<view>圈子名称</view>
				<view>
					<input type="text" v-model="name" placeholder="一个好的名称有利于他人记住" />
				</view>
			</view>
			<view class="zhuige-creat-highline">
				<view>圈子公告</view>
				<view>
					<textarea maxlength="72" v-model="brief" placeholder="简单介绍一下圈子吧…"></textarea>
				</view>
			</view>
			<picker v-if="catnames && catnames.length>0" @change="changeCategory" :value="index" :range="catnames">
				<view class="zhuige-creat-highline">
					<view>圈子分类</view>
					<view class="zhuige-creat-line">
						<view class="uni-input">
							<text class="tips-holder">分类：</text>
							{{catnames[index]}}
						</view>
						<view>
							<uni-icons type="arrowright" size="16"></uni-icons>
						</view>
					</view>
				</view>
			</picker>
			<view class="zhuige-creat-highline" @click="clickAddress()">
				<view>位置信息</view>
				<view class="zhuige-creat-line">
					<view>
						<text class="tips-holder">位置：</text>
						<text>{{marker}}</text>
					</view>
					<view>
						<uni-icons type="arrowright" size="16"></uni-icons>
					</view>
				</view>
			</view>
		</view>
		<view class="zhuige-creat-opt">
			<view class="zhuige-creat-tips" @click="clickArgee()">
				<label>
					<uni-icons :type="agree?'checkbox-filled':'circle'" color="#0C65C9" size="15"></uni-icons>
					我已阅读，同意<text @click.stop="clickLicence()">《圈子创建协议》</text>
				</label>
			</view>
		</view>
		<view class="zhuige-base-button" @click="clickCreate()">
			<view>创建</view>
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
	 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
	 */

	import Util from '@/utils/util';
	import Alert from '@/utils/alert';
	import Api from '@/utils/api';
	import Rest from '@/utils/rest';

	export default {
		data() {
			return {
				logo: '',
				name: '',
				brief: '',
				cats: [],
				catnames: [],
				index: 0,
				longitude: '',
				latitude: '',
				marker: '',
				address: '',

				agree: false,
				licence: '',
			};
		},

		onLoad(options) {
			Rest.post(Api.URL('bbs', 'setting_forum_create_pre'), {}, true).then(res => {
				if (res.code != 0) {
					if (res.code == 'require_mobile') {
						uni.redirectTo({
							url: '/pages/user/login/login?type=mobile&tip=建圈'
						})
					} else {
						Alert.error(res.message);
						setTimeout(() => {
							Util.navigateBack();
						}, 1500)
					}
					return;
				}

				this.licence = res.data.licence;
				this.cats = res.data.cats;
				let names = [];
				this.cats.forEach(item => {
					names.push(item.name);
				})
				this.catnames = names;
			}, err => {
				console.log(err)
			});
		},

		methods: {
			/**
			 * 点击上传LOGO
			 */
			clickLogo() {
				uni.chooseImage({
					count: 1,
					sizeType: ['compressed'],
					success: (res) => {
						let path = res.tempFilePaths[0];
						Rest.upload(Api.URL('other', 'upload2'), path).then(oo => {
							this.logo = oo.data;
						}, err => {
							Alert.error(err);
						});
					}
				});
			},

			/**
			 * 点击 选择地址
			 */
			clickAddress() {
				let param = {
					success: (res) => {
						this.marker = res.name;
						this.address = res.address;
						this.longitude = res.longitude;
						this.latitude = res.latitude;
					}
				}

				// #ifdef MP-WEIXIN
				if (this.latitude) {
					param.latitude = this.latitude;
				}

				if (this.longitude) {
					param.longitude = this.longitude;
				}
				// #endif

				uni.chooseLocation(param);
			},

			/**
			 * 选择分类
			 */
			changeCategory(e) {
				this.index = e.detail.value;
			},

			/**
			 * 提交创建圈子请求
			 */
			clickCreate() {
				if (!this.agree) {
					Alert.toast('需要同意圈子协议');
					return;
				}

				Rest.post(Api.URL('bbs', 'forum_create'), {
					logo: JSON.stringify(this.logo),
					name: this.name,
					brief: this.brief,
					cat_id: this.cats[this.index].id,
					latitude: this.latitude,
					longitude: this.longitude,
					marker: this.marker,
					address: this.address,
				}).then(res => {
					if (res.code != 0) {
						if (res.code == 'require_mobile') {
							Util.openLink('/pages/user/login/login?type=mobile&tip=建圈');
						} else {
							Alert.error(res.message);
						}
						return;
					}

					Alert.toast('提交成功，请耐心等待审核')

					setTimeout(() => {
						uni.redirectTo({
							url: '/pages/tabs/forum/forum'
						})
					}, 1500);
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 点击同意
			 */
			clickArgee() {
				this.agree = !this.agree;
			},

			/**
			 * 查看协议
			 */
			clickLicence() {
				Util.openLink(this.licence);
			},
		}
	}
</script>

<style>
	page {
		background: #f5f5f5;
	}

	.zhuige-creat-forum {
		margin: 0 30rpx;
	}

	.zhuige-creat-icon {
		text-align: center;
		padding-bottom: 60rpx;
	}

	.zhuige-creat-icon view {
		position: relative;
		background: #010101;
		width: 200rpx;
		text-align: center;
		height: 200rpx;
		margin: 0 auto;
		line-height: 200rpx;
		border-radius: 50%;
	}

	.zhuige-creat-icon image {
		width: 200rpx;
		height: 200rpx;
		border-radius: 50%;
	}

	.zhuige-creat-forum {
		padding: 30rpx;
		background-color: #ffffff;
		border-radius: 12rpx;
	}

	.zhuige-creat-highline {
		padding: 30rpx 0;
		border-top: 1rpx solid #dddddd;
		font-size: 28rpx;
	}

	.zhuige-creat-highline>view:nth-child(1) {
		font-size: 30rpx;
		font-weight: 600;
	}

	.zhuige-creat-highline {
		position: relative;
	}

	.zhuige-creat-highline view textarea {
		height: 90rpx;
		line-height: 45rpx;
		padding: 10rpx 0;
	}

	.zhuige-creat-line {
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 10rpx 0 0;
	}

	.zhuige-creat-opt {
		padding: 30rpx 30rpx 60rpx;
	}

	.zhuige-creat-tips {
		text-align: center;
		padding: 25rpx;
		font-size: 26rpx;
	}

	.zhuige-creat-tips text {
		padding: 10rpx;
		color: #1d9ffc;
	}

	.tips-holder {
		color: #999999;
	}
</style>