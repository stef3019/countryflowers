<?php
/**
 * Shortcode attributes
 * @var $atts
 * @var $title
 * @var $layout_style
 * @var $include_share
 * @var $share_title
 * @var $social_shape
 * @var $el_class
 * @var $css_animation
 * @var $animation_duration
 * @var $animation_delay
 * @var $css
 * @var $this WPBakeryShortCode_GSF_Portfolio_Meta
 */
$title = $layout_style = $include_share = $share_title = $social_shape = $el_class = $css_animation = $animation_duration = $animation_delay = $css = '';
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$wrapper_classes = array(
    'gf-portfolio-meta-wrap',
    'clearfix',
    G5P()->core()->vc()->customize()->getExtraClass($el_class),
    $this->getCSSAnimation($css_animation),
    vc_shortcode_custom_css_class($css),
    $responsive
);

if ('' !== $css_animation && 'none' !== $css_animation) {
    $animation_class = G5P()->core()->vc()->customize()->get_animation_class($animation_duration, $animation_delay);
    $wrapper_classes[] = $animation_class;
}

$class_to_filter = implode(' ', array_filter($wrapper_classes));
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts);
?>
<div class="<?php echo esc_attr($css_class) ?>">
    <?php if (!empty($title)): ?>
        <h4 class="portfolio-meta-title"><?php echo esc_html($title); ?></h4>
    <?php endif; ?>
	<?php if (function_exists('Spring_Plant')) {
        Spring_Plant()->helper()->getTemplate('portfolio/single/portfolio-meta', array('layout' => $layout_style));
	} ?>
    <?php if('on' === $include_share && 'vertical' === $layout_style){
        $defaults = array(
            'layout'         => $social_shape,
            'show_title'     => false,
            'page_permalink' => '',
            'page_title'     => '',
            'share_title' => $share_title
        );
        G5P()->helper()->getTemplate('inc/templates/social-share', $defaults);
    } ?>
</div>
