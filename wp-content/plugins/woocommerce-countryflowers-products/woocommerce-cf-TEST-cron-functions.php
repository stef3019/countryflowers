<?php

//used this hook to create cron job in cron plugin
//add_action( 'cf_file_checker', 'check_products_file' );

//add_shortcode( 'playground', 'check_products_file' ); 
function add_cat() {
    $args = array(
        'visibility' => 'catalog',
    );
    $products = wc_get_products( $args );
    $jsonst_products = grab_json_string();
    
    //look for the sku in the json file
    foreach ($products as $product) {
        $sku = $product->get_sku();
        $id_product = $product->get_id();
    
        $term_list = wp_get_post_terms($id_product,'product_cat',array('fields'=>'names'));

        $cat_prod = implode(' -> ', $term_list);
       // $cat_id = (int)$term_list[0];
        echo $cat_prod;
    
        //search for $sku in .json file
        $sku_search =  '"variant":"'.$sku.'"';
        if (strpos($jsonst_products, $sku_search) == FALSE) { //if not found, create alert
            echo $cat_prod.'</br>';
            // $wpdb->insert('wp_cf_products_alerts', 
            // array(
            //     'alert_type' => 'product_removed',
            //     'alert_narrative' => 'Product with code <strong>'.$sku.'</strong> has been removed.',
            //     'alert_related_id' => $sku,
            //     'alert_actioned' => 0,
            //     'alert_ignored' => 0
            //     )
            // );
        }
    }
}



function check_products_file () {

    //look for today's .json file
    $filename = 'raw_products'.date('dmy').'.json';
    chdir($_SERVER['DOCUMENT_ROOT'] .'/wp-content/uploads/imported_products');

    //if the file is 
    if ( file_exists( $filename ) ) {
            $file_time = filemtime( $filename );
            $expire = 86400; 
            $expire_time = $file_time < ( time() - $expire);
            $expire = 86400; // Time in seconds to cache the file for (1 day)
        
            if ( $file_time < ( time() - $expire ) ) {
            // if expired, overwrite file
            write_json_to_file( $filename );
            $json = file_get_contents( 'https://dhgdoo.eu/writable/export/products.json');

            file_put_contents( $filename, $json );
            }

    // if file does not exist, write to file
    } else {
        write_json_to_file( $filename );
        $json = file_get_contents( 'https://dhgdoo.eu/writable/export/products.json');
        file_put_contents( $filename, $json );
    }
        
   // scan_products_for_changes();
        
    //header('Content-Type: application/json');
    $file_data = file_get_contents( $filename );  //? not sure why this is here...
    //wp_die();                                                                                                                                                                                                                                                
}



//helper function to write the products to a .json file for quicker reference
function write_json_to_file( $filename ) {
    $json = file_get_contents( 'https://dhgdoo.eu/writable/export/products.json');
    $time = time();
    file_put_contents( $filename, $json );
}


/* SCAN FOR CHANGES */
// Scan for new categories
// 1. check file for categories, and create old_cat array.
// 2. scan new file for categories and compare to old_cat array.


// Scan for new products in given categories
// 1. Foreach old category, check if each available product exists in WC. 
// 2. If new ones are found, report them and add them


// Scan for changes in stock levels in existing products
// 1. Foreach WC product, look for it in the json file and compare stock level
// 2. If stock level is different but not zero, update it
// 3. If new stock level is 0, create an Alert




