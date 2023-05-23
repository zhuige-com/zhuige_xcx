/**
 * 不熟悉uniapp和WordPress的新朋友，请仔细阅读文档
 * 99%的安装部署问题都可以在文档中找到答案
 * 文档: https://www.zhuige.com/docs/zg.html
 * 
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