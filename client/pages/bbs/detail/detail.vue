<template>
	<view class="content">
		<!-- 轮播图 -->
		<view class="zhuige-article-swiper" v-if="topic && topic.images && topic.images.length>0">
			<swiper indicator-dots="true" autoplay="autoplay" circular="ture"
				:style="{'width':'750rpx', 'height': slideHeight}" indicator-color="rgba(255,255,255, 0.3)"
				indicator-active-color="rgba(255,255,255, 0.8)" interval="5000" duration="150" easing-function="linear"
				@change="onImageChange">
				<swiper-item v-for="(item, index) in topic.images" :key="index">
					<view @click="clickTopicImages(topic.images, index)">
						<image mode="widthFix" :src="item.image.url"></image>
					</view>
				</swiper-item>
			</swiper>
		</view>

		<!-- 主内容 -->
		<view class="zhuige-detail-block">
			<view v-if="topic" class="zhuige-block">
				<!-- 发布者 -->
				<view v-if="topic.author" class="zhuige-social-poster-blcok">
					<view @click="openLink('/pages/user/home/home?user_id=' + topic.author.user_id)"
						class="zhuige-social-poster">
						<view class="zhuige-social-poster-avatar">
							<image mode="aspectFill" :src="topic.author.avatar"></image>
						</view>
						<view class="zhuige-social-poster-info">
							<view>
								<text>{{topic.author.nickname}}</text>
								<image v-if="topic.author.certify && topic.author.certify.status==1" mode="aspectFill"
									:src="topic.author.certify.icon">
								</image>
								<image class="zhuige-social-vip" v-if="topic.author.vip && topic.author.vip.status==1"
									mode="aspectFill" :src="topic.author.vip.icon">
								</image>
							</view>
							<view>
								<text>{{topic.time}}</text>
							</view>
						</view>
					</view>
					<view class="zhuige-social-opt">
						<view v-if="is_report" @click="openLink('/pages/plugins/report/report?post_id=' + topic.id)">
							<uni-icons type="more-filled" size="20"></uni-icons>
						</view>
						<view @click="clickFollowAuthor" v-if="topic.author.is_follow">已关注</view>
						<view @click="clickFollowAuthor" v-else>+关注</view>
					</view>
				</view>

				<video v-if="topic.video" :id="'topic_detail_' + topic.id" :src="topic.video.url"
					:poster="topic.video_cover.url" object-fit="cover" @play="videoPlay" :style="{
					width: parseInt(topic.video.width)>parseInt(topic.video.height)?'654rpx':parseInt(topic.video.width)/parseInt(topic.video.height)*674 + 'rpx',
					height: parseInt(topic.video.width)<parseInt(topic.video.height)?'654rpx':parseInt(topic.video.height)/parseInt(topic.video.width)*674 + 'rpx'
				}"></video>

				<!-- 贴内图片广告 -->
				<view v-if="topic.top_img_ad" class="zhuige-wide-image-ad">
					<view @click="openLink(topic.top_img_ad.link)">
						<image mode="aspectFill" :src="topic.top_img_ad.image"></image>
					</view>
				</view>

				<!-- 主内容区 -->
				<view class="zhuige-detail-cont">
					<mp-html :content="topic.content" />
				</view>

				<!-- 话题 -->
				<view v-if="topic.subjects && topic.subjects.length>0" class="zhuige-detail-tags">
					<view v-for="(subject, index) in topic.subjects" :key="index"
						@click="openLink('/pages/bbs/list/list?subject_id=' + subject.id + '&title=' + subject.name)">
						<image mode="aspectFill" src="/static/topic.png"></image>
						<view>{{subject.name}}</view>
					</view>
				</view>

				<!-- 地址信息 -->
				<view v-if="topic.location && topic.location.marker" @click="clickLocation"
					class="zhuige-social-address">
					<view>
						<uni-icons type="location-filled" size="20"></uni-icons>
						<view>{{topic.location.marker}}</view>
					</view>
				</view>

				<!-- 圈子信息 -->
				<view v-if="topic.forum" class="zhuige-detail-classify">
					<view class="zhuige-classify-block"
						@click="openLink('/pages/bbs/forum/forum?forum_id=' + topic.forum.id)">
						<view>
							<image mode="aspectFill" :src="topic.forum.logo"></image>
						</view>
						<view class="zhuige-classify-text">
							<view>{{topic.forum.name}}</view>
							<view>
								<text>{{topic.forum.user_count}}成员</text>
								<text>/</text>
								<text>{{topic.forum.post_count}}作品</text>
							</view>
						</view>
					</view>
					<view @click="clickJoinForum(topic.forum.id)" class="zhuige-classify-join">
						<view v-if="topic.forum.is_follow">已加入</view>
						<view v-else>+加入</view>
					</view>
				</view>

			</view>
		</view>

		<!-- 自定义滚动广告 -->
		<view v-if="topic && topic.ad_imgs" class="zhuige-detail-ad">
			<zhuige-scroll-ad ext-ad-class="zhuige-scroll-goods-mini" :title="topic.ad_imgs.title"
				:items="topic.ad_imgs.items"></zhuige-scroll-ad>
		</view>

		<!-- 点赞分享 -->
		<view v-if="topic" class="zhuige-detail-share">
			<view class="zhuige-block">
				<view v-if="topic.like_list && topic.like_list.length>0" class="zhuige-detail-share-user">
					<view class="zhuige-detail-share-title">- {{topic.like_list.length}}人点赞 -</view>
					<view class="zhuige-detail-share-user-list">
						<view v-for="(user, index) in topic.like_list" :key="index"
							@click="openLink('/pages/user/home/home?user_id=' + user.user_id)">
							<image mode="aspectFill" :src="user.avatar"></image>
						</view>
					</view>
				</view>
				<view class="zhuige-detail-share-opt">
					<view @click="clickPoster()">
						<text>分享海报</text>
						<image mode="aspectFill" src="/static/poster.png"></image>
					</view>
					<view v-if="topic.author && topic.author.reward" @click="clickReward">
						<text>鼓励作者</text>
						<image mode="aspectFill" src="/static/packet.png"></image>
					</view>
				</view>
			</view>
		</view>

		<!-- 流量主广告 -->
		<!-- #ifdef MP-WEIXIN -->
		<view v-if="traffic_ad" class="zhuige-traffic-ad">
			<view class="zhuige-block">
				<ad-custom :unit-id="traffic_ad"></ad-custom>
			</view>
		</view>
		<!-- #endif -->

		<!-- 评价列表 -->
		<view class="zhuige-reply-list">
			<view class="zhuige-block">
				<view class="zhuige-block-head">
					<view>近期回复</view>
					<view @click="clickAllComments">查看全部</view>
				</view>

				<template v-if="topic && topic.comments.length>0">
					<zhuige-reply v-for="(comment, index) in topic.comments" :key="index" :item="comment"
						@clickReply="clickReply">
					</zhuige-reply>
				</template>
				<template v-else>
					<!-- 无内容提示 -->
					<view class="zhuige-none-tips">
						<image mode="aspectFill" src="/static/404.png"></image>
						<view>暂无评论，抢个沙发</view>
					</view>
				</template>
			</view>
		</view>

		<!-- 图片广告 -->
		<view v-if="topic && topic.bottom_img_ad" class="zhuige-wide-image-ad">
			<view @click="openLink(topic.bottom_img_ad.link)">
				<image mode="aspectFill" :src="topic.bottom_img_ad.image"></image>
			</view>
		</view>

		<!-- 相关推荐 -->
		<view v-if="topic && topic.recs && topic.recs.length>0" class="zhuige-detail-recom">
			<zhuige-topic v-for="(topic, index) in topic.recs" :key="index" :topic="topic"></zhuige-topic>
		</view>

		<!-- 底部按钮组 -->
		<view v-if="topic" class="zhuige-detail-comment">
			<view @click="clickComment(0)" class="zhuige-detail-input">
				<text>我想说两句...</text>
			</view>
			<view class="zhuige-detail-opt">
				<view @click="clickComment(0)">
					<uni-badge :offset="[0, 12]" :text="topic.comment_count.toString()" :inverted="false" type="error"
						absolute="rightTop">
						<uni-icons :type="topic.is_comment==1?'chat-filled':'chat'"
							:color="topic.is_comment==1?'#ff6146':'#444444'" size="24"></uni-icons>
					</uni-badge>
				</view>
				<view @click="clickLike">
					<uni-badge :offset="[0, 12]" :text="topic.like_list.length.toString()" :inverted="false"
						type="error" absolute="rightTop">
						<uni-icons :type="topic.is_like==1?'hand-up-filled':'hand-up'"
							:color="topic.is_like==1?'#ff6146':'#444444'" size="24"></uni-icons>
					</uni-badge>
				</view>
				<view @click="clickFavorite">
					<uni-badge :offset="[0, 12]" :text="topic.favorites.toString()" :inverted="false" type="error"
						absolute="rightTop">
						<uni-icons :type="topic.is_favorite==1?'star-filled':'star'"
							:color="topic.is_favorite==1?'#ff6146':'#444444'" size="24"></uni-icons>
					</uni-badge>
				</view>
			</view>
		</view>

		<!-- 海报组件 -->
		<!-- #ifdef MP-BAIDU -->
		<view v-if="isShowPainter" isRenderImage style="position: fixed; top: 0;" @longpress="longTapPainter"
			@click="clickPainter()">
			<l-painter isRenderImage :board="base" @success="onPainterSuccess" />
		</view>
		<!-- #endif -->

		<!-- #ifdef MP-WEIXIN || H5 -->
		<l-painter v-if="isShowPainter" isRenderImage custom-style="position: fixed; left: 200%;" :board="base"
			@success="onPainterSuccess" />
		<!-- #endif -->

		<!-- 弹窗 -->
		<uni-popup ref="popup" type="bottom">
			<view class="zhuige-pop-textarea" :style="{'margin-bottom': '' + comment_bottom + 'px'}">
				<view>
					<textarea v-model="comment_content" maxlength="140" placeholder="友善是交流的起点......"
						@focus="focusCommentTextarea" @blur="blurCommentTextarea" fixed="true"></textarea>
				</view>
				<view class="zhuige-pop-text-btn">
					<text>{{comment_content.length}}/140</text>
					<view @click="clickSubmitComment">确定</view>
				</view>
			</view>
		</uni-popup>
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

	import lPainter from '@/uni_modules/lime-painter/components/lime-painter/'
	import ZhuigeTopic from "@/components/zhuige-topic";
	import ZhuigeReply from "@/components/zhuige-reply";
	import ZhuigeSwiper from "@/components/zhuige-swiper";
	import ZhuigeScrollAd from "@/components/zhuige-scroll-ad";

	export default {
		data() {
			this.topic_id = undefined;

			// 小程序码
			this.acode = undefined;

			// 回复 - 评论ID
			this.comment_id = 0;
			this.reply_user_id = 0;

			// #ifdef MP-WEIXIN
			// 微信广告
			this.traffic_chp = null;
			// #endif

			return {
				curImage: 100, // 只有类型是图片时有效

				topic: undefined,

				//海报
				isShowPainter: false,
				painterImage: '',
				poster: undefined, //海报配置
				base: undefined,

				// 评论
				comment_content: '',
				// 评论框 底部举例
				comment_bottom: 0,

				// 举报功能
				is_report: false,

				// #ifdef MP-WEIXIN
				// 微信广告
				traffic_ad: undefined,
				// #endif

				//登录后 重新加载
				loginReload: false,
			}
		},

		components: {
			lPainter,
			ZhuigeTopic,
			ZhuigeReply,
			ZhuigeSwiper,
			ZhuigeScrollAd
		},

		computed: {
			slideHeight() {
				if (this.topic && this.topic.images && this.topic.images.length > 0) {
					let image = this.topic.images[this.curImage];
					let height = image.image.height * 750 / image.image.width;
					return `${height}rpx`;
				} else {
					//TODO
					return `${this.curImage}rpx`;
				}
			}
		},

		onLoad(options) {
			if (options.id) {
				options.topic_id = options.id;
			}

			if (options.topic_id) {
				this.topic_id = options.topic_id;
			} else if (options.scene) {
				// 小程序码分享
				this.topic_id = options.scene;
			} else {
				this.topic_id = 1;
			}

			if (!this.topic_id) {
				uni.reLaunch({
					url: '/pages/tabs/index/index'
				})
				return;
			}

			Util.addShareScore(options.source);

			uni.$on('linktap', this.onMPHtmlLink);
			uni.$on('zhuige_event_user_like', this.onUserLike);
			uni.$on('zhuige_event_user_login', this.onSetReload);
		},

		onUnload() {
			uni.$off('zhuige_event_user_login', this.onSetReload);
			uni.$off('zhuige_event_user_like', this.onUserLike);
			uni.$off('linktap', this.onMPHtmlLink);
		},

		onShow() {
			if (!this.topic) {
				this.loadData();
			} else {
				// #ifdef MP-BAIDU
				let content = this.topic.content.replace(/(<([^>]+)>)/ig, '');
				swan.setPageInfo({
					title: content.substring(0, 20),
					keywords: this.topic.forum.name,
					description: content,
				});
				// #endif
			}

			if (this.loginReload) {
				this.loginReload = false;

				this.loadData();
			}
		},

		onBackPress() {
			if (this.$refs.popup.isShow) {
				this.$refs.popup.close()
				return true
			}
			return false
		},

		onPullDownRefresh() {
			this.loadData()();
		},

		onShareAppMessage() {
			return {
				title: this.contentText() + '-' + getApp().globalData.appName,
				path: Util.addShareSource('pages/bbs/detail/detail?topic_id=' + this.topic_id)
			};
		},

		// #ifdef MP-WEIXIN
		onShareTimeline() {
			return {
				title: this.contentText() + '-' + getApp().globalData.appName,
			};
		},
		// #endif

		methods: {
			// ------- event start ---------
			/**
			 * 点击文章内链接
			 */
			onMPHtmlLink(data) {
				if (data['data-link']) {
					Util.openLink(data['data-link']);
				}
			},

			/**
			 * 点赞事件
			 */
			onUserLike(data) {
				if (this.topic && this.topic.recs && this.topic.recs.length > 0) {
					this.topic.recs.forEach((topic) => {
						if (topic.id == data.post_id) {
							topic.like_count = data.like_count;
						}
					})
				}
			},

			/**
			 * 需要重新加载事件
			 */
			onSetReload(data) {
				this.loginReload = true;
			},
			// ------- event end ---------

			/**
			 * 点击打开链接
			 */
			openLink(link) {
				Util.openLink(link);
			},

			/**
			 * 顶部图片切换
			 */
			onImageChange(e) {
				this.curImage = e.detail.current;
			},

			/**
			 * 点击查看顶部图片
			 */
			clickTopicImages(images, index) {
				let urls = [];
				images.forEach(image => {
					urls.push(image.image.url);
				});

				uni.previewImage({
					current: index,
					urls: urls,
				})
			},

			/**
			 * 点击关注作者
			 */
			clickFollowAuthor() {
				Rest.post(Api.URL('user', 'follow_user'), {
					user_id: this.topic.author.user_id
				}).then(res => {
					if (res.code == 0) {
						this.topic.author.is_follow = res.data.is_follow;
						uni.$emit('zhuige_event_follow_user', {
							user_id: this.topic.author.user_id,
							is_follow: res.data.is_follow
						});
					} else {
						Alert.toast(res.message);
					}
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 点击打开位置
			 */
			clickLocation() {
				uni.openLocation({
					name: this.topic.location.marker,
					address: this.topic.location.address,
					latitude: parseFloat(this.topic.location.latitude),
					longitude: parseFloat(this.topic.location.longitude),
					success: () => {
						// console.log('success');
					}
				});
			},

			/**
			 * 点击加入圈子
			 */
			clickJoinForum(forum_id) {
				Rest.post(Api.URL('user', 'follow_forum'), {
					forum_id: forum_id
				}).then(res => {
					this.topic.forum.is_follow = res.data.is_follow;
					this.topic.forum.user_count = res.data.user_count;

					uni.$emit('zhuige_event_user_join_forum', {
						forum_id: forum_id,
						user_count: res.data.user_count,
					});
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 点击打赏
			 */
			clickReward() {
				uni.previewImage({
					current: 0,
					urls: [this.topic.author.reward],
				})
			},

			/**
			 * 点击查看所有评论
			 */
			clickAllComments() {
				Util.openLink('/pages/base/comments/comments?post_id=' + this.topic_id);
			},

			/**
			 * 点击弹出评论框
			 */
			clickComment(comment_id) {
				if (!this.topic.comment_switch) {
					Alert.error('评论已关闭');
					return;
				}

				if (this.topic.comment_require_mobile && !Util.checkMobile('评论')) {
					return;
				}

				this.comment_id = comment_id;
				this.reply_user_id = 0;
				this.$refs.popup.open('bottom')
			},

			/**
			 * 评论框获取焦点
			 */
			focusCommentTextarea(e) {
				this.comment_bottom = e.detail.height;
			},

			/**
			 * 评论框失去焦点
			 */
			blurCommentTextarea(e) {
				this.comment_bottom = 0;
			},

			/**
			 * 点击回复评论
			 */
			clickReply(params) {
				this.comment_id = params.comment_id;
				this.reply_user_id = params.user_id;
				this.$refs.popup.open('bottom')
			},

			/**
			 * 发表评论
			 */
			clickSubmitComment() {
				if (this.comment_content.length == 0) {
					Alert.toast('请输入评论内容');
					return;
				}

				Rest.post(Api.URL('comment', 'add'), {
					post_id: this.topic_id,
					parent_id: this.comment_id,
					reply_id: this.reply_user_id,
					content: this.comment_content
				}).then(res => {
					if (res.code != 0) {
						if (res.code == 'require_mobile') {
							Util.openLink('/pages/user/login/login?type=mobile&tip=评论');
						} else {
							Alert.toast(res.message);
						}
						return;
					}

					Alert.toast('审核后，他人可见');

					this.comment_id = 0;
					this.reply_user_id = 0;
					this.comment_content = '';
					this.$refs.popup.close()
				}, err => {
					console.log(err);
				});
			},

			/**
			 * 点赞【喜欢】
			 */
			clickLike() {
				Rest.post(Api.URL('user', 'like'), {
					post_id: this.topic_id
				}).then(res => {
					this.topic.is_like = res.data.is_like;
					if (res.data.is_like) {
						this.topic.like_list.unshift(res.data.user)
					} else {
						let like_list = [];
						this.topic.like_list.forEach(item => {
							if (item.user_id != res.data.user.user_id) {
								like_list.push(item);
							}
						});
						this.topic.like_list = like_list;
					}

					uni.$emit('zhuige_event_user_like', {
						post_id: this.topic_id,
						like_count: res.data.like_count
					});
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 点击收藏
			 */
			clickFavorite() {
				Rest.post(Api.URL('user', 'favorite'), {
					post_id: this.topic_id
				}).then(res => {
					this.topic.is_favorite = res.data.is_favorite;
					this.topic.favorites = res.data.favorites;
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 加载文章
			 */
			loadData() {
				Rest.post(Api.URL('bbs', 'topic_detail'), {
					topic_id: this.topic_id
				}).then(res => {
					this.topic = res.data.topic;
					this.poster = res.data.poster;
					this.is_report = res.data.is_report;

					if (this.topic.images) {
						this.curImage = 0;
					}

					// #ifdef MP-WEIXIN
					if (res.data.traffic_ad) {
						this.traffic_ad = res.data.traffic_ad;
					}
					if (res.data.traffic_chp && wx.createInterstitialAd && !this.traffic_chp) {
						this.traffic_chp = wx.createInterstitialAd({
							adUnitId: res.data.traffic_chp
						})
						this.traffic_chp.onLoad(() => {})
						this.traffic_chp.onError((err) => {
							console.log(err)
						})
						this.traffic_chp.onClose(() => {})
						this.traffic_chp.show();
					}
					// #endif

					this.loadACode();

					uni.stopPullDownRefresh();
				}, err => {
					console.log(err)
				});
			},

			/**
			 * 文章内容
			 */
			contentText() {
				let text = this.topic.content.replace(/(<([^>]+)>)/ig, '');
				return text.replace(/&nbsp;/ig, '');
			},

			//海报分享-百度
			// #ifdef MP-BAIDU
			clickPainter() {
				this.isShowPainter = false;
			},

			/**
			 * 查看海报
			 */
			longTapPainter() {
				uni.showActionSheet({
					itemList: ['保存到相册'],
					success: (res) => {
						if (res.tapIndex == 0) {
							uni.showLoading({
								title: '导出……'
							})
							let save2album = setInterval(() => {
								if (!this.painterImage || this.painterImage.length == 0) {
									return;
								}
								clearInterval(save2album)
								uni.hideLoading();

								uni.saveImageToPhotosAlbum({
									filePath: this.painterImage,
									success() {
										Alert.toast('已保存')
									}
								})
							}, 500);
						}
					},
					fail: (res) => {
						console.log(res.errMsg);
					}
				});
			},
			// #endif

			/**
			 * 海报分享-微信
			 */
			clickPoster() {
				if (!this.poster) {
					Alert.error('阅读全文后，可查看');
					return;
				}

				// #ifndef MP-BAIDU
				if (this.painterImage) {
					uni.previewImage({
						urls: [this.painterImage]
					});
					return;
				}
				// #endif

				this.isShowPainter = true;

				this.base = {
					width: '750rpx',
					height: '1334rpx',
					// backgroundColor: '#FFFFFF',
					views: [{
							type: 'image',
							src: this.poster.background,
							mode: 'aspectFill',
							css: {
								left: '0rpx',
								top: '0rpx',
								width: '750rpx',
								height: '1334rpx'
							}
						},
						{
							type: 'image',
							src: this.topic.author.avatar,
							mode: 'aspectFill',
							css: {
								left: '30rpx',
								top: '70rpx',
								width: '120rpx',
								height: '120rpx',
								radius: '60rpx'
							}
						},
						{
							type: 'text',
							text: this.topic.author.nickname,
							css: {
								left: '165rpx',
								top: '90rpx',
								width: '525rpx',
								color: '#FFFFFF',
								fontSize: '38rpx',
								textAlign: 'left',
								maxLines: 1,
							}
						},
						{
							type: 'text',
							text: this.topic.author.sign ? this.topic.author.sign : this.poster.title,
							css: {
								left: '165rpx',
								top: '140rpx',
								width: '525rpx',
								color: '#FFFFFF',
								fontSize: '24rpx',
								textAlign: 'left',
								maxLines: 1,
							}
						},
						{
							type: 'view',
							css: {
								left: '30rpx',
								top: '240rpx',
								width: '690rpx',
								height: '860rpx',
								background: '#FFFFFF',
								radius: '20rpx',
							}
						},
						{
							type: 'image',
							src: this.poster.thumb,
							mode: 'aspectFill',
							css: {
								left: '30rpx',
								top: '240rpx',
								width: '690rpx',
								height: '500rpx',
								radius: '20rpx',
							}
						},
						{
							type: 'text',
							text: this.contentText(),
							css: {
								left: '70rpx',
								top: '790rpx',
								width: '610rpx',
								color: '#000000',
								fontSize: '28rpx',
								lineHeight: '50rpx',
								maxLines: 2,
							}
						},

						// #ifdef MP-WEIXIN
						{
							type: 'view',
							css: {
								left: '255rpx',
								top: '940rpx',
								width: '240rpx',
								height: '240rpx',
								background: '#FFFFFF',
								radius: '120rpx'
							}
						},
						{
							type: 'image',
							src: this.acode,
							mode: 'aspectFill',
							css: {
								left: '275rpx',
								top: '960rpx',
								width: '200rpx',
								height: '200rpx',
								// radius: '100rpx',
								borderRadius: '30%',
								border: '1rpx solid rgb(255,255,255)'
							}
						},
						// #endif

						// #ifdef MP-BAIDU
						{
							type: 'view',
							css: {
								left: '255rpx',
								top: '940rpx',
								width: '240rpx',
								height: '240rpx',
								background: '#FFFFFF',
								radius: '20rpx'
							}
						},
						{
							type: 'image',
							src: this.acode,
							mode: 'aspectFill',
							css: {
								left: '275rpx',
								top: '960rpx',
								width: '200rpx',
								height: '200rpx'
							}
						},
						// #endif

						{
							type: 'text',
							text: getApp().globalData.appName,
							css: {
								left: '30rpx',
								top: '1200rpx',
								width: '690rpx',
								color: '#FFFFFF',
								fontSize: '28rpx',
								textAlign: 'center',
							}
						},
					]
				}
			},

			/**
			 * 海报完成绘制 - 展示海报
			 */
			onPainterSuccess: function(e) {
				this.painterImage = e;

				// #ifndef MP-BAIDU
				uni.previewImage({
					urls: [e]
				});
				// #endif
			},

			/**
			 * 加载小程序码
			 */
			loadACode() {
				// #ifdef MP-WEIXIN
				Rest.post(Api.URL('posts', 'wxacode'), {
					post_id: this.topic_id,
				}).then(res => {
					this.acode = res.data.acode;
				}, err => {
					console.log(err);
				});
				// #endif

				// #ifdef MP-BAIDU || H5
				Rest.post(Api.URL('posts', 'bdacode'), {
					post_id: this.topic_id,
				}).then(res => {
					this.acode = res.data.acode;
				}, err => {
					console.log(err);
				});
				// #endif
			},

			videoPlay(e) {
				let videoId = e.currentTarget.id;
				if (getApp().globalData.videoId !== videoId && getApp().globalData.videoContext) {
					getApp().globalData.videoContext.stop();
				}
				getApp().globalData.videoId = videoId;
				////创建控制视频标签的实例对象
				getApp().globalData.videoContext = wx.createVideoContext(getApp().globalData.videoId, this);
			},
		}
	}
</script>

<style>
	page {
		background: #f5f5f5;
	}

	.content {
		padding-bottom: 160rpx;
	}

	swiper {
		width: 100%;
	}

	swiper image {
		width: 100%;
		transition: all 0.1s linear;
	}

	.zhuige-detail-recom,
	.zhuige-reply-list,
	.zhuige-traffic-ad,
	.zhuige-detail-ad,
	.zhuige-detail-share,
	.zhuige-detail-block {
		padding: 0 20rpx;
	}

	.zhuige-detail-block .zhuige-block {
		padding: 20rpx 30rpx;
	}

	.zhuige-detail-ad {
		margin-bottom: 20rpx;
	}

	.zhuige-detail-ad .zhuige-scroll-ad-box {
		padding-bottom: 0 !important;
	}

	.zhuige-detail-ad .zhuige-scroll-ad {
		margin-top: -20rpx !important;
	}

	.zhuige-detail-ad .zhuige-scroll-ad-block {
		vertical-align: text-top !important;
	}

	.zhuige-detail-ad .zhuige-scroll-ad-info .title-info {
		font-size: 28rpx !important;
		font-weight: 600 !important;
	}

	.zhuige-detail-ad .price-unit {
		display: none;
	}

	.zhuige-detail-ad .price-info {
		padding: 0 !important;
	}

	.zhuige-detail-ad .zhuige-scroll-goods-mini .price-info .item-price {
		font-size: 26rpx !important;
	}


	.zhuige-reply-list .zhuige-block-head {
		position: relative;
		margin-bottom: -4rpx;
		border-bottom: 6rpx solid #FFFFFF;
		z-index: 2;
	}

	.zhuige-detail-block .zhuige-wide-image-ad {
		padding: 20rpx 0;
	}

	.zhuige-detail-block video {
		border-radius: 12rpx;
	}

	.zhuige-detail-cont {
		padding: 20rpx 0;
		font-size: 32rpx;
	}

	.zhuige-detail-classify {
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 30rpx;
		background: #F1F3F5;
		border-radius: 8rpx;
		border: 1rpx solid #EEEEEE;
		margin-top: 20rpx;
	}

	.zhuige-detail-classify .zhuige-classify-block {
		width: 76%;
		border: none;
		padding: 0;
	}

	.zhuige-detail-classify .zhuige-classify-block>view:nth-child(1) {
		height: 88rpx;
		width: 88rpx;
	}

	.zhuige-detail-classify .zhuige-classify-text view {
		height: 44rpx;
		line-height: 44rpx;
	}

	.zhuige-detail-comment {
		display: flex;
		align-items: center;
		justify-content: space-between;
		position: fixed;
		bottom: 0;
		left: 0;
		height: 140rpx;
		width: 100%;
		background: #FFFFFF;
		z-index: 20;
		padding-bottom: 20rpx;
		box-shadow: 0rpx 0rpx 6rpx rgba(99, 99, 99, 0.1);
	}

	.zhuige-detail-input {
		width: 320rpx;
		height: 80rpx;
		line-height: 80rpx;
		border-radius: 80rpx;
		background-color: #F6F6F6;
		margin-left: 30rpx;
		padding-left: 30rpx;
	}

	.zhuige-detail-input text {
		text-indent: 1.4rem;
		font-size: 24rpx;
		color: #999;
	}

	.zhuige-detail-opt {
		display: flex;
		align-items: center;
		padding: 0 30rpx;
	}

	.zhuige-detail-opt>view {
		margin: 0 24rpx;
	}

	.zhuige-detail-block .zhuige-social-poster-blcok {
		border-bottom: 1rpx solid #EEEEEE;
		padding-bottom: 20rpx;
	}

	/* 评论弹框 start */
	.zhuige-pop-textarea {
		padding: 30rpx 30rpx 0rpx;
		background: #FFFFFF;
		border-radius: 12rpx 12rpx 0 0;
		/* margin-bottom: -64rpx; */
	}

	.zhuige-pop-textarea>view:nth-child(1) {
		height: 360rpx;
	}

	.zhuige-pop-textarea>view:nth-child(1) textarea {
		height: 340rpx;
		width: 100%;
		font-size: 32rpx;
		line-height: normal;
	}

	.zhuige-pop-text-btn {
		display: flex;
		align-items: center;
		justify-content: flex-end;
		padding-bottom: 40rpx;
	}

	.zhuige-pop-text-btn text {
		font-size: 26rpx;
		font-weight: 300;
		color: #999999;
		margin-right: 20rpx;
	}

	.zhuige-pop-text-btn view {
		font-size: 28rpx;
		font-weight: 300;
		height: 60rpx;
		line-height: 60rpx;
		color: #FFFFFF;
		border-radius: 30rpx;
		text-align: center;
		background: #010101;
		width: 160rpx;
	}

	/* 评论弹框 end */

	.zhuige-article-swiper {
		margin-bottom: 20rpx;
	}

	.zhuige-article-swiper,
	.zhuige-article-swiper swiper,
	.zhuige-article-swiper swiper image {
		transition: all 0.3s linear;
	}

	.zhuige-wide-image-ad view {
		height: 120rpx;
	}
</style>