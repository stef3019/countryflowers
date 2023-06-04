<?php 
function cf_insert_dhg_products() {
    global $wpdb;
    
    if (isset($_POST['wc_product_add_category_products'])) {
        $category_slug = sanitize_text_field($_POST['wc_product_add_category_products']);
        
        $category = get_term_by('slug', $category_slug, 'product_cat');

        $wc_cat_id = $category->term_id;
       
        //Get Corresponding DHG category from wp_category_lookup table. (Assumes they are correct)
        $dhg_subcat = $wpdb->get_var( 'SELECT `dhg_id` FROM `wp_category_lookup` WHERE `wc_id` = '.$wc_cat_id); 
        $dhg_parent = $wpdb->get_var( 'SELECT `dhg_parent_id` FROM `wp_category_lookup` WHERE `wc_id` = '.$wc_cat_id);

        $status = $_POST['wc_cat_product_status'];

        cf_delete_prev_cat_in_dump_table($dhg_subcat);

        $json_products = cf_grab_products_json ();

        foreach ($json_products as $key => $selected_product) {    

            $code = $selected_product['code'];

            if((!isset($selected_product['categories']))||(empty($selected_product['categories']))) {
                continue;
            }
                if ($selected_product['subCategories'][0]['id'] == $dhg_subcat)  {
                    $clean = str_replace(',', '.', $selected_product['price']);
                    $orig =  ($clean)/3;
                    $cost =  $orig*3.5;
                    $round_num = round($cost / 0.05) * 0.05;
                    $cf_price = number_format($round_num, 2);

                    $code = $selected_product['code'];
                    $variant = $selected_product['variant'];
                    $image = $selected_product['Image'];
                    $name = trim($selected_product['name']);
                    $color = trim($selected_product['color']);
                    $price = $cf_price;
                    $category = $selected_product['categories'][0]['id'];
                    $subcategory = $selected_product['subCategories'][0]['id'];
                    $datecreated = date('Y-m-d H:i:s');

                    if ($selected_product['code'] != $selected_product['variant']) {
                        //double check for single occurance of code with different variant. These are still simple products with no variations
                        $type = 'variant';
                    } else {
                        $type = 'simple';
                    }

                $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM `wp_dhg_product_dump` WHERE `variant` = '$variant' AND `subcategory` = $dhg_subcat");
                
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
                        'type' => $type,
                        'subcategory' => $subcategory,
                        'datecreated' => $datecreated
                    ));
                }
                //   echo '</br>'.$wpdb->last_query;
                //   echo '</br>'.$wpdb->last_error;
            } //inner if

        } //foreach

        //////////////////// ----- SIMPLE ------//////////////////////


        //First Create Simple Items
        $selected = $wpdb->get_results( "SELECT * FROM `wp_dhg_product_dump` WHERE `subcategory` = $dhg_subcat AND `type` = 'simple' ", ARRAY_A );

        if ( $wpdb->last_error ) {
            echo 'wpdb error: ';
            echo '<pre>';
            var_dump($wpdb->last_error);
            echo '</pre>';
          }

        //remove non-viable products and add some extra WC stuff
        $viable = cf_remove_non_viable_products($selected);
        if (!empty($viable)) {
            $simple = add_wc_stuff_to_product($viable);
            //Testing: comment this line to supress actual product creation
            cf_create_simple_products($simple, $wc_cat_id, $status); 
        }


        //////////////////// ----- VARIABLES ------//////////////////////


        // get distinct codes that are type variant. For each code, create the variable product and its variants
       $codes = $wpdb->get_results( "SELECT DISTINCT `code` FROM `wp_dhg_product_dump` WHERE `type` = 'variant' AND `subcategory` = $dhg_subcat", ARRAY_A );
        $count_vars = count($codes);
       //get each product as a group with its variants (so 3 variants = 3 rows)
       foreach ($codes as $key => $group) {

            //for testing, uncomment
            // if ($key == 3) {
            //     break;
            // }

            //create the variable and its variants
            $group_code = $group['code'];
            //get the actual variant codes

            //if product already exists, skip it. 
            $product_id = wc_get_product_id_by_sku($group_code);
            if ($product_id) {
                echo '<p>Skipping '.$group_code.', it exists.</p>';
                continue;
            }

            $selected_vars = $wpdb->get_results( "SELECT DISTINCT * FROM `wp_dhg_product_dump` WHERE `code` = $group_code AND `subcategory` = $dhg_subcat", ARRAY_A );
             echo '--------------</br><strong>'.count($selected_vars).' items for product with code '.$group_code.'<strong></br>';
            

            if ( $wpdb->last_error ) {
                echo 'wpdb error: ';
                echo '<pre>';
                var_dump($wpdb->last_error);
                echo '</pre>';
            }

            $viable = cf_remove_non_viable_products($selected_vars);
            
            if (!empty($viable)) {
                $vars = add_wc_stuff_to_product($viable);
                 cf_create_variables_and_variants ($vars, $wc_cat_id, $status);
            } 
        }
    } //if isset
} //function

require 'cf-create-products.php';
