<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Atum\Components\AtumCapabilities' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_ATUM' ) ) :

final class PWBE_ATUM {

    private $atum_fields;
    private $product_data_table;
    private $items_table;
    private $itemmeta_table;

    function __construct() {
        global $wpdb;

        add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
        add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
        add_filter( 'pwbe_results_product_initial', array( $this, 'pwbe_results_product_initial' ) );
        add_filter( 'pwbe_save_field_value', array( $this, 'pwbe_save_field_value' ), 10, 2 );
        add_filter( 'pwbe_common_fields', array( $this, 'pwbe_common_fields' ) );

        $this->product_data_table = $wpdb->prefix . Atum\Inc\Globals::ATUM_PRODUCT_DATA_TABLE;
        $this->items_table        = $wpdb->prefix . Atum\Components\AtumOrders\AtumOrderPostType::ORDER_ITEMS_TABLE;
        $this->itemmeta_table     = $wpdb->prefix . Atum\Components\AtumOrders\AtumOrderPostType::ORDER_ITEM_META_TABLE;

        $this->atum_fields = array(
            '_purchase_price' => array(
                'name' => __( 'Purchase Price', 'atum' ),
                'type' => 'currency',
            ),
            '_supplier_id' => array(
                'name' => __( 'Supplier', 'atum' ),
                'type' => 'select',
            ),
            '_supplier_sku' => array(
                'name' => __( "Supplier's SKU", 'atum' ),
                'type' => 'text',
            ),
        );
    }

    function pwbe_select_options( $select_options ) {
        global $wpdb;

        $post_statuses = Atum\Components\AtumCapabilities::current_user_can('edit_private_suppliers' ) ? array( 'private', 'publish' ) : array( 'publish' );

        $query = $wpdb->prepare(
            "SELECT DISTINCT ID, post_title from $wpdb->posts
             WHERE post_type = %s
             AND post_status IN ('" . implode( "','", $post_statuses ) . "')",
            Atum\Suppliers\Suppliers::POST_TYPE
        );

        $suppliers = $wpdb->get_results( $query );

        foreach ( $suppliers as $supplier ) {
            $select_options['_supplier_id'][ $supplier->ID ]['name'] = $supplier->post_title;
            $select_options['_supplier_id'][ $supplier->ID ]['visibility'] = 'both';
        }

        return $select_options;
    }

    function pwbe_product_columns( $columns ) {
        foreach ( $this->atum_fields as $field => $settings ) {
            $columns[] = array(
                'name' => $settings['name'],
                'type' => $settings['type'],
                'table' => 'atum',
                'field' => $field,
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' )
            );
        }

        return $columns;
    }

    function pwbe_results_product_initial( $pwbe_product ) {
        if ( !isset( $pwbe_product->atum_product ) ) {
            $pwbe_product->atum_product = Atum\Inc\Helpers::get_atum_product( $pwbe_product->post_id );
        }

        if ( ! Atum\Inc\Helpers::is_atum_product( $pwbe_product->atum_product ) ) {
            return;
        }

        $pwbe_product->_purchase_price = $pwbe_product->atum_product->get_purchase_price();
        $pwbe_product->_supplier_id = $pwbe_product->atum_product->get_supplier_id();
        $pwbe_product->_supplier_sku = $pwbe_product->atum_product->get_supplier_sku();

        return $pwbe_product;
    }

    function pwbe_save_field_value( $value, $field ) {
        if ( ! isset( $this->atum_fields[ $field['field'] ] ) ) {
            return $value;
        }

        $atum_product = Atum\Inc\Helpers::get_atum_product( $field['post_id'] );
        if ( ! Atum\Inc\Helpers::is_atum_product( $atum_product ) ) {
            return $value;
        }

        switch ( $field['field'] ) {
            case '_purchase_price':
                $atum_product->set_purchase_price( $value );
            break;

            case '_supplier_id':
                $atum_product->set_supplier_id( $value );
            break;

            case '_supplier_sku':
                $atum_product->set_supplier_sku( $value );
            break;
        }

        $atum_product->save_atum_data();

        return $value;
    }

    function pwbe_common_fields( $common_fields ) {
        global $wpdb;

        if ( isset( $_POST['order_by'] ) && isset( $this->atum_fields[ $_POST['order_by'] ] ) ) {
            $field = $_POST['order_by'];

            $common_fields .= ", (SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = post.ID AND meta_key = '$field') AS `$field`";
        }
        return $common_fields;
    }
}

new PWBE_ATUM();

endif;

?>