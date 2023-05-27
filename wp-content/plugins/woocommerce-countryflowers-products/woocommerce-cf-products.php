<?php
/*
Plugin Name: WooCommerce Country Flowers DHG Importer
Plugin URI: https://stefcordina.com
Description: WooCommerce Country Flowers Product Importer
Author: Stef Cordina
Author URI: http://www.stefcordina.com
Text Domain: cf_import_products
Version: 1.2.0

	Copyright: © 2020 Stef Cordina (email : stefcordina@gmail.com)
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

/* FILE CONTENTS ---------------------------------------------------------------------------------------------------------

CLASS
	if woocommerce_loaded
		1. check_products_file  			(cron function to check products file)
		2. write_json_to_file  				(create the json file for the day)  !NEED TO INCLUDE DELETION OF OLDEST
		3. scan_products_for_changes  		(cron scanner for changes) !UNDER CONSTRUCTION
		4. grab_products_json				(helper function to grab products from file and return them in a variable)
		5. send_products_to_wc				(result of POST to send json file products to become WC products)
		7. get_available_categories			(get a list of available main categories)
		7.1 remove_csv_products				(remove products that have been uploaded via the CSV)
8.  cf_products_admin_css
9.  register_submenu_item					(add the item in the admin menu)
10. submenu_page_callback					(submenu page initial render)
11. import_products_enqueue_admin_script	(admin scripts, localizing admin-ajax)

12. get_categories_checkbox_list			(helper function render checkboxes for WC product categories to import into)
13. cf_create_tables						(create tables on activation)
14. create_form_for_assorted_products		(create form to add variations to products marked 'assortito')
15. create_variations_for_assorted			(actually create the variations for the assorted products)
16. Shortcode Test function					--used for testing functions quickly--
 ---------------------------------------------------------------------------------------------------------------------------
*/

// add_shortcode( 'testsc', 'run_test' );
// function run_test () {
// 	$args = array(
// 		'post_type'      => 'product',
// 		'posts_per_page' => -1,
// 		'product_cat'    => 'flowers-and-pick'
// 	);
// 	$loop = new WP_Query( $args );
// 	while ( $loop->have_posts() ) : $loop->the_post();
// 		global $product;
// 		//if (! woocommerce_get_product_thumbnail()) {
// 			echo woocommerce_get_product_thumbnail();
// 		//}
// 	endwhile;
// 	wp_reset_query();
// }


//require and enqueue files
//require_once('woocommerce-cf-cron-functions.php');
require_once('woocommerce-cf-TEST-cron-functions.php');
require_once('cf-render-functions.php');
require_once('woocommerce-cf-product-creator.php');
//require_once('update-prices.php');
require_once('woocommerce-cf-current-supplier-products.php');
require_once('woocommerce-cf-stock-updates.php');
require_once('woocommerce-cf-alerts.php');
require_once('woocommerce-cf-freshflowers.php');
require_once('woocommerce-cf-dhg-database.php');

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/*setup Cron*/
function trigger_sync_cron () {
	if (!wp_next_scheduled('cf_daily_sync')) {

		 wp_schedule_event( time(), 'daily', 'cf_daily_sync' );
		 	// wp_mail( 'stefcordina@gmail.com', 'cf activation test', 'this ran');
	 } 

 }

 register_activation_hook( __FILE__, 'trigger_sync_cron' );
 


