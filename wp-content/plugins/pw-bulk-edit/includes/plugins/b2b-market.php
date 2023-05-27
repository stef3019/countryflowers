<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'B2B_PLUGIN_PATH' ) ) {
	return;
}

if ( ! class_exists( 'PWBE_B2B_Market' ) ) :

final class PWBE_B2B_Market {

    private $meta_prefix;
    private $slug;
    private $price_types;
    private $groups;

	function __construct() {
        $this->meta_prefix = 'bm_';

        $this->slug        = 'customer_groups';

        $this->price_types = array(
            __( 'Fixed Price', 'b2b-market' )            => 'fix',
            __( 'Discount (fixed Value)', 'b2b-market' ) => 'discount',
            __( 'Discount (%)', 'b2b-market' )           => 'discount-percent',
        );

		add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
        add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
        add_filter( 'pwbe_skip_sorting_options', array( $this, 'pwbe_skip_sorting_options' ) );
        add_filter( 'pwbe_save_field_value', array( $this, 'pwbe_save_field_value' ), 10, 2 );
	}

    function get_groups() {
        if ( ! $this->groups ) {
            $this->groups = get_posts( array(
                'posts_per_page' => - 1,
                'post_type'      => 'customer_groups',
            ) );
        }

        return $this->groups;
    }

    function group_slug( $group ) {
        return $group_slug = $this->meta_prefix . $group->post_name;
    }

	function pwbe_product_columns( $columns ) {
        $new_columns = array();

        foreach ( $this->get_groups() as $group ) {

            $new_columns[] = array(
                'name' => __( 'Price:', 'b2b-market' ) . ' ' . get_the_title( $group ),
                'type' => 'currency',
                'table' => 'meta',
                'field' => $this->group_slug( $group ) . '_price',
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );

            $new_columns[] = array(
                'name' => __( 'Price-Type:', 'b2b-market' ) . ' ' . get_the_title( $group ),
                'type' => 'select',
                'table' => 'meta',
                'field' => $this->group_slug( $group ) . '_price_type',
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( !empty( $new_columns ) ) {
            // Insert after the "Sale End Date" column.
            $start_index = 1;
            foreach ( $columns as $index => $column ) {
                if ( $column['field'] === '_sale_price_dates_to' ) {
                    $start_index = $index + 1;
                    break;
                }
            }

            array_splice( $columns, $start_index, 0, $new_columns );
        }

		return $columns;
	}

    function pwbe_select_options( $select_options ) {
        global $wpdb;

        if ( !empty( $this->price_types ) ) {
            foreach ( $this->price_types as $price_type => $price_type_slug ) {
                foreach ( $this->get_groups() as $group ) {
                    $select_options[$this->group_slug( $group ) . '_price_type'][ $price_type_slug ]['name'] = esc_html( $price_type );
                    $select_options[$this->group_slug( $group ) . '_price_type'][ $price_type_slug ]['visibility'] = 'both';
                }
            }
        }

        return $select_options;
    }

    function pwbe_skip_sorting_options( $options ) {
        foreach ( $this->get_groups() as $group ) {
            $options[] = $this->group_slug( $group ) . '_price_type';
        }

        return $options;
    }

    function pwbe_save_field_value( $value, $field ) {
        if ( $field['post_id'] != $field['parent_post_id'] ) {
            foreach ( $this->get_groups() as $group ) {
                if ( $field['field'] == $this->group_slug( $group ) . '_price' ) {
                    update_post_meta( $field['parent_post_id'], $this->group_slug( $group ) . '_' . $field['post_id'] . '_price', $value );
                } else if ( $field['field'] == $this->group_slug( $group ) . '_price_type' ) {
                    update_post_meta( $field['parent_post_id'], $this->group_slug( $group ) . '_' . $field['post_id'] . '_price_type', $value );
                }
            }
        }

        return $value;
    }
}

new PWBE_B2B_Market();

endif;

?>