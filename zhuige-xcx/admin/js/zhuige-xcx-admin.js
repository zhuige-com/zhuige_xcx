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
		$('body').on('click', '.button-zhuige-xcx-update', () => {
			layer.msg('升级中~')
            $.post("/wp-admin/admin-ajax.php",
                {
                    action: 'admin_zhuige_xcx_update'
                },
                function (res, status) {
                    layer.closeAll();

                    if (status != 'success' || !res.success) {
                        layer.msg('系统异常');
                        return;
                    }

                    if (res.data.code == 0) {
                        layer.msg(res.data.msg);
                        return;
                    }

                    window.location.reload();
                }
            );
		})
	});

})(jQuery);