/**
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	
	if ( ! class_exists( 'WC_CF_Products' ) ) {
		
		/**
		 * Localisation
		 **/
		load_plugin_textdomain( 'WC_CF_Products', false, dirname( plugin_basename( __FILE__ ) ) . '/' );

		class WC_CF_Products {

			public function __construct() {
				// called only after woocommerce has finished loading
				add_action( 'woocommerce_init', array( &$this, 'woocommerce_loaded' ) );
				
			}
			
			/**
			 * Take care of anything that needs woocommerce to be loaded.  
			 * For instance, if you need access to the $woocommerce global
			 */
			public function woocommerce_loaded() {

				function grab_json_string () {
					$upload_dir = wp_upload_dir();
					$fileurl = trailingslashit( $upload_dir['baseurl'] ).'imported_products/raw_products'.date('dmy').'.json';
					$productsstring = file_get_contents($fileurl);
					
					return $productsstring;
				}
				
				
				//HELPER: put products from json file into a php object
				function grab_products_json () {
					$upload_dir = wp_upload_dir();
					$fileurl = trailingslashit( $upload_dir['baseurl'] ).'imported_products/raw_products'.date('dmy').'.json';
			
					$productsstring = file_get_contents($fileurl);
					//var_dump($productsstring);
					$json = json_decode($productsstring, true);
					$json_p = $json['products'];
					
					return $json_p;  //returns array
				}


				function grab_selected_products_json ($selected_prods) {
					
					//get all available products from json/csv and bung into an array
					$upload_dir = wp_upload_dir();
					$fileurl = trailingslashit( $upload_dir['baseurl'] ).'imported_products/raw_products'.date('dmy').'.json';
					$productsstring = file_get_contents($fileurl);
					
					$json = json_decode($productsstring, true);					
					$json_p = $json['products'];


					//create WC product
						foreach ($selected_prods as $prod_item) {
						// 	echo '</br>running';
						// echo $prod_item[0].' ';
							// search for prod_item in $json_p and add it to selected
							$found_key = array_search($prod_item[0], array_column($json_p, 'variant'));
						//	echo var_dump($found_key);

							//replace name and price
							$details = $json_p[$found_key];
							$details['name'] = $prod_item[1];

							//$price =  str_replace(".",",",$prod_item[2]);
							var_dump($prod_item);
							
							//$price = $prod_item[2];
								echo $prod_item['0'].' '.'New '.$prod_item[2].' - ';
						
							$cost =  ($prod_item[2])/3;
							$cost =  $cost*3.5;
						
							//$cost =  ($prod_item[2]);
								echo 'Multiplied '.$cost.' - ';

							$round_num = round($cost / 0.05) * 0.05;
								echo 'Rounded '.$round_num.' - ';

							$price = number_format($round_num, 2,'.','');
								echo 'Final '.$price.'</br>';
						
							$details['price'] = $price;
							
							// use this array to return to the WC product creator	
						//	echo 'key'.$found_key.'</br>';

							

							//add to selected products json_s array
							$json_s[] = $details;
						}
					return ($json_s);
				}
	
				//used by import csv products
		function send_products_to_wc ($pr = null, $cat = 0, $subcat = 0) {	
					
			if (isset ($_POST['cf_to_add'])) {
				$selected = $_POST['cf_to_add'];
				$json_p = grab_products_json ();
				$categories = array();
				$categories[] = $_POST['category'];
				$categories[] = $_POST['subcategory'];
			} else {
				//echo '</br>in else</br>';
				$selected = array();
				$json_p = grab_selected_products_json ($pr);
				print_r($json_p);
			//	echo 'something';
				foreach ($json_p as $cf_to_add) {
					$selected[] = $cf_to_add['id'];
				}
				
				$categories = array();
				$categories[] = $cat;
				$categories[] = $subcat;
				//exit;
			}
						
				

					//$group_number = 1;
					$items = array();
					$count = 0;
					
					//foreach $selected, create array of all items selected with TYPE and GROUP No if Variable product 
					foreach ($selected as $selected_product) { 
						

						foreach ($json_p as $product) { //start iterating the products for this particular id
							if ($selected_product == $product['id']) { //if found;

								// echo '</br>Found Match for :'.$product['name'];
								// echo ' - counter='.$count;
								
								//look for it in json_p and grab the product. ['product_info'] (array)
								$items[$count]['product'] = $product;

								//save its type ['item_type'] (string)
								if (($product['code'] != $product['variant']) || (preg_replace('/\s/', '',  $product['color']) == 'ASSORTITO')) {
								//	echo 'VARIABLE MATCH';
									$items[$count]['item_type'] = 'variable';

									//create a group number count ['sku'] (int) = product SKU
									$items[$count]['sku']  = $product['code'];

								} else {
							
									$items[$count]['item_type'] = 'simple';
								}

									
								//flag as assorted ['assorted'] (bool)
								echo '<br>'.$product['code'].'-'.preg_replace('/\s/', '',  $product['color']);

								if (preg_replace('/\s/', '',  $product['color']) == 'ASSORTITO') {
									//echo 'assorted TRUE flag';
									$items[$count]['assortito'] = true;
								} else {
									//echo 'FALSE flag';
									$items[$count]['assortito'] = false;
								}
								break;
							}
						
						}
						$count++;
						}
						

						filter_selected_items($items, $categories);
						exit;
				}

				
				//CURRENTLY UNUSED
				function create_variations_for_assorted () {
					$assorted_meta_nonce = wp_create_nonce( 'assorted_meta_nonce' ); 
					if (isset($_POST['parent_sku'])) {
						$vars = $_POST['assorted_variation_list'];
						$vars = explode(',',$vars);
					   $sku = $_POST['parent_sku'];
					   assorted_variations_creator($sku, $vars);

					} else {
						
					?>

					<form action="<?php echo esc_url( admin_url( 'admin.php?page=import-products&tab=assortito' )); ?>" method="POST" id="add_assorted_variations">	 		
						<input type="hidden"  name="action" value="assorted_product_variations" />
						<input type="hidden" name="assorted_meta_nonce" value="<?php echo $assorted_meta_nonce ?>" />	
						<input type="text"  name="parent_sku" placeholder="Parent SKU" />	
						<input type="text"  name="assorted_variation_list" placeholder="eg: white,red,blue" style="width:800px" />	
						<p class="submit"><input type="submit" id="add_variations_submit" class="button button-primary" value="Add Variations"></p>
					</form>

					<?php	
				}

			}

				
				function get_available_categories ($submit = true) {
					//echo 'TEST';
					$json_p = grab_products_json ();
					//filter out all products that are not available
					$p = 1;
		
					
					foreach ($json_p as $product) {
						//if ($product['meta']['Disponibilità'] != 0) {
							$available_products[] = $product;
							$p++;
						//}
					}
					//var_dump($available_products);
					//grab only different categories
					$p = $c = 0;
					$categories = array();
					$subcategories = array();

					

					//foreach product, gather unique categories and subcategories
					foreach ($available_products as $product) {
					
						if (!empty($product['categories'])) {
							if (!in_array($product['categories'][0]['id'], $categories)) {
								$categories[] =  $product['categories'][0]['id'];
								$cat_links[$p]['id'] = $product['categories'][0]['id'];
								$cat_links[$p]['name'] = $product['categories'][0]['name'];
								$p++;
							}	
						}

						if (!empty($product['subCategories'])) {
							if (!in_array($product['subCategories'][0]['id'], $subcategories)) {
								$subcategories[] =  $product['subCategories'][0]['id'];
								$subcat_links[$c]['id'] = $product['subCategories'][0]['id'];
								$subcat_links[$c]['name'] = $product['subCategories'][0]['name'];
								$subcat_links[$c]['parent'] = $product['subCategories'][0]['categoryId'];
								$c++;
							}
						}


					}
					
					//print("<pre>".print_r($cat_links,true)."</pre>");
					// print("<pre>".print_r($subcat_links,true)."</pre>");
					
					build_category_selector($cat_links, $subcat_links, $submit);
					
					if (isset($_GET['cat'])) {
						//echo create_product_category_table ($_GET['cat'], $available_products, 'list', $_GET['subcat']);
						echo render_dgh_category_inventory  ($_GET['cat'], $available_products, 'list', $_GET['subcat']);
					}
					
					//list_category_products ();

					//get products of said category and list them with a checkbox

					//after selection (POST form, import to WooCommerce as products)
				}
				add_action( 'wp_ajax_get_available_categories', 'get_available_categories' );
			}

			

		} //close class


		//add admin stuff

		add_action('admin_enqueue_scripts', 'cf_products_admin_css');
		function cf_products_admin_css($hook)
		{
			$current_screen = get_current_screen();
			if ( strpos($current_screen->base, 'woocommerce_page_import-products') === false) {
				return;
			} else {
				wp_enqueue_style( 'bootstrap_style','//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css');
				wp_enqueue_style( 'bootstrap_editable_style', 'https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css');
				wp_enqueue_style('cf_products_admin', plugins_url('cf-products-admin.css',__FILE__ ));
				wp_enqueue_style('data_tables','https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css');
				wp_enqueue_style('data_tables_buttons','https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.22/b-1.6.4/b-colvis-1.6.4/b-flash-1.6.4/b-html5-1.6.4/b-print-1.6.4/datatables.min.css');
				
				// wp_enqueue_script('boot_js', plugins_url('inc/bootstrap.js',__FILE__ ));
				// wp_enqueue_script('ln_script', plugins_url('inc/main_script.js', __FILE__), ['jquery'], false, true);
				}
		}

		function register_submenu_item() {
			add_submenu_page( 'woocommerce', 'Import DHG Products', 'Manage DHG Products', 'edit_others_shop_orders', 'import-products', 'submenu_page_callback' ); 
		}

		function submenu_page_callback() {
			if (isset($_GET['tab'])) { 
				$tab = $_GET['tab']; 
			} else { 
				$tab = '';
			} ?>
			<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
				<a href="<?php echo admin_url('admin.php?page=import-products&tab=default')?>" class="nav-tab<?php echo (!(isset($_GET['tab']))|| ($tab == 'default')) ? ' nav-tab-active' : ''?>">Functions</a>
				<a href="<?php echo admin_url('admin.php?page=import-products&tab=sync')?>" class="nav-tab<?php echo ($tab == 'sync') ? ' nav-tab-active"' : '"'?>>Sync with DHG</a>
				<a href="<?php echo admin_url('admin.php?page=import-products&tab=import')?>" class="nav-tab<?php echo ($tab == 'import') ? ' nav-tab-active"' : '"'?>">List DHG Products</a>
				<!-- <a href="<?php //echo admin_url('admin.php?page=import-products&tab=assortito')?>" class="nav-tab<?php //echo ($tab == 'assortito') ? ' nav-tab-active"' : '"'?>">Assortito</a> -->
				<a href="<?php echo admin_url('admin.php?page=import-products&tab=mapping')?>" class="nav-tab<?php echo ($tab == 'mapping') ? ' nav-tab-active"' : '"'?>">Map Categories</a>
				<a href="<?php echo admin_url('admin.php?page=import-products&tab=bulk-csv')?>" class="nav-tab<?php echo ($tab == 'bulk-csv') ? ' nav-tab-active"' : '"'?>">CSV Operations</a>
				<a href="<?php echo admin_url('admin.php?page=import-products&tab=images')?>" class="nav-tab<?php echo ($tab == 'images') ? ' nav-tab-active"' : '"'?>">Download Images</a>
				<a href="<?php echo admin_url('admin.php?page=import-products&tab=changes')?>" class="nav-tab<?php echo ($tab == 'changes') ? ' nav-tab-active"' : '"'?>">DHG Item Changes</a>
				<a href="<?php echo admin_url('admin.php?page=import-products&tab=database')?>" class="nav-tab<?php echo ($tab == 'database') ? ' nav-tab-active"' : '"'?>">DB Functions</a>
			</nav>
			<?php

			switch ($tab) {
				// case 'assortito':
				// 	echo create_variations_for_assorted();
				// 	break;

				case 'mapping':
					echo render_new_category_form();
					break;
				
				case 'import':
					display_current_supplier_products();
					break;

				case 'sync':
					sync_inventory();
					break;
					
				case 'bulk-csv':
					csv_amend_page();
					break;

				case 'images':
					download_images();
					break;

				case 'changes':
					view_changes();
					break;
				
				case 'database':
					database_functions();
					break;
			
				default:
					render_info_div();
					break;
			}
		}	
		add_action('admin_menu', 'register_submenu_item',99);

		
		

		function view_changes() {
			echo '<h1>Product Changes</h1>';
			echo get_cf_product_changes();
		
		}


		function database_functions() {
			echo '<h1>Database Changes Changes</h1>';
			echo '<a href="'.admin_url("admin.php?page=import-products&tab=database&act=insert").'">Insert into Database</a></br>';
			if ($_GET['act'] == 'insert') {
				insert_dhg_products();
			}
			echo '<h3>Import Subcategory</h3>';
			echo '<form action="'.esc_url( admin_url( 'admin-post.php' ) ).'" method="post" id="create_db_wc_products"  enctype="multipart/form-data">
            <input type="hidden" name="action" value="create_db_wc_products">';
			//echo '<a href="'.admin_url("admin.php?page=import-products&tab=database&act=subcat").'">Create Subcat Products</a></br></br>';
			echo get_available_categories(false);
			echo ' <input type="submit" name="insertBtn" value="Add New Products" />
			</form>';	
			
		}

		add_action( 'admin_post_create_db_wc_products', 'create_new_products_from_db' );
		add_action( 'admin_post_nopriv_create_db_wc_products', 'create_new_products_from_db' );


		function import_products_enqueue_admin_script( $hook ) {
			if ( 'woocommerce_page_import-products' != $hook ) {
				return;
			}
			wp_enqueue_script( 'admin_calls', plugin_dir_url( __FILE__ ) . 'admin-calls.js', array('jquery') , '1.0' );
			wp_localize_script( 'admin_calls', 'ajax_object',
				array( 'ajax_url' => admin_url( 'admin-ajax.php' ),
			)); 
		}
		add_action( 'admin_enqueue_scripts', 'import_products_enqueue_admin_script' );
		

		// finally instantiate our plugin class and add it to the set of globals
		$GLOBALS['WC_CF_Products'] = new WC_CF_Products();
	} // end if plugin active class exists 


