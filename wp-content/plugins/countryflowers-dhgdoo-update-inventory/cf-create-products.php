<?php


function cf_create_simple_products($items, $cat, $status ) {
    global $wpdb;

    echo count($items).' simple items to add.</br>';

    //Get the parent category of this one, returns 1D array of parent and child category
    $cats = get_id_of_parent_wc_cat($cat);
    
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



function cf_create_variables_and_variants ($items, $cat, $status) {
    global $wpdb;
    
    $cats = get_id_of_parent_wc_cat($cat);

    //for the group that just came in, create the variable product and its associated variants
    cf_create_variable_product_with_variations($items, $cats);

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

   // }

}


/////////////////////////////////////////
// ADD VARIATION TO PRODUCT           //
///////////////////////////////////////

// function cf_add_variation_to_product ($parent_id, $item, $cat, $status, $type) {
//     global $woocommerce;
//     $variation_data = array(
//         'regular_price' =>$item['product']['price'], // Regular price for the variation
//         'sku' => $item['sku'], // SKU for the variation
//         'stock_status' => 'instock', // Stock status for the variation
//     );
//     $attributes =  array(
//          'pa_colour' => ucwords(strtolower($item['product']['color'])), // Attribute and value for the variation
//     );

//     $var_id = cf_create_product_variation($parent_id, $attributes, $variation_data);

//     if ($var_id) {
//         return $var_id;
//     } else {
//         return false;
//     }
// }


// function cf_create_product_variation($product_id, $attributes, $variation_data) {
//     global $woocommerce;
//     $product = wc_get_product($product_id);
    
//     if (!$product || !$product->is_type('variable')) {
//         return false; // Not a variable product
//     }

//     $variation = new WC_Product_Variation();
//     $variation->set_parent_id($product_id);
//     $variation->set_attributes($attributes);

//     foreach ($variation_data as $key => $value) {
//         $variation->{"set_$key"}($value);
//     }

//     $variation->save();

//     return $variation->get_id();
// }



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
        'image_url' => $item['product']['image']
    );

    // Create the product object
    $product_id = cf_create_simple_product($product_data);

    // Return the product ID
    return $product_id;
   
}



/////////////////////////////////////////
// ADD VARIABLE PRODUCT               //
///////////////////////////////////////


function cf_add_variable_product_to_catalog($item, $cats, $status, $type) {
    global $woocommerce, $wpdb;
    // Set up product data
    $sku = $item['sku'];
    $value = array();
    $att_values =  $att_terms = $wpdb->get_results( "SELECT DISTINCT `color` FROM `wp_dhg_product_dump` WHERE `code` = $sku", ARRAY_A );
    foreach ($att_values as $value) {
        $values[] = $value['pa_colour']; 
    }
    $values = implode(',', $values);

    $product_data = array(
        'name' => ucwords(strtolower($item['product']['name'])), // Product name
        'slug' => sanitize_title(ucwords(strtolower($item['product']['name']))),
        'type' => $type,// Product type
        'regular_price' => $item['product']['price'], // Regular price
        'description'  => 'Please allow 10-15 days for delivery.', // Product description
        'short_description' => ucwords(strtolower($item['product']['name'])), // Short description
        'sku' =>  $item['sku'], // SKU
        'stock_status' => 'instock', // Stock status
        'manage_stock' => false, // Manage stock
        'categories' => $cats,  // Product category IDs (replace with the desired category ID)
        'attributes' => array(
            'pa_colour' => array(
                'name' => 'Colour', // Attribute name
                'value' => $values, // Attribute options (comma-separated if multiple)
                'position' => 1, // Attribute position
                'is_visible' => 1, // Attribute visibility on the product page
                'is_variation' => 1, // Attribute variation status
                'is_taxonomy' => 1, // Use attribute as taxonomy
            ),
        ),
        'catalog_visibility' => 'visible',
        'image_url' => $item['product']['image']
    );

    // Create the product object
    $product_id = cf_create_simple_product($product_data);
    
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
    $product->set_reviews_allowed(false);

    //if is variable

    if ($product_data['type'] == 'variable') {
        echo 'setting attributes';
        $product->set_attributes($product_data['attributes']);
    }


    // Save the product
    $product->save();
    $product_id = $product->get_id();

    if (is_wp_error($result)) {
        $error_message = $result->get_error_message();
        return 'Error: ' . $error_message;
    }

     // Set the main product image
     $attachment_id = cf_upload_image_from_url($product_data['image_url'], $product_id);

    return $product_id;
}


//NEW TEST FUNCTION
function cf_create_variable_product_with_variations($var_group, $cats) {
    global $woocommerce, $wpdb;

    $sku = $var_group[0]['product']['code'];
    $colours = array();
    foreach ($var_group as $variant) {
        $colours[] = ucwords(strtolower($variant['product']['color']));
    }
    $colours_list = implode('|', $colours);
   

    // Create a new variable product
    $product = new WC_Product_Variable();
    $product->set_name(ucwords(strtolower($var_group[0]['product']['name'])));
    $product->set_slug(sanitize_title(ucwords(strtolower($var_group[0]['product']['name']))));
    $product->set_sku($sku);

    $product->set_description('Please allow 10-15 days for delivery.');
    $product->set_short_description(ucwords(strtolower($var_group[0]['product']['name'])));

    $product->set_regular_price($var_group[0]['product']['price']);
   
    $product->set_manage_stock(false);
    $product->set_stock_status('instock');

   
    $product->set_category_ids($cats);

    $product->set_catalog_visibility('visible');
    $product->set_reviews_allowed(false);

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

    $product_id = $product->get_id();

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
    foreach ($colours as $value) {

        $attributes = ['pa_colour' => $value];


        $variation = new WC_Product_Variation();
        $variation->set_regular_price($var_group[0]['product']['price']);
        $variation->set_parent_id($product_id);
        $variation->set_attributes($attributes);
        $variation->set_manage_stock(false);
        $variation->set_stock_status('instock');
        $variation->save();

        // Now update some value unrelated to attributes.
        $variation = wc_get_product($variation->get_id());
        $variation->set_status('publish');
        $variation->save();
    
        // $pa_term = get_term_by('name',$value, 'pa_colour');
        // $pa_term_ids[] = $pa_term->term_id;
    }

    $product->save();
    
  
    return $product_id;
}