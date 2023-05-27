<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'WC_Local_Pickup_Plus_Loader' ) ) {
	return;
}

if ( ! class_exists( 'PWBE_WC_Local_Pickup_Plus_Loader' ) ) :

final class PWBE_WC_Local_Pickup_Plus_Loader {

	function __construct() {
        add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
        add_filter( 'pwbe_skip_sorting_options', array( $this, 'pwbe_skip_sorting_options' ) );
        add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
        add_filter( 'pwbe_results_product_initial', array( $this, 'pwbe_results_product_initial' ) );
	}

    function pwbe_select_options( $select_options ) {
        $types = array(
            'inherit'    => __( 'Use category-level setting', 'woocommerce-shipping-local-pickup-plus' ),
            'allowed'    => __( 'Can be picked up',           'woocommerce-shipping-local-pickup-plus' ),
            'disallowed' => __( 'Cannot be picked up',        'woocommerce-shipping-local-pickup-plus' ),
            'required'   => __( 'Must be picked up',          'woocommerce-shipping-local-pickup-plus' ),
        );

        foreach ( $types as $type => $name ) {
            $select_options['_wc_local_pickup_plus_local_pickup_product_availability'][$type]['name'] = $name;
            $select_options['_wc_local_pickup_plus_local_pickup_product_availability'][$type]['visibility'] = 'both';
        }

        return $select_options;
    }

    function pwbe_skip_sorting_options( $options ) {
        $options[] = '_wc_local_pickup_plus_local_pickup_product_availability';

        return $options;
    }

    function pwbe_product_columns( $columns ) {
        $new_columns[] = array(
            'name' => __( 'Local Pickup', 'woocommerce-shipping-local-pickup-plus' ),
            'type' => 'select',
            'table' => 'meta',
            'field' => '_wc_local_pickup_plus_local_pickup_product_availability',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all' )
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

    function pwbe_results_product_initial( $pwbe_product ) {
        if ( !isset( $pwbe_product->_wc_local_pickup_plus_local_pickup_product_availability ) || empty( $pwbe_product->_wc_local_pickup_plus_local_pickup_product_availability ) ) {
            $pwbe_product->_wc_local_pickup_plus_local_pickup_product_availability = 'inherit';
        }

        return $pwbe_product;
    }
}

new PWBE_WC_Local_Pickup_Plus_Loader();

endif;

?>