<?php
/*
Plugin Name: CountryFlowers Fresh Flowers Checkout
Plugin URI: https://www.stefcordina.com/
Description: Amends checkout fields when fresh flowers are present and adds extra fields to the order.
Version: 1.0
Author: Stef Cordina (AI)
Author URI: https://www.stefcordina.com/
License: GPL2
*/



add_action( 'woocommerce_before_order_notes', 'add_fresh_flowers_message' );
function add_fresh_flowers_message( $checkout ) { 

	// Set $cat_in_cart to false
	$cat_in_cart = false;
	
	// Loop through all products in the Cart        
	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
        // echo '<pre>';
        // var_dump($cart_item['product_id'] );
        // echo '</pre>';
       
		// If Cart has category "download", set $cat_in_cart to true
		if ( has_term( 'fresh-flowers', 'product_cat', $cart_item['product_id'] ) ) {
			$cat_in_cart = true;
			break;
		}
		
	}
	// if fresh flowers category is in cart, add the field
	if ( $cat_in_cart ) {
		wc_print_notice( 'Write your custom card message.', 'notice' );
		woocommerce_form_field( 'fresh_flowers_message', array(        
		'type' => 'textarea',        
		'class' => array( 'form-row-wide' ),        
		'label' => 'Card Message',        
		'placeholder' => 'Enter your message to appear on the card',        
		'required' => false    
      ), $checkout->get_value( 'fresh_flowers_message' ) ); 
      
      //and add the delivery date selection

     
      woocommerce_form_field( 'delivery_date', array(
           'type'          => 'text',
           'class'         => array('form-row-wide'),
           'id'            => 'datepicker',
           'required'      => true,
           'label'         => __('Select Delivery Date'),
           'placeholder'       => __('Click to open calendar'),
           ), $checkout->get_value( 'delivery_date' ) );
      

	}
 }

 add_filter( 'woocommerce_email_order_meta_fields', 'add_fresh_flowers_message_to_order_email', 10, 3 );
 function add_fresh_flowers_message_to_order_email( $fields, $sent_to_admin, $order ) {
	 $fields['fresh_flowers_message'] = array(
		 'value' => get_post_meta( $order->get_id(), 'fresh_flowers_message', true ) ,
	 );
	 return $fields;
 }


 add_action( 'woocommerce_new_order', 'ff_new_order' );
function ff_new_order( $order_id ) {
  $tp_preferred_slot_nr = isset( $_POST['fresh_flowers_message'] ) ? $_POST['fresh_flowers_message'] : 0;
    add_post_meta( $order_id, 'fresh_flowers_message', $tp_preferred_slot_nr, true ); 
}

 add_action( 'woocommerce_email_after_order_table', 'woocommerce_email_after_order_table_func' );
function woocommerce_email_after_order_table_func( $order ) {
	?>

	<h3>Additional information</h3>
	<table>
		<tr>
			<td>Card message: </td>
			<td><?php echo wptexturize( get_post_meta( $order->id, 'fresh_flowers_message', true ) ); ?></td>
		</tr>
      <tr>
			<td>Delivery date: </td>
			<td><?php echo wptexturize( get_post_meta( $order->id, '_delivery_date', true ) ); ?></td>
		</tr>
	</table>

	<?php
}



/**
 * Display card message field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'display_card_message', 10, 1 );

function display_card_message($order){

	if (!empty(get_post_meta( $order->get_id(), 'fresh_flowers_message', true ))) {
		echo '<p><strong>'.__('Card Message').':</strong> <br/>' . get_post_meta( $order->get_id(), 'fresh_flowers_message', true ) . '</p>';
   }

   if (!empty(get_post_meta( $order->get_id(), '_delivery_date', true ))) {
      echo '<p><strong>Delivery Date:</strong> ' . get_post_meta( $order->get_id(), '_delivery_date', true ) . '</p>';
   }
    
}

function cf_add_images_woocommerce_emails( $output, $order ) {
	
	// set a flag so we don't recursively call this filter
	static $run = 0;
  
	// if we've already run this filter, bail out
	if ( $run ) {
		return $output;
	}
  
	$args = array(
		'show_image'   	=> true,
      'image_size'    => array( 100, 100 ),
      'show_sku'      => true,
	);
  
	// increment our flag so we don't run again
	$run++;
  
	// if first run, give WooComm our updated table
	return $order->email_order_items_table( $args );
}
add_filter( 'woocommerce_email_order_items_table', 'cf_add_images_woocommerce_emails', 10, 2 );






add_action( 'woocommerce_checkout_process', 'ff_validate_new_checkout_fields' );
   
function ff_validate_new_checkout_fields() {   
   foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

		// If Cart has category "download", set $cat_in_cart to true
		if ( has_term( 'fresh-flowers', 'product_cat', $cart_item['product_id'] ) ) {
			$cat_in_cart = true;
			break;
		}
		
	}
   if ( $cat_in_cart ) {
      if ( isset( $_POST['delivery_date'] ) && empty( $_POST['delivery_date'] ) ) wc_add_notice( __( 'Please select the Delivery Date' ), 'error' );
   }
   
}
  
// -------------------------------
// 2. Load JQuery Datepicker
  
add_action( 'woocommerce_after_checkout_form', 'bbloomer_enable_datepicker', 10 );
   
function bbloomer_enable_datepicker() {
     
  ?>
  
   <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  
  <?php    
     
}
  
// -------------------------------
// 3. Load Calendar Dates
  
add_action( 'woocommerce_after_checkout_form', 'load_calendar_dates', 20 );
   
function load_calendar_dates( $available_gateways ) {
  
   ?>
  
   <script type="text/javascript">
  
  jQuery(document).ready(function($) {
        var dates = ["14/05/2023"]; 

		function DisableDates(date) {
			var string = jQuery.datepicker.formatDate('dd/mm/yy', date);
			return [dates.indexOf(string) == -1];
		}

         $('#datepicker').click(function() {
  
            $('#datepicker').datepicker({ 
			   beforeShowDay: DisableDates,
               dateFormat: 'dd-mm-yy',
               maxDate: "+2m",
               minDate: 1, 
            }).datepicker( "show" );
              
         });
  
      });
  
   </script>
  
   <?php
   
}
  
// -------------------------------
// 4. Save & show date as order meta
  
add_action( 'woocommerce_checkout_update_order_meta', 'save_date_weight_order' );
  
function save_date_weight_order( $order_id ) {
     
    global $woocommerce;
     
    if ( $_POST['delivery_date'] ) update_post_meta( $order_id, '_delivery_date', esc_attr( $_POST['delivery_date'] ) );
     
}
  

