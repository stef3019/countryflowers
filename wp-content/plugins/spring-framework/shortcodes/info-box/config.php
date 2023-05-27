<?php
return array(
	'base' => 'gsf_info_box',
	'name' => esc_html__('Info Box','spring-framework'),
	'icon' => 'fa fa-diamond',
	'category' => G5P()->shortcode()->get_category_name(),
	'params' => array(
		array(
			'type' => 'gsf_image_set',
			'heading' => esc_html__('Layout Style', 'spring-framework'),
			'param_name' => 'layout_style',
			'value' => apply_filters('gsf_info_box_layout_style',array(
				'style-01' => array(
					'label' => esc_html__('Style 01', 'spring-framework'),
					'img' => G5P()->pluginUrl('assets/images/shortcode/info-box-style-03.png'),
				),
				'style-02' => array(
					'label' => esc_html__('Style 02', 'spring-framework'),
					'img' => G5P()->pluginUrl('assets/images/shortcode/info-box-style-01.png'),
				),
				'style-03' => array(
					'label' => esc_html__('Style 03', 'spring-framework'),
					'img' => G5P()->pluginUrl('assets/images/shortcode/info-box-style-02.png'),
				),
				'style-04' => array(
					'label' => esc_html__('Style 04', 'spring-framework'),
					'img' => G5P()->pluginUrl('assets/images/shortcode/info-box-style-04.png'),
				)
			)),
			'std' => 'style-01',
			'admin_label' => true,
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__('Title', 'spring-framework' ),
			'param_name' => 'title',
			'value' => '',
			'admin_label' => true,
		),
		array(
			'type' => 'gsf_switch',
			'heading' => __( 'Use theme default font family?', 'spring-framework' ),
			'param_name' => 'use_theme_fonts',
			'std' => 'on',
			'description' => __( 'Use font family from the theme.', 'spring-framework' ),
			'dependency' => array('element' => 'title', 'value_not_equal_to' => array(''))
		),
		array(
			'type' => 'gsf_typography',
			'param_name' => 'typography',
			'dependency' => array('element' => 'use_theme_fonts', 'value_not_equal_to' => 'on')
		),
		
        array(
            'type' => 'gsf_number',
            'heading' => esc_html__('Title Font Size', 'spring-framework'),
            'param_name' => 'title_font_size',
            'std' => 20
        ),
		array(
			'type' => 'textfield',
			'heading' => esc_html__('Sub Title', 'spring-framework' ),
			'param_name' => 'subtitle',
			'value' => '',
			'admin_label' => true,
		),
		array(
			'type' => 'textarea_html',
			'heading' => esc_html__('Description', 'spring-framework'),
			'param_name' => 'content',
			'description' => esc_html__('Provide the description for this element.', 'spring-framework')
		),
		array(
			'type' => 'gsf_button_set',
			'heading' => esc_html__('Icon Type', 'spring-framework'),
			'param_name' => 'icon_type',
			'group' => esc_html__('Icon Options', 'spring-framework'),
			'value' => array(
				esc_html__('Icon', 'spring-framework') => 'icon',
				esc_html__('Image', 'spring-framework') => 'image',
			),
			'std' => 'icon'
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__('Title Color', 'spring-framework'),
			'param_name' => 'title_color',
			'std' => '#363636',
			'edit_field_class' => 'vc_col-sm-6 vc_column'
		),
		array(
			'type'        => 'attach_image',
			'heading'     => esc_html__('Images', 'spring-framework'),
			'param_name'  => 'image',
			'group' => esc_html__('Icon Options', 'spring-framework'),
			'value'       => '',
			'description' => esc_html__('Select images from media library.', 'spring-framework'),
			'dependency' => array('element' => 'icon_type', 'value' => 'image')
		),
        array(
            'type' => 'dropdown',
            'heading' => esc_html__('Icon Background Style', 'spring-framework'),
            'param_name' => 'icon_bg_style',
            'value' => array(
                esc_html__('Classic', 'spring-framework') => 'icon-classic',
                esc_html__('Circle - Fill Color', 'spring-framework') => 'icon-bg-circle-fill',
                esc_html__('Circle - Outline', 'spring-framework') => 'icon-bg-circle-outline',
                esc_html__('Square - Fill Color', 'spring-framework') => 'icon-bg-square-fill',
                esc_html__('Square - Outline', 'spring-framework') => 'icon-bg-square-outline'
            ),
            'std' => 'icon-classic',
            'group' => esc_html__('Icon Options', 'spring-framework'),
            'description' => esc_html__('Select Icon Background Style.', 'spring-framework'),
			'dependency' => array('element' => 'icon_type', 'value' => 'icon')
        ),
		G5P()->shortcode()->vc_map_add_icon_font(array(
			'group' => esc_html__('Icon Options', 'spring-framework'),
			'dependency' => array('element' => 'icon_type', 'value' => 'icon')
		)),
        array(
            'type' => 'colorpicker',
            'heading' => esc_html__('Icon Color', 'spring-framework'),
            'param_name' => 'icon_color',
            'std' => G5P()->options()->get_accent_color(),
            'group' => esc_html__('Icon Options', 'spring-framework'),
            'edit_field_class' => 'vc_col-sm-6 vc_column',
			'dependency' => array('element' => 'icon_type', 'value' => 'icon'),
        ),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__('Icon Color', 'spring-framework'),
			'param_name' => 'icon_hover_color',
			'std' => '#e0e0e0',
			'description' => __( 'Choose icon color when hover', 'spring-framework' ),
			'group' => esc_html__('Hover Options', 'spring-framework'),
			'edit_field_class' => 'vc_col-sm-6 vc_column',
			'dependency' => array('element' => 'icon_type', 'value' => 'icon'),
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__('Background Color', 'spring-framework'),
			'param_name' => 'ib_bg_color',
			'std' => G5P()->options()->get_foreground_accent_color(),
			'edit_field_class' => 'vc_col-sm-6 vc_column',
		),
        array(
            'type' => 'colorpicker',
            'heading' => esc_html__('Background Color', 'spring-framework'),
            'param_name' => 'icon_bg_color',
            'std' => '#333',
            'group' => esc_html__('Icon Options', 'spring-framework'),
            'edit_field_class' => 'vc_col-sm-6 vc_column',
            'dependency' => array('element'=>'icon_bg_style', 'value_not_equal_to'=>'icon-classic')
			
        ),
        array(
            'type' => 'dropdown',
            'heading' => esc_html__('Icon Size', 'spring-framework'),
            'param_name' => 'icon_size',
            'value' => array(
                esc_html__('Large', 'spring-framework') => 'ib-large',
                esc_html__('Medium', 'spring-framework') => 'ib-medium',
                esc_html__('Small', 'spring-framework') => 'ib-small'
            ),
            'std' => 'ib-large',
            'group' => esc_html__('Icon Options', 'spring-framework'),
            'description' => esc_html__('Select Color Scheme.', 'spring-framework'),
            'edit_field_class' => 'vc_col-sm-6 vc_column',
			'dependency' => array('element' => 'icon_type', 'value' => 'icon'),
        ),
        array(
            'type' => 'gsf_button_set',
            'heading' => esc_html__('Icon Vertical Alignment', 'spring-framework'),
            'param_name' => 'icon_align',
            'value' => array(
                esc_html__('Top', 'spring-framework') => 'icon-align-top',
                esc_html__('Middle', 'spring-framework') => 'icon-align-middle'
            ),
            'std' => 'icon-align-top',
            'group' => esc_html__('Icon Options', 'spring-framework'),
            'description' => esc_html__('Select Icon Vertical Alignment.', 'spring-framework'),
            'dependency' => array('element'=>'layout_style', 'value'=>array('ib-left','ib-right')),
            'edit_field_class' => 'vc_col-sm-6 vc_column',
        ),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__('Background Color', 'spring-framework'),
			'param_name' => 'hover_bg_color',
			'std' => '#fff',
			'description' => __( 'Choose background color when hover', 'spring-framework' ),
			'group' => esc_html__('Hover Options', 'spring-framework'),
			'edit_field_class' => 'vc_col-sm-6 vc_column'
		),
		array(
			'type' => 'gsf_switch',
			'heading' => __( 'Box Shadow', 'spring-framework' ),
			'param_name' => 'ib_box_shadow',
			'std' => 'off',
			'group' => esc_html__('Hover Options', 'spring-framework'),
			'description' => __( 'Show box shadow when hover', 'spring-framework' ),
			'edit_field_class' => 'vc_col-sm-6 vc_column'
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__('Title & Subtitle Color', 'spring-framework'),
			'param_name' => 'hover_text_color',
			'std' => '#333',
			'description' => __( 'Choose color when hover', 'spring-framework' ),
			'group' => esc_html__('Hover Options', 'spring-framework'),
			'edit_field_class' => 'vc_col-sm-6 vc_column'
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__('Description Color', 'spring-framework'),
			'param_name' => 'hover_des_color',
			'std' => '#7d7d7d',
			'description' => __( 'Choose color when hover', 'spring-framework' ),
			'group' => esc_html__('Hover Options', 'spring-framework'),
			'edit_field_class' => 'vc_col-sm-6 vc_column'
		),
		array(
			'type' => 'vc_link',
			'heading' => esc_html__('Link (url)', 'spring-framework'),
			'param_name' => 'link',
			'value' => '',
		),
        G5P()->shortcode()->vc_map_add_css_animation(),
        G5P()->shortcode()->vc_map_add_animation_duration(),
        G5P()->shortcode()->vc_map_add_animation_delay(),
        G5P()->shortcode()->vc_map_add_extra_class(),
        G5P()->shortcode()->vc_map_add_css_editor(),
        G5P()->shortcode()->vc_map_add_responsive()
	)
);