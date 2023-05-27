<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPSubtitle' ) ) {
	return;
}

if ( ! class_exists( 'PWBE_WPSubtitle' ) ) :

final class PWBE_WPSubtitle {

	function __construct() {
		add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
	}

	function pwbe_product_columns( $columns ) {
        $new_columns = array();

        $new_columns[] = array(
            'name' => __( 'Subtitle', 'wp-subtitle' ),
            'type' => 'text',
            'table' => 'meta',
            'field' => 'wps_subtitle',
            'readonly' => 'false',
            'visibility' => 'both',
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

new PWBE_WPSubtitle();

endif;

?>