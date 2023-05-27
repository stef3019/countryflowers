<?php
/**
 * Shortcode attributes
 * @var $atts
 * @var $layout_style
 * @var $title
 * @var $title_color
 * @var $title_letter_spacing
 * @var $title_font_size
 * @var $sub_title
 * @var $sub_title_font_size
 * @var $sub_title_color
 * @var $sub_title_letter_spacing
 * @var $content
 * @var $icon_font
 * @var $text_align
 * @var $title_use_theme_fonts
 * @var $title_typography
 * @var $sub_title_use_theme_fonts
 * @var $sub_title_typography
 * @var $css_animation
 * @var $animation_duration
 * @var $animation_delay
 * @var $el_class
 * @var $css
 * @var $responsive
 * Shortcode class
 * @var $this WPBakeryShortCode_GSF_Heading
 */

$layout_style = $title = $title_font_size = $title_color = $title_letter_spacing = $sub_title = $sub_title_font_size = $sub_title_color = $sub_title_letter_spacing = $icon_font = $text_align = $title_use_theme_fonts =
$title_typography = $sub_title_use_theme_fonts = $sub_title_typography =
$css_animation = $animation_duration = $animation_delay = $el_class = $css = $responsive = '';
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

if(in_array($layout_style, array('style-02', 'style-03'))) {
    $text_align = 'text-center';
}
$wrapper_classes = array(
	'gf-heading',
	'gf-heading-'.$layout_style,
	$text_align,
	G5P()->core()->vc()->customize()->getExtraClass($el_class),
	$this->getCSSAnimation($css_animation),
	vc_shortcode_custom_css_class( $css ),
	$responsive
);

if ('' !== $css_animation && 'none' !== $css_animation) {
	$animation_class = G5P()->core()->vc()->customize()->get_animation_class($animation_duration, $animation_delay);
	$wrapper_classes[] = $animation_class;
}

if(in_array($layout_style, array('style-02', 'style-04'))) {
    $content = '';
}

$heading_class = 'gf-heading-' . uniqid();
$heading_css = '';
if(!empty($title)) {
    $heading_css .= <<<CSS
        .{$heading_class} .heading-title {
            font-size: {$title_font_size}px !important;
            line-height: {$title_font_size}px !important;
            color: {$title_color} !important;
            letter-spacing: {$title_letter_spacing}em !important;
        }
CSS;

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
        $heading_css .= <<<CSS
        .{$heading_class} .heading-title {
            font-family: {$title_typography[0]} !important;
            font-weight: {$title_typography[2]} !important;
            font-style: {$title_typography[3]} !important;
}
CSS;
    }
    if('style-02' !== $layout_style) {
        if($title_font_size > 34) {
            $heading_css .= <<<CSS
        @media (max-width: 575px) {
            .{$heading_class} .heading-title {
                font-size: 34px !important;
                line-height: 34px !important;
            }
        }
CSS;
        }
    } else {
        if($title_font_size > 100) {
            $heading_css .= <<<CSS
        @media (max-width: 575px) {
            .{$heading_class} .heading-title {
                font-size: 100px !important;
                line-height: 100px !important;
            }
        }
CSS;
        }
    }
}
if(!empty($sub_title)) {
    $heading_css .= <<<CSS
        .{$heading_class} .heading-sub-title {
            font-size: {$sub_title_font_size}px !important;
            line-height: {$sub_title_font_size}px !important;
            color: {$sub_title_color} !important;
            letter-spacing: {$sub_title_letter_spacing}em;
        }
CSS;
    if ('on' !== $sub_title_use_theme_fonts) {
        if (empty($sub_title_typography)) {
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
            $sub_title_typography = array($font_family, $font_variant, $font_weight, $font_style);
        } else {
            $sub_title_typography = explode('|', $sub_title_typography);
        }
        $heading_css .= <<<CSS
    .{$heading_class} .heading-sub-title {
        font-family: {$sub_title_typography[0]} !important;
        font-weight: {$sub_title_typography[2]} !important;
        font-style: {$sub_title_typography[3]} !important;
    }
CSS;
    }
}

GSF()->customCss()->addCss($heading_css);

if (!(defined('CSS_DEBUG') && CSS_DEBUG)) {
	wp_enqueue_style(G5P()->assetsHandle('g5-heading'), G5P()->helper()->getAssetUrl('shortcodes/heading/assets/css/heading.min.css'), array(), G5P()->pluginVer());
}

$class_to_filter = implode(' ', array_filter($wrapper_classes));
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts);
?>
<?php if(!empty( $title ) || !empty( $sub_title ) || !empty($content)): ?>
<div class="<?php echo esc_attr($css_class) ?>">
	<div class="gf-heading-inner <?php echo esc_attr( $heading_class ); ?>">
        <?php if('style-04' !== $layout_style): ?>
            <?php if (!empty($sub_title)): ?>
                <span class="heading-sub-title"><?php echo wp_kses_post($sub_title); ?></span>
            <?php endif; ?>
            <?php if (!empty($title)): ?>
                <h4 class="heading-title"><?php echo wp_kses_post($title); ?></h4>
                <?php if('style-03' === $layout_style && !empty($icon_font)): ?>
                    <span class="<?php echo esc_attr($icon_font); ?>"></span>
                <?php endif; ?>
            <?php endif; ?>
            <?php if(!empty($content)): ?>
                <div class="heading-description">
                    <?php echo wpb_js_remove_wpautop( $content, true ); ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <?php if (!empty($title)): ?>
                <h4 class="heading-title"><?php echo wp_kses_post($title); ?></h4>
            <?php endif; ?>
            <?php if (!empty($sub_title)): ?>
                <span class="heading-sub-title"><?php echo wp_kses_post($sub_title); ?></span>
            <?php endif; ?>
        <?php endif; ?>
	</div>
</div>
<?php endif; ?>