<?php
//USED BY CSV Product IMPORTER
function filter_selected_items ($items, $cats) {
    
    //iterate array of selected items and determine what to do with them.
    $SKUarray = array();

    $key = 0;
    print("<pre>".print_r($cats,true)."</pre>");

    foreach ($items as  $item) {
        //foreach item, 3 options:
        //simple product, new variable product, variation if existing product
    

        //SIMPLE
        if ($item['item_type'] == 'simple') {
            add_selected_simple_products ($item['product'], $cats, 'simple');
            ;

        //VARIABLE
        } elseif ($item['item_type'] == 'variable') {
            $args['manage_stock'] = false;

            //IF PARENT EXISTS, JUST CREATE VARIATION
            if (in_array ($item['sku'], $SKUarray)) {

             
               
                //get parent ID and send to create variant     
               
                $parent_id = wc_get_product_id_by_sku($item['sku']);
                $variation_data =  array(
                    'attributes' => array(
                        'colour' => ucwords(strtolower($item['product']['color'])),
                    ),
                    'sku' => $item['product']['variant'],
                    //'regular_price' => number_format(floatval(str_replace(',', '.', str_replace('.', '', $item['product']['price']))),2),
                    'regular_price' => $item['product']['price'],
                    'manage_stock' => true,
                    'stock_qty' => $item['product']['meta']['Disponibilità'],
                   
                );

                // Create the variation
               create_product_variation( $parent_id, $variation_data );

            //IF NEW VARIABLE PRDODUCT
            } else {
              

                    //if item SKU does not exist no, save SKU in an SKUarray and send to add a new variable item. Save parent ID in this array too.
                    $parent_id = add_selected_simple_products ($item['product'],  $cats, 'variable');
                    $SKUarray[] = $item['sku'];

                    //then add variation data of this product
                    // echo 'VAR ID'.$item['product']['variant']; 
                   echo '</br>parent: '.$parent_id.'</br>';

                    $variation_data =  array(
                        'attributes' => array(
                            'colour' => $item['product']['color'],
                        ),
                        'sku' => $item['product']['variant'],
                       // 'regular_price' => number_format(floatval(str_replace(',', '.', str_replace('.', '', $item['product']['price']))),2),
                       'regular_price' => $item['product']['price'],
                        'stock_qty'     => $item['product']['quantity'],
                    );
                    create_product_variation( $parent_id, $variation_data );

         
            } //if new or existing variable
        } //if simple or variable
    } //end foreach
} //end function


//FUNCTION TO CREATE SIMPLE PRODUCTS
add_action( 'admin_post_add_selected_products', 'add_selected_products' );
function add_selected_simple_products($product, $cat_ids, $type) {

    //$cat id must be passed or input manually for WP cat id != json cat id.
  
  
    if ((isset($product['image'])) && (!strpos($product['image'], 'no_image'))) {
        $product['image_id'] = crb_insert_attachment_from_url($product['image']);
    } else {
        $product['image_id'] = 17797;
    }
 
   
    if ($type == 'variable') {
        $attributes = ['pa_colour' => $product['color']];
        $short_desc = '';
    } else {
        $short_desc = $product['color'];
        $attributes = array();
    }

    //$price = number_format(floatval(str_replace(',', '.', str_replace('.', '', $product['price']))),2);
    $price = $product['price'];
    $name = ucwords(strtolower($product['name']));

    $product_data = array(
                    'name'               => $name,
                    'slug'               => sanitize_title($name),
                    'date_created'       => null,
                    'date_modified'      => null,
                    'status'             => 'draft',
                    'featured'           => false,
                    'catalog_visibility' => 'visible',
                    'description'        => '',
                    'short_description'  => $short_desc,
                    'sku'                => $product['code'],
                    'price'              => $price,
                    'regular_price'      => $price,
                    'sale_price'         => '',
                    'date_on_sale_from'  => null,
                    'date_on_sale_to'    => null,
                    'total_sales'        => '0',
                    'tax_status'         => 'taxable',
                    'tax_class'          => '',
                    'manage_stock'       => false,
                    'stock_quantity'     => '',
                    'stock_status'       => 'instock',
                    'backorders'         => 'no',
                    'low_stock_amount'   => '',
                    'sold_individually'  => false,
                    'weight'             => '',
                    'length'             => '',
                    'width'              => '',
                    'height'             => '',
                    'upsell_ids'         => array(),
                    'cross_sell_ids'     => array(),
                    'parent_id'          => 0,
                    'reviews_allowed'    => false,
                    'purchase_note'      => '',
                    'attributes'         => $attributes,
                    'default_attributes' => $attributes,
                    'menu_order'         => 0,
                    'post_password'      => '',
                    'virtual'            => false,
                    'downloadable'       => false,
                    'category_ids'       => $cat_ids,
                    'tag_ids'            => array(),
                    'shipping_class_id'  => 0,
                    'downloads'          => array(),
                    'image_id'           => $product['image_id'],
                    'gallery_image_ids'  => array(),
                    'download_limit'     => -1,
                    'download_expiry'    => -1,
                    'rating_counts'      => array(),
                    'average_rating'     => 0,
                    'review_count'       => 0,
                    'type'               => $type
    );
 
    
    $product_id =  create_product_in_wc( $product_data );
    return $product_id;
}

