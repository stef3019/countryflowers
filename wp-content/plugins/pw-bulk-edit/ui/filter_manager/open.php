<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

?>
<span id="pwbe-dialog-content-open" class="pwbe-dialog-content" data-function="pwbeFilterManagerOpenHandler">
	<h3><?php _e( 'Loading...', 'pw-bulk-edit' ); ?></h3>
</span>
<script>

	function pwbeFilterManagerOpenHandler(action, oldValue) {
		var dialog = jQuery('#pwbe-dialog-content-open');

		switch (action) {
			case 'init':
				jQuery.post(ajaxurl, {'action': 'pwbe_filter_manager', 'function': 'list'}, function(results) {
					var savedFilters = '<?php _e( 'You have no saved filters.', 'pw-bulk-edit' ); ?>';

					if (results.length > 0) {
						savedFilters = '<div class="pwbe-table pwbe-dialog-content-open-table">';
						savedFilters += '	<div class="pwbe-tbody">';

						for (var i = 0; i < results.length; i++) {
							var filter = results[i];

							savedFilters += '<div class="pwbe-tr pwbe-dialog-content-open-tr">';
							savedFilters += '	<div class="pwbe-td pwbe-dialog-content-open-td pwbe-dialog-content-open-icon-td">';
							savedFilters += '		<i class="fa fa-folder-o fa-fw pwbe-dialog-content-open-td" aria-hidden="true"></i>';
							savedFilters += '	</div>';
							savedFilters += '	<div class="pwbe-td pwbe-dialog-content-open-td">';
							savedFilters += '		<span class="pwbe-link pwbe-dialog-content-open-filter-link" data-post-id="' + filter.post_id + '" title="<?php _e( 'Open filter', 'pw-bulk-edit' ); ?>">' + filter.name + '</span>';
							savedFilters += '	</div>';
							savedFilters += '	<div class="pwbe-td pwbe-dialog-content-open-td pwbe-dialog-content-open-icon-td">';
							savedFilters += '		<span class="pwbe-link pwbe-dialog-content-rename-filter-link" data-post-id="' + filter.post_id + '" title="<?php _e( 'Rename', 'pw-bulk-edit' ); ?>"><i class="fa fa-pencil-square-o fa-fw"></i></span>';
							savedFilters += '	</div>';
							savedFilters += '	<div class="pwbe-td pwbe-dialog-content-open-td pwbe-dialog-content-open-icon-td">';
							savedFilters += '		<span class="pwbe-link pwbe-dialog-content-delete-filter-link" data-post-id="' + filter.post_id + '" title="<?php _e( 'Delete', 'pw-bulk-edit' ); ?>"><i class="fa fa-trash fa-fw"></i></span>';
							savedFilters += '	</div>';
							savedFilters += '</div>';
						}

						savedFilters += '	</div>';
						savedFilters += '</div>';
					}

					dialog.html(savedFilters);
					dialog.find('.pwbe-dialog-content-open-icon-td i').not('.fa-folder-o').hide();

					jQuery('.pwbe-dialog-content-open-td').click(function(e) {
						e.stopPropagation();

						var openLink = jQuery(this).closest('.pwbe-tr').find('.pwbe-dialog-content-open-filter-link:first');
						var postId = openLink.attr('data-post-id');
						var filterName = openLink.text();
						var span = jQuery(this).children('.pwbe-link');

						if (span && span.hasClass('pwbe-dialog-content-rename-filter-link')) {
							filterName = prompt('<?php _e( 'Filter name', 'pw-bulk-edit' ); ?>', filterName);
							if (filterName) {
								jQuery('body').css('cursor', 'wait');
								jQuery.post(ajaxurl, {
									'action': 'pwbe_filter_manager',
									'function': 'rename',
									'ID': postId,
									'name': filterName
								},
								function(data) {
									pwbeFilterManagerOpenHandler('init');
									jQuery('body').css('cursor', 'default');
								});
							}

						} else if (span && span.hasClass('pwbe-dialog-content-download-filter-link')) {

						} else if (span && span.hasClass('pwbe-dialog-content-delete-filter-link')) {
							if (confirm('<?php _e( 'Are you sure you want to delete this filter?', 'pw-bulk-edit' ); ?> ' + filterName)) {
								jQuery('body').css('cursor', 'wait');
								jQuery.post(ajaxurl, {
									'action': 'pwbe_filter_manager',
									'function': 'delete',
									'ID': postId
								},
								function(data) {
									pwbeFilterManagerOpenHandler('init');
									jQuery('body').css('cursor', 'default');
								});
							}

						} else {
							jQuery('#pwbe-filter-save').attr('data-filter-post-id', postId).attr('data-filter-name', filterName);

							jQuery('body').css('cursor', 'wait');
							jQuery.post(ajaxurl, {
								'action': 'pwbe_filter_manager',
								'function': 'open',
								'ID': postId
							},
							function(data) {
								pwbeFilterManagerDialogClose();
								pwbeLoadSavedFilters(data);
								jQuery('body').css('cursor', 'default');
							});
						}
					});

					jQuery('.pwbe-dialog-content-open-tr').hover(
						function() {
							jQuery(this).find('.fa-folder-o').removeClass('fa-folder-o').addClass('fa-folder-open-o');
							jQuery(this).find('.pwbe-dialog-content-open-icon-td i').show();
						},
						function() {
							jQuery(this).find('.fa-folder-open-o').removeClass('fa-folder-open-o').addClass('fa-folder-o');
							jQuery(this).find('.pwbe-dialog-content-open-icon-td i').not('.fa-folder-o').hide();
						}
					);
				});

			break;

			case 'apply':
			break;

			case 'reset':
				dialog.html('<h3><?php _e( 'Loading...', 'pw-bulk-edit' ); ?></h3>');
			break;
		}
	}

</script>