function scan_products_for_changes() {
					
    global $wpdb, $newcats;
    $newcats = array();
    $unaccounted = array();
    $json_p = grab_products_json ();
    $email_message = '';



    //echo 'Total: '.count($json_p).'</br>';
    
    //foreach product, compare its SKU with a current product/variation
    //Produts from DHG Json = "item". Woocommerce products = "product"
    foreach ($json_p as $key => $item) {

    
        //scan for new subcategories. If found, create Alert.
        if (!empty($item['subCategories'][0]['id'])) {
            scan_for_new_subcategories($item['subCategories'][0]['id']);
        }
        // continue;
       

        //Products with known issues - THIS HAS TO BECOME A BLACKLIST TABLE IN DB
        if ($item['variant']=='601') {
            continue;
        }

        if ($item['variant']=='604') {
            continue;
        }



 //conditions to skip categories
        if(!empty($item['categories'])) {
       

            //skip christmas 
            if ($item['categories'][0]['id']=='1') {
                continue;
            }

            //skip spring 
            if ($item['categories'][0]['id']=='6') {
                continue;
            }
        }

        //get the var SKU
        $pid = wc_get_product_id_by_sku($item['variant']);
        //in the json file, the 'variant' field is what becomes the SKU in WooCommerce

        

        if ($pid != 0) {  //if found, a pid will be returned. If 0, probably new product. 

            
            $wcproduct = wc_get_product( $pid ); //get the wc_product object
            $type = $wcproduct->get_type; //check what wc_product type it is

          
            $stock = $wcproduct->get_stock_quantity(); //check current wc stock level
            $difference = $stock-$item['meta']['Disponibilità']; //see if this has changed (it is either more, less or unchanged.)
               

            

            //if stock is 0, create an alert

            if ((($item['meta']['Disponibilità'] == 0)&&($difference > 0))&&($wcproduct->get_catalog_visibility != 'hidden')) { 
                //if out of stock or difference is greater than 0 or not already hidden from visibility, create alert that is now out of stock
                //CURRENTLY IGNOR SPRING 
                if ($item['categories'][0]['id'] != 6 ) {
                    $wpdb->insert('wp_cf_products_alerts', 
                    array(
                        'alert_type' => 'zero_stock',
                        'alert_narrative' => 'Product with code: <strong>'.$item['variant'].'</strong> in category '.$item['categories'][0]['name'].' -> '.$item['subCategories'][0]['name'].'  is now out of stock.',
                        'alert_related_id' => $item['variant'],
                        'alert_dhg_cat' => $item['categories'][0]['id'],
                        'alert_dhg_subcat' => $item['subCategories'][0]['id'],
                        'alert_actioned' => 0,
                        'alert_ignored' => 0
                        )
                    );
                }
               
                continue; //next item
            }


        } else {   //if ($pid != 0)
            $unaccounted[] = $item['variant']; 

                //if out of stock or difference is greater than 0 or not already hidden from visibility, create alert that is now out of stock
                if(!empty($item['categories'])) {
                    $narrative = 'Product with code: <strong>'.$item['variant'].'</strong> in category '.$item['categories'][0]['name'].' -> '.$item['subCategories'][0]['name'].' has been added to the list.';
                    if ($item['categories'][0]['id'] != 6 ) {
                        $wpdb->insert('wp_cf_products_alerts', 
                        array(
                            'alert_type' => 'new_product_added',
                            'alert_narrative' => $item['name'],
                            'alert_related_id' => $item['variant'],
                            'alert_item_price' => str_replace(',', '.', $item['price']),
                            'alert_dhg_cat' => $item['categories'][0]['id'],
                            'alert_dhg_subcat' => $item['subCategories'][0]['id'],
                            'alert_actioned' => 0,
                            'alert_ignored' => 0
                            )
                        );
                    }
                    continue; //next item
                }   
            } //else $pid    
    } //foreach json item


    //create alerts for new_categories
    $newcats = array_unique($newcats);
    foreach($newcats as $newcat) {

        //lookfor this id and get its name
        foreach ($json_p as $key => $item) {
            //if subcat is this one, get the name
            if(!empty($item['categories'])) {
                if ($newcat == $item['subCategories'][0]['id']) {
                    $name = $item['subCategories'][0]['name'];
                    $parent = $item['categories'][0]['name'];
                    $parent_id = $item['categories'][0]['id'];
                    continue;
                }
            }
        }
        
        $wpdb->insert('wp_cf_products_alerts', 
        array(
            'alert_type' => 'new_subcategory',
            'alert_narrative' => 'A new subcategory '.$parent.' -> <strong>'.$name.'</strong> was found.',
            'alert_related_id' => $newcat,
            'alert_related_parent' => $parent_id,
            'alert_actioned' => 0,
            'alert_ignored' => 0
            )
        );
    } //foreach new category

        
        //get all visible products from WooCommerce, 
        $args = array(
            'visibility' => 'catalog',
        );
        $products = wc_get_products( $args );
        $jsonst_products = grab_json_string();

        //look for the sku in the json file
        foreach ($products as $product) {
            $sku = $product->get_sku();
            $id_product = $product->get_id();

            $term_list = wp_get_post_terms($id_product,'product_cat',array('fields'=>'names'));
            $cat_prod = implode($term_list);
           // $cat_id = (int)$term_list[0];

        //    if( has_term( 42, 'product_cat' ) ) {
        //         // do something if current product in the loop is in product category with ID 42 (SPRING)
        //         continue;
        //     }
            
            //search for $sku in .json file
            $sku_search =  '"variant":"'.$sku.'"';
            if (strpos($jsonst_products, $sku_search) == FALSE) { //if not found, create alert
               
                    $wpdb->insert('wp_cf_products_alerts', 
                    array(
                        'alert_type' => 'product_removed',
                        'alert_narrative' => 'Product with code <strong>'.$sku.'</strong> in '.$cat_prod.' has been removed.',
                        'alert_related_id' => $sku,
                        'alert_wc_cat' => $cat_prod,
                        'alert_actioned' => 0,
                        'alert_ignored' => 0
                        )
                    );
                
            }
        } 

        reverse_scan();
    
}


