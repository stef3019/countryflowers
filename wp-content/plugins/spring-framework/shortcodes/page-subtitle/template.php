<?php
/**
 * Shortcode attributes
 * @var $atts
 * @var $use_theme_fonts
 * @var $typography
 * @var $font_container
 * @var $title_font_size
 * @var $css_animation
 * @var $animation_duration
 * @var $animation_delay
 * @var $el_class
 * @var $css
 * @var $responsive
 * Shortcode class
 * @var $this WPBakeryShortCode_GSF_Page_Subtitle
 */
$use_theme_fonts = $font_container = $title_font_size =
$typography = $css_animation = $animation_duration = $animation_delay = $el_class = $css = $responsive = '';
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$wrapper_classes = array(
	'page-subtitle-container',
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
$title_class = 'gf-page-subtitle-' . uniqid();
$title_css = '';
$title_css .= <<<CSS
    .{$title_class} {
        color: {$attributes[0]} !important;
        font-size: {$title_font_size}px !important;
    }
CSS;
$title_classes = array(
    $attributes[1],
    $title_class
);
if('on' !== $use_theme_fonts) {
    if(empty($typography)) {
        $font = GSF_Core_Fonts::getInstance()->getActiveFonts()[0];
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
    $title_css .= <<<CSS
        .{$title_class} {
            font-family: {$typography[0]} !important;
            font-weight: {$typography[2]} !important;
            font-style: {$typography[3]} !important;
        }
CSS;
}
GSF()->customCss()->addCss($title_css);
$title_class = implode(' ', array_filter($title_classes));
$wrapper_class = implode(' ', array_filter($wrapper_classes));
?>
<div class="<?php echo esc_attr($wrapper_class) ?>">
	<?php $page_subtitle = '';
	if(function_exists( 'Spring_Plant' )){
		$page_subtitle = Spring_Plant()->helper()->get_page_subtitle();
	}
	if(!empty($page_subtitle)):
	?>
        <p class="<?php echo esc_attr($title_class) ?>"><?php echo esc_html($page_subtitle); ?></p>
    <?php endif; ?>
</div>