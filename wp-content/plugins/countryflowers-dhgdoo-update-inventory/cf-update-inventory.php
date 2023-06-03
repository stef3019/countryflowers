<?php
/*
Plugin Name: DHGDOO Product Importer
Plugin URI: https://www.stefcordina.com/
Description: Contains necessary functionality to prepare the site for an inventory update from DHGDOO.EU
Version: 1.0
Author: Stef Cordina (AI)
Author URI: https://www.stefcordina.com/
License: GPL2
*/

if (!defined('ABSPATH')) {
    exit;
}



// Register the plugin activation hook
register_activation_hook( __FILE__, 'wp_dhg_product_dump_activation' );

// Plugin activation callback function
function wp_dhg_product_dump_activation() {
    create_wp_dhg_product_dump_table();
    echo '<pre>';
    var_dump('HISSSSSSS');
    echo '</pre>';
    // You can perform any additional activation tasks here
}

// Function to create the wp_dhg_product_dump table
function create_wp_dhg_product_dump_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'dhg_product_dump';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT(11) NOT NULL AUTO_INCREMENT,
        code MEDIUMINT(9) NOT NULL,
        variant MEDIUMINT(9) NOT NULL,
        type VARCHAR(8) NOT NULL,
        image VARCHAR(255) NOT NULL,
        name VARCHAR(120) NOT NULL,
        color VARCHAR(30) NOT NULL,
        price VARCHAR(10) NOT NULL,
        category SMALLINT(5) NOT NULL,
        subcategory SMALLINT(5) NOT NULL,
        imported TINYINT(1) NOT NULL,
        datecreated DATETIME NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $delta = dbDelta( $sql );
}


require 'cf-helpers.php';


// Add a menu page for "Dhgdoo Products" and a submenu page for "Settings"
function wc_product_deletion_menu() {
    add_menu_page(
        'DHGDOO',
        'DHGDOO',
        'manage_options',
        'wp-product-dhg-management',
        'wc_product_deletion_menu_page',
        'dashicons-cart',
        25
    );

    add_submenu_page(
        'wp-product-dhg-management',
        'Delete Products',
        'Delete Products',
        'manage_options',
        'wc-product-dhg-management-settings',
        'wc_product_deletion_settings_page'
    );

    add_submenu_page(
        'wp-product-dhg-management',
        'Clean Pics',
        'Clean Pics',
        'manage_options',
        'wc-product-dhg-management-clean-pics',
        'wc_product_deletion_clean_pics_page'
    );

    add_submenu_page(
        'wp-product-dhg-management',
        'Add New DHG Products',
        'Add New DHG Products',
        'manage_options',
        'wc-product-dhg-management-new-products',
        'wc_product_add_new_dhg_products_page'
    );

    add_submenu_page(
        'wp-product-dhg-management',
        'Product Info',
        'Product Info',
        'manage_options',
        'wc-product-dhg-management-info',
        'wc_product_dhg_info'
    );

    add_submenu_page(
        'wp-product-dhg-management',
        'Category Mapping',
        'Category Mapping',
        'manage_options',
        'wc-product-dhg-management-cat-mappings',
        'wc_product_dhg_mappings'
    );
}
add_action('admin_menu', 'wc_product_deletion_menu');



