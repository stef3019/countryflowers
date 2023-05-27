<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'WE_XML_SKROUTZ_PLUGIN_VERSION' ) ) {
    return;
}

if ( ! class_exists( 'PWBE_WE_XML_SKROUTZ' ) ) :

final class PWBE_WE_XML_SKROUTZ {

    function __construct() {
        add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
        add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
        add_filter( 'pwbe_skip_sorting_options', array( $this, 'pwbe_skip_sorting_options' ) );
    }

    function pwbe_product_columns( $columns ) {
        $new_columns = array();

        $new_columns[] = array(
            'name' => 'Skroutz ' . __( 'Availability', 'webexpert-skroutz-xml-feed' ),
            'type' => 'select',
            'table' => 'meta',
            'field' => 'we_skroutzxml_custom_availability',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        $new_columns[] = array(
            'name' => 'Skroutz ' . __( 'Non Availability', 'webexpert-skroutz-xml-feed' ),
            'type' => 'select',
            'table' => 'meta',
            'field' => 'we_skroutzxml_custom_noavailability',
            'readonly' => 'false',
            'visibility' => 'both',
            'sortable' => 'true',
            'views' => array( 'all', 'standard' ),
        );

        if ( !empty( $new_columns ) ) {
            $start_index = 1;
            foreach ( $columns as $index => $column ) {
                // Insert the new columns after this column.
                if ( $column['field'] === 'product_shipping_class' ) {
                    $start_index = $index + 1;
                    break;
                }
            }

            array_splice( $columns, $start_index, 0, $new_columns );
        }

        return $columns;
    }

    function pwbe_select_options( $select_options ) {
        $select_options['we_skroutzxml_custom_availability'][PW_Bulk_Edit::NULL]['name'] = __( 'Default', 'webexpert-skroutz-xml-feed' );
        $select_options['we_skroutzxml_custom_availability'][PW_Bulk_Edit::NULL]['visibility'] = 'both';
        $select_options['we_skroutzxml_custom_availability']['']['name'] = __( 'Default', 'webexpert-skroutz-xml-feed' );
        $select_options['we_skroutzxml_custom_availability']['']['visibility'] = 'both';

        $select_options['we_skroutzxml_custom_availability']['Άμεση παραλαβή / Παράδoση 1 έως 3 ημέρες']['name'] = __( 'Available in store / Delivery 1 to 3 days', 'webexpert-skroutz-xml-feed' );
        $select_options['we_skroutzxml_custom_availability']['Άμεση παραλαβή / Παράδoση 1 έως 3 ημέρες']['visibility'] = 'both';

        $select_options['we_skroutzxml_custom_availability']['Παράδοση σε 1 - 3 ημέρες']['name'] = __( 'Delivery 1 to 3 days', 'webexpert-skroutz-xml-feed' );
        $select_options['we_skroutzxml_custom_availability']['Παράδοση σε 1 - 3 ημέρες']['visibility'] = 'both';

        $select_options['we_skroutzxml_custom_availability']['Παράδοση σε 4 - 10 ημέρες']['name'] = __( 'Delivery 4 to 10 days', 'webexpert-skroutz-xml-feed' );
        $select_options['we_skroutzxml_custom_availability']['Παράδοση σε 4 - 10 ημέρες']['visibility'] = 'both';

        $select_options['we_skroutzxml_custom_availability']['Κατόπιν Παραγγελίας']['name'] = __( 'Upon order', 'webexpert-skroutz-xml-feed' );
        $select_options['we_skroutzxml_custom_availability']['Κατόπιν Παραγγελίας']['visibility'] = 'both';

        $select_options['we_skroutzxml_custom_availability']['Απόκρυψη από το XML']['name'] = __( 'Hide from XML Feed', 'webexpert-skroutz-xml-feed' );
        $select_options['we_skroutzxml_custom_availability']['Απόκρυψη από το XML']['visibility'] = 'both';



        $select_options['we_skroutzxml_custom_noavailability'][PW_Bulk_Edit::NULL]['name'] = __( 'Default', 'webexpert-skroutz-xml-feed' );
        $select_options['we_skroutzxml_custom_noavailability'][PW_Bulk_Edit::NULL]['visibility'] = 'both';
        $select_options['we_skroutzxml_custom_noavailability']['']['name'] = __( 'Default', 'webexpert-skroutz-xml-feed' );
        $select_options['we_skroutzxml_custom_noavailability']['']['visibility'] = 'both';

        $select_options['we_skroutzxml_custom_noavailability']['Παράδοση σε 1 - 3 ημέρες']['name'] = __( 'Delivery 1 to 3 days', 'webexpert-skroutz-xml-feed' );
        $select_options['we_skroutzxml_custom_noavailability']['Παράδοση σε 1 - 3 ημέρες']['visibility'] = 'both';

        $select_options['we_skroutzxml_custom_noavailability']['Παράδοση σε 4 - 10 ημέρες']['name'] = __( 'Delivery 4 to 10 days', 'webexpert-skroutz-xml-feed' );
        $select_options['we_skroutzxml_custom_noavailability']['Παράδοση σε 4 - 10 ημέρες']['visibility'] = 'both';

        $select_options['we_skroutzxml_custom_noavailability']['Κατόπιν Παραγγελίας']['name'] = __( 'Upon order', 'webexpert-skroutz-xml-feed' );
        $select_options['we_skroutzxml_custom_noavailability']['Κατόπιν Παραγγελίας']['visibility'] = 'both';

        $select_options['we_skroutzxml_custom_noavailability']['Απόκρυψη από το XML']['name'] = __( 'Hide from XML Feed', 'webexpert-skroutz-xml-feed' );
        $select_options['we_skroutzxml_custom_noavailability']['Απόκρυψη από το XML']['visibility'] = 'both';

        return $select_options;
    }

    function pwbe_skip_sorting_options( $options ) {
        $options[] = 'we_skroutzxml_custom_availability';
        $options[] = 'we_skroutzxml_custom_noavailability';

        return $options;
    }
}

new PWBE_WE_XML_SKROUTZ();

endif;


