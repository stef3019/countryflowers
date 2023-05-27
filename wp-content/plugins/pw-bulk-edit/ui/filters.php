<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

?>
<?php require( 'activation.php' ); ?>
<div class="pwbe-filter">
	<div class="pwbe-filter-container">
		<span class="pwbe-filter-toolbar-right">
			<a href="<?php echo $settings_url; ?>" id="pwbe-settings" class="pwbe-link pwbe-settings-link"><i class="fa fa-fw fa-cog pwbe-link"></i> <?php _e( 'Settings', 'pw-bulk-edit' ); ?></a>
			<a href="<?php echo $help_url; ?>" id="pwbe-help" class="pwbe-link pwbe-help-link"><i class="fa fa-fw fa-life-ring pwbe-link"></i> <?php _e( 'Help', 'pw-bulk-edit' ); ?></a>
		</span>
		<span class="pwbe-filter-toolbar">
			<span id="pwbe-filter-new" class="pwbe-filter-link pwbe-filter-new pwbe-filter-toolbar-button" title="<?php _e( 'Create a new filter', 'pw-bulk-edit' ); ?>"><i class="fa fa-file-o fa-fw"></i></span>
			<span id="pwbe-filter-save" class="pwbe-filter-link pwbe-premium-item pwbe-filter-toolbar-button" title="<?php _e( 'Save this filter', 'pw-bulk-edit' ); ?>"><i class="fa fa-save fa-fw"></i></span>
			<span id="pwbe-filter-open" class="pwbe-filter-link pwbe-premium-item pwbe-filter-toolbar-button" title="<?php _e( 'Open a saved filter', 'pw-bulk-edit' ); ?>"><i class="fa fa-folder-open-o fa-fw"></i></span>
		</span>
		<div class="pwbe-filter-form" data-dirty="false">
			<div class="pwbe-pull-right">
				<span id="pwbe-hide-filters-button" class="pwbe-link pwbe-hidden" title="<?php _e( 'Hide Filters', 'pw-bulk-edit' ); ?>"><i class="fa fa-eye-slash fa-fw" aria-hidden="true"></i> <?php _e( 'Hide Filters', 'pw-bulk-edit' ); ?></span>
				<span id="pwbe-show-filters-button" class="pwbe-link pwbe-hidden" title="<?php _e( 'Show Filters', 'pw-bulk-edit' ); ?>"><i class="fa fa-eye fa-fw" aria-hidden="true"></i> <?php _e( 'Show Filters', 'pw-bulk-edit' ); ?></span>
			</div>
			<form id="pwbe-filters-form">
				<input type="hidden" id="pwbe-order-by" name="order_by" value="post_title" />
				<input type="hidden" id="pwbe-order-by-desc" name="order_by_desc" value="" />

				<div class="pwbe-filter-header">
					<span id="pwbe-header-multiple-filters" class="pwbe-pull-left">
						<?php _e( 'Find products that match', 'pw-bulk-edit' ); ?>
						<select id="pwbe-filter-group" name="main_group_type">
							<option value="pwbe_and"><?php _e( 'all', 'pw-bulk-edit' ); ?></option>
							<option value="pwbe_or"><?php _e( 'any', 'pw-bulk-edit' ); ?></option>
						</select>
						<?php _e( 'of the following rules:', 'pw-bulk-edit' ); ?>
					</span>
				</div>
				<div class="pwbe-filter-row-container">
					<hr class="pwbe-filter-container-break"/>
				</div>

				<div style="margin-bottom: 8px;">
					<label id="pwbe-show-all-variations-label" for="pwbe-show-all-variations"><input type="checkbox" id="pwbe-show-all-variations" name="show_all_variations"> <?php _e( 'Show all Variations for Variable Products', 'pw-bulk-edit' ); ?></label>
				</div>

				<button type="submit" id="pwbe-search-button" class="button"><i class="fa fa-search" aria-hidden="true"></i> <?php _e( 'Search', 'pw-bulk-edit' ); ?></button>
				<?php
					echo apply_filters( 'pwbe_html_after_search_button', '' );
				?>
			</form>
		</div>
	</div>
	<?php require( 'filters_help.php' ); ?>
</div>

