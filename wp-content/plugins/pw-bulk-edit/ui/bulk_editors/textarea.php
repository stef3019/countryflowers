<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

?>
<span id="pwbe-dialog-content-textarea" class="pwbe-dialog-content" data-function="pwbeBulkEditorTextareaHandler">
	<fieldset id="pwbe-bulkedit-textarea-mode">
		<input type="radio" value="replace" name="pwbe-bulkedit-textarea-mode" id="pwbe-bulkedit-textarea-mode-replace" /> <label for="pwbe-bulkedit-textarea-mode-replace"><?php _e( 'Search and replace', 'pw-bulk-edit' ); ?></label><br />
		<input type="radio" value="prepend" name="pwbe-bulkedit-textarea-mode" id="pwbe-bulkedit-textarea-mode-prepend" /> <label for="pwbe-bulkedit-textarea-mode-prepend"><?php _e( 'Add to the beginning', 'pw-bulk-edit' ); ?></label><br />
		<input type="radio" value="append" name="pwbe-bulkedit-textarea-mode" id="pwbe-bulkedit-textarea-mode-append" /> <label for="pwbe-bulkedit-textarea-mode-append"><?php _e( 'Add to the end', 'pw-bulk-edit' ); ?></label><br />
		<input type="radio" value="uppercase" name="pwbe-bulkedit-textarea-mode" id="pwbe-bulkedit-textarea-mode-uppercase" /> <label for="pwbe-bulkedit-textarea-mode-uppercase"><?php _e( 'ALL UPPERCASE', 'pw-bulk-edit' ); ?></label><br />
		<input type="radio" value="lowercase" name="pwbe-bulkedit-textarea-mode" id="pwbe-bulkedit-textarea-mode-lowercase" /> <label for="pwbe-bulkedit-textarea-mode-lowercase"><?php _e( 'all lowercase', 'pw-bulk-edit' ); ?></label><br />
		<input type="radio" value="propercase" name="pwbe-bulkedit-textarea-mode" id="pwbe-bulkedit-textarea-mode-propercase" /> <label for="pwbe-bulkedit-textarea-mode-propercase"><?php _e( 'Proper Case Words', 'pw-bulk-edit' ); ?></label>
	</fieldset>
	<div id="pwbe-bulkedit-details-textarea-container">
		<div class="form-field">
			<div class="pwbe-bulkedit-details pwbe-bulkedit-details-replace">
				<label for="pwbe-bulkedit-search-textarea"><?php printf( __( 'Search for this text anywhere in %s:', 'pw-bulk-edit' ), '<span class="pwbe-bulkedit-field-name"></span>' ); ?></label><br />
				<textarea id="pwbe-bulkedit-search-textarea" placeholder="<?php _e( 'Leave blank to replace the entire value.', 'pw-bulk-edit' ); ?>"></textarea><br />

				<label for="pwbe-bulkedit-replace-textarea"><?php _e( 'Replace it with this text:', 'pw-bulk-edit' ); ?></label><br />
				<textarea id="pwbe-bulkedit-replace-textarea"></textarea><br />
				<input type="checkbox" id="pwbe-bulkedit-replace-case-sensitive-textarea" /><label for="pwbe-bulkedit-replace-case-sensitive-textarea"> <?php _e( 'Case Sensitive', 'pw-bulk-edit' ); ?></label><br />
				<input type="checkbox" id="pwbe-bulkedit-replace-regex-textarea" /><label for="pwbe-bulkedit-replace-regex-textarea"> <?php _e( 'Use Regular Expressions', 'pw-bulk-edit' ); ?></label>
			</div>
			<div class="pwbe-bulkedit-details pwbe-bulkedit-details-prepend">
				<label for="pwbe-bulkedit-prepend-textarea"><?php printf( __( 'Prepend this text to the beginning of %s:', 'pw-bulk-edit' ), '<span class="pwbe-bulkedit-field-name"></span>' ); ?></label><br />
				<textarea id="pwbe-bulkedit-prepend-textarea"></textarea>
			</div>
			<div class="pwbe-bulkedit-details pwbe-bulkedit-details-append">
				<label for="pwbe-bulkedit-append-textarea"><?php printf( __( 'Append this text to the end of %s:', 'pw-bulk-edit' ), '<span class="pwbe-bulkedit-field-name"></span>' ); ?></label><br />
				<textarea id="pwbe-bulkedit-append-textarea"></textarea>
			</div>
			<div class="pwbe-bulkedit-details pwbe-bulkedit-details-uppercase">
				<?php _e( 'All words will be changed to uppercase.', 'pw-bulk-edit' ); ?>
				<p><strong><?php _e( 'Mary had a little LAMB &rarr; MARY HAD A LITTLE LAMB', 'pw-bulk-edit' ); ?></strong></p>
			</div>
			<div class="pwbe-bulkedit-details pwbe-bulkedit-details-lowercase">
				<?php _e( 'All words will be changed to lowercase.', 'pw-bulk-edit' ); ?>
				<p><strong><?php _e( 'Mary had a little LAMB &rarr; mary had a little lamb', 'pw-bulk-edit' ); ?></strong></p>
			</div>
			<div class="pwbe-bulkedit-details pwbe-bulkedit-details-propercase">
				<?php _e( 'The first letter of every word will be capitalized.', 'pw-bulk-edit' ); ?>
				<p><strong><?php _e( 'Mary had a little LAMB &rarr; Mary Had A Little Lamb', 'pw-bulk-edit' ); ?></strong></p>
			</div>
		</div>
	</div>
