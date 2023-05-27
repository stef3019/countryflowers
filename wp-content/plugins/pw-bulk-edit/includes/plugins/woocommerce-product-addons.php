<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !defined( 'WC_PRODUCT_ADDONS_VERSION' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_WooCommerce_Product_Addons' ) ) :

final class PWBE_WooCommerce_Product_Addons {

    function __construct() {
        add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
        add_filter( 'pwbe_save_field_value', array( $this, 'pwbe_save_field_value' ), 10, 2 );
    }

    function pwbe_product_columns( $columns ) {
        $columns[] = array(
            'name' => __( 'Exclude from all Global Addons', 'woocommerce-product-addons' ),
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => '_product_addons_exclude_global',
            'visibility' => 'parent',
            'readonly' => false,
            'sortable' => 'true',
            'checked_value' => '1',
            'unchecked_value' => '0',
        );

        return $columns;
    }

    function pwbe_save_field_value( $value, $field ) {
        if ( $field['field'] == '_product_addons_exclude_global' ) {
            if ( $value == 'yes' ) {
                $value = '1';
            } else {
                $value = '0';
            }
        }

        return $value;
    }
}

new PWBE_WooCommerce_Product_Addons();

endif;

?>