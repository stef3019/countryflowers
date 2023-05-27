<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Woo_Advanced_Qty' ) ) {
	return;
}

if ( ! class_exists( 'PWBE_Woo_Advanced_Qty' ) ) :

final class PWBE_Woo_Advanced_Qty {

	function __construct() {
		add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
	}

	function pwbe_product_columns( $columns ) {
        $new_columns = array();

        $new_columns[] = array(
            'name' => __( 'Minimum', 'woo-advanced-qty' ),
            'type' => 'number',
            'table' => 'meta',
            'field' => '_advanced-qty-min',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $new_columns[] = array(
            'name' => __( 'Maximum', 'woo-advanced-qty' ),
            'type' => 'number',
            'table' => 'meta',
            'field' => '_advanced-qty-max',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $new_columns[] = array(
            'name' => __( 'Step', 'woo-advanced-qty' ),
            'type' => 'number',
            'table' => 'meta',
            'field' => '_advanced-qty-step',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $new_columns[] = array(
            'name' => __( 'Standard value', 'woo-advanced-qty' ),
            'type' => 'number',
            'table' => 'meta',
            'field' => '_advanced-qty-value',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $new_columns[] = array(
            'name' => __( 'Price suffix', 'woo-advanced-qty' ),
            'type' => 'text',
            'table' => 'meta',
            'field' => '_advanced-qty-price-suffix',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $new_columns[] = array(
            'name' => __( 'Quantity suffix', 'woo-advanced-qty' ),
            'type' => 'text',
            'table' => 'meta',
            'field' => '_advanced-qty-quantity-suffix',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
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
}

new PWBE_Woo_Advanced_Qty();

endif;

?>