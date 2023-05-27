<?php
return array(
	'base' => 'gsf_shop_category',
	'name' => esc_html__( 'Shop Category', 'spring-framework' ),
	'icon' => 'fa fa-image',
	'category' => G5P()->shortcode()->get_category_name(),
	'params' => array(
        array(
            'type' => 'gsf_image_set',
            'heading' => esc_html__('Layout Style', 'spring-framework'),
            'param_name' => 'layout_style',
            'value' => array(
                'style-01' => array(
                    'label' => esc_html__('Style 01', 'spring-framework'),
                    'img' => G5P()->pluginUrl('assets/images/shortcode/shop-category-01.jpg'),
                ),
                'style-02' => array(
                    'label' => esc_html__('Style 02', 'spring-framework'),
                    'img' => G5P()->pluginUrl('assets/images/shortcode/shop-category-02.jpg'),
                ),
				'style-03' => array(
					'label' => esc_html__('Style 03', 'spring-framework'),
					'img' => G5P()->pluginUrl('assets/images/shortcode/shop-category-03.png'),
				)
            ),
            'std' => 'style-01',
            'admin_label' => true,
        ),
        G5P()->shortcode()->vc_map_add_product_category(),
		array(
			'type' => 'attach_image',
			'heading' => esc_html__('Background Image', 'spring-framework'),
			'edit_field_class' => 'vc_col-sm-6 vc_column',
			'param_name' => 'image'
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Height Mode', 'spring-framework' ),
			'param_name' => 'height_mode',
			'value' => array(
				'1:1' => '100',
				esc_html__( 'Original', 'spring-framework' )=> 'original',
				'4:3' => '133.333333333',
				'3:4' => '75',
				'16:9' => '177.777777778',
				'9:16' => '56.25',
				esc_html__( 'Custom', 'spring-framework' )=> 'custom'
			),
			'std' => 'original',
			'dependency' => array('element' => 'banner_bg_image', 'value_not_equal_to' => array('')),
			'description' => esc_html__( 'Sizing proportions for height and width. Select "Original" to scale image without cropping.', 'spring-framework' )
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Height', 'spring-framework' ),
			'param_name' => 'height',
			'std' => '340px',
			'dependency' => array('element' => 'height_mode', 'value' => 'custom'),
			'description' => esc_html__( 'Enter custom height (include unit)', 'spring-framework' )
		),
		G5P()->shortcode()->vc_map_add_css_animation(),
		G5P()->shortcode()->vc_map_add_animation_duration(),
		G5P()->shortcode()->vc_map_add_animation_delay(),
		G5P()->shortcode()->vc_map_add_extra_class(),
		G5P()->shortcode()->vc_map_add_css_editor(),
		G5P()->shortcode()->vc_map_add_responsive()
	),
);