//reverse_scan gets all WooCommerce products and checks that they are still listed in today's .json. If not, it creates an alert.
function reverse_scan() {
    global $wpdb;
            //get all visible products from WooCommerce, 
            $args = array(
                'visibility' => 'catalog',
                'limit' => -1,
            );
            $products = wc_get_products( $args );
            $jsonst_products = grab_json_string();
           
            //look for the sku in the json file
            foreach ($products as $product) {
                $sku = $product->get_sku();
                $id_product = $product->get_id();
                $term_list = wp_get_post_terms($id_product,'product_cat',array('fields'=>'names'));
                $term_list_ids = wp_get_post_terms($id_product,'product_cat',array('fields'=>'ids'));
                $cat_prod = implode('-> ',$term_list);

                //int_r($term_list);
                //print_r($term_list_ids);
                //NOTE: !== FALSE means there is a match in the string
                
                if (($term_list_ids[0] == 42)||($term_list_ids[1] == 42)) {
                    continue;
                } 


                //if product is not a DHG product, skip or is in SPRING (WC) category
                if (strpos($sku, 'ff-') !== FALSE) {
                    echo 'skipping '.$sku.'</br>'; //if found, then skip
                    continue;
                }

                $string = ':"'.$sku.'"';
                //Now search for string
                if (strpos($jsonst_products, $string) !== FALSE) {
                    continue;
                }


                echo $string.' was not found</br>';
                
              
                //f not found, create alert
                
                    $wpdb->insert('wp_cf_products_alerts', 
                    array(
                        'alert_type' => 'product_removed',
                        'alert_narrative' => 'Product with code <strong>'.$sku.'</strong> in '.$cat_prod.' has been removed.',
                        'alert_related_id' => $sku,
                        'alert_wc_cat' => $cat_prod,
                        'alert_actioned' => 0,
                        'alert_ignored' => 0
                        )
                    );
           
                 } //foreach
}


