<?php

/*
cf_get_today_json_filepath
cf_json_save_products_file
cf_grab_products_json
cf_grab_categories_from_json
cf_upload_image_from_url
cf_attach_image_to_product
cf_delete_prev_cat_in_dump_table
get_parent_category_id
add_wc_stuff_to_product 
get_id_of_parent_wc_cat 
cf_get_product_object_type
cf_get_dhg_cat_from_wc
cf_get_wc_cat_from_dhg
*/

function cf_get_today_json_filepath() {
    $upload_dir = wp_upload_dir();
    $json_folder = $upload_dir['basedir'] . '/json-files';
    $file_name = date('Ymd') . '_dhg_products.json';
    $file_path = $json_folder . '/' . $file_name;
    return $file_path;
}

function cf_json_save_products_file() {

    if (!current_user_can('manage_options')) {
        echo 'Permission denied.';
    }

    $upload_dir = wp_upload_dir();
    $json_folder = $upload_dir['basedir'] . '/json-files';

    if (!is_dir($json_folder)) {
        mkdir($json_folder);
    }

    $file_name = date('Ymd') . '_dhg_products.json';
    $file_path = $json_folder . '/' . $file_name;

    $file_exists = file_exists($file_path);

    if (!$file_exists) {

        $json_data = file_get_contents('https://dhgdoo.eu/writable/export/products.json');

        if ($json_data === false) {
            echo 'Failed to retrieve JSON data.';
        }

        $result = file_put_contents($file_path, $json_data);

        if ($result === false) {
            echo 'Failed to save JSON file.';
        }

        echo 'JSON file saved successfully.';
    }
}

function cf_grab_products_json () {
    if (!current_user_can('manage_options')) {
        echo 'Permission denied.';
    }

    $upload_dir = wp_upload_dir();
    $json_folder = $upload_dir['basedir'] . '/json-files';
    $file_name = date('Ymd') . '_dhg_products.json';
    $file_path = $json_folder . '/' . $file_name;

    $productsstring = file_get_contents($file_path);
    
    $json = json_decode($productsstring, true);
    $json_p = $json['products'];

    
    return $json_p;  //returns array
}


function cf_grab_categories_from_json () {
    if (!current_user_can('manage_options')) {
        echo 'Permission denied.';
    }

    $fileurl = cf_get_today_json_filepath();
    $jsonData = file_get_contents($fileurl);
    
    // Decode the JSON data into an associative array
    $data = json_decode($jsonData, true);
    $data = $data['products'];

    $c = 0;
    $s = 0;

    foreach ($data as $item) {
        if (isset($item['categories'][0]['id']) && (!in_array($item['categories'][0]['name'], $category_names))) {
            $categories[$c] = $item['categories'][0]['name'];
            $category_names[] = $item['categories'][0]['name'];
            $c++;
        }
    
        if (isset($item['subCategories'][0]['id']) && (!in_array($item['subCategories'][0]['name'], $subCategories))) {
            $subCategories[$s]['subcat_id'] = $item['subCategories'][0]['id'];
            $subCategories[$s]['subcat'] = $item['subCategories'][0]['name'];
            $subCategories[$s]['parent_id'] = $item['categories'][0]['id'];
            $subCategories[$s]['parent'] = $item['categories'][0]['name'];
            $s++;
        }

    }
    return array(
        'categories' => $categories,
        'subCategories' => $subCategories
    );
}


function cf_upload_image_from_url($image_url, $product_id) {

    if (!empty($image_url) && !strpos($image_url, 'no_image.png')) {
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

            if ($attachment_id) {
                $thumb = set_post_thumbnail($product_id, $attachment_id);
                if ($thumb) {
                    cf_attach_image_to_product($product_id, $attachment_id);
                }
                
            }


        } 
   }
}

function cf_attach_image_to_product($product_id, $attachment_id) {
    wp_update_post(array(
        'ID' => $attachment_id,
        'post_parent' => $product_id
    ));
}

function cf_delete_prev_cat_in_dump_table($subcategory) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'dhg_product_dump';

    $wpdb->delete(
        $table_name,
        array('subcategory' => $subcategory),
        array('%d') // Format for WHERE clause
    );
}


function get_parent_category_id($subcategory_id) {
    $term = get_term($subcategory_id, 'product_cat'); // Replace 'product_cat' with your custom taxonomy if applicable

    if (is_wp_error($term) || !isset($term->parent)) {
        return 0; // Return 0 if the subcategory does not exist or has no parent
    }

    return $term->parent;
}

function add_wc_stuff_to_product ($selected) {
           //add some extra WC stuff
           foreach ($selected as $count => $product) { 

            //skip if assortito
            if ($product ['color'] == 'ASSORTITO') {
                continue;
            }

            //skip if no image
            if ((isset($product['image'])) && (strpos($product['image'], 'no_image'))) 
            {
                continue;
            }
           
            $simple[$count]['product'] = $product;
            $simple[$count]['sku']  = $product['variant'];
            $simple[$count]['item_type'] = $product['type'];
      
    } //foreach selected from DB

    return $simple;
}

function get_id_of_parent_wc_cat ($cat) {
    $parent_category_id = get_parent_category_id($cat);
    if ($parent_category_id) {
       $cats = array($parent_category_id, $cat);
    } else {
        $cats = array($cat);
    }

    return $cats;
}


// Utility function that returns the correct product object instance
function cf_get_product_object_type( $type ) {
   
    // Get an instance of the WC_Product object (depending on his type)
    if( isset($type) && $type === 'variable' ){
        $product = new WC_Product_Variable();
    } elseif( isset($type) && $type === 'grouped' ){
        $product = new WC_Product_Grouped();
    } elseif( isset($type) && $type === 'external' ){
        $product = new WC_Product_External();
    } else {
        $product = new WC_Product_Simple(); // "simple" By default
    } 

    if( ! is_a( $product, 'WC_Product' ) )
        return false;
    else
        return $product;
}


function cf_get_dhg_cat_from_wc ($wc_cat) {
    global $wpdb;
    $dhg = $wpdb->get_var( "SELECT `dhg_id` FROM `wp_category_lookup` WHERE `wc_id` = $wc_cat");
    echo 'DHG:'.$dhg;
    return $dhg;
}

function cf_get_wc_cat_from_dhg ($dhg_cat) {
    global $wpdb;
    $wc_cat = $wpdb->get_var( "SELECT `wc_id` FROM `wp_category_lookup` WHERE `dhg_id` = $dhg_cat" );
    return $wc_cat;
}