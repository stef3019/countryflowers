<?php

/* SCAN FOR CHANGES */
// Scan for new categories
// 1. check file for categories, and create old_cat array.
// 2. scan new file for categories and compare to old_cat array.


// Scan for new products in given categories
// 1. Foreach old category, check if each available product exists in WC. 
// 2. If new ones are found, report them and add them


// Scan for changes in stock levels & pricing in existing products
// 1. Foreach WC product, look for it in the json file and compare stock level
// 2. If stock level is different but not zero, update it
// 3. If new stock level is 0, create an Alert


/*  ------------  NEW ------------------
Foreach WC product, scan today's json to see if its still there. 
    - IF NO, delete it
    - IF YES, check stock and update it if different

*/



add_action ( 'cf_daily_sync', 'trigger_product_sync' );

function trigger_product_sync() {
    sync_inventory ();
}  


//add_shortcode( 'testcall', 'sync_inventory' );
/* Sync Functions */
function sync_inventory () {
    
    global $wpdb;
    check_products_file ();
    //grab today's products from json file
    $json_p = grab_products_json ();
   // $json_s = grab_json_string();
//var_dump($json_p);
    //grab WC inventory
    $args = array(
//        'visibility' => 'catalog',
        'limit' => -1,
    );
    $products = wc_get_products( $args );

    

    foreach ($products as $product) {
    //  $sku = 89319;
    //  $pid = 10136;
    $pid = $product->get_id();
    //check if variable
     $product_s = wc_get_product( $pid );

    if ($product_s->product_type == 'variable') {
        $args = array(
            'post_parent' =>  $pid,
            'post_type'   => 'product_variation',
            'numberposts' => -1,
        );
        $variations = $product_s->get_available_variations();
        // echo '<pre>';
        // print_r($variations);
        // echo '</pre>';
        //foreach variation, run the following
       // echo 'VARIATION INCOMING:: </br>';
        foreach ($variations as $variation) {
            
            $var = new WC_Product_Variation($variation['variation_id']);
            sync_inventory_actions ($var, $json_p);
        
        }
     //  echo '-- END VARIATIONS --</br>';
    }   else { //if simple product
        sync_inventory_actions ($product, $json_p);
    }
    
}

}


function sync_inventory_actions ($product, $json_p) {

   global $wpdb;
   $sku = $product->get_sku();
   $name = $product->get_name();
   $pid = $product->get_id();
   $image = wp_get_attachment_image_src( get_post_thumbnail_id($pid), 'single-post-thumbnail' );

    if (strpos($sku, 'ff-') !== FALSE) {
         echo 'skipping '.$sku.'</br>'; //if found, then skip
         return;
     }

     if (strpos($sku, 'cf-') !== FALSE) {
         echo 'skipping '.$sku.'</br>'; //if found, then skip
         return;
     }
 
     //search for item. If not found, mark as deleted
     $json_p_key = array_search($sku, array_column($json_p, 'variant'));
     if (empty($json_p_key)) {
         $json_p_key = array_search($sku, array_column($json_p, 'code'));
     }
     
     if (empty($json_p_key)) {  //product is not found in today's json, so delete the WC
       // echo 'not in json';
        //record event
        $narrative = 'Product '.$sku.' '.$name.' not found.</br> DELETING: '.$pid;
      
         $wpdb->insert('wp_cf_products_alerts', 
         array(
             'alert_type' => 'deleted',
             'alert_narrative' => $narrative,
             'alert_related_id' => $sku,
             )
         );

         //Get WC ID of the product and delete it.
       $deleted = wc_deleteProduct($pid);
      // echo $narrative.'</br>';


      //   return;
         


     } else {  
              
         //product is found, check stock level and update
        
        // Get current stock level and check if its the same
        $new_qty = $json_p[$json_p_key]['quantity'];
        $product = wc_get_product($pid);
         wc_set_product_stock($pid, $new_qty, $sku, $name);
    
        // Get current price, apply CF pricing and check if its is the same
        $orig_price = $json_p[$json_p_key]['price'];
        $cleaned =  (number_format(floatval(str_replace(',', '.', $orig_price)),2));
        $cleaned = str_replace(',', '', $cleaned);
         echo 'Orig '.$cleaned.' - ';
       

        $new_price = $cleaned/3;
        echo 'CF '.$new_price.' - ';
        $new_price = (number_format(floatval(str_replace(',', '.', $new_price)),2))*3.5;
        
        echo 'Multiplied '.$new_price.' - ';
        $new_price = round($new_price / 0.05) * 0.05;
        echo 'Rounded '.$new_price.' - ';
        $price = number_format($new_price, 2);
        echo 'Decimaled '.$price.'</br>';

       wc_set_product_sync_price($pid, $price, $sku, $name);

               
         //if image is no image, then check if update is available. 
         if (strpos($image, 'no_image')) { 
         //if current image is NoImage.jpg see if image is there
            $new_image = $json_p[$json_p_key]['Image'];
            
            if (!strpos($new_image, 'no_image')) {  //if json image for product does not contain no_image, then replace it. 

                //grab the image and upload it
                $newImgId = crb_insert_attachment_from_url($json_p[$json_p_key]['Image']);
                
                $product->set_image_id( isset( $newImgId ) ? $newImgId : "" );
                $product->save();

                $wpdb->insert('wp_cf_products_alerts', 
                    array(
                        'alert_type' => 'image',
                        'alert_narrative' => 'Image Udpated',
                        'alert_related_id' => $sku,
                        )
                    );

	            wc_delete_product_transients( $pid );
            }

         }
       
    }
}


function scan_for_new_products () {

    //grab today's products from json file
    $json_p = grab_products_json ();

    //search wc products for sku, if not found then probably a new product
    foreach ($json_p as $key => $item) {

    

    //check if in existing category

    //if yes, add.


    }
}



function deal_with_comma_decimal( $search) {
    $search = ',';
    $replace = '.';
    if( ( $pos = strrpos( $str , $search ) ) !== false ) {
        $search_length  = strlen( $search );
        $str    = substr_replace( $str , $replace , $pos , $search_length );
    }
    return $str;
}