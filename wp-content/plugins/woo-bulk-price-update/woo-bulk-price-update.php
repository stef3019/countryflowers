<?php
/**
 * Plugin Name: Woocommerce Bulk Price Update
 * Description: WooCommerce percentage pricing by Category allows you to Change WooCommerce products Price By Category.
 * Version: 2.1.8
 * Author: technocrackers
 * Author URI: https://technocrackers.com
 * WC tested up to: 4.6.0 
 */
require_once(plugin_dir_path(__FILE__).'js/techno_live.php');
class woocommerce_bulk_price_update
{
	function __construct() 
	{
        $this->add_actions();
    }
	private function add_actions() 
	{
		add_action( 'admin_menu', array($this,'woocommerce_bulk_price_update_setup') );
		add_action('wp_ajax_techno_change_price_percentge', array($this,'techno_change_price_percentge_callback'));
		add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array($this,'woocommerce_bulk_price_setting'));
		add_action('wp_ajax_techno_change_price_product_ids', array($this,'techno_change_price_product_ids_callback'));
		add_action('wp_ajax_techno_get_products', array($this,'techno_products_callback'));
	}
	function woocommerce_bulk_price_update_setup() 
	{
		add_submenu_page( 'edit.php?post_type=product', 'woocommerce-bulk-price-update', 'WC Change Price', 'manage_options', 'woocommerce-bulk-price-update', array($this,'woocommerce_bulk_price_update_callback_function') ); 
	}
	function woocommerce_bulk_price_setting($links) 
	{
		return array_merge(array('<a href="'.esc_url(admin_url( '/edit.php?post_type=product&page=woocommerce-bulk-price-update')).'">Settings</a>'),$links);
	}
	function techno_products_callback()
	{
		$return = array();
		$search_results = new WP_Query(array('post_type' => 'product', 's'=> $_REQUEST['s'],'paged'=> $_REQUEST['page'],'posts_per_page' => 50));
		if( $search_results->have_posts() ) :
			while( $search_results->have_posts() ) : $search_results->the_post();	
				$return[] = array('id'=>get_the_ID(), 'text'=>get_the_title());
			endwhile;
		endif;
		echo json_encode(array('results'=>$return,'count_filtered'=>$search_results->found_posts,'page'=>$_REQUEST['page'],'pagination' => array( "more"=> true )));
		exit();
	}
	function techno_wc_bulk_price_update_pro_html() 
	{       
		$pugin_path =  plugin_dir_url( __FILE__ ); echo '
		<form method="POST">
    	<div class="col-50">
            <h2>Woocommerce Bulk Price Update</h2>
            <h4 class="paid_color">	Woo-commerce / Premium Features:</h4>
			<p class="paid_color">01. You can update price of variable products.</p>
			<p class="paid_color">02. Update product price with fixed amount/price.</p>
			<p class="paid_color">03. You can update price for specific products.</p>
            <p><label for="techno_wc_bulk_price_updatekey">License Key : </label><input class="regular-text" type="text" id="techno_wc_bulk_price_update_license_key" name="techno_wc_bulk_price_update_license_key"></p>
            <p class="submit">
                <input type="submit" name="activate_license_techno" value="Activate" class="button button-primary">
            </p>
        </div>
        <div class="col-50">
			<a href="https://technocrackers.com/woo-bulk-price-update/" target="_blank">
				<img src="'.$pugin_path.'img/premium.png">
			</a>
			<div class="content_right">
				<p>Buy Activation Key form Here..</p>
				<p><a href="https://technocrackers.com/woo-bulk-price-update/" target="_blank">Buy Now...</a></p>
			</div>
		</div>
        </form>';
	}
	function woocommerce_bulk_price_update_callback_function() 
	{
		ini_set( 'memory_limit', '2048M' );
		defined( 'WP_MEMORY_LIMIT' ) or define( 'WP_MEMORY_LIMIT', '2048M' );
		$categories = get_terms('product_cat', array('post_type' => array('product'),'hide_empty' => true,'orderby' => 'name','order' => 'ASC'));
	    $pugin_path =  plugin_dir_url( __FILE__ ); 
	    wp_enqueue_style('bootstrap', $pugin_path.'css/bootstrap-3.3.2.min.css');
	    wp_enqueue_style('multiselect', $pugin_path.'css/bootstrap-multiselect.css');
	    wp_enqueue_style('bulkprice-custom-css', $pugin_path.'css/bulkprice-custom.css'); 
	    wp_enqueue_script('bootstrap',$pugin_path.'js/bootstrap-3.3.2.min.js');       
		wp_enqueue_script('multiselect',$pugin_path.'js/bootstrap-multiselect.js'); echo '
		<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
		<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
		<div class="bulk-title">
			<h1>Bulk Price Change</h1>
		</div>
		<div class="wrap tab_wrapper bulk-content-area">
			<div class="main-panel">
				<div id="tab_dashbord" class="techno_main_tabs active"><a href="#dashbord">Dashbord</a></div>
				<div id="tab_premium" class="techno_main_tabs"><a href="#premium">Premium</a></div>
			</div>
			<div class="boxed" id="percentage_form">
				<div class="techno_tabs tab_dashbord">'; 
			        $lic_chk = new techno_wc_bulk_price_update_lic_class();
					if (isset($_REQUEST['deactivate_techno_wc_bulk_price_update_license'])) 
				    {
				        if($lic_chk->techno_wc_bulk_price_update_deactive())
				        {
				        	echo '<div id="message" class="updated fade"><p><strong>You license Deactivated successfuly...!!!</strong></p></div>';
				        }
				        else
				        {
				        	echo '<div id="message" class="updated fade" style="border-left-color:#a00;"><p><strong>'.$lic_chk->err.'</strong></p></div>';
				        }
				    }
				    $lic_chk_stateus = $lic_chk->is_techno_wc_bulk_price_update_act_lic();
				    if (isset($_REQUEST['activate_license_techno']) && isset($_POST['techno_wc_bulk_price_update_license_key']))
			    	{
						$license_key = $_POST['techno_wc_bulk_price_update_license_key']; 
						$lic_chk_stateus = $lic_chk->techno_wc_bulk_price_update_act_call($license_key);
					} echo'
					<form method="post">';
						wp_nonce_field('update-prices'); echo '
						<table class="form-table">';
							if($lic_chk_stateus){ echo '
								<tr valign="top">
									<th scope="row">Price Change Type:<br/></th>
									<td>
										<input type="radio" checked value="by_percent" name="price_type_by_change" id="by_percent">
										<label for="by_percent">Percentage</label>
										<input type="radio" value="by_fixed" name="price_type_by_change" id="by_fixed">
										<label for="by_fixed">Fixed</label>		
									</td>
								</tr>								
								<tr valign="top">
									<th scope="row">Amount:<br/></th>
									<td>
										<input type="number" name="percentage" id="percentage" value="0" step="0.01"/><br />
										<span id="errmsg"></span>					
									</td>
								</tr>';
							}
							else{ echo '
								<tr valign="top">
									<th scope="row">Percentage:<br/><small>(Enter pricing percentage)</small></th>
									<td>
										<input style="display:none;" type="radio" checked value="by_percent" name="price_type_by_change" id="by_percent">
										<input type="number" name="percentage" id="percentage" value="0" />%<br />
										<span id="errmsg"></span>					
									</td>
								</tr>';
							}echo '
							<tr>';
							if($lic_chk_stateus){ echo '
								<th>
									Please select between following methods:<br>
								</th>
								<td>
									<input type="radio" checked value="by_categories" name="price_change_method" id="by_categories">
									<label for="by_categories">Categories</label>
									<input type="radio" value="by_products" name="price_change_method" id="by_products">
									<label for="by_products">Specific Products</label>		
								</td>';
							}
							else{
								echo '<input style="display:none;" type="radio" checked value="by_categories" name="price_change_method" id="by_categories">';
							}echo '
							</tr>														
							<tr id="method_by_categories" class="method_aria_tc" style="display: none;">
								<th>
									Please select categories<br>
								</th>
								<td>
								<select id="techno_product_select" name="techno_product_select[]" multiple="multiple">';
									foreach ($categories as $key => $cat) 
									{
									    echo '<option value="'.$cat->term_id.'">'.$cat->name.'</option>';
									} echo '
								</select>
								</td>
							</tr>';
							if($lic_chk_stateus){ echo '												
							<tr id="method_by_products" class="method_aria_tc" style="display: none;">
								<th>
									Please select Products<br>
								</th>
								<td>
									<select multiple id="add_products"  class="chosen-select"></select>
								</td>
							</tr>';
							}echo '
							<tr>
								<th scope="row">Round Up Prices.</th>
								<td>
									<input type="checkbox" value="price_rounds_point" name="price_rounds_point" id="price_rounds_point"  class="percentge-submit"><label class="lbl_tc" for="price_rounds_point">( $5.2 => $5 or $5.9 => $6 )</label>
								</td>
							</tr>
							<tr>
								<th scope="row">Increase Prices</th>
								<td>
									<input type="radio" checked value="increase-percentge" name="price_change_type" id="increase-percentge-submit"  class="percentge-submit"><label class="lbl_tc" for="increase-percentge-submit">(Regular price and sale price)</label>
								</td>
							</tr>
							<tr>
								<th scope="row">Decrease Prices</th>
								<td>
									<input type="radio" value="discount-percentge" name="price_change_type" id="discount-percentge-submit"  class="percentge-submit"><label class="lbl_tc" for="discount-percentge-submit">(Regular price and sale price)</label>
								</td>
							</tr>';
							if($lic_chk_stateus){ echo '												
							<tr>
								<th>
									Run as dry run?<br>									
								</th>
								<td>
									<input type="checkbox" value="tc_dry_run" name="tc_dry_run" id="tc_dry_run">
									<label class="lbl_tc" for="tc_dry_run"><b>If checked, no changes will be made to the database, allowing you to check the results beforehand.</b></label>
								</td>
							</tr>';
							}echo '
						</table>
						<p class="submit">
							<label class="button button-primary" id="percentge_submit" onclick="techno_chage_price();">Submit</label>
						</p>
						<div style="display:none;" id="loader">
							<progress class="techno-progress" max="100" value="0"></progress>
						</div>
						<div style="display:none;" id="update_product_results">
					        <table class="widefat striped">
						        <thead>
						        <tr>
							        <td>No.</td>
							        <td>Thumb</td>
							        <td>Product ID</td>
							        <td>Product Name</td>
							        <td>Product Type</td>
							        <td>Old Price <span class="dashicons dashicons-arrow-right-alt"></span>New Price</td>
						        </tr>
						        </thead>
						        	<tbody id="update_product_results_body">
						        	</tbody>
	    					</table>
						</div>
					</form>
				</div>
				<div class="techno_tabs tab_premium" style="display:none;">';				
					if($lic_chk_stateus)
					{
						if (isset($_REQUEST['activate_license_techno']))
					    {
					    	echo '<div id="message" class="updated fade"><p><strong>You license Activated successfuly...!!!</strong></p></div>';
					    } echo'
						<form method="POST">	    
							<div class="col-50">
								<h2> Thank You Phurchasing ...!!!</h2>
								<h4 class="paid_color">Deactivate Yore License:</h4>
								<p class="submit">
					               	<input type="submit" name="deactivate_techno_wc_bulk_price_update_license" value="Deactive" class="button button-primary">
					           	</p>
							</div>
			            </form>';
					}
					else
					{
						$this->techno_wc_bulk_price_update_pro_html();
						if(!empty($lic_chk->err)){
						    echo '<div id="message" class="updated fade" style="border-left-color:#a00;"><p><strong>'.$lic_chk->err.'</strong></p></div>';
						}
					} echo '
				</div>
			</div>
		</div>';?>
		<script type="text/javascript">
			var ajaxurl = "<?php echo admin_url('admin-ajax.php')?>";
			var wp_product_update_ids = { action: 'techno_change_price_percentge'};		
			var wp_product_get_ids = { action: 'techno_change_price_product_ids'};		
			var arr = [];
		   	var opration_type='';
		   	var price_type_by_change='';
		   	var percentage='';
		   	var tc_dry_run = '';
		   	var price_rounds_point='';
			function tc_start_over() 
			{					
				jQuery('#percentge_submit').css({'opacity':0.5});
				jQuery('#percentge_submit').attr('disable',true);
				jQuery('#update_product_results_body').html('');
				jQuery('#loader').show();				
			}
			function techno_chage_price() 
			{				
				Array.prototype.chunk = function(n) {
					if (!this.length) {
						return [];
					}
					return [this.slice(0, n)].concat(this.slice(n).chunk(n));
				};
				jQuery('.techno-progress').attr('value',0);
				if(arr.length == 0)
				{
					percentage=jQuery("#percentage").val();	
					if(percentage > 0)
					{	
						opration_type = jQuery("input[name='price_change_type']:checked").val();	
						price_type_by_change = jQuery("input[name='price_type_by_change']:checked").val();	
						price_rounds_point = (jQuery("#price_rounds_point").is(":checked")) ? 'true' : 'false';
						tc_dry_run = (jQuery("#tc_dry_run").is(":checked")) ? 'true' : 'false';
						if(jQuery("input[name='price_change_method']:checked").val()=='by_categories')
						{
					        if(jQuery('#techno_product_select').val() != null){
					        	tc_start_over();
					        	wp_product_get_ids['cat_ids'] = jQuery('#techno_product_select').val();	
						        wp_product_get_ids['nonce'] = "<?php echo wp_create_nonce('wporg_product_ids') ?>";			
								jQuery.post( ajaxurl, wp_product_get_ids, function(res_cat) 
							   	{
							   		arr = JSON.parse(res_cat);
							   		arr = arr.chunk(5);
									recur_loop();
									jQuery('.techno-progress').attr('max',arr.length);
								});
					        }
					        else{
								alert('Please select a Category...!!');
					        }			
						}
						else{
							if(jQuery('#add_products').val() != null){
								arr = jQuery('#add_products').val();
							   	arr = arr.chunk(5);
								tc_start_over();
								recur_loop(); 
								jQuery('.techno-progress').attr('max',arr.length);
							}
							else{
								alert('Please select a Product...!!');								
							}
						}
					}			
					else
					{
						alert('Please provide a Amount more-than Zero...!!');
					}
				}				
			}	
			var recur_loop = function(i) 
			{
			    var num = i || 0; 
			    if(num < arr.length) 
			    {
			        wp_product_update_ids['product_id'] = arr[num];
			        wp_product_update_ids['opration_type'] = opration_type;
			        wp_product_update_ids['price_type_by_change'] = price_type_by_change;
			        wp_product_update_ids['percentage'] = percentage;
			        wp_product_update_ids['price_rounds_point'] = price_rounds_point;
			        wp_product_update_ids['tc_dry_run'] = tc_dry_run;
			        wp_product_update_ids['tc_req_count'] = i;
			        wp_product_update_ids['nonce'] = "<?php echo wp_create_nonce('wporg_product_update_ids') ?>";
				   	jQuery.post( ajaxurl, wp_product_update_ids, function(response) 
				   	{
				   		jQuery('#update_product_results').show();
				   		var count=num+1;
			        	recur_loop(num+1);
				   		jQuery('.techno-progress').attr('value',count);
				   		jQuery('#update_product_results_body').append(response);
					});  
			    }
			    else
			    {
			    	arr = [];
					jQuery('#loader').hide();
					if(tc_dry_run=='true'){
						alert('Dry Run Complete...!!');
					}
					else{
						jQuery('#techno_product_select').val('');
						jQuery("#percentage").val('');	
						jQuery('#techno_product_select').multiselect('refresh');
						if(jQuery('.chosen-select').length > 0){
							jQuery('.search-choice-close').trigger('click');
						}
						alert('Operation Complete...!!');
					}
					jQuery('#percentge_submit').css({'opacity':''});
					jQuery('#percentge_submit').removeAttr('disable');
			    }
			};
			jQuery(document).ready(function(jQuery) 
			{
				jQuery('#method_'+jQuery('input[name="price_change_method"]').val()).show();
				jQuery('input[name="price_change_method"]').change(function(e)
				{
					jQuery('.method_aria_tc').hide();
					jQuery('#method_'+jQuery(this).val()).show();
				});
				jQuery("#techno_product_select").multiselect({enableClickableOptGroups: true,enableCollapsibleOptGroups: true,enableFiltering: true,includeSelectAllOption: true });
	            jQuery("select.chosen-select").select2({
			        ajax: {
					    url: ajaxurl,
					    dataType: 'json',
					    delay: 250,
					    data: function (params) {
					      	return {
					        	s: params.term,
					        	action: 'techno_get_products',
					        	page: params.page || 1
					      	};
					    },
					    processResults: function (data, params) {
					      	params.page = params.page || 1;
						    return {
						        results: data.results,
						        pagination: {
						            more: (params.page * 50) < data.count_filtered
						        }
						    };
					    },
					    cache: true
					},				
			        placeholder: "Select Products...",
			        width: "90%",
	  				minimumInputLength: 0,
					templateResult: formatRepo,
					templateSelection: formatRepoSelection
			    });
			  	jQuery("#percentage").keypress(function(e) 
			  	{
					if (e.keyCode === 46 && this.value.split('.').length === 2)
					{
						return false;
					}
			   	});
			   	jQuery('div.techno_main_tabs').click(function(e){
			   		jQuery('.techno_main_tabs').removeClass('active');
			   		jQuery(this).addClass('active');
					jQuery('.techno_tabs').hide();
					jQuery('.'+this.id).show();
				});
				if(window.location.hash)
			  	{
				    var tab_active=window.location.hash.substring(1);
				    jQuery("#tab_"+tab_active).trigger('click');   
			  	}
			});			
			function formatRepo (repo) {
			  if (repo.loading) {
			    return repo.text;
			  }
			  var $container = jQuery("<div class='select2-result-repository clearfix'><div class='select2-result-repository__meta'><div class='select2-result-repository__title'>"+repo.text+"</div></div></div>");
			  return $container;
			}
			function formatRepoSelection (repo) {
			  return repo.name || repo.text;
			}
		</script><?php
	}
	function techno_change_price_product_ids_callback()
	{
		if(isset($_POST["cat_ids"]) && $_POST["cat_ids"]!='' && isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'wporg_product_ids'))
		{
			$posts_array = get_posts(array('fields'=> 'ids','numberposts' => -1,'post_type' => 'product','status'=>'publish','order' => 'ASC','tax_query' => array(array('taxonomy' => 'product_cat','field' => 'term_id','terms' => $_POST["cat_ids"]))));
			echo json_encode($posts_array);
		}
		exit();
	} 
	function techno_change_price_percentge_callback() 
	{	
		if(isset($_POST["product_id"]) && !empty($_POST["product_id"]) && isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'wporg_product_update_ids'))
		{
			$product_count = $_POST['tc_req_count'];
			$product_count = $product_count+1;
			$product_count = 5 * $product_count;
			$temp_i=4;
			foreach ($_POST["product_id"] as $key => $product_id) 
			{				
				$res=array(); 
				$opration_type= sanitize_text_field(trim($_POST["opration_type"]));
				$price_type_by_change = sanitize_text_field(trim($_POST["price_type_by_change"]));
				$lic_obj = new techno_wc_bulk_price_update_lic_class();
			    $percentage  = trim($_POST["percentage"]);
				$price_rounds_point = sanitize_text_field(trim($_POST["price_rounds_point"]));
				$tc_dry_run = sanitize_text_field(trim($_POST["tc_dry_run"]));
				$product = wc_get_product(intval(trim($product_id)));
				$lic_state = $lic_obj->is_techno_wc_bulk_price_update_act_lic();
				$product_id = $product->get_id();			
				$currency = get_woocommerce_currency_symbol();
		        $thumbnail = wp_get_attachment_image($product->get_image_id(), array(50,50));
		        $html = '<td>'.(($thumbnail) ? $thumbnail : wc_placeholder_img(array(50,50))).'</td>';
		        $html .= '<td>'.$product_id.'</td>';
		        $html .= '<td>'.$product->get_name().'</td>';
		        $html .= '<td>'.$product->get_type().'</td>';
		        $html .= '<td><table><tbody>';
				if(!$product->is_type('variable')) 
				{
					$res['is_type'] = 'simple';
					$product_prc = get_post_meta( $product->get_id(), '_price', true);
					$sale_price = get_post_meta( $product->get_id(), '_sale_price', true);
					$regular_price = get_post_meta( $product->get_id(), '_regular_price', true);		    			
					$res['old_price'] = $product_prc;
					if (!empty($sale_price))
					{
						if($price_type_by_change=='by_percent'){
							$sale_price_update = (float) $sale_price  * ( $percentage / 100 );
							$regular_price_update = (float) $regular_price  * ( $percentage / 100 );
						}
						elseif ($price_type_by_change=='by_fixed' && $lic_state)
						{
							$sale_price_update = $percentage;
							$regular_price_update = $percentage;
						}
						if($opration_type=="increase-percentge")
						{
							$sale_product_prc=$sale_price+$sale_price_update;
							$regular_product_prc=$regular_price+$regular_price_update;				
						}
						elseif($opration_type=="discount-percentge")
						{
							$sale_product_prc=$sale_price-$sale_price_update;
							$regular_product_prc=$regular_price-$regular_price_update;	
						}
						if($price_rounds_point == 'true'){
							$sale_product_prc = round($sale_product_prc);
							$regular_product_prc = round($regular_product_prc);
						}
						$sale_product_prc = round($sale_product_prc, 2);
						$regular_product_prc = round($regular_product_prc, 2);
						if($tc_dry_run == 'false'){
							update_post_meta( $product->get_id(), '_sale_price', $sale_product_prc);
							update_post_meta( $product->get_id(), '_regular_price', $regular_product_prc);
							update_post_meta( $product->get_id(), '_price', $sale_product_prc );
						}
						$res['new_price'] = $sale_product_prc;
					}
					else
					{
						if($price_type_by_change=='by_percent'){
							$regular_price_update = (float) $regular_price  * ( $percentage / 100 );
						}elseif ($price_type_by_change=='by_fixed' && $lic_state){
							$regular_price_update = $percentage;
						}					
						if($opration_type=="increase-percentge")
						{
							$regular_product_prc=$regular_price+$regular_price_update;				
						}
						if($opration_type=="discount-percentge")
						{
							$regular_product_prc=$regular_price-$regular_price_update;	
						}					
						if($price_rounds_point == 'true'){
							$regular_product_prc = round($regular_product_prc);
						}
						$regular_product_prc = round($regular_product_prc, 2);					
						if($tc_dry_run == 'false'){
							update_post_meta( $product->get_id(), '_regular_price', $regular_product_prc);
							update_post_meta( $product->get_id(), '_price', $regular_product_prc );
						}
						$res['new_price']= $regular_product_prc;
					} 
					$html .= '<tr><td><strong>Old Price:</strong></td><td><code>'.$currency.' '.$res['old_price'].'</code></td></tr><tr><td><strong>New Price:</strong></td><td><code>'.$currency.' '.$res['new_price'].'</code></td></tr>';
			 	} 
				elseif($lic_state) 
				{	
					$res['is_type']= 'variable';
					$var_new_price=array();
					$variation_count=0;
			 		foreach ( $product->get_children() as $child_id ) 
			 		{	 			
						$variation_res=array(); 
			 			$variation_count++;
			    		$product_prc = get_post_meta( $child_id, '_price', true);
						$variation_res['old_price']= $product_prc;
			    		$sale_price = get_post_meta( $child_id, '_sale_price', true);	
						$regular_price = get_post_meta( $child_id, '_regular_price', true);
			    		if (!empty($sale_price))
			    		{
							if($price_type_by_change=='by_percent'){
				    			$sale_price_update = (float) $sale_price  * ( $percentage / 100 );
								$regular_price_update = (float) $regular_price  * ( $percentage / 100 );
							}
							elseif ($price_type_by_change=='by_fixed' && $lic_state)
							{
			    				$sale_price_update = $percentage;
								$regular_price_update = $percentage;
							}
							if($opration_type=="increase-percentge")
							{
								$sale_product_prc=$sale_price+$sale_price_update;
								$regular_product_prc=$regular_price+$regular_price_update;				
							}
							if($opration_type=="discount-percentge")
							{
								$sale_product_prc=$sale_price-$sale_price_update;
								$regular_product_prc=$regular_price-$regular_price_update;	
							}						
							if($price_rounds_point == 'true'){
								$sale_product_prc = round($sale_product_prc);
								$regular_product_prc = round($regular_product_prc);
							}							
							$sale_product_prc = round($sale_product_prc, 2);
							$regular_product_prc = round($regular_product_prc, 2);
							if($tc_dry_run == 'false'){
								update_post_meta( $child_id, '_sale_price', $sale_product_prc);
								update_post_meta( $child_id, '_regular_price', $regular_product_prc);
								update_post_meta( $child_id, '_price', $sale_product_prc );
								$var_new_price[]=$sale_product_prc;
							}
							$variation_res['new_price']= $sale_product_prc;
			    		}
			    		else
			    		{
							if($price_type_by_change=='by_percent'){
			    				$regular_price_update = (float) $regular_price  * ( $percentage / 100 );
							}elseif($price_type_by_change=='by_fixed' && $lic_state){
								$regular_price_update = $percentage;
							}
							if($opration_type=="increase-percentge"){
								$regular_product_prc=$regular_price+$regular_price_update;				
							}elseif($opration_type=="discount-percentge"){
								$regular_product_prc=$regular_price-$regular_price_update;	
							}						
							if($price_rounds_point == 'true'){
								$regular_product_prc = round($regular_product_prc);
							}
							$regular_product_prc = round($regular_product_prc, 2);
							if($tc_dry_run == 'false'){
								update_post_meta( $child_id, '_regular_price', $regular_product_prc);
								update_post_meta( $child_id, '_price', $regular_product_prc );
								$var_new_price[]=$regular_product_prc;
							}
							$variation_res['new_price']= $regular_product_prc;
			    		}
			    		$res['variation_'.$variation_count] = $variation_res;
					}
					foreach ($res as $key => $value) 
		        	{
		        		if($key !='is_type')
		        		{
		        			$html .= '<tr><td><strong>Old Price:</strong></td><td><code>'.$currency.' '.$value['old_price'].'</code></td></tr><tr><td><strong>New Price:</strong></td><td><code>'.$currency.' '.$value['new_price'].'</code></td></tr>';
		        		}
		        	}
		        	update_post_meta( $product->get_id(), '_price', min($var_new_price));
				}
				if(sizeof($res)==0){
					$html .= '<tr><td><a href="https://technocrackers.com/woo-bulk-price-update/" target="_blank">Buy Premium!</a></td></tr>';
				}
		        $html .= '</tbody></table></td>';
				$product->save();
				$product_count_1 = $product_count - $temp_i;
				echo '<tr><td>'.$product_count_1.'</td>'.$html.'</tr>';
				$temp_i--;
			}	        
		}
	    exit();
	}
}
new woocommerce_bulk_price_update();?>