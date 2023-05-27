<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

?>
<span id="pwbe-dialog-content-checkbox" class="pwbe-dialog-content" data-function="pwbeBulkEditorCheckboxHandler">
	<span class="pwbe-bulkedit-field-name"></span>?
	<p>
		&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="pwbe-bulkedit-editor-checkbox-field" class="pwbe-bulkedit-editor-checkbox-field" /><label for="pwbe-bulkedit-editor-checkbox-field"><?php _e( 'Yes', 'pw-bulk-edit' ); ?></label>
	</p>
</span>
<script>

	function pwbeBulkEditorCheckboxHandler(action, oldValue) {
		var dialog = jQuery('#pwbe-dialog-content-checkbox');
		var fieldName = dialog.attr('data-field-name');
		var checkbox = dialog.find('.pwbe-bulkedit-editor-checkbox-field');

		switch (action) {
			case 'init':
				dialog.find('.pwbe-bulkedit-field-name').text(fieldName);
			break;

			case 'apply':
				if (checkbox.prop('checked')) {
					return 'yes';
				} else {
					return 'no';
				}
			break;

			case 'reset':
				checkbox.prop('checked', false);
			break;
		}
	}

</script>