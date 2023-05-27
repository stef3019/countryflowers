<?php
/**
 * Plugin Name: PW WooCommerce Bulk Edit Pro
 * Plugin URI: https://www.pimwick.com/pw-bulk-edit/
 * Description: A powerful way to update your WooCommerce product catalog. Finally, no more tedious clicking through countless pages making the same change to all products!
 * Version: 2.300
 * Author: Pimwick, LLC
 * Author URI: https://www.pimwick.com
 * Text Domain: pw-bulk-edit
 * Domain Path: /languages
 *
 * WC requires at least: 2.6.5
 * WC tested up to: 6.0
 *
 * Copyright: © Pimwick, LLC
*/
define('PWBE_VERSION', '2.300');

// Exit if accessed directly
defined( 'ABSPATH' ) or exit;

// Increase the available memory since returning lots of data can often exhaust typical memory allocation amounts.
defined( 'PWBE_MEMORY_LIMIT' ) or define( 'PWBE_MEMORY_LIMIT', '1024M' );
if ( PWBE_MEMORY_LIMIT !== false ) {
    ini_set( 'memory_limit', PWBE_MEMORY_LIMIT );
    defined( 'WP_MEMORY_LIMIT' ) or define( 'WP_MEMORY_LIMIT', PWBE_MEMORY_LIMIT );
}

// Only change this if you are comfortable with possible unexpected behavior!
defined( 'PWBE_MAX_RESULTS' ) or define( 'PWBE_MAX_RESULTS', '1000' );

// Number of fields to save in a single AJAX call. Lower number can avoid HTTP 504 Timeout errors.
defined( 'PWBE_SAVE_BATCH_SIZE' ) or define( 'PWBE_SAVE_BATCH_SIZE', 25 );

// If the data contains product_variation records that are children of Simple Products you will want to
// enable this flag. This could make the query run slower.
defined( 'PWBE_PREFILTER_VARIATIONS' ) or define( 'PWBE_PREFILTER_VARIATIONS', false );

// How many downloadable files to show
defined( 'PWBE_DOWNLOADABLE_FILE_COUNT' ) or define( 'PWBE_DOWNLOADABLE_FILE_COUNT', 1 );

// Allow comma-separated strings to be pasted into the Select2 dropdowns (experimental).
defined( 'PWBE_PASTE_INTO_DROPDOWNS' ) or define( 'PWBE_PASTE_INTO_DROPDOWNS', false );

// For systems that have a lot of product records without any actual custom product attributes, this can slow
// things down or cause out of memory errors. Set this to false to disable custom product attribute lookup.
defined( 'PWBE_LOAD_CUSTOM_PRODUCT_ATTRIBUTES' ) or define( 'PWBE_LOAD_CUSTOM_PRODUCT_ATTRIBUTES', true );

defined( 'PWBE_THUMBNAIL_SIZE' ) or define( 'PWBE_THUMBNAIL_SIZE', 40 );

defined( 'PWBE_REQUIRES_CAPABILITY' ) or define( 'PWBE_REQUIRES_CAPABILITY', 'manage_woocommerce' );

// Verify this isn't called directly.
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

register_activation_hook( __FILE__, array( 'PW_Bulk_Edit', 'plugin_activate' ) );
register_deactivation_hook( __FILE__, array( 'PW_Bulk_Edit', 'plugin_deactivate' ) );

final class PW_Bulk_Edit {

    const FILTER_POST_TYPE = 'pwbe_filter';
    const NULL = '!!pwbe_null_value!!';

    static $options = array(
        'pwbe_help_dismiss_intro',
        'pwbe_help_minimize_filter_help',
        'pwbe_help_minimize_renew_notice',
        'pwbe_auto_create_variations'
    );

    function __construct() {
        require_once( 'includes/class-pimwick-license-manager.php' );
        $this->license = new Pimwick_License_Manager( __FILE__ );

        add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
        add_action( 'woocommerce_init', array( $this, 'woocommerce_init' ) );
    }

    function plugins_loaded() {
        load_plugin_textdomain( 'pw-bulk-edit', false, basename( dirname( __FILE__ ) ) . '/languages' );
    }

    function woocommerce_init() {
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'admin_print_styles', 'print_emoji_styles' );

