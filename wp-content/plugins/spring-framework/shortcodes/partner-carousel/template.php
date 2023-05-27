<?php
/**
 * Shortcode attributes
 * @var $atts
 * @var $partners
 * @var $items
 * @var $columns_gutter
 * @var $opacity
 * @var $items_md
 * @var $items_sm
 * @var $items_xs
 * @var $items_mb
 * @var $css_animation
 * @var $animation_duration
 * @var $animation_delay
 * @var $el_class
 * @var $css
 * @var $responsive
 * Shortcode class
 * @var $this WPBakeryShortCode_GSF_Partner_Carousel
 */
$partners = $items = $columns_gutter = $opacity = $items_md = $items_sm = $items_xs = $items_mb =
$css_animation = $animation_duration = $animation_delay = $el_class = $css = $responsive = '';
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$wrapper_attributes = array();
$wrapper_styles = array();

$wrapper_classes = array(
	'gsf-partner',
	'owl-carousel',
	'clearfix',
    G5P()->core()->vc()->customize()->getExtraClass($el_class),
    $this->getCSSAnimation($css_animation),
    vc_shortcode_custom_css_class( $css ),
    $responsive
);

if ('' !== $css_animation && 'none' !== $css_animation) {
    $animation_class = G5P()->core()->vc()->customize()->get_animation_class($animation_duration, $animation_delay);
    $wrapper_classes[] = $animation_class;
}

if ($items_md == -1) {
	$items_md = 4;
}

if ($items_sm == -1) {
	$items_sm = 3;
}

if ($items_xs == -1) {
	$items_xs = 2;
}

if ($items_mb == -1) {
	$items_mb = 2;
}

if(intval($opacity) <0 || intval($opacity) > 100) {
    $opacity = 100;
}

$owl_attributes = array(
    'items' => intval($items),
	'autoHeight' => true,
	'loop' => false,
	'margin' => intval($columns_gutter),
	'responsive' => array(
	    '0' => array(
	        'items' => intval($items_mb)
        ),
        '575' => array(
            'items' => intval($items_xs)
        ),
        '768' => array(
            'items' => intval($items_sm)
        ),
        '992' => array(
            'items' => intval($items_md)
        ),
        '1200' => array(
            'items' => intval($items)
        )
    )
);
$class_to_filter = implode(' ', array_filter($wrapper_classes));
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts);

if (!(defined('CSS_DEBUG') && CSS_DEBUG)) {
    wp_enqueue_style(G5P()->assetsHandle('partner-carousel'), G5P()->helper()->getAssetUrl('shortcodes/partner-carousel/assets/css/partner-carousel.min.css'), array(), G5P()->pluginVer());
}
?>

<div class="<?php echo esc_attr($css_class) ?>" data-owl-options='<?php echo json_encode( $owl_attributes );?>'>
	<?php
	$values = (array)vc_param_group_parse_atts($partners);
	foreach ($values as $data) {
		$partner_img = isset($data['image']) ? $data['image'] : '';
		if (!empty($partner_img)) {
			$partner_img = wp_get_attachment_image_src($partner_img, 'full');
			if(!empty($partner_img)) {
				$partner_img = $partner_img[0];
			}
        }
        if (empty($partner_img)) continue;
		$link = isset($data['link']) ? $data['link'] : '';
		$link = ($link == '||') ? '' : $link;
		$link_arr = vc_build_link($link);
		$a_title = '';
		$a_target = '_blank';
		$a_href = '#';
        $use_link = false;

        $a_attributes = array();
        $img_attributes = array();

		if (strlen($link_arr['url']) > 0) {
            $use_link = true;
			$a_href = $link_arr['url'];
			$a_title = $link_arr['title'];
			$a_target = strlen($link_arr['target']) > 0 ? $link_arr['target'] : '_blank';
		}

		$img_attributes[] = sprintf('src="%s"',esc_url($partner_img));
		$a_attributes[] = sprintf('href="%s"',esc_attr($a_href));
		$a_attributes[] = sprintf('target="%s"',esc_attr($a_target));
		if (!empty($a_title)) {
			$a_attributes[] = sprintf('title="%s"',esc_attr($a_title));
			$img_attributes[] = sprintf('alt="%s"',esc_attr($a_title));
        }
		?>
        <div class='partner-item' style="opacity: <?php echo esc_attr($opacity / 100) ?>">
            <?php if ($use_link): ?>
            <a <?php echo join(' ', $a_attributes)?>>
                <?php endif; ?>
                    <img <?php echo join(' ', $img_attributes); ?>>
                <?php if ($use_link): ?>
            </a>
        <?php endif; ?>
        </div>
	<?php
	}
	?>
</div>