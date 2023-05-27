<?php
/**
 * The template for displaying config.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
return array(
	'base' => 'gsf_border',
	'name' => esc_html__('Border', 'spring-framework'),
	'category' => G5P()->shortcode()->get_category_name(),
	'icon' => 'fa fa-window-minimize',
	'params' => array(
		array(
			'type' => 'gsf_switch',
			'heading' => __( 'Use skin border color?', 'spring-framework' ),
			'param_name' => 'use_skin_border_color',
            'std' => 'on',
            'edit_field_class' => 'vc_col-sm-6 vc_column'
		),
		array(
			'type' => 'colorpicker',
            'heading' => __( 'Border color', 'spring-framework' ),
			'param_name' => 'border_color',
            'dependency' => array('element' => 'use_skin_border_color', 'value_not_equal_to' => 'on'),
            'edit_field_class' => 'vc_col-sm-6 vc_column'
		),
        array(
            'type' => 'gsf_switch',
            'heading' => __( 'Custom border width?', 'spring-framework' ),
            'param_name' => 'custom_border_width',
            'std' => '',
            'edit_field_class' => 'vc_col-sm-6 vc_column'
        ),
        array(
            'type' => 'gsf_number',
            'heading' => __( 'Custom Width', 'spring-framework' ),
            'param_name' => 'custom_width',
            'std' => '100px',
            'dependency' => array('element' => 'custom_border_width', 'value' => 'on'),
            'edit_field_class' => 'vc_col-sm-6 vc_column'
        ),
        array(
            'type' => 'gsf_button_set',
            'heading' => __( 'Alignment', 'spring-framework' ),
            'param_name' => 'alignment',
            'std' => 'text-center',
            'dependency' => array('element' => 'custom_border_width', 'value' => 'on'),
            'value' => array(
                esc_html__('Left', 'spring-framework') =>'text-left',
                esc_html__('Center', 'spring-framework') => 'text-center',
                esc_html__('Right', 'spring-framework') => 'text-right'
            ),
            'edit_field_class' => 'vc_col-sm-6 vc_column'
        ),
		array(
		    'type' => 'gsf_number',
            'heading' => __( 'Border height', 'spring-framework' ),
            'param_name' => 'border_height',
            'std' => 1,
            'admin_label' => true
        ),
		G5P()->shortcode()->vc_map_add_css_animation(),
		G5P()->shortcode()->vc_map_add_animation_duration(),
		G5P()->shortcode()->vc_map_add_animation_delay(),
		G5P()->shortcode()->vc_map_add_extra_class(),
		G5P()->shortcode()->vc_map_add_css_editor(),
		G5P()->shortcode()->vc_map_add_responsive()
	)
);