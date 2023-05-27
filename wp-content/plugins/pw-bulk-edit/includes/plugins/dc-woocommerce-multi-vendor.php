<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'WC_Dependencies_Product_Vendor' ) ) {
	return;
}

if ( !class_exists( 'PWBE_WC_Dependencies_Product_Vendor' ) ) :

final class PWBE_WC_Dependencies_Product_Vendor {

	private $taxonomy_name = 'dc_vendor_shop';

	function __construct() {
		add_filter( 'pwbe_filter_types', array( $this, 'pwbe_filter_types' ) );
		add_action( 'pwbe_after_filter_select_templates', array( $this, 'pwbe_after_filter_select_templates' ) );
		add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
		add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
		add_filter( 'pwbe_taxonomy_list', array( $this, 'pwbe_taxonomy_list' ) );
		add_filter( 'pwbe_terms_save_handled', array( $this, 'pwbe_terms_save_handled' ), 10, 3 );
		add_filter( 'pwbe_search_row_sql', array( $this, 'pwbe_search_row_sql' ), 10, 6 );
	}

	function pwbe_filter_types( $filter_types ) {
		$filter_types[ $this->taxonomy_name ] = array( 'name' => __( 'Vendor', 'dc-woocommerce-multi-vendor' ), 'type' => 'attributes' );

		return $filter_types;
	}

	function pwbe_after_filter_select_templates() {
		?>
		<span class="pwbe-multiselect pwbe-filter-<?php echo $this->taxonomy_name; ?>-container pwbe-filter-<?php echo $this->taxonomy_name; ?>-template" style="display: none;">
			<select name="filter_select[]" class="pwbe-filter-field pwbe-filter-select pwbe-filter-value" multiple="multiple" ></select>
		</span>
		<?php
	}

	function pwbe_select_options( $select_options ) {

		$wp_user_query = new WP_User_Query( array(
			'role__in' => array( 'dc_vendor', 'dc_pending_vendor', 'dc_rejected_vendor' )
		) );
		$users = $wp_user_query->get_results();

		foreach ( $users as $user ) {
			$select_options['attribute_' . $this->taxonomy_name ][ $user->data->user_login ]['name'] = $user->data->display_name;
			$select_options['attribute_' . $this->taxonomy_name ][ $user->data->user_login ]['visibility'] = 'both';
			$select_options[ $this->taxonomy_name ][ $user->data->user_login ]['name'] = $user->data->display_name;
			$select_options[ $this->taxonomy_name ][ $user->data->user_login ]['visibility'] = 'both';
		}

	    return $select_options;
	}

	function pwbe_product_columns( $columns ) {
		$columns[] = array(
			'name' => __( 'Vendor', 'dc-woocommerce-multi-vendor' ),
			'type' => 'select',
			'table' => 'terms',
			'field' => $this->taxonomy_name,
			'readonly' => 'false',
			'visibility' => 'parent',
			'sortable' => 'true',
			'views' => array( 'all', 'standard' )
		);

		$columns[] = array(
			'name' => __( 'Vendor Commission', 'dc-woocommerce-multi-vendor' ),
			'type' => 'text',
			'table' => 'meta',
			'field' => '_commission_per_product',
			'readonly' => 'false',
			'visibility' => 'parent',
			'sortable' => 'true',
			'views' => array( 'all', 'standard' )
		);

		return $columns;
	}

	function pwbe_taxonomy_list( $taxonomy_list ) {
		$taxonomy_list[] = $this->taxonomy_name;

		return $taxonomy_list;
	}

	function pwbe_terms_save_handled( $handled, $save_class, $field ) {
		if ( $field['field'] == $this->taxonomy_name ) {
			$save_class->save_taxonomies( $field['post_id'], $field['value'], $this->taxonomy_name );

            $term = get_term( $field['value'], $this->taxonomy_name);
            if ( $term ) {
                wp_delete_object_term_relationships( $field['post_id'], $this->taxonomy_name);
                wp_set_object_terms( $field['post_id'], (int) $term->term_id, $this->taxonomy_name, true );
            }

            $user = get_user_by( 'login', $field['value'] );
            if ( $user && !wp_is_post_revision( $field['post_id'] ) ) {
                wp_update_post( array( 'ID' => $field['post_id'], 'post_author' => $user->ID ) );
            }

			$handled = true;
		}

		return $handled;
	}

	function pwbe_search_row_sql( $row_sql, $search_class, $field_name, $filter_type, $field_value, $field_value2 ) {
		if ( $this->taxonomy_name == $field_name ) {
			$row_sql = $search_class->taxonomy_search( $this->taxonomy_name, $filter_type, $field_value );
		}

		return $row_sql;
	}
}

new PWBE_WC_Dependencies_Product_Vendor();

endif;

?>