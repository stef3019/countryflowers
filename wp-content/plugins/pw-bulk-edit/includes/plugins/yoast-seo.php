<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !defined( 'WPSEO_VERSION' ) ) {
	return;
}

if ( ! class_exists( 'PWBE_Yoast_SEO' ) ) :

final class PWBE_Yoast_SEO {

	function __construct() {
		add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
		add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
        add_filter( 'pwbe_skip_sorting_options', array( $this, 'pwbe_skip_sorting_options' ) );
	}

	function pwbe_select_options( $select_options ) {
        $categories = array();
        $terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
        if ( !is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$select_options['_yoast_wpseo_primary_product_cat'][ $term->term_id ]['name'] = $term->name;
				$select_options['_yoast_wpseo_primary_product_cat'][ $term->term_id ]['visibility'] = 'both';
			}
		}

		$select_options['_yoast_wpseo_meta-robots-noindex'][PW_Bulk_Edit::NULL]['name'] = __( 'Default for Products', 'wordpress-seo' );
		$select_options['_yoast_wpseo_meta-robots-noindex'][PW_Bulk_Edit::NULL]['visibility'] = 'both';
		$select_options['_yoast_wpseo_meta-robots-noindex']['']['name'] = __( 'Default for Products', 'wordpress-seo' );
		$select_options['_yoast_wpseo_meta-robots-noindex']['']['visibility'] = 'both';

		$select_options['_yoast_wpseo_meta-robots-noindex']['2']['name'] = __( 'Yes', 'wordpress-seo' );
		$select_options['_yoast_wpseo_meta-robots-noindex']['2']['visibility'] = 'both';

		$select_options['_yoast_wpseo_meta-robots-noindex']['1']['name'] = __( 'No', 'wordpress-seo' );
		$select_options['_yoast_wpseo_meta-robots-noindex']['1']['visibility'] = 'both';

	    return $select_options;
	}

	function pwbe_product_columns( $columns ) {
		$columns[] = array(
			'name' => 'Primary category',
			'type' => 'select',
			'table' => 'meta',
			'field' => '_yoast_wpseo_primary_product_cat',
			'readonly' => 'false',
			'visibility' => 'parent',
			'sortable' => 'false',
			'views' => array( 'all' )
		);

		$columns[] = array(
			'name' => __( 'SEO title', 'wordpress-seo' ),
			'type' => 'text',
			'table' => 'meta',
			'field' => '_yoast_wpseo_title',
			'readonly' => 'false',
			'visibility' => 'parent',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		$columns[] = array(
			'name' => 'SEO ' . __( 'Meta description', 'wordpress-seo' ),
			'type' => 'text',
			'table' => 'meta',
			'field' => '_yoast_wpseo_metadesc',
			'readonly' => 'false',
			'visibility' => 'parent',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		$columns[] = array(
			'name' => 'SEO ' . __( 'Focus keyword', 'wordpress-seo' ),
			'type' => 'text',
			'table' => 'meta',
			'field' => '_yoast_wpseo_focuskw',
			'readonly' => 'false',
			'visibility' => 'parent',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		$columns[] = array(
			'name' => 'SEO Allow search engines to show product',
			'type' => 'select',
			'table' => 'meta',
			'field' => '_yoast_wpseo_meta-robots-noindex',
			'readonly' => 'false',
			'visibility' => 'parent',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		return $columns;
	}

    function pwbe_skip_sorting_options( $options ) {
        $options[] = '_yoast_wpseo_meta-robots-noindex';

        return $options;
    }
}

new PWBE_Yoast_SEO();

endif;

?>