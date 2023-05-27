<?php
/**
 * Shortcode attributes
 * @var $atts
 * @var $time
 * @var $url_redirect
 * @var $css_animation
 * @var $animation_duration
 * @var $animation_delay
 * @var $el_class
 * @var $css
 * @var $responsive
 * Shortcode class
 * @var $this WPBakeryShortCode_GSF_Countdown
 */
$time = $url_redirect = $css_animation = $animation_duration = $animation_delay = $el_class = $css = $responsive = '';
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$wrapper_classes = array(
	'gsf-countdown',
	G5P()->core()->vc()->customize()->getExtraClass($el_class),
	$this->getCSSAnimation($css_animation),
	vc_shortcode_custom_css_class($css),
	$responsive
);
if (empty($number_color)) $number_color = '#6ea820';
$ib_custom_class = 'countdown-' . uniqid();
$icon_box_css = <<<CSS
        .{$ib_custom_class} .countdown-value {
            color:{$number_color}!important;
        }
CSS;
$wrapper_classes[] = $ib_custom_class;
GSF()->customCss()->addCss($icon_box_css);

$cd_font_class = 'cd-font-' . uniqid();
$cd_font_css = '';
if ('on' !== $title_use_theme_fonts) {
	if (empty($title_typography)) {
		$font = GSF()->core()->fonts()->getActiveFonts()[0];
		$font_family = $font_variant = $font_weight = $font_style = '';
		$font_family = isset($font['name']) ? $font['name'] : $font['family'];
		$font_variant = isset($font['variants'][0]) ? $font['variants'][0] : '400';
		if (strpos($font_variant, 'i') && strpos($font_variant, 'i') != -1) {
			$font_style = 'italic';
			$font_weight = substr($font_variant, 0, strpos($font_variant, 'i'));
			if (!$font_weight || '' == $font_weight) {
				$font_weight = '400';
			}
		} else {
			$font_style = 'normal';
			if ($font_variant == 'regular') {
				$font_weight = '400';
			} else {
				$font_weight = $font_variant;
			}
		}
		$title_typography = array($font_family, $font_variant, $font_weight, $font_style);
	} else {
		$title_typography = explode('|', $title_typography);
	}
	$cd_font_css .= <<<CSS
        .{$cd_font_class} .countdown-value {
            font-family: {$title_typography[0]} !important;
            font-weight: {$title_typography[2]} !important;
            font-style: {$title_typography[3]} !important;
}
CSS;
}
GSF()->customCss()->addCss($cd_font_css);
if ('' !== $css_animation && 'none' !== $css_animation) {
	$animation_class = G5P()->core()->vc()->customize()->get_animation_class($animation_duration, $animation_delay);
	$wrapper_classes[] = $animation_class;
}
$class_to_filter = implode(' ', array_filter($wrapper_classes));
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts);

if (!(defined('CSS_DEBUG') && CSS_DEBUG)) {
	wp_enqueue_style(G5P()->assetsHandle('g5-countdown'), G5P()->helper()->getAssetUrl('shortcodes/countdown/assets/css/countdown.min.css'), array(), G5P()->pluginVer());
}
wp_enqueue_script(G5P()->assetsHandle('countdown-js'), G5P()->helper()->getAssetUrl('shortcodes/countdown/assets/js/countdown.min.js'), array('jquery'), G5P()->pluginVer(), true);

if (!empty($time)) {
	$time = mysql2date('Y/m/d H:i:s', $time);
	
	?>
<div class="<?php echo esc_attr($css_class) ?>"
	 data-url-redirect="<?php echo esc_attr($url_redirect) ?>"
	 data-date-end="<?php echo esc_attr($time); ?>">
	<?php if ($layout_style == 'style-02'): ?>
	<div class="gsf-countdown-inner countdown-style-02 <?php echo esc_attr( $cd_font_class ); ?>">
	<?php else: ?>
	<div class="gsf-countdown-inner <?php echo esc_attr( $cd_font_class ); ?>">
<?php endif; ?>
	<div class="countdown-section">
		<span class="countdown-value countdown-day">00</span>
		<span class="countdown-text"><?php esc_html_e('Days', 'spring-framework'); ?></span>
	</div>
	<div class="countdown-section">
		<span class="countdown-value countdown-hours">00</span>
		<span class="countdown-text"><?php esc_html_e('Hours', 'spring-framework'); ?></span>
	</div>
	<div class="countdown-section">
		<span class="countdown-value countdown-minutes">00</span>
		<span class="countdown-text"><?php esc_html_e('Mins', 'spring-framework'); ?></span>
	</div>
	<div class="countdown-section">
		<span class="countdown-value countdown-seconds">00</span>
		<span class="countdown-text"><?php esc_html_e('Secs', 'spring-framework'); ?></span>
	</div>
	</div>
	</div>
	<?php
}