<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

?>
<span id="pwbe-dialog-content-select" class="pwbe-dialog-content" data-function="pwbeBulkEditorSelectHandler">
	<span class="pwbe-bulkedit-field-name"></span>:
	<p>
		&nbsp;&nbsp;&nbsp;&nbsp;<select class="pwbe-bulkedit-editor-select-field"></select>
	</p>
</span>
<script>

	function pwbeBulkEditorSelectHandler(action, oldValue) {
		var dialog = jQuery('#pwbe-dialog-content-select');
		var fieldName = dialog.attr('data-field-name');
		var dataField = dialog.attr('data-field');
		var select = dialog.find('.pwbe-bulkedit-editor-select-field');

		switch (action) {
			case 'init':
				dialog.find('.pwbe-bulkedit-field-name').text(fieldName);

				var options = jQuery('.pwbe-dropdown-template-' + dataField + ' option').clone();
				options.appendTo(select);

				select.focus();
			break;

			case 'apply':
				return select.val();
			break;

			case 'visibility':
				var selectedOption = select.children('option:selected');
				if (selectedOption.hasClass('pwbe-dropdown-visibility-variation')) {
					return 'variation';
				} else if (selectedOption.hasClass('pwbe-dropdown-visibility-parent')) {
					return 'parent';
				} else {
					return 'both';
				}

			break;

			case 'reset':
				select.empty();
			break;
		}
	}

</script>