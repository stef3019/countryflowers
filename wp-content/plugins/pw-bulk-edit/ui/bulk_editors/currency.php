<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

?>
<span id="pwbe-dialog-content-currency" class="pwbe-dialog-content" data-function="pwbeBulkEditorCurrencyHandler">
	<fieldset id="pwbe-bulkedit-currency-mode">
		<input type="radio" value="fixed" name="pwbe-bulkedit-currency-mode" id="pwbe-bulkedit-currency-mode-fixed" /> <label for="pwbe-bulkedit-currency-mode-fixed"><?php _e( 'Set to specific amount', 'pw-bulk-edit' ); ?></label><br />
		<input type="radio" value="fixed-increase" name="pwbe-bulkedit-currency-mode" id="pwbe-bulkedit-currency-mode-fixed-increase" /> <label for="pwbe-bulkedit-currency-mode-fixed-increase"><?php _e( 'Increase by specific amount', 'pw-bulk-edit' ); ?></label><br />
		<input type="radio" value="fixed-decrease" name="pwbe-bulkedit-currency-mode" id="pwbe-bulkedit-currency-mode-fixed-decrease" /> <label for="pwbe-bulkedit-currency-mode-fixed-decrease"><?php _e( 'Decrease by specific amount', 'pw-bulk-edit' ); ?></label><br />
		<input type="radio" value="percentage-increase" name="pwbe-bulkedit-currency-mode" id="pwbe-bulkedit-currency-mode-percentage-increase" /> <label for="pwbe-bulkedit-currency-mode-percentage-increase"><?php _e( 'Increase by percentage', 'pw-bulk-edit' ); ?></label><br />
		<input type="radio" value="percentage-decrease" name="pwbe-bulkedit-currency-mode" id="pwbe-bulkedit-currency-mode-percentage-decrease" /> <label for="pwbe-bulkedit-currency-mode-percentage-decrease"><?php _e( 'Decrease by percentage', 'pw-bulk-edit' ); ?></label><br />
		<input type="radio" value="replace" name="pwbe-bulkedit-currency-mode" id="pwbe-bulkedit-currency-mode-replace" /> <label for="pwbe-bulkedit-currency-mode-replace"><?php _e( 'Search and replace', 'pw-bulk-edit' ); ?></label><br />
		<input type="radio" value="clear" name="pwbe-bulkedit-currency-mode" id="pwbe-bulkedit-currency-mode-clear" /> <label for="pwbe-bulkedit-currency-mode-clear"><?php _e( 'Clear value (set to n/a)', 'pw-bulk-edit' ); ?></label><br />
	</fieldset>
	<div id="pwbe-bulkedit-details-currency-container">
		<div>
			<div class="pwbe-bulkedit-details pwbe-bulkedit-details-fixed">
				<label for="pwbe-bulkedit-fixed-amount"><?php printf( __( 'Set %s to the following amount:', 'pw-bulk-edit' ), '<span class="pwbe-bulkedit-field-name"></span>' ); ?></label><br />
				<?php echo get_woocommerce_currency_symbol(); ?><input type="text" id="pwbe-bulkedit-fixed-amount" name="pwbe-bulkedit-fixed-amount" />
			</div>

			<div class="pwbe-bulkedit-details pwbe-bulkedit-details-fixed-increase">
				<label for="pwbe-bulkedit-fixed-increase-amount"><?php printf( __( 'Increase %s by the following amount:', 'pw-bulk-edit' ), '<span class="pwbe-bulkedit-field-name"></span>' ); ?></label><br />
				<?php echo get_woocommerce_currency_symbol(); ?><input type="text" id="pwbe-bulkedit-fixed-increase-amount" name="pwbe-bulkedit-fixed-increase-amount" /><br />
			</div>

			<div class="pwbe-bulkedit-details pwbe-bulkedit-details-fixed-decrease">
				<label for="pwbe-bulkedit-fixed-decrease-amount"><?php printf( __( 'Decrease %s by the following amount:', 'pw-bulk-edit' ), '<span class="pwbe-bulkedit-field-name"></span>' ); ?></label><br />
				<?php echo get_woocommerce_currency_symbol(); ?><input type="text" id="pwbe-bulkedit-fixed-decrease-amount" name="pwbe-bulkedit-fixed-decrease-amount" />
			</div>

			<div class="pwbe-bulkedit-details pwbe-bulkedit-details-percentage-increase">
				<label for="pwbe-bulkedit-percentage-increase-amount"><?php printf( __( 'Increase %s by the following percentage:', 'pw-bulk-edit' ), '<span class="pwbe-bulkedit-field-name"></span>' ); ?></label><br />
				<input type="text" id="pwbe-bulkedit-percentage-increase-amount" name="pwbe-bulkedit-percentage-increase-amount" />%
			</div>

			<div class="pwbe-bulkedit-details pwbe-bulkedit-details-percentage-decrease">
				<label for="pwbe-bulkedit-percentage-decrease-amount"><?php printf( __( 'Decrease %s by the following percentage:', 'pw-bulk-edit' ), '<span class="pwbe-bulkedit-field-name"></span>' ); ?></label><br />
				<input type="text" id="pwbe-bulkedit-percentage-decrease-amount" name="pwbe-bulkedit-percentage-decrease-amount" />%
			</div>

			<div class="pwbe-bulkedit-details pwbe-bulkedit-details-replace">
				<label for="pwbe-bulkedit-currency-search-text"><?php printf( __( 'Search for this text anywhere in %s:', 'pw-bulk-edit' ), '<span class="pwbe-bulkedit-field-name"></span>' ); ?></label><br />
				<input type="text" id="pwbe-bulkedit-currency-search-text" style="width: 95%;" placeholder="<?php _e( 'Leave blank to replace the entire value.', 'pw-bulk-edit' ); ?>" /><br />

				<label for="pwbe-bulkedit-currency-replace-text"><?php _e( 'Replace it with this text:', 'pw-bulk-edit' ); ?></label><br />
				<input type="text" id="pwbe-bulkedit-currency-replace-text" style="width: 95%;" /><br />
			</div>

			<div class="pwbe-bulkedit-details pwbe-bulkedit-details-clear">
				<label for="pwbe-bulkedit-clear"><?php printf( __( 'Clear the value of %s and set it to empty (n/a).', 'pw-bulk-edit' ), '<span class="pwbe-bulkedit-field-name"></span>' ); ?></label>
			</div>

			<p class="pwbe-bulkedit-editor-allow-negative-container">
				<input type="checkbox" class="pwbe-bulkedit-allow-negative" id="pwbe-bulkedit-allow-negative"><label for="pwbe-bulkedit-allow-negative"><?php _e( 'Allow amounts to be less than zero.', 'pw-bulk-edit' ); ?></label>
			</p>

			<p class="pwbe-bulkedit-editor-use-regular-price-container">
				<input type="checkbox" class="pwbe-bulkedit-use-regular-price" id="pwbe-bulkedit-use-regular-price"><label for="pwbe-bulkedit-use-regular-price"><?php _e( 'Use Regular Price for adjustment.', 'pw-bulk-edit' ); ?></label>
			</p>

			<p class="pwbe-bulkedit-editor-rounding-container">
				<label for="pwbe-bulkedit-rounding"><?php _e( 'Rounding precision', 'pw-bulk-edit' ); ?></label><br />
				<?php
					if ( function_exists( 'wc_get_price_decimals' ) ) {
						$price_decimals = wc_get_price_decimals();
					} else {
						$price_decimals = absint( apply_filters( 'wc_get_price_decimals', get_option( 'woocommerce_price_num_decimals', 2 ) ) );
					}
				?>
				<input type="text" id="pwbe-bulkedit-rounding" name="pwbe-bulkedit-rounding" value="<?php echo $price_decimals; ?>" />
			</p>
		</div>
	</div>
