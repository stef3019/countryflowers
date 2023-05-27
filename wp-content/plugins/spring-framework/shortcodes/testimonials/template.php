<?php
/**
 * Shortcode attributes
 * @var $atts
 * @var $layout_style
 * @var $columns_gutter
 * @var $image
 * @var $tes_style
 * @var $item_active
 * @var $values
 * @var $dots
 * @var $nav
 * @var $nav_position
 * @var $nav_style
 * @var $autoplay
 * @var $autoplay_timeout
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
 * @var $author_job_color
 * @var $author_name_color
 * @var $responsive
 * Shortcode class
 * @var $this WPBakeryShortCode_GSF_Testimonials
 */

$layout_style = $columns_gutter = $author_job_color = $author_name_color= $image = $tes_style = $values = $dots = $nav = $nav_position = $nav_style = $dot_style = $autoplay = $autoplay_timeout = $item_active =
$columns = $columns_md = $columns_sm = $columns_xs = $columns_mb = $css_animation = $animation_duration = $animation_delay = $el_class = $css = $responsive = '';
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$wrapper_classes = array(
	'gsf-testimonials',
	'owl-carousel owl-theme',
	'testimonials-' . $layout_style,
	'clearfix',
	G5P()->core()->vc()->customize()->getExtraClass($el_class),
	$this->getCSSAnimation($css_animation),
	vc_shortcode_custom_css_class( $css ),
	$responsive
);
if(empty($author_job_color)) $author_job_color = "#7d7d7d";
if(empty($author_name_color)) $author_name_color = "#333";
$t_custom_class = 'author-custom' .uniqid();
$testimonial_css = <<<CSS
	.{$t_custom_class} .author-name{
		color: {$author_name_color} !important;
	}
	.{$t_custom_class}  .author-job{
		color: {$author_job_color} !important;
	}
CSS;
GSF()->customCss()->addCss($testimonial_css);
$wrapper_classes[] = $t_custom_class;

if ('' !== $css_animation && 'none' !== $css_animation) {
	$animation_class = G5P()->core()->vc()->customize()->get_animation_class($animation_duration, $animation_delay);
	$wrapper_classes[] = $animation_class;
}

$columns = intval($columns);
$columns_gutter = intval($columns_gutter);
$columns_md = intval($columns_md);
$columns_sm = intval($columns_sm);
$columns_xs = intval($columns_xs);
$columns_mb = intval($columns_mb);
$owl_attributes = '';
if('style-04' == $layout_style) {
	$owl_attributes = array(
		'items' => $columns,
		'margin' => $columns_gutter,
		'dots' => ($dots === 'on') ? true : false,
		'nav' => ($nav === 'on') ? true : false,
		'autoHeight' => true,
		'autoplay' => ($autoplay === 'on') ? true : false,
		'autoplayTimeout' => $autoplay_timeout,
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
			'575' => array(
				'items' => $columns_xs,
				'margin' => $columns_gutter,
				'slideBy' => $columns_xs,
			),
			'0' => array(
				'items' => $columns_mb,
				'margin' => $columns_gutter,
				'slideBy' => $columns_mb,
			)
		)
	);
} else if('style-03' == $layout_style){
	$owl_attributes = array(
		'items' => $item_active,
		'dots' => ($dots === 'on') ? true : false,
		'nav' => ($nav === 'on') ? true : false,
		'autoplay' => ($autoplay === 'on') ? true : false,
		'center' =>true
	);
}
else {
	$owl_attributes = array(
		'items' => 1,
		'margin' => 0,
		'dots' => ($dots === 'on') ? true : false,
		'nav' => ($nav === 'on') ? true : false,
		'autoHeight' => true,
		'autoplay' => ($autoplay === 'on') ? true : false,
		'autoplayTimeout' => $autoplay_timeout,
		'navText' => ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>']
	);
}
if($nav_style == 'style-01') {
	$owl_attributes['navText'] = array('<i class="flaticon-slim-left"></i>','<i class="flaticon-slim-right"></i>');
}
if( $nav_style == 'style-04'){
	$owl_attributes['navText'] = array('<i class="flaticon-left-arrow-1"></i>','<i class="flaticon-right-arrow-1"></i>');
}
elseif ($nav_style == 'style-03' || $nav_style == 'style-02') {
	$owl_attributes['navText'] = array('<i class="flaticon-left-arrow"></i>','<i class="flaticon-right-arrow"></i>');
}
if($nav === 'on') {
	$wrapper_classes[] =' ' . $nav_position. ' nav-' . $nav_style;;
}
if('on' === $dots) {
	$wrapper_classes[] = 'dots-' .$dot_style;
}
if (!(defined('CSS_DEBUG') && CSS_DEBUG)) {
	wp_enqueue_style(G5P()->assetsHandle('g5-testimonials'), G5P()->helper()->getAssetUrl('shortcodes/testimonials/assets/css/testimonials.min.css'), array(), G5P()->pluginVer());
}

wp_enqueue_script(G5P()->assetsHandle('testimonials'), G5P()->helper()->getAssetUrl('shortcodes/testimonials/assets/js/testimonials.js'), array( 'jquery' ), G5P()->pluginVer(), true);

$class_to_filter = implode(' ', array_filter($wrapper_classes));
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts);
?>
<?php if($layout_style == 'style-03'): ?>
	<?php G5P()->helper()->getTemplate('shortcodes/testimonials/templates/style-03', array('values' => $values, 'item_active' => $item_active,'owl_attributes' =>$owl_attributes,'t_custom_class' =>$t_custom_class,'nav_style' => $nav_style, 'dot_style' => $dot_style)); ?>
 <?php else: ?>
<div class="<?php echo esc_attr($css_class) ?>" data-owl-options='<?php echo json_encode($owl_attributes); ?>'>
	<?php
	$values = (array)vc_param_group_parse_atts($values);
	$index = 0;
	?>
	<?php foreach ($values as $value):
		$index++;
		$name = isset($value['author_name']) ? $value['author_name'] : '';
		$job = isset($value['author_job']) ? $value['author_job'] : '';
		$bio = isset($value['author_bio']) ? $value['author_bio'] : '';
		$url = isset($value['author_link']) ? $value['author_link'] : '';
		$avatar = isset($value['author_avatar']) ? $value['author_avatar'] : '';
		$image_src = '';
		if (!empty($avatar)) {
			$image_src = G5P()->image_resize()->resize(array(
				'image_id' => $avatar,
				'width' => 80,
				'height' => 80
			));
			if (isset($image_src['url']) && ($image_src['url'] !== '')) {
				$image_src = $image_src['url'];
			}
		}
		?>
		<?php G5P()->helper()->getTemplate('shortcodes/testimonials/templates/' . $layout_style, array('name'=>$name, 'job' => $job, 'bio' => $bio,'image_src' => $image_src, 'url' => $url, 'image' => $image,'index'=>$index)); ?>
	<?php endforeach; ?>
</div>
<?php endif; ?>