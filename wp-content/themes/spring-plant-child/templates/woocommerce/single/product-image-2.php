<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
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
 * @version 3.0.2
 */

if (!defined('ABSPATH')) {
	exit;
}

global $post, $woocommerce, $product;
$index = 0;
$product_images = array();
$image_ids = array();
if (!is_a($product, 'WC_Product')) {
	$product = wc_get_product(get_the_ID());
}

if (has_post_thumbnail()) {
	if ( get_post_thumbnail_id() == '17797') {
		set_post_thumbnail($post->id, '18500');
	}
	$product_images[$index] = array(
		'image_id' => get_post_thumbnail_id()
	);
	$image_ids[$index] = get_post_thumbnail_id();
	$index++;
}





// Additional Images
$attachment_ids = $product->get_gallery_image_ids();
if ($attachment_ids) {
	foreach ($attachment_ids as $attachment_id) {
		if (in_array($attachment_id, $image_ids)) continue;
		$product_images[$index] = array(
			'image_id' => $attachment_id
		);
		$image_ids[$index] = $attachment_id;
		$index++;
	}
}

// product variable type
if ($product->get_type() == 'variable') {
	$available_variations = $product->get_available_variations();
	
	if (isset($available_variations)) {
		foreach ($available_variations as $available_variation) {
			$variation_id = $available_variation['variation_id'];
			if (has_post_thumbnail($variation_id)) {
				$variation_image_id = get_post_thumbnail_id($variation_id);
				
				if (in_array($variation_image_id, $image_ids)) {
					$index_of = array_search($variation_image_id, $image_ids);
					if (isset($product_images[$index_of]['variation_id'])) {
						$product_images[$index_of]['variation_id'] .= $variation_id . '|';
					} else {
						$product_images[$index_of]['variation_id'] = '|' . $variation_id . '|';
					}
					continue;
				}
				
				$product_images[$index] = array(
					'image_id'     => $variation_image_id,
					'variation_id' => '|' . $variation_id . '|'
				);
				$image_ids[$index] = $variation_image_id;
				$index++;
			}
		}
	}
}

$gallery_id = rand();
$args = array('galleryId' => $gallery_id);
$owl_attributes = array(
	'items'      => 1,
	'autoHeight' => true,
	'loop'       => false,
	'margin'     => 0,
	'nav'        => true,
	'dots'       => false
);
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
<div id="single-product-image-2" class="mg-between-60">
	<div class="container d-flex justify-content-center single-product-image-main-wrap">
		<div class="single-product-image-inner col-lg-6">
			<div class="product-flash-inner">
				<?php
				remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
				do_action( 'woocommerce_before_single_product_summary' );
				?>
			</div>
			<div class="single-product-image-main owl-carousel nav-center"
				 data-owl-options='<?php echo json_encode($owl_attributes); ?>'>
				<?php
				foreach ($product_images as $key => $value) {
					$index = $key;
					$image_id = $value['image_id'];
					$variation_id = isset($value['variation_id']) ? $value['variation_id'] : '';
					$image_title = esc_attr(get_the_title($image_id));
					$image_caption = '';
					$image_obj = get_post($image_id);
					if (isset($image_obj) && isset($image_obj->post_excerpt)) {
						$image_caption = $image_obj->post_excerpt;
					}

					$image_link = wp_get_attachment_url($image_id);
					$image = wp_get_attachment_image($image_id, apply_filters('single_product_large_thumbnail_size', 'shop_single'), array(
						'title' => $image_title,
						'alt'   => $image_title
					));
					echo '<div class="product-image-thumb-item">';
					if (!empty($variation_id)) { ?>
						<a href="<?php echo esc_url($image_link); ?>" class="woocommerce-thumbnail-image" data-magnific="true" data-variation_id="<?php echo esc_attr($variation_id); ?>">
						<img src="<?php echo $image_link ?>"/>
						   </a>
					<?php } else { ?>
						<a href="<?php echo esc_url($image_link); ?>" class="woocommerce-thumbnail-image" data-magnific="true">
						<img src="<?php echo $image_link ?>"/>
						   </a>
					<?php }
					echo '</div>';
				}
				?>
			</div>
		</div>
		<div class="summary-product entry-summary col-lg-6">
			
			<?php
			$product_add_to_cart_enable = Spring_Plant()->options()->get_product_add_to_cart_enable();
			if (!$product_add_to_cart_enable) {
				remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
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
			 * @hooked spring_plant_woocommerce_template_single_function - 35
			 * @hooked woocommerce_template_single_meta - 40
			 * @hooked woocommerce_template_single_sharing - 50
			 */
			do_action('woocommerce_single_product_summary');
			?>
		</div><!-- .summary -->
	</div>
</div>

