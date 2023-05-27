<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// WooCommerce Product Feed Pro
// https://webappick.com/

if ( !defined( 'WOO_FEED_PRO_VERSION' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_WOO_FEED_PRO' ) ) :

final class PWBE_WOO_FEED_PRO {

    function __construct() {
        add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
    }

    function pwbe_product_columns( $columns ) {
        $new_columns = array();

        $new_columns[] = array(
            'name' => __( 'GTIN', 'woo-feed' ),
            'type' => 'text',
            'table' => 'meta',
            'field' => 'woo_feed_gtin',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' )
        );

        $new_columns[] = array(
            'name' => __( 'MPN', 'woo-feed' ),
            'type' => 'text',
            'table' => 'meta',
            'field' => 'woo_feed_mpn',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' )
        );

        $new_columns[] = array(
            'name' => __( 'EAN', 'woo-feed' ),
            'type' => 'text',
            'table' => 'meta',
            'field' => 'woo_feed_ean',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' )
        );

        if ( !empty( $new_columns ) ) {
            $start_index = 1;
            foreach ( $columns as $index => $column ) {
                // Insert the new columns after this column.
                if ( $column['field'] === 'product_shipping_class' ) {
                    $start_index = $index + 1;
                    break;
                }
            }

            array_splice( $columns, $start_index, 0, $new_columns );
        }

        return $columns;
    }

    function pwbe_skip_sorting_options( $options ) {
        $options[] = '_product_meta_id';

        return $options;
    }

    function pwbe_save_field_value( $value, $field ) {
        if ( $field['field'] == '_product_meta_id' ) {
            $value = explode( ',', $value );
        }

        return $value;
    }
}

new PWBE_WOO_FEED_PRO();

endif;

?>