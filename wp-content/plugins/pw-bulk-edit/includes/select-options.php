<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PWBE_Select_Options' ) ) :

final class PWBE_Select_Options {

	private static $select_options = null;

	public static function get() {
		if ( PWBE_Select_Options::$select_options === null ) {
			PWBE_Select_Options::load();
		}

		return PWBE_Select_Options::$select_options;
	}

	private static function load() {
		global $wpdb;
		global $wp_post_statuses;

		$select_options['product_type'] = array();
		if ( function_exists( 'wc_get_product_types' ) ) {
			$product_types = wc_get_product_types();
		} else {
			$product_types = array(
				'simple'   => __( 'Simple product', 'woocommerce' ),
				'grouped'  => __( 'Grouped product', 'woocommerce' ),
				'external' => __( 'External/Affiliate product', 'woocommerce' ),
				'variable' => __( 'Variable product', 'woocommerce' )
			);
		}

		foreach ( $product_types as $key => $product_type ) {
			$select_options['product_type'][$key]['name'] = $product_type;
			$select_options['product_type'][$key]['visibility'] = 'both';
		}

		$select_options['product_shipping_class'][PW_Bulk_Edit::NULL]['name'] = __( 'Same as parent', 'woocommerce' );
		$select_options['product_shipping_class'][PW_Bulk_Edit::NULL]['visibility'] = 'variation';
		$select_options['product_shipping_class']['']['name'] = __( 'No shipping class', 'woocommerce' );
		$select_options['product_shipping_class']['']['visibility'] = 'parent';

		if ( class_exists( 'YITH_Vendors' ) ) {
			foreach ( YITH_Vendors()->get_vendors() as $vendor ) {
				$select_options['yith_shop_vendor'][ $vendor->slug ]['name'] = esc_html( $vendor->term->name );
				$select_options['yith_shop_vendor'][ $vendor->slug ]['visibility'] = 'parent';
			}
			$select_options['yith_shop_vendor']['']['name'] = 'n/a';
			$select_options['yith_shop_vendor']['']['visibility'] = 'parent';
		}

		$values = PWBE_Attributes::get_values();
		foreach( $values as $taxonomy => $slugs ) {
			$select_options['attribute_' . $taxonomy]['']['name'] = '';
			$select_options['attribute_' . $taxonomy]['']['visibility'] = 'both';

			foreach( $slugs as $slug => $name ) {
				$slug = esc_html( $slug );
				$name = esc_html( $name );

				$select_options[$taxonomy][$slug]['name'] = $name;
				$select_options[$taxonomy][$slug]['visibility'] = 'both';
				$select_options['attribute_' . $taxonomy][$slug]['name'] = $name;
				$select_options['attribute_' . $taxonomy][$slug]['visibility'] = 'both';

				$select_options['_default_attribute_' . $taxonomy]['']['name'] = '';
				$select_options['_default_attribute_' . $taxonomy]['']['visibility'] = 'both';
				$select_options['_default_attribute_' . $taxonomy][$slug]['name'] = $name;
				$select_options['_default_attribute_' . $taxonomy][$slug]['visibility'] = 'both';
			}
		}

		$select_options['_tax_status'] = array();
		$select_options['_tax_class'] = array();
		$select_options['tax_classes'] = array();
		$select_options['tax_statuses'] = array();

		if ( function_exists( 'wc_tax_enabled' ) ) {
			$tax_enabled = wc_tax_enabled();
		} else {
			$tax_enabled = apply_filters( 'wc_tax_enabled', get_option( 'woocommerce_calc_taxes' ) === 'yes' );
		}

		if ( $tax_enabled ) {

			$select_options['_tax_status']['taxable']['name'] = __( 'Taxable', 'woocommerce' );
			$select_options['_tax_status']['taxable']['visibility'] = 'both';
			$select_options['_tax_status']['shipping']['name'] = __( 'Shipping only', 'woocommerce' );
			$select_options['_tax_status']['shipping']['visibility'] = 'both';
			$select_options['_tax_status']['none']['name'] = __( 'None', 'woocommerce' );
			$select_options['_tax_status']['none']['visibility'] = 'both';


			$select_options['_tax_class']['parent']['name'] = __( 'Same as parent', 'woocommerce' );
			$select_options['_tax_class']['parent']['visibility'] = 'variation';
			$select_options['_tax_class']['']['name'] = __( 'Standard', 'woocommerce' );
			$select_options['_tax_class']['']['visibility'] = 'both';

			$tax_classes = array();
			if ( method_exists( 'WC_Tax', 'get_tax_classes' ) ) {
				$tax_classes = WC_Tax::get_tax_classes();
			} else {
				$tax_classes = array_filter( array_map( 'trim', explode( "\n", get_option('woocommerce_tax_classes' ) ) ) );
			}

			if ( ! empty( $tax_classes ) ) {
				foreach ( $tax_classes as $class ) {
					$select_options['_tax_class'][ sanitize_title( $class )]['name'] = esc_html( $class );
					$select_options['_tax_class'][ sanitize_title( $class )]['visibility'] = 'both';
				}
			}

			// Blank options with select2 are "placeholders" but for Tax Class blank = Standard so we swap it with a single space for the value.
			$select_options['_tax_class'][' '] = $select_options['_tax_class'][''];
			unset( $select_options['_tax_class'][''] );

			$select_options['tax_statuses'] = $select_options['_tax_status'];
			$select_options['tax_classes'] = $select_options['_tax_class'];
		}

		if ( PW_Bulk_Edit::wc_min_version( '3.0' ) ) {
			$stock_status_options = wc_get_product_stock_status_options();
			foreach ( $stock_status_options as $key => $value ) {
				$select_options['_stock_status'][ $key ]['name'] = $value;
				$select_options['_stock_status'][ $key ]['visibility'] = 'both';
			}

		} else {
			$select_options['_stock_status']['instock']['name'] = __( 'In stock', 'woocommerce' );
			$select_options['_stock_status']['instock']['visibility'] = 'both';
			$select_options['_stock_status']['outofstock']['name'] = __( 'Out of stock', 'woocommerce' );
			$select_options['_stock_status']['outofstock']['visibility'] = 'both';
		}

		$select_options['stock_statuses'] = $select_options['_stock_status'];

		$select_options['_backorders']['no']['name'] = __( 'Do not allow', 'woocommerce' );
		$select_options['_backorders']['no']['visibility'] = 'both';
		$select_options['_backorders']['notify']['name'] = __( 'Allow, but notify customer', 'woocommerce' );
		$select_options['_backorders']['notify']['visibility'] = 'both';
		$select_options['_backorders']['yes']['name'] = __( 'Allow', 'woocommerce' );
		$select_options['_backorders']['yes']['visibility'] = 'both';

		if ( PW_Bulk_Edit::wc_min_version( '3.0' ) ) {
			$visibility_options = wc_get_product_visibility_options();
		} else {
			$visibility_options = apply_filters( 'woocommerce_product_visibility_options', array(
				'visible' => __( 'Catalog/search', 'woocommerce' ),
				'catalog' => __( 'Catalog', 'woocommerce' ),
				'search'  => __( 'Search', 'woocommerce' ),
				'hidden'  => __( 'Hidden', 'woocommerce' )
			) );
		}

		foreach ( $visibility_options as $key => $visibility ) {
			$select_options['_visibility'][$key]['name'] = esc_html( $visibility );
			$select_options['_visibility'][$key]['visibility'] = 'parent';
		}
		$select_options['catalog_visibility'] = $select_options['_visibility'];

		foreach ( $wp_post_statuses as $key => $post_status ) {
			if ( '1' == $post_status->show_in_admin_status_list ) {
				$select_options['post_status'][$key]['name'] = esc_html( $post_status->label );
				$select_options['post_status'][$key]['visibility'] = 'both';
			}
		}

		if ( PW_Bulk_Edit::wc_min_version( '3.0' ) ) {
			$select_options['post_status']['pwbe_delete']['name'] = esc_html( __( 'Delete (CAUTION! This is permanent.)', 'pw-bulk-edit' ) );
			$select_options['post_status']['pwbe_delete']['visibility'] = 'both';
		}

		$select_options = apply_filters( 'pwbe_select_options', $select_options );

		PWBE_Select_Options::sort_select_options( $select_options );

		PWBE_Select_Options::$select_options = $select_options;
	}

	private static function sort_select_options( &$select_options ) {
		$ignore_options = apply_filters( 'pwbe_skip_sorting_options', array( 'product_type', '_visibility', 'catalog_visibility', '_stock_status', 'stock_statuses' ) );

		foreach ( $select_options as $f => &$values ) {
			if ( !in_array( $f, $ignore_options ) ) {
				uasort( $values, 'PWBE_Select_Options::name_compare');
				uksort( $values, 'PWBE_Select_Options::blanks_first' );
			}
		}
	}

	private static function name_compare( $a, $b ) {
		return strnatcmp( $a['name'], $b['name'] );
	}

	private static function blanks_first( $a, $b ) {
		if ( $a === PW_Bulk_Edit::NULL ) {
			return -2;
	    } else if ( $a === '') {
	        return -1;
	    } else {
		    return strnatcmp( $a, $b );
		}
	}
}

endif;

?>