//COMMENTED below coz its causing output character error on activation

//Run this function on activation
// function cf_create_tables()
// {
//     global $table_prefix, $wpdb;

//     $tblname = 'category_lookup';
//     $wp_track_table = $table_prefix . "$tblname ";

//     //Check to see if the table exists already, if not, then create it
//     if($wpdb->get_var( "show tables like '$wp_track_table'" ) != $wp_track_table) 
//     {

//         $sql = "CREATE TABLE `". $wp_track_table . "` ( ";
//         $sql .= "  `id`  int(11)   NOT NULL auto_increment, ";
// 		$sql .= "  `wc_id`  int(11)   NOT NULL, ";
// 		$sql .= "  `dhg_id`  int(11)   NOT NULL, ";
// 		$sql .= "  `english_name`  int(11)   NOT NULL, ";
//         $sql .= "  PRIMARY KEY `id` (`id`) "; 
//         $sql .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
//         require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
//         dbDelta($sql);
// 	}
	

// 	$tblname = 'cf_products_alerts';
//     $wp_track_table = $table_prefix . "$tblname ";

//     //Check to see if the table exists already, if not, then create it
//     if($wpdb->get_var( "show tables like '$wp_track_table'" ) != $wp_track_table) 
//     {

//         $sql = "CREATE TABLE `". $wp_track_table . "` ( ";
//         $sql .= "  `alert_id`  int(11)   NOT NULL auto_increment, ";
// 		$sql .= "  `alert_type` ENUM('deleted','new','stock','price','zero_stock')  NOT NULL, ";
// 		$sql .= "  `alert_narrative`  var(120)   NOT NULL, ";
// 		$sql .= "  `alert_related_id`  int(11)  NULL, ";
// 		$sql .= "  `alert_dhg_cat`  int(11)  NULL, ";
// 		$sql .= "  `alert_dhg_subcat`  int(11) NULL, ";
// 		$sql .= "  `alert_wc_cat`  int(11) NULL, ";
// 		$sql .= "  `alert_created`  DATETIME  CURRENT_TIMESTAMP, ";
//         $sql .= "  PRIMARY KEY `id` (`alert_id`) "; 
//         $sql .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
//         require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
//         dbDelta($sql);
// 	}
	
