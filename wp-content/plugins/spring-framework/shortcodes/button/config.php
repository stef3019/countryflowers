<?php
/**
 * The template for displaying config.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
return array(
	'base' => 'gsf_button',
	'name' => esc_html__('Button', 'spring-framework'),
	'category' => G5P()->shortcode()->get_category_name(),
	'description' => esc_html__('Eye catching button', 'spring-framework'),
	'icon'        => 'fa fa-bold',
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__('Text', 'spring-framework'),
			'param_name' => 'title',
			'value' => esc_html__('Text on the button', 'spring-framework'),
			'admin_label' => true,
		),
		array(
			'type' => 'vc_link',
			'heading' => esc_html__('URL (Link)', 'spring-framework'),
			'param_name' => 'link',
			'description' => esc_html__('Add link to button.', 'spring-framework'),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__('Style', 'spring-framework'),
			'description' => esc_html__('Select button display style.', 'spring-framework'),
			'param_name' => 'style',
			'value' => array(
				esc_html__('Classic', 'spring-framework') => 'classic',
				esc_html__('Outline', 'spring-framework') => 'outline',
				esc_html__('3D', 'spring-framework') => '3d',
                esc_html__('Link', 'spring-framework') => 'link'
			),
			'std' => 'classic',
			'admin_label' => true,
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__('Shape', 'spring-framework'),
			'description' => esc_html__('Select button shape.', 'spring-framework'),
			'param_name' => 'shape',
			'value' => array(
				esc_html__('Rounded', 'spring-framework') => 'rounded',
				esc_html__('Square', 'spring-framework') => 'square',
				esc_html__('Round', 'spring-framework') => 'round',
			),
            'dependency' => array(
                'element' => 'style',
                'value_not_equal_to' => array('link'),
            ),
			'std' => 'rounded',
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__('Color', 'spring-framework'),
			'param_name' => 'color',
			'description' => esc_html__('Select button color.', 'spring-framework'),
			'value' => array(
				esc_html__('Accent', 'spring-framework') => 'accent',
				esc_html__('Gray', 'spring-framework') => 'gray',
				esc_html__('Black', 'spring-framework') => 'black',
				esc_html__('White', 'spring-framework') => 'white',
				esc_html__('Red', 'spring-framework') => 'red',
			),
			'std' => 'accent',
			'admin_label' => true,
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__('Size', 'spring-framework'),
			'param_name' => 'size',
			'description' => esc_html__('Select button display size.', 'spring-framework'),
			'std' => 'md',
			'value' => array(
				esc_html__('Mini', 'spring-framework') => 'xs', // height 35px
				esc_html__('Small', 'spring-framework') => 'sm', // height 40px
				esc_html__('Normal', 'spring-framework') => 'md', // height 45px
				esc_html__('Large', 'spring-framework') => 'lg', // height 54px
				esc_html__('Extra Large', 'spring-framework') => 'xl', // height 56px
				esc_html__('Extra Extra Large', 'spring-framework') => 'xxl', // height 60px
			),
			'admin_label' => true,
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__('Alignment', 'spring-framework'),
			'param_name' => 'align',
			'description' => esc_html__('Select button alignment.', 'spring-framework'),
			'value' => array(
				esc_html__('Inline', 'spring-framework') => 'inline',
				esc_html__('Left', 'spring-framework') => 'left',
				esc_html__('Right', 'spring-framework') => 'right',
				esc_html__('Center', 'spring-framework') => 'center',
			),
			'std' => 'inline',
			'admin_label' => true,
		),
		array(
			'type' => 'gsf_switch',
			'heading' => esc_html__('Set full width button?', 'spring-framework'),
			'param_name' => 'button_block',
			'std' => '',
			'dependency' => array(
				'element' => 'align',
				'value_not_equal_to' => 'inline',
			),
			'admin_label' => true,
		),

        G5P()->shortcode()->vc_map_add_icon_font(),
		array(
			'type' => 'gsf_button_set',
			'heading' => esc_html__('Icon Alignment', 'spring-framework'),
			'description' => esc_html__('Select icon alignment.', 'spring-framework'),
			'param_name' => 'icon_align',
			'value' => array(
				esc_html__('Left', 'spring-framework') => 'left',
				esc_html__('Right', 'spring-framework') => 'right',
			),
			'dependency' => array(
				'element' => 'icon_font',
				'value_not_equal_to' => array(''),
			),
		),
		array(
			'type' => 'gsf_switch',
			'heading' => esc_html__('Advanced on click action', 'spring-framework'),
			'param_name' => 'custom_onclick',
			'std' => '',
			'description' => esc_html__('Insert inline onclick javascript action.', 'spring-framework'),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__('On click code', 'spring-framework'),
			'param_name' => 'custom_onclick_code',
			'description' => esc_html__('Enter onclick action code.', 'spring-framework'),
			'dependency' => array(
				'element' => 'custom_onclick',
				'value' => 'on',
			),
		),
		G5P()->shortcode()->vc_map_add_css_animation(),
		G5P()->shortcode()->vc_map_add_animation_duration(),
		G5P()->shortcode()->vc_map_add_animation_delay(),
		G5P()->shortcode()->vc_map_add_extra_class(),
		G5P()->shortcode()->vc_map_add_css_editor(),
		G5P()->shortcode()->vc_map_add_responsive()
	)
);