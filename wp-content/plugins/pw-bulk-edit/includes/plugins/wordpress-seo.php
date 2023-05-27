<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// WooCommerce Wholesale Prices plugin
// https://wordpress.org/plugins/woocommerce-wholesale-prices/

if ( !defined( 'WPSEO_VERSION' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_WordPress_SEO' ) ) :

final class PWBE_WordPress_SEO {

    function __construct() {
        add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
    }

    function pwbe_product_columns( $columns ) {

        $new_columns[] = array(
            'name' => __( 'Canonical URL', 'wordpress-seo' ),
            'type' => 'text',
            'table' => 'meta',
            'field' => '_yoast_wpseo_canonical',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        // Insert after the "Sale End Date" column.
        $start_index = 1;
        foreach ( $columns as $index => $column ) {
            if ( $column['field'] === '_sale_price_dates_to' ) {
                $start_index = $index + 1;
                break;
            }
        }

        array_splice( $columns, $start_index, 0, $new_columns );

        return $columns;
    }
}

new PWBE_WordPress_SEO();

endif;

?>