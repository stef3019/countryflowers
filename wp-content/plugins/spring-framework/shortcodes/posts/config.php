<?php
/**
 * The template for displaying config.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
return array(
	'base' => 'gsf_posts',
	'name' => esc_html__( 'Posts', 'spring-framework' ),
	'category' => G5P()->shortcode()->get_category_name(),
	'icon' => 'fa fa-file-text',
	'params' => array_merge(
		array(
			array(
				'param_name' => 'post_layout',
				'heading' => esc_html__( 'Post Layout', 'spring-framework' ),
				'description' => esc_html__( 'Specify your post layout', 'spring-framework' ),
				'type' => 'gsf_image_set',
				'value' => array(
					'large-image'    => array(
						'label' => esc_html__('Large Image', 'spring-framework'),
						'img'   => G5P()->pluginUrl('assets/images/theme-options/blog-large-image.png'),
					),
					'medium-image'   => array(
						'label' => esc_html__('Medium Image', 'spring-framework'),
						'img'   => G5P()->pluginUrl('assets/images/theme-options/blog-medium-image.png'),
					),
					'grid'         => array(
						'label' => esc_html__('Grid', 'spring-framework'),
						'img'   => G5P()->pluginUrl('assets/images/theme-options/blog-grid.png'),
					),
					'masonry'        => array(
						'label' => esc_html__('Masonry', 'spring-framework'),
						'img'   => G5P()->pluginUrl('assets/images/theme-options/blog-masonry.png'),
					),
					'zigzac'        => array(
						'label' => esc_html__('Zigzag', 'spring-framework'),
						'img'   => G5P()->pluginUrl('assets/images/theme-options/blog-zigzac.png'),
					),
					'list'        => array(
						'label' => esc_html__('Medium Image 2', 'spring-framework'),
						'img'   => G5P()->pluginUrl('assets/images/theme-options/blog-list.png'),
					),
					'list-no-img'        => array(
						'label' => esc_html__('List No Image', 'spring-framework'),
						'img'   => G5P()->pluginUrl('assets/images/theme-options/blog-list-no-img.png'),
					)
				),
				'std' => 'large-image',
				'admin_label' => true
			),
			array(
				'param_name' => "post_item_skin",
				'heading' => esc_html__('Post Item Skin','spring-framework'),
				'type'     => 'gsf_image_set',
				'value'  => G5P()->settings()->get_post_item_skin(),
				'std'  => 'post-skin-01',
				'dependency' => array('element' => 'post_layout', 'value' => array('grid', 'masonry')),
			),
			array(
				'param_name'       => 'image_size',
				'heading'    => esc_html__('Image size', 'spring-framework'),
				'description' => esc_html__('Enter your product image size', 'spring-framework'),
				'type'     => 'textfield',
				'std'  => '370x230',
				'edit_field_class' => 'vc_col-sm-6 vc_column',
                'dependency' => array('element' => 'post_layout', 'value_not_equal_to' => array('list-no-img'))
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
		),
		G5P()->shortcode()->get_post_filter(),
		array(
			array(
				'param_name' => 'posts_per_page',
				'heading' => esc_html__( 'Posts Per Page', 'spring-framework' ),
				'description' => esc_html__( 'Enter number of posts per page you want to display. Default 10', 'spring-framework' ),
				'type' => 'textfield',
				'std' => 10
			),
			array(
				'param_name' => 'show_cate_filter',
				'heading' => esc_html__( 'Category Filter', 'spring-framework' ),
				'type' => 'gsf_switch',
				'edit_field_class' => 'vc_col-sm-6 vc_column',
				'std' => '0'
			),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Category Filter Alignment', 'spring-framework'),
                'param_name' => 'cate_filter_align',
                'value' => array(
                    esc_html__('Left', 'spring-framework') => 'cate-filter-left',
                    esc_html__('Center', 'spring-framework') => 'cate-filter-center',
                    esc_html__('Right', 'spring-framework') => 'cate-filter-right'
                ),
                'std' => 'cate-filter-left',
                'dependency' => array('element'=>'show_cate_filter', 'value'=> 'on'),
                'edit_field_class' => 'vc_col-sm-6 vc_column'
            ),
		),
		array(
			array(
				'param_name' => 'post_columns_gutter',
				'heading' => esc_html__( 'Post Columns Gutter', 'spring-framework' ),
				'description' => esc_html__( 'Specify your horizontal space between post.', 'spring-framework' ),
				'type' => 'dropdown',
				'value' => array_flip( G5P()->settings()->get_post_columns_gutter() ),
				'std' => '30',
				'dependency' => array( 'element' => 'post_layout', 'value' => array( 'grid', 'masonry', 'mix-1', 'grid-2', 'tall-1', 'tall-2', 'tall-3', 'medium-image-2' ) )
			),

            array(
                'type' => 'gsf_switch',
                'heading' => esc_html__('Is Slider?', 'spring-framework' ),
                'param_name' => 'is_slider',
                'std' => '',
                'admin_label' => true,
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
                'dependency' => array('element' => 'is_slider','value' => 'on'),
                'group' => esc_html__('Slider Options','spring-framework'),
                'edit_field_class' => 'vc_col-sm-6 vc_column'
            ),
            G5P()->shortcode()->vc_map_add_pagination(array(
                'dependency' => array('element' => 'is_slider', 'value' => 'on'),
                'group' => esc_html__('Slider Options', 'spring-framework'),
                'edit_field_class' => 'vc_col-sm-6 vc_column'
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
				'param_name' => 'post_paging',
				'heading' => esc_html__( 'Post Paging', 'spring-framework' ),
				'description' => esc_html__( 'Specify your post paging mode', 'spring-framework' ),
				'type' => 'dropdown',
				'value' => array(
					esc_html__('No Pagination', 'spring-framework')=>'none',
					esc_html__('Pagination', 'spring-framework') => 'pagination',
					esc_html__('Ajax - Pagination', 'spring-framework') => 'pagination-ajax',
					esc_html__('Ajax - Next Prev', 'spring-framework') => 'next-prev',
					esc_html__('Ajax - Load More', 'spring-framework') => 'load-more',
					esc_html__('Ajax - Infinite Scroll', 'spring-framework') => 'infinite-scroll'
				),
                'dependency' => array('element' => 'is_slider','value_not_equal_to' => array('on')),
				'edit_field_class' => 'vc_col-sm-6 vc_column',
				'std' => ''
			),
			array(
				'param_name' => 'post_animation',
				'heading' => esc_html__( 'Animation', 'spring-framework' ),
				'description' => esc_html__( 'Specify your post animation', 'spring-framework' ),
				'type' => 'dropdown',
				'value' => array_flip( G5P()->settings()->get_animation(true) ),
				'edit_field_class' => 'vc_col-sm-6 vc_column',
				'std' => '-1'
			),
        ),
        G5P()->shortCode()->get_column_responsive(array(
            'element'=>'post_layout',
            'value'=>array('grid', 'masonry')
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