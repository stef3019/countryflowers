<?php


function cf_create_simple_products($items, $cat, $status ) {
    global $wpdb;

    $count = count($items);
    echo $count.' simple items to add.</br>';
    //Get the parent category of this one, returns 1D array of parent and child category
    $cats = get_id_of_parent_wc_cat($cat);
    if ($count > 0) {
        foreach ($items as $item) {

            $product_id = wc_get_product_id_by_sku($item['sku']);
            if ($product_id) {
                continue;
            }
            //foreach item, add it to the catalogue as a simple product
            cf_add_simple_product_to_catalog($item, $cats, $status, 'simple');
            
            //update DB as imported
            $table_name = $wpdb->prefix . 'dhg_product_dump';
            $wpdb->update(
                $table_name,
                array('imported' => 1),
                array('variant' => $item['sku']), // WHERE clause: 'variant' column equals the specified value
                array('%d'), // Format for 'imported' column
                array('%s') // Format for WHERE clause (string in this case)
            );
        }
    }
}



function cf_create_variables_and_variants ($items, $cat, $status) {
    global $wpdb;
    
    $cats = get_id_of_parent_wc_cat($cat);

    //for the group that just came in, create the variable product and its associated variants
    cf_create_variable_product_with_variations($items, $cats, $status);
    if (count($items) > 0) {
        foreach($items as $item) {
            //update DB as imported
            $table_name = $wpdb->prefix . 'dhg_product_dump';
            $wpdb->update(
                $table_name,
                array('imported' => 1),
                array('variant' => $item['sku']), // WHERE clause: 'variant' column equals the specified value
                array('%d'), // Format for 'imported' column
                array('%s') // Format for WHERE clause (string in this case)
            );
        }
    } else {
        echo 'Something weird for item '.$item['sku'];
    }
    
}


/////////////////////////////////////////
// ADD SIMPLE PRODUCT                 //
///////////////////////////////////////

function cf_add_simple_product_to_catalog($item, $cats, $status, $type) {
    global $woocommerce;
   
    // Set up product data
    $product_data = array(
        'name' => ucwords(strtolower($item['product']['name'])), // Product name
        'slug' => sanitize_title(ucwords(strtolower($item['product']['name']))),
        'type' => $type, // Product type
        'regular_price' => $item['product']['price'], // Regular price
        'description'  => 'Please allow 10-15 days for delivery.',
        'short_description' => ucwords(strtolower($item['product']['name'])).' '.$item['product']['color'], // Short description
        'sku' =>  $item['sku'], // SKU
        'stock_status' => 'instock', // Stock status
        'manage_stock' => false, // Manage stock
        'stock_quantity' => '', // Stock quantity
        'categories' => $cats, // Product categories
        'catalog_visibility' => 'visible',
        'image_url' => $item['product']['image'],
        'status' => $status
    );

    // Create the product object
    $product_id = cf_create_simple_product($product_data);

    // Return the product ID
    return $product_id;
   
}


/////////////////////////////////////////
// CREATE THE SIMPLE WC PRODUCT       //
///////////////////////////////////////

function cf_create_simple_product($product_data) {

    global $woocommerce;

    // Initialise the $product from the Woo Commerce product classes
    $product = cf_get_product_object_type($product_data['type']);

    //$product = wc_get_product($product_id);


    // Set the product data
    $product->set_name($product_data['name']);
    $product->set_slug($product_data['slug']);
    $product->set_regular_price($product_data['regular_price']);
    $product->set_description($product_data['description']);
    $product->set_short_description($product_data['short_description']);
    $product->set_sku($product_data['sku']);
    $product->set_stock_status($product_data['stock_status']);
    $product->set_manage_stock($product_data['manage_stock']);
    $product->set_stock_quantity($product_data['stock_quantity']);
    $product->set_category_ids($product_data['categories']);
    $product->set_catalog_visibility('visible');
    $product->set_status($product_data['status']);
    $product->set_reviews_allowed(false);

    // Save the product
    $product->save();
    $product_id = $product->get_id();

    if (is_wp_error($product_id )) {
        $error_message = $product_id->get_error_message();
        return 'Error: ' . $error_message;
    }

     // Set the main product image
     $attachment_id = cf_upload_image_from_url($product_data['image_url'], $product_id);

    return $product_id;
}


/////////////////////////////////////////
// CREATE VARIABLE & IT'S VARIATION   //
///////////////////////////////////////

function cf_create_variable_product_with_variations($var_group, $cats, $status) {
    global $woocommerce, $wpdb;

    $sku = $var_group[0]['product']['code'];
    $colours = array();
    foreach ($var_group as $variant) {
        $colours[] = ucwords(strtolower($variant['product']['color']));
        $skus[] = $variant['sku'];
    }
    $colours_list = implode('|', $colours);
   

    // Create a new variable product
    $product = new WC_Product_Variable();
    $product->set_name(ucwords(strtolower($var_group[0]['product']['name'])));
    $product->set_slug(sanitize_title(ucwords(strtolower($var_group[0]['product']['name']))));


    $product->set_description('Please allow 10-15 days for delivery.');
    $product->set_short_description(ucwords(strtolower($var_group[0]['product']['name'])));

    $product->set_regular_price($var_group[0]['product']['price']);
   
    $product->set_manage_stock(false);
    $product->set_stock_status('instock');

   
    $product->set_category_ids($cats);

    $product->set_catalog_visibility('visible');
    $product->set_reviews_allowed(false);

    $product->set_status($status);

    $product->save();

    $product_id = $product->get_id();
    // Check if SKU is already used
    
    $product->set_sku($sku);
    $product->save();
    

    // Add the "colour" attribute and its terms
    $attribute_name = 'pa_colour';
    $attribute_label = 'Colour';
    $attribute_values = $colours;

    $attribute = new WC_Product_Attribute();
    $attribute->set_name($attribute_name);
    $attribute->set_visible(true);
    $attribute->set_variation(true);
    $attribute->set_options($attribute_values);



    $product->set_attributes(array($attribute));
    $product->save();

   

    wp_set_post_terms( $product_id, $attribute_values, 'pa_colour', false );

    if (is_wp_error($product_id)) {
        $error_message = $result->get_error_message();
        return 'Error: ' . $error_message;
    }

     // Set the main product image
     $imageUrl = $var_group[0]['product']['image'];
     $attachment_id = cf_upload_image_from_url($imageUrl, $product_id);

     $pa_terms = array();
    // Create variations for each attribute value
    foreach ($colours as $key => $value) {

        $attributes = ['pa_colour' => $value];


        $variation = new WC_Product_Variation();
        $variation->set_regular_price($var_group[0]['product']['price']);
        $variation->set_parent_id($product_id);
        $variation->set_attributes($attributes);
        $variation->set_manage_stock(false);
        $variation->set_stock_status('instock');
        
        $variation->set_sku($skus[$key]);
        $variation->save();

        // Now update some value unrelated to attributes.
        $variation = wc_get_product($variation->get_id());
        $variation->set_status($status);
        $variation->save();
    
    }

    $product->save();
    
  
    return $product_id;
}