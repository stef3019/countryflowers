<?php
/**
 * The template for displaying config.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
return array(
	'base' => 'gsf_product_singular',
	'name' => esc_html__('Product Singular', 'spring-framework'),
	'category' => G5P()->shortcode()->get_category_name(),
	'icon' => 'fa fa-product-hunt',
	'params' => array(
		array(
			'type' => 'autocomplete',
			'heading' => __( 'Choose product to show', 'spring-framework' ),
			'param_name' => 'product_ids',
            'settings' => array(
                'multiple' => false,
                'unique_values' => true,
                'display_inline' => true
            ),
            'save_always' => true,
		),
        array(
            'type' => 'attach_images',
            'heading' => esc_html__('Custom Images', 'spring-framework'),
            'description' => esc_html__('Choose gallery to show (set empty to use above product image)', 'spring-framework'),
            'param_name' => 'images'
        ),
        array(
            'type' => 'textfield',
            'heading' => esc_html__('Image Size', 'spring-framework'),
            'description' => esc_html__('Enter image size ("thumbnail" or "full"). Alternatively enter size in pixels (Example: 280x180, 330x180, 380x180 (Not Include Unit, Space)).', 'spring-framework'),
            'param_name' => 'image_size',
            'std' => '516x572',
            'edit_field_class' => 'vc_col-sm-6 vc_column',
            'dependency' => array('element' => 'images', 'value_not_equal_to' => array(''))
        ),
        array(
            'type' => 'gsf_button_set',
            'heading' => __( 'Product Info Size', 'spring-framework' ),
            'param_name' => 'name_size',
            'value' => array(
                esc_html__('Larrge', 'spring-framework') => 'large',
                esc_html__('Medium', 'spring-framework') => 'medium'
            ),
            'std' => 'medium'
        ),
		array(
			'type' => 'textfield',
            'heading' => __( 'Additional Information', 'spring-framework' ),
			'param_name' => 'additional_info'
		),
		G5P()->shortcode()->vc_map_add_css_animation(),
		G5P()->shortcode()->vc_map_add_animation_duration(),
		G5P()->shortcode()->vc_map_add_animation_delay(),
		G5P()->shortcode()->vc_map_add_extra_class(),
		G5P()->shortcode()->vc_map_add_css_editor(),
		G5P()->shortcode()->vc_map_add_responsive()
	)
);