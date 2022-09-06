/** 
 *  
 * jQuery scrollQ plugin li上下滚动插件
 * @name jquery-scrollQ.js 
 * @author Q 
 * @date 2012-03-23 
 * line 显示li行数 
 * scrollNum 每次滚动li行数
 * scrollTime 滚动速度 单位毫秒
 * 
 */
(function ($) {
	var status = false;
	$.fn.scrollQ = function (options) {
		var defaults = {
			line: 4,
			scrollNum: 2,
			scrollTime: 1000
		}
		var options = jQuery.extend(defaults, options);
		var _self = this;
		return this.each(function () {
			$("li", this).each(function () {
				$(this).css("display", "none");
			})
			$("li:lt(" + options.line + ")", this).each(function () {
				$(this).css("display", "block");
			})
			function scroll() {
				for (i = 0; i < options.scrollNum; i++) {
					var start = $("li:first", _self);
					start.fadeOut(100);
					start.css("display", "none");
					start.appendTo(_self);
					$("li:eq(" + (options.line - 1) + ")", _self).each(function () {
						$(this).fadeIn(500);
						$(this).css("display", "block");
					})
				}
			}
			var timer;
			timer = setInterval(scroll, options.scrollTime);
			_self.bind("mouseover", function () {
				clearInterval(timer);
			});
			_self.bind("mouseout", function () {
				timer = setInterval(scroll, options.scrollTime);
			});

		});
	}
})(jQuery); 