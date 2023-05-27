<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !defined( 'WOOCOMMERCE_GPF_VERSION' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_WooCommerce_GPF' ) ) :

final class PWBE_WooCommerce_GPF {

    function __construct() {
        add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
        add_filter( 'pwbe_save_array_value', array( $this, 'pwbe_save_array_value' ), 10, 3 );
    }

    function pwbe_product_columns( $columns ) {
        $new_columns = array();

        $new_columns[] = array(
            'name' => 'GPF ' . __( 'Brand', 'woocommerce_gpf' ),
            'type' => 'text',
            'table' => 'meta',
            'field' => "_woocommerce_gpf_data___brand",
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $new_columns[] = array(
            'name' => 'GPF ' . __( 'Manufacturer Part Number (MPN)', 'woocommerce_gpf' ),
            'type' => 'text',
            'table' => 'meta',
            'field' => "_woocommerce_gpf_data___mpn",
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $new_columns[] = array(
            'name' => 'GPF ' . __( 'Product Type', 'woocommerce_gpf' ),
            'type' => 'text',
            'table' => 'meta',
            'field' => "_woocommerce_gpf_data___product_type",
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $new_columns[] = array(
            'name' => __( 'Google Product Category', 'woocommerce_gpf' ),
            'type' => 'text',
            'table' => 'meta',
            'field' => "_woocommerce_gpf_data___google_product_category",
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $new_columns[] = array(
            'name' => __( 'Hide this product from the feed', 'woocommerce_gpf' ),
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => "_woocommerce_gpf_data___exclude_product",
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'checked_value' => 'on',
            'unchecked_value' => '',
            'views' => array( 'all', 'standard' ),
        );

        if ( !empty( $new_columns ) ) {
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

    function pwbe_save_array_value( $array_value, $meta_key, $field ) {
        if ( isset( $array_value['exclude_product'] ) && $array_value['exclude_product'] == 'yes' ) {
            $array_value['exclude_product'] = 'on';
        } else {
            unset( $array_value['exclude_product'] );
        }

        return $array_value;
    }
}

new PWBE_WooCommerce_GPF();

endif;

?>