// Custom function for product creation (For Woocommerce 3+ only)
function create_product_in_wc( $args ){
    global $woocommerce;
  
    //fail if function doesnt exist
    if( ! function_exists('wc_get_product_object_type') && ! function_exists('wc_prepare_product_attributes') )
        return false;

    // Get an empty instance of the product object (defining it's type)
    if( !$product = wc_get_product_object_type( $args['type'] ) ) 
        return false;

        
    
    // Product name (Title) and slug
    $product->set_name( $args['name'] ); // Name (title).
    if( isset( $args['slug'] ) )
        $product->set_slug( $args['slug'] );
    
    // Description and short description:
    $product->set_description( $args['description'] );
    $product->set_short_description( $args['short_description'] );

    // Status ('publish', 'pending', 'draft' or 'trash')
    $product->set_status( isset($args['status']) ? $args['status'] : 'draft' );

    // Visibility ('hidden', 'visible', 'search' or 'catalog')
    $product->set_catalog_visibility( isset($args['visibility']) ? $args['visibility'] : 'visible' );

    // Featured (boolean)
    $product->set_featured(  isset($args['featured']) ? $args['featured'] : false );

    // Virtual (boolean)
    $product->set_virtual( isset($args['virtual']) ? $args['virtual'] : false );

    // Prices
    //GENERATE CF PRICE (x.3 rounded to nearest 5c)
   
   // $args['regular_price'] = str_replace(',', '', $args['regular_price']);
    // $orig =  ($args['regular_price'])/3;
    // $cost =  $orig*3.5;
    // $round_num = round($cost / 0.05) * 0.05;
    // $price = number_format($round_num, 2);
   // $args['regular_price'] = $price;
    
    $product->set_price( $args['regular_price'] );
    $product->set_regular_price($args['regular_price']);


    $product->set_sale_price( isset( $args['sale_price'] ) ? $args['sale_price'] : '' );
    $product->set_price( isset( $args['sale_price'] ) ? $args['sale_price'] :  $args['regular_price'] );
    if( isset( $args['sale_price'] ) ){
        $product->set_date_on_sale_from( isset( $args['sale_from'] ) ? $args['sale_from'] : '' );
        $product->set_date_on_sale_to( isset( $args['sale_to'] ) ? $args['sale_to'] : '' );
    }

    // Downloadable (boolean)
    $product->set_downloadable(  isset($args['downloadable']) ? $args['downloadable'] : false );
    if( isset($args['downloadable']) && $args['downloadable'] ) {
        $product->set_downloads(  isset($args['downloads']) ? $args['downloads'] : array() );
        $product->set_download_limit(  isset($args['download_limit']) ? $args['download_limit'] : '-1' );
        $product->set_download_expiry(  isset($args['download_expiry']) ? $args['download_expiry'] : '-1' );
    }

    // Taxes
    if ( get_option( 'woocommerce_calc_taxes' ) === 'yes' ) {
        $product->set_tax_status(  isset($args['tax_status']) ? $args['tax_status'] : 'taxable' );
        $product->set_tax_class(  isset($args['tax_class']) ? $args['tax_class'] : '' );
    }

    // SKU and Stock (Not a virtual product)
    if( isset($args['virtual']) && ! $args['virtual'] ) {
        $product->set_sku( isset( $args['sku'] ) ? $args['sku'] : '' );
        $product->set_manage_stock( isset( $args['manage_stock'] ) ? $args['manage_stock'] : false );
        $product->set_stock_status( isset( $args['stock_status'] ) ? $args['stock_status'] : 'instock' );
        if( isset( $args['manage_stock'] ) && $args['manage_stock'] ) {
            $product->set_stock_status( $args['stock_quantity'] );
            $product->set_stock_quantity( $args['stock_quantity'] );
            $product->set_low_stock_amount( $args['low_stock_amount'] );
            $product->set_backorders( isset( $args['backorders'] ) ? $args['backorders'] : 'no' ); // 'yes', 'no' or 'notify'
        }
    }

    // Sold Individually
    $product->set_sold_individually( isset( $args['sold_individually'] ) ? $args['sold_individually'] : false );

    // Weight, dimensions and shipping class
    $product->set_weight( isset( $args['weight'] ) ? $args['weight'] : '' );
    $product->set_length( isset( $args['length'] ) ? $args['length'] : '' );
    $product->set_width( isset(  $args['width'] ) ?  $args['width']  : '' );

    $product->set_height( isset( $args['height'] ) ? $args['height'] : '' );
    if( isset( $args['shipping_class_id'] ) )
        $product->set_shipping_class_id( $args['shipping_class_id'] );

    // Upsell and Cross sell (IDs)
    $product->set_upsell_ids( isset( $args['upsells'] ) ? $args['upsells'] : '' );
    $product->set_cross_sell_ids( isset( $args['cross_sells'] ) ? $args['upsells'] : '' );

    // Attributes et default attributes
    if( isset( $args['attributes'] ) )
        $product->set_attributes( wc_prepare_product_attributes($args['attributes']) );
    if( isset( $args['default_attributes'] ) )
        $product->set_default_attributes( $args['default_attributes'] ); // Needs a special formatting

    // Reviews, purchase note and menu order
    $product->set_reviews_allowed( isset( $args['reviews'] ) ? $args['reviews'] : false );
    $product->set_purchase_note( isset( $args['note'] ) ? $args['note'] : '' );
    if( isset( $args['menu_order'] ) )
        $product->set_menu_order( $args['menu_order'] );

    // Product categories and Tags
   if( isset( $args['category_ids'] ) )
        $product->set_category_ids( $args['category_ids']);
    if( isset( $args['tag_ids'] ) )
        $product->set_tag_ids( $args['category_ids'] );


    // Images and Gallery
    $product->set_image_id( isset( $args['image_id'] ) ? $args['image_id'] : "" );
    $product->set_gallery_image_ids( isset( $args['gallery_ids'] ) ? $args['gallery_ids'] : array() );

    ## --- SAVE PRODUCT --- ##
    $product_id = $product->save();

    //SET PRODUCT ALT TEXT
    $thisproduct = wc_get_product($product_id);
    if (is_numeric( $args['image_id'])) {

       
        $image_id = $thisproduct->get_image_id();
        
        $alt_text = $thisproduct->get_title();
     
        update_post_meta( $image_id, '_wp_attachment_image_alt', $alt_text );
    } 

   // print("<pre>".print_r($product,true)."</pre>");
    return $product_id;
}


