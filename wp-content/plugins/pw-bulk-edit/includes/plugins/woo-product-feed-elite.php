<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

//
// Product Feed ELITE for WooCommerce by AdTribes.io
// https://wwww.adtribes.io/pro-vs-elite/
//
if ( !defined( 'WOOCOMMERCESEA_ELITE_PLUGIN_VERSION' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_WOOCOMMERCESEA_ELITE_PLUGIN_VERSION' ) ) :

final class PWBE_WOOCOMMERCESEA_ELITE_PLUGIN_VERSION {

    function __construct() {
        add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
        add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
        add_filter( 'pwbe_skip_sorting_options', array( $this, 'pwbe_skip_sorting_options' ) );
    }

    function pwbe_product_columns( $columns ) {

        if ( ( get_option( 'add_unique_identifiers' ) != 'yes' ) ) {
            return $columns;
        }

        $extra_attributes = get_option( 'woosea_extra_attributes' );

        if ( array_key_exists( 'custom_attributes__woosea_brand', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Brand', 'woosea' ),
                'type' => 'text',
                'table' => 'meta',
                'field' => '_woosea_brand',
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_gtin', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'GTIN', 'woosea' ),
                'type' => 'text',
                'table' => 'meta',
                'field' => '_woosea_gtin',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_mpn', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'MPN', 'woosea' ),
                'type' => 'text',
                'table' => 'meta',
                'field' => '_woosea_mpn',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_upc', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'UPC', 'woosea' ),
                'type' => 'text',
                'table' => 'meta',
                'field' => '_woosea_upc',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_ean', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'EAN', 'woosea' ),
                'type' => 'text',
                'table' => 'meta',
                'field' => '_woosea_ean',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_optimized_title', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Optimized title', 'woosea' ),
                'type' => 'text',
                'table' => 'meta',
                'field' => '_woosea_optimized_title',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_condition', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Product condition', 'woosea' ),
                'type' => 'select',
                'table' => 'meta',
                'field' => '_woosea_condition',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_color', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Color', 'woosea' ),
                'type' => 'text',
                'table' => 'meta',
                'field' => '_woosea_color',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_size', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Size', 'woosea' ),
                'type' => 'text',
                'table' => 'meta',
                'field' => '_woosea_size',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_gender', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Gender', 'woosea' ),
                'type' => 'select',
                'table' => 'meta',
                'field' => '_woosea_gender',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_material', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Material', 'woosea' ),
                'type' => 'text',
                'table' => 'meta',
                'field' => '_woosea_material',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_pattern', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Pattern', 'woosea' ),
                'type' => 'text',
                'table' => 'meta',
                'field' => '_woosea_pattern',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_age_group', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Age group', 'woosea' ),
                'type' => 'select',
                'table' => 'meta',
                'field' => '_woosea_age_group',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_unit_pricing_measure', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Unit pricing measure', 'woosea' ),
                'type' => 'text',
                'table' => 'meta',
                'field' => '_woosea_unit_pricing_measure',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_unit_pricing_base_measure', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Unit pricing base measure', 'woosea' ),
                'type' => 'text',
                'table' => 'meta',
                'field' => '_woosea_unit_pricing_base_measure',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_installment_months', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Installment months', 'woosea' ),
                'type' => 'text',
                'table' => 'meta',
                'field' => '_woosea_installment_months',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_installment_amount', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Installment amount', 'woosea' ),
                'type' => 'text',
                'table' => 'meta',
                'field' => '_woosea_installment_amount',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_cost_of_good_sold', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Cost of goods sold', 'woosea' ),
                'type' => 'text',
                'table' => 'meta',
                'field' => '_woosea_cost_of_good_sold',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_multipack', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Multipack', 'woosea' ),
                'type' => 'text',
                'table' => 'meta',
                'field' => '_woosea_multipack',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_is_bundle', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Is bundle', 'woosea' ),
                'type' => 'select',
                'table' => 'meta',
                'field' => '_woosea_is_bundle',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_energy_efficiency_class', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Energy efficiency class', 'woosea' ),
                'type' => 'select',
                'table' => 'meta',
                'field' => '_woosea_energy_efficiency_class',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_min_energy_efficiency_class', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Minimum energy efficiency class', 'woosea' ),
                'type' => 'select',
                'table' => 'meta',
                'field' => '_woosea_min_energy_efficiency_class',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_max_energy_efficiency_class', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Maximum energy efficiency class', 'woosea' ),
                'type' => 'select',
                'table' => 'meta',
                'field' => '_woosea_max_energy_efficiency_class',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_is_promotion', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Is promotion', 'woosea' ),
                'type' => 'text',
                'table' => 'meta',
                'field' => '_woosea_is_promotion',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_custom_field_0', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Custom field 0', 'woosea' ),
                'type' => 'text',
                'table' => 'meta',
                'field' => '_woosea_custom_field_0',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_custom_field_1', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Custom field 1', 'woosea' ),
                'type' => 'text',
                'table' => 'meta',
                'field' => '_woosea_custom_field_1',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_custom_field_2', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Custom field 2', 'woosea' ),
                'type' => 'text',
                'table' => 'meta',
                'field' => '_woosea_custom_field_2',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_custom_field_3', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Custom field 3', 'woosea' ),
                'type' => 'text',
                'table' => 'meta',
                'field' => '_woosea_custom_field_3',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        if ( array_key_exists( 'custom_attributes__woosea_custom_field_4', $extra_attributes ) ) {
            $new_columns[] = array(
                'name' => __( 'Custom field 4', 'woosea' ),
                'type' => 'text',
                'table' => 'meta',
                'field' => '_woosea_custom_field_4',
                'readonly' => 'false',
                'visibility' => 'parent_variation',
                'sortable' => 'true',
                'views' => array( 'all', 'standard' ),
            );
        }

        $new_columns[] = array(
            'name' => __( 'Exclude from feeds', 'woosea' ),
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => '_woosea_exclude_product',
            'readonly' => 'false',
            'visibility' => 'parent_variation',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $start_index = 1;
        foreach ( $columns as $index => $column ) {
            if ( $column['field'] === '_weight' ) {
                $start_index = $index;
                break;
            }
        }

        array_splice( $columns, $start_index, 0, $new_columns );

        return $columns;
    }

    function pwbe_select_options( $select_options ) {
        global $wpdb;

        $items = array (
            ''      => __( '', 'woosea' ),
            'new'       => __( 'new', 'woosea' ),
            'refurbished'   => __( 'refurbished', 'woosea' ),
            'used'      => __( 'used', 'woosea' ),
            'damaged'   => __( 'damaged', 'woosea' ),
        );
        foreach ( $items as $slug => $name ) {
            $select_options[ '_woosea_condition' ][ $slug ]['name'] = esc_html( $name );
            $select_options[ '_woosea_condition' ][ $slug ]['visibility'] = 'both';
        }


        $items = array (
            ''      => __( '', 'woosea' ),
            'female'    => __( 'female', 'woosea' ),
            'male'      => __( 'male', 'woosea' ),
            'unisex'    => __( 'unisex', 'woosea' ),
        );
        foreach ( $items as $slug => $name ) {
            $select_options[ '_woosea_gender' ][ $slug ]['name'] = esc_html( $name );
            $select_options[ '_woosea_gender' ][ $slug ]['visibility'] = 'both';
        }


        $items = array (
            ''      => __( '', 'woosea' ),
            'newborn'   => __( 'newborn', 'woosea' ),
            'infant'    => __( 'infant', 'woosea' ),
            'toddler'   => __( 'toddler', 'woosea' ),
            'kids'      => __( 'kids', 'woosea' ),
            'adult'     => __( 'adult', 'woosea' ),
        );
        foreach ( $items as $slug => $name ) {
            $select_options[ '_woosea_age_group' ][ $slug ]['name'] = esc_html( $name );
            $select_options[ '_woosea_age_group' ][ $slug ]['visibility'] = 'both';
        }


        $items = array (
            ''      => __( '', 'woosea' ),
            'yes'       => __( 'yes', 'woosea' ),
            'no'        => __( 'no', 'woosea' ),
        );
        foreach ( $items as $slug => $name ) {
            $select_options[ '_woosea_is_bundle' ][ $slug ]['name'] = esc_html( $name );
            $select_options[ '_woosea_is_bundle' ][ $slug ]['visibility'] = 'both';
        }


        $items = array (
            ''      => __( '', 'woosea' ),
            'A+++'      => __( 'A+++', 'woosea' ),
            'A++'       => __( 'A++', 'woosea' ),
            'A+'        => __( 'A+', 'woosea' ),
            'A'     => __( 'A', 'woosea' ),
            'B'     => __( 'B', 'woosea' ),
            'C'     => __( 'C', 'woosea' ),
            'D'     => __( 'D', 'woosea' ),
            'E'     => __( 'E', 'woosea' ),
            'F'     => __( 'F', 'woosea' ),
            'G'     => __( 'G', 'woosea' ),
        );
        foreach ( $items as $slug => $name ) {
            $select_options[ '_woosea_energy_efficiency_class' ][ $slug ]['name'] = esc_html( $name );
            $select_options[ '_woosea_energy_efficiency_class' ][ $slug ]['visibility'] = 'both';
        }


        $items = array (
            ''      => __( '', 'woosea' ),
            'A+++'      => __( 'A+++', 'woosea' ),
            'A++'       => __( 'A++', 'woosea' ),
            'A+'        => __( 'A+', 'woosea' ),
            'A'     => __( 'A', 'woosea' ),
            'B'     => __( 'B', 'woosea' ),
            'C'     => __( 'C', 'woosea' ),
            'D'     => __( 'D', 'woosea' ),
            'E'     => __( 'E', 'woosea' ),
            'F'     => __( 'F', 'woosea' ),
            'G'     => __( 'G', 'woosea' ),
        );
        foreach ( $items as $slug => $name ) {
            $select_options[ '_woosea_min_energy_efficiency_class' ][ $slug ]['name'] = esc_html( $name );
            $select_options[ '_woosea_min_energy_efficiency_class' ][ $slug ]['visibility'] = 'both';
        }


        $items = array (
            ''      => __( '', 'woosea' ),
            'A+++'      => __( 'A+++', 'woosea' ),
            'A++'       => __( 'A++', 'woosea' ),
            'A+'        => __( 'A+', 'woosea' ),
            'A'     => __( 'A', 'woosea' ),
            'B'     => __( 'B', 'woosea' ),
            'C'     => __( 'C', 'woosea' ),
            'D'     => __( 'D', 'woosea' ),
            'E'     => __( 'E', 'woosea' ),
            'F'     => __( 'F', 'woosea' ),
            'G'     => __( 'G', 'woosea' ),
        );
        foreach ( $items as $slug => $name ) {
            $select_options[ '_woosea_max_energy_efficiency_class' ][ $slug ]['name'] = esc_html( $name );
            $select_options[ '_woosea_max_energy_efficiency_class' ][ $slug ]['visibility'] = 'both';
        }

        return $select_options;
    }

    function pwbe_skip_sorting_options( $options ) {
        $options[] = '_woosea_condition';
        $options[] = '_woosea_gender';
        $options[] = '_woosea_age_group';
        $options[] = '_woosea_is_bundle';
        $options[] = '_woosea_energy_efficiency_class';
        $options[] = '_woosea_min_energy_efficiency_class';
        $options[] = '_woosea_max_energy_efficiency_class';

        return $options;
    }
}

new PWBE_WOOCOMMERCESEA_ELITE_PLUGIN_VERSION();

endif;

?>