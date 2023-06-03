<?php
add_action( 'wp_enqueue_scripts', 'spring_plant_child_theme_enqueue_styles', 1000 );
function spring_plant_child_theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'spring-plant-main' ) );
   
}

add_action( 'after_setup_theme', 'spring_plant_child_theme_setup');
function spring_plant_child_theme_setup(){
    $language_path = get_stylesheet_directory() .'/languages';
    if(is_dir($language_path)){
        load_child_theme_textdomain('spring-plant', $language_path );
    }
}
// if you want to add some custom function

//hide sku from the page
function sv_remove_product_page_skus( $enabled ) {
    if ( ! is_admin() && is_product() ) {
        return false;
    }

    return $enabled;
}
add_filter( 'wc_product_sku_enabled', 'sv_remove_product_page_skus' );

//remove description tab
add_filter( 'woocommerce_product_tabs', 'wc_remove_description_tab', 20, 1 );

function wc_remove_description_tab( $tabs ) {

	// Remove the description tab
    if ( isset( $tabs['description'] ) ) unset( $tabs['description'] );      	    
    return $tabs;
}



add_filter( 'woocommerce_billing_fields', 'require_shipping_phone_field');
function require_shipping_phone_field( $fields ) {
    $fields['shipping_phone_field']['required'] = false;
return $fields;
}


/**
 * Disable out of stock variations
 * https://github.com/woocommerce/woocommerce/blob/826af31e1e3b6e8e5fc3c1004cc517c5c5ec25b1/includes/class-wc-product-variation.php
 * @return Boolean
 */
function wcbv_variation_is_active( $active, $variation ) {
	if( ! $variation->is_in_stock() ) {
		return false;
	}
	return $active;
}
add_filter( 'woocommerce_variation_is_active', 'wcbv_variation_is_active', 10, 2 );

/*defer emails from sending until after the order is sent through, which speeds things up:*/
add_filter( 'woocommerce_defer_transactional_emails', '__return_true' );

add_action('wp_head', 'pn_add_analytics');
function pn_add_analytics() { ?>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-178642334-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-178642334-1');
</script>

<?php }




add_filter( 'wp_nav_menu_items', 'add_loginout_link_in_menu', 10, 2 );

function add_loginout_link_in_menu( $items, $args ) {

   if (is_user_logged_in() && $args->theme_location == 'primary') {
       $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-loginout" id="menu-item-logout"><a class="x-menu-link" href="'. wp_logout_url( get_permalink( woocommerce_get_page_id( 'myaccount' ) ) ) .'">Log Out</a></li>';
   }
   elseif (!is_user_logged_in() && $args->theme_location == 'primary') {
       $items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-loginout" id="menu-item-logout"><a class="x-menu-link" href="' . get_permalink(woocommerce_get_page_id( 'myaccount' )) . '">Log In</a></li>';
   }
   return $items;

}


function stay_on_page_after_logout( $logouturl, $redir )
{
    return $logouturl . '&amp;redirect_to=' . get_permalink();
}
add_filter( 'logout_url', 'stay_on_page_after_logout', 10, 2 );


// get the the role object
$admin_role = get_role( 'shop_manager' );
// grant the unfiltered_html capability
$admin_role->add_cap( 'wpseo_manage_options', true );
 
/**
 * Add edited date column on product page (header)
 */
function add_edited_date_product_column_header( $columns ) {	
    //add column header
    $columns['edited_date'] = __( 'Edited date', 'woocommerce' );

    return $columns;
}
add_filter( 'manage_edit-product_columns', 'add_edited_date_product_column_header', 10, 1 );


/**
 * Add edited date column on product page (content)
 */
function add_edited_date_product_column_content( $column, $postid ) {
    // add column content
    if ( $column == 'edited_date' ) {	
        // Get product object
        $product = wc_get_product( $postid );
		
        // Get product date modified
        $date_modified = $product->get_date_modified();
		
        // Echo output
        echo 'Modified' . '<br><span title="' . $formatted_date = date( 'Y/m/d h:i:s a', strtotime( $date_modified ) ) . '">' . $formatted_date = date( 'd F Y h:i', strtotime( $date_modified ) ) . '</span>';
    }
}
add_action( 'manage_product_posts_custom_column', 'add_edited_date_product_column_content', 10, 2 );


/**
 * Make the edited date column on the product page sortable
 */
function edited_date_product_column_sortable( $columns ) {
    $columns['edited_date'] = 'edited_date';	

    //To make a column 'un-sortable' remove it from the array
    //unset($columns['date']);
	
    return $columns;
}
add_filter( 'manage_edit-product_sortable_columns', 'edited_date_product_column_sortable', 10, 1 );


