<?php
/**
 * Shortcode attributes
 * @var $atts
 * @var $layout_style
 * @var $image
 * @var $category
 * @var $height_mode
 * @var $height
 * @var $css_animation
 * @var $animation_duration
 * @var $animation_delay
 * @var $el_class
 * @var $css
 * @var $responsive
 * Shortcode class
 * @var $this WPBakeryShortCode_GSF_Shop_Category
 */

$layout_style =  $image = $category = $height_mode = $height =
$css_animation = $animation_duration = $animation_delay = $el_class = $css = $responsive = '';
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$wrapper_classes = array(
	'gf-shop-category',
	'gf-shop-category-' . $layout_style,
	G5P()->core()->vc()->customize()->getExtraClass($el_class),
	$this->getCSSAnimation($css_animation),
	vc_shortcode_custom_css_class($css),
	$responsive
);

if ('' !== $css_animation && 'none' !== $css_animation) {
	$animation_class = G5P()->core()->vc()->customize()->get_animation_class($animation_duration, $animation_delay);
	$wrapper_classes[] = $animation_class;
}
$banner_class = 'gf-banner-' . random_int(1000, 9999);
$banner_bg_css = '';
if (!empty($image)) {
    $image_src = '';
    $image_arr = wp_get_attachment_image_src( $image, 'full' );
    $img_width = $img_height = '';
    if ( count( $image_arr ) > 0 && ! empty( $image_arr[0] ) ) {
        $image_src = $image_arr[0];
        $img_width = isset($image_arr[1]) ? intval($image_arr[1]) : 0;
        $img_height = isset($image_arr[2]) ? intval($image_arr[2]) : 0;
    }
	if ($height_mode != 'custom') {
		if ($height_mode === 'original' && intval($img_width) != 0) {
			$height_mode = ($img_height / $img_width) * 100;
		}
		$banner_bg_css = <<<CSS
			.{$banner_class} {
				background-image: url('{$image_src}');
				padding-bottom: {$height_mode}%;
			}
CSS;
	} else {
		
		$banner_bg_css = <<<CSS
			.{$banner_class} {
				background-image: url('{$image_src}');
				height: {$height};
			}
CSS;
	}
}
GSF()->customCss()->addCss($banner_bg_css);
if (!(defined('CSS_DEBUG') && CSS_DEBUG)) {
	wp_enqueue_style(G5P()->assetsHandle('g5-shop-category'), G5P()->helper()->getAssetUrl('shortcodes/shop-category/assets/css/shop-category.min.css'), array(), G5P()->pluginVer());
}
$class_to_filter = implode(' ', array_filter($wrapper_classes));
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts);
?>
<?php if (!empty($category)): ?>
	<div class="<?php echo esc_attr($css_class) ?>">
		<?php $cate_link = get_term_link($category, 'product_cat'); ?>
		<?php $category = get_term_by('slug', $category, 'product_cat', 'OBJECT'); ?>
        <?php if($category && !is_object($cate_link)): ?>
            <div class="gf-shop-category-bg effect-bg-image <?php echo esc_attr($banner_class); ?>">
                <a href="<?php echo esc_url($cate_link); ?>" title="<?php echo esc_attr($category->name); ?>"
                                                                                                       class="gsf-link"></a>
            </div>
            <div class="gf-shop-category-inner effect-content">
                <div class="gf-shop-category-content">
                    <h5><a href="<?php echo esc_url($cate_link); ?>" title="<?php echo esc_attr($category->name); ?>"
                           class="gsf-link"><?php echo esc_html($category->name); ?></a></h5>
                    <?php $count = _n_noop('%s item', '%s items', 'spring-framework'); ?>
                    <span class="cate-count"><?php printf(translate_nooped_plural($count, $category->count, 'spring-framework'), $category->count) ?></span>
                </div>
            </div>
            <?php if($layout_style !== 'style-03'): ?>
            <div class="gsf-category-button">
                <a class="btn btn-accent btn-classic btn-rounded btn-md" href="<?php echo esc_url($cate_link); ?>"
                   title="<?php echo esc_attr($category->name); ?>"><?php esc_html_e('View product', 'spring-framework') ?>
                    <i class="flaticon-right-arrow-1"></i></a>
            </div>
            <?php endif; ?>
        <?php endif; ?>
	</div>
<?php endif; ?>