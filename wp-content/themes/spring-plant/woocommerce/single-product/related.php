<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$product_related_enable = Spring_Plant()->options()->get_product_related_enable();
if('on' !== $product_related_enable) return;

$product_carousel_enable = Spring_Plant()->options()->get_product_related_carousel_enable();
$product_columns_gutter = intval(Spring_Plant()->options()->get_product_related_columns_gutter());
$product_columns = intval(Spring_Plant()->options()->get_product_related_columns());
$product_columns_md = intval(Spring_Plant()->options()->get_product_related_columns_md());
$product_columns_sm = intval(Spring_Plant()->options()->get_product_related_columns_sm());
$product_columns_xs = intval(Spring_Plant()->options()->get_product_related_columns_xs());
$product_columns_mb = intval(Spring_Plant()->options()->get_product_related_columns_mb());
$product_animation = Spring_Plant()->options()->get_product_related_animation();
$product_related_item_skin = Spring_Plant()->options()->get_product_related_item_skin();
$single_layout = Spring_Plant()->options()->get_product_single_layout();
$is_fullwidth='';
if(in_array($single_layout,array('layout-02','layout-03','layout-05'))){
	$is_fullwidth = 'fullwidth';
}

$settings = array(
    'post_layout'            => 'grid',
    'product_item_skin' 		 => $product_related_item_skin,
    'post_columns'           => array(
        'xl' => $product_columns,
        'lg' => $product_columns_md,
        'md' => $product_columns_sm,
        'sm' => $product_columns_xs,
        '' => $product_columns_mb,
    ),
    'post_columns_gutter'    => $product_columns_gutter,
    'post_paging'            => 'none',
    'post_animation'         => $product_animation,
    'itemSelector'           => 'article',
    'category_filter_enable' => false,
    'post_type' => 'product'
);
if('on' === $product_carousel_enable) {
    $settings['carousel'] = array(
        'items' => $product_columns,
        'margin' => $product_columns == 1 ? 0 : $product_columns_gutter,
        'slideBy' => $product_columns,
        'responsive' => array(
            '1200' => array(
                'items' => $product_columns,
                'margin' => $product_columns == 1 ? 0 : $product_columns_gutter,
                'slideBy' => $product_columns,
                'nav' => false
            ),
            '992' => array(
                'items' => $product_columns_md,
                'margin' => $product_columns_md == 1 ? 0 : $product_columns_gutter,
                'slideBy' => $product_columns_md,
                'nav' => false
            ),
            '768' => array(
                'items' => $product_columns_sm,
                'margin' => $product_columns_sm == 1 ? 0 : $product_columns_gutter,
                'slideBy' => $product_columns_sm,
                'nav' => false
            ),
            '575' => array(
                'items' => $product_columns_xs,
                'margin' => $product_columns_xs == 1 ? 0 : $product_columns_gutter,
                'slideBy' => $product_columns_xs,
                'nav' => true
            ),
            '0' => array(
                'items' => $product_columns_mb,
                'margin' => $product_columns_mb == 1 ? 0 : $product_columns_gutter,
                'slideBy' => $product_columns_mb,
                'nav' => true
            )
        ),
        'autoHeight' => true,
    );
}

Spring_Plant()->blog()->set_layout_settings($settings);

if ( $related_products ) : ?>

	<section class="related products <?php echo esc_attr($is_fullwidth)?>">
		<h2><?php esc_html_e( 'Related products', 'spring-plant' ); ?></h2>

		<?php woocommerce_product_loop_start(); ?>

			<?php foreach ( $related_products as $related_product ) : ?>

				<?php
				 	$post_object = get_post( $related_product->get_id() );

					setup_postdata( $GLOBALS['post'] =& $post_object );
				
				wc_get_template( 'content-product.php', array( 'product_item_skin' => $product_related_item_skin, 'post_layout' => 'grid') ); ?>

			<?php endforeach; ?>

		<?php woocommerce_product_loop_end(); ?>

	</section>

<?php endif;

wp_reset_postdata();
Spring_Plant()->blog()->unset_layout_settings();