/**
 * Orderby edited_date (product)
 */
function edited_date_product_column_orderby( $query ) {
    if( ! is_admin() )
        return;

    $orderby = $query->get('orderby');

    // edited date, product
    if( $orderby == 'edited_date' ) {
        //$query->set( 'orderby', '' );
    }
}
add_action( 'pre_get_posts', 'edited_date_product_column_orderby' );

add_filter('manage_product_posts_columns', 'misha_hide_product_tags_column', 999 );

function misha_hide_product_tags_column( $product_columns ) {
	unset( $product_columns['product_tag'] );
	return $product_columns;
}

if (function_exists('is_product')) {
    if ( is_product() ) { ?>
        <script type="text/javascript">
            document.querySelector('img[srcset]').attributes.removeNamedItem('srcset')
        </script>
    <?php } 
}
 


// Disable /users rest routes that show at example.com/wp-json/wp/v2/users
add_filter('rest_endpoints', function( $endpoints ) {
    if ( isset( $endpoints['/wp/v2/users'] ) ) {
        unset( $endpoints['/wp/v2/users'] );
    }
    if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) {
        unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
    }
    return $endpoints;
});

/**

 *        WooCommerce Holiday/Pause Mode

*/

 // Trigger Holiday Mode

//  add_action ('init', 'temp_disable_checkout');

//   // Disable Cart, Checkout, Add Cart

//  function temp_disable_checkout() {

//    remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

//    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

//    remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );

//    remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );

//    add_action( 'woocommerce_before_main_content', 'njengah_wc_shop_disabled', 5 );

//    add_action( 'woocommerce_before_cart', 'njengah_wc_shop_disabled', 5 );

//    add_action( 'woocommerce_before_checkout_form', 'njengah_wc_shop_disabled', 5 );

// }

//  // Show Holiday Notice

//  function njengah_wc_shop_disabled() {

//         wc_print_notice( 'Online ordering is temporarily unavailable and will resume on Monday 9th May', 'error');

// }


add_action('wp_head', 'show_template');
function show_template() {
    global $template;
    if($_SERVER['SERVER_NAME'] =='cf.barkode.xyz') {
        echo basename($template);
    }
}



function wc_dropdown_variation_attribute_options( $args = array() ) {
    $args = wp_parse_args(
        apply_filters( 'woocommerce_dropdown_variation_attribute_options_args', $args ),
        array(
            'options'          => false,
            'attribute'        => false,
            'product'          => false,
            'selected'         => false,
            'required'         => false,
            'name'             => '',
            'id'               => '',
            'class'            => '',
            'show_option_none' => __( 'Choose an option', 'woocommerce' ),
        )
    );

    // Get selected value.
    if ( false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product ) {
        $selected_key = 'attribute_' . sanitize_title( $args['attribute'] );
        // phpcs:disable WordPress.Security.NonceVerification.Recommended
        $args['selected'] = isset( $_REQUEST[ $selected_key ] ) ? wc_clean( wp_unslash( $_REQUEST[ $selected_key ] ) ) : $args['product']->get_variation_default_attribute( $args['attribute'] );
        // phpcs:enable WordPress.Security.NonceVerification.Recommended
    }

    $options               = $args['options'];
    $product               = $args['product'];
    $attribute             = $args['attribute'];
    $name                  = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
    $id                    = $args['id'] ? $args['id'] : sanitize_title( $attribute );
    $class                 = $args['class'];
    $required              = (bool) $args['required'];
    $show_option_none      = (bool) $args['show_option_none'];
    $show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __( 'Choose an option', 'woocommerce' ); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.

    if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
        $attributes = $product->get_variation_attributes();
        $options    = $attributes[ $attribute ];
    }

    $html  = '<select id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '"' . ( $required ? ' required' : '' ) . '>';
    $html .= '<option value="">' . esc_html( $show_option_none_text ) . '</option>';

    if ( ! empty( $options ) ) {
        if ( $product && taxonomy_exists( $attribute ) ) {
            // Get terms if this is a taxonomy - ordered. We need the names too.
            $terms = wc_get_product_terms(
                $product->get_id(),
                $attribute,
                array(
                    'fields' => 'all',
                )
            );

            foreach ( $terms as $term ) {
                   $html .= '<option class="attached enabled" value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $product ) ) . '</option>';

            }
        } else {
            foreach ( $options as $option ) {
                // This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
                $selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false );
                $html    .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute, $product ) ) . '</option>';
            }
        }
    }

    $html .= '</select>';

    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo apply_filters( 'woocommerce_dropdown_variation_attribute_options_html', $html, $args );
}

add_filter( 'wc_product_has_unique_sku', '__return_false' ); 
