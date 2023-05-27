<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !function_exists( 'YITH_Pre_Order' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_YITH_Pre_Order' ) ) :

final class PWBE_YITH_Pre_Order {

    function __construct() {
        add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
        add_filter( 'pwbe_filter_types', array( $this, 'pwbe_filter_types' ) );
        add_filter( 'pwbe_search_row_sql', array( $this, 'pwbe_search_row_sql' ), 10, 6 );
        add_filter( 'pwbe_common_joins', array( $this, 'pwbe_common_joins' ) );
    }

    function pwbe_product_columns( $columns ) {
        $new_columns = array();

        $new_columns[] = array(
            'name' => __( 'Pre-Order', 'yith-pre-order-for-woocommerce' ),
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => '_ywpo_preorder',
            'readonly' => 'false',
            'visibility' => 'parent_variation',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' )
        );

        if ( !empty( $new_columns ) ) {
            $start_index = 1;
            foreach ( $columns as $index => $column ) {
                // Insert the new columns after this column:
                if ( $column['field'] === '_downloadable' ) {
                    $start_index = $index + 1;
                    break;
                }
            }

            array_splice( $columns, $start_index, 0, $new_columns );
        }

        return $columns;
    }

    function pwbe_filter_types( $filter_types ) {
        $filter_types['_ywpo_preorder'] = array( 'name' => 'Pre-Order?', 'type' => 'boolean' );

        return $filter_types;
    }

    function pwbe_search_row_sql( $row_sql, $search_class, $field_name, $filter_type, $field_value, $field_value2 ) {
        global $pwbe_sql_builder;

        if ( $field_name == '_ywpo_preorder' ) {
            $row_sql = $pwbe_sql_builder->boolean_search( 'meta__ywpo_preorder.meta_value', $filter_type, $field_value, $field_value2 );
        }

        return $row_sql;
    }

    function pwbe_common_joins( $common_joins ) {
        global $wpdb;

        $common_joins .= "
            LEFT JOIN
                {$wpdb->postmeta} AS meta__ywpo_preorder ON (meta__ywpo_preorder.post_id = post.ID AND meta__ywpo_preorder.meta_key = '_ywpo_preorder')
        ";
        return $common_joins;
    }
}

new PWBE_YITH_Pre_Order();

endif;

?>