// Utility function that returns the correct product object instance
function wc_get_product_object_type( $type ) {
   
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

// Utility function that prepare product attributes before saving
function wc_prepare_product_attributes( $attributes ){
    global $woocommerce;

    $data = array();
    $position = 0;

    foreach( $attributes as $taxonomy => $values ){
        if( ! taxonomy_exists( $taxonomy ) )
            continue;

        // Get an instance of the WC_Product_Attribute Object
        $attribute = new WC_Product_Attribute();

        $term_ids = array();

        // Loop through the term names
        if (isset($values['term_names'])) {
            foreach( $values['term_names'] as $term_name ) {
                if( term_exists( $term_name, $taxonomy ) )
                    // Get and set the term ID in the array from the term name
                    $term_ids[] = get_term_by( 'name', $term_name, $taxonomy )->term_id;
                else
                    continue;
            }
        }


        $taxonomy_id = wc_attribute_taxonomy_id_by_name( $taxonomy ); // Get taxonomy ID

        $attribute->set_id( $taxonomy_id );
        $attribute->set_name( $taxonomy );
        $attribute->set_options( $term_ids );
        $attribute->set_position( $position );
        $attribute->set_visible( true);
        $attribute->set_variation(true );

        $data[$taxonomy] = $attribute; // Set in an array

        $position++; // Increase position
    }
    return $data;
}

//function to create local new product image from url in json
function crb_insert_attachment_from_url( $url, $parent_post_id = null) {

	if( !class_exists( 'WP_Http' ) )
		include_once( ABSPATH . WPINC . '/class-http.php' );

	$http = new WP_Http();
	$response = $http->request( $url );
    //echo 'RESPONSE: '.$response['response']['code'];
    // if( $response['response']['code'] != 200 ) {
	// 	return false;
	// }
    if (!is_wp_error ($response)) {
        $upload = wp_upload_bits( basename($url), null, $response['body'] );
    }

   
	if(isset($upload) && !empty( $upload['error'] ) ) {
		return false;
	}

	$file_path = $upload['file'];
	$file_name = basename( $file_path );
	$file_type = wp_check_filetype( $file_name, null );
	$attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );
	$wp_upload_dir = wp_upload_dir();

	$post_info = array(
		'guid'           => $wp_upload_dir['url'] . '/' . $file_name,
		'post_mime_type' => $file_type['type'],
		'post_title'     => $attachment_title,
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	// Create the attachment
	$attach_id = wp_insert_attachment( $post_info, $file_path, $parent_post_id );

	// Include image.php
	require_once( ABSPATH . 'wp-admin/includes/image.php' );

	// Define attachment metadata
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );

	// Assign metadata to attachment
    wp_update_attachment_metadata( $attach_id,  $attach_data );
    
	return $attach_id;

}

