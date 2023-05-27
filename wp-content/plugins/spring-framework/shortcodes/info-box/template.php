<?php
/**
 * Shortcode attributes
 * @var $atts
 * @var $layout_style
 * @var $icon_bg_style
 * @var $icon_color
 * @var $icon_bg_color
 * @var $ib_bg_color
 * @var $icon_size
 * @var $icon_align
 * @var $title
 * @var $subtitle
 * @var $title_font_size
 * @var $content
 * @var $des_letter_spacing
 * @var $icon_font
 * @var $ib_box_shadow
 * @var $link
 * @var $css_animation
 * @var $animation_duration
 * @var $animation_delay
 * @var $el_class
 * @var $hover_des_color
 * @var $css
 * @var $hover_bg_color
 * @var $icon_hover_color
 * @var $hover_text_color
 * @var $title_color
 * @var $responsive
 * @var $image
 * Shortcode class
 * @var $this WPBakeryShortCode_GSF_Info_Box
 */

$layout_style = $image = $icon_bg_style = $title_color = $hover_des_color = $icon_hover_color = $hover_text_color = $icon_color = $icon_bg_color = $icon_size = $icon_align = $title = $title_font_size = $icon_font = $link =
$css_animation = $animation_duration = $animation_delay = $el_class = $css = $responsive = '';
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);
$wrapper_classes = array(
	'gsf-info-box',
	$layout_style,
	'clearfix',
	$icon_align,
	$icon_bg_style,
	$icon_size,
	G5P()->core()->vc()->customize()->getExtraClass($el_class),
	$this->getCSSAnimation($css_animation),
	vc_shortcode_custom_css_class($css),
	$responsive
);

// animation
if ('' !== $css_animation && 'none' !== $css_animation) {
	$animation_class = G5P()->core()->vc()->customize()->get_animation_class($animation_duration, $animation_delay);
	$wrapper_classes[] = $animation_class;
}

if (empty($icon_color)) $icon_color = '#6ea820';
if (empty($icon_bg_color)) $icon_bg_color = $icon_color;
if (empty($ib_bg_color)) $ib_bg_color = 'transparent';
if (empty($icon_hover_color)) $icon_hover_color = $icon_color;
if (empty($hover_text_color)) $hover_text_color = '#333';
if (empty($hover_des_color)) $hover_des_color = '#333';
if (empty($title_color)) $title_color = '#363636';
$ib_custom_class = 'info-box-' . uniqid();
$icon_box_css = <<<CSS
    .{$ib_custom_class} .info-box-title {
        font-size: {$title_font_size}px !important;
    };
	 .{$ib_custom_class} .ib-content{
	 	background-color:  {$ib_bg_color}!important;
	 }
	.{$ib_custom_class}:hover .info-box-title{
	 	color:  {$hover_text_color}!important;
	 }
	.{$ib_custom_class}:hover .info-box-subtitle{
	 	color:  {$hover_text_color}!important;
	 }
	.{$ib_custom_class} .info-box-title{
	 	color:  {$title_color}!important;
	 }
	.{$ib_custom_class}:hover .info-box-des{
	 	color:  {$hover_des_color}!important;
	 }
CSS;
$icon_box_css .= <<<CSS
	 .{$ib_custom_class}{
	 	background-color:  {$ib_bg_color}!important;
	 }
CSS;
if (empty($hover_bg_color)) $hover_bg_color = $ib_bg_color;
$icon_box_css .= <<<CSS
	 .{$ib_custom_class}:hover{
	 	background-color:  {$hover_bg_color}!important;
	 }
	.{$ib_custom_class}:hover .ib-shape-inner i{
	 	color:  {$icon_hover_color}!important;
	 }
CSS;
if (in_array($icon_bg_style, array('icon-bg-circle-fill', 'icon-bg-square-fill'))) {
	$icon_box_css .= <<<CSS
        .{$ib_custom_class} .ib-shape-inner{
            background-color: {$icon_bg_color};
            border-color: {$icon_bg_color} !important;
            color: {$icon_color};
        }
CSS;
} elseif (in_array($icon_bg_style, array('icon-bg-circle-outline', 'icon-bg-square-outline'))) {
	$icon_box_css .= <<<CSS
        .{$ib_custom_class} .ib-shape-inner {
            border-color: {$icon_bg_color} !important;
            color: {$icon_color};
        }
CSS;
} else {
	$icon_box_css .= <<<CSS
        .{$ib_custom_class} .ib-shape-inner {
            color: {$icon_color};
        }
CSS;
}
if ('on' == $ib_box_shadow) {
	$wrapper_classes[] = 'ib-shadow';
}
if ('on' !== $use_theme_fonts) {
	if (empty($typography)) {
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
		$typography = array($font_family, $font_variant, $font_weight, $font_style);
	} else {
		$typography = explode('|', $typography);
	}
	$icon_box_css .= <<<CSS
        .{$ib_custom_class} .info-box-title {
            font-family: {$typography[0]} !important;
            font-weight: {$typography[2]} !important;
            font-style: {$typography[3]} !important;
        }
CSS;
}

