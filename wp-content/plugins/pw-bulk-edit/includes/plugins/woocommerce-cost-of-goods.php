<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// WooCommerce Cost of Goods by Skyverge
// https://www.skyverge.com/product/woocommerce-cost-of-goods-tracking/

if ( !class_exists( 'WC_COG' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_WC_COG' ) ) :

final class PWBE_WC_COG {

    function __construct() {
        add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
    }

    function pwbe_product_columns( $columns ) {
        $insert_before_column = __( 'Weight', 'woocommerce' );

        $new_columns[] = array(
            'name' => __( 'Cost of Good', 'woocommerce-cost-of-goods' ),
            'type' => 'currency',
            'table' => 'meta',
            'field' => '_wc_cog_cost',
            'visibility' => 'both'
        );

        $new_columns[] = array(
            'name' => __( 'Default Variation Cost of Good', 'woocommerce-cost-of-goods' ),
            'type' => 'currency',
            'table' => 'meta',
            'field' => '_wc_cog_cost_variable',
            'visibility' => 'parent'
        );

        $new_columns[] = array(
            'name' => __( 'Min. Cost of Good', 'woocommerce-cost-of-goods' ),
            'type' => 'currency',
            'table' => 'meta',
            'field' => '_wc_cog_min_variation_cost',
            'visibility' => 'parent'
        );

        $new_columns[] = array(
            'name' => __( 'Max. Cost of Good', 'woocommerce-cost-of-goods' ),
            'type' => 'currency',
            'table' => 'meta',
            'field' => '_wc_cog_max_variation_cost',
            'visibility' => 'parent'
        );

        $new_columns[] = array(
            'name' => __( 'Same Cost of Good as parent', 'woocommerce-cost-of-goods' ),
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => '_wc_cog_default_cost',
            'visibility' => 'variation'
        );

        $insert_index = count( $columns );
        for ( $x = 0; $x < count( $columns ); $x++ ) {
            if ( $columns[ $x ]['name'] === $insert_before_column ) {
                $insert_index = $x;
                break;
             }
        }

        array_splice( $columns, $insert_index, 0, $new_columns );

        return $columns;
    }
}

new PWBE_WC_COG();

endif;

?>