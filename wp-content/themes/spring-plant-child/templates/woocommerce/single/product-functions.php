<?php
/**
 * Template display single product wishlist and compare
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */

if ((in_array('yith-woocommerce-compare/init.php', apply_filters('active_plugins', get_option('active_plugins')))
        || in_array('yith-woocommerce-compare-premium/init.php', apply_filters('active_plugins', get_option('active_plugins'))))
    || (in_array('yith-woocommerce-wishlist/init.php', apply_filters('active_plugins', get_option('active_plugins'))) && (get_option( 'yith_wcwl_enabled' ) == 'yes'))) {
    echo '<div class="single-product-function">Please allow 10 - 15 working days for delivery';
    if (in_array('yith-woocommerce-wishlist/init.php', apply_filters('active_plugins', get_option('active_plugins'))) && (get_option( 'yith_wcwl_enabled' ) == 'yes')) {
        echo do_shortcode('[yith_wcwl_add_to_wishlist]');
    }

    if ((in_array('yith-woocommerce-compare/init.php', apply_filters('active_plugins', get_option('active_plugins')))
            || in_array('yith-woocommerce-compare-premium/init.php', apply_filters('active_plugins', get_option('active_plugins'))))
        && get_option('yith_woocompare_compare_button_in_products_list') == 'yes') {
        if (!shortcode_exists('yith_compare_button') && class_exists('YITH_Woocompare') && function_exists('yith_woocompare_constructor')) {
            $context = isset($_REQUEST['context']) ? $_REQUEST['context'] : null;
            $_REQUEST['context'] = 'frontend';
            yith_woocompare_constructor();
            $_REQUEST['context'] = $context;
        }

        if (shortcode_exists('yith_compare_button')) {
            echo do_shortcode('[yith_compare_button container="false" type="link"]');
        }
    }
    echo '</div>';
}