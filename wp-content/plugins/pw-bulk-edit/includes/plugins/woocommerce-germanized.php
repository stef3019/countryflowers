<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WooCommerce_Germanized' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_WooCommerce_Germanized' ) ) :

final class PWBE_WooCommerce_Germanized {

    function __construct() {
        add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
        add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
        add_action( 'pwbe_after_save_products', array( $this, 'pwbe_after_save_products' ) );
    }

    function pwbe_select_options( $select_options ) {
        $sale_labels = array_merge( array( "-1" => __( 'Select Price Label', 'woocommerce-germanized' ) ), WC_germanized()->price_labels->get_labels() );
        foreach ( $sale_labels as $sale_id => $sale_label ) {
            $select_options['_sale_price_label'][ $sale_id ]['name'] = $sale_label;
            $select_options['_sale_price_label'][ $sale_id ]['visibility'] = 'both';
            $select_options['_sale_price_regular_label'][ $sale_id ]['name'] = $sale_label;
            $select_options['_sale_price_regular_label'][ $sale_id ]['visibility'] = 'both';
        }

        $units = array_merge( array( "-1" => __( 'Select unit', 'woocommerce-germanized' ) ), WC_germanized()->units->get_units() );
        foreach ( $units as $id => $unit ) {
            $select_options['_unit'][ $id ]['name'] = $unit;
            $select_options['_unit'][ $id ]['visibility'] = 'both';
        }

        return $select_options;
    }

    function pwbe_product_columns( $columns ) {
        $columns[] = array(
            'name' => __( 'Sale Label', 'woocommerce-germanized' ),
            'type' => 'select',
            'table' => 'meta',
            'field' => '_sale_price_label',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $columns[] = array(
            'name' => __( 'Sale Regular Label', 'woocommerce-germanized' ),
            'type' => 'select',
            'table' => 'meta',
            'field' => '_sale_price_regular_label',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $columns[] = array(
            'name' => __( 'Unit', 'woocommerce-germanized' ),
            'type' => 'select',
            'table' => 'meta',
            'field' => '_unit',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $columns[] = array(
            'name' => __( 'Product Units', 'woocommerce-germanized' ),
            'type' => 'number',
            'table' => 'meta',
            'field' => '_unit_product',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $columns[] = array(
            'name' => __( 'Base Price Units', 'woocommerce-germanized' ),
            'type' => 'number',
            'table' => 'meta',
            'field' => '_unit_base',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        if ( isset( $GLOBALS['woocommerce_germanized'] ) && $GLOBALS['woocommerce_germanized']->is_pro() ) {
            $columns[] = array(
                'name' => __( 'Calculate base prices automatically.', 'woocommerce-germanized' ),
                'type' => 'checkbox',
                'table' => 'meta',
                'field' => '_unit_price_auto',
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all' )
            );
        }

        $columns[] = array(
            'name' => __( 'Regular Base Price', 'woocommerce-germanized' ),
            'type' => 'currency',
            'table' => 'meta',
            'field' => '_unit_price_regular',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $columns[] = array(
            'name' => __( 'Sale Base Price', 'woocommerce-germanized' ),
            'type' => 'currency',
            'table' => 'meta',
            'field' => '_unit_price_sale',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        return $columns;
    }

    function pwbe_after_save_products( $fields ) {
        if ( isset( $GLOBALS['woocommerce_germanized'] ) && $GLOBALS['woocommerce_germanized']->is_pro() === false ) {
            return;
        }

        $save = new PWBE_Save_Products();
        $columns = PWBE_Columns::get();

        $calculation_fields = array(
            '_regular_price',
            '_sale_price',
            '_unit_price_auto',
            '_unit_product',
            '_unit_base',
        );

        if ( function_exists( 'wc_get_price_decimals' ) ) {
            $price_decimals = wc_get_price_decimals();
        } else {
            $price_decimals = absint( apply_filters( 'wc_get_price_decimals', get_option( 'woocommerce_price_num_decimals', 2 ) ) );
        }

        $calculate_product_ids = array();
        foreach( $fields as $field ) {
            if ( !isset( $field['field'] ) ) {
                continue;
            }

            $table = $save->get_column_value( $columns, $field['field'], 'table' );

            if ( $table == 'meta' && in_array( $field['field'], $calculation_fields ) ) {

                if ( !in_array( $field['post_id'], $calculate_product_ids ) ) {
                    $calculate_product_ids[] = $field['post_id'];
                }
            }
        }

        foreach ( $calculate_product_ids as $product_id ) {
            $product = wc_get_product( $product_id );
            if ( !$product ) {
                continue;
            }

            $unit_price_auto = get_post_meta( $product_id, '_unit_price_auto', true );
            if ( !boolval( $unit_price_auto ) ) {
                continue;
            }

            $regular_price = get_post_meta( $product_id, '_regular_price', true );
            $sale_price = get_post_meta( $product_id, '_sale_price', true );

            $unit_product = get_post_meta( $product_id, '_unit_product', true );
            $unit_base = get_post_meta( $product_id, '_unit_base', true );

            if ( empty( $unit_product ) ) {
                $unit_product = $unit_base;
            }

            if ( !empty( $unit_product ) && !empty( $unit_base ) ) {
                $unit_price_regular = $regular_price / $unit_product * $unit_base;
                $unit_price_sale = $sale_price / $unit_product * $unit_base;

                update_post_meta( $product_id, '_unit_price_regular', round( $unit_price_regular, $price_decimals ) );
                update_post_meta( $product_id, '_unit_price_sale', round( $unit_price_sale, $price_decimals ) );
            }
        }
    }
}

new PWBE_WooCommerce_Germanized();

endif;

?>