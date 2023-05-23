<template>
	<view class="content">
		<!-- 搜索框 -->
		<view class="zhuige-topic-search-box">
			<view class="zhuige-topic-search">
				<view>
					<input type="text" v-model="new_subject" placeholder="请输入话题..." confirm-type="done"
						@confirm="inputConfirm" />
				</view>
				<view @click="clickAddNew">添加</view>
			</view>
		</view>

		<!-- 标签编辑 -->
		<view v-if="subjects.length>0" class="zhuige-topic-tag-check">
			<view class="zhuige-topic-tag-list">
				<!-- 标签基础块，放入 标签编辑 时，增加 uni-icons 结构 -->
				<view v-for="(subject, index) in subjects" :key="index">
					<view>#{{subject}}</view>
					<uni-icons @click="clickDelete(subject)" type="clear" color="#FD6531" size="16"></uni-icons>
				</view>
			</view>
		</view>

		<!-- 话题块 -->
		<view v-if="recs.length>0" class="zhuige-topic-tag-group">
			<view class="zhuige-block">
				<view class="zhuige-block-head">
					<view>话题推荐</view>
				</view>
				<view class="zhuige-topic-tag-list">
					<view v-for="(subject, index) in recs" :key="subject.id" :class="subject.class"
						@click="clickSubject(subject)">
						<view>#{{subject.name}}</view>
					</view>
				</view>
			</view>
		</view>

		<!-- 话题块 -->
		<view v-if="hots.length>0" class="zhuige-topic-tag-group">
			<view class="zhuige-block">
				<view class="zhuige-block-head">
					<view>热门话题(Top {{hots.length}})</view>
				</view>
				<view class="zhuige-topic-tag-list">
					<view v-for="(subject, index) in hots" :key="subject.id" :class="subject.class"
						@click="clickSubject(subject)">
						<view>#{{subject.name}}</view>
					</view>
				</view>
			</view>
		</view>

		<!-- 话题块 -->
		<view class="zhuige-topic-tag-group">
			<view class="zhuige-block">
				<view class="zhuige-block-head">
					<view>话题(总计 {{alls.length}} 个话题)</view>
				</view>

				<view class="zhuige-topic-tag-list">
					<view v-for="(subject, index) in alls" :key="subject.id" :class="subject.class"
						@click="clickSubject(subject)">
						<view>#{{subject.name}}</view>
					</view>
				</view>

			</view>
		</view>

		<!-- 底部大按钮 -->
		<view class="zhuige-base-button">
			<view @click="clickOK">确定</view>
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
				new_subject: '',
				subjects: [],

				hots: [],
				recs: [],
				alls: [],
			}
		},

		onLoad(options) {
			Util.addShareScore(options.source);

			if (options.subjects) {
				let subjects = decodeURIComponent(options.subjects);
				this.subjects = subjects.split('-0-');
			}

			this.loadSetting();
		},

		onShow() {
			// #ifdef MP-BAIDU
			swan.setPageInfo({
				title: '话题汇总-' + getApp().globalData.appName,
				keywords: getApp().globalData.appKeywords,
				description: getApp().globalData.appDesc,
			});
			// #endif
		},

		onShareAppMessage() {
			return {
				title: '话题汇总-' + getApp().globalData.appName,
				path: Util.addShareSource('pages/bbs/subjects/subjects?n=n')
			};
		},

		// #ifdef MP-WEIXIN
		onShareTimeline() {
			return {
				title: '话题汇总-' + getApp().globalData.appName,
			};
		},
		// #endif

		methods: {
			/**
			 * 增加话题
			 */
			addNewSubject() {
				if (this.new_subject) {
					let found = false;
					this.subjects.forEach((ele) => {
						if (ele == this.new_subject) {
							found = true;
						}
					})

					if (found) {
						Alert.toast(this.new_subject + '已添加过了');
					} else {
						this.subjects.push(this.new_subject);

						this.hots.forEach((ele) => {
							if (ele.name == this.new_subject) {
								ele.class = 'active';
							}
						})

						this.recs.forEach((ele) => {
							if (ele.name == this.new_subject) {
								ele.class = 'active';
							}
						})

						this.alls.forEach((ele) => {
							if (ele.name == this.new_subject) {
								ele.class = 'active';
							}
						})
					}

					this.new_subject = '';
				} else {
					Alert.toast('不能添加空话题');
				}
			},

			/**
			 * 输入确定增加话题
			 */
			inputConfirm() {
				this.addNewSubject();
			},

			/**
			 * 点击增加话题
			 */
			clickAddNew() {
				this.addNewSubject();
			},

			/**
			 * 取消已选择话题
			 */
			clickDelete(subject) {
				let new_subjects = [];
				this.subjects.forEach((ele) => {
					if (ele != subject) {
						new_subjects.push(ele);
					}
				})
				this.subjects = new_subjects;

				this.hots.forEach((ele) => {
					if (ele.name == subject) {
						ele.class = '';
					}
				})

				this.recs.forEach((ele) => {
					if (ele.name == subject) {
						ele.class = '';
					}
				})

				this.alls.forEach((ele) => {
					if (ele.name == subject) {
						ele.class = '';
					}
				})
			},

			/**
			 * 点击选择话题
			 */
			clickSubject(subject) {
				let found = false;
				let new_subjects = [];
				this.subjects.forEach((ele) => {
					if (ele == subject.name) {
						found = true;
					} else {
						new_subjects.push(ele);
					}
				})

				if (found) {
					subject.class = '';
					this.subjects = new_subjects;
				} else {
					subject.class = 'active';
					this.subjects.push(subject.name);
				}
			},

			/**
			 * 确定
			 */
			clickOK(e) {
				if (this.subjects.length == 0) {
					Alert.toast('请至少选择一个话题');
					return;
				}

				if (this.subjects.length > 3) {
					Alert.toast('最多选择3个话题');
					return;
				}

				uni.$emit('subjectChange', this.subjects);
				Util.navigateBack();
			},

			/**
			 * 加载配置
			 */
			loadSetting() {
				Rest.post(Api.URL('bbs', 'setting_subject')).then(res => {
					this.hots = res.data.hots;
					this.recs = res.data.recs;
					this.alls = res.data.alls;

					this.subjects.forEach((subject) => {
						this.hots.forEach((ele) => {
							if (ele.name == subject) {
								ele.class = 'active';
							}
						})

						this.recs.forEach((ele) => {
							if (ele.name == subject) {
								ele.class = 'active';
							}
						})

						this.alls.forEach((ele) => {
							if (ele.name == subject) {
								ele.class = 'active';
							}
						})
					})
				}, err => {
					console.log(err)
				});
			},
		}
	}
</script>

<style>
	.content {
		position: fixed;
		height: 100%;
		width: 100%;
		overflow-y: scroll;
		padding-bottom: 180rpx;
	}

	.zhuige-topic-tag-group {
		padding: 0 20rpx;
	}

	.zhuige-topic-search-box {
		padding: 0 20rpx 20rpx;
	}

	.zhuige-topic-search {
		display: flex;
		align-items: center;
		justify-content: space-between;
		background: #FFFFFF;
		border: 1rpx solid #DDDDDD;
		border-radius: 12rpx;
		height: 100rpx;
		line-height: 100rpx;
	}

	.zhuige-topic-search view:nth-child(1) {
		width: 80%;
	}

	.zhuige-topic-search view:nth-child(1) input {
		padding-left: 30rpx;
		font-size: 30rpx;
	}

	.zhuige-topic-search view:nth-child(2) {
		width: 20%;
		text-align: center;
		font-size: 36rpx;
		font-weight: 500;
		border-left: 1rpx solid #DDDDDD;
	}
</style>