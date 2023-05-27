<?php
/*
1. build_category_selector          (function to select with category to see)
2. create_product_category_table	(rendering the product list from json into a table to select and import)
3. get_categories_checkbox_list      (create checkbox heirarchy from WC categories)
4. get_categories_checkbox_list 
*/

//built the HTML selects with the categories and subcatebories when needed in forms. 
function build_category_selector ($cat_links,  $subcat_links = null, $submit = false) {
	/**
	 * The form to be loaded on the plugin's admin page
	 */

	if( current_user_can( 'edit_users' ) ) {

	// Populate the dropdown list with exising users.
	$dropdown_html = '<select id="parent_category" name="cat">
			    <option value="">Select a Category</option>';
	//$wp_users = get_users( array( 'fields' => array( 'user_login', 'display_name' ) ) );		


	foreach ($cat_links as $cat_option) {
		$dropdown_html .= '<option value="'.$cat_option['id'].'">'.ucfirst($cat_option['name']).' ('.$cat_option['id'].')</option>' . "\n";
	}
    $dropdown_html .= '</select>';
    
    if (!is_null($subcat_links)) {
        // Populate the dropdown list with exising users.
	$dropdown_html .= '<select name="subcat" id="sub_category">
                 <option value="">Select a Sub Category</option>';

        foreach ($subcat_links as $key => $subcat_option) {
            	$dropdown_html .= '<option data-parent="'.$subcat_option['parent'].'" value="'.$subcat_option['id'].'">'.ucfirst($subcat_option['name']).' ('.$subcat_option['id'].')</option>' . "\n";
        }
        $dropdown_html .= '</select>';
    }

	// Generate a custom nonce value. 
	$nds_add_meta_nonce = wp_create_nonce( 'category_selector_form_nonce' ); 

	// Build the Form
    ?>				
       	
        <div class="category_selector_form">

        <?php if ($submit == true ) { ?>
            <form action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" method="get" id="category_selector_form" >			

                <?php echo $dropdown_html; ?>
                <!-- <input type="hidden" name="action" value="nds_form_response"> -->
                <input type="hidden" name="page" value="<?php echo $_GET['page'] ?>" />
                <input type="hidden" name="tab" value="<?php echo $_GET['tab'] ?>" />			
                <p class="submit"><input type="submit" id="submit" class="button button-primary" value="Select"></p>

            </form>
            <br/><br/>
            <div id="nds_form_feedback"></div>
            <br/><br/>	
        <?php } else {?>
            <?php echo $dropdown_html; ?>
            <?php } ?>
 
        </div>
    <?php    
    }
    else {  
    ?>
        <p> <?php __("You are not authorized to perform this operation.", $this->plugin_name) ?> </p>
    <?php   
    }

}

