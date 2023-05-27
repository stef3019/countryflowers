<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PWBE_Save_Products' ) ) :

final class PWBE_Save_Products {

	public function save( $fields ) {
		global $wpdb;
		global $sitepress;

		if ( isset( $sitepress ) ) {
			$sitepress->switch_lang( 'all' );
		}

		do_action( 'pwbe_before_save_products', $fields );

		$wpdb->show_errors();

		$columns = PWBE_Columns::get();
		$custom_attributes = PWBE_Attributes::get_custom_attributes();

		$products_with_price_changes = array();
		$updated_variable_products = array();
		$new_variations = array();
		$meta_field_arrays = array();

		$updated_product_ids = array();
		$updated_variation_ids = array();

		$featured_products_changed = false;

		foreach( $fields as $field ) {

			if ( !isset( $field['post_id'] ) ) {
				continue;
			}

			// Reset the time limit for each product to give time to process everything.
			@set_time_limit( 0 );

			$field['value'] = apply_filters( 'pwbe_save_field_value', $field['value'], $field );

			$table = $this->get_column_value( $columns, $field['field'], 'table' );
			$type = $this->get_column_value( $columns, $field['field'], 'type' );

			if ( $table == 'post' ) {
				if ( $field['field'] == 'post_status' && $field['value'] == 'pwbe_delete' ) {
					$product = wc_get_product( $field['post_id'] );
					if ( $product ) {
						$product->delete(true);
					}

				} else {
					if ( $field['field'] == 'comment_status' ) {
						$field['value'] = ( $field['value'] == 'no' ) ? 'closed' : 'open';
					}

					$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET $field[field] = %s WHERE ID = %d", stripslashes( $field['value'] ), $field['post_id'] ) );
				}
				$db_err = PWBE_DB::error();
				if ( !empty( $db_err ) ) { return $db_err; }

			} else if ( $table == 'meta' ) {

				if ( PW_Bulk_Edit::starts_with( '_default_attribute_', $field['field'] ) ) {
					$attribute_name = str_replace( '_default_attribute_', '', $field['field'] );

					$default_attributes_string = get_post_meta( $field['parent_post_id'], '_default_attributes', true );
					$default_attributes = maybe_unserialize( $default_attributes_string );
					if ( empty( $default_attributes ) ) {
						$default_attributes = array();
					}

					$default_attributes[ $attribute_name ] = $field['value'];

					update_post_meta( $field['parent_post_id'], '_default_attributes', $default_attributes );

				} else {
					if ( $field['field'] == '_featured' ) {
						$featured_products_changed = true;
					}

					if ( $field['field'] == '_featured' && PW_Bulk_Edit::wc_min_version( '3.0' ) ) {
						$product = wc_get_product( $field['post_id'] );
						if ( $product ) {
							$product->set_featured( $field['value'] );
							$product->save();
						}

					} else if ( PW_Bulk_Edit::wc_min_version( '3.0' ) && in_array( $field['field'], array( '_regular_price', '_sale_price', '_sale_price_dates_from', '_sale_price_dates_to', '_visibility' ) ) ) {
						$product = wc_get_product( $field['post_id'] );
						if ( $product ) {
							switch ( $field['field'] ) {
								case '_regular_price':
									$product->set_regular_price( $field['value'] );
								break;

								case '_sale_price':
									$product->set_sale_price( $field['value'] );
								break;

								case '_sale_price_dates_from':
									$product->set_date_on_sale_from( $field['value'] );
								break;

								case '_sale_price_dates_to':
									if ( !empty( $field['value'] ) ) {
										$product->set_date_on_sale_to( $field['value'] . ' 23:59:59');
									} else {
										$product->set_date_on_sale_to( '' );
									}
								break;

								case '_visibility':
									$product->set_catalog_visibility( $field['value'] );
								break;
							}
							$product->save();
						}

					} else if ( strpos( $field['field'], '___' ) !== false ) {

						// For array values, split by ___ and build out the $meta_field_arrays object.
						// When we're done processing fields, we will merge this array with the value
						// of the product's array.
						$meta_key = substr( $field['field'], 0, strpos( $field['field'], '___' ) );
						$array_key = substr( $field['field'], strpos( $field['field'], '___' ) + 3 );

						if ( $meta_key === '_downloadable_files' ) {
							$downloads = maybe_unserialize( get_post_meta( $field['post_id'], $meta_key, true ) );
							if ( !is_array( $downloads ) ) {
								$downloads = array();
							}

							$download_field = substr( $array_key, strpos( $array_key, '___' ) + 3 );
							$index = substr( $array_key, 0, strpos( $array_key, '___' ) );
							$keys = array_keys( $downloads );
							if ( count( $downloads ) > $index ) {
								$downloads[ $keys[ $index ] ][ $download_field ] = $field['value'];
							} else {
								// New download record.
								$download_id = wp_generate_uuid4();
								$downloads[ $download_id ] = array(
									'id' => $download_id,
									'name' => ( 'name' === $download_field ) ? $field['value'] : '',
									'file' => ( 'file' === $download_field ) ? $field['value'] : ''
								);
							}

							update_post_meta( $field['post_id'], '_downloadable_files', $downloads );

						} else {
							if ( !isset( $meta_field_arrays[ $field['post_id'] ][ $meta_key ] ) ) {
								$stored_data = maybe_unserialize( get_post_meta( $field['post_id'], $meta_key, true ) );
								if ( empty( $stored_data ) || !is_array( $stored_data ) ) {
									$stored_data = array();
								}
							} else {
								$stored_data = $meta_field_arrays[ $field['post_id'] ][ $meta_key ];
							}

							$stored_data = apply_filters( 'pwbe_before_save_array_value', $stored_data, $meta_key, $field );
							$meta_field_arrays[ $field['post_id'] ][ $meta_key ] = $stored_data;

							$array_value = &$meta_field_arrays[ $field['post_id'] ][ $meta_key ];

							// Descend into the array if it's nested.
							while ( strpos( $array_key, '___' ) !== false ) {
								$array_name = substr( $array_key, 0, strpos( $array_key, '___' ) );
								$array_key = substr( $array_key, strpos( $array_key, '___' ) + 3 );

								if ( !isset( $array_value[ $array_name ] ) || !is_array( $array_value[ $array_name ] ) ) {
									$array_value[ $array_name ] = array();
								}

								$array_value = &$array_value[ $array_name ];
							}
							if ( !empty( $array_key ) ) {
								$array_value[ $array_key ] = $field['value'];
							} else {
								// Maybe we shouldn't always explode? Works for crosssells for now.
								$array_value = explode( ',', $field['value'] );
							}

							$array_value = apply_filters( 'pwbe_save_array_value', $array_value, $meta_key, $field );
						}

					} else {

						if ( $type === 'checkbox' ) {
							if ( $field['value'] === 'yes' ) {
								$checkbox_value = $this->get_column_value( $columns, $field['field'], 'checked_value' );
							} else {
								$checkbox_value = $this->get_column_value( $columns, $field['field'], 'unchecked_value' );
							}

							if ( !is_null( $checkbox_value ) ) {
								$field['value'] = $checkbox_value;
							}
						}

						if ( $field['value'] == PW_Bulk_Edit::NULL || ( $type == 'image' && empty( $field['value'] ) ) ) {
							delete_post_meta( $field['post_id'], $field['field'] );
						} else {
							if ( $type == 'date' ) {
								$field['value'] = strtotime( $field['value'] );
							}

							update_post_meta( $field['post_id'], $field['field'], $field['value'] );
						}

						if ( $field['field'] == '_regular_price' ) {
							if ( strpos( $field['product_type'], 'subscription' ) !== false ) {
								update_post_meta( 'post', $field['post_id'], '_subscription_price', $field['value'] );
							}
						}

						// Keep track of any products whose prices or sale dates have changed. We'll need to reprocess them.
						if ( !PW_Bulk_Edit::wc_min_version( '3.0' ) ) {
							if ( in_array( $field['field'], array( '_regular_price', '_sale_price', '_sale_price_dates_from', '_sale_price_dates_to' ) ) ) {
								if ( !in_array( $field['post_id'], $products_with_price_changes ) ) {
									$products_with_price_changes[] = $field['post_id'];
								}
							}
						}
					}
				}

			} else if ( $table == 'terms' ) {

				switch ( $field['field'] ) {
					case 'categories':
						$this->save_taxonomies( $field['post_id'], $field['value'], 'product_cat' );
						break;

					case 'tags':
						$this->save_taxonomies( $field['post_id'], $field['value'], 'product_tag' );
						break;

					case 'brands':
						$this->save_taxonomies( $field['post_id'], $field['value'], 'product_brand' );
						break;

					case 'yith_brands':
						$this->save_taxonomies( $field['post_id'], $field['value'], 'yith_product_brand' );
						break;

					case 'product_shipping_class':
					case 'yith_shop_vendor':
					case 'product-custom-label':
						$this->save_taxonomies( $field['post_id'], $field['value'], $field['field'] );
						break;

					default:

						$handled = apply_filters( 'pwbe_terms_save_handled', false, $this, $field );
						if ( false === $handled ) {
							$post_id = $field['parent_post_id'];
							$attribute_name = sanitize_title( $field['field'] );

							$product_attributes_string = get_post_meta( $post_id, '_product_attributes', true );
							$product_attributes = maybe_unserialize( $product_attributes_string );

							if ( !is_array( $product_attributes ) ) {
								$product_attributes = array();
							}

							if ( !isset( $product_attributes[ $attribute_name ] ) || !isset( $product_attributes[ $attribute_name ]['is_taxonomy'] ) || !isset( $product_attributes[ $attribute_name ]['is_variation'] ) ) {
								$product_attributes[ $attribute_name ] = array(
									'name' 			=> PW_Bulk_Edit::starts_with( 'pa_', $attribute_name ) ? $attribute_name : wc_clean( str_replace( ' attributes', '', $field['display_name'] ) ),
									'value' 		=> '',
									'position' 		=> $this->get_new_attribute_position( $product_attributes ),
									'is_visible' 	=> 1,
									'is_variation' 	=> ( isset( $_POST['auto_create_variations'] ) && $_POST['auto_create_variations'] == '1' ) ? 1 : 0,
									'is_taxonomy' 	=> PW_Bulk_Edit::starts_with( 'pa_', $attribute_name ) ? 1 : 0
								);
							} else if ( isset( $_POST['auto_create_variations'] ) && $_POST['auto_create_variations'] == '1' && $product_attributes[ $attribute_name ]['is_variation'] != 1 ) {
								$product_attributes[ $attribute_name ]['is_variation'] = 1;
								update_post_meta( $post_id, '_product_attributes', $product_attributes );
							}

							$new_values = array_filter( explode( ',', stripslashes( $field['value'] ) ) );
							$old_values = array_filter( explode( ',', stripslashes( $field['old_value'] ) ) );

							if ( $product_attributes[ $attribute_name ]['is_taxonomy'] == 0 ) {
								$values = implode( ' ' . WC_DELIMITER . ' ', $new_values );
								$product_attributes[ $attribute_name ]['value'] = $values;
							} else {
								$this->save_taxonomies( $post_id, $field['value'], $field['field'] );
							}

							if ( count( $new_values ) == 0 ) {
								unset( $product_attributes[ $attribute_name ] );
							}

							// save_product_attributes meta value
							uasort( $product_attributes, 'wc_product_attribute_uasort_comparison' );
							update_post_meta( $post_id, '_product_attributes', $product_attributes );

							if ( isset( $product_attributes[ $attribute_name ] ) && $product_attributes[ $attribute_name ]['is_variation'] == '1' ) {
								if ( isset( $_POST['auto_create_variations'] ) && $_POST['auto_create_variations'] == '1' ) {
									// We'll only mess with attributes that didn't already exist.
									$new_values = array_diff( $new_values, $old_values );

									$new_variations[$post_id] = array(
										'attribute_name' => $attribute_name,
										'new_values' => $new_values
									);
								}
							}
						}

					break;
				}
			} else if ( $table == 'product_type' ) {

				if ( PW_Bulk_Edit::wc_min_version( '3.0' ) ) {

					$post_id = $field['post_id'];
					$product_type = $field['value'];

					$classname    = WC_Product_Factory::get_product_classname( $post_id, $product_type ? $product_type : 'simple' );
					$product      = new $classname( $post_id );
					$product->save();

				} else {
					wp_set_object_terms( $field['post_id'], $field['value'], 'product_type' );
				}

			} else if ( $table == 'attributes' ) {

				$post_id = $field['parent_post_id'];

				$product_attributes_string = get_post_meta( $post_id, '_product_attributes', true );
				$product_attributes = maybe_unserialize( $product_attributes_string );

				if ( is_array( $product_attributes ) ) {

					if ( PW_Bulk_Edit::starts_with( '_attribute_visibility_', $field['field'] ) ) {
						$attribute_name = str_replace( '_attribute_visibility_', '', $field['field'] );
						$product_attributes[ $attribute_name ]['is_visible'] = ( $field['value'] == 'yes' );

					} else if ( PW_Bulk_Edit::starts_with( '_attribute_variations_', $field['field'] ) ) {
						$is_variation = ( $field['value'] == 'yes' );

						$attribute_name = str_replace( '_attribute_variations_', '', $field['field'] );
						$product_attributes[ $attribute_name ]['is_variation'] = $is_variation;

						if ( true === $is_variation && ( !defined( 'PWBE_IS_VARIATION_CREATES_ALL_VARIATIONS' ) || PWBE_IS_VARIATION_CREATES_ALL_VARIATIONS === true ) ) {
							$new_variations[$post_id] = array(
								'attribute_name' => $attribute_name,
								'new_values' => array()
							);
						}
					}

					update_post_meta( $post_id, '_product_attributes', $product_attributes );

				} else {
					return sprintf( __( 'Unable to set attribute values %s for post_id %s. Does this product have the attribute?', 'pw-bulk-edit' ), $field['field'], $post_id );
				}

			}

			if ( $field['post_id'] != $field['parent_post_id'] ) {
				if ( !in_array( $field['parent_post_id'], $updated_variable_products ) ) {
					$updated_variable_products[] = $field['parent_post_id'];
				}

				$updated_variation_ids[] = $field['post_id'];
				$updated_product_ids[] = $field['parent_post_id'];
			} else {
				$updated_product_ids[] = $field['post_id'];
			}
		}

		// Save any meta values that are stored in an array.
		foreach ( $meta_field_arrays as $post_id => $values ) {
			if ( empty( $post_id ) ) { continue; }

			foreach ( $values as $meta_key => $array ) {
				update_post_meta( $post_id, $meta_key, $array );
			}
		}

		if ( count( $products_with_price_changes ) > 0 ) {
			//
			// Ensure product price is accurate for any changed prices.
			//
			$placeholders = implode( ', ', array_fill( 0, count( $products_with_price_changes ), '%d' ) );
			$where_clause = $wpdb->prepare( "post.ID IN ($placeholders) ", $products_with_price_changes );

			$wpdb->query("SET SQL_BIG_SELECTS=1");

			$price_changes = $wpdb->get_results("
				SELECT
					post.ID,
					regular_price.meta_value AS _regular_price,
					sale_price.meta_value AS _sale_price,
					sale_price_dates_from.meta_value AS _sale_price_dates_from,
					sale_price_dates_to.meta_value AS _sale_price_dates_to
				FROM
					{$wpdb->posts} AS post
				LEFT JOIN
					{$wpdb->postmeta} AS regular_price ON (regular_price.post_id = post.ID AND regular_price.meta_key = '_regular_price')
				LEFT JOIN
					{$wpdb->postmeta} AS sale_price ON (sale_price.post_id = post.ID AND sale_price.meta_key = '_sale_price')
				LEFT JOIN
					{$wpdb->postmeta} AS sale_price_dates_from ON (sale_price_dates_from.post_id = post.ID AND sale_price_dates_from.meta_key = '_sale_price_dates_from')
				LEFT JOIN
					{$wpdb->postmeta} AS sale_price_dates_to ON (sale_price_dates_to.post_id = post.ID AND sale_price_dates_to.meta_key = '_sale_price_dates_to')
				WHERE
					$where_clause
			");
			$db_err = PWBE_DB::error();
			if ( !empty( $db_err ) ) { return $db_err; }

			foreach ( $price_changes as $pc ) {
				$this->save_product_price(
					$pc->ID,
					$pc->_regular_price,
					$pc->_sale_price,
					$pc->_sale_price_dates_from ? date_i18n( 'Y-m-d', $pc->_sale_price_dates_from, true ) : '',
					$pc->_sale_price_dates_to ? date_i18n( 'Y-m-d', $pc->_sale_price_dates_to, true ) : ''
				);
			}
		}

		foreach ( $new_variations as $post_id => $variation ) {
			$this->link_variations( $post_id, $variation['attribute_name'], $variation['new_values'] );
		}

		foreach ( $updated_variable_products as $product_id ) {
			WC_Product_Variable::sync( $product_id );
			WC_Product_Variable::sync_stock_status( $product_id );
		}

		$updated_product_ids = array_unique( $updated_product_ids );
		foreach ( $updated_product_ids as $post_id ) {
			// This will sync things like "Stock Status", etc.
			if ( PW_Bulk_Edit::wc_min_version( '3.0' ) ) {
				$product = wc_get_product( $post_id );
				if ( $product ) {
					$product->save();
				}
			}
			wc_delete_product_transients( $post_id );
			wp_cache_delete( 'product-' . $post_id, 'products' );
			clean_post_cache( $post_id );

			if ( isset( $product ) ) {
				do_action( 'woocommerce_update_product', $post_id, $product );
			} else {
				do_action( 'woocommerce_update_product', $post_id );
			}
		}

		$updated_variation_ids = array_unique( $updated_variation_ids );
		foreach ( $updated_variation_ids as $post_id ) {
			// This will sync things like "Stock Status", etc.
			$product = wc_get_product( $post_id );
			if ( PW_Bulk_Edit::wc_min_version( '3.0' ) ) {
				if ( $product ) {
					$product->save();
				}
			}
			wc_delete_product_transients( $post_id );
			wp_cache_delete( 'product-' . $post_id, 'products' );
			clean_post_cache( $post_id );
			if ( PW_Bulk_Edit::wc_min_version( '3.7' ) ) {
				do_action( 'woocommerce_update_product_variation', $post_id, $product );
			} else {
				do_action( 'woocommerce_update_product_variation', $post_id );
			}
		}

		if ( $featured_products_changed ) {
			delete_transient( 'wc_featured_products' );
		}

		$wpdb->hide_errors();

		if ( isset( $_POST['auto_create_variations'] ) && $_POST['auto_create_variations'] == '1' ) {
			update_option( 'pwbe_auto_create_variations', '1' );
		} else {
			update_option( 'pwbe_auto_create_variations', '' );
		}

		if ( class_exists( 'LiteSpeed_Cache_API' ) && method_exists( 'LiteSpeed_Cache_API', 'purge_all' ) ) {
			LiteSpeed_Cache_API::purge_all();
		}

		do_action( 'pwbe_after_save_products', $fields );

		return 'success';
	}

	function save_taxonomies( $post_id, $value_string, $taxonomy ) {
		if ( $value_string != PW_Bulk_Edit::NULL ) {
			$slugs = explode( ',', $value_string );
			$slugs = array_unique( $slugs );
		} else {
			$slugs = '';
		}

		wp_set_object_terms( $post_id, $slugs, $taxonomy );
	}

	function link_variations( $post_id, $attribute_name, $new_values ) {

		$variations = array();
		$_product   = wc_get_product( $post_id );
		if ( !$_product ) { return; }

		// Put variation attributes into an array
		foreach ( $_product->get_attributes() as $attribute ) {

			if ( ! $attribute['is_variation'] ) {
				continue;
			}

			$attribute_field_name = 'attribute_' . sanitize_title( $attribute['name'] );

			if ( $attribute['is_taxonomy'] ) {
				$options = wp_get_post_terms( $post_id, $attribute['name'], array( 'fields' => 'slugs' ) );
			} else {
				$options = explode( WC_DELIMITER, $attribute['value'] );
			}

			$options = array_map( 'trim', $options );

			$variations[ $attribute_field_name ] = $options;
		}

		// Quit out if none were found
		if ( count( $variations ) == 0 ) {
			return;
		}

		// Get existing variations so we don't create duplicates
		$existing_variations = array();

		foreach( $_product->get_children() as $child_id ) {
			if ( PW_Bulk_Edit::wc_min_version( '3.0' ) ) {
				$child = wc_get_product( $child_id );
				$variation_id = $child->get_id();
			} else {
				$child = $_product->get_child( $child_id );
				$variation_id = $child->variation_id;
			}

			if ( ! empty( $variation_id ) ) {
				$existing_variations[] = $child->get_variation_attributes();
			}
		}

		// Created posts will all have the following data
		$variation_post_data = array(
			'post_title'   => 'Product #' . $post_id . ' Variation',
			'post_content' => '',
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id(),
			'post_parent'  => $post_id,
			'post_type'    => 'product_variation'
		);

		$variation_ids       = array();
		$possible_variations = wc_array_cartesian( $variations );

		foreach ( $possible_variations as $variation ) {
			// Check if variation already exists
			if ( in_array( $variation, $existing_variations ) ) {
				continue;
			}

			if ( !isset( $variation[ 'attribute_' . $attribute_name ] ) ) {
				continue;
			}

			$slug = $variation[ 'attribute_' . $attribute_name ];
			if ( !empty( $new_values ) && ! in_array( $slug, $new_values ) ) {
				continue;
			}

			$variation_id = wp_insert_post( $variation_post_data );

			$variation_ids[] = $variation_id;

			foreach ( $variation as $key => $value ) {
				update_post_meta( $variation_id, $key, $value );
			}

			// Save stock status
			update_post_meta( $variation_id, '_stock_status', 'instock' );

			do_action( 'product_variation_linked', $variation_id );

			do_action( 'pwbe_variation_created', $variation_id, $post_id );
		}

		delete_transient( 'wc_product_children_' . $post_id );

		return;
	}

	function get_column_value( $columns, $field, $value ) {
		foreach ( $columns as $column ) {
			if ( is_array( $column ) && isset( $column['field'] ) ) {
				if ( $column['field'] === $field ) {
					if ( isset( $column[$value] ) ) {
						return $column[$value];
					}
					break;
				}
			}
		}

		return null;
	}

	function save_product_price( $product_id, $regular_price, $sale_price = '', $date_from = '', $date_to = '' ) {
		$product_id    = absint( $product_id );
		$regular_price = wc_format_decimal( $regular_price );
		$sale_price    = $sale_price === '' ? '' : wc_format_decimal( $sale_price );
		$date_from     = wc_clean( $date_from );
		$date_to       = wc_clean( $date_to );

		update_post_meta( $product_id, '_price', $regular_price );
		update_post_meta( $product_id, '_regular_price', $regular_price );
		update_post_meta( $product_id, '_sale_price', $sale_price );

		// Save Dates
		update_post_meta( $product_id, '_sale_price_dates_from', $date_from ? strtotime( $date_from ) : '' );
		update_post_meta( $product_id, '_sale_price_dates_to', $date_to ? strtotime( $date_to ) : '' );

		if ( $date_to && ! $date_from ) {
			$date_from = strtotime( 'NOW', current_time( 'timestamp' ) );
			update_post_meta( $product_id, '_sale_price_dates_from', $date_from );
		}

		// Update price if on sale
		if ( '' !== $sale_price && '' === $date_to && '' === $date_from ) {
			update_post_meta( $product_id, '_price', $sale_price );
		}

		if ( '' !== $sale_price && $date_from && strtotime( $date_from ) < strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
			update_post_meta( $product_id, '_price', $sale_price );
		}

		if ( $date_to && strtotime( $date_to ) < strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
			update_post_meta( $product_id, '_price', $regular_price );
			update_post_meta( $product_id, '_sale_price_dates_from', '' );
			update_post_meta( $product_id, '_sale_price_dates_to', '' );
		}

		// Ensure we always have a price.
		$price = get_post_meta( $product_id, '_price', true );
		if ( empty( $price ) ) {
			update_post_meta( $product_id, '_price', $regular_price );
		}
	}

	function get_new_attribute_position( $attributes ) {
		$position = 0;
		foreach ( $attributes as $attribute ) {
			$this_position = isset( $attribute['position'] ) ? intval( $attribute['position'] ) : 0;
			if ( $this_position > $position ) {
				$position = intval( $attribute['position'] );
			}
		}

		return $position + 1;
	}
}

endif;

?>