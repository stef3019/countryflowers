<?php
/**
 * The template for displaying config.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
return array(
	'base' => 'gsf_breadcrumbs',
	'name' => esc_html__('Breadcrumbs', 'spring-framework'),
	'category' => G5P()->shortcode()->get_category_name(),
    'icon' => 'fa fa-code',
	'params' => array(
		array(
			'type' => 'dropdown',
			'heading' => esc_html__('Alignment', 'spring-framework'),
			'param_name' => 'align',
			'description' => esc_html__('Select text alignment.', 'spring-framework'),
			'value' => array(
				esc_html__('Left', 'spring-framework') => 'left',
				esc_html__('Center', 'spring-framework') => 'center',
				esc_html__('Right', 'spring-framework') => 'right'
			),
			'std' => 'left',
			'admin_label' => true,
		),
		G5P()->shortcode()->vc_map_add_css_animation(),
		G5P()->shortcode()->vc_map_add_animation_duration(),
		G5P()->shortcode()->vc_map_add_animation_delay(),
		G5P()->shortcode()->vc_map_add_extra_class(),
		G5P()->shortcode()->vc_map_add_css_editor(),
		G5P()->shortcode()->vc_map_add_responsive()
	)
);