// Callback function for the menu page
function wc_product_deletion_menu_page() {
    ?>
    <div class="wrap">
        <h1>Dhgdoo Products</h1>
        <h3>Delete</h3>
        <p>Delete products by category. Warning, this is permanent. Products will not be in the Bin.</p>
        <a class="button-primary" href="<?php echo esc_url(admin_url('admin.php?page=wc-product-dhg-management-settings')); ?>">Delete Products</a>
        
        <h3 style="margin-top:30px">Clean up Media Library</h3>
        <p>Clean up Media Library from any unattached and residual images.</p>
        <a class="button-primary" href="<?php echo esc_url(admin_url('admin.php?page=wc-product-dhg-management-clean-pics')); ?>">Clean</a>
        <h3 style="margin-top:30px">New Stock</h3>
        <p>Add new stock from DHG json page as published or draft. Ideally delete first but not required.</p>
        <a class="button-primary" href="<?php echo esc_url(admin_url('admin.php?page=wc-product-dhg-management-new-products')); ?>">Add</a>
        <h3 style="margin-top:30px">Today</h3>
        <p>View info from today's dhg json file. </p><p><em>WIP (json file needs to be saved first)</em></p>
        <a class="button-primary" href="<?php echo esc_url(admin_url('admin.php?page=wc-product-dhg-management-info')); ?>">Info</a>
        <h3 style="margin-top:30px">Category Mappings</h3>
        <p>DHG category IDs mapped onto WooCommerce category IDs. </p><p><em>WIP (form doesnt work, needs to be edited from phpmyadmin)</em></p>
        <a class="button-primary" href="<?php echo esc_url(admin_url('admin.php?page=wc-product-dhg-management-cat-mappings')); ?>">Map</a>
        <h3 style="margin-top:30px">Further updates required</h3>
        <p>- Incorporate christmas image importer</p>
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
            // echo '<pre>';
            // var_dump($category);
            // echo '</pre>';
    
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

                    // Delete the product
                    wp_delete_post($product_id, true);

                }
                
                echo 'success';
                // Display a success message
                add_action('admin_notices', 'wc_product_deletion_success');
            } else {
                echo 'not ok';
                // Display an error message if the category doesn't exist
                add_action('admin_notices', 'wc_product_deletion_category_error');
            }
        }
    }
    ?>
    <div class="wrap">
        <h1>Product Deletion Settings</h1>
        <h3> Select a category to permanently delete all associated products.</h3>
        <form id="dhgdoo-settings-form" method="post" action="<?php echo esc_url(admin_url('admin.php?page=wc-product-dhg-management-settings')); ?>">
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
            <div id="dhgdoo-loader"></div>
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
    if ($hook == 'dhgdoo-products_page_wc-product-dhg-management-settings') {
        wp_enqueue_script('dhgdoo-import-scripts', plugins_url('/js/scripts.js', __FILE__), array('jquery'), '1.0', true);
        wp_enqueue_style('dhgdoo-styles', plugins_url('/css/style.css', __FILE__), array(), '1.0');
    }
}
add_action('admin_enqueue_scripts', 'wc_product_deletion_enqueue_scripts');

// Callback function for the "Clean Pics" submenu page
function wc_product_deletion_clean_pics_page() {
    if (isset($_POST['clean_pics']) && ($_POST['clean_pics'] == 'clean')) {
       delete_numeric_filename_images();
    }
    ?>
    <div class="wrap">
        <h1>Clean Pics</h1>
        <h3 id="clean-pics-description">This function goes through the media libary and removes all images that have a filename that passes the following checks:</h3>
        <ol>
            <li>The filename is all numeric, apart from the file extension</li>
            <li>There is no '-rotated' or '-scaled' in the filename</li>
            <li>There is no - between two numbers, eg. 12345-1.jpg (tad risky), but worth the risk</li>
        </ol>
        <p>Use to clear all images before re-importing everything.</p>
        <p>Fresh Flowers images are unaffected</p>
        <form id="dhgdoo-settings-form" method="post" action="<?php echo esc_url(admin_url('admin.php?page=wc-product-dhg-management-clean-pics')); ?>">
            <input type="hidden" name="clean_pics" value="clean" />
            <input type="submit" class="button-primary" value="Clean Media Library">
            <div id="dhgdoo-loader"></div>
        </form>
    </div>
    <?php
}

function delete_numeric_filename_images() {
    $files = array();
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
        $to_delete = process_string($filename);
        
        // Check if the filename is fully numeric
        if ($to_delete === true) {
            // echo '<br>';
            // var_dump($filename);
            // echo '</br>';
            if ($filename == 'scott-webb-OPvJMelToY4-unsplash-scaled.jpg') {
                continue;
            }
            // Delete the attachment and its metadata
           wp_delete_attachment($attachment_id, true);
        }
    }
    return $files;
}


