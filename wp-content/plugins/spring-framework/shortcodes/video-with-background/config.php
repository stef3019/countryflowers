<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 15/11/2017
 * Time: 11:12 SA
 */

return array(
    'name' => esc_html__('Video with Background', 'spring-framework'),
    'base' => 'gsf_video_with_background',
    'icon' => 'fa fa-youtube-play',
    'category' => G5P()->shortcode()->get_category_name(),
    'params' => array(
        array(
            'type' => 'textfield',
            'heading' => esc_html__( 'Video URL', 'spring-framework' ),
            'param_name' => 'video_url',
            'value' => ''
        ),
        array(
            'type' => 'attach_image',
            'heading' => esc_html__( 'Custom Background', 'spring-framework' ),
            'param_name' => 'image',
            'value' => '',
            'description' => esc_html__( 'Select an image from media library.', 'spring-framework' ),
        ),
        array(
            'type' => 'dropdown',
            'heading' => esc_html__( 'Height Mode', 'spring-framework' ),
            'param_name' => 'height_mode',
            'value' => array(
                '1:1' => '100',
                '4:3' => '133.333333333',
                '3:4' => '75',
                '16:9' => '177.777777778',
                '9:16' => '56.25',
                esc_html__( 'Custom', 'spring-framework' )=> 'custom'
            ),
            'std' => '56.25',
            'dependency' => array('element' => 'banner_bg_image', 'value_not_equal_to' => array('')),
            'description' => esc_html__( 'Sizing proportions for height and width. Select "Original" to scale image without cropping.', 'spring-framework' )
        ),
        array(
            'type' => 'textfield',
            'heading' => esc_html__( 'Height', 'spring-framework' ),
            'param_name' => 'height',
            'std' => '400px',
            'dependency' => array('element' => 'height_mode', 'value' => 'custom'),
            'description' => esc_html__( 'Enter custom height (include unit)', 'spring-framework' )
        ),
        array(
            'type' => 'colorpicker',
            'heading' => esc_html__( 'Icon Background Color', 'spring-framework' ),
            'param_name' => 'icon_bg_color',
            'edit_field_class' => 'vc_col-sm-6 vc_column',
            'dependency' => array('element' => 'image', 'value_not_equal_to' => array('')),
            'std' => G5P()->options()->get_accent_color()
        ),
        array(
            'type' => 'colorpicker',
            'heading' => esc_html__( 'Icon Color', 'spring-framework' ),
            'param_name' => 'icon_color',
            'edit_field_class' => 'vc_col-sm-6 vc_column',
            'dependency' => array('element' => 'image', 'value_not_equal_to' => array('')),
            'std' => G5P()->options()->get_foreground_accent_color()
        ),
        array(
            'type' => 'colorpicker',
            'heading' => esc_html__( 'Icon Background Hover Color', 'spring-framework' ),
            'param_name' => 'icon_bg_hover_color',
            'edit_field_class' => 'vc_col-sm-6 vc_column',
            'std' => '#333'
        ),
        array(
            'type' => 'colorpicker',
            'heading' => esc_html__( 'Icon Hover Color', 'spring-framework' ),
            'param_name' => 'icon_hover_color',
            'edit_field_class' => 'vc_col-sm-6 vc_column',
            'std' => '#fff'
        ),
        G5P()->shortcode()->vc_map_add_css_animation(),
        G5P()->shortcode()->vc_map_add_animation_duration(),
        G5P()->shortcode()->vc_map_add_animation_delay(),
        G5P()->shortcode()->vc_map_add_extra_class(),
        G5P()->shortcode()->vc_map_add_css_editor(),
        G5P()->shortcode()->vc_map_add_responsive()
    )
);