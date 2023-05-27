<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}


//
// Estimated Delivery Date for WooCommerce
// by PI WebSolution
// https://wordpress.org/plugins/estimate-delivery-date-for-woocommerce/
//

if ( !defined( 'PI_EDD_VERSION' ) || ! function_exists( 'is_plugin_active' ) || ! is_plugin_active( 'estimate-delivery-date-for-woocommerce-pro/pi-edd.php') ) {
    return;
}

if ( ! class_exists( 'PWBE_PI_EDD_VERSION' ) ) :

final class PWBE_PI_EDD_VERSION {

    function __construct() {
        add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
    }

    function pwbe_product_columns( $columns ) {
        $new_columns[] = array(
            'name' => __( 'Disable estimate for this product', 'pi-edd' ),
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => 'pisol_edd_disable_estimate',
            'visibility' => 'parent',
            'readonly' => false,
            'sortable' => 'true',
        );

        $new_columns[] = array(
            'name' => __( 'Product preparation days', 'pi-edd' ),
            'type' => 'number',
            'table' => 'meta',
            'field' => 'product_preparation_time',
            'visibility' => 'parent',
            'readonly' => false,
            'sortable' => 'true',
        );

        $new_columns[] = array(
            'name' => __( 'Extra time', 'pi-edd' ),
            'type' => 'number',
            'table' => 'meta',
            'field' => 'out_of_stock_product_preparation_time',
            'visibility' => 'parent',
            'readonly' => false,
            'sortable' => 'true',
        );

        $new_columns[] = array(
            'name' => __( 'Exact date instead of prep time', 'pi-edd' ),
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => 'pisol_enable_exact_date',
            'visibility' => 'parent',
            'readonly' => false,
            'sortable' => 'true',
        );

        $new_columns[] = array(
            'name' => __( 'Exact Product availability date', 'pi-edd' ),
            'type' => 'text',
            'table' => 'meta',
            'field' => 'pisol_exact_availability_date',
            'visibility' => 'parent',
            'readonly' => false,
            'sortable' => 'true',
        );

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

new PWBE_PI_EDD_VERSION();

endif;

?>