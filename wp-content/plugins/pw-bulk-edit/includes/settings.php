<?php

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'PWBE_Settings' ) ) :

class PWBE_Settings {

    public $settings;

    function __construct() {
        $this->settings = array(
            array(
                'title' => __( 'PW Bulk Edit', 'pw-bulk-edit' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'pw_bulk_edit_options',
            ),
            array(
                'title'    => __( 'Row Border', 'pw-bulk-edit' ),
                'desc'     => __( 'Show a border around the row that is currently being edited. Default: Checked', 'pw-bulk-edit' ),
                'id'       => 'pwbe_editing_row_border',
                'default'  => 'yes',
                'type'     => 'checkbox',
                'desc_tip' => false,
            ),
            array(
                'title'    => __( 'Row Limit', 'pw-bulk-edit' ),
                'desc'     => __( 'The maximum number of rows to return. A higher limit requires more resources. Default: 1000', 'pw-bulk-edit' ),
                'id'       => 'pwbe_max_results',
                'default'  => PWBE_MAX_RESULTS,
                'type'     => 'number',
                'desc_tip' => false,
            ),
            array(
                'title'    => __( 'Save Batch Size', 'pw-bulk-edit' ),
                'desc'     => __( 'The maximum number of changes to save in a single batch. A higher limit requires more resources. Default: 25', 'pw-bulk-edit' ),
                'id'       => 'pwbe_save_batch_size',
                'default'  => PWBE_SAVE_BATCH_SIZE,
                'type'     => 'number',
                'desc_tip' => false,
            ),
            array(
                'title'    => __( 'Linked Products', 'pw-bulk-edit' ),
                'desc'     => __( 'Show the Cross Sells and Up-Sells so you can bulk edit by entering the ID of the corresponding products. Default: Unchecked', 'pw-bulk-edit' ),
                'id'       => 'pwbe_linked_products',
                'default'  => defined( 'PWBE_LINKED_PRODUCTS' ) ? 'yes' : 'no',
                'type'     => 'checkbox',
                'desc_tip' => false,
            ),
            array(
                'title'    => __( 'Print Mode', 'pw-bulk-edit' ),
                'desc'     => __( 'When printing the results, hide everything except for the results grid. Default: Checked', 'pw-bulk-edit' ),
                'id'       => 'pwbe_include_print_css',
                'default'  => 'yes',
                'type'     => 'checkbox',
                'desc_tip' => false,
            ),
            array(
                'type'  => 'sectionend',
                'id'    => 'pw_bulk_edit_options',
            ),
        );

        add_action( 'woocommerce_get_settings_pages', array( $this, 'woocommerce_get_settings_pages' ), 11 );
    }

    function woocommerce_get_settings_pages( $settings ) {
        // Fix for a conflict with the "Hide Price Until Login" plugin by CedCommerce.
        if ( !is_array( $settings ) ) {
            $settings = array();
        }

        $settings[] = include( 'class-wc-settings-pw-bulk-edit.php' );

        return $settings;
    }

}

global $pwbe_settings;
$pwbe_settings = new PWBE_Settings();

endif;
