<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 1/31/2018
 * Time: 3:32 PM
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $product;
$owl_attributes = array(
    'items'      => 1,
    'autoHeight' => true,
    'loop'       => false,
    'margin'     => 0,
    'nav'        => false,
    'dots'       => true
);
// Ensure visibility
if (!$product || !$product->is_visible()) {
    return;
}
if (!isset($image_size)) {
    $image_size = 'shop-catalog';
}

if (!isset($placeholder_enable)) {
    $placeholder_enable = true;
}
$classes = array('product-item-inner', 'd-flex');

$images = array();
if(has_post_thumbnail($product->get_id())) {
    $images[] = get_post_thumbnail_id($product->get_id());
}
$attachment_ids = $product->get_gallery_image_ids();
if ($attachment_ids) {
    foreach ($attachment_ids as $attachment_id) {
        if (in_array($attachment_id, $images)) continue;
        $images[] = $attachment_id;
    }
}
?>
<div <?php post_class('product-item-wrap'); ?>>
    <div class='<?php echo join(' ', $classes); ?>'>
        <div class="product-thumb">
            <div class="owl-carousel owl-theme dots-style-06" data-owl-options='<?php echo json_encode($owl_attributes); ?>'>
                <?php $gallery_id = uniqid(); ?>
                <?php if(count($images) > 0): ?>
                    <?php foreach ($images as $image_id) : ?>
                        <?php Spring_Plant()->blog()->render_post_image_markup(array(
                            'post_id'           => $product->get_id(),
                            'image_id'          => $image_id,
                            'image_size'        => $image_size,
                            'display_permalink' => true,
                            'gallery_id' => $gallery_id
                        )); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
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
                add_action('woocommerce_after_shop_loop_item_title', array(Spring_Plant()->templates(), 'shop_single_loop_sale_count_down'), 25);
                remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 15);
                /**
                 * woocommerce_after_shop_loop_item_title hook.
                 *
                 * @hooked woocommerce_template_loop_price - 10
                 * @hooked woocommerce_template_loop_rating - 15
                 * @hooked shop_loop_product_excerpt - 20
                 * @hook shop_single_loop_sale_count_down -25
                 */
                do_action('woocommerce_after_shop_loop_item_title');
                remove_action('woocommerce_after_shop_loop_item_title', array(Spring_Plant()->templates(), 'shop_single_loop_sale_count_down'), 25);
                add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 15);
                ?>
            </div>
            <div class="product-actions">
                <a class="gsf-link btn btn-md btn-rounded btn classic btn-accent btn-icon-right" href="<?php the_permalink(); ?>"><?php esc_html_e('Shop now', 'spring-plant'); ?><i class="flaticon-right-arrow-1"></i></a>
            </div>
        </div>
    </div>
</div>
