<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !defined( 'WOOFV_VERSION' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_WOOFV' ) ) :

final class PWBE_WOOFV {

    function __construct() {
        add_filter( 'pwbe_product_columns', array( $this, 'product_columns' ) );
    }

    function product_columns( $columns ) {
        $columns[] = array(
            'name' => __( 'Featured Video', 'woofv' ),
            'type' => 'text',
            'table' => 'meta',
            'field' => '_woofv_video_embed___url',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        return $columns;
    }
}

new PWBE_WOOFV();

endif;

?>