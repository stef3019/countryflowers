<?php
/**
 * Shortcode attributes
 * @var $atts
 * @var $layout_style
 * @var $values
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
 * @var $this WPBakeryShortCode_GSF_Process
 */
$layout_style = $values = $columns = $columns_md = $columns_sm = $columns_xs = $columns_mb = $css_animation = $animation_duration = $animation_delay = $el_class = $css = $responsive = '';

$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$values = (array)vc_param_group_parse_atts($values);
if(empty($values)) {
    return;
}
$wrapper_classes = array(
    'gsf-process',
    'gsf-process-' . $layout_style,
    G5P()->core()->vc()->customize()->getExtraClass($el_class),
    $this->getCSSAnimation($css_animation),
    vc_shortcode_custom_css_class( $css ),
    $responsive
);

if ('' !== $css_animation && 'none' !== $css_animation) {
    $animation_class = G5P()->core()->vc()->customize()->get_animation_class($animation_duration, $animation_delay);
    $wrapper_classes[] = $animation_class;
}

$class_to_filter = implode(' ', array_filter($wrapper_classes));
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts);

if (!(defined('CSS_DEBUG') && CSS_DEBUG)) {
    wp_enqueue_style(G5P()->assetsHandle('gf-process'), G5P()->helper()->getAssetUrl('shortcodes/process/assets/css/process.min.css'), array(), G5P()->pluginVer());
}
$index = 1;
$item_class = array('process-item');
$inner_class = array('gsf-process-inner');
if('style-01' === $layout_style) {
    $item_class = array_merge($item_class, array(G5P()->helper()->get_bootstrap_columns(array(
        'xl' => $columns,
        'lg' => $columns_md,
        'md' => $columns_sm,
        'sm' => $columns_xs,
        '' => $columns_mb
    ))));
    $inner_class[] = 'row';
}
?>

<div class="<?php echo esc_attr($css_class) ?>">
    <div class="<?php echo join(' ', $inner_class); ?>">
        <?php foreach ($values as $value):
            $process_index = 'style-02' === $layout_style ? ('0'. $index) : $index;
            $title = isset($value['title']) ? $value['title'] : '';
            $description = isset($value['description']) ? $value['description'] : '';
            $link = isset($value['link']) ? $value['link'] : '';
            $use_link = false;
            $a_attr = array();
            $link = vc_build_link($link);
            if (isset($link['url']) && !empty($link['url'])) {
                $use_link = true;
                $a_title = isset($link['title']) ? $link['title'] : '';
                if(!empty($a_title) && empty($title)) {
                    $title = $a_title;
                }
                $a_attr = array(
                    'class="gsf-link transition03"',
                    'href="' . esc_url(trim($link['url'])) . '"'
                );
                if(isset($link['target']) && !empty($link['target'])) {
                    $a_attr[] = 'target="' . esc_attr(trim($link['target'])) . '"';
                }
                if(isset($link['rel']) && !empty($link['rel'])) {
                    $a_attr[] = 'rel="' . esc_attr(trim($link['rel'])) . '"';
                }
                if(!empty($a_title)) {
                    $a_attr[] = 'title="' . esc_attr(trim($a_title)) . '"';
                }
            }
            ?>
            <div class="<?php echo join(' ', $item_class); ?>">
                <div class="process-item-inner">
                    <div class="process-index primary-font">
                        <span><?php echo esc_html($process_index) ?></span>
                    </div>
                    <div class="process-content">
                        <?php if(!empty($title)): ?>
                            <h3 class="process-title">
                                <?php if($use_link): ?>
                                    <a <?php echo join(' ', $a_attr); ?>>
                                <?php endif; ?>
                                <?php echo esc_html($title); ?>
                                <?php if($use_link): ?>
                                    </a>
                                <?php endif; ?>
                            </h3>
                        <?php endif; ?>
                        <?php if(!empty($description)): ?>
                            <div class="process-desc">
                                <?php  echo(rawurldecode(base64_decode(strip_tags($description)))); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php
        $index ++;
        endforeach; ?>
    </div>
</div>