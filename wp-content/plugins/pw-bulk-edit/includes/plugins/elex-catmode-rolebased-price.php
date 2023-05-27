<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Pricing_discounts_By_User_Role_WooCommerce' ) && ! class_exists( 'Elex_Pricing_discounts_By_User_Role_WooCommerce' ) ) {
	return;
}

if ( ! class_exists( 'PWBE_Pricing_discounts_By_User_Role_WooCommerce' ) ) :

final class PWBE_Pricing_discounts_By_User_Role_WooCommerce {

	function __construct() {
		add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
	}

	function pwbe_product_columns( $columns ) {
        global $wp_roles;

        $new_columns = array();

        $user_roles = get_option( 'eh_pricing_discount_product_price_user_role' );
        if ( is_array( $user_roles ) && !empty( $user_roles ) ) {
            foreach ( $user_roles as $value ) {
                $new_columns[] = array(
                    'name' => __( 'Role Based Price', 'elex-catmode-rolebased-price' ) . ' - ' . $wp_roles->role_names[ $value ],
                    'type' => 'currency',
                    'table' => 'meta',
                    'field' => 'product_role_based_price___' . $value . '___role_price',
                    'readonly' => 'false',
                    'visibility' => 'both',
                    'sortable' => 'true',
                    'views' => array( 'all', 'standard' ),
                );
            }

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
        }

		return $columns;
	}
}

new PWBE_Pricing_discounts_By_User_Role_WooCommerce();

endif;

?>