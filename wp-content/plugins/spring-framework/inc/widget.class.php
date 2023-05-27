<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('G5P_Inc_Widget')) {
	class G5P_Inc_Widget {
		private static $_instance;
		public static function getInstance()
		{
			if (self::$_instance == NULL) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		public function init() {
			spl_autoload_register(array($this, 'widgetsAutoload'));
			add_action('widgets_init', array($this,'registerWidgets'), 1);

			add_action( 'in_widget_form', array($this,'customWidgetFieldForm'), 10, 3 );
			add_filter( 'widget_update_callback', array($this,'customWidgetFieldUpdate'), 10, 2 );
			add_action( 'wp_loaded', array($this,'customWidgetFieldFrontendHook') );

			add_filter('gsf_sidebar',array($this,'register_sidebar'));
		}

		/**
		 * Widget auto loader
		 *
		 * @param $class
		 */
		public function widgetsAutoload($class)
		{
			$file_name = preg_replace('/^G5P_Widget_/', '', $class);
			if ($file_name !== $class) {
				$file_name = strtolower($file_name);
				$file_name = str_replace('_', '-', $file_name);
				G5P()->loadFile(G5P()->pluginDir("widgets/{$file_name}.class.php"));
			}
		}



		public function registerWidgets() {
			register_widget('G5P_Widget_Author');
			register_widget('G5P_Widget_Banner');
			register_widget('G5P_Widget_Gallery');
			register_widget('G5P_Widget_Login_Register');
			register_widget('G5P_Widget_Posts');
			register_widget('G5P_Widget_Social_Profile');
			register_widget('G5P_Widget_Twitter');
            if(class_exists('WooCommerce')) {
                register_widget('G5P_Widget_Payment_Method');
                register_widget('G5P_Widget_Price_Filter');
                register_widget('G5P_Widget_Product_Sorting');
                register_widget('G5P_Widget_Product_Filter_Attribute');
                register_widget('G5P_Widget_Product_Category_Filter');
            }
            register_widget('G5P_Widget_Wishlist');

		}

		public function customWidgetFieldForm($widget, $return, $instance) {
			if ( !isset( $instance['css_class'] ) ) $instance['css_class'] = null;

			$extra_classes = &G5P()->helper()->get_extra_class();
			$extra_class = array();
			foreach ($extra_classes as $value) {
				$extra_class[$value] = $value;
			}
			$settings = array(
				'fields' => array(
                    array(
                        'id' => 'title_style',
                        'type' => 'image_set',
                        'options' => G5P()->settings()->get_widget_title_style(true),
                        'layout' => 'full',
                        'default' => '',
                        'title' => esc_html__('Widget Title Style','spring-framework')
                    ),
					array(
						'id' => 'css_class',
						'type' => 'selectize',
						'tags' => true,
						'options' => $extra_class,
						'layout' => 'full',
						'title' => esc_html__('Custom Css','spring-framework')
					)
				)
			);

			?>
			<div class="gsf-widget-custom">
				<?php
				GSF()->helper()->setFieldPrefix('widget-' . $widget->id_base . '[' . $widget->number . ']');
				GSF()->helper()->renderFields($settings, $instance);
				GSF()->helper()->setFieldPrefix('');
				?>
			</div>
			<?php
			return $instance;
		}

		public function customWidgetFieldUpdate($instance, $new_instance){
			$instance['css_class'] = array_key_exists('css_class',$new_instance) ? $new_instance['css_class'] : '';
            $instance['title_style'] = array_key_exists('title_style',$new_instance) ? $new_instance['title_style'] : '';
			return $instance;
		}

		public function customWidgetFieldFrontendHook() {
			if ( !is_admin() ) {
				add_filter( 'dynamic_sidebar_params', array($this,'customWidgetFieldFrontEnd') );
			}
		}

		public function customWidgetFieldFrontEnd($params) {
			global $wp_registered_widgets, $widget_number;

			$widget_id              = $params[0]['widget_id'];
			$widget_obj             = $wp_registered_widgets[$widget_id];
			$widget_num             = $widget_obj['params'][0]['number'];
			$widget_opt             = null;

			// if Widget Logic plugin is enabled, use it's callback
			if ( in_array( 'widget-logic/widget_logic.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				$widget_logic_options = get_option( 'widget_logic' );
				if ( isset( $widget_logic_options['widget_logic-options-filter'] ) && 'checked' == $widget_logic_options['widget_logic-options-filter'] ) {
					$widget_opt = get_option( $widget_obj['callback_wl_redirect'][0]->option_name );
				} else {
					$widget_opt = get_option( $widget_obj['callback'][0]->option_name );
				}

				// if Widget Context plugin is enabled, use it's callback
			} elseif ( in_array( 'widget-context/widget-context.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				$callback = isset($widget_obj['callback_original_wc']) ? $widget_obj['callback_original_wc'] : null;
				$callback = !$callback && isset($widget_obj['callback']) ? $widget_obj['callback'] : null;

				if ($callback && is_array($widget_obj['callback'])) {
					$widget_opt = get_option( $callback[0]->option_name );
				}
			}
			// Default callback
			else {
				// Check if WP Page Widget is in use
				global $post;
				$id = ( isset( $post->ID ) ? get_the_ID() : NULL );
				if ( isset( $id ) && get_post_meta( $id, '_customize_sidebars' ) ) {
					$custom_sidebarcheck = get_post_meta( $id, '_customize_sidebars' );
				}
				if ( isset( $custom_sidebarcheck[0] ) && ( $custom_sidebarcheck[0] == 'yes' ) ) {
					$widget_opt = get_option( 'widget_'.$id.'_'.substr($widget_obj['callback'][0]->option_name, 7) );
				} elseif ( isset( $widget_obj['callback'][0]->option_name ) ) {
					$widget_opt = get_option( $widget_obj['callback'][0]->option_name );
				}
			}


			if ( isset( $widget_opt[$widget_num]['css_class'] ) && is_array( $widget_opt[$widget_num]['css_class'] ) ) {
				$custom_css = join(' ', $widget_opt[$widget_num]['css_class']);
				$params[0]['before_widget'] = preg_replace( '/class="/', "class=\"{$custom_css} ", $params[0]['before_widget'], 1 );
			}
            $widget_opt[$widget_num]['title_style'] = (!isset($widget_opt[$widget_num]['title_style']) || empty($widget_opt[$widget_num]['title_style'])) ? G5P()->options()->get_widget_title_style() : $widget_opt[$widget_num]['title_style'];
            $custom_css = $widget_opt[$widget_num]['title_style'];
            $params[0]['before_widget'] = preg_replace( '/class="/', "class=\"{$custom_css} ", $params[0]['before_widget'], 1 );

			return $params;
		}

		public function register_sidebar($sidebar) {
		    return wp_parse_args(array(
                array(
                    'name' => esc_html__("Top Bar Left", 'spring-framework'),
                    'id' => 'top_bar_left',
                ),
                array(
                    'name' => esc_html__("Top Bar Right", 'spring-framework'),
                    'id' => 'top_bar_right',
                ),
                array(
                    'name' => esc_html__("Footer 1", 'spring-framework'),
                    'id' => 'footer_1',
                ),
                array(
                    'name' => esc_html__("Footer 2", 'spring-framework'),
                    'id' => 'footer_2',
                ),
                array(
                    'name' => esc_html__("Footer 3", 'spring-framework'),
                    'id' => 'footer_3',
                ),
                array(
                    'name' => esc_html__("Footer 4", 'spring-framework'),
                    'id' => 'footer_4',
                ),
                array(
                    'name' => esc_html__("Canvas", 'spring-framework'),
                    'id' => 'canvas',
                ),
                array(
                    'name' => esc_html__("Woocommerce Filter", 'spring-framework'),
                    'id' => 'woocommerce-filter',
                )
            ),$sidebar);
        }
	}
}