<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !defined( 'ICL_SITEPRESS_VERSION' ) ) {
	return;
}

if ( ! class_exists( 'PWBE_WPML' ) ) :

final class PWBE_WPML {

	function __construct() {
		add_filter( 'pwbe_filter_types', array( $this, 'pwbe_filter_types' ) );
		add_action( 'pwbe_after_filter_select_templates', array( $this, 'pwbe_after_filter_select_templates' ) );
		add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
		add_filter( 'pwbe_skip_sorting_options', array( $this, 'pwbe_skip_sorting_options' ) );
		add_filter( 'pwbe_search_row_sql', array( $this, 'pwbe_search_row_sql' ), 10, 6 );
	}

	function pwbe_filter_types( $filter_types ) {
		$filter_types['wpml'] = array( 'name' => __( 'WPML Language', 'pw-bulk-edit' ), 'type' => 'wpml' );
		return $filter_types;
	}

	function pwbe_after_filter_select_templates() {
		?>
		<span class="pwbe-multiselect pwbe-filter-wpml-container pwbe-filter-wpml-template">
			<select name="filter_select[]" class="pwbe-filter-field pwbe-filter-select pwbe-filter-value" multiple="multiple" ></select>
		</span>
		<?php
	}

	function pwbe_select_options( $select_options ) {
		global $sitepress;

		$active_languages = $sitepress->get_active_languages();

		foreach ( $active_languages as $lang ) {
			$select_options['wpml'][ $lang['code'] ]['name'] = $lang['display_name'];
			$select_options['wpml'][ $lang['code'] ]['visibility'] = 'both';
		}

	    return $select_options;
	}

	function pwbe_skip_sorting_options( $options ) {
		$options[] = 'wpml';
		return $options;
	}

	function pwbe_search_row_sql( $row_sql, $search_class, $field_name, $filter_type, $field_value, $field_value2 ) {
		if ( 'wpml' == $field_name ) {
			$row_sql = $this->language_search( $filter_type, $field_value );
		}

		return $row_sql;
	}

    function language_search( $filter_type, $values ) {
        global $wpdb;

        if ( !is_array( $values ) ) {
            $values = array();
        }

        $placeholders = implode( ', ', array_fill( 0, count( $values ), '%s' ) );

        switch( $filter_type ) {
            case 'is any of':
            case 'is none of':
                $negator = ( $filter_type == 'is none of' ) ? 'NOT' : '';
                return $wpdb->prepare("$negator EXISTS (SELECT 1 FROM {$wpdb->prefix}icl_translations AS lang WHERE lang.element_id = parent.ID AND lang.language_code IN ($placeholders))", $values);
            break;

            case 'is empty':
            case 'is not empty':
                $negator = ( $filter_type == 'is empty' ) ? 'NOT' : '';
                return "$negator EXISTS (SELECT 1 FROM {$wpdb->prefix}icl_translations AS lang WHERE lang.element_id = parent.ID)";
            break;
        }
    }
}

new PWBE_WPML();

endif;

?>