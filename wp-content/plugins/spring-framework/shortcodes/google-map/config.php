<?php
return array(
    'name'     => esc_html__('Google Map', 'spring-framework'),
    'base'     => 'gsf_google_map',
    'icon'     => 'fa fa-map-marker',
    'category' => G5P()->shortcode()->get_category_name(),
    'params'   => array(
        array(
            'type'       => 'param_group',
            'heading'    => esc_html__('Markers', 'spring-framework'),
            'param_name' => 'markers',
            'value'      => urlencode(json_encode(array(
                array(
                    'label' => esc_html__('Title', 'spring-framework'),
                    'value' => '',
                ),
            ))),
            'params'     => array(
                array(
                    'type'        => 'textfield',
                    'heading'     => esc_html__('Latitude ', 'spring-framework'),
                    'param_name'  => 'lat',
                    'admin_label' => true,
                    'value'       => '',
                ),
                array(
                    'type'        => 'textfield',
                    'heading'     => esc_html__('Longitude ', 'spring-framework'),
                    'param_name'  => 'lng',
                    'admin_label' => true,
                    'value'       => '',
                ),
                array(
                    'type'        => 'textfield',
                    'heading'     => esc_html__('Title', 'spring-framework'),
                    'param_name'  => 'title',
                    'admin_label' => true,
                    'value'       => '',
                ),
                array(
                    'type'       => 'textarea',
                    'heading'    => esc_html__('Description', 'spring-framework'),
                    'param_name' => 'description',
                    'value'      => ''
                ),
                array(
                    'type'        => 'attach_image',
                    'heading'     => esc_html__('Marker Icon', 'spring-framework'),
                    'param_name'  => 'icon',
                    'value'       => '',
                    'description' => esc_html__('Select an image from media library.', 'spring-framework'),
                ),
            ),
        ),
        array(
            'type'       => 'textfield',
            'heading'    => esc_html__('API Key', 'spring-framework'),
            'param_name' => 'api_key',
            'std'        => 'AIzaSyAwey_47Cen4qJOjwHQ_sK1igwKPd74J18',
        ),
        array(
            'type'             => 'textfield',
            'heading'          => esc_html__('Map height (px or %)', 'spring-framework'),
            'param_name'       => 'map_height',
            'edit_field_class' => 'vc_col-sm-6 vc_column',
            'std'              => '500px',
        ),
        array(
            'type'             => 'textfield',
            'heading' => esc_html__('Map height in Large Devices', 'spring-framework'),
            'description' => esc_html__('Browser Width < 1200px', 'spring-framework'),
            'param_name'       => 'map_height_lg',
            'edit_field_class' => 'vc_col-sm-6 vc_column',
            'std'              => '450px',
        ),
        array(
            'type'             => 'textfield',
            'heading' => esc_html__('Map height in Medium Devices', 'spring-framework'),
            'description' => esc_html__('Browser Width < 992px', 'spring-framework'),
            'param_name'       => 'map_height_md',
            'edit_field_class' => 'vc_col-sm-6 vc_column',
            'std'              => '400px',
        ),
        array(
            'type'             => 'textfield',
            'heading' => esc_html__('Map height in Small Devices', 'spring-framework'),
            'description' => esc_html__('Browser Width < 768px', 'spring-framework'),
            'param_name'       => 'map_height_sm',
            'edit_field_class' => 'vc_col-sm-6 vc_column',
            'std'              => '350px',
        ),
        array(
            'type'             => 'textfield',
            'heading' => esc_html__('Map height in Extra Small Devices', 'spring-framework'),
            'description' => esc_html__('Browser Width < 600px', 'spring-framework'),
            'param_name'       => 'map_height_mb',
            'edit_field_class' => 'vc_col-sm-6 vc_column',
            'std'              => '300px',
        ),
        array(
            'type'             => 'gsf_number',
            'heading'          => esc_html__('Map zoom level default (0 - 18)', 'spring-framework'),
            'param_name'       => 'map_zoom',
            'edit_field_class' => 'vc_col-sm-6 vc_column',
            'args' => array(
                'min' => 0,
                'max' => 18,
                'step' => 1
            ),
            'std' => 15
        ),
        array(
            'type'             => 'gsf_switch',
            'heading'          => esc_html__('Zoom on scroll', 'spring-framework'),
            'param_name'       => 'scroll_wheel',
            'edit_field_class' => 'vc_col-sm-6 vc_column',
            'std'              => ''
        ),
        array(
            'type' => 'dropdown',
            'heading' => esc_html__('Map Style', 'spring-framework'),
            'param_name' => 'map_style',
            'std' => 'light',
            'value' => array(
                esc_html__('Standar', 'spring-framework') => 'standar',
                esc_html__('Light (Default)', 'spring-framework') => 'light',
                esc_html__('Dark', 'spring-framework') => 'dark',
                esc_html__('Sliver', 'spring-framework') => 'sliver',
                esc_html__('Retro', 'spring-framework') => 'retro',
                esc_html__('Night', 'spring-framework') => 'night',
                esc_html__('Aubergine', 'spring-framework') => 'aubergine',
                esc_html__('Custom', 'spring-framework') => 'custom'
            ),
            'admin_label'      => true,
            'edit_field_class' => 'vc_col-sm-6 vc_column',
        ),
        array(
            'type'             => 'textarea_raw_html',
            'heading'          => esc_html__('Custom map style', 'spring-framework'),
            'param_name'       => 'map_style_content',
            'dependency' => array('element' => 'map_style', 'value' => 'custom'),
            'description' => wp_kses_post(__('Come <a target="_blank" href="https://snazzymaps.com/">here</a> to search map style code!', 'spring-framework'))
        ),
        G5P()->shortcode()->vc_map_add_css_animation(),
        G5P()->shortcode()->vc_map_add_animation_duration(),
        G5P()->shortcode()->vc_map_add_animation_delay(),
        G5P()->shortcode()->vc_map_add_extra_class(),
        G5P()->shortcode()->vc_map_add_css_editor(),
        G5P()->shortcode()->vc_map_add_responsive()
    ),
);