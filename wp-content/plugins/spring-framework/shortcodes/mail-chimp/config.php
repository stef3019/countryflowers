<?php
/**
 * The template for displaying config.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
return array(
	'base'     => 'gsf_mail_chimp',
	'name'     => esc_html__('Mail Chimp', 'spring-framework'),
	'category' => G5P()->shortcode()->get_category_name(),
	'icon'     => 'fa fa-envelope',
	'params'   => array(
		array(
			'type'        => 'gsf_image_set',
			'heading'     => esc_html__('Layout Style', 'spring-framework'),
			'param_name'  => 'layout_style',
			'value'       => apply_filters('gsf_mail_chimp_layout_style',array(
				'style-01' => array(
					'label' => esc_html__('Style 01', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/shortcode/mailchimp-style-01.png'),
				),
				'style-02' => array(
					'label' => esc_html__('Style 02', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/shortcode/mailchimp-style-02.png'),
				),
				'style-03' => array(
					'label' => esc_html__('Style 03', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/shortcode/mailchimp-style-03.png'),
				),
				'style-04' => array(
					'label' => esc_html__('Style 04', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/shortcode/mailchimp-style-04.png'),
				)
			)),
			'std'         => 'style-01',
			'admin_label' => true,
		),
		array(
			'type' => 'colorpicker',
			'heading' => esc_html__('Icon Color', 'spring-framework'),
			'param_name' => 'mc_icon_color',
			'std' => G5P()->options()->get_accent_color(),
			'dependency' => array('element'=>'layout_style', 'value'=>'style-02'),
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