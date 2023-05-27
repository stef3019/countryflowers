<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

?>
<span id="pwbe-dialog-content-multiselect" class="pwbe-dialog-content" data-function="pwbeBulkEditorMultiselectHandler">
	<fieldset class="pwbe-bulkedit-editor-multiselect-mode">
		<input type="radio" value="add" name="pwbe-bulkedit-editor-multiselect-mode" id="pwbe-bulkedit-editor-multiselect-mode-add" /> <label for="pwbe-bulkedit-editor-multiselect-mode-add"><?php _e( 'Add to ', 'pw-bulk-edit' ); ?><span class="pwbe-bulkedit-field-name"></span></label><br />
		<input type="radio" value="remove" name="pwbe-bulkedit-editor-multiselect-mode" id="pwbe-bulkedit-editor-multiselect-mode-remove" /> <label for="pwbe-bulkedit-editor-multiselect-mode-remove"><?php _e( 'Remove from ', 'pw-bulk-edit' ); ?><span class="pwbe-bulkedit-field-name"></span></label><br />
		<input type="radio" value="clear" name="pwbe-bulkedit-editor-multiselect-mode" id="pwbe-bulkedit-editor-multiselect-mode-clear" /> <label for="pwbe-bulkedit-editor-multiselect-mode-clear"><?php printf( __( 'Clear %s (remove all)', 'pw-bulk-edit' ), '<span class="pwbe-bulkedit-field-name"></span>' ); ?></label><br />
	</fieldset>
	<div class="pwbe-bulkedit-editor-mode-container">
		<div class="form-field">
			<div class="pwbe-bulkedit-details" style="max-height: 300px; overflow: scroll;">
				<select class="pwbe-bulkedit-multiselect" multiple="multiple"></select>
				<div style="margin-top: 4px;">
					<button id="pwbe-bulkedit-editor-select-all" class="button"><?php _e( 'Select all', 'pw-bulk-edit' ); ?></button>
					<button id="pwbe-bulkedit-editor-select-none" class="button"><?php _e( 'Select none', 'pw-bulk-edit' ); ?></button>
				</div>
			</div>
		</div>
	</div>
</span>
<style>
	#pwbe-dialog-content-multiselect {
		white-space: nowrap;
	}

	.pwbe-bulkedit-editor-multiselect-mode, .pwbe-bulkedit-editor-mode-container {
		display: inline-block;
		vertical-align: top;
	}

	.pwbe-bulkedit-editor-mode-container {
		padding-left: 30px;
		min-width: 300px;
	}

	.pwbe-bulkedit-multiselect {
		max-width: 400px;
		min-width: 300px;
	}

	.pwbe-bulkedit-details {
		display: none;
	}
</style>
<script>

	jQuery(function() {
		jQuery('.pwbe-bulkedit-editor-multiselect-mode').find('input[type=radio]').change(function() {
			var dialog = jQuery('#pwbe-dialog-content-multiselect');
			var fieldName = dialog.attr('data-field-name');
			var dataField = dialog.attr('data-field');
			var details = dialog.find('.pwbe-bulkedit-details');
			var select = details.find('.pwbe-bulkedit-multiselect');

			details.show();

			if (select.children('option').length == 0) {
				jQuery('.pwbe-dropdown-template-' + dataField + ' option').clone().appendTo(select);
				<?php
					if ( defined( 'PWBE_PASTE_INTO_DROPDOWNS' ) && true === PWBE_PASTE_INTO_DROPDOWNS ) {
						?>
						select.pwbeselect2({ placeholder: '<?php _e( 'Select ', 'pw-bulk-edit' ); ?>' + fieldName + '...', tokenSeparators: [','], tags: true, multiple: true });
						<?php
					} else {
						?>
						select.pwbeselect2({ placeholder: '<?php _e( 'Select ', 'pw-bulk-edit' ); ?>' + fieldName + '...' });
						<?php
					}
				?>
			}

			select.focus();

			if (jQuery(this).val() == 'clear') {
				details.hide();
			}
		});

		jQuery('#pwbe-bulkedit-editor-select-all').click(function() {
			pwbeSelectToggle(true);
			return false;
		});

		jQuery('#pwbe-bulkedit-editor-select-none').click(function() {
			pwbeSelectToggle(false);
			return false;
		});
	});

	function pwbeSelectToggle(selected) {
		var dialog = jQuery('#pwbe-dialog-content-multiselect');
		var details = dialog.find('.pwbe-bulkedit-details');
		var select = details.find('.pwbe-bulkedit-multiselect');

		if (selected) {
			select.find('option').attr('selected','selected');
		} else {
			select.find('option').removeAttr('selected');
		}
		select.change();
	}

	function pwbeBulkEditorMultiselectHandler(action, oldValue) {
		var dialog = jQuery('#pwbe-dialog-content-multiselect');
		var fieldName = dialog.attr('data-field-name');
		var multiselect = dialog.find('.pwbe-bulkedit-multiselect');
		var mode = dialog.find('input[name=pwbe-bulkedit-editor-multiselect-mode]:checked').first();

		switch (action) {
			case 'init':
				dialog.find('.pwbe-bulkedit-field-name').text(fieldName);
			break;

			case 'apply':
				if (!mode.val()) {
					return oldValue;
				}

				if (mode.val() != 'clear' && !multiselect.val()) {
					return oldValue;
				}

				var oldValues = [];
				if (oldValue) { oldValues = String(oldValue).split(','); }
				var newValues = oldValues;

				switch (mode.val()) {
					case 'add':
						newValues = pwbeArrayUnique(oldValues.concat(multiselect.val()));
					break;

					case 'remove':
						for(var i = 0; i < multiselect.val().length; i++ ) {
							var removeItem = multiselect.val()[i];
							newValues = jQuery.grep(newValues, function(value) {
								return value != removeItem;
							});
						}
					break;

					case 'clear':
						newValues = [];
					break;
				}

				return newValues.join();
			break;

			case 'reset':
				mode.prop('checked', false);
				multiselect.empty();
				dialog.find('.pwbe-bulkedit-details').hide();
			break;
		}
	}

	// Source: http://stackoverflow.com/questions/1584370/how-to-merge-two-arrays-in-javascript-and-de-duplicate-items
	function pwbeArrayUnique(array) {
		var a = array.concat();
		for(var i=0; i<a.length; ++i) {
			for(var j=i+1; j<a.length; ++j) {
				if(a[i] === a[j]) {
					a.splice(j--, 1);
				}
			}
		}

		return a;
	}

</script>