// 	//create folder in upload directory
// 	$upload = wp_upload_dir();
//     $upload_dir = $upload['basedir'];
//     $upload_dir = $upload_dir . '/imported_products';
//     if (! is_dir($upload_dir)) {
//        mkdir( $upload_dir, 0700 );
//     }


// }
// register_activation_hook( __FILE__, 'cf_create_tables' );



// add_action( 'pre_get_posts', 'cf_hide_out_of_stock_products' );

// function cf_hide_out_of_stock_products( $q ) {
//     if ( ! $q->is_main_query() || is_admin() ) {
//         return;
//     }

//     if ( $outofstock_term = get_term_by( 'name', 'outofstock', 'product_visibility' ) ) {
//         $tax_query = (array) $q->get('tax_query');
//         $tax_query[] = array(
//             'taxonomy' => 'product_visibility',
//             'field' => 'term_taxonomy_id',
//             'terms' => array( $outofstock_term->term_taxonomy_id ),
//             'operator' => 'NOT IN'
//         );
//         $q->set( 'tax_query', $tax_query );
//     }
//     remove_action( 'pre_get_posts', 'cf_hide_out_of_stock_products' );
// }



//allow for duplicate SKU (CAN CAUSE ISSUES WITH variants but placed since otherwise creates an internal server error :/)
add_filter( 'wc_product_has_unique_sku', '__return_false' );


} //end check if woocommerce is active

