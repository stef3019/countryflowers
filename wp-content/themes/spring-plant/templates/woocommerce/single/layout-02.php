<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 08/08/2017
 * Time: 5:00 CH
 */
global $product_images;
add_action('spring_plant_after_main_content','woocommerce_output_related_products', 20);
add_action('spring_plant_after_main_content','woocommerce_upsell_display', 15);
remove_action('woocommerce_after_single_product_summary','woocommerce_output_related_products',20);
remove_action('woocommerce_after_single_product_summary','woocommerce_upsell_display',15);

if(is_array($product_images) && count($product_images) > 1) {
?>
    <div class="single-product-gallery">
        <div class="single-product-gallery-inner row clearfix">
            <?php
                /**
                 * spring_plant_show_product_gallery hook.
                 *
                 * @hooked shop_loop_single_gallery - 10
                */
                do_action('spring_plant_show_product_gallery');
            ?>
        </div>
    </div>
<?php }
?>
<div class="single-product-info single-style-02 clearfix">
<div class="summary-product entry-summary d-flex">
	<div class="product-flash-inner">
		<?php
		add_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_loop_sale_flash', 10);
		remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
		/**
		 * woocommerce_before_single_product_summary hook.
		 *
		 * @hooked woocommerce_show_product_images - 20
		 */
		do_action( 'woocommerce_before_single_product_summary' );
		?>
	</div>
	<div class="col-sm-8">
		<?php
		$product_add_to_cart_enable = Spring_Plant()->options()->get_product_add_to_cart_enable();
		if (!$product_add_to_cart_enable) {
			remove_action('woocommerce_single_product_summary','woocommerce_template_single_add_to_cart',30);
		}
		?>
		
		<?php
		/**
		 * woocommerce_single_product_summary hook.
		 *
		 * @hooked woocommerce_template_single_title - 5
		 * @hooked woocommerce_template_single_rating - 10
		 * @hooked woocommerce_template_single_price - 10
		 * @hooked shop_single_loop_sale_count_down - 15
		 * @hooked woocommerce_template_single_excerpt - 20
		 * @hooked woocommerce_template_single_add_to_cart - 30
		 * @hooked woocommerce_template_single_meta - 40
		 * @hooked woocommerce_template_single_sharing - 50
		 * @hooked spring_plant_woocommerce_template_single_function - 60
		 */
		do_action( 'woocommerce_single_product_summary' );
		?>
	</div>
</div>
</div>