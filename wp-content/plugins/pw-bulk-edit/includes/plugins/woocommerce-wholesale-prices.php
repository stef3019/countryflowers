<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// WooCommerce Wholesale Prices plugin
// https://wordpress.org/plugins/woocommerce-wholesale-prices/

if ( !class_exists( 'WooCommerceWholeSalePrices' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_WooCommerceWholeSalePrices' ) ) :

final class PWBE_WooCommerceWholeSalePrices {

    function __construct() {
        add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
    }

    function pwbe_product_columns( $columns ) {

        $wwp_wholesale_roles  = WWP_Wholesale_Roles::getInstance();
        $all_wholesale_roles = $wwp_wholesale_roles->getAllRegisteredWholesaleRoles();

        foreach( $all_wholesale_roles as $role_key => $role ) {
            $columns[] = array(
                'name' => sprintf( __( '%s Price', 'woocommerce-wholesale-prices' ), $role[ 'roleName' ] ),
                'type' => 'currency',
                'table' => 'meta',
                'field' => $role_key . '_wholesale_price',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all' )
            );


        }

        if ( WWP_Helper_Functions::is_plugin_active( 'woocommerce-wholesale-prices-premium/woocommerce-wholesale-prices-premium.bootstrap.php' ) ) {
            $columns[] = array(
                'name' => __( 'Cost of Good', 'woocommerce-wholesale-prices' ),
                'type' => 'currency',
                'table' => 'meta',
                'field' => '_wc_cog_cost',
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all' )
            );

            foreach( $all_wholesale_roles as $role_key => $role ) {
                $columns[] = array(
                    'name' => sprintf( __( '%s Min Order Qty', 'woocommerce-wholesale-prices' ), $role[ 'roleName' ] ),
                    'type' => 'number',
                    'table' => 'meta',
                    'field' => $role_key . '_wholesale_minimum_order_quantity',
                    'readonly' => 'false',
                    'visibility' => 'both',
                    'sortable' => 'true',
                    'views' => array( 'all' )
                );
            }

            foreach( $all_wholesale_roles as $role_key => $role ) {
                $columns[] = array(
                    'name' => sprintf( __( '%s Order Qty Step', 'woocommerce-wholesale-prices' ), $role[ 'roleName' ] ),
                    'type' => 'number',
                    'table' => 'meta',
                    'field' => $role_key . '_wholesale_order_quantity_step',
                    'readonly' => 'false',
                    'visibility' => 'both',
                    'sortable' => 'true',
                    'views' => array( 'all' )
                );
            }
        }

        return $columns;
    }
}

new PWBE_WooCommerceWholeSalePrices();

endif;

?>