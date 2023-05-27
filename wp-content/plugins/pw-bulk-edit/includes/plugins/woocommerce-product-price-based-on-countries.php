<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WC_Product_Price_Based_Country' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_WC_Product_Price_Based_Country' ) ) :

final class PWBE_WC_Product_Price_Based_Country {

    private $pricing_zones = false;

    function __construct() {
        add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
        add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
        add_filter( 'pwbe_results_product_initial', array( $this, 'pwbe_results_product_initial' ) );
    }

    function get_pricing_zones() {
        if ( false === $this->pricing_zones ) {
            $this->pricing_zones = WCPBC_Pricing_Zones::get_zones();
        }

        return $this->pricing_zones;
    }

    function pwbe_select_options( $select_options ) {
        global $wpdb;

        foreach ( $this->get_pricing_zones() as $key => $value ) {
            $_id_prefix = '_' . $key;
            $select_options[$_id_prefix . '_price_method']['exchange_rate']['name'] = __( 'Calculate prices by exchange rate', 'wc-price-based-country' );
            $select_options[$_id_prefix . '_price_method']['exchange_rate']['visibility'] = 'both';
            $select_options[$_id_prefix . '_price_method']['manual']['name'] = __( 'Set prices manually', 'wc-price-based-country' );
            $select_options[$_id_prefix . '_price_method']['manual']['visibility'] = 'both';

            $select_options[$_id_prefix . '_sale_price_dates']['default']['name'] = __( 'Same as default price', 'wc-price-based-country' );
            $select_options[$_id_prefix . '_sale_price_dates']['default']['visibility'] = 'both';
            $select_options[$_id_prefix . '_sale_price_dates']['manual']['name'] = __( 'Set specific dates', 'wc-price-based-country' );
            $select_options[$_id_prefix . '_sale_price_dates']['manual']['visibility'] = 'both';
        }

        return $select_options;
    }

    function pwbe_product_columns( $columns ) {
        $new_columns = array();

        foreach ( $this->get_pricing_zones() as $key => $value ) {

            $_id_prefix = '_' . $key;

            $new_columns[] = array(
                'name' => __( 'Price for', 'wc-price-based-country' )  . ' ' . $value->get_name() . ' (' . get_woocommerce_currency_symbol( $value->get_currency() ) . ')',
                'type' => 'select',
                'table' => 'meta',
                'field' => $_id_prefix . '_price_method',
                'visibility' => 'both',
                'readonly' => false,
                'sortable' => 'true',
            );

            $new_columns[] = array(
                'name' => __( 'Regular price', 'wc-price-based-country' ) . ' ' . $value->get_name() . ' (' . get_woocommerce_currency_symbol( $value->get_currency() ) . ')',
                'type' => 'currency',
                'table' => 'meta',
                'field' => $_id_prefix . '_regular_price',
                'visibility' => 'both',
                'readonly' => false,
                'sortable' => 'true',
            );

            $new_columns[] = array(
                'name' => __( 'Sale price', 'wc-price-based-country' ) . ' (' . get_woocommerce_currency_symbol( $value->get_currency() ) . ')',
                'type' => 'currency',
                'table' => 'meta',
                'field' => $_id_prefix . '_sale_price',
                'visibility' => 'both',
                'readonly' => false,
                'sortable' => 'true',
            );

            $new_columns[] = array(
                'name' => $value->get_name() . ' ' . __( 'Sale price dates', 'wc-price-based-country' ),
                'type' => 'select',
                'table' => 'meta',
                'field' => $_id_prefix . '_sale_price_dates',
                'visibility' => 'both',
                'readonly' => false,
                'sortable' => 'true',
            );

            $new_columns[] = array(
                'name' => $value->get_name() . ' ' . __( 'Sale start date', 'woocommerce' ),
                'type' => 'date',
                'table' => 'meta',
                'field' => $_id_prefix . '_sale_price_dates_from',
                'visibility' => 'both',
                'readonly' => false,
                'sortable' => 'true',
            );

            $new_columns[] = array(
                'name' => $value->get_name() . ' ' . __( 'Sale end date', 'woocommerce' ),
                'type' => 'date',
                'table' => 'meta',
                'field' => $_id_prefix . '_sale_price_dates_to',
                'visibility' => 'both',
                'readonly' => false,
                'sortable' => 'true',
            );
        }

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

    function pwbe_results_product_initial( $pwbe_product ) {
        foreach ( $this->get_pricing_zones() as $key => $value ) {
            $price_method_field = '_' . $key . '_price_method';
            $sale_price_dates_field = '_' . $key . '_sale_price_dates';

            if ( empty( $pwbe_product->{$price_method_field} ) ) {
                $pwbe_product->{$price_method_field} = 'exchange_rate';
            }

            if ( empty( $pwbe_product->{$sale_price_dates_field} ) ) {
                $pwbe_product->{$sale_price_dates_field} = 'default';
            }
        }

        return $pwbe_product;
    }
}

new PWBE_WC_Product_Price_Based_Country();

endif;

?>