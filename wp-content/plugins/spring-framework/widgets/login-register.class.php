<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
$myaccount_page_id = get_option('woocommerce_myaccount_page_id');
if (!class_exists('G5P_Widget_Login_Register')) {
	class G5P_Widget_Login_Register extends GSF_Widget
	{
		public function __construct()
		{
			$this->widget_cssclass = 'widget-login-register';
			$this->widget_description = esc_html__("Display login register text", 'spring-framework');
			$this->widget_id = 'gsf-login-register';
			$this->widget_name = esc_html__('G5Plus: Login - Register', 'spring-framework');
			$this->settings = array(
				'fields' => array(
					array(
						'id'      => 'title',
						'type'    => 'text',
						'default' => '',
						'title'   => esc_html__('Title', 'spring-framework')
					),
					array(
						'id'      => 'login_text',
						'type'    => 'text',
						'default' => esc_html__('LOG IN', 'spring-framework'),
						'title'   => esc_html__('Login Text', 'spring-framework')
					),
					array(
						'id'      => 'register_text',
						'type'    => 'text',
						'default' => esc_html__('JOIN', 'spring-framework'),
						'title'   => esc_html__('Register Text', 'spring-framework')
					)
				)
			);
			parent::__construct();
		}
		
		function widget($args, $instance)
		{
			extract($args, EXTR_SKIP);
			$title = (!empty($instance['title'])) ? $instance['title'] : '';
			$title = apply_filters('widget_title', $title, $instance, $this->id_base);
			$login_text = (!empty($instance['login_text'])) ? $instance['login_text'] : esc_html__('Sign In', 'spring-framework');
			$register_text = (!empty($instance['register_text'])) ? $instance['register_text'] : esc_html__('Join', 'spring-framework');
			?>
			<?php echo wp_kses_post($args['before_widget']);
			if ($title) {
				echo wp_kses_post($args['before_title'] . $title . $args['after_title']);
			}
			?>
			<i class="fa fa-pagelines"></i>
			<?php if (!is_user_logged_in()): ?>
			<a class="gsf-login-link-sign-in" href="#"><?php echo esc_html($login_text); ?></a>
			<span class="gsf-login-register-separator"> / </span>
			<a class="gsf-login-link-sign-up" href="#"><?php echo esc_html($register_text); ?></a>
		<?php else: ?>
			<?php
			$current_user = wp_get_current_user();
			$display_name = empty($current_user->display_name) ? $current_user->user_login : $current_user->display_name;
			?>
			<a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>"
			   title="<?php esc_attr_e('My Account', 'spring-framework'); ?>"><?php echo esc_html($display_name).','
				?></a>
			<a href="<?php echo esc_url(wp_logout_url(is_home() ? home_url('/') : get_permalink())); ?>"><?php _e('Logout', 'spring-framework'); ?></a>
		<?php endif; ?>
			<?php echo wp_kses_post($args['after_widget']);
		}
	}
}