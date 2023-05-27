<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
  exit;
}

if ( !defined( 'TZ_FEATURE_PACK_URL' ) ) {
  return;
}

if ( ! class_exists( 'PWBE_TZ_FEATURE_PACK' ) ) :

final class PWBE_TZ_FEATURE_PACK {

  function __construct() {
    add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
    add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
    add_filter( 'pwbe_taxonomy_list', array( $this, 'pwbe_taxonomy_list' ) );
  }

  function pwbe_select_options( $select_options ) {
    global $wpdb;

    foreach ( get_terms( array( 'taxonomy' => 'product-custom-label', 'hide_empty' => false ) ) as $term ) {
      $select_options['product-custom-label'][ $term->slug ]['name'] = esc_html( $term->name );
      $select_options['product-custom-label'][ $term->slug ]['visibility'] = 'parent';
    }

    return $select_options;
  }

  function pwbe_product_columns( $columns ) {
    $insert_after_column = __( 'Tags', 'woocommerce' );

    $custom_label = array(
        'name' => 'Custom Label',
        'type' => 'multiselect',
        'table' => 'terms',
        'field' => 'product-custom-label',
        'readonly' => 'false',
        'visibility' => 'parent',
        'sortable' => 'false',
        'views' => array( 'all' )
    );

    $insert_index = count( $columns );
    for ( $x = 0; $x < count( $columns ); $x++ ) {
      if ( $columns[ $x ]['name'] === $insert_after_column ) {
        $insert_index = $x + 1;
        break;
      }
    }

    array_splice( $columns, $insert_index, 0, array( $custom_label ) );

    return $columns;
  }

  function pwbe_taxonomy_list( $taxonomy_list ) {
    $taxonomy_list[] = 'product-custom-label';

    return $taxonomy_list;
  }
}

new PWBE_TZ_FEATURE_PACK();

endif;

?>