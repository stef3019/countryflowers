<?php
/**
 * The template for displaying config.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
return array(
	'base' => 'gsf_portfolios',
	'name' => esc_html__( 'Portfolios', 'spring-framework' ),
	'category' => G5P()->shortcode()->get_category_name(),
	'icon' => 'fa fa-windows',
	'params' => array_merge(
		array(
			array(
				'param_name' => 'portfolio_layout',
				'heading' => esc_html__( 'Portfolio Layout', 'spring-framework' ),
				'description' => esc_html__( 'Specify your portfolio layout', 'spring-framework' ),
				'type' => 'gsf_image_set',
				'value' => array_merge(array(
                    'carousel' => array(
                        'label' => esc_html__('Slider', 'spring-framework'),
                        'img'   => G5P()->pluginUrl('assets/images/shortcode/carousel.png')
                    ),
                ), G5P()->settings()->get_portfolio_layout()),
				'std' => 'grid',
				'admin_label' => true
			),
            array(
                'param_name' => "portfolio_item_skin",
                'heading' => esc_html__('Portfolio Item Skin','spring-framework'),
                'type'     => 'gsf_image_set',
                'value'  => G5P()->settings()->get_portfolio_item_skin(),
                'std'  => 'portfolio-item-skin-02',
                'description'     => esc_html__('Skin 01 only apply for Slider Layout, Grid Layout and Masonry Layout', 'spring-framework')
            ),
            array(
                'param_name'       => 'portfolio_hover_color_scheme',
                'heading'    => esc_html__('Portfolio Hover Color Scheme', 'spring-framework'),
                'type'     => 'gsf_button_set',
                'value'  => array(
                    esc_html__('Accent', 'spring-framework') => 'portfolio-hover-accent',
                    esc_html__('Dark', 'spring-framework') => 'portfolio-hover-dark',
                    esc_html__('Light', 'spring-framework') => 'portfolio-hover-light',
                ),
                'std'  => 'portfolio-hover-light'
            ),
            array(
                'param_name'       => 'image_size',
                'heading'    => esc_html__('Image size', 'spring-framework'),
                'description' => esc_html__('Enter your portfolio image size', 'spring-framework'),
                'type'     => 'textfield',
                'std'  => 'medium',
                'edit_field_class' => 'vc_col-sm-6 vc_column',
                'dependency' => array('element' => 'portfolio_layout', 'value_not_equal_to' => array('masonry', 'scattered'))
            ),
            array(
                'param_name'       => 'image_ratio',
                'heading'    => esc_html__('Image ratio', 'spring-framework'),
                'description' => esc_html__('Specify your image portfolio ratio', 'spring-framework'),
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
                'param_name'       => 'image_masonry_width',
                'heading'    => esc_html__('Image masonry width', 'spring-framework'),
                'type'     => 'gsf_number',
                'std'      => '400',
                'dependency' => array('element' => 'portfolio_layout', 'value' => 'masonry')
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Show', 'spring-framework'),
                'param_name' => 'show',
                'value' => array(
                    esc_html__('All', 'spring-framework') => 'all',
                    esc_html__('Featured', 'spring-framework') => 'featured',
                    esc_html__('Narrow Portfolios', 'spring-framework') => 'portfolios'
                )
            ),
            array(
                'type' => 'autocomplete',
                'heading' => esc_html__( 'Narrow Portfolios', 'spring-framework' ),
                'param_name' => 'portfolio_ids',
                'settings' => array(
                    'multiple' => true,
                    'sortable' => true,
                    'unique_values' => true,
                ),
                'save_always' => true,
                'description' => esc_html__( 'Enter List of Portfolios', 'spring-framework' ),
                'dependency' => array('element' => 'show', 'value' => 'portfolios')
            ),
            G5P()->shortCode()->vc_map_add_portfolio_narrow_categories(array(
                'dependency' => array('element' => 'show', 'value_not_equal_to' => array('portfolios'))
            )),
			array(
				'param_name' => 'portfolios_per_page',
				'heading' => esc_html__( 'Portfolios Per Page', 'spring-framework' ),
				'description' => esc_html__( 'Enter number of portfolio per page you want to display. Default 10', 'spring-framework' ),
				'type' => 'gsf_number',
                'edit_field_class' => 'vc_col-sm-6 vc_column',
				'std' => 10
			),
            array(
                'param_name' => 'portfolio_columns_gutter',
                'heading' => esc_html__( 'Portfolio Columns Gutter', 'spring-framework' ),
                'description' => esc_html__( 'Specify your horizontal space between portfolio item.', 'spring-framework' ),
                'type' => 'dropdown',
                'edit_field_class' => 'vc_col-sm-6 vc_column',
                'value' => array(
                    esc_html__('None', 'spring-framework') => 'none',
                    '10px' => '10',
                    '20px' => '20',
                    '30px' => '30',
                    '40px' => '40',
                    '50px' => '50'
                ),
                'std' => '30',
                'dependency' => array( 'element' => 'portfolio_layout', 'value_not_equal_to' => array( 'carousel-3d', 'scattered') )
            ),
			array(
				'param_name' => 'show_cate_filter',
				'heading' => esc_html__( 'Category Filter', 'spring-framework' ),
				'type' => 'gsf_switch',
				'edit_field_class' => 'vc_col-sm-6 vc_column',
				'std' => ''
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
                'dependency' => array('element' => 'portfolio_layout','value' => array('carousel')),
                'group' => esc_html__('Slider Options','spring-framework'),
                'edit_field_class' => 'vc_col-sm-6 vc_column'
            ),
            G5P()->shortcode()->vc_map_add_pagination(array(
                'dependency' => array('element' => 'portfolio_layout','value' => array('carousel', 'carousel-3d')),
                'group' => esc_html__('Slider Options', 'spring-framework'),
                'edit_field_class' => 'vc_col-sm-6 vc_column'
            )),
            G5P()->shortcode()->vc_map_add_navigation(array(
                'dependency' => array('element' => 'portfolio_layout','value' => array('carousel', 'carousel-3d')),
                'group' => esc_html__('Slider Options', 'spring-framework'),
            )),
            G5P()->shortcode()->vc_map_add_navigation_position(array(
                'group' => esc_html__('Slider Options', 'spring-framework')
            )),
            G5P()->shortcode()->vc_map_add_navigation_style(array(
                'group' => esc_html__('Slider Options', 'spring-framework')
            )),
            G5P()->shortCode()->vc_map_add_autoplay_enable(array(
                'dependency' => array('element' => 'portfolio_layout','value' => array('carousel', 'carousel-3d')),
                'group' => esc_html__('Slider Options', 'spring-framework'),
            )),
            G5P()->shortCode()->vc_map_add_autoplay_timeout(array(
                'group' => esc_html__('Slider Options', 'spring-framework'),
            )),
			array(
				'param_name' => 'portfolio_paging',
				'heading' => esc_html__( 'Portfolio Paging', 'spring-framework' ),
				'description' => esc_html__( 'Specify your portfolio paging mode', 'spring-framework' ),
				'type' => 'dropdown',
				'value' => array(
					esc_html__('No Pagination', 'spring-framework')=>'none',
					esc_html__('Pagination', 'spring-framework') => 'pagination',
					esc_html__('Ajax - Pagination', 'spring-framework') => 'pagination-ajax',
					esc_html__('Ajax - Next Prev', 'spring-framework') => 'next-prev',
					esc_html__('Ajax - Load More', 'spring-framework') => 'load-more',
					esc_html__('Ajax - Infinite Scroll', 'spring-framework') => 'infinite-scroll'
				),
                'dependency' => array('element' => 'portfolio_layout','value_not_equal_to' => array('carousel', 'carousel-3d')),
				'edit_field_class' => 'vc_col-sm-6 vc_column',
				'std' => ''
			),
			array(
				'param_name' => 'portfolio_animation',
				'heading' => esc_html__( 'Animation', 'spring-framework' ),
				'description' => esc_html__( 'Specify your portfolio animation', 'spring-framework' ),
				'type' => 'dropdown',
				'value' => array_flip( G5P()->settings()->get_animation(true) ),
				'edit_field_class' => 'vc_col-sm-6 vc_column',
				'std' => '-1'
			),
            array(
                'param_name' => 'portfolio_hover_effect',
                'type'     => 'dropdown',
                'heading'    => esc_html__('Hover Effect', 'spring-framework'),
                'value'  => array_flip( G5P()->settings()->get_portfolio_hover_effect(true) ),
                'edit_field_class' => 'vc_col-sm-6 vc_column',
                'std'  => ''
            ),
            array(
                'param_name'       => 'portfolio_light_box',
                'type'     => 'dropdown',
                'heading'    => esc_html__('Light Box', 'spring-framework'),
                'value'  => array(
                    esc_html__('Inherit', 'spring-framework') => '',
                    esc_html__('Feature Image', 'spring-framework') => 'feature',
                    esc_html__('Media Gallery', 'spring-framework') => 'media'
                ),
                'edit_field_class' => 'vc_col-sm-6 vc_column',
                'std'  => ''
            ),
            array(
                'type'       => 'dropdown',
                'heading'    => esc_html__('Order By', 'spring-framework'),
                'param_name' => 'order_by',
                'value'      => array(
                    esc_html__('Date', 'spring-framework') => 'date',
                    esc_html__('Portfolio Id', 'spring-framework') => 'ID',
                    esc_html__('Portfolio Title', 'spring-framework') => 'title'
                ),
                'default' => 'date',
                'dependency' => array('element' => 'show','value' => array('all')),
                'edit_field_class' => 'vc_col-sm-6 vc_column'
            ),
            array(
                'type'       => 'dropdown',
                'heading'    => esc_html__('Order', 'spring-framework'),
                'param_name' => 'order',
                'value'      => array(
                    esc_html__('Ascending', 'spring-framework') => 'ASC',
                    esc_html__('Descending', 'spring-framework') => 'DESC'),
                'dependency' => array('element' => 'show','spring' => array('all')),
                'default' => 'ASC',
                'edit_field_class' => 'vc_col-sm-6 vc_column'
            )
        ),
        G5P()->shortCode()->get_column_responsive(array(
            'element'=>'portfolio_layout',
            'value'=>array('grid', 'masonry', 'carousel')
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