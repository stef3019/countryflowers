<?php
/**
 * Shortcode attributes
 * @var $atts
 * @var $category
 * @var $tag
 * @var $ids
 * @var $orderby
 * @var $time_filter
 * @var $order
 * @var $meta_key
 * @var $posts_per_page
 * @var $autoplay
 * @var $autoplay_timeout
 * @var $post_animation
 * @var $css_animation
 * @var $animation_duration
 * @var $animation_delay
 * @var $el_class
 * @var $css
 * @var $responsive
 * Shortcode class
 * @var $this WPBakeryShortCode_GSF_Post_Horizontal
 */
$category = $tag = $ids = $orderby = $time_filter = $order = $meta_key = $posts_per_page = $autoplay = $autoplay_timeout = $post_animation =
$css_animation = $animation_duration = $animation_delay = $el_class = $css = $responsive = '';
if (!function_exists('Spring_Plant')) return;
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$wrapper_classes = array(
	'gf-post-horizontal',
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
	'post_type'=> 'post',
	'ignore_sticky_posts' => true,
	'posts_per_page' => (is_numeric( $posts_per_page ) && $posts_per_page > 0) ? $posts_per_page : '-1',
	'orderby' => $orderby,
	'order' => $order,
	'meta_key' => ( 'meta_value' == $orderby || 'meta_value_num' == $orderby ) ? $meta_key : ''
);
$category = G5P()->helper()->get_term_ids_from_slugs(explode(',', $category), 'category');
if (count($category) > 0) {
	$args['category__in'] = $category;
}
$tag = G5P()->helper()->get_term_ids_from_slugs(explode(',', $tag), 'post_tag');
if (count($tag) > 0) {
	$args['tag__in'] = $tag;
}
// Prepares time filter
if ( $time_filter !== 'none' ) {
	$args['date_query'] = $this -> get_time_filter_query( $time_filter );
}
if ( ! empty( $ids ) ) {
	$post_id_array = explode( ',', $ids );
    $post_in = array_map('intval', $post_id_array);
	if ( ! empty( $post_in ) ) {
		$args['post__in'] = $post_in;
	}
}

$settings = array(
	'posts_per_page' => (is_numeric( $posts_per_page ) && $posts_per_page > 0) ? $posts_per_page : '-1',
	'post_image_size' => '600x330',
	'post_layout' => 'list',
	'post_paging' => 'none',
	'cat' => $category,
);
if($post_animation !== '') {
    $settings['post_animation'] = $post_animation;
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
    wp_enqueue_style(G5P()->assetsHandle('gsf-post-horizontal'), G5P()->helper()->getAssetUrl('shortcodes/post-horizontal/assets/css/post-horizontal.min.css'), array(), G5P()->pluginVer());
}
wp_enqueue_script(G5P()->assetsHandle('post-horizontal'), G5P()->helper()->getAssetUrl('shortcodes/post-horizontal/assets/js/post-horizontal.min.js'), array('jquery'), G5P()->pluginVer(), true);

$class_to_filter = implode(' ', array_filter($wrapper_classes));
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts);
?>
<div class="<?php echo esc_attr($css_class) ?>">
	<?php Spring_Plant()->blog()->archive_markup($args, $settings);  ?>
</div>