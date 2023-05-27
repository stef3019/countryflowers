<?php
return array(
    'base'        => 'gsf_testimonials',
    'name'        => esc_html__('Testimonials', 'spring-framework'),
    'icon'        => 'fa fa-quote-right',
    'category'    => G5P()->shortcode()->get_category_name(),
    'params'      => array_merge(
        array(
            array(
                'type'        => 'gsf_image_set',
                'heading'     => esc_html__('Testimonials Layout', 'spring-framework'),
                'description' => esc_html__('Select our testimonial layout.', 'spring-framework'),
                'param_name'  => 'layout_style',
                'value'       => apply_filters('gsf_testimonials_layout_style',array(
                    'style-01' => array(
                        'label' => esc_html__('Style 01', 'spring-framework'),
                        'img' => G5P()->pluginUrl('assets/images/shortcode/testimonials-01.png'),
                    ),
                    'style-02' => array(
                        'label' => esc_html__('Style 02', 'spring-framework'),
                        'img' => G5P()->pluginUrl('assets/images/shortcode/testimonials-02.png'),
                    ),
					'style-03' => array(
						'label' => esc_html__('Style 03', 'spring-framework'),
						'img' => G5P()->pluginUrl('assets/images/shortcode/testimonials-03.png'),
					),
					'style-04' => array(
						'label' => esc_html__('Style 04', 'spring-framework'),
						'img' => G5P()->pluginUrl('assets/images/shortcode/testimonials-05.png'),
					)
                )),
                'std' => 'style-02',
                'admin_label' => true,
            ),
            array(
                'type'             => 'dropdown',
                'heading'          => esc_html__('Columns Gutter', 'spring-framework'),
                'param_name'       => 'columns_gutter',
                'value' => array_flip( G5P()->settings()->get_post_columns_gutter() ),
                'std' => '30',
                'edit_field_class' => 'vc_col-sm-6 vc_column',
            ),
			array(
				'type'             => 'dropdown',
				'heading'          => esc_html__('Avatar Active', 'spring-framework'),
				'param_name'       => 'item_active',
				'value'            => array('1' => 1, '3' => 3),
				'std'              => 3,
				'dependency'  => array(
					'element'            => 'layout_style',
					'value' => array('style-03')
				)
			),
            array(
                'type'        => 'param_group',
                'heading'     => esc_html__('Values', 'spring-framework'),
                'param_name'  => 'values',
                'description' => esc_html__('Enter values for author. Enter at least 3 authors', 'spring-framework'),
                'value'       => '',
                'params'      => array(
                    array(
                        'type'        => 'textfield',
                        'heading'     => esc_html__('Author Name', 'spring-framework'),
                        'param_name'  => 'author_name',
                        'admin_label' => true,
                    ),
                    array(
                        'type'        => 'textfield',
                        'heading'     => esc_html__('Author Job', 'spring-framework'),
                        'param_name'  => 'author_job',
                        'admin_label' => true,
                    ),
                    array(
                        'type'       => 'textarea_raw_html',
                        'heading'    => esc_html__('Content testimonials of the author', 'spring-framework'),
                        'param_name' => 'author_bio'
                    ),
                    array(
                        'type'        => 'attach_image',
                        'heading'     => esc_html__('Upload Avatar:', 'spring-framework'),
                        'param_name'  => 'author_avatar',
                        'value'       => '',
                        'dependency' => array('element' => 'layout_style', 'value' => array('style-01', 'style-02')),
                        'description' => esc_html__('Upload avatar for author.', 'spring-framework'),
                    ),
                    array(
                        'type'       => 'textfield',
                        'heading'    => esc_html__('Author Link', 'spring-framework'),
                        'param_name' => 'author_link'
                    ),
                )
            ),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__('Author Name Color', 'spring-framework'),
				'param_name' => 'author_name_color',
				'std' => '#fff',
				'edit_field_class' => 'vc_col-sm-6 vc_column'
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__('Author Job Color', 'spring-framework'),
				'param_name' => 'author_job_color',
				'std' => '#d7d7d7',
				'edit_field_class' => 'vc_col-sm-6 vc_column'
			),
            G5P()->shortcode()->vc_map_add_pagination(array(
                'edit_field_class' => 'vc_col-sm-6 vc_column'
            )),
            G5P()->shortcode()->vc_map_add_pagination_style(),
            G5P()->shortcode()->vc_map_add_navigation(array(
                'edit_field_class' => 'vc_col-sm-6 vc_column'
            )),
            G5P()->shortcode()->vc_map_add_navigation_position(array(
                'edit_field_class' => 'vc_col-sm-6 vc_column',
				'dependency' => array('element' => 'layout_style', 'value_not_equal_to' => array('style-03'))
            )),
            G5P()->shortcode()->vc_map_add_navigation_style(),
            G5P()->shortCode()->vc_map_add_autoplay_enable(),
            G5P()->shortCode()->vc_map_add_autoplay_timeout(),
        ),
        G5P()->shortCode()->get_column_responsive(array(
            'element'=>'layout_style',
            'value'=>array('style-04')
        )),
        array(
            G5P()->shortcode()->vc_map_add_css_animation(),
            G5P()->shortcode()->vc_map_add_animation_duration(),
            G5P()->shortcode()->vc_map_add_animation_delay(),
            G5P()->shortcode()->vc_map_add_extra_class(),
            G5P()->shortcode()->vc_map_add_css_editor(),
            G5P()->shortcode()->vc_map_add_responsive()
        )
    )
);