<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands' ) ) {
	return;
}

if ( !class_exists( 'PWBE_Perfect_WooCommerce_Brands' ) ) :

final class PWBE_Perfect_WooCommerce_Brands {

	function __construct() {
		add_filter( 'pwbe_filter_types', array( $this, 'pwbe_filter_types' ) );
		add_action( 'pwbe_after_filter_select_templates', array( $this, 'pwbe_after_filter_select_templates' ) );
		add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
		add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
		add_filter( 'pwbe_skip_sorting_options', array( $this, 'pwbe_skip_sorting_options' ) );
		add_filter( 'pwbe_taxonomy_list', array( $this, 'pwbe_taxonomy_list' ) );
		add_filter( 'pwbe_terms_save_handled', array( $this, 'pwbe_terms_save_handled' ), 10, 3 );
		add_filter( 'pwbe_search_row_sql', array( $this, 'pwbe_search_row_sql' ), 10, 6 );
	}

	function pwbe_filter_types( $filter_types ) {
		$filter_types['pwb-brand'] = array( 'name' => __( 'Perfect Brand', 'perfect-woocommerce-brands' ), 'type' => 'attributes' );

		return $filter_types;
	}

	function pwbe_after_filter_select_templates() {
		?>
		<span class="pwbe-multiselect pwbe-filter-pwb-brand-container pwbe-filter-pwb-brand-template" style="display: none;">
			<select name="filter_select[]" class="pwbe-filter-field pwbe-filter-select pwbe-filter-value" multiple="multiple" ></select>
		</span>
		<?php
	}

	function pwbe_select_options( $select_options ) {

		$brands = Perfect_Woocommerce_Brands\Perfect_Woocommerce_Brands::get_brands();

		foreach ( $brands as $brand ) {
			$select_options['attribute_pwb-brand'][ $brand->slug ]['name'] = $brand->name;
			$select_options['attribute_pwb-brand'][ $brand->slug ]['visibility'] = 'both';
			$select_options['pwb-brand'][ $brand->slug ]['name'] = $brand->name;
			$select_options['pwb-brand'][ $brand->slug ]['visibility'] = 'both';
		}

	    return $select_options;
	}

	function pwbe_product_columns( $columns ) {
		$columns[] = array(
			'name' => __( 'Brands', 'perfect-woocommerce-brands' ),
			'type' => 'multiselect',
			'table' => 'terms',
			'field' => 'pwb-brand',
			'readonly' => 'false',
			'visibility' => 'parent',
			'sortable' => 'false',
			'views' => array( 'all', 'standard' )
		);

		return $columns;
	}

	function pwbe_skip_sorting_options( $options ) {
		$options[] = 'pwb-brand';

		return $options;
	}

	function pwbe_taxonomy_list( $taxonomy_list ) {
		$taxonomy_list[] = 'pwb-brand';

		return $taxonomy_list;
	}

	function pwbe_terms_save_handled( $handled, $save_class, $field ) {
		if ( $field['field'] == 'pwb-brand' ) {
			$save_class->save_taxonomies( $field['post_id'], $field['value'], 'pwb-brand' );
			$handled = true;
		}

		return $handled;
	}

	function pwbe_search_row_sql( $row_sql, $search_class, $field_name, $filter_type, $field_value, $field_value2 ) {
		if ( 'pwb-brand' == $field_name ) {
			$row_sql = $search_class->taxonomy_search( 'pwb-brand', $filter_type, $field_value );
		}

		return $row_sql;
	}
}

new PWBE_Perfect_WooCommerce_Brands();

endif;

?>