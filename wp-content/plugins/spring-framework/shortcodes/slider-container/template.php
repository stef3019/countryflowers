<?php
/**
 * Shortcode attributes
 * @var $content
 * @var $atts
 * @var $dots
 * @var $dot_style
 * @var $nav
 * @var $nav_position
 * @var $nav_style
 * @var $autoplay
 * @var $autoplay_timeout
 * @var $margin
 * @var $columns
 * @var $columns_md
 * @var $columns_sm
 * @var $columns_xs
 * @var $columns_mb
 * @var $css_animation
 * @var $animation_duration
 * @var $animation_delay
 * @var $el_class
 * @var $css
 * @var $responsive
 * Shortcode class
 * @var $this WPBakeryShortCode_GSF_Slider_Container
 */
$dots = $dot_style = $nav = $nav_position = $nav_style = $autoplay = $autoplay_timeout = $margin = $columns = $columns_md = $columns_sm = $columns_xs = $columns_mb =
$css_animation = $animation_duration = $animation_delay = $el_class = $css = $responsive = '';
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$wrapper_classes = array(
	'gsf-slider-container',
    'owl-carousel owl-theme',
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
$columns = intval($columns);
$columns_gutter = intval($margin);
$columns_md = intval($columns_md);
$columns_sm = intval($columns_sm);
$columns_xs = intval($columns_xs);
$columns_mb = intval($columns_mb);

$owl_args = array(
    'items' => $columns,
    'margin' => $columns == 1 ? 0 : $columns_gutter,
    'slideBy' => $columns,
    'dots' => ($dots === 'on') ? true : false,
    'nav' => ($nav === 'on') ? true : false,
    'responsive' => array(
        '1200' => array(
            'items' => $columns,
            'margin' => $columns_gutter,
            'slideBy' => $columns,
        ),
        '992' => array(
            'items' => $columns_md,
            'margin' => $columns_gutter,
            'slideBy' => $columns_md,
        ),
        '768' => array(
            'items' => $columns_sm,
            'margin' => $columns_gutter,
            'slideBy' => $columns_sm,
        ),
        '576' => array(
            'items' => $columns_xs,
            'margin' => $columns_gutter,
            'slideBy' => $columns_xs,
        ),
        '0' => array(
            'items' => $columns_mb,
            'margin' => $columns_gutter,
            'slideBy' => $columns_mb,
        )
    ),
    'autoHeight' => true,
    'autoplay' => ($autoplay === 'on') ? true : false,
    'autoplayTimeout' => intval($autoplay_timeout)
);

if($nav_style !== 'style-01' && $nav_style !== 'style-04') {
    $owl_args['navText'] = array('<i class="flaticon-left-arrow"></i>','<i class="flaticon-right-arrow"></i>');
}
else{
	$owl_args['navText'] = array('<i class="flaticon-left-arrow-1"></i>','<i class="flaticon-right-arrow-1"></i>');
}
if($nav === 'on') {
    $wrapper_classes[] = ' ' . $nav_position. ' nav-' . $nav_style;
}
if('on' === $dots) {
    $wrapper_classes[] = 'dots-' .$dot_style;
}
$class_to_filter = implode(' ', array_filter($wrapper_classes));
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts);

?>
<div class="<?php echo esc_attr($css_class) ?>" data-owl-options='<?php echo json_encode($owl_args); ?>'>
	<?php G5P()->helper()->shortCodeContent($content) ?>
</div>