<?php
return array(
	'base' => 'gsf_portfolio_meta',
	'name' => esc_html__( 'Portfolio Meta', 'spring-framework' ),
	'icon' => 'fa fa-info-circle',
	'category' => G5P()->shortcode()->get_category_name(),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__('Meta Title', 'spring-framework'),
			'param_name' => 'title',
			'admin_label' => true,
		),
        array(
            'param_name'       => 'layout_style',
            'heading'    => esc_html__('Layout', 'spring-framework'),
            'type'     => 'gsf_button_set',
            'value'  => array(
                esc_html__('Vertical', 'spring-framework') => 'vertical',
                esc_html__('Horizontal', 'spring-framework') => 'horizontal'
            ),
            'std'  => 'horizontal'
        ),
        array(
            'type' => 'gsf_switch',
            'heading' => esc_html__('Include share?', 'spring-framework'),
            'param_name' => 'include_share',
            'dependency' => array('element' => 'layout_style', 'value'=> 'vertical'),
            'std' => ''
        ),
        G5P()->shortcode()->vc_map_add_title(array(
            'param_name'       => 'share_title',
            'heading' => esc_html__( 'Share Title', 'spring-framework' ),
            'dependency' => array('element' => 'include_share', 'value'=> 'on'),
            'std'  => esc_html__( 'Share', 'spring-framework' )
        )),
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
            'dependency' => array('element' => 'include_share', 'value'=> 'on'),
            'std' => 'classic'
        ),
		G5P()->shortcode()->vc_map_add_css_animation(),
		G5P()->shortcode()->vc_map_add_animation_duration(),
		G5P()->shortcode()->vc_map_add_animation_delay(),
		G5P()->shortcode()->vc_map_add_extra_class(),
		G5P()->shortcode()->vc_map_add_css_editor(),
		G5P()->shortcode()->vc_map_add_responsive()
	),
);