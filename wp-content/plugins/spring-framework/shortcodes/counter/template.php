<?php
/**
 * Shortcode attributes
 * @var $atts
 * @var $el_class
 * @var $icon_font
 * @var $icon_color
 * @var $animation_duration
 * @var $animation_delay
 * @var $css_animation
 * @var $css
 * @var $title
 * @var $subtitle
 * @var $end
 * @var $start
 * @var $decimals
 * @var $duration
 * @var $separator
 * @var $decimal
 * @var $prefix
 * @var $suffix
 * Shortcode class
 * @var $this WPBakeryShortCode_GSF_Counter
 */
$el_class = $icon_font = $animation_duration = $animation_delay = $css_animation = $css = $title = $end = $start = $decimals = $duration = $separator = $decimal = $prefix = $suffix = '';
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$wrapper_classes = array(
	'gsf-counter',
	G5P()->core()->vc()->customize()->getExtraClass($el_class),
	$this->getCSSAnimation($css_animation),
	vc_shortcode_custom_css_class($css),
	$responsive
);
//animation
if ('' !== $css_animation && 'none' !== $css_animation) {
	$animation_class = G5P()->core()->vc()->customize()->get_animation_class($animation_duration, $animation_delay);
	$wrapper_classes[] = $animation_class;
}
$icon_html = '';
if(!empty($icon_font)) {
    $icon_html = '<i class="' . esc_attr($icon_font) . '"></i>';
    if (empty($icon_color)) $icon_color = '#333';
    $ct_custom_class = 'ct-' . uniqid();
    $ct_css = <<<CSS
	 .{$ct_custom_class} .ct-icon{
	 	color:  {$icon_color};
	 }
CSS;
    GSF()->customCss()->addCss($ct_css);
    $wrapper_classes[] = $ct_custom_class;
}
//enqueue class
if (!(defined('CSS_DEBUG') && CSS_DEBUG)) {
	wp_enqueue_style(G5P()->assetsHandle('g5-counter'), G5P()->helper()->getAssetUrl('shortcodes/counter/assets/css/counter.min.css'), array(), G5P()->pluginVer());
}
wp_enqueue_script(G5P()->assetsHandle('counter'), G5P()->helper()->getAssetUrl('shortcodes/counter/assets/js/countUp.min.js'), array('jquery'), G5P()->pluginVer(), true);

$class_to_filter = implode(' ', array_filter($wrapper_classes));
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts);
?>

<div class="<?php echo esc_attr($css_class)?>">
	<?php if (!empty($icon_font)): ?>
		<div class="ct-icon-shape">
			<div class="fs-48 ct-icon"><?php echo wp_kses_post($icon_html); ?></div>
		</div>
	<?php endif; ?>
	<div class="ct-content">
		<?php if (!empty($end)): ?>
			<h4 class="counterup" data-start="<?php echo esc_attr($start) ?>"
				  data-end="<?php echo esc_attr($end) ?>" data-decimals="<?php echo esc_attr($decimals) ?>"
				  data-duration="<?php echo esc_attr($duration) ?>" data-separator="<?php echo esc_attr($separator) ?>"
				  data-decimal="<?php echo esc_attr($decimal) ?>" data-prefix="<?php echo esc_attr($prefix) ?>"
				  data-suffix="<?php echo esc_attr($suffix) ?>"><?php echo wp_kses_post($end) ?><span><?php echo wp_kses_post($suffix) ?></span></h4>
		<?php endif; ?>
		<div class="ct-content-right">
			<?php if (!empty($title)): ?>
				<span class="fs-20 fw-bold"><?php echo wp_kses_post($title) ?></span>
			<?php endif;?>
			<?php if (!empty($subtitle)): ?>
				<span class="fs-15"><?php echo wp_kses_post($subtitle) ?></span>
			<?php endif;?>
		</div>
	</div>
</div>
