<?php
/*
Plugin Name: DHGDOO Image Importer for Christmas Items
Description: Imports images from a JSON file and saves them to the WordPress media library.
Version: 1.0
Author: Stef Cordina (AI generated)
*/

// Create database table upon plugin activation
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
    add_submenu_page('tools.php', 'JSON Image Importer', 'JSON Image Importer', 'manage_options', 'json-image-importer', 'json_image_importer_settings_page');
}

// Render plugin settings page
function json_image_importer_settings_page() {
    ?>
    <div class="wrap">
        <h1>JSON Image Importer</h1>
        <p>Enter the URL of a JSON file to import images.</p>
        <form method="post">
            <label for="json_url">JSON File URL:</label>
            <input type="text" id="json_url" name="json_url" placeholder="Enter JSON file URL" required>
            <input type="submit" class="button button-primary" value="Import Images">
            <div id="json-image-importer-loader"></div>
            <?php wp_nonce_field('json_image_importer', 'json_image_importer_nonce'); ?>
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
function json_image_importer_import_images($json_url) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'json_image_importer';

    $response = wp_remote_get($json_url);

    if (is_wp_error($response)) {
        echo '<div class="error notice"><p>Error retrieving JSON file.</p></div>';
        return;
    }

    $json_data = wp_remote_retrieve_body($response);
    $data = json_decode($json_data, true);
   

    if (empty($data)) {
        echo '<div class="error notice"><p>Invalid JSON file.</p></div>';
        return;
    }
    $data = $data['products'];
    $imported_count = 0;

    foreach ($data as $item) {
       

        if (isset($item['categories'][0]['id']) && $item['categories'][0]['id'] == 1) {
            $variant = isset($item['variant']) ? $item['variant'] : '';
            $image_url = isset($item['Image']) ? $item['Image'] : '';
            // echo '<pre style="margin-left: 230px;">';
            // print_r($item);
            // echo '</pre>';
            // break;
            echo $variant.': '.$image_url.'</br>';

            if (!empty($variant) && !empty($image_url) && !strpos($image_url, 'no_image.png')) {
                // Save image to media library
                $upload_dir = wp_upload_dir();
                $image_data = file_get_contents($image_url);
                $image_name = basename($image_url);

                $file_path = $upload_dir['path'] . '/' . $image_name;
                file_put_contents($file_path, $image_data);

                $attachment = array(
                    'post_title' => $image_name,
                    'post_mime_type' => wp_check_filetype($image_name)['type'],
                    'post_content' => '',
                    'post_status' => 'inherit'
                );

                $attachment_id = wp_insert_attachment($attachment, $file_path);

                if (!is_wp_error($attachment_id)) {
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
}

##NOTES

/*
Create a new directory in your WordPress installation's wp-content/plugins folder and name it csv-image-importer.
Create a new PHP file inside the csv-image-importer folder and name it csv-image-importer.php.
Copy and paste the above code into the csv-image-importer.php file.
Save the file.
Create a new folder inside the csv-image-importer folder and name it js.
Create a new JavaScript file inside the js folder and name it csv-image-importer.js.
Open the csv-image-importer.js file and add the following code:(added already)
Save the csv-image-importer.js file.
Activate the "CSV Image Importer" plugin in the WordPress admin area.
Go to "Tools" -> "CSV Image Importer" to access the plugin's settings page.
Select a CSV file containing image URLs and click the "Import" button.
The plugin will read the CSV file, download the images, and save them to the WordPress media library.
Note: Make sure the CSV file contains only one column with image URLs, and the URLs should be in the first column (column index 0). The plugin assumes that the first column of the CSV file contains the image URLs.


*/

//In this updated version, the CSV file should have two columns: the image URL and the title. The image URL should be in the first column (column index 0), and the title should be in the second column (column index 1). The plugin will set the title as both the alt text and image title when importing the images.

//With this updated version, a new database table named wp_csv_image_importer will be created when the plugin is activated. The table will have three columns: ID (primary key), product_id, and image_name. The CSV file should now include three columns: ID, product_id, and image_url. The plugin will extract the product_id and image_url from each row, download and save the image, and insert the product_id and image filename into the wp_csv_image_importer table.

 // a new button has been added to the settings page labeled "Process JSON and Import Images." When clicked, it triggers the csv_image_importer_process_json() function, which checks if the CSV file exists and then calls the csv_image_importer_import_images_from_csv() function to import the images from the CSV file into the WordPress media library.

