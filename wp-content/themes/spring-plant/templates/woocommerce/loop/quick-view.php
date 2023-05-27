<?php
/**
 * Template display quickview
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
global $product;
$product_quick_view = Spring_Plant()->options()->get_product_quick_view_enable();
if ('on' !== $product_quick_view) return;
?>
<a data-toggle="tooltip" title="<?php esc_attr_e('Quick view', 'spring-plant') ?>" class="product-quick-view no-animation" data-product_id="<?php echo esc_attr($product->get_id()); ?>" href="<?php the_permalink(); ?>"><i class="fa fa-search"></i></a>