<?php
/**
 * The template for displaying config.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
return array(
	'base' => 'gsf_post_horizontal',
	'name' => esc_html__( 'Post Horizontal', 'spring-framework' ),
	'category' => G5P()->shortcode()->get_category_name(),
	'icon' => 'fa fa-file-text',
	'params' => array_merge(
		array(
			array(
				'param_name' => 'posts_per_page',
				'heading' => esc_html__( 'Posts Per Page', 'spring-framework' ),
				'description' => esc_html__( 'Enter number of posts per page you want to display. Default 5', 'spring-framework' ),
				'type' => 'textfield',
				'std' => 5
			),
            G5P()->shortCode()->vc_map_add_autoplay_enable(),
            G5P()->shortCode()->vc_map_add_autoplay_timeout(),
			array(
				'param_name' => 'post_animation',
				'heading' => esc_html__( 'Animation', 'spring-framework' ),
				'description' => esc_html__( 'Specify your post animation', 'spring-framework' ),
				'type' => 'dropdown',
				'value' => array_flip( G5P()->settings()->get_animation(true) ),
				'edit_field_class' => 'vc_col-sm-6 vc_column',
				'std' => '-1'
			)
        ),
        G5P()->shortcode()->get_post_filter(),
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