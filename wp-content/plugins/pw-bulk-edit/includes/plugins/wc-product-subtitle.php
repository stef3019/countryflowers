<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !defined( 'WCPS_V' ) && !defined( 'WCPS_VERSION' ) ) {
	return;
}

if ( ! class_exists( 'PWBE_WooCommerce_Product_Subtitle' ) ) :

final class PWBE_WooCommerce_Product_Subtitle {

	function __construct() {
		add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
	}

	function pwbe_product_columns( $columns ) {
		$columns[] = array(
			'name' => __( 'Subtitle', 'woocommerce-product-subtitle' ),
			'type' => 'text',
			'table' => 'meta',
			'field' => 'wc_ps_subtitle',
			'readonly' => 'false',
			'visibility' => 'parent',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		return $columns;
	}
}

new PWBE_WooCommerce_Product_Subtitle();

endif;

?>