GSF()->customCss()->addCss($icon_box_css);
$wrapper_classes[] = $ib_custom_class;

$ib_class = array(
	'ib-shape-inner'
);

//parse link
$link_attributes = $title_attributes = array();
$link = ('||' === $link) ? '' : $link;
$link = vc_build_link($link);
$use_link = false;
if (strlen($link['url']) > 0) {
	$use_link = true;
	$link_attributes[] = 'href="' . esc_url(trim($link['url'])) . '"';
	if (strlen($link['target']) > 0) {
		$link_attributes[] = 'target="' . trim($link['target']) . '"';
	}
	if (strlen($link['rel']) > 0) {
		$link_attributes[] = 'rel="' . trim($link['rel']) . '"';
	}
	$title_attributes = $link_attributes;
	if (strlen($link['title']) > 0) {
		$link_attributes[] = 'title="' . trim($link['title']) . '"';
	}

	if (!empty($title)) {
		$title_attributes[] = 'title="' . esc_attr(trim($title)) . '"';
    }



}

// icon html
$icon_html = '';
if ('icon' === $icon_type && !empty($icon_font)) {
	$icon_html = '<i class="' . esc_attr($icon_font) . '"></i>';
} elseif ('image' === $icon_type && !empty($image)) {
	$image_src = '';
	if ('icon-classic' === $icon_bg_style) {
		$img = wp_get_attachment_image_src($image, 'full');
		if (!empty($img) && isset($image[0])) {
			$image_src = $img[0];
		}
	} else {
		$img =  G5P()->image_resize()->resize(array(
			'image_id' => $image,
			'width' => 160,
			'height' => 160
		));
		if (isset($img['url']) && ($img['url'] !== '')) {
			$image_src = $img['url'];
		}
	}
	if (!empty($image_src)) {
		$alt = '';
		if (!empty($title)) {
			$alt = sprintf(' alt="%s"',esc_attr($title));
		}
		$icon_html = '<img'. $alt .' src="' . esc_url($image_src) . '">';
    }
}
$class_to_filter = implode(' ', array_filter($wrapper_classes));
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts);
if (!(defined('CSS_DEBUG') && CSS_DEBUG)) {
	wp_enqueue_style(G5P()->assetsHandle('info-box'), G5P()->helper()->getAssetUrl('shortcodes/info-box/assets/css/info-box.min.css'), array(), G5P()->pluginVer());
}

?>
<div class="<?php echo esc_attr($css_class) ?>">
	<?php if ($layout_style == 'style-02' || $layout_style == 'style-01' || $layout_style == 'style-04'): ?>
		<?php if (!empty($title) || !empty($content) || !empty($icon_html)): ?>
			<div class="ib-content">
				<div class="ib-shape">
					<div class="<?php echo implode(' ', $ib_class); ?>">
						<?php if ($use_link): ?>
							<a <?php echo implode(' ', $link_attributes); ?> class="gsf-link">
								<?php echo wp_kses_post($icon_html); ?>
							</a>
						<?php else:
							echo wp_kses_post($icon_html);
						endif; ?>
					</div>
				</div>
				<?php if (!empty($title)):
					if ($use_link): ?>
						<h4 class="info-box-title"><a <?php echo implode(' ', $title_attributes); ?> class="gsf-link">
								<span><?php echo esc_attr($title) ?></span>
							</a></h4>
					<?php else: ?>
						<h4 class="info-box-title"><?php echo esc_attr($title) ?></h4>
					<?php endif;
				endif;
				if (!empty($subtitle)): ?>
					<p class="info-box-subtitle"><?php echo wp_kses_post($subtitle); ?></p>
				<?php endif; ?>
				<?php if (!empty($content)): ?>
					<div class="info-box-des">
						<?php echo wpb_js_remove_wpautop($content, true); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	<?php elseif ($layout_style = 'style-03'): ?>
		<div class="ib-shape">
			<div class="<?php echo implode(' ', $ib_class); ?>">
				<?php if ($use_link): ?>
					<a <?php echo implode(' ', $link_attributes); ?> class="gsf-link">
						<?php echo wp_kses_post($icon_html); ?>
					</a>
				<?php else:
					echo wp_kses_post($icon_html);
				endif; ?>
			</div>
		</div>
		<?php if (!empty($title) || !empty($content)): ?>
			<div class="ib-content">
				<?php if (!empty($subtitle)): ?>
					<p class="info-box-subtitle"><?php echo wp_kses_post($subtitle); ?></p>
				<?php endif; ?>
				<?php if (!empty($title)):
					if ($use_link): ?>
						<h4 class="info-box-title"><a <?php echo implode(' ', $title_attributes); ?> class="gsf-link">
								<span><?php echo esc_attr($title) ?></span>
							</a></h4>
					<?php else: ?>
						<h4 class="info-box-title"><?php echo esc_attr($title) ?></h4>
					<?php endif;
				endif; ?>
				<?php if (!empty($content)): ?>
					<div class="info-box-des">
						<?php echo wpb_js_remove_wpautop($content, true); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</div>
