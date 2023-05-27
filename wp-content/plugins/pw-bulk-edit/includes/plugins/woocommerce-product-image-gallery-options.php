<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'WooCommerce_Product_Image_Gallery_Options' ) ) {
	return;
}

if ( ! class_exists( 'PWBE_WooCommerce_Product_Image_Gallery_Options' ) ) :

final class PWBE_WooCommerce_Product_Image_Gallery_Options {

	function __construct() {
		add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
	}

	function pwbe_product_columns( $columns ) {
		$columns[] = array(
			'name' => __( 'Hide Image Zoom', 'woocommerce-product-image-gallery-options' ),
			'type' => 'checkbox',
			'table' => 'meta',
			'field' => '_wc_pigo_hide_image_zoom',
			'readonly' => 'false',
			'visibility' => 'parent',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		$columns[] = array(
			'name' => __( 'Hide Image Lightbox', 'woocommerce-product-image-gallery-options' ),
			'type' => 'checkbox',
			'table' => 'meta',
			'field' => '_wc_pigo_hide_image_lightbox',
			'readonly' => 'false',
			'visibility' => 'parent',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		$columns[] = array(
			'name' => __( 'Hide Image Slider', 'woocommerce-product-image-gallery-options' ),
			'type' => 'checkbox',
			'table' => 'meta',
			'field' => '_wc_pigo_hide_image_slider',
			'readonly' => 'false',
			'visibility' => 'parent',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		return $columns;
	}
}

new PWBE_WooCommerce_Product_Image_Gallery_Options();

endif;

?>