<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Affiliate_WP' ) ) {
	return;
}

if ( ! class_exists( 'PWBE_Affiliate_WP' ) ) :

final class PWBE_Affiliate_WP {

	function __construct() {
		add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
        add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
        add_filter( 'pwbe_skip_sorting_options', array( $this, 'pwbe_skip_sorting_options' ) );
        add_filter( 'pwbe_save_field_value', array( $this, 'pwbe_save_field_value' ), 10, 2 );
	}

	function pwbe_product_columns( $columns ) {
        $new_columns = array();

        $new_columns[] = array(
            'name' => __( 'Affiliate Rate Type', 'affiliate-wp' ),
            'type' => 'select',
            'table' => 'meta',
            'field' => '_affwp_woocommerce_product_rate_type',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $new_columns[] = array(
            'name' => __( 'Affiliate Rate', 'affiliate-wp' ),
            'type' => 'currency',
            'table' => 'meta',
            'field' => '_affwp_woocommerce_product_rate',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $new_columns[] = array(
            'name' => __( 'Disable referrals', 'affiliate-wp' ),
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => '_affwp_woocommerce_referrals_disabled',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
            'checked_value' => '1',
            'unchecked_value' => '0',
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

    function pwbe_select_options( $select_options ) {
        $select_options['_affwp_woocommerce_product_rate_type'][PW_Bulk_Edit::NULL]['name'] = __( 'Site Default', 'affiliate-wp' );
        $select_options['_affwp_woocommerce_product_rate_type'][PW_Bulk_Edit::NULL]['visibility'] = 'both';
        $select_options['_affwp_woocommerce_product_rate_type']['']['name'] = __( 'Site Default', 'affiliate-wp' );
        $select_options['_affwp_woocommerce_product_rate_type']['']['visibility'] = 'both';

        foreach ( affwp_get_affiliate_rate_types() as $option_slug => $option_name ) {
            $select_options['_affwp_woocommerce_product_rate_type'][ $option_slug ]['name'] = esc_html( $option_name );
            $select_options['_affwp_woocommerce_product_rate_type'][ $option_slug ]['visibility'] = 'both';
        }

        return $select_options;
    }

    function pwbe_skip_sorting_options( $options ) {
        $options[] = '_affwp_woocommerce_product_rate_type';

        return $options;
    }

    function pwbe_save_field_value( $value, $field ) {
        if ( $field['field'] == '_affwp_woocommerce_referrals_disabled' ) {
            if ( $value == 'yes' ) {
                $value = '1';
            } else {
                $value = PW_Bulk_Edit::NULL;
            }
        }

        return $value;
    }
}

new PWBE_Affiliate_WP();

endif;

?>