<?php


function cf_create_simple_products($items, $cat, $status ) {
    global $wpdb;

    echo count($items).' simple items to add.</br>';

    //Get the parent category of this one, returns 1D array of parent and child category
    $cats = get_id_of_parent_wc_cat($cat);
    
    foreach ($items as $item) {

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
    
    //check if a parent exists. If yes, it is its variant, if not, create a variable product and create the first variant
    foreach ($items as $key => $item) {
        if ($key == 1) {
            break;
        }

        $product_id = wc_get_product_id_by_sku($item['sku']);

        if ($product_id) {
            echo 'just a variant of '.$product_id;
            //if exits, just create variant
            //cf_add_variation_to_product($product_id, $item, $cat, $status, 'variant');
        } else {
            echo 'there is no product, so create it first.';
            //if does not, first create the parent product, then add the variant colour
           $parent_id = cf_add_variable_product_to_catalog($item, $cats, $status,  'variable');
           echo 'PARENT: '.$parent_id;
          //cf_add_variation_to_product($parent_id, $item, $cat, $status, 'variant');
        }
    }

}


/////////////////////////////////////////
// ADD VARIATION TO PRODUCT           //
///////////////////////////////////////

function cf_add_variation_to_product ($parent_id, $item, $cat, $status, $type) {
    global $woocommerce;
    $variation_data = array(
        'regular_price' =>$item['product']['price'], // Regular price for the variation
        'sku' => $item['sku'], // SKU for the variation
        'stock_status' => 'instock', // Stock status for the variation
    );
    $attributes =  array(
         'pa_colour' => ucwords(strtolower($item['product']['color'])), // Attribute and value for the variation
    );

    $var_id = cf_create_product_variation($parent_id, $attributes, $variation_data);

    if ($var_id) {
        return $var_id;
    } else {
        return false;
    }
}


function cf_create_product_variation($product_id, $attributes, $variation_data) {
    $product = wc_get_product($product_id);
    
    if (!$product || !$product->is_type('variable')) {
        return false; // Not a variable product
    }

    $variation = new WC_Product_Variation();
    $variation->set_parent_id($product_id);

    foreach ($attributes as $attribute => $value) {
        $variation->set_attribute($attribute, $value);
    }

    foreach ($variation_data as $key => $value) {
        $variation->{"set_$key"}($value);
    }

    $variation->save();

    return $variation->get_id();
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
        'image_url' => $item['product']['image']
    );

    // Insert the product post
    // $product_id = wp_insert_post(array( 
    //     'post_title' => $product_data['name'],
    //     'post_content' => $product_data['description'],
    //     'post_excerpt' => $product_data['short_description'],
    //     'post_type' => 'product',
    //     'post_status' => $status,
    // ));


    // Create the product object
    $product_id = wc_create_product($product_data);

    // Return the product ID
    return $product_id;
   
}



/////////////////////////////////////////
// ADD VARIABLE PRODUCT               //
///////////////////////////////////////


function cf_add_variable_product_to_catalog($item, $cats, $status, $type) {
    global $woocommerce;
    // Set up product data
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
                'value' => '', // Attribute options (comma-separated if multiple)
                'position' => 0, // Attribute position
                'is_visible' => 1, // Attribute visibility on the product page
                'is_variation' => 1, // Attribute variation status
                'is_taxonomy' => 1, // Use attribute as taxonomy
            ),
        ),
        'catalog_visibility' => 'visible',
        'image_url' => $item['product']['image']
    );

    // Insert the product post
    // $product_id = wp_insert_post(array(
    //     'post_title' => $product_data['name'],
    //     'post_content' => $product_data['description'],
    //     'post_excerpt' => $product_data['short_description'],
    //     'post_type' => 'product',
    //     'post_status' => $status,
    // ));

    // Create the product object
    $product_id = wc_create_product($product_data);
    return $product_id;
    // Return the product ID
  //  return $product_id;
}


/////////////////////////////////////////
// CREATE THE WC PRODUCT              //
///////////////////////////////////////

