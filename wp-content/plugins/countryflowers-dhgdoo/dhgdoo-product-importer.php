<?php
/*
Plugin Name: DHGDOO Image Importer for Christmas Items
Description: Imports DHG Christmas images from a JSON file and saves them to the WordPress media library with a lookup table matching file path and variant code.
Version: 1.0
Author: Stef Cordina (AI generated)
*/

register_activation_hook(__FILE__, 'json_image_importer_create_table');
function json_image_importer_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'json_image_importer';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        variant varchar(255) NOT NULL,
        image varchar(255) NOT NULL,
        attachment_id bigint(20) NOT NULL,
        date_created datetime NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Enqueue necessary scripts and styles
add_action('admin_enqueue_scripts', 'json_image_importer_enqueue_scripts');
function json_image_importer_enqueue_scripts() {
    wp_enqueue_media();
    wp_enqueue_script('json-image-importer', plugin_dir_url(__FILE__) . 'js/json-image-importer.js', array('jquery'), '1.0', true);
    wp_enqueue_style( 'json-importer-styles',plugin_dir_url(__FILE__) . 'css/style.css');
}

// Add plugin settings page
add_action('admin_menu', 'json_image_importer_add_settings_page');
function json_image_importer_add_settings_page() {
    //add_submenu_page('tools.php', 'JSON Image Importer', 'JSON Image Importer', 'manage_options', 'json-image-importer', 'json_image_importer_settings_page');
    add_submenu_page(
        'woocommerce',                    // Parent menu slug
        'DHG Image Importer',                  // Page title
        'DHG Image Importer',                  // Menu title
        'manage_options',                  // Capability required to access the page
        'json-image-importer',               // Menu slug
        'json_image_importer_settings_page'           // Callback function to display the page content
    );
}

// Render plugin settings page
function json_image_importer_settings_page() {
    ?>
    <div class="wrap">
        <h1>JSON Image Importer</h1>
        <p>Enter the URL of a JSON file to import images.</p>
        <form method="post" class="dhg_import_fn">
            <label for="json_url">JSON File URL:</label>
            <input type="text" id="json_url" name="json_url" placeholder="Enter JSON file URL" required>
            <input type="submit" id="json-image-importer-button" class="button button-primary" value="Import Christmas Images" onclick="showLoader()">
            <div id="json-image-importer-loader"></div>
            <?php wp_nonce_field('json_image_importer', 'json_image_importer_nonce'); ?>
        </form>
</br></br>
        <form method="post" class="dhg_import_fn" id="save-json-file">
            <label for="json_url">JSON File URL:</label>
            <input type="text" id="json_url_2" name="json_url" placeholder="Enter JSON file URL" required>
            <input type="submit" id="save_json_button" class="button" value="Save JSON File" onclick="saveJsonFile()">
            <?php wp_nonce_field('json_save_file', 'json_save_file_nonce'); ?>
        </form>
    </div>
    <?php
}

// Process form submission to import images
add_action('admin_init', 'json_image_importer_process_form');
function json_image_importer_process_form() {
    if (isset($_POST['json_image_importer_nonce']) && wp_verify_nonce($_POST['json_image_importer_nonce'], 'json_image_importer')) {
        if (isset($_POST['json_url'])) {
            $json_url = sanitize_text_field($_POST['json_url']);
            json_image_importer_import_images($json_url);
        }
    }
}

