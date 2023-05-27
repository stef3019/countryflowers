<?php
//get the json file
function insert_dhg_products() {
    global $wpdb;

    $json_products = grab_products_json ();

    foreach ($json_products as $key => $selected_product) { //start iterating the products for this particular sku
        $code = $selected_product['code'];
        //echo $key.': '.$code.' CAT: '.$selected_product['categories']['id'].'</br>';

        if (sizeof($selected_product['categories']) > 0)  {

            //alter price correctly to 3.5 multiplier
            // if ($selected_product['code'] != 46244 )
            // {
            //     continue;
            // }
            // echo $selected_product['price'].'</br>';

            $clean = str_replace(',', '.', $selected_product['price']);
            $orig =  ($clean)/3;
            $cost =  $orig*3.5;
            $round_num = round($cost / 0.05) * 0.05;
            $cf_price = number_format($round_num, 2);

//             echo $clean.' '.$orig.' '.$cost.' '.$round_num.' '.$cf_price;
// break;
            // var_dump($selected_product);
            $code = $selected_product['code'];
            $variant = $selected_product['variant'];
            $image = $selected_product['Image'];
            $name = trim($selected_product['name']);
            $color = trim($selected_product['color']);
            $price = $cf_price;
            $category = $selected_product['categories'][0]['id'];
            $subcategory = $selected_product['subCategories'][0]['id'];
            $datecreated = date('Y-m-d H:i:s');


        $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM `wp_dhg_product_dump` WHERE `variant` = '$variant' ");
         
        if ($rowcount == 0) {

            $query =  $wpdb->insert('wp_dhg_product_dump', 
            array(
                'code' => $code, 
                'variant' => $variant,
                'image' => $image,
                'name' => $name,
                'color' => $color,
                'price' => $price,
                'category' => $category,
                'subcategory' => $subcategory,
                'datecreated' => $datecreated
            ));
        }
        


             echo '</br>'.$wpdb->last_error;
        //        var_dump($rowcount);
         //break;
        }
       
    }

}



function create_new_products_from_db () {
    
    global $wpdb;

    $categories = array();

    $dhg_cats[0] = $_POST['cat'];
    $dhg_cats[1] = $_POST['subcat'];

    // var_dump($dhg_cats);
    // exit;
    //get the proper ids from the mapping table
    foreach($dhg_cats as $cat) {
        $categories[] = $wpdb->get_var( "SELECT `wc_id` FROM `wp_category_lookup` WHERE `dhg_id` = $cat");
    }
 
    // var_dump($categories);
    // exit;

    //grab the rows of such a subcategory

     $selected = $wpdb->get_results( "SELECT * FROM `wp_dhg_product_dump` WHERE `subcategory` = $dhg_cats[1]", ARRAY_A );

    //$selected = $wpdb->get_results( "SELECT * FROM `wp_dhg_product_dump` WHERE `code` = 53089", ARRAY_A ); 

    if ( $wpdb->last_error ) {
      echo 'wpdb error: ' . $wpdb->last_error;
    }

    foreach ($selected as $count => $product) { 
        
            //look for it in json_p and grab the product. ['product_info'] (array)
            $items[$count]['product'] = $product;

            //save its type ['item_type'] (string)
            if (($product['code'] != $product['variant'])) {

                //double check for single occurance of code with different variant. These are still simple products with no variations
                $c = $product['code'];
                $codecount = $wpdb->get_var("SELECT COUNT(*) FROM `wp_dhg_product_dump` WHERE `code` = '$c' ");
                
                if ($codecount == 1) {
                    $items[$count]['sku']  = $product['code'];
                    $items[$count]['item_type'] = 'simple';
                    continue;
                } 

                

            //	echo 'VARIABLE MATCH';
                $items[$count]['item_type'] = 'variable';
                $items[$count]['sku']  = $product['code'];
                //create a group number count ['sku'] (int) = product SKU
           

            } else {
                $items[$count]['sku']  = $product['code'];
                $items[$count]['item_type'] = 'simple';
            }

            
            //flag as assorted ['assorted'] (bool)
            echo '<br>'.$product['code'].'-'.preg_replace('/\s/', '',  $product['color']);  
      
    }
 // var_dump($items);
  filter_database_items($items, $categories);
  echo 'done';
  exit;
}   

function filter_database_items ($items, $cats) {
    
    //iterate array of selected items and determine what to do with them.
    $SKUarray = array();

    $key = 0;
    print("<pre>".print_r($cats,true)."</pre>");

    foreach ($items as $item) {
        //foreach item, 3 options:
        //simple product, new variable product, variation if existing product
    

        //SIMPLE
        if ($item['item_type'] == 'simple') {
            add_selected_simple_products ($item['product'], $cats, 'simple');


        //VARIABLE
        } elseif ($item['item_type'] == 'variable') {
            $args['manage_stock'] = false;

            //IF PARENT EXISTS, JUST CREATE VARIATION
            if (in_array ($item['sku'], $SKUarray)) {
               echo 'Creating Variation </br>';
                //get parent ID and send to create variant     
                echo 'Variant: '.$item['product']['variant'].'</br>';


                $parent_id = wc_get_product_id_by_sku($item['sku']);
                $variation_data =  array(
                    'attributes' => array(
                        'colour' => ucwords(strtolower($item['product']['color'])),
                    ),
                    'sku' => $item['product']['variant'],
                    'regular_price' => $item['product']['price'],
                    'manage_stock' => false,
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
                    echo 'Creating New Variable </br>';
                   
                   //$var_price = number_format(floatval(str_replace(',', '.', str_replace('.', '', $item['product']['price']))),2);
                    echo '</br>Price: '.$item['product']['price'].'</br>';
                    $variation_data =  array(
                        'attributes' => array(
                            'colour' => $item['product']['color'],
                        ),
                        'sku' => $item['product']['variant'],
                       'regular_price' => $var_price,
                        'manage_stock'     => false,
                    );
                   create_product_variation( $parent_id, $variation_data );

            } //if new or existing variable
        } //if simple or variable
    } //end foreach
} //end function
