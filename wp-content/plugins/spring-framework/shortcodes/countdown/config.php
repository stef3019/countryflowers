<?php
/**
 * Created by PhpStorm.
 * User: Kaga
 * Date: 20/5/2016
 * Time: 3:57 PM
 */
return array(
	'name'        => esc_html__('Countdown', 'spring-framework'),
	'base'        => 'gsf_countdown',
	'class'       => '',
	'icon'        => 'fa fa-clock-o',
    'category' => G5P()->shortcode()->get_category_name(),
	'params'      => array(
		array(
			'type' => 'gsf_image_set',
			'heading' => esc_html__('Layout Style', 'spring-framework'),
			'param_name' => 'layout_style',
			'value' => apply_filters('gsf_countdown_layout_style',array(
				'style-01' => array(
					'label' => esc_html__('Style 01', 'spring-framework'),
					'img' => G5P()->pluginUrl('assets/images/shortcode/countdown-style-01.png'),
				),
				'style-02' => array(
					'label' => esc_html__('Style 02', 'spring-framework'),
					'img' => G5P()->pluginUrl('assets/images/shortcode/countdown-style-02.png'),
				)
			)),
			'std' => 'style-01',
			'admin_label' => true,
		),
		array(
			'type'       => 'gsf_datetimepicker',
			'heading'    => esc_html__('Time Off', 'spring-framework'),
			'param_name' => 'time',
		),
		array(
			'type'       => 'textfield',
			'heading'    => esc_html__('Url Redirect', 'spring-framework'),
			'param_name' => 'url_redirect',
			'value'      => '',
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__('Date Color', 'spring-framework'),
			'param_name' => 'number_color',
			'std' => '#333',
			'edit_field_class' => 'vc_col-sm-6 vc_column',
		),
		array(
			'type' => 'gsf_switch',
			'heading' => __( 'Use Theme Default Font family for Number Text?', 'spring-framework' ),
			'param_name' => 'title_use_theme_fonts',
			'std' => 'on',
		),
		array(
			'type' => 'gsf_typography',
			'param_name' => 'title_typography',
			'dependency' => array('element' => 'title_use_theme_fonts', 'value_not_equal_to' => 'on'),
		),
        G5P()->shortcode()->vc_map_add_css_animation(),
        G5P()->shortcode()->vc_map_add_animation_duration(),
        G5P()->shortcode()->vc_map_add_animation_delay(),
        G5P()->shortcode()->vc_map_add_extra_class(),
        G5P()->shortcode()->vc_map_add_css_editor(),
        G5P()->shortcode()->vc_map_add_responsive()
	)
);