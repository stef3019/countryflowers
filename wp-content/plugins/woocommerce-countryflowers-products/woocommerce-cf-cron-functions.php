<?php

/***********************************************************************/
//THIS IS NOT USED woocommerce-cf-TEST-cron-functions.php is used instead
/***********************************************************************/





//used this hook to create cron job in cron plugin
//add_action( 'cf_file_checker', 'check_products_file' );

// add_shortcode( 'nothing', 'test_this_cf' );

// function test_this_cf () {
//     echo 'test';
// }

// function check_products_file () {
  
//     //look for today's .json file
//     $filename = 'raw_products'.date('dmy').'.json';
//     chdir($_SERVER['DOCUMENT_ROOT'] .'/wp-content/uploads/imported_products');

//     //if the file is 
//     if ( file_exists( $filename ) ) {
//             $file_time = filemtime( $filename );
//             $expire_time = $file_time < ( time() - $expire);
//             $expire = 86400; // Time in seconds to cache the file for (1 day)
        
//             if ( $file_time < ( time() - $expire ) ) {
//             // if expired, overwrite file
//             write_json_to_file( $filename );
//             $json = file_get_contents( 'https://dhgdoo.eu/writable/export/products.json');
//             file_put_contents( $filename, $json );
//             }

//     // if file does not exist, write to file
//     } else {
//         write_json_to_file( $filename );
//         $json = file_get_contents( 'https://dhgdoo.eu/writable/export/products.json');
//         file_put_contents( $filename, $json );
//     }
        
//     //scan_products_for_changes();
        
//     //header('Content-Type: application/json');
//     $file_data = file_get_contents( $filename );  //? not sure why this is here...
//     wp_die();                                                                                                                                                                                                                                                
// }



// //helper function to write the products to a .json file for quicker reference
// function write_json_to_file( $filename ) {
//     $json = file_get_contents( 'https://dhgdoo.eu/writable/export/products.json');
//     $time = time();
//     file_put_contents( $filename, $json );
// }


// /* SCAN FOR CHANGES */
// // Scan for new categories
// // 1. check file for categories, and create old_cat array.
// // 2. scan new file for categories and compare to old_cat array.


// // Scan for new products in given categories
// // 1. Foreach old category, check if each available product exists in WC. 
// // 2. If new ones are found, report them and add them


// // Scan for changes in stock levels in existing products
// // 1. Foreach WC product, look for it in the json file and compare stock level
// // 2. If stock level is different but not zero, update it
// // 3. If new stock level is 0, create an Alert




// function scan_products_for_changes() {
					
//     global $wpdb;
//     $json_p = grab_products_json ();
//     $email_message = '';

//     //echo 'Total: '.count($json_p).'</br>';
    
//     //foreach product, compare its SKU with a current product/variation
//     //Produts from DHG Json = "item". Woocommerce products = "product"
//     foreach ($json_p as $key => $item) {

//         //Products with known issues - THIS HAS TO BECOME A BLACKLIST TABLE IN DB
//         if ($item['variant']=='601') {
//             continue;
//         }

//         if ($item['variant']=='604') {
//             continue;
//         }

//         //get the var SKU
//         $pid = wc_get_product_id_by_sku($item['variant']);
//         //in the json file, the 'variant' field is what becomes the SKU in WooCommerce

//         if ($pid != 0) {

//             $wcproduct = wc_get_product( $pid );
//             $type = $wcproduct->product_type;

//             switch ($type) {
//                 case 'simple':
//                     //get current stock and compare to json stock of this item
//                     $stock = $wcproduct->get_stock_quantity();
//                     $difference = $stock-$item['meta']['Disponibilità'];
                    
//                     if ($difference != 0) {
//                         //if difference between old and new stock is not 0, update to new stock level.
//                         $wcproduct->set_stock_quantity( $item['meta']['Disponibilità'] );
//                         $product_id = $wcproduct->save();

