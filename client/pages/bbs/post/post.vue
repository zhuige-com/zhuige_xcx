<template>
	<view class="content">
		<!-- 输入框 -->
		<view class="zhuige-post-box">
			<view class="zhuige-block">
				<view class="zhuige-post-input">
					<textarea v-model="content" placeholder="想说你就多说点..." maxlength="140"></textarea>
				</view>
				<view class="zhuige-post-plug">
					<view @click="clickType('image')" :class="{active:type=='image'}">
						<uni-icons type="image-filled" color="#333333" size="20"></uni-icons>
						<text>图文</text>
					</view>
					<view @click="clickType('video')" :class="{active:type=='video'}">
						<uni-icons type="videocam-filled" color="#333333" size="20"></uni-icons>
						<text>视频</text>
					</view>
				</view>
			</view>
		</view>

		<!-- 图片列表 -->
		<view v-if="type=='image'" class="zhuige-post-box">
			<view class="zhuige-upload-set">
				<view v-if="images.length<9" @click="clickAddImage">
					<uni-icons type="plusempty" color="#777777" size="30"></uni-icons>
					<view>图片</view>
				</view>
				<!-- 上传后，增加 class="loaded" view替换为image，icon type替换为clear -->
				<view class="loaded" v-for="(image, index) in images" :key="index">
					<uni-icons type="clear" color="#FD6531" size="24" @click="clickDelImage(index)"></uni-icons>
					<image mode="aspectFill" :src="image.image.url"></image>
				</view>
			</view>
		</view>

		<!-- 视频 -->
		<view v-else-if="type=='video'" class="zhuige-post-video-box">
			<video v-if="video" :src="video.url"></video>
			<view v-else @click="clickAddVideo" class="zhuige-upload-set">
				<view>
					<uni-icons type="plusempty" color="#777777" size="30"></uni-icons>
					<view>视频</view>
				</view>
			</view>
			<view class="zhuige-upload-set">
				<view class="loaded" v-if="video_cover">
					<uni-icons type="clear" color="#FD6531" size="24" @click="clickDelVideoPoster"></uni-icons>
					<image mode="aspectFill" :src="video_cover.url"></image>
				</view>
				<view v-else @click="clickAddVideoPoster">
					<uni-icons type="plusempty" color="#777777" size="30"></uni-icons>
					<view>封面</view>
				</view>
			</view>
		</view>

		<!-- 选择行 -->
		<view class="zhuige-post-box">
			<view class="zhuige-block">
				<view class="zhuige-post-line">
					<view>选择圈子：</view>
					<view @click="openLink('/pages/bbs/forum-select/forum-select')">
						<view>{{forum?forum.name:'请选择圈子'}}</view>
						<uni-icons type="right" color="#BBBBBB" size="16"></uni-icons>
					</view>
				</view>
			</view>
		</view>

		<!-- 选择行 -->
		<view class="zhuige-post-box">
			<view class="zhuige-block">
				<view class="zhuige-post-line">
					<view>你的位置：</view>
					<view @click="clickAddress">
						<view>{{marker?marker:'请选择位置'}}</view>
						<uni-icons type="right" color="#BBBBBB" size="16"></uni-icons>
					</view>
				</view>
			</view>
		</view>

		<!-- 选择行 -->
		<view class="zhuige-post-box">
			<view class="zhuige-block">
				<view class="zhuige-post-line">
					<view>动态话题：</view>
					<view @click="clickSubject">
						<view>已选{{subjects.length}}个话题</view>
						<uni-icons type="right" color="#BBBBBB" size="16"></uni-icons>
					</view>
				</view>
			</view>
		</view>

		<!-- 选择行 -->
		<!-- <view class="zhuige-post-box">
			<view class="zhuige-block">
				<view class="zhuige-post-line">
					<view>@ 好友：</view>
					<view>
						<view>已选0个好友</view>
						<uni-icons type="right" color="#BBBBBB" size="16"></uni-icons>
					</view>
				</view>
			</view>
		</view> -->

		<!-- 选择行 -->
		<!-- <view class="zhuige-post-box">
			<view class="zhuige-block">
				<view class="zhuige-post-line">
					<view>积分数：</view>
					<view>
						<input type="text" placeholder="如：88，填写即开启积分阅读全文" />
					</view>
				</view>
			</view>
		</view> -->

		<!-- 底部大按钮 -->
		<view @click="clickCreate" class="zhuige-base-button">
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
	 * Copyright © 2022-2023 www.zhuige.com All rights reserved.
	 */

	import Util from '@/utils/util';
	import Alert from '@/utils/alert';
	import Api from '@/utils/api';
	import Rest from '@/utils/rest';

	export default {
		data() {
			this.requesting = false;

			return {
				type: 'image',

				content: '',

				//图片
				images: [],

				//视频
				video: undefined,
				video_cover: undefined,

				forum: undefined,

				// 选择的话题
				subjects: [],

				longitude: '',
				latitude: '',
				marker: '',
				address: '',
			}
		},

		onLoad(options) {
			Util.addShareScore(options.source);

			if (options.type) {
				this.type = options.type;
			}

			if (options.fid && options.fname) {
				this.forum = {
					id: options.fid,
					name: options.fname
				}
			}

			this.preCreate();

			uni.$on('subjectChange', this.onSubjectChange);
			uni.$on('forumChange', this.onForumChange);
		},

		onUnload() {
			uni.$off('forumChange', this.onForumChange);
			uni.$off('subjectChange', this.onSubjectChange);
		},

		onShareAppMessage() {
			return {
				title: '发布-' + getApp().globalData.appName,
				path: Util.addShareSource('pages/bbs/post/post?n=n')
			};
		},

		// #ifdef MP-WEIXIN
		onShareTimeline() {
			return {
				title: '发布-' + getApp().globalData.appName,
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
			 * 切换类型
			 */
			clickType(type) {
				this.type = type;
			},

			/**
			 * 添加图片
			 */
			clickAddImage() {
				uni.chooseImage({
					count: 9 - this.images.length,
					sizeType: ['compressed'],
					success: (res) => {
						for (let i = 0; i < res.tempFilePaths.length; i++) {
							let path = res.tempFilePaths[i];
							Rest.upload(Api.URL('other', 'upload2'), path).then(oo => {
								if (oo.code != 0) {
									Alert.error(oo.message);
									return;
								}
								this.images.push({
									image: oo.data
								});
							}, err => {
								Alert.error(err);
							});
						}
					}
				});
			},

			/**
			 * 删除图片
			 */
			clickDelImage(index) {
				if (index >= 0 && index < this.images.length) {
					this.images.splice(index, 1);
				}
			},

			/**
			 * 添加视频
			 */
			clickAddVideo() {
				uni.chooseVideo({
					sourceType: ['camera', 'album'],
					success: (res) => {
						let path = res.tempFilePath;
						Rest.upload(Api.URL('other', 'uploadv'), path).then(oo => {
							if (oo.code == 0) {
								this.video = oo.data;
								this.video.width = res.width;
								this.video.height = res.height;
								console.log(this.video);
							} else {
								Alert.toast(oo.message);
							}
						}, err => {
							Alert.error(err);
						});
					}
				});
			},

			/**
			 * 添加视频封面
			 */
			clickAddVideoPoster() {
				uni.chooseImage({
					count: 1,
					sizeType: ['compressed'],
					success: (res) => {
						let path = res.tempFilePaths[0];
						Rest.upload(Api.URL('other', 'upload2'), path).then(oo => {
							if (oo.code != 0) {
								Alert.error(oo.message);
								return;
							}
							this.video_cover = oo.data;
						}, err => {
							Alert.error(err);
						});
					}
				});
			},

			/**
			 * 删除视频封面
			 */
			clickDelVideoPoster() {
				this.video_cover = false;
			},

			/**
			 * 选择话题结果
			 */
			onSubjectChange(data) {
				this.subjects = data;
			},

			/**
			 * 选择板块
			 */
			onForumChange(data) {
				this.forum = data;
			},

			/**
			 * 选择位置
			 */
			clickAddress() {
				let param = {
					success: (res) => {
						this.marker = res.name;
						this.address = res.address;
						this.longitude = res.longitude;
						this.latitude = res.latitude;
					},
					fail: (e) => {
						console.log(e)
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
			 * 选择话题
			 */
			clickSubject() {
				Util.openLink('/pages/bbs/subject/subject?subjects=' + this.subjects.join('-0-'));
			},

			/**
			 * 发帖
			 */
			clickCreate() {
				let content = this.content.replace(/\r\n/g, '<br/>').replace(/\n/g, '<br/>').replace(/\s/g, ' ');
				
				let params = {
					type: this.type,
					content: Util.htmlEncode(content),
					latitude: this.latitude,
					longitude: this.longitude,
					marker: this.marker,
					address: this.address,
				};

				if (!this.forum) {
					Alert.error('请选择圈子');
					return;
				}
				params.forum_id = this.forum.id;

				if (this.type == 'image') {
					params.images = JSON.stringify(this.images);
				} else if (this.type == 'video') {
					if (!this.video) {
						Alert.error('请上传视频');
						return;
					}
					params.video = JSON.stringify(this.video);
					params.video_cover = JSON.stringify(this.video_cover);
				}

				params.subjects = this.subjects.join('-0-');

				if (this.requesting) {
					return;
				}
				this.requesting = true;

				Rest.post(Api.URL('bbs', 'topic_create'), params).then(res => {
					if (res.code != 0) {
						if (res.code == 'require_mobile') {
							Util.openLink('/pages/user/login/login?type=mobile&tip=发帖');
						} else {
							Alert.error(res.message);
						}
						return;
					}

					Alert.toast('审核后，他人可见');

					setTimeout(() => {
						Util.navigateBack();
					}, 1500)
				}, err => {
					this.requesting = false;
					console.log(err)
				});
			},

			/**
			 * 发帖前准备
			 */
			preCreate() {
				Rest.post(Api.URL('bbs', 'topic_create_pre')).then(res => {
					if (res.code != 0) {
						if (res.code == 'require_mobile') {
							uni.redirectTo({
								url: '/pages/user/login/login?type=mobile&tip=发帖'
							})
						} else {
							Alert.error(res.message);
							setTimeout(() => {
								Util.navigateBack();
							}, 1500)
						}
					}
				}, err => {
					console.log(err)
				});
			}
		}
	}
</script>

<style>
	.content {
		position: fixed;
		height: 100%;
		width: 100%;
		overflow-y: scroll;
	}

	.zhuige-post-box {
		padding: 0 20rpx;
	}

	.zhuige-post-video-box {
		padding: 0 20rpx;
		display: flex;
		flex-wrap: wrap;
	}

	.zhuige-post-video-box video {
		width: 100%;
		margin-bottom: 20rpx;
	}

	.content .zhuige-post-box:nth-last-child(2) {
		margin-bottom: 180rpx;
	}

	.zhuige-post-line {
		display: flex;
		align-items: center;
		justify-content: space-between;
		line-height: 2.2em;
		height: 2.2em;
	}

	.zhuige-post-line>view:nth-child(1) {
		font-size: 30rpx;
		font-weight: 400;
		width: 156rpx;
	}

	.zhuige-post-line>view:nth-child(2) {
		display: flex;
		align-items: center;
		max-width: 510rpx;
	}

	.zhuige-post-line view:nth-child(2) view {
		margin-right: 12rpx;
		font-size: 28rpx;
		font-weight: 400;
		height: 1.6rem;
		line-height: 1.6rem;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.zhuige-post-line view:nth-child(2) input {
		width: 450rpx;
		text-align: right;
	}

	.zhuige-upload-set {
		display: flex;
		align-items: center;
		flex-wrap: wrap;
	}

	.zhuige-upload-set>view {
		height: 154rpx;
		width: 154rpx;
		border-radius: 12rpx;
		position: relative;
		text-align: center;
		margin: 0 20rpx 20rpx 0;
		text-align: center;
		background: #FFFFFF;
	}

	.zhuige-upload-set>view view {
		font-size: 28rpx;
		font-weight: 400;
		color: #777777;
		height: 1em;
		line-height: 1em;
		margin-top: -28rpx;
	}

	.zhuige-upload-set view.loaded image {
		height: 100%;
		width: 100%;
		border-radius: 12rpx;
	}

	.zhuige-upload-set>view.loaded uni-icons {
		position: absolute;
		z-index: 3;
		right: -20rpx;
		top: -46rpx;
	}

	.zhuige-post-input textarea {
		height: 240rpx;
		padding: 20rpx 0;
		font-size: 32rpx;
		line-height: normal;
	}

	.zhuige-post-plug {
		display: flex;
		flex-wrap: nowrap;
		align-items: center;
	}

	.zhuige-post-plug view {
		display: flex;
		flex-wrap: nowrap;
		align-items: center;
		opacity: 0.5;
		width: 16.66%;
		line-height: 1.8em;
	}

	.zhuige-post-plug view.active {
		opacity: 1;
	}

	.zhuige-post-plug view image {
		height: 28rpx;
		width: 28rpx;
		margin-right: 8rpx;
	}

	.zhuige-post-plug view text {
		font-size: 28rpx;
		font-weight: 300;
		margin-left: 8rpx;
		white-space: nowrap;
	}
</style>