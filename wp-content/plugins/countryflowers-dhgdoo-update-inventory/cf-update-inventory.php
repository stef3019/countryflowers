<?php
/*
Plugin Name: WooCommerce Product Deletion
Plugin URI: https://www.stefcordina.com/
Description: Deletes WooCommerce products and their associated images from the Media Library.
Version: 1.0
Author: Stef Cordina (AI)
Author URI: https://www.stefcordina.com/
License: GPL2
*/

if (!defined('ABSPATH')) {
    exit;
}

// Add a menu page for "Dhgdoo Products" and a submenu page for "Settings"
function wc_product_deletion_menu() {
    add_menu_page(
        'Dhgdoo Products',
        'Dhgdoo Products',
        'manage_options',
        'wc-product-deletion-menu',
        'wc_product_deletion_menu_page',
        'dashicons-cart',
        25
    );

    add_submenu_page(
        'wc-product-deletion-menu',
        'Settings',
        'Settings',
        'manage_options',
        'wc-product-deletion-settings',
        'wc_product_deletion_settings_page'
    );

    add_submenu_page(
        'wc-dhg-image-deletion-menu',
        'Delete Old Pics',
        'Delete Old Pics',
        'manage_options',
        'wc-product-image-deletion',
        'wc_product_image_deletion_page'
    );
}
add_action('admin_menu', 'wc_product_deletion_menu');

// Callback function for the menu page
function wc_product_deletion_menu_page() {
    ?>
    <div class="wrap">
        <h1>Dhgdoo Products</h1>
        <p>Welcome to the Dhgdoo Products page!</p>
        <a class="button-primary" href="<?php echo esc_url(admin_url('admin.php?page=wc-product-deletion-settings')); ?>">Go to Settings</a>
    </div>
    <?php
}

// Callback function for the settings page
function wc_product_deletion_settings_page() {
    $categories_unordered = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
    ]);

    // Order the categories by parent-child relationship
    $categories = wc_product_deletion_order_categories($categories_unordered);


    if (isset($_POST['wc_product_deletion_category'])) {
        $category_slug = sanitize_text_field($_POST['wc_product_deletion_category']);

        if (!empty($category_slug)) {
            $category = get_term_by('slug', $category_slug, 'product_cat');

            if ($category && !is_wp_error($category)) {
                $args = array(
                    'post_type'      => 'product',
                    'posts_per_page' => -1,
                    'tax_query'      => array(
                        array(
                            'taxonomy' => 'product_cat',
                            'field'    => 'slug',
                            'terms'    => $category_slug,
                        ),
                    ),
                );

                $products = get_posts($args);

                foreach ($products as $product) {
                    $product_id = $product->ID;

                    // Get the product gallery images
                    //$attachment_ids = $product->get_gallery_image_ids();

                    // Delete the product
                    wp_delete_post($product_id, true);

                    // Delete the product gallery images
                    foreach ($attachment_ids as $attachment_id) {
                        wp_delete_attachment($attachment_id, true);
                    }
                }
                

                // Display a success message
                add_action('admin_notices', 'wc_product_deletion_success');
            } else {
                // Display an error message if the category doesn't exist
                add_action('admin_notices', 'wc_product_deletion_category_error');
            }
        }
    }
    ?>
    <div class="wrap">
        <h1>Product Deletion Settings</h1>
        <form id="dhgdoo-settings-form" method="post" action="<?php echo esc_url(admin_url('admin.php?page=wc-product-deletion-settings')); ?>">
            <label for="wc_product_deletion_category">Select Category:</label>
            <select id="wc_product_deletion_category" name="wc_product_deletion_category">
                <option>Select Category</option>
                <?php
                    foreach ($categories as $category) {
                        
                        echo wc_product_deletion_get_category_option($category);
                    }
                ?>
            </select>
            <input type="submit" class="button-primary" value="Delete Products">
            <div id="dhgdoo-loader" style="display: none;">
                <img src="<?php echo esc_url(plugins_url('/loader.gif', __FILE__)); ?>" alt="Loader">
            </div>
        </form>
    </div>
    <?php
}

