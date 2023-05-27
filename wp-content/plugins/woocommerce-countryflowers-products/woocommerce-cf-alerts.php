<?php 
//This function needs to change to reflect new alerts table. Must have date selected before rendering. Or show today's by default.. haven't decided. 
function get_cf_product_changes() {
    global $wpdb;


   //INSERT IGNOR NEW ALL IN CATEGORY AND JUST SHOW COUNT OF IGNORED BUT UNACTIONED.
   $ignored_query = "SELECT COUNT(*) as ignored FROM wp_cf_products_alerts WHERE alert_actioned=0 AND alert_ignored=1";
   $ignored_count=$wpdb->get_results($ignored_query);

   echo $ignored_count[0]->ignored.' items ignored';
   //
    
    $alerts_query = "SELECT * FROM wp_cf_products_alerts WHERE alert_actioned=0 AND alert_ignored=0";
    $alerts=$wpdb->get_results($alerts_query);
    $table = wp_nonce_field( 'cf-alerts' );
    $table .= '<table class="widefat fixed wp-list-table striped posts" cellspacing="0" id="alerts_table">';
    $table .= '<thead>
               <th class="manage-column" data-field="type">Type</th>
                <th class="manage-column" data-field="name">Narrative</th>
                <th class="manage-column" data-field="date">Date</th>
               </thead><tbody>';
    foreach ($alerts as $alert) {
     
      $cat_names = "SELECT english_name FROM wp_category_lookup WHERE dhg_id =$alert->alert_dhg_cat";
   
      $cat_name = $wpdb->get_results($cat_names);
      //print_r($cat_name);
     
      $subcat_names = "SELECT english_name FROM wp_category_lookup WHERE dhg_id =$alert->alert_dhg_subcat";
      $subcat_name =$wpdb->get_results($subcat_names);



         switch ($alert->alert_type) {



            case 'zero_stock':
               $pid = wc_get_product_id_by_sku($alert->alert_related_id);
               $wc_product_data = wc_get_product($pid);
               if ($pid != 0) {
                  $product_name = $wc_product_data->get_name();
                  $link = $wc_product_data->get_permalink();
               } else {
                  $product_name = '';
                  $link = '';
               }
               $editurl = 'admin.php?post.php?post='.$pid.'&action=edit';
               $table .= '<tr><td>Stock Level: 0</td>';
               $table .= '<td>'.$alert->alert_related_id.'</td>';
               $table .= '<td><a href="'.$link.'" target="_blank"> ('.$product_name.')</a></td>';
               $table .= '<td>'.$cat_name[0]->english_name.'</td>';
               $table .= '<td>'.$subcat_name[0]->english_name.'</td>';
               $table .= '<td></td>';  //no price info
               $table .= '<td>'.date('j F', strtotime($alert->alert_created)).'</td>';
               // $table .= '<td class="action action1"><a href="'.admin_url('admin.php?page=import-products&tab=mapping&alertid='.$alert->alert_id.'&zero='.$alert->alert_related_id).'">Remove Item</a></td>';
               // $table .= '<td class="action action2"><a href="#" id="item'.$alert->alert_related_id.'" class="set_stock" data-type="text" data-pk="'.$alert->alert_related_id.'" data-title="New Stock Level">Set Stock Level</a></td>';
               $table .= '<td class="action action3"><a href="'.admin_url($editurl).'">Update</a></td></tr>';
               break;

            case 'new_subcategory':
               $categories = strip_tags(get_string_between($alert->alert_narrative, 'A new subcategory ', ' was found.'));         
               $table .= '<tr><td>New Subcategory</td>';
               $table .= '<td></td><td>'.$alert->alert_narrative.'</td><td></td><td></td><td></td>';
               $table .= '<td>'.date('j F', strtotime($alert->alert_created)).'</td>';
               // $table .= '<td class="action action1"><a href="'.admin_url('admin.php?page=import-products&tab=mapping&alertid='.$alert->alert_id.'&newcat='.$alert->alert_related_id.'_'.$alert->alert_related_parent.'&catnames='.$categories).'">Create Category</a></td>';
               // $table .= '<td class="action action2"></td>';
               $table .= '<td class="action action3"></td></tr>';
               break;

            case 'product_removed':

               $pid = wc_get_product_id_by_sku($alert->alert_related_id);
               $wc_product_data = wc_get_product($pid);
               if ($pid != 0) {
                  $product_name = $wc_product_data->get_name();
                  $link = $wc_product_data->get_permalink();
               } else {
                  $product_name = '';
                  $link = '';
               }
               $editurl = 'admin.php?post.php?post='.$pid.'&action=edit';
               $table .= '<tr><td>Product Removed</td>';
               $table .= '<td>'.$alert->alert_related_id.'</td>';
               $table .= '<td><a href="'.$link.'" target="_blank"> ('.$product_name.')</a></td>';
               $table .= '<td>'.$alert->alert_wc_cat.'</td><td></td>';
               $table .= '<td></td>';  //no price info
               $table .= '<td>'.date('j F', strtotime($alert->alert_created)).'</td>';
               // $table .= '<td class="action action1"><a href="'.admin_url('admin.php?page=import-products&tab=mapping&alertid='.$alert->alert_id.'&newcat='.$alert->alert_related_id.'_'.$alert->alert_related_parent).'">Remove Item</a></td>';
               // $table .= '<td class="action action2">Set Stock to: <input type="number" placeholder="0" style="width: 70px;" /><button data-sku="'.$alert->alert_related_id.'" class="set-stock" >Set Stock</button></td>';
               $table .= '<td class="action action3"><a href="'.admin_url($editurl).'">Update</a></td></tr>';
               break;
            
            case 'subcategory_removed':
               # code...
               break;

            case 'new_product_added':
               
               $table .= '<tr><td>New Product</td>';
               $table .= '<td>'.$alert->alert_related_id.'</td>';
               $table .= '<td>'.$alert->alert_narrative.'</td>';
               $table .= '<td>'.$cat_name[0]->english_name.'</td>';
               $table .= '<td>'.$subcat_name[0]->english_name.'</td>';
               $table .= '<td>'.$alert->alert_item_price.'</td>';
               $table .= '<td>'.date('j F', strtotime($alert->alert_created)).'</td>';
               // $table .= '<td class="action action1"><a href="'.admin_url('admin.php?page=import-products&tab=mapping&alertid='.$alert->alert_id.'&newcat='.$alert->alert_related_id.'_'.$alert->alert_related_parent).'">Add Item</a></td>';
               // $table .= '<td class="action action2">Set Stock to: <input type="number" placeholder="0" style="width: 70px;" /><button data-sku="'.$alert->alert_related_id.'" class="set-stock" >Set Custom Stock</button></td>';
               $table .= '<td class="action action3"></td></tr>';
               break;

            default:
               
               break;
         }

      
    }
    $table .= '</tbody></table>';
    return $table;
}


