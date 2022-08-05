const Auth = require("./auth.js");

/**
 * request封装
 */
function request(url, data = {}, method = "GET") {
	return new Promise(function(resolve, reject) {
		uni.showLoading({
			title: '加载中'
		});

		data.token = Auth.getToken();
		
		if (method == "GET") {
			data.t = new Date().getTime();
			data.r = Math.floor(Math.random() * 10000);
		}
		
		// #ifdef MP-WEIXIN
		data.os = 'wx';
		// #endif
		
		// #ifdef MP-BAIDU
		data.os = 'bd';
		// #endif
		
		// #ifdef MP-QQ
		data.os = 'qq';
		// #endif
		
		uni.request({
			url: url,
			data: data,
			method: method,
			success(res) {
				if (res.statusCode == 500 && res.data.code == 'user_not_login') {
					uni.navigateTo({
					    url: '/pages/user/login/login',
					});
					return;
				}
				
				if (res.statusCode != 200) {
					reject(res.errMsg);
					return;
				}

				if (res.data.code == 'user_not_login') {
					uni.navigateTo({
					    url: '/pages/user/login/login',
					});
					return;
				} else if (res.data.code == 'require_mobile') {
					uni.navigateTo({
					    url: '/pages/user/login/login?type=mobile',
					});
					return;
				}
				
				resolve(res.data);
			},
			fail(err) {
				reject(err);
			},
			complete() {
				uni.hideLoading();
			}
		});
	});
}

/**
 * 上传图片
 */
function upload(url, path, data = {}) {
    return new Promise(function (resolve, reject) {
        uni.showLoading({
            title: '上传中……',
        })

        data.token = Auth.getToken();
		
		// #ifdef MP-WEIXIN
		data.os = 'wx';
		// #endif
		
		// #ifdef MP-BAIDU
		data.os = 'bd';
		// #endif
		
		// #ifdef MP-QQ
		data.os = 'qq';
		// #endif
        
        uni.uploadFile({
            url: url,
            filePath: path,
            name: 'image',
            formData: data,
            success(res) {
                if (res.statusCode != 200) {
                    reject(res.errMsg);
                    return;
                }

				let data = undefined;
				if (res.data instanceof String || (typeof res.data).toLowerCase() == 'string') {
					data = JSON.parse(res.data);
				} else {
					data = res.data;
				}
                
                if (data.code == -1) { //尚未登录
                    uni.navigateTo({
                        url: '/pages/user/login/login',
                    });
                    return;
                }

                resolve(data);
            }, 
			fail(err) {
				console.log(err)
			},
			complete() {
				uni.hideLoading();
			}
        })
    });
}

/**
 * get请求
 */
function get(url, data = {}) {
	return request(url, data, 'GET');
}

/**
 * post请求
 */
function post(url, data = {}) {
	return request(url, data, 'POST');
}

module.exports = {
	get,
	post,
	upload,
};