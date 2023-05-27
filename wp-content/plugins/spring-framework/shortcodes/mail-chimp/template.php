<?php
/**
 * Shortcode attributes
 * @var $atts
 * @var $mc_icon_color
 * @var $layout_style
 * @var $css_animation
 * @var $animation_duration
 * @var $animation_delay
 * @var $el_class
 * @var $css
 * @var $responsive
 * Shortcode class
 * @var $this WPBakeryShortCode_GSF_Mail_Chimp
 */
$mc_icon_color = $layout_style = $css_animation = $animation_duration = $animation_delay = $el_class = $css = $responsive = '';
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$wrapper_classes = array(
	'gsf-mail-chimp',
	G5P()->core()->vc()->customize()->getExtraClass($el_class),
	$this->getCSSAnimation($css_animation),
	vc_shortcode_custom_css_class( $css ),
	$responsive,
	'mailchimp-'. $layout_style
);

if( empty($mc_btn_color)) $mc_btn_color="#6ea820";
$mc_custom_class = 'mail-chimp-' . uniqid();
$mc_css = '';
if($layout_style== 'style-02'){
	$mc_css = <<<CSS
	.{$mc_custom_class} button>i{
		color: {$mc_icon_color};
	}
CSS;
}

GSF()->customCss()->addCss($mc_css);
$wrapper_classes[] = $mc_custom_class;

if ('' !== $css_animation && 'none' !== $css_animation) {
	$animation_class = G5P()->core()->vc()->customize()->get_animation_class($animation_duration, $animation_delay);
	$wrapper_classes[] = $animation_class;
}

$class_to_filter = implode(' ', array_filter($wrapper_classes));
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts);
?>
<div class="<?php echo esc_attr($css_class) ?>">
<?php echo do_shortcode('[mc4wp_form]') ?>
</div>
