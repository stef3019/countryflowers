<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !defined( 'YITH_WCBM' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_YITH_WCBM' ) ) :

final class PWBE_YITH_WCBM {

    function __construct() {
        add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
        add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
        add_filter( 'pwbe_skip_sorting_options', array( $this, 'pwbe_skip_sorting_options' ) );
    }

    function pwbe_select_options( $select_options ) {
        $args   = ( array(
            'posts_per_page'   => -1,
            'post_type'        => 'yith-wcbm-badge',
            'orderby'          => 'title',
            'order'            => 'ASC',
            'post_status'      => 'publish',
            'suppress_filters' => false,
        ) );
        $badges = get_posts( $args );

        $select_options['_yith_wcbm_product_meta___id_badge']['']['name'] = __( 'none', 'yith-woocommerce-badges-management' );
        $select_options['_yith_wcbm_product_meta___id_badge']['']['visibility'] = 'both';

        foreach ( $badges as $badge ) {
            $select_options['_yith_wcbm_product_meta___id_badge'][ $badge->ID ]['name'] = get_the_title( $badge->ID );
            $select_options['_yith_wcbm_product_meta___id_badge'][ $badge->ID ]['visibility'] = 'both';
        }

        return $select_options;
    }

    function pwbe_product_columns( $columns ) {
        $columns[] = array(
            'name' => __( 'Product Badge', 'yith-woocommerce-badges-management' ),
            'type' => 'select',
            'table' => 'meta',
            'field' => '_yith_wcbm_product_meta___id_badge',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        return $columns;
    }

    function pwbe_skip_sorting_options( $options ) {
        $options[] = '_yith_wcbm_product_meta___id_badge';

        return $options;
    }
}

new PWBE_YITH_WCBM();

endif;

?>