function wc_create_product($product_data) {

    global $woocommerce;

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




// function cf_filter_database_items ($items, $cats, $status) {
    
//     //iterate array of selected items and determine what to do with them.
//     $SKUarray = array();

//     $key = 0;

//     foreach ($items as $item) {
//         //foreach item, 3 options:
//         //simple product, new variable product, variation if existing product
//         echo $item['item_type'];
//         //SIMPLE
//         if ($item['item_type'] == 'simple') {
//             echo 'creating simple.</br>';
//             cf_add_selected_simple_products ($item['product'], $cats, 'simple', $status);


//         //VARIABLE
//         } elseif ($item['item_type'] == 'variant') {
//             $args['manage_stock'] = false;
//             echo 'is variant';
//             //IF PARENT EXISTS, JUST CREATE VARIATION
//             if (in_array ($item['sku'], $SKUarray)) {
//                echo 'Creating Variation with existing parent '.$item['sku'].'</br>';
//                 //get parent ID and send to create variant     
//                 //echo 'Variant: '.$item['product']['variant'].'</br>';


//                 $parent_id = wc_get_product_id_by_sku($item['sku']);
//                 $variation_data =  array(
//                     'attributes' => array(
//                         'colour' => ucwords(strtolower($item['product']['color'])),
//                     ),
//                     'sku' => $item['product']['variant'],
//                     'regular_price' => $item['product']['price'],
//                     'manage_stock' => false,
//                 );

//                 // Create the variation
               
//                cf_create_product_variation( $parent_id, $variation_data );

//             //IF NEW VARIABLE PRDODUCT
//             } else {
//                     echo 'new variable';
//                     //if item SKU does not exist no, save SKU in an SKUarray and send to add a new variable item. Save parent ID in this array too.
//                     $parent_id = cf_add_selected_simple_products ($item['product'],  $cats, 'variable', $status);
//                     $SKUarray[] = $item['sku'];

//                     //then add variation data of this product
//                     // echo 'VAR ID'.$item['product']['variant'];
//                     echo 'Creating New Variable </br>';
                   
//                    //$var_price = number_format(floatval(str_replace(',', '.', str_replace('.', '', $item['product']['price']))),2);
//                     echo '</br>Price: '.$item['product']['price'].'</br>';
//                     $variation_data =  array(
//                         'attributes' => array(
//                             'colour' => $item['product']['color'],
//                         ),
//                         'sku' => $item['product']['variant'],
//                        'regular_price' => $var_price,
//                         'manage_stock'     => false,
//                     );
//                     echo '<pre>';
//                     var_dump($parent_id);
//                     echo '</pre>';
//                    cf_create_product_variation( $parent_id, $variation_data );

//             } //if new or existing variable
//         } //if simple or variable
//     } //end foreach
// } //end function


// //FUNCTION TO CREATE SIMPLE PRODUCTS
// add_action( 'admin_post_add_selected_products', 'add_selected_products' );
// function cf_add_selected_simple_products($product, $cat_ids, $type, $status = 'draft') {
//     global $wpdb;

//     //$cat id must be passed or input manually for WP cat id != json cat id.
//   echo '<pre>';
//   var_dump($product);
//   echo '</pre>';
  
//     if ((isset($product['image'])) && (!strpos($product['image'], 'no_image'))) {
//         //if no image, dont create and set DB entry to 0
//         return;
//         $product['image_id'] = crb_insert_attachment_from_url($product['image']);
       
//     } else {
//         return;
//         //$product['image_id'] = 17797;
//     }
 
   
//     if ($type == 'variable') {
//         $attributes = ['pa_colour' => $product['color']];
//         $short_desc = '';
//     } else {
//         $short_desc = $product['color'];
//         $attributes = array();
//     }

//     $price = $product['price'];
//     $name = ucwords(strtolower($product['name']));

//     $product_data = array(
//                     'name'               => $name,
//                     'slug'               => sanitize_title($name),
//                     'date_created'       => null,
//                     'date_modified'      => null,
//                     'status'             => $status,
//                     'featured'           => false,
//                     'catalog_visibility' => 'visible',
//                     'description'        => 'Please allow 10-15 days for delivery.',
//                     'short_description'  => $short_desc,
//                     'sku'                => $product['code'],
//                     'price'              => $price,
//                     'regular_price'      => $price,
//                     'sale_price'         => '',
//                     'date_on_sale_from'  => null,
//                     'date_on_sale_to'    => null,
//                     'total_sales'        => '0',
//                     'tax_status'         => 'taxable',
//                     'tax_class'          => '',
//                     'manage_stock'       => false,
//                     'stock_quantity'     => '',
//                     'stock_status'       => 'instock',
//                     'backorders'         => 'no',
//                     'low_stock_amount'   => '',
//                     'sold_individually'  => false,
//                     'weight'             => '',
//                     'length'             => '',
//                     'width'              => '',
//                     'height'             => '',
//                     'upsell_ids'         => array(),
//                     'cross_sell_ids'     => array(),
//                     'parent_id'          => 0,
//                     'reviews_allowed'    => false,
//                     'purchase_note'      => '',
//                     'attributes'         => $attributes,
//                     'default_attributes' => $attributes,
//                     'menu_order'         => 0,
//                     'post_password'      => '',
//                     'virtual'            => false,
//                     'downloadable'       => false,
//                     'category_ids'       => $cat_ids,
//                     'tag_ids'            => array(),
//                     'shipping_class_id'  => 0,
//                     'downloads'          => array(),
//                     'image_id'           => $product['image_id'],
//                     'gallery_image_ids'  => array(),
//                     'download_limit'     => -1,
//                     'download_expiry'    => -1,
//                     'rating_counts'      => array(),
//                     'average_rating'     => 0,
//                     'review_count'       => 0,
//                     'type'               => $type
//     );
 
    
//     $product_id =  cf_create_product_in_wc( $product_data );
    
//     //update DB as imported
//     $table_name = $wpdb->prefix . 'dhg_product_dump';
//     $wpdb->update(
//         $table_name,
//         array('imported' => 1),
//         array('variant' => $variant), // WHERE clause: 'variant' column equals the specified value
//         array('%d'), // Format for 'imported' column
//         array('%s') // Format for WHERE clause (string in this case)
//     );

//     return $product_id;
// }

// // Custom function for product creation (For Woocommerce 3+ only)
// function cf_create_product_in_wc( $args ){
    
//     global $woocommerce;
  
//     //fail if function doesnt exist
//     if( ! function_exists('wc_get_product_object_type') && ! function_exists('wc_prepare_product_attributes') )
//         return false;

//     // Get an empty instance of the product object (defining it's type)
//     if( !$product = wc_get_product_object_type( $args['type'] ) ) 
//         return false;

        
    
//     // Product name (Title) and slug
//     $product->set_name( $args['name'] ); // Name (title).
//     if( isset( $args['slug'] ) )
//         $product->set_slug( $args['slug'] );
    
//     // Description and short description:
//     $product->set_description( $args['description'] );
//     $product->set_short_description( $args['short_description'] );

//     // Status ('publish', 'pending', 'draft' or 'trash')
//     $product->set_status( isset($args['status']) ? $args['status'] : 'draft' );

//     // Visibility ('hidden', 'visible', 'search' or 'catalog')
//     $product->set_catalog_visibility( isset($args['visibility']) ? $args['visibility'] : 'visible' );

//     // Featured (boolean)
//     $product->set_featured(  isset($args['featured']) ? $args['featured'] : false );

//     // Virtual (boolean)
//     $product->set_virtual( isset($args['virtual']) ? $args['virtual'] : false );

//     // Prices
//     //GENERATE CF PRICE (x.3 rounded to nearest 5c)

    
//     $product->set_price( $args['regular_price'] );
//     $product->set_regular_price($args['regular_price']);


//     $product->set_sale_price( isset( $args['sale_price'] ) ? $args['sale_price'] : '' );
//     $product->set_price( isset( $args['sale_price'] ) ? $args['sale_price'] :  $args['regular_price'] );
//     if( isset( $args['sale_price'] ) ){
//         $product->set_date_on_sale_from( isset( $args['sale_from'] ) ? $args['sale_from'] : '' );
//         $product->set_date_on_sale_to( isset( $args['sale_to'] ) ? $args['sale_to'] : '' );
//     }

//     // Downloadable (boolean)
//     $product->set_downloadable(  isset($args['downloadable']) ? $args['downloadable'] : false );
//     if( isset($args['downloadable']) && $args['downloadable'] ) {
//         $product->set_downloads(  isset($args['downloads']) ? $args['downloads'] : array() );
//         $product->set_download_limit(  isset($args['download_limit']) ? $args['download_limit'] : '-1' );
//         $product->set_download_expiry(  isset($args['download_expiry']) ? $args['download_expiry'] : '-1' );
//     }

//     // Taxes
//     if ( get_option( 'woocommerce_calc_taxes' ) === 'yes' ) {
//         $product->set_tax_status(  isset($args['tax_status']) ? $args['tax_status'] : 'taxable' );
//         $product->set_tax_class(  isset($args['tax_class']) ? $args['tax_class'] : '' );
//     }

//     // SKU and Stock (Not a virtual product)
//     if( isset($args['virtual']) && ! $args['virtual'] ) {
//         $product->set_sku( isset( $args['sku'] ) ? $args['sku'] : '' );
//         $product->set_manage_stock( isset( $args['manage_stock'] ) ? $args['manage_stock'] : false );
//         $product->set_stock_status( isset( $args['stock_status'] ) ? $args['stock_status'] : 'instock' );
//         if( isset( $args['manage_stock'] ) && $args['manage_stock'] ) {
//             $product->set_stock_status( $args['stock_quantity'] );
//             $product->set_stock_quantity( $args['stock_quantity'] );
//             $product->set_low_stock_amount( $args['low_stock_amount'] );
//             $product->set_backorders( isset( $args['backorders'] ) ? $args['backorders'] : 'no' ); // 'yes', 'no' or 'notify'
//         }
//     }

//     // Sold Individually
//     $product->set_sold_individually( isset( $args['sold_individually'] ) ? $args['sold_individually'] : false );

//     // Weight, dimensions and shipping class
//     $product->set_weight( isset( $args['weight'] ) ? $args['weight'] : '' );
//     $product->set_length( isset( $args['length'] ) ? $args['length'] : '' );
//     $product->set_width( isset(  $args['width'] ) ?  $args['width']  : '' );

//     $product->set_height( isset( $args['height'] ) ? $args['height'] : '' );
//     if( isset( $args['shipping_class_id'] ) )
//         $product->set_shipping_class_id( $args['shipping_class_id'] );

//     // Upsell and Cross sell (IDs)
//     $product->set_upsell_ids( isset( $args['upsells'] ) ? $args['upsells'] : '' );
//     $product->set_cross_sell_ids( isset( $args['cross_sells'] ) ? $args['upsells'] : '' );

//     // Attributes et default attributes
//     if( isset( $args['attributes'] ) )
//         $product->set_attributes( wc_prepare_product_attributes($args['attributes']) );
//     if( isset( $args['default_attributes'] ) )
//         $product->set_default_attributes( $args['default_attributes'] ); // Needs a special formatting

//     // Reviews, purchase note and menu order
//     $product->set_reviews_allowed( isset( $args['reviews'] ) ? $args['reviews'] : false );
//     $product->set_purchase_note( isset( $args['note'] ) ? $args['note'] : '' );
//     if( isset( $args['menu_order'] ) )
//         $product->set_menu_order( $args['menu_order'] );

//     // Product categories and Tags
//    if( isset( $args['category_ids'] ) )
//         $product->set_category_ids( $args['category_ids']);
//     if( isset( $args['tag_ids'] ) )
//         $product->set_tag_ids( $args['category_ids'] );


//     // Images and Gallery
//     $product->set_image_id( isset( $args['image_id'] ) ? $args['image_id'] : "" );
//     $product->set_gallery_image_ids( isset( $args['gallery_ids'] ) ? $args['gallery_ids'] : array() );

//     ## --- SAVE PRODUCT --- ##
//     $product_id = $product->save();

//     //SET PRODUCT ALT TEXT
//     $thisproduct = wc_get_product($product_id);
//     if (is_numeric( $args['image_id'])) {

       
//         $image_id = $thisproduct->get_image_id();
        
//         $alt_text = $thisproduct->get_title();
     
//         update_post_meta( $image_id, '_wp_attachment_image_alt', $alt_text );
//     } 

//    // print("<pre>".print_r($product,true)."</pre>");
//     return $product_id;
// }



// //function to create a new variation for an existing product (UNEXPLAINED ISSUES WITH $taxonomy VARIABLE. HAD TO HARDCODE TO pa_colour)
// function cf_create_product_variation( $product_id, $variation_data ) {
//     // Get the Variable product object (parent)
//     $product = wc_get_product($product_id);
//     echo '<pre>';
//     var_dump($product);
//     echo '</pre>';
//     echo 'RUNNING FOR '.$product_id;
//     if (is_bool($product->get_name())) {
//         var_dump($product);
//         return;
//     }

//         $variation_post = array(
//             'post_title'  => $product->get_name(),
//             'post_name'   => 'product-'.$product_id.'-variation',
//             'post_status' => 'publish',
//             'post_parent' => $product_id,
//             'post_type'   => 'product_variation',
//             'guid'        => $product->get_permalink()
//         );
 
    

// 	//print_r($product);

//     // Creating the product variation
// 	$variation_id = wp_insert_post( $variation_post );
	
// 	//echo 'Var ID:'.$variation_id;

//     // Get an instance of the WC_Product_Variation object
//     $variation = new WC_Product_Variation( $variation_id );

//     // Iterating through the variations attributes
//     foreach ($variation_data['attributes'] as $attribute => $term_name )
//     {

// 		echo 'Attrib: '.$attribute.'</br>';
// 		 echo 'Term: '.$term_name.'</br>';
//         $taxonomy = 'pa_'.$attribute.'</br>'; // The attribute taxonomy
// 		 echo 'Tax: '.$taxonomy.'</br>';

//         // If taxonomy doesn't exists we create it (Thanks to Carl F. Corneil)
//         //if( ! taxonomy_exists( $taxonomy ) ) {
//         if( ! taxonomy_exists( 'pa_colour' ) ) {
//             //echo 'TAX DOESNT EXIST</br>';
//             $regtax = 
//                 register_taxonomy(
//                 $taxonomy,
//                'product_variation',
//                 array(
//                     'hierarchical' => false,
//                     'label' => ucfirst( strtolower( $attribute) ),
//                     'query_var' => true,
//                     'rewrite' => array( 'slug' => sanitize_title($attribute) ), // The base slug
//                 )
// 			);
// 			//echo 'Tax Created: '.$regtax.'</br>';
//         } 

//         // Check if the Term name exist and if not we create it.
//         if( ! term_exists( $term_name, 'pa_colour' ) ) {
//            // echo 'in !term_exits</br>';
//             wp_insert_term( $term_name, 'pa_colour' ); // Create the term
//         }
            

//         $term_slug = get_term_by('name', $term_name, 'pa_colour' )->slug; // Get the term slug

//         // Get the post Terms names from the parent variable product.
//         $post_term_names =  wp_get_post_terms( $product_id, 'pa_colour', array('fields' => 'names') );

//         // Check if the post term exist and if not we set it in the parent variable product.
//         if( ! in_array( $term_name, $post_term_names ) )
//             wp_set_post_terms( $product_id, $term_name,'pa_colour', true );

//         // Set/save the attribute data in the product variation
//         update_post_meta( $variation_id, 'attribute_'.'pa_colour', $term_slug );
//     }

//     ## Set/save all other data

//     // SKU
//     if( ! empty( $variation_data['sku'] ) )
//         $variation->set_sku( $variation_data['sku'] );

//     // Prices & stock
//     // $variation_data['product']['regular_price'] = str_replace(',', '', $variation_data['product']['regular_price']);


//     $variation->set_regular_price($variation_data['regular_price']);
//     $variation->set_manage_stock(false);
   

//     $variation->set_weight(''); // weight (reseting)
   
//    $hj = $variation->save(); // Save the data

// }