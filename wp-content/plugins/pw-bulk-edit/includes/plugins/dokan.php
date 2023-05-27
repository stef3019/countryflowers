<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WeDevs_Dokan' ) || ! function_exists( 'dokan' ) ) {
	return;
}

if ( ! class_exists( 'PWBE_WeDevs_Dokan' ) ) :

final class PWBE_WeDevs_Dokan {

	function __construct() {
        add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
        add_filter( 'pwbe_skip_sorting_options', array( $this, 'pwbe_skip_sorting_options' ) );
        add_filter( 'pwbe_filter_types', array( $this, 'pwbe_filter_types' ) );
        add_filter( 'pwbe_search_row_sql', array( $this, 'pwbe_search_row_sql' ), 10, 6 );
		add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
        add_filter( 'pwbe_common_fields', array( $this, 'pwbe_common_fields' ) );
	}

    function pwbe_select_options( $select_options ) {
        global $wpdb;
        global $user_ID;

        $admin_user = get_user_by( 'id', $user_ID );
        $vendors = dokan()->vendor->all(
            [
                'number' => -1,
                'role__in' => [ 'seller' ],
            ]
        );

        $select_options['post_author'][ $user_ID ]['name'] = esc_html( $admin_user->display_name );
        $select_options['post_author'][ $user_ID ]['visibility'] = 'both';

        foreach ( $vendors as $vendor ) {
            $vendor_name = ! empty( $vendor->get_shop_name() ) ? $vendor->get_shop_name() : $vendor->get_name();

            $select_options['post_author'][ $vendor->get_id() ]['name'] = esc_html( $vendor_name );
            $select_options['post_author'][ $vendor->get_id() ]['visibility'] = 'both';
        }

        foreach ( $select_options['post_author'] as $key => $option ) {
            $select_options['attribute_post_author'][ $key ] = $option;
        }

        return $select_options;
    }

    function pwbe_skip_sorting_options( $options ) {
        $options[] = 'post_author';

        return $options;
    }

    function pwbe_filter_types( $filter_types ) {
        $filter_types['post_author'] = array( 'name' => 'Vendor', 'type' => 'attributes' );

        return $filter_types;
    }

    function pwbe_search_row_sql( $row_sql, $search_class, $field_name, $filter_type, $field_value, $field_value2 ) {
        global $pwbe_sql_builder;

        if ( $field_name == 'post_author' ) {
            $row_sql = $pwbe_sql_builder->multiselect_search( 'post.post_author', $filter_type, $field_value );
        }

        return $row_sql;
    }

    function pwbe_product_columns( $columns ) {
        $new_columns = array();
        $vendors = dokan()->vendor->all(
            [
                'number' => -1,
                'role__in' => [ 'seller' ],
            ]
        );

        if ( ! empty( $vendors ) ) {
            $new_columns[] = array(
                'name' => 'Vendor',
                'type' => 'select',
                'table' => 'post',
                'field' => 'post_author',
                'readonly' => 'false',
                'visibility' => 'parent',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( !empty( $new_columns ) ) {
            // Insert after the "Sale End Date" column.
            $start_index = 1;
            foreach ( $columns as $index => $column ) {
                if ( $column['field'] === 'post_title' ) {
                    $start_index = $index + 1;
                    break;
                }
            }

            array_splice( $columns, $start_index, 0, $new_columns );
        }

        return $columns;
    }

    function pwbe_common_fields( $common_fields ) {
        if ( false === stripos( $common_fields, 'post.post_author' ) ) {
            $common_fields .= ', post.post_author';
        }

        return $common_fields;
    }
}

new PWBE_WeDevs_Dokan();

endif;

?>