<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PWBE_Filters' ) ) :

final class PWBE_Filters {

	public static function get() {
		global $wpdb;

		$filter_types = array(
			'categories'				=> array( 'name' => __( 'Category', 'woocommerce' ), 'type' => 'categories' ),
			'post_content'				=> array( 'name' => __( 'Description', 'woocommerce' ), 'type' => 'string' ),
			'thumbnail_id'		        => array( 'name' => __( 'Product image', 'woocommerce' ), 'type' => 'image' ),
			'post_title'				=> array( 'name' => __( 'Product name', 'woocommerce' ), 'type' => 'string' ),
			'product_type'				=> array( 'name' => __( 'Product type', 'woocommerce' ), 'type' => 'product_type' ),
			'regular_price'				=> array( 'name' => __( 'Regular price', 'woocommerce' ), 'type' => 'currency' ),
			'sale_price'				=> array( 'name' => __( 'Sale price', 'woocommerce' ), 'type' => 'currency' ),
			'post_excerpt'				=> array( 'name' => __( 'Short description', 'woocommerce' ), 'type' => 'string' ),
			'post_status'				=> array( 'name' => __( 'Status', 'woocommerce' ), 'type' => 'statuses' ),
			'product_shipping_class'	=> array( 'name' => __( 'Shipping class', 'woocommerce' ), 'type' => 'attributes' ),
			'sku'						=> array( 'name' => __( 'SKU', 'woocommerce' ), 'type' => 'string' ),
			'post_name'					=> array( 'name' => __( 'Slug', 'woocommerce' ), 'type' => 'string' ),
			'stock_status'				=> array( 'name' => __( 'Stock status', 'woocommerce' ), 'type' => 'stock_statuses' ),
			'tags'						=> array( 'name' => __( 'Tag', 'woocommerce' ), 'type' => 'tags' ),
			'variation_description'		=> array( 'name' => __( 'Variation description', 'woocommerce' ), 'type' => 'string' ),
			'tax_class'					=> array( 'name' => __( 'Tax class', 'woocommerce' ), 'type' => 'tax_classes' ),
			'tax_status'				=> array( 'name' => __( 'Tax status', 'woocommerce' ), 'type' => 'tax_statuses' ),
			'meta__manage_stock'		=> array( 'name' => __( 'Manage stock?', 'woocommerce' ), 'type' => 'boolean' ),
    		'meta__length'              => array( 'name' => __( 'Length', 'woocommerce' ), 'type' => 'numeric' ),
    		'meta__width'               => array( 'name' => __( 'Width', 'woocommerce' ), 'type' => 'numeric' ),
    		'meta__height'              => array( 'name' => __( 'Height', 'woocommerce' ), 'type' => 'numeric' ),
    		'meta__weight'              => array( 'name' => __( 'Weight', 'woocommerce' ), 'type' => 'numeric' ),
    		'meta__featured'            => array( 'name' => __( 'Featured', 'woocommerce' ), 'type' => 'boolean' ),
		);

		if ( PW_Bulk_Edit::wc_min_version( '3.0' ) ) {
			$filter_types['catalog_visibility'] = array( 'name' => __( 'Catalog visibility', 'woocommerce' ), 'type' => 'catalog_visibility' );
		}

		if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {
			$filter_types['stock_quantity'] = array( 'name' => __( 'Stock quantity', 'woocommerce' ), 'type' => 'currency' );
		}

		// Add any Attributes
		if ( !defined( 'PWBE_ATTRIBUTE_FILTERS' ) || PWBE_ATTRIBUTE_FILTERS === true ) {
			$attributes = PWBE_Attributes::get_attributes();
			foreach ( $attributes as $attribute ) {
				$counter = 0;
				$name = $attribute['name'];
				$filter_name = $attribute['slug'];

				while ( isset( $filter_types[ $filter_name ] ) ) {
					$counter++;
					$name = "$attribute[name] ($counter)";
					$filter_name = $attribute['slug'] . $counter;
				}

				if ( strpos( $filter_name, 'pa_' ) !== 0 ) {
					$name .= __( ' (Custom Attribute)', 'pw-bulk-edit' );
				}

				$filter_types[ $filter_name ] = array( 'name' => $name, 'type' => 'attributes' );
			}
		}

		// Add any Brands
		foreach ( get_taxonomies( array( 'name' => 'product_brand' ), 'objects' ) as $taxonomy ) {
			$filter_types[$taxonomy->name] = array( 'name' => $taxonomy->labels->singular_name, 'type' => 'attributes' );
		}

		// Add any YITH Brands
		foreach ( get_taxonomies( array( 'name' => 'yith_product_brand' ), 'objects' ) as $taxonomy ) {
			$filter_types['yith_product_brand'] = array( 'name' => 'YITH ' . $taxonomy->labels->singular_name, 'type' => 'attributes' );
		}

		if ( class_exists( 'YITH_Vendors' ) ) {
			$filter_types['yith_shop_vendor'] = array( 'name' => __( 'Vendor', 'pw-bulk-edit' ), 'type' => 'yith_shop_vendor' );
		}

		$filter_types = apply_filters( 'pwbe_filter_types', $filter_types );

		PWBE_Filters::sort( $filter_types );

		return $filter_types;
	}

	private static function sort( &$filter_types ) {
		uasort( $filter_types, 'PWBE_Filters::name_compare');
	}

	private static function name_compare( $a, $b ) {
		return strnatcmp( $a['name'], $b['name'] );
	}
}

endif;

?>