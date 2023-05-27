<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WC_Min_Max_Quantities' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_WooCommerce_Min_Max_Quantities' ) ) :

final class PWBE_WooCommerce_Min_Max_Quantities {

    function __construct() {
        add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
    }

    function pwbe_product_columns( $columns ) {
        $insert_before_column = __( 'Weight', 'woocommerce' );

        $new_columns[] = array(
            'name' => __( 'Minimum quantity', 'woocommerce-min-max-quantities' ),
            'type' => 'number',
            'table' => 'meta',
            'field' => 'minimum_allowed_quantity',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $new_columns[] = array(
            'name' => __( 'Maximum quantity', 'woocommerce-min-max-quantities' ),
            'type' => 'number',
            'table' => 'meta',
            'field' => 'maximum_allowed_quantity',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $new_columns[] = array(
            'name' => __( 'Group of...', 'woocommerce-min-max-quantities' ),
            'type' => 'number',
            'table' => 'meta',
            'field' => 'group_of_quantity',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $new_columns[] = array(
            'name' => __( 'Allow Combination', 'woocommerce-min-max-quantities' ) . ' ' . __( '(Variable Products Only)', 'pw-bulk-edit' ),
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => 'allow_combination',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $new_columns[] = array(
            'name' => __( 'Order rules: Do not count', 'woocommerce-min-max-quantities' ),
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => 'minmax_do_not_count',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $new_columns[] = array(
            'name' => __( 'Order rules: Exclude', 'woocommerce-min-max-quantities' ),
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => 'minmax_cart_exclude',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $new_columns[] = array(
            'name' => __( 'Category rules: Exclude', 'woocommerce-min-max-quantities' ),
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => 'minmax_category_group_of_exclude',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $new_columns[] = array(
            'name' => __( 'Min/Max Rules', 'woocommerce-min-max-quantities' ),
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => 'min_max_rules',
            'readonly' => 'false',
            'visibility' => 'variation',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $new_columns[] = array(
            'name' => __( 'Variation', 'woocommerce' ) . ' ' . __( 'Minimum quantity', 'woocommerce-min-max-quantities' ),
            'type' => 'number',
            'table' => 'meta',
            'field' => 'variation_minimum_allowed_quantity',
            'readonly' => 'false',
            'visibility' => 'variation',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $new_columns[] = array(
            'name' => __( 'Variation', 'woocommerce' ) . ' ' . __( 'Maximum quantity', 'woocommerce-min-max-quantities' ),
            'type' => 'number',
            'table' => 'meta',
            'field' => 'variation_maximum_allowed_quantity',
            'readonly' => 'false',
            'visibility' => 'variation',
            'sortable' => 'true',
            'views' => array( 'all' )
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

new PWBE_WooCommerce_Min_Max_Quantities();

endif;

?>