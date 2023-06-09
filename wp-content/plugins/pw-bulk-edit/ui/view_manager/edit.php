<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

?>
<span id="pwbe-dialog-content-edit-view" class="pwbe-dialog-content" style="padding: 12px;" data-function="pwbeViewManagerEditHandler">
	<div id="pwbe-dialog-content-edit-view-loading" class="pwbe-heading"><?php _e( 'Loading...', 'pw-bulk-edit' ); ?></div>
	<div id="pwbe-dialog-content-edit-view-content">
		<div id="pwbe-dialog-content-edit-view-name" class="pwbe-heading"></div>
		<small>[<a href="#" id="pwbe-dialog-content-edit-view-rename-button"><?php _e( 'Rename', 'pw-bulk-edit' ); ?></a>]</small>

		<div id="pwbe-dialog-content-edit-view-hidden-columns-heading" class="pwbe-heading" style="margin-top: 15px;"><?php _e( 'Hidden Columns', 'pw-bulk-edit' ); ?></div>
		<div id="pwbe-dialog-content-edit-view-hidden-columns-subhead" class="pwbe-subheading"><?php _e( 'Click to show the column again.', 'pw-bulk-edit' ); ?></div>
		<div id="pwbe-dialog-content-edit-view-hidden-columns" style="max-height: 400px; overflow-y: auto;"></div>
	</div>
</span>
<script>

	jQuery(document).ready(function() {
		jQuery('#pwbe-dialog-content-edit-view-rename-button').click(function(e) {
			var oldViewName = jQuery('#pwbe-view').val();
			var newViewName = prompt('<?php _e( 'New Name', 'pw-bulk-edit' ); ?>', oldViewName);
			if (newViewName) {
				jQuery('body').css('cursor', 'wait');

				jQuery.post(ajaxurl, {
					'action': 'pwbe_delete_view',
					'name': newViewName
				}, function(data) {
					jQuery('#pwbe-view option').filter(function() { return this.value == oldViewName; }).remove();

					pwbeSaveCurrentView(newViewName);
					jQuery('#pwbe-dialog-content-edit-view-name').text(newViewName);

					jQuery('body').css('cursor', 'default');
				});
			}
			e.preventDefault();
			return false;
		});
	});

	function pwbeViewManagerEditHandler(action, oldValue) {
		var dialog = jQuery('#pwbe-dialog-content-edit-view');
		var loading = dialog.find('#pwbe-dialog-content-edit-view-loading');
		var content = dialog.find('#pwbe-dialog-content-edit-view-content');

		switch (action) {
			case 'init':
				dialog.find('#pwbe-dialog-content-edit-view-name').text(jQuery('#pwbe-view').val());
				loading.show();
				content.hide();

				jQuery.post(ajaxurl, {'action': 'pwbe_get_view', 'name': jQuery('#pwbe-view').val()}, function(results) {
					hiddenColumns = '<div class="pwbe-table pwbe-dialog-content-open-table">';
					hiddenColumns += '	<div class="pwbe-tbody">';

					for (var i = 0; i < results.length; i++) {
						var columnName = jQuery('#pwbe-header-results .pwbe-results-table-header-td').find('[data-field="' + results[i] + '"]').text();

						hiddenColumns += '<div class="pwbe-tr pwbe-dialog-content-open-tr" data-field="' + results[i] + '">';
						hiddenColumns += '	<div class="pwbe-td pwbe-dialog-content-open-td pwbe-dialog-content-open-icon-td">';
						hiddenColumns += '		<i class="fa fa-eye-slash fa-fw pwbe-dialog-content-open-td" aria-hidden="true"></i>';
						hiddenColumns += '	</div>';
						hiddenColumns += '	<div class="pwbe-td pwbe-dialog-content-open-td">';
						hiddenColumns += '		' + columnName;
						hiddenColumns += '	</div>';
						hiddenColumns += '</div>';
					}

					hiddenColumns += '	</div>';
					hiddenColumns += '</div>';

					dialog.find('#pwbe-dialog-content-edit-view-hidden-columns').html(hiddenColumns);
					loading.hide();
					content.show();

					if (results.length > 0) {
						dialog.find('#pwbe-dialog-content-edit-view-hidden-columns-subhead').text('<?php _e( 'Click to show the column again.', 'pw-bulk-edit' ); ?>');
					} else {
						dialog.find('#pwbe-dialog-content-edit-view-hidden-columns-subhead').text('<?php _e( 'All columns are visible.', 'pw-bulk-edit' ); ?>');
					}

					dialog.find('.pwbe-dialog-content-open-icon-td i').not('.fa-eye-slash').hide();

					dialog.find('.pwbe-dialog-content-open-td').click(function(e) {
						e.stopPropagation();

						var row = jQuery(this).closest('.pwbe-tr');
						var dataField = row.attr('data-field');

						jQuery('.pwbe-hidden-column').filter(function() { return jQuery(this).attr('data-field') == dataField; }).removeClass('pwbe-hidden-column');

						pwbeResizeFixedHeaderColumns();

						pwbeSaveCurrentView(jQuery('#pwbe-view').val());

						row.remove();

						if (dialog.find('.pwbe-dialog-content-open-tr').length == 0) {
							dialog.find('#pwbe-dialog-content-edit-view-hidden-columns-subhead').text('<?php _e( 'All columns are visible.', 'pw-bulk-edit' ); ?>');
						}
					});

					jQuery('.pwbe-dialog-content-open-tr').hover(
						function() {
							jQuery(this).find('.fa-eye-slash').removeClass('fa-eye-slash').addClass('fa-eye');
							jQuery(this).find('.pwbe-dialog-content-open-icon-td i').show();
						},
						function() {
							jQuery(this).find('.fa-eye').removeClass('fa-eye').addClass('fa-eye-slash');
							jQuery(this).find('.pwbe-dialog-content-open-icon-td i').not('.fa-eye-slash').hide();
						}
					);
				});

			break;

			case 'reset':
		        jQuery('#pwbe-filters-form').submit();
			break;
		}
	}

</script>