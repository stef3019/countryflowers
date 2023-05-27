<?php
/**
 * Shortcode attributes
 * @var $atts
 * @var $title
 * @var $layout_style
 * @var $product_ids
 * @var $category
 * @var $dots
 * @var $nav
 * @var $nav_position
 * @var $nav_style
 * @var $dot_style
 * @var $image_size
 * @var $image_ratio
 * @var $image_ratio_custom_width
 * @var $image_ratio_custom_height
 * @var $autoplay
 * @var $autoplay_timeout
 * @var $css_animation
 * @var $animation_duration
 * @var $animation_delay
 * @var $el_class
 * @var $css
 * @var $responsive
 * Shortcode class
 * @var $this WPBakeryShortCode_GSF_Product_Deals
 */

$title = $layout_style = $product_ids = $category = $dots = $nav = $nav_position = $nav_style = $dot_style = $image_size = $image_ratio = $image_ratio_custom_width = $image_ratio_custom_height
    = $autoplay = $autoplay_timeout = $css_animation = $animation_duration = $animation_delay = $el_class = $css = $responsive = '';
if (!function_exists('Spring_Plant')) return;
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$wrapper_classes = array(
	'gsf-product-deals',
    'clearfix',
	$layout_style,
	G5P()->core()->vc()->customize()->getExtraClass($el_class),
	$this->getCSSAnimation($css_animation),
	vc_shortcode_custom_css_class($css),
	$responsive
);

if ('' !== $css_animation && 'none' !== $css_animation) {
	$animation_class = G5P()->core()->vc()->customize()->get_animation_class($animation_duration, $animation_delay);
	$wrapper_classes[] = $animation_class;
}

$query_args = array(
	'post_type'           => 'product',
	'post_status'         => 'publish',
	'posts_per_page'      => $number,
	'meta_query'          => WC()->query->get_meta_query()
);
if (($show !== 'products')) {
    if (!empty($category)) {
        $query_args['tax_query'][] = array(
            'taxonomy' => 'product_cat',
            'terms'    => explode(',', $category),
            'field'    => 'slug',
            'operator' => 'IN'
        );
        $category = G5P()->helper()->get_term_ids_from_slugs(explode(',', $category), 'product_cat');
    }
} else {
    $category = array();
}
switch ($show) {
	case 'sale':
		$product_ids_on_sale = wc_get_product_ids_on_sale();
		$query_args['post__in'] = array_merge(array(0), $product_ids_on_sale);
		break;
	case 'products':
		if (!empty($product_ids)) {
			$product_ids = explode(',', $product_ids);
			$query_args['post__in'] = $product_ids;
			$query_args['posts_per_page'] = -1;
			$query_args['orderby'] = 'post__in';
		}
		break;
}
$settings = array(
    'post_layout'            => 'deals',
    'post_columns'           => 1,
    'post_columns_gutter'    => 'none',
    'post_paging'            => 'none',
    'itemSelector'           => 'article',
    'category_filter_enable' => false,
    'post_type' => 'product',
    'cat' => $category,
    'image_size' => $image_size,
    'image_ratio' => $image_ratio,
    'image_ratio_custom' => array(
        'width' => intval($image_ratio_custom_width),
        'height' => intval($image_ratio_custom_height)
    )
);
$carousel_class = '';
$owl_attributes = array(
    'items'      => 1,
    'autoHeight' => true,
    'loop'       => false,
    'margin'     => 0,
    'dots'       => ($dots === 'on') ? true : false,
    'nav'        => ($nav === 'on') ? true : false,
    'autoplay'   => ($autoplay === 'on') ? true : false,
    'autoplayTimeout' => intval($autoplay_timeout),
);
if ($nav === 'on') {
    $carousel_class .= ' ' . $nav_position . ' nav-' . $nav_style;
}
if ($dots === 'on') {
    $carousel_class .= ' dots-' . $dot_style;
}
if(!empty($carousel_class)) {
    $settings['carousel_class'] = $carousel_class;
}
$settings['carousel'] = $owl_attributes;
$class_to_filter = implode(' ', array_filter($wrapper_classes));
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts);
if (!(defined('CSS_DEBUG') && CSS_DEBUG)) {
    wp_enqueue_style(G5P()->assetsHandle('gf-product-deal'), G5P()->helper()->getAssetUrl('shortcodes/product-deals/assets/css/product-deals.min.css'), array(), G5P()->pluginVer());
}
?>
<div class="<?php echo esc_attr($css_class) ?>">
	<?php if (!empty($title)): ?>
		<div class="product-deals-title">
            <span><?php echo esc_html($title); ?></span>
        </div>
	<?php endif; ?>
	<?php Spring_Plant()->woocommerce()->archive_markup($query_args, $settings); ?>
</div>