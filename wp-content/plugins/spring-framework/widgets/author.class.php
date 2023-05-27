<?php

/**
 * Created by PhpStorm.
 * User: thanhthai
 * Date: 22/01/2018
 * Time: 10:16 AM
 */
if (!class_exists('G5P_Widget_Author')) {
	class G5P_Widget_Author extends GSF_Widget
	{
		public function __construct()
		{
			$this->widget_cssclass = 'widget-author';
			$this->widget_id = 'gsf-author';
			$this->widget_name = esc_html__('G5Plus: Author', 'spring-framework');
			$this->settings = array(
				'fields' => array(
					array(
						'id'      => 'title',
						'type'    => 'text',
						'default' => '',
						'title'   => esc_html__('Title:', 'spring-framework')
					),
					array(
						'id'    => 'image',
						'type'  => 'image',
						'title' => esc_html__('Select Image', 'spring-framework')
					),
					array(
						'id'      => 'name',
						'type'    => 'text',
						'default' => '',
						'title'   => esc_html__('Name:', 'spring-framework')
					),
					array(
						'id'      => 'description',
						'type'    => 'textarea',
						'default' => '',
						'title'   => esc_html__('Description:', 'spring-framework')
					),
					array(
						'id'          => "social_networks",
						'title'       => esc_html__('Social Networks', 'spring-framework'),
						'type'        => 'selectize',
						'multiple'    => true,
						'drag'        => true,
						'placeholder' => esc_html__('Select Social Networks', 'spring-framework'),
						'options'     => G5P()->settings()->get_social_networks(),
					),
				)
			);
			parent::__construct();
		}
		
		function widget($args, $instance)
		{
			extract($args, EXTR_SKIP);
			$wrapper_classes = array(
				'widget-author-wrap',
			);
			$title = (!empty($instance['title'])) ? $instance['title'] : '';
			$title = apply_filters('widget_title', $title, $instance, $this->id_base);
			echo wp_kses_post($args['before_widget']);
			if ($title) {
				echo wp_kses_post($args['before_title'] . $title . $args['after_title']);
			}
			$image = (!empty($instance['image'])) ? $instance['image'] : '';
			$name = (!empty($instance['name'])) ? $instance['name'] : '';
			$desc = (!empty($instance['description'])) ? $instance['description'] : '';
			?>
			<div class="<?php echo join(' ', $wrapper_classes) ?>">
				<?php if (isset($image['url']) && !empty($image['url'])): ?>
					<div class="author-avatar">
						<?php if (!empty($link)): ?>
						<a href="<?php echo esc_url($link) ?>" title="<?php echo esc_attr($name) ?>">
							<?php endif; ?>
							<img src="<?php echo esc_url($image['url']) ?>" alt="<?php echo esc_attr($name) ?>">
							<?php if (!empty($link)): ?>
						</a>
					<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php if (isset($name) && !empty($name)) ?>
				<h4 class="author-name"><?php echo esc_html($name) ?></h4>
				<?php if (isset($desc) && !empty($desc)) ?>
				<p class="author-description text-italic"><?php echo esc_html($desc) ?></p>
				<?php
				if(function_exists('Spring_Plant')) {
					Spring_Plant()->templates()->social_networks($instance['social_networks']);
				}
				?>
			</div>
			<?php
			echo wp_kses_post($args['after_widget']);
			?>
			<?php
		}
	}
}