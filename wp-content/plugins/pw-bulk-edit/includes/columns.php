<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'PWBE_Columns' ) ) :

final class PWBE_Columns {

    private static $columns = null;

    public static function get_by_field( $field ) {
        if ( PWBE_Columns::$columns === null ) {
            PWBE_Columns::load();
        }

        foreach ( PWBE_Columns::$columns as $column ) {
            if ( $column['field'] == $field ) {
                return $column;
            }
        }

        return null;
    }

    public static function get() {
        if ( PWBE_Columns::$columns === null ) {
            PWBE_Columns::load();
        }

        return PWBE_Columns::$columns;
    }

    private static function load() {
        global $wpdb;

        $product_columns[] = array(
            'name' => __( 'Image', 'woocommerce' ),
            'type' => 'image',
            'table' => 'meta',
            'field' => '_thumbnail_id',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' )
        );

        $product_columns[] = array(
            'name' => __( 'Product name', 'woocommerce' ),
            'type' => 'text',
            'table' => 'post',
            'field' => 'post_title',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' )
        );

        $product_columns[] = array(
            'name' => __( 'Type', 'woocommerce' ),
            'type' => 'select',
            'table' => 'product_type',
            'field' => 'product_type',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $product_columns[] = array(
            'name' => __( 'Status', 'woocommerce' ),
            'type' => 'select',
            'table' => 'post',
            'field' => 'post_status',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $product_columns[] = array(
            'name' => __( 'Regular price', 'woocommerce' ),
            'type' => 'currency',
            'table' => 'meta',
            'field' => '_regular_price',
            'readonly' => 'false',
            'visibility' => 'parent_variation',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' )
        );

        $product_columns[] = array(
            'name' => __( 'Sale price', 'woocommerce' ),
            'type' => 'currency',
            'table' => 'meta',
            'field' => '_sale_price',
            'readonly' => 'false',
            'visibility' => 'parent_variation',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' )
        );

        $product_columns[] = array(
            'name' => __( 'Sale start date', 'woocommerce' ),
            'type' => 'date',
            'table' => 'meta',
            'field' => '_sale_price_dates_from',
            'readonly' => 'false',
            'visibility' => 'parent_variation',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' )
        );

        $product_columns[] = array(
            'name' => __( 'Sale end date', 'woocommerce' ),
            'type' => 'date',
            'table' => 'meta',
            'field' => '_sale_price_dates_to',
            'readonly' => 'false',
            'visibility' => 'parent_variation',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' )
        );

        $product_columns[] = array(
            'name' => __( 'Product description', 'woocommerce' ),
            'type' => 'textarea',
            'table' => 'post',
            'field' => 'post_content',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' )
        );

        $product_columns[] = array(
            'name' => __( 'Short description', 'woocommerce' ),
            'type' => 'textarea',
            'table' => 'post',
            'field' => 'post_excerpt',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $product_columns[] = array(
            'name' => __( 'Variation description', 'woocommerce' ),
            'type' => 'textarea',
            'table' => 'meta',
            'field' => '_variation_description',
            'readonly' => 'false',
            'visibility' => 'variation',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' )
        );

        $product_columns[] = array(
            'name' => __( 'SKU', 'woocommerce' ),
            'type' => 'text',
            'table' => 'meta',
            'field' => '_sku',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        if ( class_exists( 'YITH_Vendors' ) ) {
            $product_columns[] = array(
                'name' => __( 'Vendor', 'yith' ),
                'type' => 'select',
                'table' => 'terms',
                'field' => 'yith_shop_vendor',
                'readonly' => 'false',
                'visibility' => 'parent',
                'sortable' => 'true',
                'views' => array( 'all' )
            );
        }

        $product_columns[] = array(
            'name' => __( 'Categories', 'woocommerce' ),
            'type' => 'multiselect',
            'table' => 'terms',
            'field' => 'categories',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'false',
            'views' => array( 'all', 'standard' )
        );

        $product_columns[] = array(
            'name' => __( 'Tags', 'woocommerce' ),
            'type' => 'multiselect',
            'table' => 'terms',
            'field' => 'tags',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'false',
            'views' => array( 'all', 'standard' )
        );

        $attributes = PWBE_Attributes::get_attributes();
        foreach ( $attributes as $attribute ) {
            $product_columns[] = array(
                'name' => sprintf( __( '%s attributes', 'pw-bulk-edit' ), $attribute['name'] ),
                'type' => 'multiselect',
                'table' => 'terms',
                'field' => $attribute['slug'],
                'readonly' => 'false',
                'visibility' => 'parent',
                'sortable' => 'false',
                'views' => array( 'all' )
            );
        }

        foreach ( $attributes as $attribute ) {
            $product_columns[] = array(
                'name' => sprintf( __( 'Variation %s', 'pw-bulk-edit' ), $attribute['name'] ),
                'type' => 'select',
                'table' => 'meta',
                'field' => 'attribute_' . $attribute['slug'],
                'readonly' => 'false',
                'visibility' => 'variation',
                'sortable' => 'true',
                'views' => array( 'all' )
            );
        }

        foreach ( $attributes as $attribute ) {
            $product_columns[] = array(
                'name' => sprintf( __( 'Default %s', 'pw-bulk-edit' ), $attribute['name'] ),
                'type' => 'select',
                'table' => 'meta',
                'field' => '_default_attribute_' . $attribute['slug'],
                'readonly' => 'false',
                'visibility' => 'parent',
                'sortable' => 'false',
                'views' => array( 'all' )
            );
        }

        foreach ( $attributes as $attribute ) {
            $product_columns[] = array(
                'name' => sprintf( __( '%s visibility', 'pw-bulk-edit' ), $attribute['name'] ),
                'type' => 'checkbox',
                'table' => 'attributes',
                'field' => '_attribute_visibility_' . $attribute['slug'],
                'readonly' => 'false',
                'visibility' => 'parent',
                'sortable' => 'false',
                'views' => array( 'all' )
            );
        }

        if ( taxonomy_exists( 'product_brand' ) ) {

            $product_columns[] = array(
                'name' => __( 'Brands', 'woocommerce' ),
                'type' => 'multiselect',
                'table' => 'terms',
                'field' => 'brands',
                'readonly' => 'false',
                'visibility' => 'parent',
                'sortable' => 'false',
                'views' => array( 'all', 'standard' )
            );

        }

        if ( taxonomy_exists( 'yith_product_brand' ) ) {

            $product_columns[] = array(
                'name' => __( 'YITH Brands', 'woocommerce' ),
                'type' => 'multiselect',
                'table' => 'terms',
                'field' => 'yith_brands',
                'readonly' => 'false',
                'visibility' => 'parent',
                'sortable' => 'false',
                'views' => array( 'all', 'standard' )
            );

        }

        if ( function_exists( 'wc_tax_enabled' ) ) {
            $tax_enabled = wc_tax_enabled();
        } else {
            $tax_enabled = apply_filters( 'wc_tax_enabled', get_option( 'woocommerce_calc_taxes' ) === 'yes' );
        }
        if ( $tax_enabled ) {
            $product_columns[] = array(
                'name' => __( 'Tax status', 'woocommerce' ),
                'type' => 'select',
                'table' => 'meta',
                'field' => '_tax_status',
                'readonly' => 'false',
                'visibility' => 'parent',
                'sortable' => 'true',
                'views' => array( 'all' )
            );

            $product_columns[] = array(
                'name' => __( 'Tax class', 'woocommerce' ),
                'type' => 'select',
                'table' => 'meta',
                'field' => '_tax_class',
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all' )
            );
        }

        $product_columns[] = array(
            'name' => __( 'Weight', 'woocommerce' ),
            'type' => 'number',
            'table' => 'meta',
            'field' => '_weight',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $product_columns[] = array(
            'name' => __( 'Length', 'woocommerce' ),
            'type' => 'number',
            'table' => 'meta',
            'field' => '_length',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $product_columns[] = array(
            'name' => __( 'Width', 'woocommerce' ),
            'type' => 'number',
            'table' => 'meta',
            'field' => '_width',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $product_columns[] = array(
            'name' => __( 'Height', 'woocommerce' ),
            'type' => 'number',
            'table' => 'meta',
            'field' => '_height',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $product_columns[] = array(
            'name' => __( 'Shipping class', 'woocommerce' ),
            'type' => 'select',
            'table' => 'terms',
            'field' => 'product_shipping_class',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {
            $product_columns[] = array(
                'name' => __( 'Manage stock', 'woocommerce' ),
                'type' => 'checkbox',
                'table' => 'meta',
                'field' => '_manage_stock',
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all' )
            );

            $product_columns[] = array(
                'name' => __( 'Stock quantity', 'woocommerce' ),
                'type' => 'number',
                'table' => 'meta',
                'field' => '_stock',
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all' )
            );

            $product_columns[] = array(
                'name' => __( 'Allow backorders', 'woocommerce' ),
                'type' => 'select',
                'table' => 'meta',
                'field' => '_backorders',
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all' )
            );

            $product_columns[] = array(
                'name' => __( 'Low stock threshold', 'woocommerce' ),
                'type' => 'number',
                'table' => 'meta',
                'field' => '_low_stock_amount',
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all' )
            );
        }

        $product_columns[] = array(
            'name' => __( 'Stock status', 'woocommerce' ),
            'type' => 'select',
            'table' => 'meta',
            'field' => '_stock_status',
            'readonly' => 'false',
            'visibility' => 'parent_variation',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $product_columns[] = array(
            'name' => __( 'Sold individually', 'woocommerce' ),
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => '_sold_individually',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $product_columns[] = array(
            'name' => __( 'External/Affiliate', 'woocommerce' ) . ' ' . __( 'Product URL', 'woocommerce' ),
            'type' => 'text',
            'table' => 'meta',
            'field' => '_product_url',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $product_columns[] = array(
            'name' => __( 'External/Affiliate', 'woocommerce' ) . ' ' . __( 'Button text', 'woocommerce' ),
            'type' => 'text',
            'table' => 'meta',
            'field' => '_button_text',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $product_columns[] = array(
            'name' => __( 'Virtual', 'woocommerce' ),
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => '_virtual',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $product_columns[] = array(
            'name' => __( 'Downloadable', 'woocommerce' ),
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => '_downloadable',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $product_columns[] = array(
            'name' => __( 'Download limit', 'woocommerce' ),
            'type' => 'number',
            'table' => 'meta',
            'field' => '_download_limit',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $product_columns[] = array(
            'name' => __( 'Download expiry', 'woocommerce' ),
            'type' => 'number',
            'table' => 'meta',
            'field' => '_download_expiry',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        for ( $x = 0; $x < PWBE_DOWNLOADABLE_FILE_COUNT; $x++ ) {
            $product_columns[] = array(
                'name' => sprintf( __( 'Download #%s Name', 'pw-bulk-edit' ), ( $x + 1 ) ),
                'type' => 'text',
                'table' => 'meta',
                'field' => "_downloadable_files___{$x}___name",
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all' )
            );

            $product_columns[] = array(
                'name' => sprintf( __( 'Download #%s File URL', 'pw-bulk-edit' ), ( $x + 1 ) ),
                'type' => 'text',
                'table' => 'meta',
                'field' => "_downloadable_files___{$x}___file",
                'readonly' => 'false',
                'visibility' => 'both',
                'sortable' => 'true',
                'views' => array( 'all' )
            );
        }

        $product_columns[] = array(
            'name' => __( 'Purchase note', 'woocommerce' ),
            'type' => 'textarea',
            'table' => 'meta',
            'field' => '_purchase_note',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $product_columns[] = array(
            'name' => __( 'Enable reviews', 'woocommerce' ),
            'type' => 'checkbox',
            'table' => 'post',
            'field' => 'comment_status',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $product_columns[] = array(
            'name' => __( 'Slug', 'woocommerce' ),
            'type' => 'text',
            'table' => 'post',
            'field' => 'post_name',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $product_columns[] = array(
            'name' => __( 'Menu order', 'woocommerce' ),
            'type' => 'number',
            'table' => 'post',
            'field' => 'menu_order',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $product_columns[] = array(
            'name' => __( 'Catalog visibility', 'woocommerce' ),
            'type' => 'select',
            'table' => 'meta',
            'field' => '_visibility',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $product_columns[] = array(
            'name' => __( 'Featured', 'woocommerce' ),
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => '_featured',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $product_columns[] = array(
            'name' => __( 'Published on', 'woocommerce' ),
            'type' => 'text',
            'table' => 'post',
            'field' => 'post_date',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $product_columns[] = array(
            'name' => __( 'Last edited on', 'woocommerce' ),
            'type' => 'text',
            'table' => 'post',
            'field' => 'post_modified',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $product_columns[] = array(
            'name' => __( 'ID', 'woocommerce' ),
            'type' => 'number',
            'table' => 'post',
            'field' => 'post_id',
            'readonly' => 'true',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $product_columns = apply_filters( 'pwbe_product_columns', $product_columns );

        PWBE_Columns::$columns = $product_columns;
    }
}

endif;

?>