//used in Map Categories Tab
function create_new_product_subcategory($parent_wcid, $subcatname, $subcat_dhgid, $parent_dhgid, $alertid) {
	global $wpdb;
	//create woocommerce product category
    $newcat = wp_insert_term(
        $subcatname,	// the name of the new sub-category
        'product_cat',  // the taxonomy
        array(
			'slug' =>  urlencode(str_replace(' ', '-', $subcatname)),  // what to use in the url for term archive
			'parent'=>$parent_wcid // link with main category. In the case, become a child of the $parentname parent category
		)
	);

	//add to lookup
	$lookupId = $wpdb->insert('wp_category_lookup', 
		array(
			'wc_id' => $newcat, 
			'dhg_id' => $subcat_dhgid,
			'dhg_parent_id' => $parent_dhgid,
			'currently_active' => 1,
			'english_name' => $subcatname
			)
		);
	
	if ($lookupId > 0) {
		//set alert to actioned
		$wpdb->update('wp_cf_products_alerts', 
			array(
				'alert_actioned'=>1
			),
			array(
				'alert_id'=>$alertid
			)
		); ?>
	
		<a href="<?php echo admin_url('admin.php?cat='.$parent_dhgid.'&subcat='.$subcat_dhgid.'&page=import-products&tab=import')?>">Import Products</a>
<?php	}
	return $newcat;
}


