<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_YWBC_Backend' ) ) {
	return;
}

if ( ! class_exists( 'PWBE_YITH_YWBC' ) ) :

final class PWBE_YITH_YWBC {

	function __construct() {
		add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
        add_filter( 'pwbe_save_field_value', array( $this, 'pwbe_save_field_value' ), 10, 2 );
	}

	function pwbe_product_columns( $columns ) {
        $new_columns = array();

        $new_columns[] = array(
            'name' => __( 'YITH Barcodes', 'yith-woocommerce-barcodes' ),
            'type' => 'text',
            'table' => 'meta',
            'field' => '_ywbc_barcode_value',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        if ( !empty( $new_columns ) ) {
            // Insert after the "Sale End Date" column.
            $start_index = 1;
            foreach ( $columns as $index => $column ) {
                if ( $column['field'] === '_sale_price_dates_to' ) {
                    $start_index = $index + 1;
                    break;
                }
            }

            array_splice( $columns, $start_index, 0, $new_columns );
        }

		return $columns;
	}

    function pwbe_save_field_value( $value, $field ) {
        if ( $field['field'] == '_ywbc_barcode_value' ) {
            $yith = YITH_YWBC_Backend::get_instance();
            $yith->create_product_barcode( $field['post_id'], '', $value );
        }

        return $value;
    }
}

new PWBE_YITH_YWBC();

endif;

?>