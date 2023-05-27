(function ($) {
	"use strict";

	var GF_Widget_Areas = {
		init : function() {
			$('.gsf-sidebars-wrap .gsf-sidebars-remove-item').on('click', function () {
				var $this = $(this);
				if (confirm(gsf_widget_areas_variable.confirm_delete)) {
					var widget_name = $this.data('id');

					$.ajax({
						type: "POST",
						url: gsf_widget_areas_variable.ajax_url,
						data: {
							name: widget_name
						},
						success: function (response) {
							if (response.trim() == 'widget-area-deleted') {
								$this.closest('tr').slideUp(200).remove();
							}
						}
					});
				}
			});
		}
	};

	$(function () {
		GF_Widget_Areas.init();
	});
})(jQuery);
