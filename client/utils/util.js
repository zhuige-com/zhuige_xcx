import Constant from '@/utils/constants.js';
import Auth from '@/utils/auth';
import Alert from '@/utils/alert';
import Api from '@/utils/api';
import Rest from '@/utils/rest';

/**
 * 返回
 */
function navigateBack() {
	uni.navigateBack({
		delta: 1,
		fail(res) {
			uni.switchTab({
				url: '/pages/tabs/index/index'
			});
		}
	});
}

/**
 * 解析出url中的参数
 */
function getQueryString(url, name) {
	let index = url.indexOf("?");
	if (index != -1) {
		let str = url.substr(index + 1);
		let strs = str.split("=");
		if (strs[0] == name) {
			return strs[1];
		} else {
			return '';
		}
	}

	return '';
}

/**
 *  把html转义成HTML实体字符
 * @param str
 * @returns {string}
 * @constructor
 */
function htmlEncode(str) {
	var s = "";
	if (str.length === 0) {
		return "";
	}
	s = str.replace(/&/g, "&amp;");
	s = s.replace(/</g, "&lt;");
	s = s.replace(/>/g, "&gt;");
	// s = s.replace(/ /g, "&nbsp;"); //如果不忽略空格 投稿发布文章后 图片会丢失
	s = s.replace(/\'/g, "&#39;"); //IE下不支持实体名称
	s = s.replace(/\"/g, "&quot;");
	return s;
}

/**
 *  转义字符还原成html字符
 * @param str
 * @returns {string}
 * @constructor
 */
function htmlRestore(str) {
	var s = "";
	if (str.length === 0) {
		return "";
	}
	s = str.replace(/&amp;/g, "&");
	s = s.replace(/&lt;/g, "<");
	s = s.replace(/&gt;/g, ">");
	s = s.replace(/&nbsp;/g, " ");
	s = s.replace(/&#39;/g, "\'");
	s = s.replace(/&quot;/g, "\"");
	return s;
}

/**
 * 打开链接
 */
function openLink(link) {
	if (!link) {
		return;
	}

	if (link.startsWith('/pages/')) {
		let links = [];

		if (!Auth.getUser()) {
			links = [
				'/pages/user/info/info',
				'/pages/bbs/post/post',
				'/pages/bbs/forum-create/forum-create',
				'/pages/contribution/manage/manage',
				'/pages/contribution/post-edit/post-edit',
				'/pages/vote/post/post',
			];
			for (let i = 0; i < links.length; i++) {
				if (link.indexOf(links[i]) > -1) {
					uni.navigateTo({
						url: '/pages/user/login/login',
						fail(res) {
							uni.redirectTo({
								url: '/pages/user/login/login'
							});
						}
					});
					return;
				}
			}

			links = ['/pages/user/home/home', '/pages/user/friend/friend'];
			for (let i = 0; i < links.length; i++) {
				if (link.indexOf(links[i]) > -1 && link.indexOf('user_id') == -1) {
					uni.navigateTo({
						url: '/pages/user/login/login',
						fail(res) {
							uni.redirectTo({
								url: '/pages/user/login/login'
							});
						}
					});
					return;
				}
			}
		}

		links = [
			'/pages/tabs/forum/forum',
			'/pages/tabs/notice/notice',
			'/pages/tabs/create/create',
			'/pages/tabs/index/index',
			'/pages/tabs/mine/mine'
		];
		for (let i = 0; i < links.length; i++) {
			if (link.indexOf(links[i]) > -1) {
				getApp().globalData.tabTab = getQueryString(link, 'tab');
				uni.switchTab({
					url: links[i]
				});
				return;
			}
		}

		link = htmlRestore(link);
		uni.navigateTo({
			url: link,
			fail: (res) => {
				uni.redirectTo({
					url: link,
					fail: (res) => {
						uni.showToast({
							icon: 'none',
							title: '未配置该模块'
						})
					}
				});
			}
		});

	} else if (link.startsWith('https://') || link.startsWith('http://')) {
		link = htmlRestore(link);
		uni.navigateTo({
			url: '/pages/base/webview/webview?src=' + encodeURIComponent(link),
			fail(res) {
				uni.redirectTo({
					url: '/pages/base/webview/webview?src=' + encodeURIComponent(link)
				});
			}
		});
	} else {
		// #ifdef MP-WEIXIN
		if (link.startsWith('plugin-private://wx34345ae5855f892d') ||
			link.startsWith('plugin-private://wx2b03c6e691cd7370') ||
			link.startsWith('plugin-private://wx8c873f830774d652')) {
			link = htmlRestore(link);
			uni.navigateTo({
				url: link,
				fail(res) {
					uni.redirectTo({
						url: link
					});
				}
			})
			return;
		} else if (link.startsWith('appid:')) {
			let appid = '';
			let page = '';
			let index = link.indexOf(';page:');
			if (index < 0) {
				appid = link.substring('appid:'.length);
			} else {
				appid = link.substring('appid:'.length, index);
				page = link.substring(index + ';page:'.length);
				page = htmlRestore(page);
			}
			let params = {
				appId: appid,
				fail: res => {
					// uni.setClipboardData({
					// 	data: link
					// });
					if (res.errMsg && res.errMsg.indexOf('cancel') < 0) {
						Alert.toast(res.errMsg)
					}
				}
			};
			if (page != '') {
				params.path = page;
			}

			uni.navigateToMiniProgram(params);
			return;
		} else if (link.startsWith('finder:')) {
			let finder = '';
			let feedId = '';
			let index = link.indexOf(';feedId:');
			if (index < 0) {
				finder = link.substring('finder:'.length);
			} else {
				finder = link.substring('finder:'.length, index);
				feedId = link.substring(index + ';feedId:'.length);
			}

			let params = {
				finderUserName: finder,
				fail: res => {
					// uni.setClipboardData({
					// 	data: link
					// });
					if (res.errMsg && res.errMsg.indexOf('cancel') < 0) {
						Alert.toast(res.errMsg)
					}
				}
			};

			if (feedId != '') {
				// params.feedId = feedId;
				// wx.openChannelsActivity(params);
				uni.navigateTo({
					url: '/pages/base/channel_video/channel_video?id=' + feedId + '&name=' + finder,
					fail(res) {
						uni.redirectTo({
							url: '/pages/base/channel_video/channel_video?id=' + feedId + '&name=' +
								finder
						});
					}
				});
			} else {
				wx.openChannelsUserProfile(params);
			}

			return;
		}
		// #endif

		// #ifdef H5
		Alert.toast(link);
		// #endif

		// #ifndef H5
		uni.setClipboardData({
			data: link
		});
		// #endif
	}
}

/**
 * 是否弹窗
 */
function getPopAd(pop_ad, key) {
	if (!pop_ad) {
		return false;
	}

	let lastTime = wx.getStorageSync(key);
	if (!lastTime) {
		lastTime = 0;
	}

	let now = new Date().getTime();
	if ((now - lastTime) > pop_ad.interval * 3600000) {
		return pop_ad;
	}

	return false;
}

/**
 * 判断是否是手机号
 */
function isMobile(mobile) {
	return (/^1[3456789]\d{9}$/.test(mobile));
}

/**
 * 检查是否已绑定了手机
 */
function checkMobile(tip) {
	let user = Auth.getUser();
	if (!user) {
		uni.navigateTo({
			url: '/pages/user/login/login',
		});
		return false;
	}

	if (!user.mobile) {
		let url = '/pages/user/login/login?type=mobile';
		if (tip) {
			url += '&tip=' + tip;
		}
		uni.navigateTo({
			url: url,
		});
		return false;
	}

	return true;
}

/**
 * 分享添加参数
 */
function addShareSource(path) {
	let user = Auth.getUser();
	if (!user || !user.user_id) {
		return;
	}

	return path + '&source=' + user.user_id;
}

/**
 * 分享加分
 */
function addShareScore(source) {
	if (!source) {
		return;
	}

	wx.setStorageSync(Constant.ZHUIGE_SOURCE_USER_ID, source);

	Rest.post(Api.URL('user', 'share_score'), {
		source: source,
	}).then(res => {
		console.log(res);
	})
}

/**
 * 设置【消息】tab的红点 因为微信小程序bug的原因，只有在tab页面设置才有效
 */
function setNoticeRedDot() {
	if (getApp().globalData.noticeRedDot) {
		uni.showTabBarRedDot({
			index: 3
		})
	} else {
		uni.hideTabBarRedDot({
			index: 3
		})
	}
}

/**
 * 设置购物车角标
 * 当购物车作为TAB页面使用时，需要放开
 * index 表示 tab 的位置，需按实际情况修改
 */
function updateCartBadge(count) {
	// if (count > 0) {
	// 	uni.setTabBarBadge({
	// 		index: 2,
	// 		text: '' + count,
	// 		fail(e) {
	// 			console.log(e);
	// 		}
	// 	})
	// } else {
	// 	uni.removeTabBarBadge({
	// 		index: 2
	// 	})
	// }
}

/**
 * 保存 购物车
 */
function saveCart(cart) {
	uni.setStorageSync('zhuige_wpmall_cart', cart);
}

/**
 * 读取 购物车
 */
function loadCart() {
	return uni.getStorageSync('zhuige_wpmall_cart');
}

module.exports = {
	navigateBack,

	htmlEncode,

	openLink,

	isMobile,
	checkMobile,

	addShareSource,
	addShareScore,

	getPopAd,

	setNoticeRedDot,

	updateCartBadge,

	saveCart,
	loadCart,
};