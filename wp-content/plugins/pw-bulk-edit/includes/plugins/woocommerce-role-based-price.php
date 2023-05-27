<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !defined( 'WC_RBP_VARIABLE_VERSION' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_WC_RBP' ) ) :

final class PWBE_WC_RBP {

    function __construct() {
        add_filter( 'pwbe_product_columns', array( $this, 'product_columns' ) );
        add_filter( 'pwbe_save_field_value', array( $this, 'pwbe_save_field_value' ), 10, 2 );
    }

    function product_columns( $columns ) {
        if ( function_exists( 'wc_rbp_allowed_roles' ) && function_exists( 'wc_rbp_get_wp_roles' ) && function_exists( 'wc_rbp_allowed_price' ) && function_exists( 'wc_rbp_avaiable_price_type' ) ) {
            $new_columns = array();
            $allowed_roles    = wc_rbp_allowed_roles();
            $registered_roles = wc_rbp_get_wp_roles();
            $all_price_types = wc_rbp_avaiable_price_type();

            $new_columns[] = array(
                'name' => __( 'Enable Role Based Pricing', WC_RBP_TXT ),
                'type' => 'checkbox',
                'table' => 'meta',
                'field' => '_enable_role_based_price',
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
                'checked_value' => '1',
                'unchecked_value' => '0',
            );

            foreach ( $allowed_roles as $role_slug ) {
                if ( isset( $registered_roles[ $role_slug ] ) ) {
                    foreach ( wc_rbp_allowed_price() as $price_type_slug ) {
                        if ( isset( $all_price_types[ $price_type_slug ] ) ) {
                            $new_columns[] = array(
                                'name' => $registered_roles[ $role_slug ]['name'] . ' ' . wc_rbp_option( $price_type_slug . '_label', $all_price_types[ $price_type_slug ] ),
                                'type' => 'currency',
                                'table' => 'meta',
                                'field' => "_role_based_price___{$role_slug}___{$price_type_slug}",
                                'readonly' => 'false',
                                'visibility' => 'both',
                                'sortable' => 'true',
                                'views' => array( 'all', 'standard' ),
                            );
                        }
                    }
                }
            }

            if ( !empty( $new_columns ) ) {
                $start_index = 1;
                foreach ( $columns as $index => $column ) {
                    if ( $column['field'] === '_sale_price_dates_to' ) {
                        $start_index = $index + 1;
                        break;
                    }
                }

                array_splice( $columns, $start_index, 0, $new_columns );
            }
        }
        return $columns;
    }

    function pwbe_save_field_value( $value, $field ) {
        if ( $field['field'] == '_enable_role_based_price' ) {
            if ( $value == 'yes' ) {
                $value = '1';
            } else {
                $value = '0';
            }
        }

        return $value;
    }
}

new PWBE_WC_RBP();

endif;

?>