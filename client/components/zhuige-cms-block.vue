<template>
	<view>
		<!-- 无图 -->
		<view class="zhuige-block">
			<view class="zhugie-info-block">

				<view class="zhugie-info-text">
					<view class="zhugie-info-title">文章标题文章标题文章标题文章标题文章标题文章标题</view>
					<view class="zhuige-info-post">
						<view class="zhuige-info-poster">
							<view>
								<image mode="aspectFill" src="/static/logo.png"></image>
							</view>
							<text>用户名</text>
						</view>
						<view class="zhuige-info-data">
							<text>浏览 320</text>
							<text>点赞 490</text>
						</view>
					</view>
				</view>
			</view>
		</view>

		<!-- 单大图 列表基础结构 -->
		<view class="zhuige-block">
			<!-- 单大图 其他结构不变，对应 无图 增加 zhugie-info-image 结构 -->
			<view class="zhugie-info-block">
				<view class="zhugie-info-image">
					<image mode="aspectFill" src="/static/logo.png"></image>
				</view>
				<view class="zhugie-info-text">
					<view class="zhugie-info-title">文章标题文章标题文章标题文章标题文章标题文章标题</view>
					<view class="zhuige-info-post">
						<view class="zhuige-info-poster">
							<view>
								<image mode="aspectFill" src="/static/logo.png"></image>
							</view>
							<text>用户名</text>
						</view>
						<view class="zhuige-info-data">
							<text>浏览 320</text>
							<text>点赞 490</text>
						</view>
					</view>
				</view>
			</view>
		</view>

		<!-- 三图 -->
		<view class="zhuige-block">
			<view class="zhugie-info-block">
				<!-- 三图 其他结构不变，zhugie-info-image 后增加 image-treble-->
				<view class="zhugie-info-image image-treble">
					<image mode="aspectFill" src="/static/logo.png"></image>
					<image mode="aspectFill" src="/static/logo.png"></image>
					<image mode="aspectFill" src="/static/logo.png"></image>
				</view>
				<view class="zhugie-info-text">
					<view class="zhugie-info-title">文章标题文章标题文章标题文章标题文章标题文章标题</view>
					<view class="zhuige-info-post">
						<view class="zhuige-info-poster">
							<view>
								<image mode="aspectFill" src="/static/logo.png"></image>
							</view>
							<text>用户名</text>
						</view>
						<view class="zhuige-info-data">
							<text>浏览 320</text>
							<text>点赞 490</text>
						</view>
					</view>
				</view>
			</view>
		</view>

		<!-- 单左图 -->
		<view class="zhuige-block">
			<!-- 单左图，单右图，需要在 zhugie-info-block 后增加浮动控制样式 left-side, right-side -->
			<view class="zhugie-info-block left-side">
				<view class="zhugie-info-image">
					<image mode="aspectFill" src="/static/logo.png"></image>
				</view>
				<view class="zhugie-info-text">
					<view class="zhugie-info-title">文章标题文章标题文章标题文章标题文章标题文章标题</view>
					<view class="zhuige-info-post">
						<view class="zhuige-info-poster">
							<view>
								<image mode="aspectFill" src="/static/logo.png"></image>
							</view>
							<text>用户名</text>
						</view>
						<view class="zhuige-info-data">
							<text class="pay">100积分</text>
							<text>浏览 320</text>
						</view>
					</view>
				</view>
			</view>
		</view>

		<!-- 单右图 -->
		<view class="zhuige-block">
			<!-- 切换为右图时，zhugie-info-block 后增加 right-side -->
			<view class="zhugie-info-block right-side">
				<view class="zhugie-info-image">
					<image mode="aspectFill" src="/static/logo.png"></image>
				</view>
				<view class="zhugie-info-text">
					<view class="zhugie-info-title">文章标题文章标题文章标题文章标题文章标题文章标题</view>
					<view class="zhuige-info-post">
						<view class="zhuige-info-poster">
							<view>
								<image mode="aspectFill" src="/static/logo.png"></image>
							</view>
							<text>用户名</text>
						</view>
						<view class="zhuige-info-data">
							<text>浏览 320</text>
						</view>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import Util from '@/utils/util';

	export default {
		name: "zhuige-cms-block",

		props: {
			prop: {
				type: String,
				default: "prop"
			},
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
