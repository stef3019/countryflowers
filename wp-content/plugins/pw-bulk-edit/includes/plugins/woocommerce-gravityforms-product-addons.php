<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'GFForms' ) || !class_exists( 'WC_GFPA_Main' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_WooCommerce_GravityForms_Product_Addons' ) ) :

final class PWBE_WooCommerce_GravityForms_Product_Addons {

    function __construct() {
        add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
        add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
    }

    function pwbe_select_options( $select_options ) {
        $select_options['_gravity_form_data___id'][ PW_Bulk_Edit::NULL ]['name'] = __( 'None', 'wc_gf_addons' );
        $select_options['_gravity_form_data___id'][ PW_Bulk_Edit::NULL ]['visibility'] = 'both';

        foreach ( RGFormsModel::get_forms() as $form ) {
            $select_options['_gravity_form_data___id'][ $form->id ]['name'] = wptexturize( $form->title );
            $select_options['_gravity_form_data___id'][ $form->id ]['visibility'] = 'both';
        }

        return $select_options;
    }

    function pwbe_product_columns( $columns ) {
        $columns[] = array(
            'name' => 'Gravity Form',
            'type' => 'select',
            'table' => 'meta',
            'field' => '_gravity_form_data___id',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $columns[] = array(
            'name' => 'GF Display Title',
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => '_gravity_form_data___display_title',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        $columns[] = array(
            'name' => 'GF Display Description',
            'type' => 'checkbox',
            'table' => 'meta',
            'field' => '_gravity_form_data___display_description',
            'readonly' => 'false',
            'visibility' => 'parent',
            'sortable' => 'true',
            'views' => array( 'all' )
        );

        return $columns;
    }
}

new PWBE_WooCommerce_GravityForms_Product_Addons();

endif;

?>