function scan_for_new_subcategories($subcat_id) {
    global $wpdb, $newcats;

    //check if subcategory of this 
    $sql = "SELECT * FROM wp_category_lookup WHERE dhg_id=$subcat_id";
    $results=$wpdb->get_results($sql);
    
    //if not found in category lookup, then subcategory is new
    if (count($results)== 0){

        //if new category, look for it in the alerts table too. If its not there, add to array.
       $sql = "SELECT * FROM wp_cf_products_alerts WHERE alert_type='new_subcategory' AND alert_related_id = $subcat_id";
       $alerts=$wpdb->get_results($sql);
       if (count($alerts)== 0){
        $newcats[] = $subcat_id; 
       }

    } 

}


//this is to hide all variable products with no variations (that appear without a price on the site).
//currently not included in cron
add_shortcode( 'test-vars', 'clean_variable_products_with_no_variations' );

// function test_if_running () {
//     echo 'THIS IS RUNNING';
// }

function clean_variable_products_with_no_variations () {

    $args = array(
        'type' => 'variable',
        'limit' => -1,
    );
    
    $var_products = wc_get_products( $args );
    //var_dump ($var_products);

    foreach ($var_products as $var_product) {

        $id_product = $var_product->get_id();
       
        $variations = $var_product->get_children();
        

        echo $id_product.' is variable with '.count( $variations ).' variations</br>';

        if( count( $variations ) == 0 ) {
            echo 'Hiding: '.$id_product.'</br>';
            //   hide this var_product
           $var_product->set_catalog_visibility('hidden');
           $var_product->save();
           wc_delete_product_transients( $id_product );
        }

    }
}

add_shortcode('fix-vars', 'fix_stock_of_variables');
function fix_stock_of_variables () {
global $wpdb;
// $args = array(
//         'type' => 'variable',
//         'limit' => -1,
//     );
    
//     $var_products = wc_get_products( $args );
    //var_dump ($var_products);

    $dhg_items = grab_products_json();



    //var_dump($dhg_items);
    $x=0;
     foreach ($dhg_items as $item) {
        
      
        //if code is not equal to variant, then item is a variant
        if (($item['code'] != $item['variant'])&&($item['quantity'] != 0)&&($item['categories'][0]['id'] == 1)) {
  
            $pid = wc_get_product_id_by_sku($item['variant']);
           
             //get the wc product to edit it
            $wcproduct = wc_get_product( $pid );
           
            //update the stock and manage stock
            if ($pid != 0) {
                $wcproduct->set_stock_quantity( $item['quantity'] );
                $wcproduct->set_manage_stock( true);
                $wcproduct->save();
            }
          
            wc_delete_product_transients( $pid );

           //get its parent and set manage_stock to false
            $parent_id = wc_get_product_id_by_sku($item['code']);
            if ($parent_id != 0) {
            $wcproduct = wc_get_product( $parent_id );
            $wcproduct->set_manage_stock( false);
            $wcproduct->save();
            wc_delete_product_transients( $parent_id );
}
         }



     }

    //     // get id of parent
    //     $id_main_product = $var_product->get_id();

    //     //get variations
    //     $variations = $var_product->get_children();

    //     // //foreach variation get stock and save
    //     // var_dump($variations);
    //     // foreach ($variations as $variation) {
    //     //     //get SKU from product id
    //     //     $sku = $product->get_sku();

    //     //     //find stock in JSON file
    //     //     // if (strpos($jsonst_products, $sku_search) == FALSE) { //if not found, create alert
               
    //     //     //         $wpdb->insert('wp_cf_products_alerts', 
    //     //     //         array(
    //     //     //             'alert_type' => 'product_removed',
    //     //     //             'alert_narrative' => 'Product with code <strong>'.$sku.'</strong> in '.$cat_prod.' has been removed.',
    //     //     //             'alert_related_id' => $sku,
    //     //     //             'alert_wc_cat' => $cat_prod,
    //     //     //             'alert_actioned' => 0,
    //     //     //             'alert_ignored' => 0
    //     //     //             )
    //     //     //         );
                
    //     //     // }


    //     //     //update it and set manage_stock to true (if 0)

    //     // }
       

    // }
    //manage_stock of main = false
}