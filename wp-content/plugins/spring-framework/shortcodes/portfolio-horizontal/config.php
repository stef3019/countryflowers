<?php
/**
 * The template for displaying config.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
return array(
	'base' => 'gsf_portfolio_horizontal',
	'name' => esc_html__( 'Portfolio Horizontal', 'spring-framework' ),
	'category' => G5P()->shortcode()->get_category_name(),
	'icon' => 'fa fa-file-text',
	'params' => array(
        array(
            'param_name' => 'portfolios_per_page',
            'heading' => esc_html__( 'Portfolios Per Page', 'spring-framework' ),
            'description' => esc_html__( 'Enter number of portfolios per page you want to display. Default 5', 'spring-framework' ),
            'type' => 'textfield',
            'std' => 5
        ),
        G5P()->shortCode()->vc_map_add_autoplay_enable(),
        G5P()->shortCode()->vc_map_add_autoplay_timeout(),
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
        G5P()->shortcode()->vc_map_add_css_animation(),
        G5P()->shortcode()->vc_map_add_animation_duration(),
        G5P()->shortcode()->vc_map_add_animation_delay(),
        G5P()->shortcode()->vc_map_add_extra_class(),
        G5P()->shortcode()->vc_map_add_css_editor(),
        G5P()->shortcode()->vc_map_add_responsive()
	)
);