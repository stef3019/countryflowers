<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('G5P_Widget_Social_Profile')) {
	class G5P_Widget_Social_Profile extends  GSF_Widget
	{
		public function __construct() {
			$this->widget_cssclass    = 'widget-social-profile';
			$this->widget_id          = 'gsf-social-profile';
			$this->widget_name        = esc_html__( 'G5Plus: Social Profile', 'spring-framework' );

			$this->settings = array(
				'fields' => array(
					array(
						'id'      => 'title',
						'type'    => 'text',
						'default' => '',
						'title'   => esc_html__('Title', 'spring-framework')
					),
					array(
						'id'        => 'style',
						'title'     => esc_html__('Layout Style ', 'spring-framework'),
						'type'      => 'select',
						'default'      => 'classic',
						'options' => array(
							'classic' => esc_html__('Classic', 'spring-framework'),
							'circle' => esc_html__('Circle', 'spring-framework'),
							'circle-outline' => esc_html__('Circle Outline', 'spring-framework'),
						)
					),
					array(
						'id'        => 'size',
						'title'     => esc_html__('Size: ', 'spring-framework'),
						'type'      => 'select',
						'default'      => 'normal',
						'options' => array(
							'large' => esc_html__('Large', 'spring-framework'),
							'normal' => esc_html__('Normal', 'spring-framework'),
							'small' => esc_html__('Small', 'spring-framework'),

						)
					),
					array(
						'id' => "social_networks",
						'title' => esc_html__('Social Networks', 'spring-framework'),
						'type' => 'selectize',
						'multiple' => true,
						'drag' => true,
						'placeholder' => esc_html__('Select Social Networks', 'spring-framework'),
						'options' => G5P()->settings()->get_social_networks(),
					),
					array(
						'id'      => 'social_text',
						'type'    => 'text',
						'default' => '',
						'title'   => esc_html__('Text Before Icon', 'spring-framework')
					)
				)
			);

			parent::__construct();
		}

		public function widget($args, $instance) {
			extract( $args, EXTR_SKIP );
			$title = (!empty($instance['title'])) ? $instance['title'] : '';
			$title = apply_filters('widget_title', $title, $instance, $this->id_base);
			
			echo wp_kses_post($args['before_widget']);
			if ($title) {
				echo wp_kses_post($args['before_title'] . $title . $args['after_title']);
			}
			if(function_exists('Spring_Plant')) {
                Spring_Plant()->templates()->social_networks($instance['social_networks'], $instance['style'], $instance['size'], $instance['social_text']);
            }
			echo wp_kses_post($args['after_widget']);
		}
	}
}
