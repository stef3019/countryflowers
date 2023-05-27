<?php 
function display_current_supplier_products () {
    global $wpdb;

   check_products_file ();

    echo '<h1>Check Current DHG Inventory</h1>';		
    echo '<h3>List products for category: </h3>';
    echo get_available_categories();
}

function render_dgh_category_inventory ($cat, $products, $action = null, $subcat) {

    $table = wp_nonce_field( 'cf-dhg-inventory' );
    $table .= '<table class="widefat fixed wp-list-table striped posts" cellspacing="0" id="dhg_inventory">';
    $table .= '<thead>
                <th class="manage-column" data-field="status">New or Listed</th>
               <th class="manage-column" data-field="type">Sub Category</th>
               <th class="manage-column" data-field="product">Stock Amount</th>
                <th class="manage-column" data-field="name">DGH ID</th>
                <th class="manage-column" data-field="cat">Code</th>
                <th class="manage-column" data-field="subcat">Variant</th>
                <th class="manage-column" data-field="price">Name</th>
                <th class="manage-column" data-field="date">Colour</th>
                <th class="manage-column" data-field="act">Selling Price</th>
                <th class="manage-column" data-field="act">Availability</th>
               </thead><tbody>';

    foreach ($products as $product) {
                $pid = wc_get_product_id_by_sku($product['variant']);
                if (!empty($pid)) {
                    $new = 'LISTED '.$pid;
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
        
        
                if (isset($product['categories'][0])) {
                    if (($product['categories'][0]['id'] == $cat) && ($product['subCategories'][0]['id']) == $subcat) {
                        $table .= '<tr">
                                <td>'.$new.' </td>
                                <td>'.$product['subCategories'][0]['name'].'</td>
                                <td>'.$stock.' </td>
                                <td>'.$product['id'].' </td>
                                <td>'.$product['code'].' </td>
                                <td>'.$product['variant'].' </td>
                                <td>'.$product['name'].'</td>
                                <td>'.$product['color'].' </td>
                                <td>'.number_format(floatval(str_replace(',', '.', str_replace('.', '', $product['price']))),2).' </td> 
                                <td>'.$product['meta']['Disponibilità'].' </td>
                                </tr>';
                    }
                }

            }
     $table .= '<tfoot>
            <th class="manage-column" data-field="status">New or Listed</th>
           <th class="manage-column" data-field="type">Sub Category</th>
           <th class="manage-column" data-field="product">Stock Amount</th>
            <th class="manage-column" data-field="name">DGH ID</th>
            <th class="manage-column" data-field="cat">Code</th>
            <th class="manage-column" data-field="subcat">Variant</th>
            <th class="manage-column" data-field="price">Name</th>
            <th class="manage-column" data-field="date">Colour</th>
            <th class="manage-column" data-field="act">Selling Price</th>
            <th class="manage-column" data-field="act">Availability</th>
           </tfoot>';


    $table .= '</table>';
        return $table;
}
   


add_action( 'admin_enqueue_scripts', 'script_enqueuer' );
//do_action( 'admin_enqueue_scripts','string $hook_suffix ')
function script_enqueuer() {
   
   // Register the JS file with a unique handle, file location, and an array of dependencies
   wp_register_script( "alert_script", plugin_dir_url( __FILE__ ) . 'alerts.js', array('jquery') );
   wp_enqueue_script( 'alert_script' );

   wp_register_script('bootstrap',  'https://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js', array('jquery') );
   wp_enqueue_script( 'bootstrap' );

   // wp_register_script('xeditable', 'https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js', array('bootstrap') );
   // wp_enqueue_script( 'xeditable' );

   wp_register_script('datatable', 'https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js', array('bootstrap') );
   wp_enqueue_script( 'datatable' );

   wp_register_script('pdfmaker', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js', array('datatable') );
   wp_enqueue_script( 'pdfmaker' );

   wp_register_script('vfsfonts', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js', array('pdfmaker') );
   wp_enqueue_script( 'vfsfonts' );

   wp_register_script('datatable_buttons', 'https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-colvis-1.6.4/b-flash-1.6.4/b-html5-1.6.4/b-print-1.6.4/datatables.min.js', array('vfsfonts') );
   wp_enqueue_script( 'datatable_buttons' );

   // localize the script to your domain name, so that you can reference the url to admin-ajax.php file easily
   wp_localize_script( 'alert_script', 'alertScripts', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        

 
}

 

// add_action("wp_ajax_set_alert_stock", "set_alert_stock");
// add_action( "wp_ajax_nopriv_set_alert_stock", "set_alert_stock");
// function set_alert_stock () {
//    //get sku, set the stock value and return successful or not.
//    $sku = $_POST["sku"];

//    echo 'hissss';
//    die();
   
// }

function download_images () {
    echo '<h1>Import Products from DHG</h1>';
			
			echo '<h3>Download images for: </h3>';
			//echo check_products_file();
			echo get_available_categories();
}


?>