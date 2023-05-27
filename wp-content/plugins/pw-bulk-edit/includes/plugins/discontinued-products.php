<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Discontinued_Products' ) ) {
	return;
}

if ( ! class_exists( 'PWBE_WC_Discontinued_Products' ) ) :

final class PWBE_WC_Discontinued_Products {

	function __construct() {
		add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
        add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
        add_filter( 'pwbe_skip_sorting_options', array( $this, 'pwbe_skip_sorting_options' ) );
	}

	function pwbe_product_columns( $columns ) {
        $new_columns = array();

        $new_columns[] = array(
            'name' => __( 'Is Discontinued', 'woocommerce-discontinued-products' ),
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => '_is_discontinued',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $new_columns[] = array(
            'name' => __( 'Display text', 'woocommerce-discontinued-products' ),
            'type' => 'text',
            'table' => 'meta',
            'field' => '_discontinued_product_text',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $new_columns[] = array(
            'name' => __( 'Hide on shop / archive.', 'woocommerce-discontinued-products' ),
            'type' => 'select',
            'table' => 'meta',
            'field' => '_hide_from_shop',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $new_columns[] = array(
            'name' => __( 'Hide on search.', 'woocommerce-discontinued-products' ),
            'type' => 'select',
            'table' => 'meta',
            'field' => '_hide_from_search',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        // Insert after the "Sale End Date" column.
        $start_index = 1;
        foreach ( $columns as $index => $column ) {
            if ( $column['field'] === '_sale_price_dates_to' ) {
                $start_index = $index + 1;
                break;
            }
        }

        array_splice( $columns, $start_index, 0, $new_columns );

		return $columns;
	}

    function pwbe_select_options( $select_options ) {
        $select_options['_hide_from_shop'][PW_Bulk_Edit::NULL]['name'] = __( 'Default', 'woocommerce-discontinued-products' );
        $select_options['_hide_from_shop'][PW_Bulk_Edit::NULL]['visibility'] = 'both';
        $select_options['_hide_from_shop']['']['name'] = __( 'Default', 'woocommerce-discontinued-products' );
        $select_options['_hide_from_shop']['']['visibility'] = 'both';
        $select_options['_hide_from_shop']['hide']['name'] = __( 'Hide', 'woocommerce-discontinued-products' );
        $select_options['_hide_from_shop']['hide']['visibility'] = 'both';
        $select_options['_hide_from_shop']['show']['name'] = __( 'Show', 'woocommerce-discontinued-products' );
        $select_options['_hide_from_shop']['show']['visibility'] = 'both';

        $select_options['_hide_from_search'][PW_Bulk_Edit::NULL]['name'] = __( 'Default', 'woocommerce-discontinued-products' );
        $select_options['_hide_from_search'][PW_Bulk_Edit::NULL]['visibility'] = 'both';
        $select_options['_hide_from_search']['']['name'] = __( 'Default', 'woocommerce-discontinued-products' );
        $select_options['_hide_from_search']['']['visibility'] = 'both';
        $select_options['_hide_from_search']['hide']['name'] = __( 'Hide', 'woocommerce-discontinued-products' );
        $select_options['_hide_from_search']['hide']['visibility'] = 'both';
        $select_options['_hide_from_search']['show']['name'] = __( 'Show', 'woocommerce-discontinued-products' );
        $select_options['_hide_from_search']['show']['visibility'] = 'both';

        return $select_options;
    }

    function pwbe_skip_sorting_options( $options ) {
        $options[] = '_hide_from_shop';
        $options[] = '_hide_from_search';

        return $options;
    }
}

new PWBE_WC_Discontinued_Products();

endif;

?>