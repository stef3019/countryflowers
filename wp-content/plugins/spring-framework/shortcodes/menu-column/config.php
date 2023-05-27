<?php
return array(
	'base' => 'gsf_menu_column',
	'name' => esc_html__( 'Menu Column', 'spring-framework' ),
	'icon' => 'fa fa-bars',
	'category' => G5P()->shortcode()->get_category_name(),
	'params' => array(
        array(
            'type' => 'textfield',
            'heading' => esc_html__('Column Heading', 'spring-framework'),
            'param_name' => 'title',
            'admin_label' => true
        ),
        array(
            'type' => 'param_group',
            'heading' => esc_html__('Menu Items', 'spring-framework'),
            'param_name' => 'menu_items',
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => esc_html__('Item Title', 'spring-framework'),
                    'param_name' => 'label',
                    'admin_label' => true
                ),
                array(
                    'type' => 'vc_link',
                    'heading' => esc_html__('URL (Link)', 'spring-framework'),
                    'param_name' => 'link'
                ),
                G5P()->shortcode()->vc_map_add_icon_font(array(
                    'edit_field_class' => 'vc_col-sm-6 vc_column'
                )),
                array(
                    'type' => 'dropdown',
                    'heading' => esc_html__('Item Style', 'spring-framework'),
                    'param_name' => 'item_style',
                    'value' => array(
                        esc_html__('None', 'spring-framework') => '',
                        esc_html__('New', 'spring-framework') => 'new',
                        esc_html__('Hot', 'spring-framework') => 'hot'
                    )
                ),
                array(
                    'type' => 'textfield',
                    'heading' => esc_html__('Style Title', 'spring-framework'),
                    'param_name' => 'style_title',
                    'dependency' => array('element' => 'item_style', 'value' => array('new', 'hot'))
                )
            )
        ),
		G5P()->shortcode()->vc_map_add_css_animation(),
		G5P()->shortcode()->vc_map_add_animation_duration(),
		G5P()->shortcode()->vc_map_add_animation_delay(),
		G5P()->shortcode()->vc_map_add_extra_class(),
		G5P()->shortcode()->vc_map_add_css_editor(),
		G5P()->shortcode()->vc_map_add_responsive()
	),
);