<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Woocommerce_German_Market' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_Woocommerce_German_Market' ) ) :

final class PWBE_Woocommerce_German_Market {

    function __construct() {
        add_filter( 'pwbe_filter_types', array( $this, 'pwbe_filter_types' ) );
        add_action( 'pwbe_after_filter_select_templates', array( $this, 'pwbe_after_filter_select_templates' ) );
        add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
        add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
        add_filter( 'pwbe_skip_sorting_options', array( $this, 'pwbe_skip_sorting_options' ) );
        add_filter( 'pwbe_common_joins', array( $this, 'pwbe_common_joins' ) );
        add_filter( 'pwbe_search_row_sql', array( $this, 'pwbe_search_row_sql' ), 10, 6 );
    }

    function pwbe_filter_types( $filter_types ) {
        $filter_types['_lieferzeit'] = array( 'name' => __( 'Delivery Time', 'woocommerce' ), 'type' => 'attributes' );
        return $filter_types;
    }

    function pwbe_after_filter_select_templates() {
        ?>
        <span class="pwbe-multiselect pwbe-filter-_lieferzeit-container pwbe-filter-_lieferzeit-template" style="display: none;">
            <select name="filter_select[]" class="pwbe-filter-field pwbe-filter-select pwbe-filter-value" multiple="multiple" ></select>
        </span>
        <?php
    }

    function pwbe_select_options( $select_options ) {
        global $wpdb;

        $terms = get_terms( array( 'taxonomy' => 'product_delivery_times', 'hide_empty' => false ) );
        if ( !is_a( $terms, 'WP_Error' ) ) {
            foreach ( $terms as $term ) {
                $select_options['attribute__lieferzeit'][ $term->term_id ]['name'] = esc_html( $term->name );
                $select_options['attribute__lieferzeit'][ $term->term_id ]['visibility'] = 'both';
                $select_options['_lieferzeit'][ $term->term_id ]['name'] = esc_html( $term->name );
                $select_options['_lieferzeit'][ $term->term_id ]['visibility'] = 'both';
            }
        }

        return $select_options;
    }

    function pwbe_product_columns( $columns ) {
        $columns[] = array(
            'name' => __( 'Digital', 'woocommerce-german-market' ),
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => '_digital',
            'readonly' => 'false',
            'visibility' => 'parent_variation',
            'sortable' => 'false',
            'views' => array( 'all', 'standard' )
        );

        $columns[] = array(
            'name' => __( 'Delivery Time', 'woocommerce-german-market' ),
            'type' => 'select',
            'table' => 'meta',
            'field' => '_lieferzeit',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'false',
            'views' => array( 'all', 'standard' )
        );

        return $columns;
    }

    function pwbe_skip_sorting_options( $options ) {
        $options[] = 'attribute_lieferzeit';
        $options[] = '_lieferzeit';

        return $options;
    }

    function pwbe_common_joins( $common_joins ) {
        global $wpdb;

        $common_joins .= "
            LEFT JOIN
                {$wpdb->postmeta} AS meta__lieferzeit ON (meta__lieferzeit.post_id = post.ID AND meta__lieferzeit.meta_key = '_lieferzeit')
        ";

        return $common_joins;
    }

    function pwbe_search_row_sql( $row_sql, $search_class, $field_name, $filter_type, $field_value, $field_value2 ) {
        global $pwbe_sql_builder;

        if ( '_lieferzeit' == $field_name ) {
            $row_sql = $pwbe_sql_builder->multiselect_search( 'meta__lieferzeit.meta_value', $filter_type, $field_value );
        }

        return $row_sql;
    }
}

new PWBE_Woocommerce_German_Market();

endif;

?>