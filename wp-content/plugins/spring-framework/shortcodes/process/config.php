<?php
return array(
    'base'     => 'gsf_process',
    'name'     => esc_html__('Process', 'spring-framework'),
    'icon'     => 'fa fa-sort-numeric-asc',
    'category' => G5P()->shortcode()->get_category_name(),
    'params'   => array_merge(
        array(
            array(
                'type' => 'gsf_image_set',
                'heading' => esc_html__('Layout Style', 'spring-framework'),
                'param_name' => 'layout_style',
                'value' => apply_filters('gsf_process_layout_style',array(
                    'style-01' => array(
                        'label' => esc_html__('Style 01', 'spring-framework'),
                        'img' => G5P()->pluginUrl('assets/images/shortcode/process-01.png'),
                    ),
                    'style-02' => array(
                        'label' => esc_html__('Style 02', 'spring-framework'),
                        'img' => G5P()->pluginUrl('assets/images/shortcode/process-02.png'),
                    )
                )),
                'std' => 'style-01',
                'admin_label' => true
            ),
            array(
                'type' => 'param_group',
                'heading' => esc_html__('Values','spring-framework'),
                'param_name' => 'values',
                'description' => esc_html__('Enter values process list','spring-framework'),
                'value' => '',
                'params' => array(
                    array(
                        'type'        => 'textfield',
                        'heading'     => esc_html__('Title', 'spring-framework'),
                        'param_name'  => 'title',
                        'value'       => '',
                        'admin_label' => true,
                    ),
                    array(
                        'type'        => 'textarea_raw_html',
                        'heading'     => esc_html__('Description', 'spring-framework'),
                        'param_name'  => 'description',
                        'value'       => '',
                        'description' => esc_html__('Provide the description for this element.', 'spring-framework'),
                    ),
                    array(
                        'type'       => 'vc_link',
                        'heading'    => esc_html__('Link (url)', 'spring-framework'),
                        'param_name' => 'link',
                        'value'      => '',
                    ),
                )
            )
        ),
        G5P()->shortCode()->get_column_responsive(array('element' => 'layout_style', 'value' => 'style-01')),
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