<div class="pwbe-row-template-group pwbe-filter-row pwbe-filter-group-row" data-suffix="">
	<input type="hidden" name="row[]" value="group">

	<select name="filter_name" class="pwbe-filter-field pwbe-filter-name">
  		<option value="pwbe_and"><?php _e( 'all', 'pw-bulk-edit' ); ?></option>
		<option value="pwbe_or"><?php _e( 'any', 'pw-bulk-edit' ); ?></option>
	</select> <?php _e( 'of the following are true', 'pw-bulk-edit' ); ?>

	<input type="hidden" name="filter_type" class="pwbe-filter-type" value="" />

	<span class="pwbe-pull-right">
		<span class="pwbe-filter-link pwbe-filter-icon pwbe-filter-remove" title="<?php _e( 'Remove', 'pw-bulk-edit' ); ?>"><i class="fa fa-minus-square-o"></i></span>
		<span class="pwbe-filter-link pwbe-filter-icon pwbe-filter-add" title="<?php _e( 'Add a filter', 'pw-bulk-edit' ); ?>"><i class="fa fa-plus-square-o"></i></span>
	</span>

	<br />
	<span class="pwbe-filter-link pwbe-filter-criteria pwbe-filter-add" title="<?php _e( 'Add a filter', 'pw-bulk-edit' ); ?>"><i class="fa fa-plus-square-o"></i> <?php _e( 'Add a filter', 'pw-bulk-edit' ); ?></span>
	<span class="pwbe-filter-link pwbe-filter-criteria pwbe-filter-add-group" title="<?php _e( 'Add a group of filters', 'pw-bulk-edit' ); ?>"><i class="fa fa-plus-square-o"></i> <?php _e( 'Add a Group of Filters', 'pw-bulk-edit' ); ?></span>
	<span class="pwbe-filter-link pwbe-filter-criteria pwbe-filter-remove" title="<?php _e( 'Remove', 'pw-bulk-edit' ); ?>"><i class="fa fa-minus-square-o"></i> <?php _e( 'Remove', 'pw-bulk-edit' ); ?></span>
	<hr class="pwbe-filter-container-break"/>
</div>

<div class="pwbe-row-template pwbe-filter-row" data-suffix="">
	<input type="hidden" name="row[]" value="">

	<select name="filter_name" class="pwbe-filter-field pwbe-filter-name">
		<?php
			foreach (PWBE_Filters::get() as $filter_name => $criteria) {
				$name = $criteria['name'];
				$type = $criteria['type'];
				$key = isset( $criteria['key'] ) ? $criteria['key'] : '';

				echo "<option value=\"$filter_name\" data-type=\"$type\" data-key=\"$key\">$name</option>\n";
			}
		?>
	</select>

	<select name="filter_type" class="pwbe-filter-field pwbe-filter-type"></select>

	<input name="filter_value" class="pwbe-filter-field pwbe-filter-field-input pwbe-filter-value" type="text" value="" autocomplete="off" />

	<span class="pwbe-filter-required">
		* <?php _e( 'required', 'pw-bulk-edit' ); ?>
	</span>

	<span class="pwbe-pull-right">
		<span class="pwbe-filter-link pwbe-filter-icon pwbe-filter-remove" title="<?php _e( 'Remove', 'pw-bulk-edit' ); ?>"><i class="fa fa-minus-square-o"></i></span>
		<span class="pwbe-filter-link pwbe-filter-icon pwbe-filter-add" title="<?php _e( 'Add a filter', 'pw-bulk-edit' ); ?>"><i class="fa fa-plus-square-o"></i></span>
	</span>

	<br />
	<span class="pwbe-filter-link pwbe-filter-criteria pwbe-filter-add" title="<?php _e( 'Add a filter', 'pw-bulk-edit' ); ?>"><i class="fa fa-plus-square-o"></i> <?php _e( 'Add a filter', 'pw-bulk-edit' ); ?></span>
	<span class="pwbe-filter-link pwbe-filter-criteria pwbe-filter-add-group" title="<?php _e( 'Add a group of filters', 'pw-bulk-edit' ); ?>"><i class="fa fa-plus-square-o"></i> <?php _e( 'Add a group of filters', 'pw-bulk-edit' ); ?></span>
	<span class="pwbe-filter-link pwbe-filter-criteria pwbe-filter-remove" title="<?php _e( 'Remove', 'pw-bulk-edit' ); ?>"><i class="fa fa-minus-square-o"></i> <?php _e( 'Remove', 'pw-bulk-edit' ); ?></span>
	<hr class="pwbe-filter-container-break"/>
