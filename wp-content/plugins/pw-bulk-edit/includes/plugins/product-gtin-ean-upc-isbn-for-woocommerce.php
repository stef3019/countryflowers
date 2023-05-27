<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//
// Product GTIN (EAN, UPC, ISBN) for WooCommerce by Emanuela Castorina
// https://wordpress.org/plugins/product-gtin-ean-upc-isbn-for-woocommerce/
//

if ( ! class_exists( 'WPM_Product_GTIN_WC' ) ) {
	return;
}

if ( ! class_exists( 'PWBE_WPM_Product_GTIN_WC' ) ) :

final class PWBE_WPM_Product_GTIN_WC {

	function __construct() {
		add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
	}

	function pwbe_product_columns( $columns ) {
        $new_columns = array();

        $label = get_option( 'wpm_pgw_label', __('EAN', 'product-gtin-ean-upc-isbn-for-woocommerce' ) );

        $new_columns[] = array(
            'name' => str_replace( ':', '', sprintf( __( '%s Code:', 'product-gtin-ean-upc-isbn-for-woocommerce' ), $label ) ),
            'type' => 'text',
            'table' => 'meta',
            'field' => '_wpm_gtin_code',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        if ( !empty( $new_columns ) ) {
            // Insert after the "Sale End Date" column.
            $start_index = 1;
            foreach ( $columns as $index => $column ) {
                if ( $column['field'] === '_sale_price_dates_to' ) {
                    $start_index = $index + 1;
                    break;
                }
            }

            array_splice( $columns, $start_index, 0, $new_columns );
        }

		return $columns;
	}
}

new PWBE_WPM_Product_GTIN_WC();

endif;

?>