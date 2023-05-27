<?php
return array(
	'base' => 'gsf_heading',
	'name' => esc_html__( 'Heading', 'spring-framework' ),
	'icon' => 'fa fa-header',
	'category' => G5P()->shortcode()->get_category_name(),
	'params' => array(
        array(
            'type' => 'gsf_image_set',
            'heading' => esc_html__('Layout Style', 'spring-framework'),
            'param_name' => 'layout_style',
            'value' => apply_filters('gsf_heading_layout_style',array(
                'style-01' => array(
                    'label' => esc_html__('Style 01', 'spring-framework'),
                    'img' => G5P()->pluginUrl('assets/images/shortcode/heading-01.png'),
                ),
                'style-02' => array(
                    'label' => esc_html__('Style 02', 'spring-framework'),
                    'img' => G5P()->pluginUrl('assets/images/shortcode/heading-02.png'),
                ),
                'style-03' => array(
                    'label' => esc_html__('Style 03', 'spring-framework'),
                    'img' => G5P()->pluginUrl('assets/images/shortcode/heading-03.png'),
                ),
                'style-04' => array(
                    'label' => esc_html__('Style 04', 'spring-framework'),
                    'img' => G5P()->pluginUrl('assets/images/shortcode/heading-04.png'),
                )
            )),
            'std' => 'style-01',
            'admin_label' => true,
        ),
		G5P()->shortcode()->vc_map_add_title(array(
		    'admin_label' => true,
            'edit_field_class' => 'vc_col-sm-6 vc_column'
        )),
        array(
            'type' => 'gsf_number',
            'heading' => esc_html__('Title font size', 'spring-framework'),
            'param_name' => 'title_font_size',
            'std' => 48,
            'dependency' => array('element' => 'title', 'value_not_equal_to' => array('')),
            'group' => esc_html__('Title Options', 'spring-framework')
        ),
        array(
            'type' => 'colorpicker',
            'heading' => __( 'Title color', 'spring-framework' ),
            'param_name' => 'title_color',
            'std' => '#333',
            'edit_field_class' => 'vc_col-sm-6 vc_column',
            'group' => esc_html__('Title Options', 'spring-framework'),
            'dependency' => array('element' => 'title', 'value_not_equal_to' => array(''))
        ),
        array(
            'type' => 'dropdown',
            'heading' => esc_html__('Title Letter Spacing', 'spring-framework'),
            'param_name' => 'title_letter_spacing',
            'value' => array(
                '0' => '0',
                '100' => '0.1',
                '200' => '0.2',
                '300' => '0.3',
                '400' => '0.4',
                '500' => '0.5',
                '600' => '0.6',
                '700' => '0.7',
                '800' => '0.8',
                '900' => '0.9',
                '1000' => '1',
            ),
            'std' => '0',
            'edit_field_class' => 'vc_col-sm-6 vc_column',
            'group' => esc_html__('Title Options', 'spring-framework'),
            'dependency' => array('element' => 'title', 'value_not_equal_to' => array(''))
        ),
        array(
            'type' => 'textfield',
            'heading' => esc_html__('Subtitle', 'spring-framework'),
            'param_name' => 'sub_title',
            'edit_field_class' => 'vc_col-sm-6 vc_column'
        ),
        array(
            'type' => 'gsf_number',
            'heading' => esc_html__('Subtitle font size', 'spring-framework'),
            'param_name' => 'sub_title_font_size',
            'std' => 14,
            'dependency' => array('element' => 'sub_title', 'value_not_equal_to' => array('')),
            'group' => esc_html__('Sub Title Options', 'spring-framework')
        ),
        array(
            'type' => 'colorpicker',
            'heading' => __( 'Sub Title color', 'spring-framework' ),
            'param_name' => 'sub_title_color',
            'std' => G5P()->options()->get_accent_color(),
            'edit_field_class' => 'vc_col-sm-6 vc_column',
            'group' => esc_html__('Sub Title Options', 'spring-framework'),
            'dependency' => array('element' => 'sub_title', 'value_not_equal_to' => array(''))
        ),
        array(
            'type' => 'dropdown',
            'heading' => esc_html__('Sub Title Letter Spacing', 'spring-framework'),
            'param_name' => 'sub_title_letter_spacing',
            'value' => array(
                '0' => '0',
                '100' => '0.1',
                '200' => '0.2',
                '300' => '0.3',
                '400' => '0.4',
                '500' => '0.5',
                '600' => '0.6',
                '700' => '0.7',
                '800' => '0.8',
                '900' => '0.9',
                '1000' => '1',
            ),
            'std' => '0.2',
            'edit_field_class' => 'vc_col-sm-6 vc_column',
            'group' => esc_html__('Sub Title Options', 'spring-framework'),
            'dependency' => array('element' => 'sub_title', 'value_not_equal_to' => array(''))
        ),
        array(
            'type' => 'textarea_html',
            'heading' => esc_html__( 'Descriptions', 'spring-framework' ),
            'param_name' => 'content',
            'dependency' => array('element' => 'layout_style', 'value' => array('style-01', 'style-03'))
        ),
        G5P()->shortcode()->vc_map_add_icon_font(array(
            'dependency' => array('element' => 'layout_style', 'value' => array('style-03'))
        )),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__('Text Alignment', 'spring-framework'),
			'param_name' => 'text_align',
			'description' => esc_html__('Select text alignment.', 'spring-framework'),
			'value' => array(
				esc_html__('Left', 'spring-framework') => 'text-left',
				esc_html__('Center', 'spring-framework') => 'text-center',
				esc_html__('Right', 'spring-framework') => 'text-right'
			),
			'std' => 'text-center',
			'admin_label' => true,
            'dependency' => array('element' => 'layout_style', 'value' => array('style-01', 'style-04'))
		),
        array(
            'type' => 'gsf_switch',
            'heading' => __( 'Use Theme Default Font family for Heading title?', 'spring-framework' ),
            'param_name' => 'title_use_theme_fonts',
            'std' => 'on',
            'dependency' => array('element' => 'title', 'value_not_equal_to' => array('')),
            'group' => esc_html__('Title Options', 'spring-framework')
        ),
        array(
            'type' => 'gsf_typography',
            'param_name' => 'title_typography',
            'dependency' => array('element' => 'title_use_theme_fonts', 'value_not_equal_to' => 'on'),
            'group' => esc_html__('Title Options', 'spring-framework')
        ),
        array(
            'type' => 'gsf_switch',
            'heading' => __( 'Use Theme Default Font family for Heading sub title?', 'spring-framework' ),
            'param_name' => 'sub_title_use_theme_fonts',
            'std' => 'on',
            'dependency' => array('element' => 'sub_title', 'value_not_equal_to' => array('')),
            'group' => esc_html__('Sub Title Options', 'spring-framework')
        ),
        array(
            'type' => 'gsf_typography',
            'param_name' => 'sub_title_typography',
            'dependency' => array('element' => 'sub_title_use_theme_fonts', 'value_not_equal_to' => 'on'),
            'group' => esc_html__('Sub Title Options', 'spring-framework')
        ),
		G5P()->shortcode()->vc_map_add_css_animation(),
		G5P()->shortcode()->vc_map_add_animation_duration(),
		G5P()->shortcode()->vc_map_add_animation_delay(),
		G5P()->shortcode()->vc_map_add_extra_class(),
		G5P()->shortcode()->vc_map_add_css_editor(),
		G5P()->shortcode()->vc_map_add_responsive()
	),
);