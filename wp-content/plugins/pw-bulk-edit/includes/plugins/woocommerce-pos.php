<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !defined( 'WC_POS_VERSION' ) ) {
	return;
}

if ( ! class_exists( 'PWBE_WooCommerce_POS' ) ) :

final class PWBE_WooCommerce_POS {

	function __construct() {
		add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
		add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
		add_filter( 'pwbe_skip_sorting_options', array( $this, 'pwbe_skip_sorting_options' ) );
	}

	function pwbe_select_options( $select_options ) {

		$pos_visibility_options = array(
			''            => __( 'POS & Online', 'woocommerce-pos' ),
			'pos_only'    => __( 'POS Only', 'woocommerce-pos' ),
			'online_only' => __( 'Online Only', 'woocommerce-pos' )
		);

		foreach ( $pos_visibility_options as $key => $value ) {
			$select_options['_pos_visibility'][ $key ]['name'] = $value;
			$select_options['_pos_visibility'][ $key ]['visibility'] = 'both';
		}

	    return $select_options;
	}

	function pwbe_product_columns( $columns ) {
		$columns[] = array(
			'name' => __( 'POS visibility', 'woocommerce-seo' ),
			'type' => 'select',
			'table' => 'meta',
			'field' => '_pos_visibility',
			'readonly' => 'false',
			'visibility' => 'parent',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		return $columns;
	}

	function pwbe_skip_sorting_options( $options ) {
		$options[] = '_pos_visibility';

		return $options;
	}
}

new PWBE_WooCommerce_POS();

endif;

?>