<?php
/**
 * Shortcode attributes
 * @var $atts
 * @var $portfolios_per_page
 * @var $autoplay
 * @var $autoplay_timeout
 * @var $portfolio_animation
 * @var $show
 * @var $portfolio_ids
 * @var $category
 * @var $css_animation
 * @var $animation_duration
 * @var $animation_delay
 * @var $el_class
 * @var $css
 * @var $responsive
 * Shortcode class
 * @var $this WPBakeryShortCode_GSF_Portfolio_Horizontal
 */
$portfolios_per_page = $autoplay = $autoplay_timeout = $portfolio_animation = $show = $portfolio_ids = $category =
$css_animation = $animation_duration = $animation_delay = $el_class = $css = $responsive = '';
if (!function_exists('Spring_Plant')) return;
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$wrapper_classes = array(
	'gf-portfolio-horizontal',
    'clearfix',
	G5P()->core()->vc()->customize()->getExtraClass($el_class),
	$this->getCSSAnimation($css_animation),
	vc_shortcode_custom_css_class( $css ),
	$responsive
);

if ('' !== $css_animation && 'none' !== $css_animation) {
	$animation_class = G5P()->core()->vc()->customize()->get_animation_class($animation_duration, $animation_delay);
	$wrapper_classes[] = $animation_class;
}

$args = array(
	'post_type'=> 'portfolio',
	'posts_per_page' => (is_numeric( $portfolios_per_page ) && $portfolios_per_page > 0) ? $portfolios_per_page : '-1'
);
$category = G5P()->helper()->get_term_ids_from_slugs(explode(',', $category), 'portfolio_cat');
if (count($category) > 0) {
	$args['category__in'] = $category;
}
if ( ! empty( $portfolio_ids ) ) {
	$post_id_array = explode( ',', $portfolio_ids );

    $post_in = array_map('intval', $post_id_array);
	if ( ! empty( $post_in ) ) {
		$args['post__in'] = $post_in;
	}
}

$settings = array(
	'posts_per_page' => (is_numeric( $portfolios_per_page ) && $portfolios_per_page > 0) ? $portfolios_per_page : '-1',
	'image_size' => '600x330',
	'post_layout' => 'list',
	'post_paging' => 'none',
	'cat' => $category,
);
if($portfolio_animation !== '') {
    $settings['post_animation'] = $portfolio_animation;
}
$owl_args = array(
    'items' => 1,
    'margin' => 0,
    'slideBy' => 1,
    'dots' => false,
    'nav' => false,
    'autoHeight' => true,
    'autoplay' => ($autoplay === 'on') ? true : false,
    'autoplayTimeout' => intval($autoplay_timeout),
);
$settings['carousel'] = $owl_args;

//enqueue class
if (!(defined('CSS_DEBUG') && CSS_DEBUG)) {
    wp_enqueue_style(G5P()->assetsHandle('gsf-portfolio-horizontal'), G5P()->helper()->getAssetUrl('shortcodes/portfolio-horizontal/assets/css/portfolio-horizontal.min.css'), array(), G5P()->pluginVer());
}
wp_enqueue_script(G5P()->assetsHandle('portfolio-horizontal'), G5P()->helper()->getAssetUrl('shortcodes/portfolio-horizontal/assets/js/portfolio-horizontal.min.js'), array('jquery'), G5P()->pluginVer(), true);

$class_to_filter = implode(' ', array_filter($wrapper_classes));
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts);
?>
<div class="<?php echo esc_attr($css_class) ?>">
	<?php Spring_Plant()->portfolio()->archive_markup($args, $settings);  ?>
</div>