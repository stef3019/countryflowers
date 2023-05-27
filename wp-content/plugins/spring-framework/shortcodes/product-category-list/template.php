<?php
/**
 * Shortcode attributes
 * @var $atts
 * @var $title
 * @var $show_empty
 * @var $show_hierarchy
 * @var $show_count
 * @var $css_animation
 * @var $animation_duration
 * @var $animation_delay
 * @var $el_class
 * @var $css
 * @var $responsive
 * Shortcode class
 * @var $this WPBakeryShortCode_GSF_Product_Category_List
 */

$title = $show_empty = $show_hierarchy = $show_count = $show_as_dropdown = $css_animation = $animation_duration = $animation_delay = $el_class = $css = $responsive = '';
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$wrapper_classes = array(
	'gf-product-category-list',
	G5P()->core()->vc()->customize()->getExtraClass($el_class),
	$this->getCSSAnimation($css_animation),
	vc_shortcode_custom_css_class( $css ),
	$responsive
);

if ('' !== $css_animation && 'none' !== $css_animation) {
	$animation_class = G5P()->core()->vc()->customize()->get_animation_class($animation_duration, $animation_delay);
	$wrapper_classes[] = $animation_class;
}
$list_args          = array(
    'show_count'   => ('on' === $show_count),
    'hierarchical' => ('on' === $show_hierarchy),
    'taxonomy'     => 'product_cat',
    'hide_empty'   => ('on' !== $show_empty)
);
if (!(defined('CSS_DEBUG') && CSS_DEBUG)) {
	wp_enqueue_style(G5P()->assetsHandle('g5-product-category-list'), G5P()->helper()->getAssetUrl('shortcodes/product-category-list/assets/css/product-category-list.min.css'), array(), G5P()->pluginVer());
}
$class_to_filter = implode(' ', array_filter($wrapper_classes));
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts);
?>
<div class="<?php echo esc_attr($css_class) ?>">
    <?php
    if(class_exists('WooCommerce')) {
        include_once(WC()->plugin_path() . '/includes/walkers/class-product-cat-list-walker.php');

        $list_args['walker'] = new WC_Product_Cat_List_Walker;
        $list_args['title_li'] = '';
        $list_args['current_category'] = '';
        $list_args['current_category_ancestors'] = array();
        echo '<ul class="product-categories">';

        wp_list_categories(apply_filters('woocommerce_product_categories_widget_args', $list_args));

        echo '</ul>';
    }
    ?>
</div>
