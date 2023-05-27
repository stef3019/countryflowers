<?php
return array(
	'name' => esc_html__('Space', 'spring-framework'),
	'base' => 'gsf_space',
	'icon' => 'fa fa-arrows-v',
	'category' => G5P()->shortcode()->get_category_name(),
	'params' => array(
		array(
			'type' => 'gsf_number',
            'param_name' => 'desktop',
			'heading' => __('<i class="fa fa-desktop"></i> Desktop', 'spring-framework'),
			'description' => esc_html__('Browser Width >= 1200px', 'spring-framework'),
			'admin_label' => true,
			'std' => 90,
			'args' => array(
                'min' => 1,
                'max' => 500,
                'step' => 1
            )
		),
		array(
            'type' => 'gsf_number',
			'heading' => __('<i class="fa fa-tablet" style="transform: rotate(90deg);"></i> Tablet', 'spring-framework'),
			'description' => esc_html__('Browser Width >= 992px and < 1200px', 'spring-framework'),
			'param_name' => 'tablet',
			'admin_label' => true,
			'std' => 70,
            'args' => array(
                'min' => 1,
                'max' => 500,
                'step' => 1
            ),
			'edit_field_class' => 'vc_col-sm-6 vc_column'
		),
		array(
            'type' => 'gsf_number',
			'heading' => __('<i class="fa fa-tablet"></i> Tablet Portrait', 'spring-framework'),
			'description' => esc_html__('Browser Width >= 768px and < 991px', 'spring-framework'),
			'param_name' => 'tablet_portrait',
			'admin_label' => true,
			'value' => 60,
            'args' => array(
                'min' => 1,
                'max' => 500,
                'step' => 1
            ),
			'edit_field_class' => 'vc_col-sm-6 vc_column'
		),
		array(
            'type' => 'gsf_number',
			'heading' => __('<i class="fa fa-mobile" style="transform: rotate(90deg);"></i> Mobile Landscape', 'spring-framework'),
			'description' => esc_html__('Browser Width >= 576px and < 768px', 'spring-framework'),
			'param_name' => 'mobile_landscape',
			'admin_label' => true,
			'value' => 50,
            'args' => array(
                'min' => 1,
                'max' => 500,
                'step' => 1
            ),
			'edit_field_class' => 'vc_col-sm-6 vc_column'
		),
		array(
            'type' => 'gsf_number',
			'heading' => __('<i class="fa fa-mobile"></i> Mobile', 'spring-framework'),
			'description' => esc_html__('Browser Width < 576px', 'spring-framework'),
			'param_name' => 'mobile',
			'admin_label' => true,
			'value' => 40,
            'args' => array(
                'min' => 1,
                'max' => 500,
                'step' => 1
            ),
			'edit_field_class' => 'vc_col-sm-6 vc_column'
		)
	)
);