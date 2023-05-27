<?php
/**
 * The template for displaying config.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
return array(
	'base' => 'gsf_products_horizontal',
	'name' => esc_html__('Products Horizontal', 'spring-framework'),
	'category' => G5P()->shortcode()->get_category_name(),
	'icon' => 'fa fa-product-hunt',
	'params' => array(
        array(
            'param_name' => 'products_per_page',
            'heading' => esc_html__( 'Products Per Page', 'spring-framework' ),
            'description' => esc_html__( 'Enter number of products per page you want to display. Default 5', 'spring-framework' ),
            'type' => 'textfield',
            'std' => 5
        ),
        G5P()->shortCode()->vc_map_add_autoplay_enable(),
        G5P()->shortCode()->vc_map_add_autoplay_timeout(),
        array(
            'param_name' => 'product_animation',
            'heading' => esc_html__( 'Product Animation', 'spring-framework' ),
            'description' => esc_html__( 'Specify your product animation', 'spring-framework' ),
            'type' => 'dropdown',
            'value' => array_flip( G5P()->settings()->get_animation(true) ),
            'edit_field_class' => 'vc_col-sm-6 vc_column',
            'std' => ''
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
                esc_html__('Best Selling', 'spring-framework') => 'best-selling',
                esc_html__('Narrow Products', 'spring-framework') => 'products'
            )
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
            'dependency' => array('element' => 'show', 'value' => 'products')
        ),
        G5P()->shortCode()->vc_map_add_product_narrow_categories(array(
            'dependency' => array('element' => 'show','value_not_equal_to' => array('products'))
        )),

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
        ),
		G5P()->shortcode()->vc_map_add_css_animation(),
		G5P()->shortcode()->vc_map_add_animation_duration(),
		G5P()->shortcode()->vc_map_add_animation_delay(),
		G5P()->shortcode()->vc_map_add_extra_class(),
		G5P()->shortcode()->vc_map_add_css_editor(),
		G5P()->shortcode()->vc_map_add_responsive()
	)
);