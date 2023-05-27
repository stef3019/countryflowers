<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// Cost of Goods for WooCommerce by WPFactory
// https://wordpress.org/plugins/cost-of-goods-for-woocommerce/

if ( !class_exists( 'Alg_WC_Cost_of_Goods' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_Alg_WC_Cost_of_Goods' ) ) :

final class PWBE_Alg_WC_Cost_of_Goods {

    function __construct() {
        add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
    }

    function pwbe_product_columns( $columns ) {
        $insert_before_column = __( 'Sale end date', 'woocommerce' );

        $new_columns[] = array(
            'name' => __( 'Cost', 'cost-of-goods-for-woocommerce' ),
            'type' => 'currency',
            'table' => 'meta',
            'field' => '_alg_wc_cog_cost',
            'visibility' => 'both'
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

new PWBE_Alg_WC_Cost_of_Goods();

endif;

?>