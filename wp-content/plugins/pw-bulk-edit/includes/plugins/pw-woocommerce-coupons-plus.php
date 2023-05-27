<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'PW_WooCommerce_Coupons_Plus' ) ) {
	return;
}

if ( ! class_exists( 'PWBE_PW_WooCommerce_Coupons_Plus' ) ) :

final class PWBE_PW_WooCommerce_Coupons_Plus {

	function __construct() {
        add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
        add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
    }

    function pwbe_select_options( $select_options ) {
        $select_options['_pwcp_clip_product_coupon_display_format']['percentage']['name'] = __( 'Percentage', 'pw-woocommerce-coupons-plus' );
        $select_options['_pwcp_clip_product_coupon_display_format']['percentage']['visibility'] = 'both';
        $select_options['_pwcp_clip_product_coupon_display_format']['fixed']['name'] = __( 'Fixed amount', 'pw-woocommerce-coupons-plus' );
        $select_options['_pwcp_clip_product_coupon_display_format']['fixed']['visibility'] = 'both';

        return $select_options;
    }

    function pwbe_product_columns( $columns ) {
        $new_columns[] = array(
            'name' => __( 'No Coupons Allowed', 'pw-woocommerce-coupons-plus' ),
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => '_pwcp_no_coupons_allowed',
            'visibility' => 'both',
            'readonly' => false,
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
            'checked_value' => '1',
            'unchecked_value' => '0',
        );

        $new_columns[] = array(
            'name' => __( 'Clip Coupon', 'pw-woocommerce-coupons-plus' ),
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => '_pwcp_clip_product_coupon',
            'visibility' => 'parent',
            'readonly' => false,
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
            'checked_value' => '1',
            'unchecked_value' => '0',
        );

        $new_columns[] = array(
            'name' => __( 'Coupon display format', 'woocommerce' ),
            'type' => 'select',
            'table' => 'meta',
            'field' => '_pwcp_clip_product_coupon_display_format',
            'visibility' => 'parent',
            'readonly' => false,
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        if ( !empty( $new_columns ) ) {
            $start_index = 1;
            foreach ( $columns as $index => $column ) {
                // Insert the new columns after this column.
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

new PWBE_PW_WooCommerce_Coupons_Plus();

endif;

?>