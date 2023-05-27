<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Woo_Variation_Swatches_Pro' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_Woo_Variation_Swatches_Pro' ) ) :

final class PWBE_Woo_Variation_Swatches_Pro {

    function __construct() {
        add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
        add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
        add_action( 'pwbe_after_save_products', array( $this, 'pwbe_after_save_products' ) );
    }

    function pwbe_select_options( $select_options ) {
        global $wpdb;

        $attribute_types              = wc_get_attribute_types();
        $attribute_types[ 'custom' ]  = esc_html__( 'Custom', 'woo-variation-swatches-pro' );

        $attributes = PWBE_Attributes::get_attributes();
        foreach ( $attributes as $attribute ) {
            $slug = $attribute['slug'];
            $field_name = '_wvs_product_attributes___' . $slug . '___type';

            foreach ( $attribute_types as $key => $attribute_type ) {
                $select_options[ $field_name ][ $key ]['name'] = $attribute_type;
                $select_options[ $field_name ][ $key ]['visibility'] = 'both';
            }
        }

        return $select_options;
    }

    function pwbe_product_columns( $columns ) {
        $new_columns = array();

        $attributes = PWBE_Attributes::get_attributes();
        foreach ( $attributes as $attribute ) {
            $slug = $attribute['slug'];
            $field_name = '_wvs_product_attributes___' . $slug . '___type';

            $new_columns[] = array(
                'name' => __( 'Swatches Settings', 'woo-variation-swatches-pro' ) . ' ' . __( 'Attribute Type', 'woo-variation-swatches-pro' ) . ' ' . $attribute['name'],
                'type' => 'select',
                'table' => 'meta',
                'field' => $field_name,
                'readonly' => 'false',
                'visibility' => 'parent',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' )
            );
        }

        if ( !empty( $new_columns ) ) {
            $start_index = 1;
            foreach ( $columns as $index => $column ) {
                // Insert the new columns after this column.
                if ( $column['field'] === 'product_shipping_class' ) {
                    $start_index = $index + 1;
                    break;
                }
            }

            array_splice( $columns, $start_index, 0, $new_columns );
        }

        return $columns;
    }

    function pwbe_after_save_products( $fields ) {
        foreach( $fields as $field ) {
            if ( !isset( $field['post_id'] ) ) {
                continue;
            }

            $product_id = $field['post_id'];

            if ( strpos( $field['field'], '_wvs_product_attributes___' ) !== false ) {
                $slug = str_replace( '_wvs_product_attributes___', '', $field['field'] );
                $slug = str_replace( '___type', '', $slug );

                $saved_product_attributes = (array) wvs_pro_get_product_option( $product_id );

                if ( isset( $saved_product_attributes[ $slug ]['terms'] ) ) {
                    foreach ( $saved_product_attributes[ $slug ]['terms'] as &$attribute_settings ) {
                        $attribute_settings['type'] = $saved_product_attributes[ $slug ]['type'];
                    }
                }

                update_post_meta( $product_id, '_wvs_product_attributes', $saved_product_attributes );
            }
        }
    }
}

new PWBE_Woo_Variation_Swatches_Pro();

endif;

?>