//function to create a new variation for an existing product (UNEXPLAINED ISSUES WITH $taxonomy VARIABLE. HAD TO HARDCODE TO pa_colour)
function create_product_variation( $product_id, $variation_data ) {
    // Get the Variable product object (parent)
    $product = wc_get_product($product_id);

    $variation_post = array(
        'post_title'  => $product->get_name(),
        'post_name'   => 'product-'.$product_id.'-variation',
        'post_status' => 'publish',
        'post_parent' => $product_id,
        'post_type'   => 'product_variation',
        'guid'        => $product->get_permalink()
    );

	//print_r($product);

    // Creating the product variation
	$variation_id = wp_insert_post( $variation_post );
	
	//echo 'Var ID:'.$variation_id;

    // Get an instance of the WC_Product_Variation object
    $variation = new WC_Product_Variation( $variation_id );
var_dump($variation_data['attributes']);
    // Iterating through the variations attributes
    foreach ($variation_data['attributes'] as $attribute => $term_name )
    {

		echo 'Attrib: '.$attribute.'</br>';
		 echo 'Term: '.$term_name.'</br>';
        $taxonomy = 'pa_'.$attribute.'</br>'; // The attribute taxonomy
		 echo 'Tax: '.$taxonomy.'</br>';

        // If taxonomy doesn't exists we create it (Thanks to Carl F. Corneil)
        //if( ! taxonomy_exists( $taxonomy ) ) {
        if( ! taxonomy_exists( 'pa_colour' ) ) {
            //echo 'TAX DOESNT EXIST</br>';
            $regtax = 
                register_taxonomy(
                $taxonomy,
               'product_variation',
                array(
                    'hierarchical' => false,
                    'label' => ucfirst( strtolower( $attribute) ),
                    'query_var' => true,
                    'rewrite' => array( 'slug' => sanitize_title($attribute) ), // The base slug
                )
			);
			//echo 'Tax Created: '.$regtax.'</br>';
        } 

        // Check if the Term name exist and if not we create it.
        if( ! term_exists( $term_name, 'pa_colour' ) ) {
           // echo 'in !term_exits</br>';
            wp_insert_term( $term_name, 'pa_colour' ); // Create the term
        }
            

        $term_slug = get_term_by('name', $term_name, 'pa_colour' )->slug; // Get the term slug

        // Get the post Terms names from the parent variable product.
        $post_term_names =  wp_get_post_terms( $product_id, 'pa_colour', array('fields' => 'names') );

        // Check if the post term exist and if not we set it in the parent variable product.
        if( ! in_array( $term_name, $post_term_names ) )
            wp_set_post_terms( $product_id, $term_name,'pa_colour', true );

        // Set/save the attribute data in the product variation
        update_post_meta( $variation_id, 'attribute_'.'pa_colour', $term_slug );
    }

    ## Set/save all other data
var_dump($variation_data);
    // SKU
    if( ! empty( $variation_data['sku'] ) )
        $variation->set_sku( $variation_data['sku'] );

    // Prices & stock
    // $variation_data['product']['regular_price'] = str_replace(',', '', $variation_data['product']['regular_price']);


    $variation->set_regular_price($variation_data['regular_price']);
    $variation->set_manage_stock(false);
   

    $variation->set_weight(''); // weight (reseting)
   
   $hj = $variation->save(); // Save the data
   var_dump($hj);
}

