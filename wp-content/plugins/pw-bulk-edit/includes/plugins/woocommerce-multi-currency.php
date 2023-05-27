<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// WooCommerce Multi Currency Premium
// by VillaTheme

if ( !defined( 'WOOMULTI_CURRENCY_VERSION' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_WOOMULTI_CURRENCY' ) ) :

final class PWBE_WOOMULTI_CURRENCY {

    function __construct() {
        add_filter( 'pwbe_product_columns', array( $this, 'product_columns' ) );
        add_filter( 'pwbe_results_decode_array', array( $this, 'results_decode_array' ), 10, 4 );
        add_filter( 'pwbe_before_save_array_value', array( $this, 'before_save_array_value' ), 10, 3 );
        add_filter( 'pwbe_save_array_value', array( $this, 'save_array_value' ), 10, 3 );
    }

    function product_columns( $columns ) {
        $settings = WOOMULTI_CURRENCY_Data::get_ins();
        $currencies = $settings->get_currencies();

        foreach ( $currencies as $currency ) {
            if ( $currency != $settings->get_default_currency() ) {
                $new_columns[] = array(
                    'name' => __( 'Regular Price', 'woocommerce-multi-currency' ) . ' (' . $currency . ')',
                    'type' => 'currency',
                    'table' => 'meta',
                    'field' => "_regular_price_wmcp___{$currency}",
                    'readonly' => 'false',
                    'visibility' => 'both',
                    'sortable' => 'true',
                    'views' => array( 'all', 'standard' ),
                );

                $new_columns[] = array(
                    'name' => __( 'Sale Price', 'woocommerce-multi-currency' ) . ' (' . $currency . ')',
                    'type' => 'currency',
                    'table' => 'meta',
                    'field' => "_sale_price_wmcp___{$currency}",
                    'readonly' => 'false',
                    'visibility' => 'both',
                    'sortable' => 'true',
                    'views' => array( 'all', 'standard' ),
                );
            }
        }

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

    function results_decode_array( $array_value, $field, $pwbe_product, $array_name ) {
        if ( strpos( $field, '_price_wmcp' ) !== false ) {
            // Handle JSON data.
            if ( !is_array( $array_value ) ) {
                $json_value = json_decode( $array_value, true);
                if ( json_last_error() == JSON_ERROR_NONE ) {
                    $array_value = $json_value;
                }
            }
        }

        return $array_value;
    }

    function before_save_array_value( $stored_data, $meta_key, $field ) {
        if ( strpos( $meta_key, '_price_wmcp' ) !== false ) {
            if ( !empty( $stored_data ) ) {
                // Handle JSON data.
                $json_value = json_decode( $stored_data, true);
                if ( json_last_error() == JSON_ERROR_NONE ) {
                    $stored_data = $json_value;
                }
            }
        }

        return $stored_data;
    }

    function save_array_value( $array_value, $meta_key, $field ) {
        if ( strpos( $meta_key, '_price_wmcp' ) !== false ) {
            $array_value = json_encode( $array_value );
        }

        return $array_value;
    }
}

new PWBE_WOOMULTI_CURRENCY();

endif;

?>