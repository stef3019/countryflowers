<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'WC_Facebook_Product' ) ) {
	return;
}

if ( ! class_exists( 'PWBE_WC_Facebookcommerce_Integration' ) ) :

final class PWBE_WC_Facebookcommerce_Integration {

	function __construct() {
        add_filter( 'pwbe_filter_types', array( $this, 'pwbe_filter_types' ) );
        add_filter( 'pwbe_common_joins', array( $this, 'pwbe_common_joins' ) );
		add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
        add_filter( 'pwbe_save_field_value', array( $this, 'pwbe_save_field_value' ), 10, 2 );
	}

    function pwbe_filter_types( $filter_types ) {
        $filter_types[ 'meta__fb_visibility' ] = array( 'name' => 'Facebook visibility', 'type' => 'boolean' );
        $filter_types[ 'meta__fb_sync_status' ] = array( 'name' => 'Facebook sync', 'type' => 'boolean' );

        return $filter_types;
    }

    function pwbe_common_joins( $common_joins ) {
        global $wpdb;

        $common_joins .= "
            LEFT JOIN
                {$wpdb->postmeta} AS meta__fb_visibility ON (meta__fb_visibility.post_id = post.ID AND meta__fb_visibility.meta_key = 'fb_visibility')
            LEFT JOIN
                {$wpdb->postmeta} AS meta__fb_sync_status ON (meta__fb_sync_status.post_id = post.ID AND meta__fb_sync_status.meta_key = 'fb_sync_status')
        ";
        return $common_joins;
    }

	function pwbe_product_columns( $columns ) {
        $columns[] = array(
            'name' => 'Facebook visibility',
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => 'fb_visibility',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all' ),
            'checked_value' => '1',
            'unchecked_value' => '0',
        );

        $columns[] = array(
            'name' => 'Sync to Facebook',
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => 'fb_sync_status',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all' ),
            'checked_value' => '1',
            'unchecked_value' => '0',
        );

        $columns[] = array(
            'name' => 'Facebook Description',
            'type' => 'textarea',
            'table' => 'meta',
            'field' => 'fb_product_description',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all' ),
        );

		return $columns;
	}

    function pwbe_save_field_value( $value, $field ) {
        if ( $field['field'] == 'fb_sync_status' || $field['field'] == 'fb_visibility' ) {
            if ( $value == 'yes' ) {
                $value = '1';
            } else {
                $value = '0';
            }
        }

        return $value;
    }
}

new PWBE_WC_Facebookcommerce_Integration();

endif;

?>