<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.0
 * @var $image_size
 * @var $image_ratio
 * @var $post_class
 * @var $post_inner_class
 * @var $placeholder_enable
 * @var $post_inner_attributes
 */

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

$product_layout = isset($post_layout) ? $post_layout : Spring_Plant()->options()->get_product_catalog_layout();
?>
<article <?php post_class($post_class) ?>>
    <div <?php echo implode(' ', $post_inner_attributes); ?> class="<?php echo esc_attr($post_inner_class); ?>">
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
                'image_ratio'        => $image_ratio,
                'image_mode'         => 'background',
                'placeholder_enable' => $placeholder_enable
            ));
            ?>
	
			<div class="product-flash-inner">
				<?php
				/**
				 * woocommerce_before_shop_loop_item_title hook.
				 *
				 * @hooked shop_loop_sale_count_down - 10
				 * @hooked woocommerce_show_product_loop_sale_flash - 10
				 */
				do_action('woocommerce_before_shop_loop_item_title');
				?>
			</div>
            <?php
            add_action('spring_plant_shop_before_flash', array(Spring_Plant()->templates(), 'shop_loop_sale_count_down'), 10);
            do_action('spring_plant_shop_before_flash');
            ?>
		</div>
		<div class="product-actions">
            <?php $tooltip_options = array('placement' => 'left'); ?>
            <div class="product-action-inner gf-tooltip-wrap" data-tooltip-options='<?php echo json_encode($tooltip_options); ?>'>
				<?php
				add_action('spring_plant_woocommerce_product_actions', array(Spring_Plant()->templates(), 'shop_loop_wishlist'), 10);
				/**
				 * spring_plant_woocommerce_product_action hook
				 *
				 * @hooked shop_loop_quick_view - 5
				 * @hooked shop_loop_wishlist - 10
				 * @hooked shop_loop_compare - 15
				 */
				do_action( 'spring_plant_woocommerce_product_actions' );
                remove_action('spring_plant_woocommerce_product_actions', array(Spring_Plant()->templates(), 'shop_loop_wishlist'), 10);
				?>
			</div>
		</div>
		<div class="product-info">
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

