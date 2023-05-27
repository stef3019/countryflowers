<?php
/**
 * Shortcode attributes
 * @var $atts
 * @var $align
 * @var $css_animation
 * @var $animation_duration
 * @var $animation_delay
 * @var $el_class
 * @var $css
 * @var $responsive
 * Shortcode class
 * @var $this WPBakeryShortCode_GSF_Breadcrumbs
 */
$align = $css_animation = $animation_duration = $animation_delay = $el_class = $css = $responsive = '';
if (!function_exists('Spring_Plant')) return;
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

global $breadcrumb_used;

$wrapper_classes = array(
	'breadcrumbs-container',
	G5P()->core()->vc()->customize()->getExtraClass($el_class),
	$this->getCSSAnimation($css_animation),
	vc_shortcode_custom_css_class( $css ),
	$responsive
);
if('center' === $align) {
    add_filter('gf-breadcrumbs-class', function ($class){
        $class = array_merge($class, array('justify-content-center'));
        return $class;
    });
} elseif ('right' === $align) {
    add_filter('gf-breadcrumbs-class', function ($class){
        $class = array_merge($class, array('justify-content-end'));
        return $class;
    });
}
else {
    add_filter('gf-breadcrumbs-class', function ($class){
        $class = array_merge($class, array('justify-content-start'));
        return $class;
    });
}
if ('' !== $css_animation && 'none' !== $css_animation) {
	$animation_class = G5P()->core()->vc()->customize()->get_animation_class($animation_duration, $animation_delay);
	$wrapper_classes[] = $animation_class;
}

$class_to_filter = implode(' ', array_filter($wrapper_classes));
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts);
$breadcrumb_used = true;
?>
<div class="<?php echo esc_attr($css_class) ?>">
	<?php Spring_Plant()->breadcrumbs()->get_breadcrumbs(); ?>
</div>

