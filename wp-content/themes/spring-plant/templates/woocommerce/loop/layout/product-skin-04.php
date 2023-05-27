<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

global $product;

// Ensure visibility
if (empty($product) || !$product->is_visible()) {
	return;
}

if (!isset($post_class)) {
	$post_class = Spring_Plant()->woocommerce()->get_product_class();
}

if (!isset($post_inner_class)) {
	$post_inner_class = Spring_Plant()->woocommerce()->get_product_inner_class();
}

if (!isset($image_size)) {
	$image_size = 'shop_catalog';
}

if (!isset($placeholder_enable)) {
	$placeholder_enable = true;
}

?>
<article <?php post_class($post_class) ?>>
	<div class="<?php echo esc_attr($post_inner_class); ?>">
		<?php
		/**
		 * woocommerce_before_shop_loop_item hook.
		 *
		 * @hooked woocommerce_template_loop_product_link_open - 10
		 */
		do_action('woocommerce_before_shop_loop_item');
		?>
		<div class="product-thumb">
			<?php
			Spring_Plant()->woocommerce()->render_product_thumbnail_markup(array(
				'image_size'         => $image_size,
				'image_ratio'        => '',
				'placeholder_enable' => $placeholder_enable
			));
			?>
			<div class="product-flash-inner">
				<?php
				/**
				 * woocommerce_before_shop_loop_item_title hook.
				 *
				 * @hooked shop_loop_sale_count_down - 10
				 */
				do_action('woocommerce_before_shop_loop_item_title');
				?>
			</div>
            <?php
            add_action('spring_plant_shop_before_flash', array(Spring_Plant()->templates(), 'shop_loop_sale_count_down'), 10);
            do_action('spring_plant_shop_before_flash');
            ?>
		</div>
		<div class="product-info">
			<div class="product-actions">
				<div class="product-action-inner">
					<?php
					add_action('spring_plant_woocommerce_product_actions', array(Spring_Plant()->templates(), 'shop_loop_wishlist'), 10);
					/**
					 * spring_plant_woocommerce_product_action hook
					 *
					 * @hooked shop_loop_quick_view - 10
					 * @hooked shop_loop_wishlist - 10
					 * @hooked shop_loop_compare - 15
					 */
					do_action( 'spring_plant_woocommerce_product_actions' );
					remove_action('spring_plant_woocommerce_product_actions', array(Spring_Plant()->templates(), 'shop_loop_wishlist'), 10);
					?>
				</div>
			</div>
			<div class="product-heading">
				<?php
				/**
				 * woocommerce_shop_loop_item_title hook.
				 *
				 * @hooked shop_loop_product_cat
				 * @hooked woocommerce_template_loop_product_title - 10
				 */
				
				do_action('woocommerce_shop_loop_item_title');
				?>
			</div>
			<div class="product-meta">
				<?php
				/**
				 * woocommerce_after_shop_loop_item_title hook.
				 *
				 * @hooked woocommerce_template_loop_price - 10
				 * @hooked woocommerce_template_loop_rating - 15s
				 * @hooked shop_loop_product_excerpt - 20
				 */
				do_action('woocommerce_after_shop_loop_item_title');
				?>
			</div>
			<div class="product-list-actions">
				<?php
				/**
				 * spring_plant_woocommerce_shop_loop_list_info hook.
				 *
				 * @hooked shop_loop_list_add_to_cart - 10
				 * @hooked shop_loop_quick_view - 15
				 * @hooked shop_loop_compare - 20
				 */
				do_action('spring_plant_woocommerce_shop_loop_list_info');
				?>
			</div>
		</div>
		<?php
		/**
		 * woocommerce_after_shop_loop_item hook.
		 *
		 * @hooked woocommerce_template_loop_product_link_close - 5
		 */
		do_action('woocommerce_after_shop_loop_item');
		?>
	</div>
</article>