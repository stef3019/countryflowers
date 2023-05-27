<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 08/08/2017
 * Time: 5:00 CH
 */
$sidebar_layout = Spring_Plant()->options()->get_sidebar_layout();
$col_left_class = 'col-md-6 md-mg-bottom-30';
$col_right_class = 'col-md-6';
if($sidebar_layout === 'none') {
    $col_left_class = 'col-md-5 md-mg-bottom-30';
    $col_right_class = 'col-md-7';
}
?>
<div class="single-product-controls d-flex align-items-center single-style-04">
    <div class="product-breadcrum">
        <?php Spring_Plant()->breadcrumbs()->get_breadcrumbs(); ?>
    </div>
    <div class="product-near-items">
        <ul class="d-flex">
            <li>
                <?php $prev_product = get_adjacent_post(false, '', true, 'product_cat');
                if($prev_product):?>
                    <?php $product = wc_get_product( $prev_product->ID ); ?>
                    <a href="<?php echo esc_url($product->get_permalink()); ?>" class="prev-product" title="<?php esc_attr_e('Previous', 'spring-plant') ?>">
                        <i class="flaticon-left-arrow-1"></i>
                    </a>
                    <div class="product-near">
                        <div class="product-near-thumb">
                            <a href="<?php echo esc_url( $product->get_permalink() ); ?>" title="<?php echo esc_attr($product->get_title()); ?>">
                                <?php echo wp_kses_post($product->get_image()); ?>
                            </a>
                        </div>
                        <div class="product-near-info">
                            <a href="<?php echo esc_url( $product->get_permalink() ); ?>" title="<?php echo esc_attr($product->get_title()); ?>" class="product-near-title">
                                <span class="product-title"><?php echo esc_html($product->get_name()); ?></span>
                            </a>
                            <p class="price">
                                <?php echo wp_kses_post($product->get_price_html()); ?>
                            </p>
                        </div>
                    </div>
                <?php else: ?>
                    <span class="prev-product disable flaticon-left-arrow-1"></span>
                <?php endif; ?>
            </li>
            <li>
                <?php $next_product = get_adjacent_post(false, '', false, 'product_cat');
                if($next_product):?>
                    <?php $product = wc_get_product( $next_product->ID ); ?>
                    <a href="<?php echo esc_url(get_permalink($next_product->ID)); ?>" class="next-product" title="<?php esc_attr_e('Next', 'spring-plant') ?>">
                        <i class="flaticon-right-arrow-1"></i>
                    </a>
                    <div class="product-near">
                        <div class="product-near-thumb">
                            <a href="<?php echo esc_url( $product->get_permalink() ); ?>" title="<?php echo esc_attr($product->get_title()); ?>">
                                <?php echo wp_kses_post($product->get_image()); ?>
                            </a>
                        </div>
                        <div class="product-near-info">
                            <a href="<?php echo esc_url( $product->get_permalink() ); ?>" title="<?php echo esc_attr($product->get_title()); ?>" class="product-near-title">
                                <span class="product-title"><?php echo esc_html($product->get_name()); ?></span>
                            </a>
                            <p class="price">
                                <?php echo wp_kses_post($product->get_price_html()); ?>
                            </p>
                        </div>
                    </div>
                <?php else: ?>
                    <span class="next-product disable flaticon-right-arrow-1"></span>
                <?php endif; ?>
            </li>
        </ul>
    </div>
</div>
<div class="single-product-info single-style-01">
    <div class="single-product-info-inner row clearfix">
        <div class="<?php echo esc_attr($col_left_class); ?>">
            <div class="single-product-image">
				<div class="product-flash-inner">
					<?php
					remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
					do_action( 'woocommerce_before_single_product_summary' );
					?>
				</div>
                <?php
				remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_loop_sale_flash', 10);
				add_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
                /**
                 * woocommerce_before_single_product_summary hook.
                 *
                 * @hooked woocommerce_show_product_images - 20
                 */
                do_action( 'woocommerce_before_single_product_summary' );
                ?>
            </div>
        </div>
        <div class="<?php echo esc_attr($col_right_class); ?>">
            <div class="summary-product entry-summary">
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
                 * @hooked shop_loop_rating - 10
                 * @hooked woocommerce_template_single_price - 10
                 * @hooked shop_single_loop_sale_count_down - 15
                 * @hooked woocommerce_template_single_excerpt - 20
                 * @hooked woocommerce_template_single_add_to_cart - 30
				 * @hooked shop_single_function - 35
                 * @hooked woocommerce_template_single_meta - 40
                 * @hooked woocommerce_template_single_sharing - 50
                 
                 */
                do_action( 'woocommerce_single_product_summary' );
                ?>
            </div><!-- .summary -->
        </div>
    </div>
</div>
