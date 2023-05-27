<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !defined( 'WPLA_VERSION' ) ) {
	return;
}

if ( ! class_exists( 'PWBE_WP_Lister_For_Amazon' ) ) :

final class PWBE_WP_Lister_For_Amazon {

	function __construct() {
		add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
		add_filter( 'pwbe_skip_sorting_options', array( $this, 'pwbe_skip_sorting_options' ) );
		add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
	}

	function pwbe_select_options( $select_options ) {
		$select_options['_amazon_id_type'][ PW_Bulk_Edit::NULL ]['name'] = __( '-- use profile setting --', 'wp-lister-for-amazon' );
		$select_options['_amazon_id_type'][ PW_Bulk_Edit::NULL ]['visibility'] = 'both';
		$select_options['_amazon_id_type']['']['name'] = __( '-- use profile setting --', 'wp-lister-for-amazon' );
		$select_options['_amazon_id_type']['']['visibility'] = 'both';
		$select_options['_amazon_id_type']['UPC']['name'] = __( 'UPC', 'wp-lister-for-amazon' );
		$select_options['_amazon_id_type']['UPC']['visibility'] = 'both';
		$select_options['_amazon_id_type']['EAN']['name'] = __( 'EAN', 'wp-lister-for-amazon' );
		$select_options['_amazon_id_type']['EAN']['visibility'] = 'both';

		$select_options['_amazon_fba_overwrite'][ PW_Bulk_Edit::NULL ]['name'] = __( '-- set automatically --', 'wp-lister-for-amazon' );
		$select_options['_amazon_fba_overwrite'][ PW_Bulk_Edit::NULL ]['visibility'] = 'both';
		$select_options['_amazon_fba_overwrite']['']['name'] = __( '-- set automatically --', 'wp-lister-for-amazon' );
		$select_options['_amazon_fba_overwrite']['']['visibility'] = 'both';
		$select_options['_amazon_fba_overwrite']['FBA']['name'] = __( 'Fulfilled by Amazon (FBA)', 'wp-lister-for-amazon' );
		$select_options['_amazon_fba_overwrite']['FBA']['visibility'] = 'both';
		$select_options['_amazon_fba_overwrite']['FBM']['name'] = __( 'Fulfilled by Merchant (FBM)', 'wp-lister-for-amazon' );
		$select_options['_amazon_fba_overwrite']['FBM']['visibility'] = 'both';

		$select_options['_amazon_condition_type'][ PW_Bulk_Edit::NULL ]['name'] = __( '-- use profile setting --', 'wp-lister-for-amazon' );
		$select_options['_amazon_condition_type'][ PW_Bulk_Edit::NULL ]['visibility'] = 'both';
		$select_options['_amazon_condition_type']['']['name'] = __( '-- use profile setting --', 'wp-lister-for-amazon' );
		$select_options['_amazon_condition_type']['']['visibility'] = 'both';
		$select_options['_amazon_condition_type']['New']['name'] = __( 'New', 'wp-lister-for-amazon' );
		$select_options['_amazon_condition_type']['New']['visibility'] = 'both';
		$select_options['_amazon_condition_type']['UsedLikeNew']['name'] = __( 'Used - Like New', 'wp-lister-for-amazon' );
		$select_options['_amazon_condition_type']['UsedLikeNew']['visibility'] = 'both';
		$select_options['_amazon_condition_type']['UsedVeryGood']['name'] = __( 'Used - Very Good', 'wp-lister-for-amazon' );
		$select_options['_amazon_condition_type']['UsedVeryGood']['visibility'] = 'both';
		$select_options['_amazon_condition_type']['UsedGood']['name'] = __( 'Used - Good', 'wp-lister-for-amazon' );
		$select_options['_amazon_condition_type']['UsedGood']['visibility'] = 'both';
		$select_options['_amazon_condition_type']['UsedAcceptable']['name'] = __( 'Used - Acceptable', 'wp-lister-for-amazon' );
		$select_options['_amazon_condition_type']['UsedAcceptable']['visibility'] = 'both';
		$select_options['_amazon_condition_type']['Refurbished']['name'] = __( 'Refurbished', 'wp-lister-for-amazon' );
		$select_options['_amazon_condition_type']['Refurbished']['visibility'] = 'both';
		$select_options['_amazon_condition_type']['CollectibleLikeNew']['name'] = __( 'Collectible - Like New', 'wp-lister-for-amazon' );
		$select_options['_amazon_condition_type']['CollectibleLikeNew']['visibility'] = 'both';
		$select_options['_amazon_condition_type']['CollectibleVeryGood']['name'] = __( 'Collectible - Very Good', 'wp-lister-for-amazon' );
		$select_options['_amazon_condition_type']['CollectibleVeryGood']['visibility'] = 'both';
		$select_options['_amazon_condition_type']['CollectibleGood']['name'] = __( 'Collectible - Good', 'wp-lister-for-amazon' );
		$select_options['_amazon_condition_type']['CollectibleGood']['visibility'] = 'both';
		$select_options['_amazon_condition_type']['CollectibleAcceptable']['name'] = __( 'Collectible - Acceptable', 'wp-lister-for-amazon' );
		$select_options['_amazon_condition_type']['CollectibleAcceptable']['visibility'] = 'both';

	    return $select_options;
	}

