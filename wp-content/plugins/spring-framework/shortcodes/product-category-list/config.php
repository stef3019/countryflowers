<?php
return array(
	'base' => 'gsf_product_category_list',
	'name' => esc_html__( 'Product Category List', 'spring-framework' ),
	'icon' => 'fa fa-list-ul',
	'category' => G5P()->shortcode()->get_category_name(),
	'params' => array(
        G5P()->shortcode()->vc_map_add_title(array(
            'admin_label' => true
        )),
		array(
            'type' => 'gsf_switch',
            'heading' => esc_html__('Show Empty Category', 'spring-framework'),
            'param_name' => 'show_empty',
            'edit_field_class' => 'vc_col-sm-6 vc_column',
            'std' => ''
        ),
        array(
            'type' => 'gsf_switch',
            'heading' => esc_html__('Show Hierarchy', 'spring-framework'),
            'param_name' => 'show_hierarchy',
            'edit_field_class' => 'vc_col-sm-6 vc_column',
            'std' => 'on'
        ),
        array(
            'type' => 'gsf_switch',
            'heading' => esc_html__('Show Item Count', 'spring-framework'),
            'param_name' => 'show_count',
            'edit_field_class' => 'vc_col-sm-6 vc_column',
            'std' => 'on'
        ),
		G5P()->shortcode()->vc_map_add_css_animation(),
		G5P()->shortcode()->vc_map_add_animation_duration(),
		G5P()->shortcode()->vc_map_add_animation_delay(),
		G5P()->shortcode()->vc_map_add_extra_class(),
		G5P()->shortcode()->vc_map_add_css_editor(),
		G5P()->shortcode()->vc_map_add_responsive()
	),
);