//                         //add to log table
//                         $wpdb->insert('wp_product_changes', 
//                         array(
//                             'wc_prod_id' => $pid, 
//                             'dhg_id' => $item['code'],
//                             'column_changed' => 'stock',
//                             'wc_prod_type' => 'simple',
//                             'old_value' => $stock,
//                             'new_value' => $item['meta']['Disponibilità']
//                             )
//                         );
                        
//                         $email_message .= '<p>Simple Item '.$item['code'].' stock level was changed from '.$stock.' to '.$item['meta']['Disponibilità'].'</p>';
//                     }

//                     break;


//                 case 'variation':
//                     $stock = $wcproduct->get_stock_quantity();
//                     $difference = $stock-$item['meta']['Disponibilità'];

//                     //if in stock (so difference is > 0)
//                     if ($difference != 0) {
//                         $wcproduct->set_stock_quantity( $item['meta']['Disponibilità'] );

//                         if ( $item['meta']['Disponibilità'] > 0 ) {
//                             $wcproduct->set_stock_status('instock'); 
//                         } else {
//                             $wcproduct->set_stock_status('outofstock'); 
//                         }
                      
//                         $product_id = $wcproduct->save();

//                          //set stock status for parent too
//                          $parent_id = $wcproduct->get_parent_id($pid);
                         
//                          $parent_product = wc_get_product( $parent_id );
//                          $parent_product->set_stock_status('instock');
//                          $parent_product->set_stock_quantity(100);
//                          $parent_id = $parent_product->save();

//                         $wpdb->insert('wp_product_changes', 
//                         array(
//                             'wc_prod_id' => $pid, 
//                             'dhg_id' => $item['variant'],
//                             'column_changed' => 'stock',
//                             'wc_prod_type' => 'variant',
//                             'old_value' => $stock,
//                             'new_value' => $item['meta']['Disponibilità']
//                             )
//                         );

//                         $email_message .= '<p>Item Variant'.$item['variant'].' stock level was changed from '.$stock.' to '.$item['meta']['Disponibilità'].'</p>';
//                     }

//                     break;

//                 case 'variable':
//                     $stock = $wcproduct->get_stock_quantity();
//                     $difference = $stock-$item['meta']['Disponibilità'];

//                     if ($difference != 0) {
                        
//                         //set the general stock level to new one
//                         $wcproduct->set_stock_quantity( $item['meta']['Disponibilità'] );
//                         $product_id = $wcproduct->save();

//                         if ($stock != 0) {
//                             $wcproduct->set_stock_status('instock'); 
//                             $prod = $wcproduct->save();
//                         }


                       

//                         $wpdb->insert('wp_product_changes', 
//                         array(
//                             'wc_prod_id' => $pid, 
//                             'dhg_id' => $item['code'],
//                             'column_changed' => 'stock',
//                             'wc_prod_type' => 'variable',
//                             'old_value' => $stock,
//                             'new_value' => $item['meta']['Disponibilità']
//                             )
//                         );
                    

//                         //get all variants. If they contain a dash, then update all variants
//                         $handle=new WC_Product_Variable($pid);
//                         $variations=$handle->get_children();

//                         foreach ($variations as $value) {
//                             $single_variation=new WC_Product_Variation($value);
//                             //print("<pre>".print_r($single_variation,true)."</pre>");
//                             $varsku = $single_variation->sku;
                            
//                             $varpid = wc_get_product_id_by_sku($varsku);
//                             $wcvariant = wc_get_product($varpid);
                            
//                             $wcvariant->set_stock_quantity( $item['meta']['Disponibilità'] );
//                             $wcvariant->save();

//                         }

//                         $email_message .= '<p>Variable Item'.$item['code'].' stock level was changed from '.$stock.' to '.$item['meta']['Disponibilità'].'</p>';
//                     }

//                     break;
                
                
//                 default:
                    
//                     break;
//             }
//         }	
//     }

//     $email_message .= ' </br>The time now is: '.date();
//     //send report by email 
//     $to = 'stefcordina@gmail.com';
//     $subject = 'Country Flowers Website Stock Level Changes';
//     $body = $email_message;
//     $headers = array('Content-Type: text/html; charset=UTF-8');
//  //wp_mail( $to, $subject, $body, $headers );


    
// }