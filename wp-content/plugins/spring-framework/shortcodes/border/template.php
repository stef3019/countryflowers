<?php
/**
 * Shortcode attributes
 * @var $atts
 * @var $use_skin_border_color
 * @var $border_color
 * @var $custom_border_width
 * @var $custom_width
 * @var $alignment
 * @var $border_height
 * @var $css_animation
 * @var $animation_duration
 * @var $animation_delay
 * @var $el_class
 * @var $css
 * @var $responsive
 * Shortcode class
 * @var $this WPBakeryShortCode_GSF_Border
 */
$use_skin_border_color = $border_color = $custom_border_width = $custom_width = $alignment = $border_height = $css_animation =
$animation_duration = $animation_delay = $el_class = $css = $responsive = '';
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$wrapper_classes = array(
	'gsf-border-container fs-0',
    $alignment,
	G5P()->core()->vc()->customize()->getExtraClass($el_class),
	$this->getCSSAnimation($css_animation),
	vc_shortcode_custom_css_class( $css ),
	$responsive
);

if ('' !== $css_animation && 'none' !== $css_animation) {
	$animation_class = G5P()->core()->vc()->customize()->get_animation_class($animation_duration, $animation_delay);
	$wrapper_classes[] = $animation_class;
}
$border_class = 'gsf-border-' . random_int(1000, 9999);
if(intval($border_height) <= 0) {
    $border_height = 1;
}
$border_css = <<<CSS
	.{$border_class} {
		height: {$border_height}px;
	}
CSS;
if('on' !== $use_skin_border_color) {
	$border_css .= <<<CSS
	.{$border_class} {
		background-color: {$border_color} !important;
	}
CSS;
}
if('on' === $custom_border_width) {
    $border_css .= <<<CSS
    .{$border_class} {
        width: {$custom_width}px;
    }
CSS;
} else {
    $border_css .= <<<CSS
    .{$border_class} {
        width: 100%;
    }
CSS;
}

GSF()->customCss()->addCss($border_css);
$class_to_filter = implode(' ', array_filter($wrapper_classes));
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts);
?>
<div class="<?php echo esc_attr($css_class) ?>">
    <span class="gsf-border-inner display-inline-block <?php echo esc_attr($border_class) ?>"></span>
</div>