<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

//
// WooCommerce Coming Soon Product by Terry Tsang
// https://terrytsang.com/shop/shop/woocommerce-coming-soon-product/
//
if ( ! class_exists( 'WC_Coming_Soon_Product' ) ) {
	return;
}

if ( ! class_exists( 'PWBE_WC_Coming_Soon_Product' ) ) :

final class PWBE_WC_Coming_Soon_Product {

	function __construct() {
        add_filter( 'pwbe_filter_types', array( $this, 'pwbe_filter_types' ) );
        add_filter( 'pwbe_common_joins', array( $this, 'pwbe_common_joins' ) );
		add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
	}

    function pwbe_filter_types( $filter_types ) {
        $filter_types[ 'meta__set_coming_soon' ] = array( 'name' => __( 'Set for Coming Soon?', 'wc-coming-soon-product' ), 'type' => 'boolean' );

        return $filter_types;
    }

    function pwbe_common_joins( $common_joins ) {
        global $wpdb;

        if ( strpos( $common_joins, 'meta__set_coming_soon' ) === false ) {
            $common_joins .= "
                LEFT JOIN
                    {$wpdb->postmeta} AS meta__set_coming_soon ON (meta__set_coming_soon.post_id = post.ID AND meta__set_coming_soon.meta_key = '_set_coming_soon')
            ";
        }

        return $common_joins;
    }

    function pwbe_product_columns( $columns ) {

        $new_columns[] = array(
            'name' => __( 'Set for Coming Soon?', 'wc-coming-soon-product' ),
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => '_set_coming_soon',
            'readonly' => 'false',
            'visibility' => 'simple',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $new_columns[] = array(
            'name' => __( 'Coming Soon Label', 'wc-coming-soon-product' ),
            'type' => 'text',
            'table' => 'meta',
            'field' => '_coming_soon_label',
            'readonly' => 'false',
            'visibility' => 'simple',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $new_columns[] = array(
            'name' => __( 'Show Countdown Clock?', 'wc-coming-soon-product' ),
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => '_coming_soon_countdown',
            'readonly' => 'false',
            'visibility' => 'simple',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $new_columns[] = array(
            'name' => __( 'Launch Date', 'wc-coming-soon-product' ),
            'type' => 'text',
            'table' => 'meta',
            'field' => '_coming_soon_countdown_date',
            'readonly' => 'false',
            'visibility' => 'simple',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $new_columns[] = array(
            'name' => __( 'Additional Link Text', 'wc-coming-soon-product' ),
            'type' => 'text',
            'table' => 'meta',
            'field' => '_coming_soon_link_text',
            'readonly' => 'false',
            'visibility' => 'simple',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $new_columns[] = array(
            'name' => __( 'Additional Link URL', 'wc-coming-soon-product' ),
            'type' => 'text',
            'table' => 'meta',
            'field' => '_coming_soon_link_url',
            'readonly' => 'false',
            'visibility' => 'simple',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $start_index = 1;
        foreach ( $columns as $index => $column ) {
            if ( $column['field'] === '_sold_individually' ) {
                $start_index = $index + 1;
                break;
            }
        }

        array_splice( $columns, $start_index, 0, $new_columns );

        return $columns;
    }
}

new PWBE_WC_Coming_Soon_Product();

endif;

?>