function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}



add_filter( 'woocommerce_package_rates', 'my_hide_shipping_when_free_is_available' );
function my_hide_shipping_when_free_is_available( $rates ) {
		$free = array();
		//echo 'shipping stuff';
        foreach( $rates as $rate_id => $rate ) {
          if( 'free_shipping' === $rate->method_id ) {
                $free[ $rate_id ] = $rate;
                break;
          }
        }

        return ! empty( $free ) ? $free : $rates;
}


//Used to process form in CSV Pperations Tab
function process_csv_products() {
	global $wpdb;
	if (isset($_FILES['amendableProducts']) && $_FILES['amendableProducts']['error'] === UPLOAD_ERR_OK) {
		// get details of the uploaded file
		$fileTmpPath = $_FILES['amendableProducts']['tmp_name'];
		$fileName = $_FILES['amendableProducts']['name'];
		$fileSize = $_FILES['amendableProducts']['size'];
		$fileType = $_FILES['amendableProducts']['type'];
		$fileNameCmps = explode(".", $fileName);
		$fileExtension = strtolower(end($fileNameCmps));

		$action = $_POST['amend_action'];

		// sanitize the filename
		$newFileName = md5(time() . $fileName) . '.' . $fileExtension;

		$allowedfileExtensions = array('csv');
		if (in_array($fileExtension, $allowedfileExtensions)) {
			// directory in which the uploaded file will be moved
			$upload_dir = wp_upload_dir();
	
			$uploadFileDir = trailingslashit( $upload_dir['basedir'] ).'imported_products/';
		
			$dest_path = $uploadFileDir . $newFileName;
			$import_csv = array();
			if(move_uploaded_file($fileTmpPath, $dest_path)) {
				$message ='File is successfully uploaded.';
				$r = 0;
				$fileHandle = fopen($dest_path, "r");
				while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
					//Print out my column data.
					

					switch ($action) {
						case 'import':
							//build the array of products with a variant ids
							$import_csv[$r][0] = $row[0];
							$import_csv[$r][1] = $row[1];
							$import_csv[$r][2] = $row[2];
						
							$r++;
							break;

						case 'delete':
							$pid = wc_get_product_id_by_sku($row[0]);
							wc_deleteProduct($pid);
							// mark alert as actioned.
							$alert = $wpdb->update('wp_cf_products_alerts', 
									array(
										'alert_actioned' => 1 
									),
									array(
										'alert_related_id' => $row[0],
										'alert_actioned_on' => date("Y-m-d H:i:s")
									)
							);
							break;

						case 'stock':
							$pid = wc_get_product_id_by_sku($row[0]);
							wc_set_product_stock($pid, $row[1]);
							break;

						case 'price':
							$pid = wc_get_product_id_by_sku($row[0]);
							echo $pid.' '.$row[1];
							wc_set_product_price($pid, $row[1]);
							break;

						default:
							# code...
							break;
					}
					
				}

				//out of the foreach loop
				if ($action == 'import') {

					$dhg_cat = $_POST['cat'];
					$dhg_subcat = $_POST['subcat'];

				
					$sql = "SELECT wc_id  FROM wp_category_lookup WHERE dhg_id=$dhg_cat";
					$results=$wpdb->get_row($sql);
					$cat = $results->wc_id;
					echo $cat.'</br>';

					$sql = "SELECT wc_id FROM wp_category_lookup WHERE dhg_id=$dhg_subcat";
					$results=$wpdb->get_row($sql);
					$subcat = $results->wc_id;
					echo $subcat.'</br>'; 
					

					send_products_to_wc($import_csv, $cat, $subcat);
				}	


			} else	{
				$message = 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
			}
		}
		echo $message;
	} else {
		$message = 'Error';
		echo $message;
	}
}


