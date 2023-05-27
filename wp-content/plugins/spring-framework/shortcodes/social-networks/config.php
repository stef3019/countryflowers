<?php
return array(
	'base' => 'gsf_social_networks',
	'name' => esc_html__( 'Social Networks', 'spring-framework' ),
	'icon' => 'fa fa-share-alt',
	'category' => G5P()->shortcode()->get_category_name(),
	'params' => array(
		array(
			'param_name' => 'social_networks',
			'heading' => esc_html__('Social Networks', 'spring-framework'),
			'type' => 'gsf_selectize',
			'multiple' => true,
			'drag' => true,
			'description' => esc_html__('Select Social Networks', 'spring-framework'),
			'value' => array_flip(G5P()->settings()->get_social_networks())
		),
		array(
			'param_name' => 'social_shape',
			'heading' => esc_html__('Social Shape', 'spring-framework'),
			'type' => 'dropdown',
			'value' => array(
				esc_html__( 'Classic', 'spring-framework' ) => 'classic',
				esc_html__( 'Circle Fill', 'spring-framework' ) => 'circle',
                esc_html__( 'Circle Outline', 'spring-framework' ) => 'circle-outline',
                esc_html__( 'Square', 'spring-framework' ) => 'square',
			),
			'std' => 'classic'
		),
        array(
            'param_name' => 'social_size',
            'heading' => esc_html__('Social Size', 'spring-framework'),
            'type' => 'dropdown',
            'value' => array(
                esc_html__( 'Small', 'spring-framework' ) => 'small',
                esc_html__( 'Normal', 'spring-framework' ) => 'normal',
                esc_html__( 'Large', 'spring-framework' ) => 'large'
            ),
            'std' => 'normal'
        ),
		array(
			'type' => 'textfield',
			'heading' => esc_html__('Text Before Icon', 'spring-framework'),
			'param_name' => 'social_text',
			'value' => '',
			'admin_label' => true,
		),
		G5P()->shortcode()->vc_map_add_css_animation(),
		G5P()->shortcode()->vc_map_add_animation_duration(),
		G5P()->shortcode()->vc_map_add_animation_delay(),
		G5P()->shortcode()->vc_map_add_extra_class(),
		G5P()->shortcode()->vc_map_add_css_editor(),
		G5P()->shortcode()->vc_map_add_responsive()
	),
);