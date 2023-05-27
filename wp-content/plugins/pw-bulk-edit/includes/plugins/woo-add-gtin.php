<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

//
// WooCommerce UPC, EAN, and ISBN by Scott Bolinger
// https://wordpress.org/plugins/woo-add-gtin/
//

if ( !class_exists( 'Woo_GTIN' ) ) {
	return;
}

if ( ! class_exists( 'PWBE_Woo_GTIN' ) ) :

final class PWBE_Woo_GTIN {

	function __construct() {
		add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
	}

	function pwbe_product_columns( $columns ) {
	    $columns[] = array(
	        'name' => 'Product GTIN',
	        'type' => 'text',
	        'table' => 'meta',
	        'field' => 'hwp_product_gtin',
	        'visibility' => 'parent',
	        'readonly' => false,
	        'sortable' => 'true'
	    );

	    $columns[] = array(
	        'name' => 'Variation GTIN',
	        'type' => 'text',
	        'table' => 'meta',
	        'field' => 'hwp_var_gtin',
	        'visibility' => 'variation',
	        'readonly' => false,
	        'sortable' => 'true'
	    );

	    return $columns;
	}
}

new PWBE_Woo_GTIN();

endif;

?>