<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wc_custom_product_data_fields' ) ) {
	return;
}

if ( ! class_exists( 'PWBE_Flatsome' ) ) :

final class PWBE_Flatsome {

	function __construct() {
		add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
		add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
		add_filter( 'pwbe_select2', array( $this, 'pwbe_select2' ), 10, 2 );
	}

	function pwbe_select_options( $select_options ) {
		$data_fields = wc_custom_product_data_fields();

		foreach ( $data_fields as $field ) {
			// Old style.
			if ( isset( $field['options'] ) ) {
				$id = 'wc_productdata_options___0___' . $field['id'];

				foreach ( $field['options'] as $key => $value ) {
					$select_options[ $id ][ $key ]['name'] = $value;
					$select_options[ $id ][ $key ]['visibility'] = 'parent';
				}
			} else {
				// New style.
				foreach( $field as $f ) {
					if ( isset( $f['options'] ) ) {
						$id = 'wc_productdata_options___0___' . $f['id'];

						foreach ( $f['options'] as $key => $value ) {
							$select_options[ $id ][ $key ]['name'] = $value;
							$select_options[ $id ][ $key ]['visibility'] = 'parent';
						}
					}
				}
			}
		}

	    return $select_options;
	}

	function pwbe_product_columns( $columns ) {
		$data_fields = wc_custom_product_data_fields();

		foreach ( $data_fields as $field ) {
			// Old style.
			if ( isset( $field['id'] ) ) {
				$columns[] = array(
					'name' => $field['label'],
					'type' => $field['type'],
					'table' => 'meta',
					'field' => 'wc_productdata_options___0___' . $field['id'],
					'readonly' => 'false',
					'visibility' => 'parent',
					'sortable' => 'false',
					'views' => array( 'all' )
				);
			} else {
				// New style.
				foreach( $field as $f ) {
					if ( isset( $f['id'] ) ) {
						$columns[] = array(
							'name' => $f['label'],
							'type' => $f['type'],
							'table' => 'meta',
							'field' => 'wc_productdata_options___0___' . $f['id'],
							'readonly' => 'false',
							'visibility' => 'parent',
							'sortable' => 'false',
							'views' => array( 'all' )
						);
					}
				}
			}
		}

		return $columns;
	}

	function pwbe_select2( $select2, $field_name ) {
		if ( $field_name == 'wc_productdata_options___0____bubble_new' ) {
			$select2 = false;
		}

		return $select2;
	}
}

new PWBE_Flatsome();

endif;

?>