//Function to delete products from WooCommerce (used from CSV and sync products)
function wc_deleteProduct($id, $force = FALSE) {

	$product = wc_get_product($id);
	
    if(empty($product))
        return new WP_Error(999, sprintf(__('No %s is associated with #%d', 'woocommerce'), 'product', $id));

    // If we're forcing, then delete permanently.
    if ($force)
    {
        if ($product->is_type('variable'))
        {
            foreach ($product->get_children() as $child_id)
            {
                $child = wc_get_product($child_id);
                $child->delete(true);
            }
        }
        elseif ($product->is_type('grouped'))
        {
            foreach ($product->get_children() as $child_id)
            {
                $child = wc_get_product($child_id);
                $child->set_parent_id(0);
                $child->save();
            }
        }

        $product->delete(true);
        $result = $product->get_id() > 0 ? false : true;
    }
    else
    {
	  $product->set_status('trash');
	  $result = $product->save();
    }


    if (!$result)
    {
        return new WP_Error(999, sprintf(__('This %s cannot be deleted', 'woocommerce'), 'product'));
    }

    // Delete parent product transients.
    if ($parent_id = wp_get_post_parent_id($id))
    {
        wc_delete_product_transients($parent_id);
    }


    return true;
}



//used from CSV upload to set the price to that stated in the CSV itself (ie not DHG)
function wc_set_product_price($id = 0, $price = 0, $sku = null, $name = '') {
	$product = wc_get_product($id);
	$product->set_catalog_visibility('catalog');
	$product->set_regular_price($price);
	$product->save();
	wc_delete_product_transients( $id );
}

//used in sync products: to update stock value
function wc_set_product_stock($id, $stock = 0, $sku = null, $name = '') {
	global $wpdb;

	$product = wc_get_product($id);
	$current = $product->get_stock_quantity();

	if ($current != $stock)  { //if stock is different...
/*		$narrative = ' CHANGING STOCK: '.$sku.' - '.$name.': current: '.$current.' new: '.$stock.'\n';
		//echo $narrative;
		
		if ($stock != 0) { //if stock is non-zero, update
			$product->set_stock_status('instock');
			$product->set_catalog_visibility('catalog');
			$product->set_stock_quantity($stock);
			
			//record change
			$wpdb->insert('wp_cf_products_alerts', 
				array(
					'alert_type' => 'stock',
					'alert_narrative' => $narrative,
					'alert_related_id' => $sku,
					)
				);
		} else {  //else if 0 set as out of stock and hide from catalogue
			$product->set_stock_status('outofstock');
			$product->set_stock_quantity($stock);
			$product->set_catalog_visibility('hidden');

			//record change
			$wpdb->insert('wp_cf_products_alerts', 
				array(
					'alert_type' => 'zero_stock',
					'alert_narrative' => $narrative,
					'alert_related_id' => $sku,
					)
				);
		}	*/
	}
	
	
	$product->save();
	wc_delete_product_transients( $id );
}

//used in sync products: to update price
function wc_set_product_sync_price($id = 0, $price = 0,  $sku = null, $name = '') {
		global $wpdb;
	$product = wc_get_product($id);

	$current = $product->get_regular_price();
	$price = str_replace(',', '', $price);
	if ($current != $price)  { 
		//if price is different (multiplier applied before function is called. price parameter is already multiplied)
		//if (strpos($price, ',') === false) {
			$narrative  = 'PRICE '.$name.': current: '.$current.' new: '.$price.' - '.$sku.'\n';
			
			$product->set_regular_price($price);
			$product->set_catalog_visibility('catalog');
			$product->save();

			//record change
			$wpdb->insert('wp_cf_products_alerts', 
			array(
				'alert_type' => 'price',
				'alert_narrative' => $narrative,
				'alert_related_id' => $sku,
				)
			);
	//	}
	} 
	

	wc_delete_product_transients( $id );

}


//display description of tabs on DHG wp-admin page default tab
function render_info_div () {
	
	$render = '<div class="outer-wrap" style="margin-top:25px">';
	$render .= '<ul class="tabs-desc">';
	$render .= '<li class="tabs-item"><strong>Sync with DHG</strong> - Deletes removed products and updates stock levels (runs as soon as tab is clicked)</li>';
	$render .= '<li class="tabs-item"><strong>List DHG Products</strong> - Lists products by category, indicates if new or listed and can download CSV with info</li>';
	$render .= '<li class="tabs-item"><strong>Map Categories</strong> - Shows map of DHG category IDs to WooCommerce IDs </li>';
	$render .= '<li class="tabs-item"><strong>CSV Operations</strong> - Ability to delete product, update stock levels, add products from CSV </li>';
	$render .= '<li class="tabs-item"><strong>Download Images</strong> - Download images</li>';
	$render .= '<li class="tabs-item"><strong>View DHG Item Changes</strong> - View changes to DHG stock on a particular day </li>';
	$render .= '</ul></div>';

	echo $render;

}