</span>
<style>
	#pwbe-dialog-content-textarea {
		white-space: nowrap;
	}

	#pwbe-bulkedit-textarea-mode, #pwbe-bulkedit-details-textarea-container {
		display: inline-block;
		vertical-align: top;
	}

	#pwbe-bulkedit-details-textarea-container {
		padding-left: 30px;
		min-width: 400px;
	}

	#pwbe-bulkedit-replace-textarea {
		margin-bottom: 20px;
	}

	.pwbe-bulkedit-details {
		display: none;
	}
</style>
<script>

	jQuery(function() {
		jQuery('#pwbe-dialog-content-textarea').find('input[type=radio][name=pwbe-bulkedit-textarea-mode]').change(function() {
			var dialog = jQuery('#pwbe-dialog-content-textarea');
			var details = dialog.find('.pwbe-bulkedit-details-' + jQuery(this).val()).first();

			dialog.find('.pwbe-bulkedit-details').hide();
			details.show().find('input:first').focus();
		});

		jQuery('#pwbe-dialog-content-textarea').find('#pwbe-bulkedit-prepend-textarea, #pwbe-bulkedit-append-textarea').keydown(function(e) {
			if (e.keyCode == 13) {
				jQuery('#pwbe-bulkedit-dialog-button-apply').trigger('click');
				e.preventDefault();
				return false;
			}
		});
	});

	function pwbeBulkEditorTextareaHandler(action, oldValue) {
		var dialog = jQuery('#pwbe-dialog-content-textarea');
		var fieldName = dialog.attr('data-field-name');
		var prepend = dialog.find('#pwbe-bulkedit-prepend-textarea');
		var append = dialog.find('#pwbe-bulkedit-append-textarea');
		var search = dialog.find('#pwbe-bulkedit-search-textarea');
		var replace = dialog.find('#pwbe-bulkedit-replace-textarea');
		var caseSensitive = dialog.find('#pwbe-bulkedit-replace-case-sensitive-textarea');
		var regEx = dialog.find('#pwbe-bulkedit-replace-regex-textarea');
		var mode = dialog.find('input[name=pwbe-bulkedit-textarea-mode]:checked').first();

		switch (action) {
			case 'init':
				dialog.find('.pwbe-bulkedit-field-name').text(fieldName);
			break;

			case 'apply':
				if (!mode.val()) {
					return oldValue;
				}

				var newValue = oldValue;
				if (!oldValue) { oldValue = ''; }

				switch (mode.val()) {
					case 'replace':
						if (search.val()) {
							if (caseSensitive.prop('checked')) {
								newValue = oldValue.replace(search.val(), replace.val());
							} else if (regEx.prop('checked')) {
								newValue = oldValue.replace(new RegExp(search.val()), replace.val());
							} else {
								newValue = oldValue.replace(new RegExp('(' + pwbePregQuote(search.val()) + ')', 'gi'), replace.val());
							}
						} else {
							newValue = replace.val();
						}
					break;

					case 'prepend':
						newValue = prepend.val() + oldValue;
					break;

					case 'append':
						newValue = oldValue + append.val();
					break;

					case 'uppercase':
						newValue = oldValue.toUpperCase();
					break;

					case 'lowercase':
						newValue = oldValue.toLowerCase();
					break;

					case 'propercase':
						newValue = oldValue.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
					break;
				}

				return newValue;
			break;

			case 'reset':
				mode.prop('checked', false);
				prepend.val('');
				append.val('');
				search.val('');
				replace.val('');
				caseSensitive.prop('checked', false);
				dialog.find('.pwbe-bulkedit-details').hide();
			break;
		}
	}

</script>