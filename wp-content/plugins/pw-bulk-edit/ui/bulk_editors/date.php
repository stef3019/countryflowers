<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

?>
<span id="pwbe-dialog-content-date" class="pwbe-dialog-content" data-function="pwbeBulkEditorDateHandler">
	<fieldset class="pwbe-bulkedit-editor-date-mode">
		<input type="radio" value="fixed" name="pwbe-bulkedit-editor-date-mode" id="pwbe-bulkedit-editor-date-mode-fixed" /> <label for="pwbe-bulkedit-editor-date-mode-fixed"><?php _e( 'Set to fixed date', 'pw-bulk-edit' ); ?></label><br />
		<input type="radio" value="increase" name="pwbe-bulkedit-editor-date-mode" id="pwbe-bulkedit-editor-date-mode-increase" /> <label for="pwbe-bulkedit-editor-date-mode-increase"><?php _e( 'Add days', 'pw-bulk-edit' ); ?></label><br />
		<input type="radio" value="decrease" name="pwbe-bulkedit-editor-date-mode" id="pwbe-bulkedit-editor-date-mode-decrease" /> <label for="pwbe-bulkedit-editor-date-mode-decrease"><?php _e( 'Remove days', 'pw-bulk-edit' ); ?></label><br />
		<input type="radio" value="clear" name="pwbe-bulkedit-editor-date-mode" id="pwbe-bulkedit-editor-date-mode-clear" /> <label for="pwbe-bulkedit-editor-date-mode-clear"><?php _e( 'Clear value (set to n/a)', 'pw-bulk-edit' ); ?></label><br />
	</fieldset>
	<div class="pwbe-bulkedit-editor-mode-container">
		<div class="form-field">
			<div class="pwbe-bulkedit-details pwbe-bulkedit-details-fixed">
				<label for="pwbe-bulkedit-fixed-date-value"><?php printf( __( 'Set %s to the following date:', 'pw-bulk-edit' ), '<span class="pwbe-bulkedit-field-name"></span>' ); ?></label><br />
				<input type="date" id="pwbe-bulkedit-fixed-date-value" name="pwbe-bulkedit-fixed-date-value" />
			</div>

			<div class="pwbe-bulkedit-details pwbe-bulkedit-details-increase">
				<label for="pwbe-dialog-content-date-increase"><?php _e( 'Add one or more days to ', 'pw-bulk-edit' ); ?><span class="pwbe-bulkedit-field-name"></span>:</label><br />
				<input id="pwbe-dialog-content-date-increase" name="pwbe-dialog-content-date-increase" /><br />
			</div>

			<div class="pwbe-bulkedit-details pwbe-bulkedit-details-decrease">
				<label for="pwbe-dialog-content-date-decrease"><?php _e( 'Remove one or more days from ', 'pw-bulk-edit' ); ?><span class="pwbe-bulkedit-field-name"></span>:</label><br />
				<input id="pwbe-dialog-content-date-decrease" name="pwbe-dialog-content-date-decrease" />
			</div>

			<div class="pwbe-bulkedit-details pwbe-bulkedit-details-clear">
				<label for="pwbe-bulkedit-clear"><?php printf( __( 'Clear the value of %s and set it to empty (n/a).', 'pw-bulk-edit' ), '<span class="pwbe-bulkedit-field-name"></span>' ); ?></label>
			</div>

		</div>
	</div>
</span>
<style>
	#pwbe-dialog-content-date {
		white-space: nowrap;
	}

	.pwbe-bulkedit-editor-date-mode, .pwbe-bulkedit-editor-mode-container {
		display: inline-block;
		vertical-align: top;
	}

	.pwbe-bulkedit-editor-mode-container {
		padding-left: 30px;
		min-width: 300px;
	}

	.pwbe-bulkedit-details {
		display: none;
	}
</style>
<script>

	jQuery(function() {
		jQuery('.pwbe-bulkedit-editor-date-mode').find('input[type=radio]').change(function() {
			var dialog = jQuery('#pwbe-dialog-content-date');
			var details = dialog.find('.pwbe-bulkedit-details-' + jQuery(this).val());
			dialog.find('.pwbe-bulkedit-details').hide();
			details.show().find('input:first').focus();
		});
	});

	function pwbeBulkEditorDateHandler(action, oldValue) {
		var dialog = jQuery('#pwbe-dialog-content-date');
		var fieldName = dialog.attr('data-field-name');
		var fixed = dialog.find('#pwbe-bulkedit-fixed-date-value');
		var increase = dialog.find('#pwbe-dialog-content-date-increase');
		var decrease = dialog.find('#pwbe-dialog-content-date-decrease');
		var mode = dialog.find('input[name=pwbe-bulkedit-editor-date-mode]:checked').first();

		switch (action) {
			case 'init':
				dialog.find('.pwbe-bulkedit-field-name').text(fieldName);
			break;

			case 'apply':
				if (!mode.val()) {
					return oldValue;
				}

				if (!oldValue) {
					oldValue = new Date();
				} else {
					var parts = oldValue.split('-');
					oldValue = new Date(parts[0], parts[1]-1, parts[2]);
				}

				var newValue = null;

				switch (mode.val()) {
					case 'fixed':
						if (fixed.val()) {
							newValue = fixed.val();
						}
					break;

					case 'increase':
					case 'decrease':
						var days = 0;

						if (increase.val()) {
							days = parseInt(increase.val());
						} else if (decrease.val()) {
							days = parseInt(decrease.val()) * -1;
						}

						newValue = pwbeAddDays(oldValue, days).toISOString().slice(0, 10);
					break;

					case 'clear':
						newValue = '';
					break;
				}

				return newValue;
			break;

			case 'reset':
				mode.prop('checked', false);
				fixed.val('');
				increase.val('');
				decrease.val('');
				dialog.find('.pwbe-bulkedit-details').hide();
			break;
		}
	}

	// Source: http://stackoverflow.com/questions/563406/add-days-to-javascript-date
	function pwbeAddDays(date, days) {
		var result = new Date(date);
		result.setDate(result.getDate() + days);
		return result;
	}

	// Source: http://stackoverflow.com/questions/10073699/pad-a-number-with-leading-zeros-in-javascript
	function pwbePad(n, width, z) {
		z = z || '0';
		n = n + '';
		return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
	}

</script>