add_shortcode( 'testcall', 'spit_products' );

function spit_products () {
	global $wpdb;
    //check_products_file ();
    $json_p = grab_products_json ();
	echo '<pre>';
	var_dump($json_p);
	echo '</pre>';
	$args = array(
		//        'visibility' => 'catalog',
				'limit' => -1,
			);
	$products = wc_get_products( $args );
	// echo '<pre>';
	// var_dump($products);
	// echo '</pre>';
    foreach ($products as $product) {

		echo '-----------------------------------------------------------------------------</br>';
		
		$sku = $product->get_sku();
		$name = $product->get_name();
		$pid = $product->get_id();
		$image = wp_get_attachment_image_src( get_post_thumbnail_id($pid), 'single-post-thumbnail' );
		
	
		
		$json_p_key = array_search($sku, array_column($json_p, 'variant'));
		if (empty($json_p_key)) {
			$json_p_key = array_search($sku, array_column($json_p, 'code'));
		}

		echo 'SKU: '.$sku.' - '.$name.' -  KEY: '.$json_p_key.'</br>';

			if (strpos($image[0], 'no_image')) { 
				echo 'this has no image'.$name.'- '.$sku.'</br>';
				//if current image is NoImage.jpg see if image is there
				$new_image = $json_p[$json_p_key]['Image'];
				echo '<pre>';
				var_dump($new_image);
				echo '</pre>';
				if (!strpos($new_image, 'no_image')) {  //if json image for product does not contain no_image, then replace it. 
	
					//grab the image and upload it
					$newImgId = crb_insert_attachment_from_url($new_image);
					echo 'found new image for '.$name.'- '.$sku.'</br>';

					$product->set_image_id( isset( $newImgId ) ? $newImgId : "" );
					$product->save();
	
					// $wpdb->insert('wp_cf_products_alerts', 
					// 	array(
					// 		'alert_type' => 'image',
					// 		'alert_narrative' => 'Image Udpated',
					// 		'alert_related_id' => $sku,
					// 		)
					// 	);
					wc_delete_product_transients( $pid );
				} else {
					echo 'still no image'.$name.'- '.$sku.'</br>';
				}
			} else {
				echo 'no image change '.$name.'- '.$sku.'</br>';
			}
		
	}
}



add_shortcode( 'test-price', 'test_set_product_sync_price' );
//used in sync products: to update price
function test_set_product_sync_price($id = 0, $price = 0,  $sku = null, $name = '') {
	$oldprice = '1380,00';
	$new_price = $oldprice/3;
	// echo 'New '.$new_price.' - ';
	$new_price = (number_format(floatval(str_replace(',', '.', str_replace('.', '', $new_price))),2))*3.5;
   // $new_price = (number_format(floatval(str_replace(',', '.', str_replace('.', '', $new_price))),2));
	// echo 'Multiplied '.$new_price.' - ';
	 $new_price = round($new_price / 0.05) * 0.05;
	// echo 'Rounded '.$new_price.' - ';
	 $price = number_format($new_price, 2);

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	global $wpdb;
	$id = 15934;
	$sku = '09933';
 	$product = wc_get_product($id);
// 	$variations = $product->get_available_variations();
// //$variations_id = wp_list_pluck( $variations, 'variation_id' );

 //var_dump($product);

$current = $product->get_regular_price();
$price = str_replace(',', '', $price);

echo ' current: '.$current.' new: '.$price.' - '.$sku.'\n';
//if ($current != $price)  { 
	//if price is different (multiplier applied before function is called. price parameter is already multiplied)
	
		// $narrative  = 'PRICE '.$name.': current: '.$current.' new: '.$price.' - '.$sku.'\n';
		// echo $narrative;
		$product->set_regular_price($price);
		// $product->set_catalog_visibility('catalog');
	 	$sav = $product->save();
		// echo 'Updated: '.$sav;


		//record change
		// $wpdb->insert('wp_cf_products_alerts', 
		// array(
		// 	'alert_type' => 'price',
		// 	'alert_narrative' => $narrative,
		// 	'alert_related_id' => $sku,
		// 	)
		// );

	
//} 


wc_delete_product_transients( $id );

}
