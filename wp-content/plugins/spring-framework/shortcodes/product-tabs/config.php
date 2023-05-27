<?php
return array(
	'base' => 'gsf_product_tabs',
	'name' => esc_html__('Product Tabs','spring-framework'),
	'icon' => 'fa fa-product-hunt',
    'category' => G5P()->shortcode()->get_category_name(),
	'params' =>  array_merge(
	    array(
            array(
                'type' => 'gsf_image_set',
                'heading' => esc_html__('Layout Style', 'spring-framework'),
                'param_name' => 'layout_style',
                'admin_label' => true,
                'std' => 'grid',
                'value' => G5P()->settings()->get_product_catalog_layout()
            ),
			array(
				'type' => 'gsf_image_set',
				'param_name' => 'product_item_skin',
				'heading' => esc_html__('Skin','spring-framework'),
				'std' => 'product-skin-01',
				'value' => apply_filters('gsf_products_item_skin', array(
					'product-skin-01' => array(
						'label' => esc_html__('Skin 01', 'spring-framework'),
						'img'   => G5P()->pluginUrl('assets/images/theme-options/product-skin-01.png')
					),
					'product-skin-02' => array(
						'label' => esc_html__('Skin 02', 'spring-framework'),
						'img'   => G5P()->pluginUrl('assets/images/theme-options/product-skin-02.png')
					),
					'product-skin-03' => array(
						'label' => esc_html__('Skin 03', 'spring-framework'),
						'img'   => G5P()->pluginUrl('assets/images/theme-options/product-skin-03.png')
					),
					'product-skin-04' => array(
						'label' => esc_html__('Skin 04', 'spring-framework'),
						'img'   => G5P()->pluginUrl('assets/images/theme-options/product-skin-04.png')
					),
					'product-skin-05' => array(
						'label' => esc_html__('Skin 05', 'spring-framework'),
						'img'   => G5P()->pluginUrl('assets/images/theme-options/product-skin-05.png')
					),
					'product-skin-06' => array(
						'label' => esc_html__('Skin 06', 'spring-framework'),
						'img'   => G5P()->pluginUrl('assets/images/theme-options/product-skin-06.png')
					),
                    'product-skin-07' => array(
                        'label' => esc_html__('Skin 07', 'spring-framework'),
                        'img'   => G5P()->pluginUrl('assets/images/theme-options/product-skin-07.png')
                    )
                )),
				'dependency' => array('element' => 'layout_style','value' => 'grid')
			),
            array(
                'type'       => 'param_group',
                'heading'    => esc_html__('Tab Info', 'spring-framework'),
                'param_name' => 'product_tabs',
                'params'     => array(
                    array(
                        'type' => 'textfield',
                        'heading' => esc_html__('Tab Title', 'spring-framework' ),
                        'param_name' => 'tab_title',
                        'admin_label' => true
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Show', 'spring-framework'),
                        'param_name' => 'show',
                        'value' => array(
                            esc_html__('All', 'spring-framework') => 'all',
                            esc_html__('Sale Off', 'spring-framework') => 'sale',
                            esc_html__('New In', 'spring-framework') => 'new-in',
                            esc_html__('Featured', 'spring-framework') => 'featured',
                            esc_html__('Top rated', 'spring-framework') => 'top-rated',
                            esc_html__('Recent review', 'spring-framework') => 'recent-review',
                            esc_html__('Best Selling', 'spring-framework') => 'best-selling'
                        )
                    ),
                    G5P()->shortCode()->vc_map_add_product_narrow_categories(),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Order by', 'spring-framework'),
                        'param_name' => 'orderby',
                        'value' => array(
                            esc_html__('Date', 'spring-framework') => 'date',
                            esc_html__('Price', 'spring-framework') => 'price',
                            esc_html__('Random', 'spring-framework') => 'rand',
                            esc_html__('Sales', 'spring-framework') => 'sales'
                        ),
                        'description' => esc_html__('Select how to sort retrieved products.', 'spring-framework'),
                        'dependency' => array('element' => 'show','value' => array('all', 'sale', 'featured')),
                        'edit_field_class' => 'vc_col-sm-6 vc_column'
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Sort order', 'spring-framework'),
                        'param_name' => 'order',
                        'value' => array(
                            esc_html__('Descending', 'spring-framework') => 'DESC',
                            esc_html__('Ascending', 'spring-framework') => 'ASC'
                        ),
                        'description' => esc_html__('Designates the ascending or descending order.', 'spring-framework'),
                        'dependency' => array('element' => 'show','value' => array('all', 'sale', 'featured')),
                        'edit_field_class' => 'vc_col-sm-6 vc_column'
                    )
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Tabs Alignment', 'spring-framework'),
                'param_name' => 'tabs_align',
                'value' => array(
                    esc_html__('Left', 'spring-framework') => 'tabs-left',
                    esc_html__('Center', 'spring-framework') => 'tabs-center',
                    esc_html__('Right', 'spring-framework') => 'tabs-right'
                ),
                'std' => 'tabs-left',
                'edit_field_class' => 'vc_col-sm-6 vc_column'
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__('Products Per Page', 'spring-framework' ),
                'param_name' => 'products_per_page',
                'value' => 4,
                'edit_field_class' => 'vc_col-sm-6 vc_column'
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Columns Gutter', 'spring-framework'),
                'param_name' => 'columns_gutter',
                'value' => array_flip( G5P()->settings()->get_post_columns_gutter() ),
                'std' => '30',
                'dependency' => array('element' => 'layout_style','value_not_equal_to' => array('list')),
                'edit_field_class' => 'vc_col-sm-6 vc_column'
            ),
            array(
                'type' => 'gsf_switch',
                'heading' => esc_html__('Is Slider?', 'spring-framework' ),
                'param_name' => 'is_slider',
                'std' => '',
                'admin_label' => true,
                'dependency' => array('element' => 'layout_style', 'value' => array('grid', 'list')),
                'edit_field_class' => 'vc_col-sm-6 vc_column'
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Carousel Rows', 'spring-framework'),
                'param_name' => 'rows',
                'value' => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4'
                ),
                'dependency' => array('element' => 'is_slider', 'value' => 'on'),
                'group' => esc_html__('Slider Options','spring-framework'),
                'edit_field_class' => 'vc_col-sm-6 vc_column'
            ),
            G5P()->shortcode()->vc_map_add_pagination(array(
                'dependency' => array('element' => 'is_slider', 'value' => 'on'),
                'group' => esc_html__('Slider Options', 'spring-framework'),
                'edit_field_class' => 'vc_col-sm-6 vc_column'
            )),
			G5P()->shortcode()->vc_map_add_pagination_style(array(
				'group' => esc_html__('Slider Options', 'spring-framework'),
			)),
            G5P()->shortcode()->vc_map_add_navigation(array(
                'dependency' => array('element' => 'is_slider', 'value' => 'on'),
                'group' => esc_html__('Slider Options', 'spring-framework'),
            )),
            G5P()->shortcode()->vc_map_add_navigation_position(array(
                'group' => esc_html__('Slider Options', 'spring-framework')
            )),
            G5P()->shortcode()->vc_map_add_navigation_style(array(
                'group' => esc_html__('Slider Options', 'spring-framework')
            )),
            G5P()->shortCode()->vc_map_add_autoplay_enable(array(
                'dependency' => array('element' => 'is_slider', 'value' => 'on'),
                'group' => esc_html__('Slider Options', 'spring-framework'),
            )),
            G5P()->shortCode()->vc_map_add_autoplay_timeout(array(
                'group' => esc_html__('Slider Options', 'spring-framework'),
            )),
            array(
                'param_name' => 'product_paging',
                'heading' => esc_html__( 'Product Paging', 'spring-framework' ),
                'description' => esc_html__( 'Specify your post paging mode', 'spring-framework' ),
                'type' => 'dropdown',
                'value' => array(
                    esc_html__('No Pagination', 'spring-framework')=> 'none',
                    esc_html__('Pagination', 'spring-framework') => 'pagination',
                    esc_html__('Ajax - Pagination', 'spring-framework') => 'pagination-ajax',
                    esc_html__('Ajax - Next Prev', 'spring-framework') => 'next-prev',
                    esc_html__('Ajax - Load More', 'spring-framework') => 'load-more',
                    esc_html__('Ajax - Infinite Scroll', 'spring-framework') => 'infinite-scroll'
                ),
                'edit_field_class' => 'vc_col-sm-6 vc_column',
                'dependency' => array('element' => 'is_slider', 'value_not_equal_to' => array('on')),
                'std' => 'none'
            ),
            array(
                'param_name' => 'product_animation',
                'heading' => esc_html__( 'Product Animation', 'spring-framework' ),
                'description' => esc_html__( 'Specify your product animation', 'spring-framework' ),
                'type' => 'dropdown',
                'value' => array_flip( G5P()->settings()->get_animation(true) ),
                'edit_field_class' => 'vc_col-sm-6 vc_column',
                'std' => ''
            )
        ),
        G5P()->shortCode()->get_column_responsive(array(
            'element'=>'layout_style',
            'value'=>array('grid')
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