function process_string($input_string) {
    // Extract the substring until the first dot
    $substring = strstr($input_string, '.', true);
   
    $is_numeric = is_numeric($substring);
    $has_rotated_substring = strpos($substring, '-rotated') !== false;
    $has_scaled_substring = strpos($substring, '-scaled') !== false;
    $has_dash_between_numeric = filter_var(preg_match('/[0-9]-[0-9]/', $substring), FILTER_VALIDATE_BOOLEAN);

    $img_name_meta = array(
        'is_numeric' => $is_numeric,
        'has_rotated_substring' => $has_rotated_substring,
        'has_scaled_substring' => $has_scaled_substring,
        'has_dash_between_numeric' => $has_dash_between_numeric,
    );

    //if any of the above conditions are true, then the image can be deleted
    //  echo '<br>';
    // var_dump($img_name_meta);
    // echo '</br>';

   if (in_array(true, $img_name_meta, true)) {
        return true;
    } else {
        return false;
    }
}


// Callback function for the "Add New Products" submenu page
function wc_product_add_new_dhg_products_page() {

    //Placing all product creation functions in separate file
    require 'cf-add-new-products.php';

    cf_insert_dhg_products();
    $categories_unordered = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
    ]);

    // Order the categories by parent-child relationship
    $categories = wc_product_deletion_order_categories($categories_unordered);
    ?>
    <div class="wrap">
        <h1>Add New Products to A Category</h1>
        <h3> Select a category to add new products to.</h3>
        <form id="dhgdoo-settings-form" method="post" action="<?php echo esc_url(admin_url('admin.php?page=wc-product-dhg-management-new-products')); ?>">
            <label for="wc_product_add_category_products">Select Category:</label>
            <select id="wc_product_add_category_products" name="wc_product_add_category_products">
                <option>Select WooCommerce Category</option>
                <?php
                    foreach ($categories as $category) {
                        
                        echo wc_product_deletion_get_category_option($category);
                    }
                ?>
            </select>
            <label for="wc_cat_product_status">Select Status:</label>
            <select id="wc_cat_product_status" name="wc_cat_product_status">
                <option>Select Status for Products</option>
                <option value="draft">Draft</option>
                <option value="publish">Published</option>
            </select>

            <input type="submit" class="button-primary" value="Add Products">
            <div id="dhgdoo-loader"></div>
        </form>
    </div>
    <?php
}



function wc_product_dhg_info() { 
    ?>
    <div class="wrap">
        <h1>DHG Info</h1>
        <h3>Product info for today's inventory.</h3>
    </div>
     <?php
        require 'cf-dhg-products-info.php';
        cf_json_save_products_file();

}


function wc_product_dhg_mappings () {
    global $wpdb;
    $sql = "SELECT * FROM wp_category_lookup";
    $results=$wpdb->get_results($sql);
    cf_grab_categories_from_json();
    ?>
    <div class="wrap">
        <h1>Category Mappings</h1>
        <h3> DHG and WooCommerce product category mappings.</h3>
        <?php
        $table_html = '<table id="category_lookup" class="widefat fixed wp-list-table striped posts" cellspacing="0" style="max-width:750px;">
        <thead><th>Label</th><th>Status</th><th>WC ID</th><th>DHG ID</th><th>Parent</th><th>Delete</th></thead><tbody>';

        foreach ($results as $lookup) {
            $table_html .= '<tr>';
            $table_html .= '<td>'.$lookup->english_name.'</td>';
            $table_html .= '<td>'.$lookup->currently_active.'</td>';
            $table_html .= '<td>'.$lookup->wc_id.'</td>';
            $table_html .= '<td>'.$lookup->dhg_id.'</td>';
            $table_html .= '<td>'.$lookup->dhg_parent_id.'</td>';
            $table_html .= '<td><a href="#">Delete</a></td>';
            $table_html .= '</tr>';
        }
        $table_html .= '</tbody></table>';
        echo $table_html;
        ?>
    </div>
    <?php
}