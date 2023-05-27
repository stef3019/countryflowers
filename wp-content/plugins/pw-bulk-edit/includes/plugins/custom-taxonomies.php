<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'PWBE_Custom_Taxonomies' ) ) :

class PWBE_Custom_Taxonomies {

    function __construct() {
        add_filter( 'pwbe_filter_types', array( $this, 'pwbe_filter_types' ) );
        add_filter( 'pwbe_where_clause', array( $this, 'pwbe_where_clause' ), 10, 6 );
        add_action( 'pwbe_after_dropdown_templates', array( $this, 'pwbe_after_dropdown_templates' ) );
        add_action( 'pwbe_after_filter_select_templates', array( $this, 'pwbe_after_filter_select_templates' ) );
        add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
        add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
        add_filter( 'pwbe_taxonomy_list', array( $this, 'pwbe_taxonomy_list' ) );
        add_filter( 'pwbe_terms_save_handled', array( $this, 'pwbe_terms_save_handled' ), 10, 3 );
    }

    function custom_taxonomies() {
        if ( isset( $GLOBALS['pw_bulk_edit_custom_taxonomies'] ) ) {
            if ( is_array( $GLOBALS['pw_bulk_edit_custom_taxonomies'] ) ) {
                if ( !empty( $GLOBALS['pw_bulk_edit_custom_taxonomies'] ) ) {
                    return $GLOBALS['pw_bulk_edit_custom_taxonomies'];
                }
            }
        }

        return array();
    }

    function pwbe_filter_types( $filter_types ) {
        foreach ( $this->custom_taxonomies() as $tax ) {
            $filter_types[ $tax['slug'] ] = array( 'name' => $tax['name'], 'type' => 'multiselect', 'key' => $tax['plural'] );
        }

        return $filter_types;
    }

    function pwbe_where_clause( $row_sql, $field_name, $filter_type, $field_value, $field_value2, $group_type ) {
        global $pwbe_sql_builder;

        foreach ( $this->custom_taxonomies() as $tax ) {
            if ( $field_name == $tax['slug'] ) {
                $row_sql = $pwbe_sql_builder->attributes_search( $field_name, $filter_type, $field_value );
            }
        }

        return $row_sql;
    }

    function pwbe_after_dropdown_templates() {
        foreach ( $this->custom_taxonomies() as $tax ) {
            ?>
            <select class="pwbe-dropdown-template-<?php echo $tax['plural']; ?>">
                <?php
                    $terms = get_terms( array( 'taxonomy' => $tax['plural'], 'hide_empty' => false ) );
                    foreach ( $terms as $term ) {
                        if ( !empty( $term ) && is_object( $term ) ) {
                            echo "<option value='{$term->slug}'>{$term->name}</option>\n";
                        }
                    }
                ?>
            </select>
            <?php
        }
    }

    function pwbe_after_filter_select_templates() {
        foreach ( $this->custom_taxonomies() as $tax ) {
            ?>
            <style type="text/css">
                .pwbe-filter-<?php echo $tax['plural']; ?>-template {
                    display: none;
                }
            </style>
            <span class="pwbe-multiselect pwbe-filter-<?php echo $tax['plural']; ?>-container pwbe-filter-<?php echo $tax['plural']; ?>-template">
                <select name="filter_select[]" class="pwbe-filter-field pwbe-filter-select pwbe-filter-value" multiple="multiple"></select>
            </span>
            <?php
        }
    }

    function pwbe_select_options( $select_options ) {
        foreach ( $this->custom_taxonomies() as $tax ) {
            $terms = get_terms( array( 'taxonomy' => $tax['slug'], 'hide_empty' => false ) );

            if ( !is_a( $terms, 'WP_Error' ) && is_array( $terms ) ) {
                foreach ( $terms as $term ) {
                    $select_options[ $tax['slug'] ][ $term->slug ]['name'] = esc_html( $term->name );
                    $select_options[ $tax['slug'] ][ $term->slug ]['visibility'] = 'parent';

                    $select_options[ $tax['plural'] ][ $term->slug ]['name'] = esc_html( $term->name );
                    $select_options[ $tax['plural'] ][ $term->slug ]['visibility'] = 'parent';
                }
            }
        }

        return $select_options;
    }

    function pwbe_product_columns( $columns ) {
        if ( empty( $this->custom_taxonomies() ) ) {
            return $columns;
        }

        // Where to place the new columns.
        $insert_after_column = __( 'Tags', 'woocommerce' );

        $insert_index = count( $columns );
        for ( $x = 0; $x < count( $columns ); $x++ ) {
            if ( $columns[ $x ]['name'] === $insert_after_column ) {
                $insert_index = $x + 1;
                break;
            }
        }

        foreach ( $this->custom_taxonomies() as $tax ) {
            $custom_label = array(
                    'name' => $tax['name'],
                    'type' => 'multiselect',
                    'table' => 'terms',
                    'field' => $tax['slug'],
                    'readonly' => 'false',
                    'visibility' => 'parent',
                    'sortable' => 'true',
                    'views' => array( 'standard', 'all' )
            );

            array_splice( $columns, $insert_index, 0, array( $custom_label ) );
        }

        return $columns;
    }

    function pwbe_taxonomy_list( $taxonomy_list ) {
        foreach ( $this->custom_taxonomies() as $tax ) {
            $taxonomy_list[] = $tax['slug'];
        }

        return $taxonomy_list;
    }

    function pwbe_terms_save_handled( $handled, $pwbe_save, $field ) {
        foreach ( $this->custom_taxonomies() as $tax ) {
            if ( $tax['slug'] === $field['field'] ) {
                $pwbe_save->save_taxonomies( $field['post_id'], $field['value'], $field['field'] );
                $handled = true;
            }
        }

        return $handled;
    }
}

new PWBE_Custom_Taxonomies();

endif;

?>