// function assorted_variations_creator ($sku, $vars) {
//   // echo 'running function';
//     $parent_id = wc_get_product_id_by_sku($sku);

//   //  print("<pre>".print_r($parent_id,true)."</pre>");


//     //get the price and stock levels
//     $json_products = grab_products_json ();
//     foreach ($json_products as $selected_product) { //start iterating the products for this particular sku
//         if ($sku == $selected_product['code']) { 
//             $price = number_format(floatval(str_replace(',', '.', str_replace('.', '', $selected_product['price']))),2)*3;
//             $stock = $selected_product['meta']['Disponibilità'];
            
//         }
//     }

//     $product = wc_get_product( $parent_id );
//     $product_cats = $product->get_category_ids();
    
//     if (($key = array_search('15', $product_cats)) !== false) {
//         unset($product_cats[$key]);
//     }

//     //remove parent from 'assortito' category
//     $product->set_category_ids( $product_cats);
//     print("<pre>".print_r($product_cats,true)."</pre>");
//     $product->save();

//     //add variations
//     foreach($vars as $c => $variation) {
//         $variation_data =  array(
//             'attributes' => array(
//                 'colour' => $variation,
//             ),
//             'sku' => $sku.'-'.$c,
//             'regular_price' => $price,
//             'stock_qty'     => $stock,
//         );
        
        
//         // Create the variation
//       create_product_variation( $parent_id, $variation_data );
//     }
    
//    // wp_redirect('https://countryflowers.com.mt/wp-admin/admin.php?page=import-products&tab=assortito');
//     exit;
// }
?>