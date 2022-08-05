/**
 * 修改成你的域名
 */
const YOUR_DOMIAN = 'q.zhuige.com';

/**
 * 拼接url
 */
function URL(module, action) {
	return `https://${YOUR_DOMIAN}/wp-json/zhuige/${module}/${action}`;
}

module.exports = {
	URL
};
