<?php
return array(
	'base' => 'gsf_counter',
	'name' => esc_html__( 'Counter', 'spring-framework' ),
	'icon' => 'fa fa-tachometer',
	'category' => G5P()->shortcode()->get_category_name(),
	'params' => array(
		G5P()->shortcode()->vc_map_add_title(array('admin_label' => true)),
		array(
			'type'             => 'textfield',
			'heading'          => esc_html__('Sub Title', 'spring-framework'),
			'param_name'       => 'subtitle',
			'value'            => '',
			'std'              => 'Subtitle',
			'edit_field_class' => 'vc_col-sm-12 vc_column'
		),
		array(
			'type'             => 'textfield',
			'heading'          => esc_html__('Start Value', 'spring-framework'),
			'param_name'       => 'start',
			'value'            => '',
			'std'              => '0',
			'edit_field_class' => 'vc_col-sm-6 vc_column'
		),
		array(
			'type'             => 'textfield',
			'heading'          => esc_html__('End Value', 'spring-framework'),
			'param_name'       => 'end',
			'value'            => '',
			'std'              => '1000',
			'admin_label' => true,
			'edit_field_class' => 'vc_col-sm-6 vc_column'
		),
		array(
			'type'             => 'textfield',
			'heading'          => esc_html__('Decimals', 'spring-framework'),
			'param_name'       => 'decimals',
			'value'            => '',
			'std'              => '0',
			'edit_field_class' => 'vc_col-sm-6 vc_column'
		),
		array(
			'type'             => 'textfield',
			'heading'          => esc_html__('Duration (s)', 'spring-framework'),
			'param_name'       => 'duration',
			'value'            => '',
			'std'              => '2,5',
			'edit_field_class' => 'vc_col-sm-6 vc_column'
		),
		array(
			'type'             => 'textfield',
			'heading'          => esc_html__('Separator', 'spring-framework'),
			'param_name'       => 'separator',
			'value'            => '',
			'std'              => '',
			'edit_field_class' => 'vc_col-sm-6 vc_column'
		),
		array(
			'type'             => 'textfield',
			'heading'          => esc_html__('Decimal', 'spring-framework'),
			'param_name'       => 'decimal',
			'value'            => '',
			'std'              => '.',
			'edit_field_class' => 'vc_col-sm-6 vc_column'
		),
		array(
			'type'             => 'textfield',
			'heading'          => esc_html__('Prefix', 'spring-framework'),
			'param_name'       => 'prefix',
			'value'            => '',
			'edit_field_class' => 'vc_col-sm-6 vc_column'
		),
		array(
			'type'             => 'textfield',
			'heading'          => esc_html__('Suffix', 'spring-framework'),
			'param_name'       => 'suffix',
			'value'            => '',
			'edit_field_class' => 'vc_col-sm-6 vc_column'
		),
		G5P()->shortcode()->vc_map_add_icon_font(),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__('ICon Color', 'spring-framework'),
			'param_name' => 'icon_color',
			'std' => '#333',
			'edit_field_class' => 'vc_col-sm-6 vc_column'
		),
		G5P()->shortcode()->vc_map_add_css_animation(),
		G5P()->shortcode()->vc_map_add_animation_duration(),
		G5P()->shortcode()->vc_map_add_animation_delay(),
		G5P()->shortcode()->vc_map_add_extra_class(),
		G5P()->shortcode()->vc_map_add_css_editor(),
		G5P()->shortcode()->vc_map_add_responsive()
	),
);