        if ( is_admin() && current_user_can( get_option( 'pwbe_requires_capability', PWBE_REQUIRES_CAPABILITY ) ) ) {
            require( 'includes/attributes.php' );
            require( 'includes/columns.php' );
            require( 'includes/db.php' );
            require( 'includes/filters.php' );
            require( 'includes/select-options.php' );
            require( 'includes/settings.php' );
            require( 'includes/sql-builder.php' );
            require( 'includes/views.php' );

            foreach ( glob( dirname( __FILE__ ) . '/includes/plugins/*.php') as $file ) {
                require( $file );
            }

            add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 9999 );
            add_action( 'wp_ajax_pwbe_options', array( $this, 'ajax_options' ) );
            add_action( 'wp_ajax_pwbe_filter_manager', array( $this, 'ajax_filter_manager' ) );
            add_action( 'wp_ajax_pwbe_filter_results', array( $this, 'ajax_filter_results' ) );
            add_action( 'wp_ajax_pwbe_get_view', array( $this, 'ajax_get_view' ) );
            add_action( 'wp_ajax_pwbe_set_current_view', array( $this, 'ajax_set_current_view' ) );
            add_action( 'wp_ajax_pwbe_save_view', array( $this, 'ajax_save_view' ) );
            add_action( 'wp_ajax_pwbe_delete_view', array( $this, 'ajax_delete_view' ) );
            add_action( 'wp_ajax_pwbe_save_products', array( $this, 'ajax_save_products' ) );
            add_action( 'wp_ajax_pwbe_fix_attributes', array( $this, 'ajax_fix_attributes' ) );
            add_action( 'wp_ajax_pwbe_get_save_products_error', array( $this, 'ajax_get_save_products_error' ) );
            add_action( 'wp_ajax_pwbe_get_image_html', array( $this, 'ajax_get_image_html' ) );
            add_action( 'wp_ajax_pwbe_activation', array( $this, 'ajax_activation' ) );
        }

        add_action( 'init', array( $this, 'register_custom_post_types' ) );
    }

    public static function wc_min_version( $version ) {
        return version_compare( WC()->version, $version, ">=" );
    }

    public static function plugin_activate() {
        if ( ! current_user_can( 'activate_plugins' ) )
            return;

        foreach( PW_Bulk_Edit::$options as $option ) {
            add_option( $option, '', null, false );
        }
    }

    public static function plugin_deactivate() {
        if ( ! current_user_can( 'activate_plugins' ) )
            return;

        foreach( PW_Bulk_Edit::$options as $option ) {
            delete_option( $option );
        }
    }

    function index() {
        if ( isset( $_POST['activate-license'] ) ) {
            $this->license->activate_license( $_POST['license-key'] );
        }

        // Verify the saved views are valid. If not, clear the corrupted data to prevent issues creating new views.
        $pwbe_views = maybe_unserialize( get_option( 'pwbe_views' ) );
        if ( ! empty( $pwbe_views ) && ! is_array( $pwbe_views ) ) {
            delete_option( 'pwbe_views' );
            delete_option( 'pwbe_selected_view' );
        }

        $data = get_plugin_data( __FILE__ );
        $version = $data['Version'];
        $settings_url = add_query_arg( array( 'page' => 'wc-settings', 'tab' => 'pw-bulk-edit' ), admin_url( 'admin.php' ) );
        $help_url = plugins_url( '/docs/index.html', __FILE__ );

        require( 'ui/index.php' );
    }

    function register_custom_post_types() {
        if ( post_type_exists( PW_Bulk_Edit::FILTER_POST_TYPE ) ) {
            return;
        }

        register_post_type( PW_Bulk_Edit::FILTER_POST_TYPE,
            array(
                'labels' => array(
                    'name' => __( 'PW Bulk Edit Filters', 'pw-bulk-edit' ),
                    'singular_name' => __( 'PW Bulk Edit Filter', 'pw-bulk-edit' )
                ),
                'public' => false,
                'can_export' => true
            )
        );
    }

    function ajax_options() {
        $name = $_POST['option_name'];
        $value = $_POST['option_value'];

        if ( in_array( $name, PW_Bulk_Edit::$options ) ) {
            update_option( $name, $value );
        }
    }

    function ajax_filter_manager() {
        switch ( $_POST['function'] ) {
            case 'list':

                $filters = get_posts( array(
                    'post_type' => PW_Bulk_Edit::FILTER_POST_TYPE,
                    'post_status' => 'draft',
                    'posts_per_page' => -1,
                    'orderby' => 'title'
                ) );

                $results = array();
                foreach( $filters as $filter ) {
                    $results[] = array(
                        'post_id' => $filter->ID,
                        'name' => $filter->post_title
                    );
                }
                wp_send_json( $results );
            break;

            case 'open':
                $filter = get_post( $_POST['ID'] );
                if ( !empty( $filter ) ) {
                    echo $filter->post_content;
                } else {
                    echo '';
                }
            break;

            case 'save':
                $args = array(
                    'post_type'     => PW_Bulk_Edit::FILTER_POST_TYPE,
                    'post_title'    => $_POST['name'],
                    'post_content'  => $_POST['data']
                );

                if ( isset( $_POST['ID'] ) && !empty( $_POST['ID'] ) ) {
                    $post_id = $_POST['ID'];
                    $filter = get_post( $post_id );
                    if ( strtolower( $filter->post_title ) == strtolower( $_POST['name'] ) ) {
                        $args['ID'] = $post_id;
                    }
                }

                $id = wp_insert_post( $args );
                wp_send_json( array( 'ID' => $id ) );
            break;

            case 'rename':
                wp_update_post( array(
                    'post_type'     => PW_Bulk_Edit::FILTER_POST_TYPE,
                    'ID'            => $_POST['ID'],
                    'post_title'    => $_POST['name']
                ) );
            break;

            case 'download':
                $filter = get_post( $_POST['ID'] );
                wp_send_json( $filter );
            break;

            case 'delete':
                wp_delete_post( $_POST['ID'], true );
            break;
        }

        wp_die();
    }

    function ajax_filter_results() {
        global $sitepress;
        if ( isset( $sitepress ) ) {
            $sitepress->switch_lang( 'all' );
        }

        require( 'ui/results.php' );
        wp_die();
    }

    function ajax_get_view() {
        $view_name = stripslashes( $_POST['name'] );

        update_option( 'pwbe_selected_view', $view_name );

        $views = PWBE_Views::get();
        $view = $views[ $view_name ];

        wp_send_json( $view );

        wp_die();
    }

    function ajax_set_current_view() {
        $view_name = wc_clean( $_POST['name'] );

        update_option( 'pwbe_selected_view', $view_name );

        wp_send_json('success');

        wp_die();
    }

    function ajax_save_view() {
        $option_value = get_option( 'pwbe_views' );
        $views = maybe_unserialize( $option_value );

        $clean_name = stripslashes( $_POST['name'] );

        $views[ $clean_name ] = stripslashes( $_POST['view_data'] );

        ksort( $views, SORT_NATURAL );

        update_option( 'pwbe_views', $views );
        update_option( 'pwbe_selected_view', $clean_name );

        wp_die();
    }

    function ajax_delete_view() {
        $option_value = get_option( 'pwbe_views' );
        $views = maybe_unserialize( $option_value );

        $view_name = stripslashes( $_POST['name'] );

        unset( $views[ $view_name ] );

        update_option( 'pwbe_views', $views );
        update_option( 'pwbe_selected_view', '' );

        wp_die();
    }

    function ajax_save_products() {
        register_shutdown_function( array( $this, 'save_products_exception' ) );

        check_ajax_referer( 'pw-bulk-edit-save-products', 'security' );

        require( 'includes/save-products.php' );

        if ( isset( $_POST['fields'] ) ) {
            $fields = $_POST['fields'];

            $save = new PWBE_Save_Products();
            $results = $save->save( $fields );

            require( 'ui/products-saved.php' );
        }

        wp_die();
    }

    function maybe_create_product_attribute( $product, $taxonomy ) {
        require_once( 'includes/save-products.php' );
        $pwbe_save_products = new PWBE_Save_Products();

        $product_attributes = $product->get_attributes();
        if ( !is_array( $product_attributes ) ) {
            $product_attributes = array();
        }

        $attribute_name = 'pa_' . $taxonomy->attribute_name;
        if ( !isset( $product_attributes[ $attribute_name ] ) ) {
            $new_attribute = new WC_Product_Attribute();
            $new_attribute->set_id( $taxonomy->attribute_id );
            $new_attribute->set_name( $attribute_name );
            $new_attribute->set_position( $pwbe_save_products->get_new_attribute_position( $product_attributes ) );
            $new_attribute->set_visible( 1 );
            $new_attribute->set_variation( 1 );

            $product_attributes[ $attribute_name ] = $new_attribute;
            $product->set_attributes( $product_attributes );
            $product->save();
        }

        return $product_attributes[ $attribute_name ];
    }

    function ajax_fix_attributes() {

        $product = wc_get_product( absint( $_POST['post_id'] ) );

        $taxonomies = wc_get_attribute_taxonomies();
        foreach ( $taxonomies as $taxonomy ) {
            $taxonomy->terms = get_terms( 'pa_' . $taxonomy->attribute_name );
        }

        foreach ( $taxonomies as $taxonomy ) {
            $attribute_name = 'pa_' . $taxonomy->attribute_name;

            if ( is_a( $product, 'WC_Product_Simple' ) ) {
                $terms = wp_get_object_terms( $product->get_id(), $attribute_name );
                if ( !empty( $terms ) ) {

                    $attribute = $this->maybe_create_product_attribute( $product, $taxonomy );

                    $options = $attribute->get_options();
                    foreach ( $terms as $term ) {
                        if ( !in_array( $term->term_id, $options ) ) {
                            $options[] = $term->term_id;
                        }
                    }
                    $attribute->set_options( $options );
                    wp_set_object_terms( $product->get_id(), $options, $attribute_name );
                }
            } else if ( is_a( $product, 'WC_Product_Variable' ) ) {
                foreach ( $product->get_visible_children() as $variation_id ) {
                    $variation = wc_get_product( $variation_id );
                    $variation_attributes = $variation->get_attributes();
                    $attribute_value = get_post_meta( $variation->get_id(), 'attribute_' . $attribute_name, true );
                    if ( !empty( $attribute_value ) ) {

                        $attribute = $this->maybe_create_product_attribute( $product, $taxonomy );

                        $options = $attribute->get_options();
                        foreach ( $attribute->terms as $term ) {
                            if ( $term->slug == $attribute_value ) {
                                if ( !in_array( $term->term_id, $options ) ) {
                                    $options[] = $term->term_id;
                                }
                            }
                        }
                        $attribute->set_options( $options );
                        wp_set_object_terms( $product->get_id(), $options, $attribute_name );
                    }
                }
            }
        }

        wp_send_json('success');
    }

    function ajax_get_save_products_error() {
        $error_file = plugin_dir_path( __FILE__ ) . 'logs/save_products_exception.txt';

        printf( __( 'Error while saving products: %s', 'pw-bulk-edit' ), file_get_contents( $error_file ) );

        wp_die();
    }

    function ajax_get_image_html() {
        $image_id = absint( $_POST['image_id'] );
        $html = $this->get_image_html( $image_id );

        wp_send_json( array( 'html' => $html ) );
    }

    public function get_image_html( $image_id ) {
        $html = '';
        $size = array( PWBE_THUMBNAIL_SIZE, PWBE_THUMBNAIL_SIZE );

        if ( ! empty( $image_id ) ) {
            $html = wp_get_attachment_image( $image_id, $size, false, array( "class" => "pwbe-image" ) );
        }

        if ( ! $html  ) {
            $html = wc_placeholder_img( $size );
        }

        return $html;
    }

    public function save_products_exception() {
        $errfile = 'unknown file';
        $errstr  = 'shutdown';
        $errno   = E_CORE_ERROR;
        $errline = 0;

        $error = error_get_last();

        if ( $error !== NULL ) {
            $errno   = $error['type'];
            $errfile = $error['file'];
            $errline = $error['line'];
            $errstr  = $error['message'];

            if ( PW_Bulk_Edit::starts_with( plugin_dir_path( __FILE__ ), $errfile ) ) {
                $output_dir = plugin_dir_path( __FILE__ ) . 'logs';
                if ( ! file_exists( $output_dir ) ) {
                    mkdir( $output_dir, 0777, true );
                }
                file_put_contents( $output_dir . '/save_products_exception.txt', "$errstr in $errfile on line $errline" );
            }
        }
    }

    function ajax_activation() {
        $registration['active'] = $this->license->is_premium();
        $registration['error'] = $this->license->error;

        wp_send_json( $registration );
    }

    function error( $message ) {
        ?>
        <div class="error">
            <p><?php _e( $message, 'pw-bulk-edit' ); ?></p>
        </div>
        <?php
    }


    function register_admin_menu() {
        if ( empty ( $GLOBALS['admin_page_hooks']['pimwick'] ) ) {
            add_menu_page(
                __( 'PW Bulk Edit', 'pw-bulk-edit' ),
                __( 'Pimwick Plugins', 'pw-bulk-edit' ),
                get_option( 'pwbe_requires_capability', PWBE_REQUIRES_CAPABILITY ),
                'pimwick',
                array( $this, 'index' ),
                plugins_url( '/assets/images/pimwick-icon-120x120.png', __FILE__ ),
                6
            );

            add_submenu_page(
                'pimwick',
                __( 'PW Bulk Edit', 'pw-bulk-edit' ),
                __( 'Pimwick Plugins', 'pw-bulk-edit' ),
                get_option( 'pwbe_requires_capability', PWBE_REQUIRES_CAPABILITY ),
                'pimwick',
                array( $this, 'index' )
            );

            remove_submenu_page('pimwick','pimwick');
        }

        add_submenu_page(
            'pimwick',
            __( 'PW Bulk Edit', 'pw-bulk-edit' ),
            __( 'PW Bulk Edit', 'pw-bulk-edit' ),
            get_option( 'pwbe_requires_capability', PWBE_REQUIRES_CAPABILITY ),
            'pw-bulk-edit',
            array( $this, 'index' )
        );

        remove_submenu_page('pimwick','pimwick-plugins');
        add_submenu_page(
            'pimwick',
            __( 'Pimwick Plugins', 'pw-bulk-edit' ),
            __( 'Our Plugins', 'pw-bulk-edit' ),
            get_option( 'pwbe_requires_capability', PWBE_REQUIRES_CAPABILITY ),
            'pimwick-plugins',
            array( $this, 'other_plugins_page' )
        );

        add_submenu_page(
            'edit.php?post_type=product',
            __( 'PW Bulk Edit', 'pw-bulk-edit' ),
            __( 'PW Bulk Edit', 'pw-bulk-edit' ),
            get_option( 'pwbe_requires_capability', PWBE_REQUIRES_CAPABILITY ),
            'wc-pw-bulk-edit',
            array( $this, 'index' )
        );
    }

    function other_plugins_page() {
        global $pimwick_more_handled;

        if ( !$pimwick_more_handled ) {
            $pimwick_more_handled = true;
            require( 'ui/more.php' );
        }
    }

    function admin_scripts( $hook ) {
        if ( !empty( $hook ) && substr( $hook, -strlen( 'pw-bulk-edit' ) ) === 'pw-bulk-edit' ) {
            wp_enqueue_media();

            wp_register_style( 'pwbe-font-awesome', plugins_url( '/assets/css/font-awesome.min.css', __FILE__ ), array(), PWBE_VERSION ); // 4.6.3
            wp_enqueue_style( 'pwbe-font-awesome' );

            wp_register_style( 'pwbe-select2', plugins_url( '/assets/css/select2.min.css', __FILE__ ), array(), PWBE_VERSION ); // 4.0.3
            wp_enqueue_style( 'pwbe-select2' );

            wp_register_style( 'pwbe-context-menu', plugins_url( '/assets/css/jquery.contextMenu.min.css', __FILE__ ), array(), PWBE_VERSION );
            wp_enqueue_style( 'pwbe-context-menu' );

            wp_enqueue_script( 'pwbe-select2', plugins_url( '/assets/js/select2.min.js', __FILE__ ), array(), PWBE_VERSION ); // 4.0.3

            wp_register_style( 'pw-bulk-edit', plugins_url( '/assets/css/pro.style.css', __FILE__ ), array(), PWBE_VERSION );
            wp_enqueue_style( 'pw-bulk-edit' );

            if ( 'yes' == get_option( 'pwbe_include_print_css', 'yes' ) ) {
                wp_register_style( 'pw-bulk-edit-print', plugins_url( '/assets/css/print.css', __FILE__ ), array(), PWBE_VERSION );
                wp_enqueue_style( 'pw-bulk-edit-print' );
            }

            wp_enqueue_script( 'pwbe-context-menu', plugins_url( '/assets/js/jquery.contextMenu.min.js', __FILE__ ), array( 'jquery-ui-position' ), PWBE_VERSION ); // 2.2.3

            wp_enqueue_script( 'pwbe-filters', plugins_url( '/assets/js/pro.filters.js', __FILE__ ), array( 'jquery-form', 'pwbe-select2', 'pwbe-context-menu' ), PWBE_VERSION );

            $string_types = array(
                'contains'          => __( 'contains', 'pw-bulk-edit' ),
                'does not contain'  => __( 'does not contain', 'pw-bulk-edit' ),
                'is'                => __( 'is', 'pw-bulk-edit' ),
                'is not'            => __( 'is not', 'pw-bulk-edit' ),
                'begins with'       => __( 'begins with', 'pw-bulk-edit' ),
                'ends with'         => __( 'ends with', 'pw-bulk-edit' ),
                'is empty'          => __( 'is empty', 'pw-bulk-edit' ),
                'is not empty'      => __( 'is not empty', 'pw-bulk-edit' ),
            );

            $boolean_types = array(
                'is checked'        => __( 'is checked', 'pw-bulk-edit' ),
                'is not checked'    => __( 'is not checked', 'pw-bulk-edit' ),
            );

            $numeric_types = array(
                'is'                => __( 'is', 'pw-bulk-edit' ),
                'is not'            => __( 'is not', 'pw-bulk-edit' ),
                'is greater than'   => __( 'is greater than', 'pw-bulk-edit' ),
                'is less than'      => __( 'is less than', 'pw-bulk-edit' ),
                'is in the range'   => __( 'is in the range', 'pw-bulk-edit' ),
                'is empty'          => __( 'is empty', 'pw-bulk-edit' ),
                'is not empty'      => __( 'is not empty', 'pw-bulk-edit' ),
            );

            $multiselect_types = array(
                'is any of'         => __( 'is any of', 'pw-bulk-edit' ),
                'is none of'        => __( 'is none of', 'pw-bulk-edit' ),
                'is all of'         => __( 'is all of', 'pw-bulk-edit' ),
                'is empty'          => __( 'is empty', 'pw-bulk-edit' ),
                'is not empty'      => __( 'is not empty', 'pw-bulk-edit' ),
            );

            $select_types = array(
                'is any of'         => __( 'is any of', 'pw-bulk-edit' ),
                'is none of'        => __( 'is none of', 'pw-bulk-edit' ),
                'is empty'          => __( 'is empty', 'pw-bulk-edit' ),
                'is not empty'      => __( 'is not empty', 'pw-bulk-edit' ),
            );

            $catalog_visibility_types = array(
                'is any of'         => __( 'is any of', 'pw-bulk-edit' ),
                'is none of'        => __( 'is none of', 'pw-bulk-edit' ),
            );

            $image_types = array(
                'is set'            => __( 'is set', 'pw-bulk-edit' ),
                'is not set'        => __( 'is not set', 'pw-bulk-edit' ),
            );

            wp_localize_script( 'pwbe-filters', 'pwbeFilters', array(
                'stringTypes' => array_keys( $string_types ),
                'booleanTypes' => array_keys( $boolean_types ),
                'numericTypes' => array_keys( $numeric_types ),
                'multiSelectTypes' => array_keys( $multiselect_types ),
                'selectTypes' => array_keys( $select_types ),
                'catalogVisibilityTypes' => array_keys( $catalog_visibility_types ),
                'imageTypes' => array_keys( $image_types ),
                'i18n' => array(
                    'stringTypes' => array_values( $string_types ),
                    'booleanTypes' => array_values( $boolean_types ),
                    'numericTypes' => array_values( $numeric_types ),
                    'multiSelectTypes' => array_values( $multiselect_types ),
                    'selectTypes' => array_values( $select_types ),
                    'catalogVisibilityTypes' => array_values( $catalog_visibility_types ),
                    'imageTypes' => array_values( $image_types ),
                    'unsavedChangesPrompt' => __( 'Unsaved changes will be lost.', 'pw-bulk-edit' ),
                    'searching' => __( 'Searching', 'pw-bulk-edit' ),
                    'discardChanges' => __( 'Discard your changes?', 'pw-bulk-edit' ),
                ),
                'select2copypaste' => PWBE_PASTE_INTO_DROPDOWNS,
            ) );

            $save_batch_size = get_option( 'pwbe_save_batch_size', PWBE_SAVE_BATCH_SIZE );
            if ( empty( $save_batch_size ) ) {
                $save_batch_size = PWBE_SAVE_BATCH_SIZE;

                if ( empty( $save_batch_size ) ) {
                    $save_batch_size = 25;
                }
            }

            wp_enqueue_script( 'pwbe-results', plugins_url( '/assets/js/pro.results.js', __FILE__ ), array( 'pwbe-filters' ), PWBE_VERSION );
            wp_localize_script( 'pwbe-results', 'pwbe', array(
                'i18n' => array(
                    'select_image' => __( 'Select image', 'pw-bulk-edit' ),
                    'remove_image' => __( 'Remove image', 'pw-bulk-edit' ),
                    'view_name_prompt' => __( 'Name your custom view, for example "My View"', 'pw-bulk-edit' ),
                    'overwrite_view_prompt' => __( 'A view with this name already exists. Do you want to overwrite it?', 'pw-bulk-edit' ),
                    'editAllCheckedProducts' => __( 'Edit All Checked Products', 'pw-bulk-edit' ),
                    'sortAscending' => __( 'Sort Ascending', 'pw-bulk-edit' ),
                    'sortDescending' => __( 'Sort Descending', 'pw-bulk-edit' ),
                    'hideColumn' => __( 'Hide Column', 'pw-bulk-edit' ),
                    'acceptChanges' => __( 'Accept Changes', 'pw-bulk-edit' ),
                    'cancelChanges' => __( 'Cancel Changes', 'pw-bulk-edit' ),
                    'revertToOriginal' => __( 'Revert to the original value for this field', 'pw-bulk-edit' ),
                    'select' => __( 'Select', 'pw-bulk-edit' ),
                    'saving' => __( 'Saving', 'pw-bulk-edit' ),
                    'confirmDeleteView' => __( 'Are you sure you want to delete this view?', 'pw-bulk-edit' ),
                    'discardAllChanges' => __( 'Discard all unsaved changes? This can\'t be undone.', 'pw-bulk-edit' ),
                ),
                'saveBatchSize' => $save_batch_size,
                'select2copypaste' => PWBE_PASTE_INTO_DROPDOWNS,
                'select2filterBypass' => apply_filters( 'pwbe_select2_filter_bypass', array() ),
                'nonces' => array(
                    'save_products' => wp_create_nonce( 'pw-bulk-edit-save-products' ),
                ),
                'editingRowBorder' => get_option( 'pwbe_editing_row_border', 'yes' ),
            ) );

            wp_enqueue_script( 'jquery-form' );
            wp_enqueue_script( 'jquery-ui-draggable' );
            wp_enqueue_script( 'jquery-ui-position' );
        }

        wp_register_style( 'pw-bulk-edit-icon', plugins_url( '/assets/css/icon-style.css', __FILE__ ), array(), PWBE_VERSION );
        wp_enqueue_style( 'pw-bulk-edit-icon' );
    }

    public static function starts_with( $needle, $haystack ) {
        $length = strlen( $needle );
        return ( substr( $haystack, 0, $length ) === $needle );
    }

    /**
     * Source: http://wordpress.stackexchange.com/questions/14652/how-to-show-a-hierarchical-terms-list
     * Recursively sort an array of taxonomy terms hierarchically. Child categories will be
     * placed under a 'children' member of their parent term.
     * @param Array   $cats     taxonomy term objects to sort
     * @param Array   $into     result array to put them in
     * @param integer $parentId the current parent ID to put them in
     */
    function sort_terms_hierarchically( array &$cats, array &$into, $parentId = 0 ) {
        foreach ( $cats as $i => $cat ) {
            if ( $cat->parent == $parentId ) {
                $into[$cat->term_id] = $cat;
                unset( $cats[$i] );
            }
        }

        foreach ( $into as $topCat ) {
            $topCat->children = array();
            $this->sort_terms_hierarchically( $cats, $topCat->children, $topCat->term_id );
        }
    }

    function hierarchical_select($categories, $level = 0, $parent = NULL, $prefix = '') {
        $output = '';

        foreach ( $categories as $category ) {
            $output .= "<option value='{$category->slug}'>$prefix {$category->name}</option>\n";

            if ( $category->parent == $parent ) {
                $level = 0;
            }

            if ( count( $category->children ) > 0 ) {
                $output .= $this->hierarchical_select( $category->children, ( $level + 1 ), $category->parent, "$prefix {$category->name} &#8594;" );
            }
        }

        return $output;
    }
}

global $pw_bulk_edit;
$pw_bulk_edit = new PW_Bulk_Edit();