//build the table that lists all products in category. Used in List DHG Products tab
function create_product_category_table ($cat, $products, $action = null, $subcat) {
    global $wpdb;
	if( current_user_can( 'edit_users' ) ) {
            //get categories to choose from 
            $args = array(
                'taxonomy'   => "product_cat",
                'hide_empty' => false
            );

            //get corresponding cat and subcat

            $wc_cat = $wpdb->get_var( 'SELECT `wc_id` FROM `wp_category_lookup` WHERE dhg_id = '.$cat); 
            $wc_subcat = $wpdb->get_var( 'SELECT `wc_id` FROM `wp_category_lookup` WHERE dhg_id = '.$subcat); 


            $iterate_results = '<form action="'.esc_url( admin_url( 'admin-post.php' ) ).'" method="post" id="product_table_form">';

            $iterate_results .= '<input type="hidden" name="action" value="selected_products">';

            $iterate_results .= '<input type="hidden" name="category" value="'.$wc_cat.'">';
            $iterate_results .= '<input type="hidden" name="subcategory" value="'.$wc_subcat.'">';

            $iterate_results .= '<table class="widefat fixed wp-list-table striped posts" cellspacing="0"><thead> 
                <th id="cb" class="manage-column column-cb check-column" scope="col"><input type="checkbox" id="checkAll"></th>
                <th>ID</th>
                <th>Code</th>
                <th>Var</th>
                <th>Listed?</th>
                <th>Name</th>
                <th>Sub Category</th>
                <th>Colour</th>
                <th>Pic</th>
                <th>Individual Selling Price</th>
                <th>Stock</th>
                </thead><tbody>';
            //get products of said category
            foreach ($products as $product) {
                $pid = wc_get_product_id_by_sku($product['variant']);
                if (!empty($pid)) {
                    $new = 'LISTED';
                    $wcproduct = wc_get_product( $pid );
                    $stock = $wcproduct->get_stock_quantity();
                    $difference = $stock-$product['meta']['Disponibilità'];
                    $check = '';
                } else {
                    $new = 'new';
                    $stock = '';
                    $difference = 0;
                    $check = '<input type="checkbox" id="product'.$product['id'].'" name="cf_to_add[]" value="'.$product['id'].'">';
                }

                if ($difference == 0) {
                    $trstyle = '';
                } else {
                    $trstyle = 'style="color:red"';
                }


                if (($product['categories'][0]['id'] == $cat) && ($product['subCategories'][0]['id']) == $subcat) {
                    $iterate_results .= '<tr class="iedit author-self type-product status-publish has-post-thumbnail hentry product_cat-spring">
                            <td>'.$check.'</td>
                            <td>'.$product['id'].' </td>
                            <td>'.$product['code'].' </td>
                            <td>'.$product['variant'].' </td>
                            <td '.$trstyle.'>'.$new.'('.$stock.') </td>
                            <td>'.$product['name'].'</td>
                            <td>'.$product['subCategories'][0]['name'].'</td>
                            <td>'.$product['color'].' </td>
                            <td></td>
                            <td>'.number_format(floatval(str_replace(',', '.', str_replace('.', '', $product['price']))),2).' </td> 
                            <td>'.$product['meta']['Disponibilità'].' </td>
                            </tr>';
                }
            }
            $iterate_results .= '</tbody></table><p class="submit"><input type="submit" id="submit" class="button button-primary" value="Import Selected Products"></form></p>';
            return $iterate_results;	
    
    } else {  
            echo "<p>You are not authorized to perform this operation.</p>" ;
     
    }

}


// function get_categories_checkbox_list ()
// {
// 	$checkbox_list = '';
	
// 	$taxonomy     = 'product_cat';
// 	$orderby      = 'name';  
// 	$show_count   = 0;      // 1 for yes, 0 for no
// 	$pad_counts   = 0;      // 1 for yes, 0 for no
// 	$hierarchical = 1;      // 1 for yes, 0 for no  
// 	$title        = '';  
// 	$empty        = 0;		 // 1 for yes, 0 for no  
  
// 	$args = array(
// 		   'taxonomy'     => $taxonomy,
// 		   'orderby'      => $orderby,
// 		   'show_count'   => $show_count,
// 		   'pad_counts'   => $pad_counts,
// 		   'hierarchical' => $hierarchical,
// 		   'title_li'     => $title,
// 		   'hide_empty'   => $empty
// 	);
//    $all_categories = get_categories( $args );
//    foreach ($all_categories as $cat) {
// 	  // print_r($cat);
// 	  if($cat->category_parent == 0) {
// 		  $category_id = $cat->term_id;       
// 		 // echo '<br /><a href="'. get_term_link($cat->slug, 'product_cat') .'">'. $cat->name .'</a>'; 
// 		 $checkbox_list .= '<br /><input type="checkbox" value="'. $cat->term_id .'" name="import_cat[]">'. $cat->name .'</input>'; 
  
// 		  $args2 = array(
// 				  'taxonomy'     => $taxonomy,
// 				  'child_of'     => 0,
// 				  'parent'       => $category_id,
// 				  'orderby'      => $orderby,
// 				  'show_count'   => $show_count,
// 				  'pad_counts'   => $pad_counts,
// 				  'hierarchical' => $hierarchical,
// 				  'title_li'     => $title,
// 				  'hide_empty'   => $empty
// 		  );
// 		  $sub_cats = get_categories( $args2 );
// 		  if($sub_cats) {
// 			  foreach($sub_cats as $sub_category) {
// 				 // echo  $sub_category->name ;
// 				 $checkbox_list .=  '<br /><input style="margin-left:10px" type="checkbox" value="'. $sub_category->term_id .'" name="import_cat[]">'. $sub_category->name .'</input>'; 
// 			  }   
// 		  }
// 	  }       
//   }

//   return $checkbox_list;
// }