// Import images from JSON
add_action('wp_ajax_json_image_importer_process_json', 'json_image_importer_process_json');
function json_image_importer_process_json() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'json_image_importer';
  
    $json_url = $_POST['json_url'];
    
    $response = wp_remote_get($json_url);

    if (is_wp_error($response)) {
        echo '<div class="error notice"><p>There was an error retrieving the JSON file.</p></div>';
        return;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);


    if (empty($data)) {
        echo '<div class="error notice"><p>Invalid JSON data.</p></div>';
        return;
    }
    $data = $data['products'];
    $imported_count = 0;

    foreach ($data as $item) {
        if (isset($item['categories'][0]['id']) && $item['categories'][0]['id'] == 1) {
    //         # Uncomment for testing
            // if ($imported_count > 1 ) {
            //     break;
            // }
          
            $variant = isset($item['variant']) ? $item['variant'] : '';
            $image_url = isset($item['Image']) ? $item['Image'] : '';
    // var_dump($variant);
    // var_dump($image_url);
          
            if (!empty($variant) && !empty($image_url) && !strpos($image_url, 'no_image.png')) {
                // Save image to media library
                $upload_dir = wp_upload_dir();
                $upload_path = $upload_dir['path'];
                $image_data = file_get_contents($image_url);
                $image_name = basename($image_url);

                // Generate filename
                $file_name = $image_name;
                $file_path = $upload_dir['path'] . '/' . $file_name;
               file_put_contents($file_path, $image_data);

              // echo $file_name;

                $attachment = array(
                    'post_title' => $image_name,
                    'post_mime_type' => wp_check_filetype($file_name)['type'],
                    'post_content' => '',
                    'post_status' => 'inherit'
                );

                $attachment_id = wp_insert_attachment($attachment, $file_path);
                

                if (!is_wp_error($attachment_id)) {

                    // Generate thumbnails for the attachment
                    $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_path . '/' . $image_name);
                    wp_update_attachment_metadata($attachment_id, $attachment_data);


                    // Insert data into database table
                    $wpdb->insert(
                        $table_name,
                        array(
                            'variant' => $variant,
                            'image' => $image_url,
                            'attachment_id' => $attachment_id,
                            'date_created' => current_time('mysql')
                        ),
                        array(
                            '%s',
                            '%s',
                            '%d',
                            '%s'
                        )
                    );

                   $imported_count++;
                } 
           }
       }
   }

    if ($imported_count > 0) {
        echo '<div class="updated notice"><p>' . $imported_count . ' images imported successfully.</p></div>';
    } else {
        echo '<div class="notice"><p>No images matching the criteria were found or imported.</p></div>';
    }
    
    return;

}

// Helper function to look for existing file
function is_file_in_directory($filename, $directory) {
    $path = trailingslashit($directory) . $filename;
    return file_exists($path);
}

// Save JSON file to uploads directory
add_action('wp_ajax_save_json_file', 'json_image_importer_save_json_file');
function json_image_importer_save_json_file() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permission denied.');
    }

    check_ajax_referer('json_save_file', 'nonce');

    $upload_dir = wp_upload_dir();
    $json_folder = $upload_dir['basedir'] . '/json-files';

    if (!is_dir($json_folder)) {
        mkdir($json_folder);
    }

    $file_name = date('Ymd') . '_dhg_products.json';
    $file_path = $json_folder . '/' . $file_name;

    if (is_file_in_directory($file_name, $file_path)) {
        wp_send_json_error('File already exists.');

    } else {

        $json_data = file_get_contents($_POST['json_url']);

        if ($json_data === false) {
            wp_send_json_error('Failed to retrieve JSON data.');
        }

        $result = file_put_contents($file_path, $json_data);

        if ($result === false) {
            wp_send_json_error('Failed to save JSON file.');
        }

        wp_send_json_success('JSON file saved successfully.');
   }
}

// Enqueue JavaScript file for saving JSON file
add_action('admin_enqueue_scripts', 'json_save_file_enqueue_script');
function json_save_file_enqueue_script($hook) {
    if ($hook === 'tools_page_json-image-importer') {
        wp_enqueue_script('json-image-importer-save-json', plugin_dir_url(__FILE__) . 'js/save-json.js', array('jquery'), '1.0', true);
        wp_localize_script('json-image-importer-save-json', 'jsonImageImporter', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('json_save_file')
        ));
    }
}