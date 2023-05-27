<?php
return array(
	'base' => 'gsf_social_share',
	'name' => esc_html__( 'Social Share', 'spring-framework' ),
	'icon' => 'fa fa-share',
	'category' => G5P()->shortcode()->get_category_name(),
	'params' => array(
        G5P()->shortcode()->vc_map_add_title(array(
            'heading' => esc_html__( 'Share Title', 'spring-framework' ),
            'admin_label' => true,
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