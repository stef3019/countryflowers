<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 10/08/2017
 * Time: 9:47 SA
 */
global $product;
if ( ! is_a( $product, 'WC_Product' ) ) {
    $product = wc_get_product( get_the_ID() );
}
if(wc_notice_count()) {
    echo '<div class="container mg-top-50 mg-bottom-50">';
    /**
     * woocommerce_before_single_product hook.
     *
     * @hooked wc_print_notices - 10
     */
    do_action('woocommerce_before_single_product');
    echo '</div>';
}
?>
<div class="single-product-images single-style-02 d-flex clearfix">
    <div class="sm-mg-bottom-30">
        <div class="single-product-image">
            <?php
            remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_loop_sale_flash', 10);
            /**
             * woocommerce_before_single_product_summary hook.
             *
             * @hooked shop_show_product_images_layout_2 - 20
             */
            do_action( 'woocommerce_before_single_product_summary' );
            ?>
        </div>
    </div>
</div>