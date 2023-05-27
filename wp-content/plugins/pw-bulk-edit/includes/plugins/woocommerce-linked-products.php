<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
  exit;
}

if ( !defined( 'PWBE_LINKED_PRODUCTS' ) && 'no' === get_option( 'pwbe_linked_products', 'no' ) ) {
  return;
}

if ( ! class_exists( 'PWBE_LINKED_PRODUCTS' ) ) :

final class PWBE_LINKED_PRODUCTS {

  function __construct() {
    if ( defined( 'PWBE_LINKED_PRODUCTS_DIRECT_QUERY' ) ) {
      add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options_raw' ) );
    } else {
      add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
    }

    add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
  }

  function pwbe_select_options( $select_options ) {
    global $wpdb;

    $simple_products = array();
    $variable_products = array();
    $variation_products = array();

    if ( !defined( 'PWBE_LINKED_PRODUCTS_IGNORE_SIMPLE_PRODUCTS' ) ) {
      $simple_products = wc_get_products( array(
         'type' => 'simple',
         'limit' => -1,
      ) );
    }

    if ( !defined( 'PWBE_LINKED_PRODUCTS_IGNORE_VARIABLE_PRODUCTS' ) ) {
      $variable_products = wc_get_products( array(
         'type' => 'variable',
         'limit' => -1,
      ) );
    }

    if ( !defined( 'PWBE_LINKED_PRODUCTS_IGNORE_VARIATION_PRODUCTS' ) ) {
      $variation_products = wc_get_products( array(
         'type' => 'variation',
         'limit' => -1,
      ) );
    }

    $products = array_merge( $simple_products, $variable_products, $variation_products );

    foreach ( $products as $product ) {
      if ( !$product->is_type( 'variation' ) || $product->get_parent_id() > 0 ) {
        $select_options['_crosssell_ids___'][ $product->get_id() ]['name'] = $product->get_formatted_name();
        $select_options['_crosssell_ids___'][ $product->get_id() ]['visibility'] = 'parent';

        $select_options['_upsell_ids___'][ $product->get_id() ]['name'] = $product->get_formatted_name();
        $select_options['_upsell_ids___'][ $product->get_id() ]['visibility'] = 'parent';
      }
    }

    return $select_options;
  }

  function pwbe_select_options_raw( $select_options ) {
    global $wpdb;

    $results = $wpdb->get_results( "SELECT ID, post_parent, post_title FROM {$wpdb->posts} WHERE post_type IN ('product')" );

    if ( $results !== null ) {
      foreach ( $results as $row ) {
        $product_id = !empty( $row->post_parent ) ? $row->post_parent : $row->ID;
        $product_name = $row->post_title . ' (#' . $row->ID . ')';

        $select_options['_crosssell_ids___'][ $product_id ]['name'] = $product_name;
        $select_options['_crosssell_ids___'][ $product_id ]['visibility'] = 'parent';

        $select_options['_upsell_ids___'][ $product_id ]['name'] = $product_name;
        $select_options['_upsell_ids___'][ $product_id ]['visibility'] = 'parent';
      }
    }

    return $select_options;
  }

  function pwbe_product_columns( $columns ) {
    $insert_after_column = __( 'Shipping class', 'woocommerce' );

    $upsells = array(
        'name' => __( 'Upsells', 'woocommerce' ),
        'type' => 'multiselect',
        'table' => 'meta',
        'field' => '_upsell_ids___',
        'readonly' => 'false',
        'visibility' => 'parent',
        'sortable' => 'true',
        'views' => array( 'all' )
    );

    $crosssells = array(
        'name' => __( 'Cross-sells', 'woocommerce' ),
        'type' => 'multiselect',
        'table' => 'meta',
        'field' => '_crosssell_ids___',
        'readonly' => 'false',
        'visibility' => 'parent',
        'sortable' => 'true',
        'views' => array( 'all' )
    );

    $insert_index = count( $columns );
    for ( $x = 0; $x < count( $columns ); $x++ ) {
      if ( $columns[ $x ]['name'] === $insert_after_column ) {
        $insert_index = $x + 1;
        break;
      }
    }

    array_splice( $columns, $insert_index, 0, array( $upsells, $crosssells ) );

    return $columns;
  }
}

new PWBE_LINKED_PRODUCTS();

endif;

?>