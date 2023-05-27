<?php
/**
 * Shortcode attributes
 * @var $atts
 * @var $use_theme_fonts
 * @var $typography
 * @var $font_container
 * @var $css_animation
 * @var $animation_duration
 * @var $animation_delay
 * @var $el_class
 * @var $css
 * @var $responsive
 * Shortcode class
 * @var $this WPBakeryShortCode_GSF_Page_Title
 */
$use_theme_fonts = $font_container = $typography = $css_animation = $animation_duration = $animation_delay = $el_class = $css = $responsive = '';
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$wrapper_classes = array(
	'page-title-container',
	G5P()->core()->vc()->customize()->getExtraClass($el_class),
	$this->getCSSAnimation($css_animation),
	vc_shortcode_custom_css_class( $css ),
	$responsive
);

if ('' !== $css_animation && 'none' !== $css_animation) {
	$animation_class = G5P()->core()->vc()->customize()->get_animation_class($animation_duration, $animation_delay);
	$wrapper_classes[] = $animation_class;
}

$attributes = $this->get_font_container_attributes($font_container);
$page_title_class = 'page-title-' . random_int(1000, 9999);
$page_title_css = <<<CSS
	.{$page_title_class} {
		font-size: {$attributes[0]} !important;
		color: {$attributes[1]} !important;
		margin-bottom: 10px;
		margin-top: 0;
    }
CSS;
$heading_class = array($page_title_class, $attributes[2]);
if('on' !== $use_theme_fonts) {
    if(empty($typography)) {
        $font = GSF()->core()->fonts()->getActiveFonts()[0];
        $font_family = $font_variant = $font_weight = $font_style = '';
        $font_family = isset($font['name']) ? $font['name'] : $font['family'];
        $font_variant = isset($font['variants'][0]) ? $font['variants'][0] : '400';
        if(strpos($font_variant, 'i') && strpos($font_variant, 'i') != -1) {
            $font_style = 'italic';
            $font_weight = substr($font_variant, 0, strpos($font_variant, 'i'));
            if(!$font_weight || '' == $font_weight) {
                $font_weight = '400';
            }
        } else {
            $font_style = 'normal';
            if($font_variant == 'regular') {
                $font_weight = '400';
            } else {
                $font_weight = $font_variant;
            }
        }
        $typography = array($font_family, $font_variant, $font_weight, $font_style);
    } else {
        $typography = explode('|', $typography);
    }

    $page_title_css .= <<<CSS
	.{$page_title_class} {
		font-family: '{$typography[0]}' !important;
		font-weight: {$typography[2]} !important;
		font-style: {$typography[3]} !important;
    }
CSS;
}
GSF()->customCss()->addCss($page_title_css);
$heading_class = implode(' ', array_filter($heading_class));

$class_to_filter = implode(' ', array_filter($wrapper_classes));
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts);
?>
<div class="<?php echo esc_attr($css_class) ?>">
	<?php $page_title = '';
	if(function_exists( 'Spring_Plant' )){
		$page_title = Spring_Plant()->helper()->get_page_title();
	}
	echo '<h1 class="'.$heading_class.'">' . $page_title . '</h1>';
	?>
</div>