// Recursive function to order categories by parent-child relationship
function wc_product_deletion_order_categories($categories, $parent_id = 0, $depth = 0) {
    $ordered_categories = array();

    foreach ($categories as $category) {
        if ($category->parent == $parent_id) {
            $category->name = str_repeat('&nbsp;&nbsp;', $depth) . $category->name . ' (' . wc_product_deletion_get_product_count($category->term_id) . ')';
            $ordered_categories[] = $category;
            $ordered_categories = array_merge($ordered_categories, wc_product_deletion_order_categories($categories, $category->term_id, $depth + 1));
        }
    }

    return $ordered_categories;
}

// Helper function to generate category options
function wc_product_deletion_get_category_option($category) {
    $option = '<option value="' . esc_attr($category->slug) . '"';
    $option .= selected(get_option('wc_product_deletion_category'), $category->slug, false);
    $option .= '>' . esc_html($category->name) . '</option>';

    return $option;
}

function wc_product_deletion_get_product_count($category_id) {
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $category_id,
            ),
        ),
    );

    $products = get_posts($args);

    return count($products);
}

// Register and sanitize the settings
function wc_product_deletion_register_settings() {
    register_setting(
        'wc_product_deletion_options',
        'wc_product_deletion_category',
        'sanitize_text_field'
    );
}
add_action('admin_init', 'wc_product_deletion_register_settings');

// Delete products and associated images when a specific category is selected
function wc_product_deletion_delete_products() {
    $category_slug = get_option('wc_product_deletion_category');

    if (empty($category_slug)) {
        return;
    }

    $category = get_term_by('slug', $category_slug, 'product_cat');

    if (!$category || is_wp_error($category)) {
        // Display an error message if the category doesn't exist
        add_action('admin_notices', 'wc_product_deletion_category_error');
        return;
    }

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $category_slug,
            ),
        ),
    );

    $products = get_posts($args);

    foreach ($products as $product) {
        $product_id = $product->ID;

        // Move the product to trash
        wp_trash_post($product_id);

        // Get the product gallery images
        $attachment_ids = $product->get_gallery_image_ids();

        // Delete the product gallery images
        foreach ($attachment_ids as $attachment_id) {
            wp_delete_attachment($attachment_id, true);
        }
    }

    // Display a success message
    add_action('admin_notices', 'wc_product_deletion_success');
}
add_action('wp_loaded', 'wc_product_deletion_delete_products');

// Display an error message if the category doesn't exist
function wc_product_deletion_category_error() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p>The specified category does not exist.</p>
    </div>
    <?php
}

// Display a success message
function wc_product_deletion_success() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p>Products and associated images have been deleted successfully.</p>
    </div>
    <?php
}

// Enqueue JavaScript file and add loader animation
function wc_product_deletion_enqueue_scripts($hook) {
    if ($hook == 'dhgdoo-products_page_wc-product-deletion-settings') {
        wp_enqueue_script('dhgdoo-import-scripts', plugins_url('/js/scripts.js', __FILE__), array('jquery'), '1.0', true);
        wp_enqueue_style('dhgdoo-styles', plugins_url('/css/style.css', __FILE__), array(), '1.0');
    }
}
add_action('admin_enqueue_scripts', 'wc_product_deletion_enqueue_scripts');



function delete_numeric_filename_images() {
    // Get all media attachments
    $args = array(
        'post_type' => 'attachment',
        'posts_per_page' => -1,
        'post_status' => 'inherit',
        'post_mime_type' => 'image',
    );
    $attachments = get_posts($args);

    // Loop through the attachments
    foreach ($attachments as $key => $attachment) {
       
        $attachment_id = $attachment->ID;
        $file = get_attached_file($attachment_id);
        $filename = wp_basename($file);

        // Check if the filename is fully numeric
        if (is_numeric($filename)) {
            if ($key > 2 ) {
                break;
            }
            // Delete the attachment and its metadata
            wp_delete_attachment($attachment_id, true);
        }
    }
}