<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// PPOM for WooCommerce by Najeeb Ahmad
// https://wordpress.org/plugins/woocommerce-product-addon/

if ( !defined( 'PPOM_VERSION' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_PPOM' ) ) :

final class PWBE_PPOM {

    function __construct() {
        add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
        add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
        add_filter( 'pwbe_skip_sorting_options', array( $this, 'pwbe_skip_sorting_options' ) );

        if ( defined( 'PPOM_PRO_VERSION' ) ) {
            add_filter( 'pwbe_save_field_value', array( $this, 'pwbe_save_field_value' ), 10, 2 );
        }
    }

    function pwbe_select_options( $select_options ) {
        global $wpdb;

        if ( !defined( 'PPOM_PRO_VERSION' ) ) {
            $select_options['_product_meta_id']['None']['name'] = 'None';
            $select_options['_product_meta_id']['None']['visibility'] = 'both';
        }

        $all_meta = PPOM()->get_product_meta_all();
        foreach ( $all_meta as $meta ) {
            $select_options['_product_meta_id'][ $meta->productmeta_id ]['name'] = $meta->productmeta_name;
            $select_options['_product_meta_id'][ $meta->productmeta_id ]['visibility'] = 'both';
        }

        return $select_options;
    }

    function pwbe_product_columns( $columns ) {
        $new_columns = array();

        $new_columns[] = array(
            'name' => 'PPOM Meta',
            'type' => defined( 'PPOM_PRO_VERSION' ) ? 'multiselect' : 'select',
            'table' => 'meta',
            'field' => '_product_meta_id',
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

new PWBE_PPOM();

endif;

?>