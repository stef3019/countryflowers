<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

//
// Product Prices by User Roles for WooCommerce Pro
// by Tyche Softwares
//
if ( !class_exists( 'Alg_WC_Price_By_User_Role' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_Alg_WC_Price_By_User_Role' ) ) :

final class PWBE_Alg_WC_Price_By_User_Role {

    function __construct() {
        add_filter( 'pwbe_product_columns', array( $this, 'product_columns' ) );
    }

    function product_columns( $columns ) {
        $new_columns = array();

        $new_columns[] = array(
            'name' => 'Prices By User Role',
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => '_alg_wc_price_by_user_role_per_product_settings_enabled',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
            'checked_value' => 'yes',
            'unchecked_value' => 'no',
        );

        $visible_roles = get_option( 'alg_wc_price_by_user_role_per_product_show_roles', '' );
        foreach ( alg_get_user_roles() as $role_key => $role_data ) {
            if ( ! empty( $visible_roles ) ) {
                if ( ! in_array( $role_key, $visible_roles, true ) ) {
                    continue;
                }
            }

            $new_columns[] = array(
                'name' => $role_data['name'] . ' ' . __( 'Regular price', 'price-by-user-role-for-woocommerce' ),
                'type' => 'currency',
                'table' => 'meta',
                'field' => '_alg_wc_price_by_user_role_regular_price_' . $role_key,
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );

            $new_columns[] = array(
                'name' => $role_data['name'] . ' ' . __( 'Sale price', 'price-by-user-role-for-woocommerce' ),
                'type' => 'currency',
                'table' => 'meta',
                'field' => '_alg_wc_price_by_user_role_sale_price_' . $role_key,
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );

            $new_columns[] = array(
                'name' => $role_data['name'] . ' ' . __( 'Make "empty price"', 'price-by-user-role-for-woocommerce' ),
                'type' => 'checkbox',
                'table' => 'meta',
                'field' => '_alg_wc_price_by_user_role_empty_price_' . $role_key,
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
                'checked_value' => 'yes',
                'unchecked_value' => 'no',
            );
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

        return $columns;
    }
}

new PWBE_Alg_WC_Price_By_User_Role();

endif;

?>