	function pwbe_skip_sorting_options( $options ) {
		$options[] = '_amazon_id_type';
		$options[] = '_amazon_fba_overwrite';
		$options[] = '_amazon_condition_type';

		return $options;
	}

	function pwbe_product_columns( $columns ) {
		$new_columns[] = array(
			'name' => 'Amazon ' . __( 'Product ID', 'wp-lister-for-amazon' ),
			'type' => 'text',
			'table' => 'meta',
			'field' => '_amazon_product_id',
			'readonly' => 'false',
			'visibility' => 'both',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		$new_columns[] = array(
			'name' => 'Amazon ' . __( 'Product ID Type', 'wp-lister-for-amazon' ),
			'type' => 'select',
			'table' => 'meta',
			'field' => '_amazon_id_type',
			'readonly' => 'false',
			'visibility' => 'both',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		$new_columns[] = array(
			'name' => __( 'ASIN', 'wp-lister-for-amazon' ),
			'type' => 'text',
			'table' => 'meta',
			'field' => '_wpla_asin',
			'readonly' => 'false',
			'visibility' => 'both',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		if ( get_option( 'wpla_enable_custom_product_prices', 1 ) != 0 ) {
			$new_columns[] = array(
				'name' => __( 'Amazon Price', 'wp-lister-for-amazon' ),
				'type' => 'currency',
				'table' => 'meta',
				'field' => '_amazon_price',
				'readonly' => 'false',
				'visibility' => 'both',
				'sortable' => 'true',
				'views' => array( 'all' )
			);
		}

		$new_columns[] = array(
			'name' => 'Amazon ' . __( 'Listing title', 'wp-lister-for-amazon' ),
			'type' => 'text',
			'table' => 'meta',
			'field' => '_amazon_title',
			'readonly' => 'false',
			'visibility' => 'parent',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		$new_columns[] = array(
			'name' => 'Amazon ' . __( 'Handling Time', 'wp-lister-for-amazon' ),
			'type' => 'text',
			'table' => 'meta',
			'field' => '_amazon_handling_time',
			'readonly' => 'false',
			'visibility' => 'variation',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

        if ( get_option('wpla_load_b2b_templates',0) ) {
			$new_columns[] = array(
				'name' => __( 'Amazon B2B Price', 'wp-lister-for-amazon' ),
				'type' => 'currency',
				'table' => 'meta',
				'field' => '_amazon_b2b_price',
				'readonly' => 'false',
				'visibility' => 'parent',
				'sortable' => 'true',
				'views' => array( 'all' )
			);
        }

		if ( get_option( 'wpla_enable_minmax_product_prices', 0 ) != 0 ) {
			$new_columns[] = array(
				'name' => 'Amazon ' . __( 'Minimum Price', 'wp-lister-for-amazon' ),
				'type' => 'currency',
				'table' => 'meta',
				'field' => '_amazon_minimum_price',
				'readonly' => 'false',
				'visibility' => 'parent',
				'sortable' => 'true',
				'views' => array( 'all' )
			);

			$new_columns[] = array(
				'name' => 'Amazon ' . __( 'Maximum Price', 'wp-lister-for-amazon' ),
				'type' => 'currency',
				'table' => 'meta',
				'field' => '_amazon_maximum_price',
				'readonly' => 'false',
				'visibility' => 'parent',
				'sortable' => 'true',
				'views' => array( 'all' )
			);
		}

		$new_columns[] = array(
			'name' => 'Amazon ' . __( 'Restock Date', 'wp-lister-for-amazon' ),
			'type' => 'date',
			'table' => 'meta',
			'field' => '_amazon_restock_date',
			'readonly' => 'false',
			'visibility' => 'both',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		$new_columns[] = array(
			'name' => __( 'Hide on Amazon', 'wp-lister-for-amazon' ),
			'type' => 'checkbox',
			'table' => 'meta',
			'field' => '_amazon_is_disabled',
			'readonly' => 'false',
			'visibility' => 'variation',
			'sortable' => 'true',
			'views' => array( 'all' ),
			'checked_value' => 'on',
			'unchecked_value' => '',
		);

		$new_columns[] = array(
			'name' => __( 'MSRP Price', 'wp-lister-for-amazon' ),
			'type' => 'currency',
			'table' => 'meta',
			'field' => '_msrp',
			'readonly' => 'false',
			'visibility' => 'both',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

        if ( get_option( 'wpla_fba_enabled' ) != 0 ) {
			$new_columns[] = array(
				'name' => __( 'FBA mode', 'wp-lister-for-amazon' ),
				'type' => 'select',
				'table' => 'meta',
				'field' => '_amazon_fba_overwrite',
				'readonly' => 'false',
				'visibility' => 'parent',
				'sortable' => 'true',
				'views' => array( 'all' )
			);
		}

		if ( get_option( 'wpla_enable_item_condition_fields', 2 ) != 0 ) {
			$new_columns[] = array(
				'name' => 'Amazon ' . __( 'Item Condition', 'wp-lister-for-amazon' ),
				'type' => 'select',
				'table' => 'meta',
				'field' => '_amazon_condition_type',
				'readonly' => 'false',
				'visibility' => 'parent',
				'sortable' => 'true',
				'views' => array( 'all' )
			);

			$new_columns[] = array(
				'name' => 'Amazon ' . __( 'Condition Note', 'wp-lister-for-amazon' ),
				'type' => 'text',
				'table' => 'meta',
				'field' => '_amazon_condition_note',
				'readonly' => 'false',
				'visibility' => 'parent',
				'sortable' => 'true',
				'views' => array( 'all' )
			);
		}

		$new_columns[] = array(
			'name' => 'Amazon ' . __( 'Bullet Point 1', 'wp-lister-for-amazon' ),
			'type' => 'text',
			'table' => 'meta',
			'field' => '_amazon_bullet_point1',
			'readonly' => 'false',
			'visibility' => 'parent',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		$new_columns[] = array(
			'name' => 'Amazon ' . __( 'Bullet Point 2', 'wp-lister-for-amazon' ),
			'type' => 'text',
			'table' => 'meta',
			'field' => '_amazon_bullet_point2',
			'readonly' => 'false',
			'visibility' => 'parent',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		$new_columns[] = array(
			'name' => 'Amazon ' . __( 'Bullet Point 3', 'wp-lister-for-amazon' ),
			'type' => 'text',
			'table' => 'meta',
			'field' => '_amazon_bullet_point3',
			'readonly' => 'false',
			'visibility' => 'parent',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		$new_columns[] = array(
			'name' => 'Amazon ' . __( 'Bullet Point 4', 'wp-lister-for-amazon' ),
			'type' => 'text',
			'table' => 'meta',
			'field' => '_amazon_bullet_point4',
			'readonly' => 'false',
			'visibility' => 'parent',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		$new_columns[] = array(
			'name' => 'Amazon ' . __( 'Bullet Point 5', 'wp-lister-for-amazon' ),
			'type' => 'text',
			'table' => 'meta',
			'field' => '_amazon_bullet_point5',
			'readonly' => 'false',
			'visibility' => 'parent',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		if ( 'single' == get_option( 'wpla_keyword_fields_type', 'separate' ) ) {
			$new_columns[] = array(
				'name' => 'Amazon ' . __( 'Search Term', 'wp-lister-for-amazon' ),
				'type' => 'text',
				'table' => 'meta',
				'field' => '_amazon_search_term',
				'readonly' => 'false',
				'visibility' => 'parent',
				'sortable' => 'true',
				'views' => array( 'all' )
			);
		} else {
			$new_columns[] = array(
				'name' => 'Amazon ' . __( 'Keywords 1', 'wp-lister-for-amazon' ),
				'type' => 'text',
				'table' => 'meta',
				'field' => '_amazon_generic_keywords1',
				'readonly' => 'false',
				'visibility' => 'parent',
				'sortable' => 'true',
				'views' => array( 'all' )
			);

			$new_columns[] = array(
				'name' => 'Amazon ' . __( 'Keywords 2', 'wp-lister-for-amazon' ),
				'type' => 'text',
				'table' => 'meta',
				'field' => '_amazon_generic_keywords2',
				'readonly' => 'false',
				'visibility' => 'parent',
				'sortable' => 'true',
				'views' => array( 'all' )
			);

			$new_columns[] = array(
				'name' => 'Amazon ' . __( 'Keywords 3', 'wp-lister-for-amazon' ),
				'type' => 'text',
				'table' => 'meta',
				'field' => '_amazon_generic_keywords3',
				'readonly' => 'false',
				'visibility' => 'parent',
				'sortable' => 'true',
				'views' => array( 'all' )
			);

			$new_columns[] = array(
				'name' => 'Amazon ' . __( 'Keywords 4', 'wp-lister-for-amazon' ),
				'type' => 'text',
				'table' => 'meta',
				'field' => '_amazon_generic_keywords4',
				'readonly' => 'false',
				'visibility' => 'parent',
				'sortable' => 'true',
				'views' => array( 'all' )
			);

			$new_columns[] = array(
				'name' => 'Amazon ' . __( 'Keywords 5', 'wp-lister-for-amazon' ),
				'type' => 'text',
				'table' => 'meta',
				'field' => '_amazon_generic_keywords5',
				'readonly' => 'false',
				'visibility' => 'parent',
				'sortable' => 'true',
				'views' => array( 'all' )
			);
		}


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
}

new PWBE_WP_Lister_For_Amazon();

endif;

?>