</span>
<style>
	#pwbe-dialog-content-currency {
		white-space: nowrap;
	}

	#pwbe-bulkedit-currency-mode, #pwbe-bulkedit-details-currency-container {
		display: inline-block;
		vertical-align: top;
	}

	#pwbe-bulkedit-details-currency-container {
		padding-left: 30px;
		min-width: 400px;
	}

	#pwbe-bulkedit-currency-replace-text {
		margin-bottom: 20px;
	}

	#pwbe-bulkedit-rounding {
		width: 40px;
	}

	.pwbe-bulkedit-details, .pwbe-bulkedit-editor-allow-negative-container, .pwbe-bulkedit-editor-use-regular-price-container, .pwbe-bulkedit-editor-rounding-container {
		display: none;
	}
</style>
<script>

	jQuery(function() {
		jQuery('#pwbe-bulkedit-currency-mode').find('input[type=radio]').change(function() {
			var mode = jQuery(this).val();
			var dialog = jQuery('#pwbe-dialog-content-currency');
			var details = dialog.find('.pwbe-bulkedit-details-' + mode);
			dialog.find('.pwbe-bulkedit-details').hide();

			switch (mode) {
				case 'fixed-increase':
				case 'fixed-decrease':
				case 'percentage-increase':
				case 'percentage-decrease':
					if (jQuery('.pwbe-bulkedit-field-name:first').text() == '<?php _e( 'Sale price', 'woocommerce' ); ?>') {
						jQuery('.pwbe-bulkedit-editor-use-regular-price-container').show();
					}
				break;

				default:
					jQuery('.pwbe-bulkedit-editor-use-regular-price-container').hide();
				break;
			}

			switch (mode) {
				case 'fixed-decrease':
				case 'percentage-decrease':
					jQuery('.pwbe-bulkedit-editor-allow-negative-container').show();
				break;

				default:
					jQuery('.pwbe-bulkedit-editor-allow-negative-container').hide();
				break;
			}

			jQuery('.pwbe-bulkedit-editor-rounding-container').toggle(mode == 'percentage-increase' || mode == 'percentage-decrease');

			details.show().find('input:first').focus();
		});

		jQuery('#pwbe-dialog-content-currency').find('#pwbe-bulkedit-fixed-amount, #pwbe-bulkedit-fixed-increase-amount, #pwbe-bulkedit-fixed-decrease-amount, #pwbe-bulkedit-percentage-increase-amount, #pwbe-bulkedit-percentage-decrease-amount, #pwbe-bulkedit-currency-search-text, #pwbe-bulkedit-currency-replace-text').keydown(function(e) {
			if (e.keyCode == 13) {
				jQuery('#pwbe-bulkedit-dialog-button-apply').trigger('click');
				e.preventDefault();
				return false;
			}
		});
	});

	function pwbeBulkEditorCurrencyHandler(action, oldValueString) {
		var dialog = jQuery('#pwbe-dialog-content-currency');
		var fieldName = dialog.attr('data-field-name');
		var fixed = dialog.find('#pwbe-bulkedit-fixed-amount');
		var increaseFixed = dialog.find('#pwbe-bulkedit-fixed-increase-amount');
		var decreaseFixed = dialog.find('#pwbe-bulkedit-fixed-decrease-amount');
		var increasePercentage = dialog.find('#pwbe-bulkedit-percentage-increase-amount');
		var decreasePercentage = dialog.find('#pwbe-bulkedit-percentage-decrease-amount');
		var search = dialog.find('#pwbe-bulkedit-currency-search-text');
		var replace = dialog.find('#pwbe-bulkedit-currency-replace-text');
		var allowNegativeCheckbox = dialog.find('.pwbe-bulkedit-allow-negative');
		var useRegularPrice = dialog.find('.pwbe-bulkedit-use-regular-price');
		var mode = dialog.find('input[name=pwbe-bulkedit-currency-mode]:checked').first();
        var thousandSeparator = jQuery('#pwbe-price-thousand-separator').val();
		var decimalSeparator = jQuery('#pwbe-price-decimal-separator').val();
		var rounding = jQuery('#pwbe-bulkedit-rounding').val();
		var decimalPlaces = jQuery('#pwbe-price-decimal-places').val();

		var allowNegative = true;
		if (allowNegativeCheckbox && !allowNegativeCheckbox.prop('checked')) {
			allowNegative = false;
		}

		switch (action) {
			case 'init':
				dialog.find('.pwbe-bulkedit-field-name').text(fieldName);
			break;

			case 'apply':
				if (!mode.val()) {
					return oldValueString;
				}

				var newValue = 0.0;
				var oldValue = oldValueString;

				if (oldValue) {
					oldValue = parseFloat(String(oldValue).replace('$','').replace(thousandSeparator, '').replace(decimalSeparator, '.'));
					newValue = oldValue;
				}

				switch (mode.val()) {
					case 'fixed':
						if (fixed.val()) {
							newValue = fixed.val();
						}
					break;

					case 'fixed-increase':
						if (increaseFixed.val()) {
							newValue = oldValue + parseFloat(increaseFixed.val().replace(thousandSeparator, '').replace(decimalSeparator, '.'));
						}
					break;

					case 'fixed-decrease':
						if (decreaseFixed.val()) {
							newValue = oldValue - parseFloat(decreaseFixed.val().replace(thousandSeparator, '').replace(decimalSeparator, '.'));
						}
					break;

					case 'percentage-increase':
						if (increasePercentage.val()) {
							newValue = oldValue + (oldValue * parseFloat(increasePercentage.val().replace(thousandSeparator, '').replace(decimalSeparator, '.')) / 100.0);
						}
					break;

					case 'percentage-decrease':
						if (decreasePercentage.val()) {
							newValue = oldValue - (oldValue * parseFloat(decreasePercentage.val().replace(thousandSeparator, '').replace(decimalSeparator, '.')) / 100.0);
						}
					break;

					case 'replace':
						if (search.val()) {
							newValue = oldValueString.replace(new RegExp('(' + pwbePregQuote(search.val()) + ')', 'gi'), replace.val());
						} else {
							newValue = replace.val();
						}
					break;

					case 'clear':
						return '';
					break;
				}

				if (mode.val() == 'percentage-increase' || mode.val() == 'percentage-decrease') {
					if (!rounding) {
						rounding = 0;
					}

					newValue = parseFloat(newValue).toFixed(rounding);
				}

				newValue = parseFloat(newValue).toFixed(decimalPlaces).replace('.', decimalSeparator);

				if (!allowNegative && newValue < 0) {
					newValue = '0.00';
				}

				return newValue;
			break;

			case 'reset':
				mode.prop('checked', false);
				fixed.val('');
				increaseFixed.val('');
				decreaseFixed.val('');
				increasePercentage.val('');
				decreasePercentage.val('');
				search.val('');
				replace.val('');
				allowNegativeCheckbox.prop('checked', false);
				useRegularPrice.prop('checked', false);
				dialog.find('.pwbe-bulkedit-editor-allow-negative-container').hide();
				dialog.find('.pwbe-bulkedit-editor-use-regular-price-container').hide();
				dialog.find('.pwbe-bulkedit-editor-rounding-container').hide();
				dialog.find('.pwbe-bulkedit-details').hide();
			break;
		}
	}

</script>