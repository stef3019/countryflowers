<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PWBE_Attributes' ) ) :

final class PWBE_Attributes {

	private static $attributes = null;
	private static $attribute_values = null;
	private static $custom_attributes = null;

	public static function get_attributes() {
		if ( PWBE_Attributes::$attributes === null ) {
			PWBE_Attributes::load();
		}
		return apply_filters( 'pwbe_attributes', PWBE_Attributes::$attributes );
	}

	public static function get_values() {
		if ( PWBE_Attributes::$attribute_values === null ) {
			PWBE_Attributes::load();
		}
		return apply_filters( 'pwbe_attribute_values', PWBE_Attributes::$attribute_values );
	}

	public static function get_custom_attributes() {
		if ( PWBE_Attributes::$custom_attributes === null ) {
			PWBE_Attributes::load();
		}
		return apply_filters( 'pwbe_custom_attributes', PWBE_Attributes::$custom_attributes );
	}

	private static function load() {
		global $wpdb;

		PWBE_Attributes::$attributes = array();
		PWBE_Attributes::$attribute_values = array();
		PWBE_Attributes::$custom_attributes = array();

		$slugs = array();

		$wc_attributes = wc_get_attribute_taxonomies();
		foreach ( $wc_attributes as $wc_attribute ) {
			$attribute_slug = 'pa_' . $wc_attribute->attribute_name;
			$slugs[] = $attribute_slug;

			PWBE_Attributes::add_attribute_to_list( $attribute_slug, $wc_attribute->attribute_label );
		}

		// Split this into 2 queries due to a customer's DB having different collations.
		$wc_taxonomy_results = PWBE_DB::query( "
			SELECT
				DISTINCT
				taxonomy.taxonomy,
				taxonomy.term_id
			FROM
				{$wpdb->term_taxonomy} AS taxonomy
			WHERE
				CONVERT(taxonomy.taxonomy USING utf8) IN (
					SELECT CONVERT(CONCAT('pa_', attribute_name) USING utf8) FROM {$wpdb->prefix}woocommerce_attribute_taxonomies
					UNION SELECT CONVERT('product_brand' USING utf8)
					UNION SELECT CONVERT('yith_product_brand' USING utf8)
					UNION SELECT CONVERT('product_shipping_class' USING utf8)
				)
			ORDER BY
				taxonomy.taxonomy
		" );

		while ( $wc_taxonomy = PWBE_DB::fetch_object( $wc_taxonomy_results ) ) {
			$wc_term_results = PWBE_DB::query( $wpdb->prepare( "
				SELECT
					DISTINCT
					terms.name,
					terms.slug
				FROM
					{$wpdb->terms} AS terms
				WHERE
					terms.term_id = %d
					AND terms.slug NOT IN ('pwbe_null_value')
				ORDER BY
					terms.name
			", $wc_taxonomy->term_id ) );

			while ( $wc_term = PWBE_DB::fetch_object( $wc_term_results ) ) {
				PWBE_Attributes::$attribute_values[ $wc_taxonomy->taxonomy ][ $wc_term->slug ] = $wc_term->name;
			}

			PWBE_DB::free_result( $wc_term_results );
		}

	 	PWBE_DB::free_result( $wc_taxonomy_results );

	 	if ( PWBE_LOAD_CUSTOM_PRODUCT_ATTRIBUTES ) {
			$products = $wpdb->get_results("
				SELECT
					postmeta.post_id,
					postmeta.meta_value
				FROM
					{$wpdb->postmeta} AS postmeta
				WHERE
					postmeta.meta_key = '_product_attributes'
					AND COALESCE(postmeta.meta_value, '') != ''
			");
			foreach ( $products as $product ) {
				$product_attributes = maybe_unserialize( $product->meta_value );

				if ( is_array( $product_attributes ) || is_object( $product_attributes ) ) {
					foreach ( $product_attributes as $attribute_slug => $product_attribute ) {
						if ( isset( $product_attribute['is_taxonomy'] ) && $product_attribute['is_taxonomy'] == '0' && $attribute_slug != 'product_shipping_class' ) {
							if ( !in_array( $attribute_slug, $slugs ) ) {
								$slugs[] = $attribute_slug;

								PWBE_Attributes::add_attribute_to_list( $attribute_slug, $product_attribute['name'] );
							}

							$values = array_map( 'trim', explode( ' ' . WC_DELIMITER . ' ', $product_attribute['value'] ) );
							foreach ( $values as $value ) {
								$value_slug = $value;
								PWBE_Attributes::$attribute_values[ $attribute_slug ][ $value_slug ] = $value;
								PWBE_Attributes::$custom_attributes[ $attribute_slug ][ $value_slug ][] = $product->post_id;
							}
						}
					}
				}
			}
		}

		foreach ( PWBE_Attributes::$attribute_values as $a => &$values ) {
			natcasesort($values);
		}

		foreach ( PWBE_Attributes::$custom_attributes as $a => &$values ) {
			ksort($values);
		}
	}

	private static function add_attribute_to_list( $slug, $label ) {

		// Prevent duplicates.
		foreach ( PWBE_Attributes::$attributes as $attribute ) {
			if ( $attribute['slug'] == $slug ) {
				return;
			}
		}

		PWBE_Attributes::$attributes[] = array(
			'slug' => $slug,
			'name' => $label
		);
	}
}

endif;

?>