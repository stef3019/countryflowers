<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 07/08/2017
 * Time: 8:10 SA
 */
global $wp;
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$product_per_page = Spring_Plant()->options()->get_woocommerce_customize_item_show();
if(!empty($product_per_page)) {
    $product_per_page = str_replace(' ', '', $product_per_page);
    $product_per_page_arr = explode(",", $product_per_page);
} else {
    $product_per_page_arr = array(intval(get_option( 'posts_per_page')));
}

$product_request = isset( $_GET['product_per_page'] ) ? wc_clean( $_GET['product_per_page'] ) : '';
$product_per_page = !empty($product_request) ? $product_request : $product_per_page_arr[0];
if(!empty($product_request) && !in_array($product_request, $product_per_page_arr)) {
    $product_per_page_arr[] = $product_request;
    sort($product_per_page_arr);
}

?>
<form class="woocommerce-page-size" method="get">
    <div name="product_per_page" id="product_per_page">
		<span><?php esc_html_e('Show', 'spring-plant'); ?></span>
        <?php $link = home_url($wp->request);
        $pos = strpos($link , '/page');
        if($pos) {
            $link = substr($link, 0, $pos);
        }?>
        <?php foreach ( $product_per_page_arr as $number ) { ?>
			<?php $current_url = add_query_arg('product_per_page', $number, $link); ?>
			<a href="<?php echo esc_attr($current_url) ?>" class="<?php if($product_per_page === $number) echo "active"?>"><?php echo esc_attr($number) ?></a>
        <?php } ?>
    </div>
    <?php
    // Keep query string vars intact
    foreach ( $_GET as $key => $val ) {
        if ( 'product_per_page' === $key || 'submit' === $key ) {
            continue;
        }
        if ( is_array( $val ) ) {
            foreach( $val as $innerVal ) {
                echo '<input type="hidden" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $innerVal ) . '" />';
            }
        } else {
            echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" />';
        }
    }
    ?>
</form>

