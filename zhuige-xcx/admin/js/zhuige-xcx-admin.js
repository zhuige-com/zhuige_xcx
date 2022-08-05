(function ($) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(function () {
		//激活追格插件
		$('.btn-zhuige-addon-active').click(function () {
			$.post("/wp-admin/admin-ajax.php",
				{
					action: 'admin_active_addon',
					addon: $(this).data('addon')
				},
				function (res, status) {
					window.location.reload();
				});
		});

		//禁用追格插件
		$('.btn-zhuige-addon-deactive').click(function () {
			$.post("/wp-admin/admin-ajax.php",
				{
					action: 'admin_deactive_addon',
					addon: $(this).data('addon')
				},
				function (res, status) {
					window.location.reload();
				});
		});
	});

})(jQuery);