// add_action( 'admin_enqueue_scripts', 'script_enqueuer' );
// //do_action( 'admin_enqueue_scripts','string $hook_suffix ')
// function script_enqueuer() {
   
//    // Register the JS file with a unique handle, file location, and an array of dependencies
//    wp_register_script( "alert_script", plugin_dir_url( __FILE__ ) . 'alerts.js', array('jquery') );
//    wp_enqueue_script( 'alert_script' );

//    wp_register_script('bootstrap',  'https://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js', array('jquery') );
//    wp_enqueue_script( 'bootstrap' );

//    // wp_register_script('xeditable', 'https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js', array('bootstrap') );
//    // wp_enqueue_script( 'xeditable' );

//    wp_register_script('datatable', 'https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js', array('bootstrap') );
//    wp_enqueue_script( 'datatable' );

//    wp_register_script('pdfmaker', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js', array('datatable') );
//    wp_enqueue_script( 'pdfmaker' );

//    wp_register_script('vfsfonts', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js', array('pdfmaker') );
//    wp_enqueue_script( 'vfsfonts' );

//    wp_register_script('datatable_buttons', 'https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-colvis-1.6.4/b-flash-1.6.4/b-html5-1.6.4/b-print-1.6.4/datatables.min.js', array('vfsfonts') );
//    wp_enqueue_script( 'datatable_buttons' );

//    // localize the script to your domain name, so that you can reference the url to admin-ajax.php file easily
//    wp_localize_script( 'alert_script', 'alertScripts', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        

 
// }

 

add_action("wp_ajax_set_alert_stock", "set_alert_stock");
add_action( "wp_ajax_nopriv_set_alert_stock", "set_alert_stock");
function set_alert_stock () {
   //get sku, set the stock value and return successful or not.
   $sku = $_POST["sku"];

   echo 'hissss';
   die();
   
}


?>