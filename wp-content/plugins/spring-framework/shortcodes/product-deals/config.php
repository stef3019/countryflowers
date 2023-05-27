<?php
return array(
	'base' => 'gsf_product_deals',
	'name' => esc_html__( 'Product Deals', 'spring-framework' ),
	'icon' => 'fa fa-list-ul',
	'category' => G5P()->shortcode()->get_category_name(),
	'params' => array(
        G5P()->shortcode()->vc_map_add_title(array(
            'admin_label' => true
        )),
		array(
			'type' => 'gsf_image_set',
			'heading' => esc_html__('Layout Style', 'spring-framework'),
			'param_name' => 'layout_style',
			'std' => 'style-01',
			'value' => array(
				'style-01' => array(
					'label' => esc_html__('Style 01', 'spring-framework'),
					'img' => G5P()->pluginUrl('assets/images/shortcode/product-deals-skin-01.png')
				),
				'style-02' => array(
					'label' => esc_html__('Style 02', 'spring-framework'),
					'img' => G5P()->pluginUrl('assets/images/shortcode/product-deals-skin-02.png')
				),
				'style-03' => array(
					'label' => esc_html__('Style 03', 'spring-framework'),
					'img' => G5P()->pluginUrl('assets/images/shortcode/product-deals-skin-03.png')
				)
			)
		),
		array(
			'param_name'       => 'image_size',
			'heading'    => esc_html__('Image size', 'spring-framework'),
			'description' => esc_html__('Enter your product image size', 'spring-framework'),
			'type'     => 'textfield',
			'std'  => '485x640',
			'edit_field_class' => 'vc_col-sm-6 vc_column'
		),
		array(
			'param_name'       => 'image_ratio',
			'heading'    => esc_html__('Image ratio', 'spring-framework'),
			'description' => esc_html__('Specify your image product ratio', 'spring-framework'),
			'type'     => 'dropdown',
			'value'  => array_flip(G5P()->settings()->get_image_ratio()),
			'std'  => '1x1',
			'edit_field_class' => 'vc_col-sm-6 vc_column',
			'dependency' => array('element' => 'image_size', 'value' => 'full')
		),
        array(
            'param_name'       => 'image_ratio_custom_width',
            'heading'    => esc_html__('Image ratio custom width', 'spring-framework'),
            'description' => esc_html__('Enter custom width for image ratio', 'spring-framework'),
            'type'     => 'gsf_number',
            'edit_field_class' => 'vc_col-sm-6 vc_column',
            'dependency' => array('element' => 'image_ratio', 'value' => 'custom')
        ),
        array(
            'param_name'       => 'image_ratio_custom_height',
            'heading'    => esc_html__('Image ratio custom height', 'spring-framework'),
            'description' => esc_html__('Enter custom height for image ratio', 'spring-framework'),
            'type'     => 'gsf_number',
            'edit_field_class' => 'vc_col-sm-6 vc_column',
            'dependency' => array('element' => 'image_ratio', 'value' => 'custom')
        ),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__('Show', 'spring-framework'),
			'param_name' => 'show',
			"admin_label" => true,
			'value' => array(
				esc_html__('Sale Off', 'spring-framework') => 'sale',
				esc_html__('Narrow Products', 'spring-framework') => 'products'
			)
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__('Number of products', 'spring-framework' ),
			'description' => esc_html__('Enter number of products to display.', 'spring-framework' ),
			'param_name' => 'number',
			'value' => 4,
			'admin_label' => true,
			'edit_field_class' => 'vc_col-sm-6 vc_column'
		),
		array(
			'type' => 'autocomplete',
			'heading' => esc_html__( 'Narrow Products', 'spring-framework' ),
			'param_name' => 'product_ids',
			'settings' => array(
				'multiple' => true,
				'sortable' => true,
				'unique_values' => true,
			),
			'save_always' => true,
			'description' => esc_html__( 'Enter List of Products', 'spring-framework' ),
			'dependency' => array('element' => 'show','value' => 'products'),
		),
		G5P()->shortcode()->vc_map_add_pagination(array(
			'group' => esc_html__('Slider Options', 'spring-framework'),
			'edit_field_class' => 'vc_col-sm-6 vc_column'
		)),
		G5P()->shortcode()->vc_map_add_pagination_style(array(
			'group' => esc_html__('Slider Options', 'spring-framework'),
		)),
		G5P()->shortcode()->vc_map_add_navigation(array(
			'group' => esc_html__('Slider Options', 'spring-framework'),
		)),
		G5P()->shortcode()->vc_map_add_navigation_position(array(
			'group' => esc_html__('Slider Options', 'spring-framework')
		)),
		G5P()->shortcode()->vc_map_add_navigation_style(array(
			'group' => esc_html__('Slider Options', 'spring-framework')
		)),
		G5P()->shortCode()->vc_map_add_autoplay_enable(array(
			'group' => esc_html__('Slider Options', 'spring-framework'),
		)),
        G5P()->shortCode()->vc_map_add_autoplay_timeout(array(
            'group' => esc_html__('Slider Options', 'spring-framework'),
        )),
		G5P()->shortCode()->vc_map_add_product_narrow_categories(array(
            'dependency' => array('element' => 'show', 'value' => 'sale')
        )),
		G5P()->shortcode()->vc_map_add_css_animation(),
		G5P()->shortcode()->vc_map_add_animation_duration(),
		G5P()->shortcode()->vc_map_add_animation_delay(),
		G5P()->shortcode()->vc_map_add_extra_class(),
		G5P()->shortcode()->vc_map_add_css_editor(),
		G5P()->shortcode()->vc_map_add_responsive()
	),
);