//USED in Mapping Tab to render form
function render_new_category_form() {
    global $wpdb;
   
    
    
    if (isset($_POST['english_name'])) {
        
        $dhg_id = $_POST['dhg_id'];
        $dhg_parent_id = $_POST['dhg_parent_id'];
        $subcatname =  $_POST['english_name'];

        //check if it already exists in  wp_category_lookup
        $sql = "SELECT COUNT(*) as cat FROM wp_category_lookup WHERE dhg_id=$dhg_id";
        $results=$wpdb->get_results($sql);
        //print("<pre>".print_r($results,true)."</pre>");
      
        if ($results[0]->cat == 0) {
            //if doesnt exist, add it.
               
            //GET Parent woocommerce ID
            $sql = "SELECT wc_id as parent FROM wp_category_lookup WHERE dhg_id=$dhg_parent_id";
            $results=$wpdb->get_results($sql);


            //Create wc_term
            $newcat = create_new_product_subcategory($results[0]->parent, $subcatname, $dhg_id, $dhg_parent_id, $alert_id);
           // print("<pre>".print_r($newcat,true)."</pre>");

        }  
        
    } else {
        $newcatid = $_GET['newcat'];
        $ids = explode('_', $newcatid);
        $alertid = $_GET['alertid'];
        $catnames = $_GET['catnames'];

        $sql = "SELECT * FROM wp_category_lookup";
        $results=$wpdb->get_results($sql);

      

        $table_html = '<h3>DGH-WC Category Lookup Table</h3><table id="category_lookup" class="widefat fixed wp-list-table striped posts" cellspacing="0">
        <thead><th>Label</th><th>Status</th><th>WC ID</th><th>DHG ID</th><th>Parent</th></thead><tbody>';

        foreach ($results as $lookup) {
            $table_html .= '<tr>';
            $table_html .= '<td>'.$lookup->english_name.'</td>';
            $table_html .= '<td>'.$lookup->currently_active.'</td>';
            $table_html .= '<td>'.$lookup->wc_id.'</td>';
            $table_html .= '<td>'.$lookup->dhg_id.'</td>';
            $table_html .= '<td>'.$lookup->dhg_parent_id.'</td>';
            $table_html .= '</tr>';
        }
        $table_html .= '</tbody></table>';

       
            $form_html = '<h3>Create new shop category for: '.$catnames .'</h3>';
            $form_html .= "<form id='new_cat' method='post' style='margin-bottom:10px;'>
                        <div style='margin-bottom:10px'>Where to get data: DHG from json file. Uses 'English Name' to create a WooCommerce Product Category</div>
                        <label for='english_name' style='margin-right:5px;'> English Name: </label><input type='text' name='english_name' style='margin-right:8px;' />
                        <label for='dhg_parent_id' style='margin-right:5px;'> DHG Parent: </label><input type='text' name='dhg_parent_id' value='' style='margin-right:8px;' />
                        <label for='dhg_id' style='margin-right:5px;'> DHG Cat ID: </label><input type='text' name='dhg_id' value='' style='margin-right:8px;' />
                        <input type='hidden' name='currently_active' value='1' />
                        <input type='submit' value='Create New Category' />
                        </form>";
        

       
        return  $form_html.$table_html;
    }  
}

//USed to render CSV Operations page
function csv_amend_page() {
    if (isset($_SESSION['message']) && $_SESSION['message'])
    {
      printf('<b>%s</b>', $_SESSION['message']);
      unset($_SESSION['message']);
    }
    echo '<h1>Bulk Process Prodcts via CSV</h1>';
    
    echo '<p>Upload a .csv file with a single column of product codes (variant DHG id) to be deleted from the website.</p>
            <form action="'.esc_url( admin_url( 'admin-post.php' ) ).'" method="post" id="amendableProducts"  enctype="multipart/form-data">
            <input type="hidden" name="action" value="amend_products">
            <select id="amend_action" name="amend_action" required>
                <option value="" disabled selected>--select action--</option>    
                <option value="import">Import these products with x3.5 multiplier</option>
                <option value="delete">Delete these products</option>
                <option value="stock">Update stock of these products</option>
                <option value="price">Update price of these produts</option>
            </select>';
    echo '<div style="margin:15px 0 10px 0;">if applicable, select category';
    echo get_available_categories(false);

    echo '</div><input type="file" name="amendableProducts" id="amendableProducts">
            <input type="submit" name="uploadBtn" value="Upload" />
        </form>';	
        
        echo '<p> Columns for import: Variation ID, Title, Price </p>';
}

add_action( 'admin_post_amend_products', 'process_csv_products' );
add_action( 'admin_post_nopriv_amend_products', 'process_csv_products' );