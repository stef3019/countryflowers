<?php
/**
 * The template for displaying config.php
 *
 */
return array(
	'base' => 'gsf_page_subtitle',
	'name' => esc_html__('Page Subtitle', 'spring-framework'),
	'category' => G5P()->shortcode()->get_category_name(),
	'icon' => 'fa fa-subscript',
	'params' => array(
		array(
			'type' => 'font_container',
			'param_name' => 'font_container',
			'value' => 'text_align:left|color:#333333',
			'settings' => array(
				'fields' => array(
					'text_align' =>'left',
					'color' => '#333333',
					'text_align_description' => __( 'Select text alignment', 'spring-framework' ),
					'color_description' => __( 'Select text color', 'spring-framework' ),
				),
			),
		),
        array(
            'type' => 'gsf_number',
            'heading' => esc_html__('Font size', 'spring-framework'),
            'param_name' => 'title_font_size',
            'value' => '16'
        ),
		array(
			'type' => 'gsf_switch',
			'heading' => __( 'Use theme default font family?', 'spring-framework' ),
			'param_name' => 'use_theme_fonts',
            'std' => 'on',
			'description' => __( 'Use font family from the theme.', 'spring-framework' ),
		),
		array(
			'type' => 'gsf_typography',
			'param_name' => 'typography',
            'dependency' => array('element' => 'use_theme_fonts', 'value_not_equal_to' => 'on')
		),
		G5P()->shortcode()->vc_map_add_css_animation(),
		G5P()->shortcode()->vc_map_add_animation_duration(),
		G5P()->shortcode()->vc_map_add_animation_delay(),
		G5P()->shortcode()->vc_map_add_extra_class(),
		G5P()->shortcode()->vc_map_add_css_editor(),
		G5P()->shortcode()->vc_map_add_responsive()
	)
);