</div>

<input name="filter_value" class="pwbe-filter-value-template pwbe-filter-field pwbe-filter-field-input pwbe-filter-value" type="text" value="" autocomplete="off" />

<span class="pwbe-filter-value2-template pwbe-filter-value2-container">
	to <input name="filter_value2" class="pwbe-filter-field pwbe-filter-field-input pwbe-filter-value2" type="text" value="" autocomplete="off" />
</span>

<span class="pwbe-filter-attributes-template pwbe-multiselect pwbe-filter-attributes-container">
	<select name="filter_select[]" class="pwbe-filter-field pwbe-filter-select pwbe-filter-value" multiple="multiple" ></select>
</span>

<span class="pwbe-multiselect pwbe-filter-categories-container pwbe-filter-categories-template">
	<select name="filter_select[]" class="pwbe-filter-field pwbe-filter-select pwbe-filter-value" multiple="multiple" ></select>
</span>

<span class="pwbe-multiselect pwbe-filter-tags-container pwbe-filter-tags-template">
	<select name="filter_select[]" class="pwbe-filter-field pwbe-filter-select pwbe-filter-value" multiple="multiple" ></select>
</span>

<span class="pwbe-multiselect pwbe-filter-statuses-container pwbe-filter-statuses-template">
	<select name="filter_select[]" class="pwbe-filter-field pwbe-filter-select pwbe-filter-value" multiple="multiple" ></select>
</span>

<span class="pwbe-multiselect pwbe-filter-stock_statuses-container pwbe-filter-stock_statuses-template">
	<select name="filter_select[]" class="pwbe-filter-field pwbe-filter-select pwbe-filter-value" multiple="multiple" ></select>
</span>

<span class="pwbe-multiselect pwbe-filter-catalog_visibility-container pwbe-filter-catalog_visibility-template">
	<select name="filter_select[]" class="pwbe-filter-field pwbe-filter-select pwbe-filter-value" multiple="multiple" ></select>
</span>

<span class="pwbe-multiselect pwbe-filter-product_type-container pwbe-filter-product_type-template">
	<select name="filter_select[]" class="pwbe-filter-field pwbe-filter-select pwbe-filter-value" multiple="multiple" ></select>
</span>

<span class="pwbe-multiselect pwbe-filter-tax_classes-container pwbe-filter-tax_classes-template">
	<select name="filter_select[]" class="pwbe-filter-field pwbe-filter-select pwbe-filter-value" multiple="multiple" ></select>
</span>

<span class="pwbe-multiselect pwbe-filter-tax_statuses-container pwbe-filter-tax_statuses-template">
	<select name="filter_select[]" class="pwbe-filter-field pwbe-filter-select pwbe-filter-value" multiple="multiple" ></select>
</span>

<?php
	do_action( 'pwbe_after_filter_select_templates' );
?>

<?php
	if ( class_exists( 'YITH_Vendors' ) ) {
		?>
		<span class="pwbe-multiselect pwbe-filter-yith_shop_vendor-container pwbe-filter-yith_shop_vendor-template">
			<select name="filter_select[]" class="pwbe-filter-field pwbe-filter-select pwbe-filter-value" multiple="multiple" ></select>
		</span>
		<?php
	}
?>

<div id="pwbe-filter-manager-dialog" class="pwbe-dialog">
	<div class="pwbe-dialog-heading">
		<i class="fa fa-filter"></i> <span class="pwbe-filter-manager-dialog-name"><?php _e( 'Filter Manager', 'pw-bulk-edit' ); ?></span>
	</div>
	<div class="pwbe-dialog-container">
		<?php
			require( dirname( __FILE__ ) . '/filter_manager/open.php' );
		?>
	</div>
	<div class="pwbe-dialog-button-container">
		<button id="pwbe-filter-manager-dialog-button-cancel" class="button button-secondary pwbe-dialog-button-cancel"><?php _e( 'Close', 'pw-bulk-edit' ); ?></a>
	</div>
</div>