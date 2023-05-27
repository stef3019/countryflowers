<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Alg_WC_PQ' ) ) {
	return;
}

if ( ! class_exists( 'PWBE_Alg_WC_PQ' ) ) :

final class PWBE_Alg_WC_PQ {

	function __construct() {
		add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
	}

	function pwbe_product_columns( $columns ) {
        $new_columns = array();

        $new_columns[] = array(
            'name' => __( 'Minimum quantity', 'product-quantity-for-woocommerce' ),
            'type' => 'number',
            'table' => 'meta',
            'field' => '_alg_wc_pq_min',
            'readonly' => 'false',
            'visibility' => 'variation',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $new_columns[] = array(
            'name' => __( 'Maximum quantity', 'product-quantity-for-woocommerce' ),
            'type' => 'number',
            'table' => 'meta',
            'field' => '_alg_wc_pq_max',
            'readonly' => 'false',
            'visibility' => 'variation',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $new_columns[] = array(
            'name' => __( 'Quantity step', 'product-quantity-for-woocommerce' ),
            'type' => 'number',
            'table' => 'meta',
            'field' => '_alg_wc_pq_step',
            'readonly' => 'false',
            'visibility' => 'variation',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
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
}

new PWBE_Alg_WC_PQ();

endif;

?>