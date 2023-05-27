<?php
if (!defined('ABSPATH')) {
	exit('Direct script access denied.');
}

if (!class_exists('G5P_Inc_Config_Options')) {
	class G5P_Inc_Config_Options
	{
		/*
		 * loader instances
		 */
		private static $_instance;

		public static function getInstance()
		{
			if (self::$_instance == NULL) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		public function init()
		{
			// Defined Theme Options
			add_filter('gsf_option_config', array($this, 'define_theme_options'));
			add_filter('gsf_replace_font_option_keys',array($this,'replace_font_option_keys'));
		}

		public function replace_font_option_keys($keys) {
			return wp_parse_args(array(G5P()->getOptionName()),$keys);
		}

		public function define_theme_options($configs)
		{
			$configs['gsf_skins'] = array(
				'layout'      => 'inline',
				'page_title'  => esc_html__('Spring Plant Skin Options', 'spring-framework'),
				'menu_title'  => esc_html__('Skins Options', 'spring-framework'),
				'option_name' => G5P()->getOptionSkinName(),
				'permission'  => 'manage_options',
				'parent_slug' => 'gsf_welcome',
				'fields' => array(
					// Color Skin
					$this->get_config_section_color_skin(),
				),
			);

			$configs['gsf_options'] = array(
				'layout' => 'inline',
				'page_title' => esc_html__('Spring Plant Theme Options', 'spring-framework'),
				'menu_title' => esc_html__('Theme Options', 'spring-framework'),
				'option_name' => G5P()->getOptionName(),
				'permission' => 'manage_options',
				'parent_slug' => 'gsf_welcome',
				'preset' => true,
				'section' => array(

					// General
					$this->get_config_section_general(),



					// Layout
					$this->get_config_section_layout(),

					// Top Drawer
					$this->get_config_section_top_drawer(),

					// Top Bar
					$this->get_config_section_top_bar(),

					// Header
					$this->get_config_section_header(),

					// Logo
					$this->get_config_section_logo(),

					// Page Title
					$this->get_config_section_page_title(),

					// Footer
					$this->get_config_section_footer(),

                    // Typography
                    $this->get_config_section_typography(),

					// Color
					$this->get_config_section_colors(),

					// Connections
					$this->get_config_section_connections(),

					// Blog
					$this->get_config_section_blog(),

                    // Post Types
                    $this->get_config_section_custom_post_type(),

                    //  Popup setup
                    $this->get_config_section_popup(),
				),
			);


            if(class_exists('WooCommerce')) {
                $configs['gsf_options']['section'][] =  $this->get_config_section_woocommerce();
            }
            $custom_post_type_disable = G5P()->options()->get_custom_post_type_disable();
            if(!in_array('portfolio', $custom_post_type_disable)) {
                $configs['gsf_options']['section'][] = $this->get_config_section_portfolio();
            }
            $configs['gsf_options']['section'][] = $this->get_config_section_preset();
            $configs['gsf_options']['section'][] = $this->get_config_section_code();
			return $configs;
		}

		/**
		 * Get Config General
		 *
		 * @return array
		 */
		public function get_config_section_general()
		{
			return array(
				'id' => 'section_general',
				'title' => esc_html__('General', 'spring-framework'),
				'icon' => 'dashicons dashicons-admin-site',
				'general_options' => true,
				'fields' => array(
					/**
					 * General
					 */
					array(
						'id' => 'section_general_group_general',
						'title' => esc_html__('General', 'spring-framework'),
						'type' => 'group',
						'fields' => array(
								$this->get_config_toggle(array(
								'id' => 'lazy_load_images',
								'title' => esc_html__('Lazy Load Images', 'spring-framework'),
								'subtitle' => esc_html__('If enabled, images will only be loaded when they come to view', 'spring-framework'),
								'default' => ''
							)),

							array(
								'id' => 'section_general_group_custom_scroll',
								'title' => esc_html__('Custom Scroll', 'spring-framework'),
								'type' => 'group',
								'fields' => array(
									$this->get_config_toggle(array(
											'id' => 'custom_scroll',
											'title' => esc_html__('Custom Scroll', 'spring-framework'),
											'subtitle' => esc_html__('Turn On this option if you want to custom scroll', 'spring-framework'),
											'default' => ''
									)),
									array(
										'id' => 'custom_scroll_width',
										'type' => 'text',
										'input_type' => 'number',
										'title' => esc_html__('Custom Scroll Width', 'spring-framework'),
										'subtitle' => esc_html__('This must be numeric (no px) or empty.', 'spring-framework'),
										'default' => 10,
										'required' => array('custom_scroll', '=', 'on'),
									),
									array(
										'id' => 'custom_scroll_color',
										'type' => 'color',
										'title' => esc_html__('Custom Scroll Color', 'spring-framework'),
										'default' => '#19394B',
										'required' => array('custom_scroll', '=', 'on'),
									),
									array(
										'id' => 'custom_scroll_thumb_color',
										'type' => 'color',
										'title' => esc_html__('Custom Scroll Thumb Color', 'spring-framework'),
										'default' => '#69d2e7',
										'required' => array('custom_scroll', '=', 'on'),
									),
								)
							),
							$this->get_config_toggle(array(
								'id' => 'back_to_top',
								'title' => esc_html__('Back To Top', 'spring-framework'),
								'subtitle' => esc_html__('Turn Off this option if you want to disable back to top', 'spring-framework'),
								'default' => 'on'
							)),

							$this->get_config_toggle(array(
								'id' => 'rtl_enable',
								'title' => esc_html__('RTL Mode', 'spring-framework'),
								'subtitle' => esc_html__('Turn On this option if you want to enable rtl mode', 'spring-framework'),
								'default' => ''
							)),
                            array(
                                'id' => 'menu_transition',
                                'type' => 'select',
                                'title' => esc_html__('Menu Transition','spring-framework') ,
                                'default' => 'x-fadeInUp',
                                'general_options' => true,
                                'options' => array(
                                    'none' => esc_html__('None','spring-framework'),
                                    'x-fadeIn' => esc_html__('Fade In','spring-framework'),
                                    'x-fadeInUp' => esc_html__('Fade In Up','spring-framework'),
                                    'x-fadeInDown' => esc_html__('Fade In Down','spring-framework'),
                                    'x-fadeInLeft' => esc_html__('Fade In Left','spring-framework'),
                                    'x-fadeInRight' => esc_html__('Fade In Right','spring-framework'),
                                    'x-flipInX' => esc_html__('Flip In X','spring-framework'),
                                    'x-slideInUp' => esc_html__('Slide In Up','spring-framework')
                                )
                            ),

							array(
								'id' => 'section_general_group_social_meta',
								'title' => esc_html__('Social Meta', 'spring-framework'),
								'type' => 'group',
								'fields' => array(

									$this->get_config_toggle(array(
										'id' => 'social_meta_enable',
										'title' => esc_html__('Enable Social Meta Tags', 'spring-framework'),
										'subtitle' => esc_html__('Turn On this option if you want to enable social meta', 'spring-framework'),
										'default' => ''
									)),
									array(
										'id' => 'twitter_author_username',
										'type' => 'text',
										'title' => esc_html__('Twitter Username', 'spring-framework'),
										'subtitle' => esc_html__('Enter your twitter username here, to be used for the Twitter Card date. Ensure that you do not include the @ symbol.', 'spring-framework'),
										'default' => '',
										'required' => array('social_meta_enable', '=', 'on'),
									),
									array(
										'id' => 'googleplus_author',
										'type' => 'text',
										'title' => esc_html__('Google+ Username', 'spring-framework'),
										'subtitle' => esc_html__('Enter your Google+ username here, to be used for the authorship meta.', 'spring-framework'),
										'default' => '',
										'required' => array('social_meta_enable', '=', 'on'),
									),
								)
							),

							array(
								'id' => 'section_general_group_search_popup',
								'title' => esc_html__('Search Popup', 'spring-framework'),
								'type' => 'group',
								'fields' => array(
									array(
										'id' => 'search_popup_post_type',
										'type' => 'checkbox_list',
										'title' => esc_html__('Post Type For Ajax Search', 'spring-framework'),
										'options' => G5P()->settings()->get_search_ajax_popup_post_type(),
										'multiple' => true,
										'default' => array('post', 'product'),
									),
									array(
										'id' => 'search_popup_result_amount',
										'type' => 'text',
										'input_type' => 'number',
										'title' => esc_html__('Amount Of Search Result', 'spring-framework'),
										'default' => 8,
									)
								)
							),
                            array(
                                'id' => 'section_general_group_widget_options',
                                'title' => esc_html__('Widget Options', 'spring-framework'),
                                'type' => 'group',
                                'fields' => array(
                                    array(
                                        'id' => 'widget_title_style',
                                        'type' => 'image_set',
                                        'title' => esc_html__('Widget Title Default Style', 'spring-framework'),
                                        'options' => G5P()->settings()->get_widget_title_style(),
                                        'default' => 'title-default',
                                    )
                                )
                            ),
						)
					),
					/**
					 * Maintenance
					 */
					array(
						'id' => 'section_general_group_maintenance',
						'title' => esc_html__('Maintenance', 'spring-framework'),
						'type' => 'group',
						'toggle_default' => false,
						'fields' => array(
							array(
								'id' => 'maintenance_mode',
								'type' => 'button_set',
								'title' => esc_html__('Maintenance Mode', 'spring-framework'),
								'options' => G5P()->settings()->get_maintenance_mode(),
								'default' => '0'
							),
							array(
								'id' => 'maintenance_mode_page',
								'title' => esc_html__('Maintenance Mode Page', 'spring-framework'),
								'subtitle' => esc_html__('Select the page that is your maintenance page, if you would like to show a custom page instead of the standard WordPress message. You should use the Holding Page template for this page.', 'spring-framework'),
								'type' => 'selectize',
								'placeholder' => esc_html__('Select Page', 'spring-framework'),
								'data' => 'page',
								'data_args' => array(
									'numberposts' => -1
								),
								'edit_link' => true,
								'default' => '',
								'required' => array('maintenance_mode', '=', '2'),

							),
						)
					),
					/**
					 * Page Transition Section
					 * *******************************************************
					 */
					array(
						'id' => 'section_general_group_page_transition',
						'title' => esc_html__('Page Transition', 'spring-framework'),
						'type' => 'group',
						'toggle_default' => false,
						'fields' => array(
							$this->get_config_toggle(array(
								'id' => 'page_transition',
								'title' => esc_html__('Page Transition', 'spring-framework'),
								'subtitle' => esc_html__('Turn On this option if you want to enable page transition', 'spring-framework'),
								'default' => ''
							)),
							array(
								'id' => 'loading_animation',
								'type' => 'select',
								'title' => esc_html__('Loading Animation', 'spring-framework'),
								'subtitle' => esc_html__('Select type of pre load animation', 'spring-framework'),
								'options' => G5P()->settings()->get_loading_animation(),
								'default' => ''
							),
							array(
								'id' => 'loading_logo',
								'type' => 'image',
								'title' => esc_html__('Logo Loading', 'spring-framework'),
								'required' => array('loading_animation', '!=', ''),
							),

							array(
								'id' => 'loading_animation_bg_color',
								'title' => esc_html__('Loading Background Color', 'spring-framework'),
								'type' => 'color',
								'alpha' => true,
								'default' => '#fff',
								'required' => array('loading_animation', '!=', ''),
							),

							array(
								'id' => 'spinner_color',
								'title' => esc_html__('Spinner color', 'spring-framework'),
								'type' => 'color',
								'default' => '',
								'required' => array('loading_animation', '!=', ''),
							),

						)
					),
					/**
					 * Custom Favicon
					 * *******************************************************
					 */
					array(
						'id' => 'section_general_group_custom_favicon',
						'title' => esc_html__('Custom Favicon', 'spring-framework'),
						'type' => 'group',
						'toggle_default' => false,
						'fields' => array(
							array(
								'id' => 'custom_favicon',
								'type' => 'image',
								'title' => esc_html__('Custom favicon', 'spring-framework'),
								'subtitle' => esc_html__('Upload a 16px x 16px Png/Gif/ico image that will represent your website favicon', 'spring-framework'),
							),
							array(
								'id' => 'custom_ios_title',
								'type' => 'text',
								'title' => esc_html__('Custom iOS Bookmark Title', 'spring-framework'),
								'subtitle' => esc_html__('Enter a custom title for your site for when it is added as an iOS bookmark.', 'spring-framework'),
								'default' => ''
							),
							array(
								'id' => 'custom_ios_icon57',
								'type' => 'image',
								'title' => esc_html__('Custom iOS 57x57', 'spring-framework'),
								'subtitle' => esc_html__('Upload a 57px x 57px Png image that will be your website bookmark on non-retina iOS devices.', 'spring-framework'),
							),
							array(
								'id' => 'custom_ios_icon72',
								'type' => 'image',
								'title' => esc_html__('Custom iOS 72x72', 'spring-framework'),
								'subtitle' => esc_html__('Upload a 72px x 72px Png image that will be your website bookmark on non-retina iOS devices.', 'spring-framework'),
							),
							array(
								'id' => 'custom_ios_icon114',
								'type' => 'image',
								'title' => esc_html__('Custom iOS 114x114', 'spring-framework'),
								'subtitle' => esc_html__('Upload a 114px x 114px Png image that will be your website bookmark on retina iOS devices.', 'spring-framework'),
							),
							array(
								'id' => 'custom_ios_icon144',
								'type' => 'image',
								'title' => esc_html__('Custom iOS 144x144', 'spring-framework'),
								'subtitle' => esc_html__('Upload a 144px x 144px Png image that will be your website bookmark on retina iOS devices.', 'spring-framework'),
							),
						)
					),
					/**
					 * 404 Setting Section
					 * *******************************************************
					 */
					array(
						'id' => 'section_general_group_404',
						'title' => esc_html__('404 Page', 'spring-framework'),
						'type' => 'group',
						'toggle_default' => false,
						'fields' => array(
							$this->get_config_content_block(array(
								'id' => '404_content_block',
								'subtitle' => esc_html__('Specify the Content Block to use as a 404 page content.', 'spring-framework'),
								'required' => array('404_content_block_enable', '=', '1')
							)),
							array(
								'id' => '404_content',
								'title' => esc_html__('404 Page Content', 'spring-framework'),
								'default' => '',
								'type' => 'editor',
								'required' => array('404_content_block', '=', '')
							)
						)
					),
				)
			);
		}

		public function get_config_section_preset() {

			$configs = G5P()->settings()->getPresetPostType();
			$fields = array();
			foreach ($configs as $key => $config) {
				if (isset($config['preset']) && is_array($config['preset'])) {
					$group_fields = array();
					foreach ($config['preset'] as $presetKey => $presetValue) {
						$group_fields[] = $this->get_config_preset(array(
							'id' => "preset_{$presetKey}",
							'title' => $presetValue['title'],
							'create_link' => false,
							'link_target' => false,
						));
					}
					$group = array(
						'type' => 'group',
						'title' => $config['title'],
						'fields' => $group_fields
					);
					$fields[] = $group;
				} else {
					$fields[] = $this->get_config_preset(array(
						'id' => "preset_{$key}",
						'title' => $config['title'],
						'create_link' => false,
						'link_target' => false,
					));
				}
			}

			return array(
				'id' => 'section_preset',
				'title' => esc_html__('Preset Setting', 'spring-framework'),
				'icon' => 'dashicons dashicons-admin-generic',
				'general_options' => true,
				'fields' => $fields
			);
		}

		/**
		 * Get Config Layout
		 *
		 * @return array
		 */
		public function get_config_section_layout()
		{
			return array(
				'id' => 'section_layout',
				'title' => esc_html__('Layout', 'spring-framework'),
				'icon' => 'dashicons dashicons-editor-table',
				'fields' => array(
					array(
						'id' => 'main_layout',
						'title' => esc_html__('Site Layout', 'spring-framework'),
						'type' => 'image_set',
						'options' => G5P()->settings()->get_main_layout(),
						'default' => 'wide',
					),
					array(
						'id' => 'section_layout_group_main_content',
						'title' => esc_html__('Main Content', 'spring-framework'),
						'type' => 'group',
						'fields' => array(
							$this->get_config_toggle(array(
								'id' => 'content_full_width',
								'title' => esc_html__('Content Full Width', 'spring-framework'),
								'subtitle' => esc_html__('Turn On this option if you want to expand the content area to full width.', 'spring-framework'),
								'default' => '',
							)),
							array(
								'id' => 'content_padding',
								'title' => esc_html__('Content Padding', 'spring-framework'),
								'subtitle' => esc_html__('Set content padding', 'spring-framework'),
								'type' => 'spacing',
								'default' => array('left' => 0, 'right' => 0, 'top' => 50, 'bottom' => 50),
							),
							$this->get_config_sidebar_layout(array('id' => 'sidebar_layout')),
							$this->get_config_sidebar(array(
								'id' => 'sidebar',
								'default' => 'main',
								'required' => array('sidebar_layout', '!=', 'none')
							)),
							array(
								'id' => 'sidebar_width',
								'title' => esc_html__('Sidebar Width', 'spring-framework'),
								'type' => 'button_set',
								'options' => G5P()->settings()->get_sidebar_width(),
								'default' => 'small',
								'required' => array('sidebar_layout', '!=', 'none'),
							),
							$this->get_config_toggle(array(
								'id' => 'sidebar_sticky_enable',
								'title' => esc_html__('Sidebar Sticky', 'spring-framework'),
								'subtitle' => esc_html__('Turn On this option if you want to enable sidebar sticky', 'spring-framework'),
								'default' => '',
								'required' => array('sidebar_layout', '!=', 'none'),
							)),
                            $this->get_config_toggle(array(
                                'id' => 'above_content_enable',
                                'title' => esc_html__('Above Content Enable', 'spring-framework'),
                                'subtitle' => esc_html__('Turn Off this option if you want to disable Above content', 'spring-framework'),
                                'default' => ''
                            )),
                            $this->get_config_content_block(array(
                                'id' => 'above_content_block',
                                'subtitle' => esc_html__('Specify the Content Block to use as a Above content.', 'spring-framework'),
                                'required' => array('above_content_enable', '=', 'on')
                            )),
                            array(
                                'id' => 'above_content_margin_bottom',
                                'title' => esc_html__('Above Content Margin Bottom', 'spring-framework'),
                                'subtitle' => esc_html__('Enter number of margin bottom for Above content (default unit is px)', 'spring-framework'),
                                'type' => 'text',
                                'input_type' => 'number',
                                'default' => 50,
                                'required' => array('above_content_enable', '=', 'on')
                            )
						)
					),

					array(
						'id' => 'section_layout_group_mobile',
						'title' => esc_html__('Mobile', 'spring-framework'),
						'type' => 'group',
						'fields' => array(
							$this->get_config_toggle(array(
								'id' => 'mobile_sidebar_enable',
								'title' => esc_html__('Sidebar Mobile', 'spring-framework'),
								'subtitle' => esc_html__('Turn Off this option if you want to disable sidebar on mobile', 'spring-framework'),
								'default' => 'on',
								'required' => array('sidebar_layout', '!=', 'none'),
							)),
							$this->get_config_toggle(array(
								'id' => 'mobile_sidebar_canvas',
								'title' => esc_html__('Sidebar Mobile Canvas', 'spring-framework'),
								'subtitle' => esc_html__('Turn Off this option if you want to disable canvas sidebar on mobile', 'spring-framework'),
								'default' => 'on',
								'required' => array(
									array('sidebar_layout', '!=', 'none'),
									array('mobile_sidebar_enable', '=', 'on'),
								)
							)),
							array(
								'id' => 'mobile_content_padding',
								'title' => esc_html__('Content Padding Mobile', 'spring-framework'),
								'subtitle' => esc_html__('Set content top/bottom padding', 'spring-framework'),
								'type' => 'spacing'
							),
						)
					),
				)
			);
		}

		/**
		 * Get Config Top Drawer
		 *
		 * @return array
		 */
		public function get_config_section_top_drawer()
		{
			return array(
				'id' => 'section_top_drawer',
				'title' => esc_html__('Top Drawer', 'spring-framework'),
				'icon' => 'dashicons dashicons-archive',
				'fields' => array(
					array(
						'id' => 'top_drawer_mode',
						'title' => esc_html__('Top Drawer Mode', 'spring-framework'),
						'type' => 'button_set',
						'options' => G5P()->settings()->get_top_drawer_mode(),
						'default' => 'hide'
					),
					$this->get_config_content_block(array(
						'id' => 'top_drawer_content_block',
						'subtitle' => esc_html__('Specify the Content Block to use as a top drawer content.', 'spring-framework'),
						'required' => array('top_drawer_mode', '!=', 'hide')
					)),
					$this->get_config_toggle(array(
						'id' => 'top_drawer_content_full_width',
						'title' => esc_html__('Top Drawer Full Width', 'spring-framework'),
						'subtitle' => esc_html__('Turn On this option if you want to expand the content area to full width.', 'spring-framework'),
						'default' => '',
						'required' => array('top_drawer_mode', '!=', 'hide')
					)),
					array(
						'id' => "top_drawer_padding",
						'title' => esc_html__('Padding', 'spring-framework'),
						'subtitle' => esc_html__('Set top drawer padding', 'spring-framework'),
						'type' => 'spacing',
						'default' => array(
							'top' => 10,
							'bottom' => 10
						),
						'required' => array('top_drawer_mode', '!=', 'hide')
					),
					$this->get_config_border_bottom('top_drawer_border',array(
						'required' => array('top_drawer_mode', '!=', 'hide')
					)),
					$this->get_config_toggle(array(
						'id' => 'mobile_top_drawer_enable',
						'title' => esc_html__('Mobile Enable', 'spring-framework'),
						'subtitle' => esc_html__('Turn On this option if you want to enable top drawer on mobile', 'spring-framework'),
						'default' => '',
						'required' => array('top_drawer_mode', '!=', 'hide')
					)),
				)
			);
		}

		/**
		 * Get Config Top Bar
		 *
		 * @return array
		 */
		public function get_config_section_top_bar()
		{
			return array(
				'id' => 'section_top_bar',
				'title' => esc_html__('Top Bar', 'spring-framework'),
				'icon' => 'dashicons dashicons-schedule',
				'fields' => array(
					array(
						'id' => 'section_top_bar_group_desktop',
						'title' => esc_html__('Desktop', 'spring-framework'),
						'type' => 'group',
						'required' => array('header_layout','not in',array('header-9','header-10')),
						'fields' => array(
							$this->get_config_toggle(array(
								'id' => 'top_bar_enable',
								'title' => esc_html__('Top Bar Enable', 'spring-framework'),
								'subtitle' => esc_html__('Turn On this option if you want to enable top bar', 'spring-framework'),
								'default' => ''
							)),
							$this->get_config_content_block(array(
								'id' => 'top_bar_content_block',
								'subtitle' => esc_html__('Specify the Content Block to use as a top bar content.', 'spring-framework'),
								'required' => array('top_bar_enable', '=', 'on')
							)),
					)),

					array(
						'id' => 'section_top_bar_group_mobile',
						'title' => esc_html__('Mobile', 'spring-framework'),
						'type' => 'group',
						'fields' => array(
							$this->get_config_toggle(array(
								'id' => 'mobile_top_bar_enable',
								'title' => esc_html__('Top Bar Enable', 'spring-framework'),
								'subtitle' => esc_html__('Turn On this option if you want to enable top bar', 'spring-framework'),
								'default' => ''
							)),
							$this->get_config_content_block(array(
								'id' => 'mobile_top_bar_content_block',
								'subtitle' => esc_html__('Specify the Content Block to use as a top bar content.', 'spring-framework'),
								'required' => array('mobile_top_bar_enable', '=', 'on')
							)),
					)),
				)
			);
		}

		/**
		 * Get Config Header
		 *
		 * @return array
		 */
		public function get_config_section_header()
		{
			return array(
				'id' => 'section_header',
				'title' => esc_html__('Header', 'spring-framework'),
				'icon' => 'dashicons dashicons-editor-kitchensink',
				'fields' => array(
					G5P()->configOptions()->get_config_toggle( array(
						'id' => 'header_enable',
						'title' => esc_html__('Header Enable','spring-framework') ,
						'default' => 'on',
						'subtitle' => esc_html__('Turn Off this option if you want to hide header', 'spring-framework'),
					)),
					array(
						'id' => 'header_responsive_breakpoint',
						'type' => 'select',
						'title' => esc_html__('Header Responsive Breakpoint', 'spring-framework'),
						'options' => array(
							'1199' => esc_html__('Large Devices: < 1200px', 'spring-framework'),
							'991' => esc_html__('Medium Devices: < 992px', 'spring-framework'),
							'767' => esc_html__('Tablet Portrait: < 768px', 'spring-framework'),
						),
						'default' => '991',
						'required' => array('header_enable','=','on')
					),
					array(
						'id' => 'section_header_group_header_desktop',
						'title' => esc_html__('Desktop', 'spring-framework'),
						'type' => 'group',
						'required' => array('header_enable','=','on'),
						'fields' => array(
							array(
                                'id' => 'header_layout',
                                'title' => esc_html__('Header Layout', 'spring-framework'),
                                'type' => 'image_set',
                                'options' => G5P()->settings()->get_header_layout(),
                                'default' => 'header-1',
                            ),
							$this->get_config_group_header_customize('section_header_group_customize_nav', esc_html__('Customize Navigation', 'spring-framework'), 'header_customize_nav', array('search'), array('header_layout', 'in', G5P()->settings()->get_header_customize_nav_required())),
							$this->get_config_group_header_customize('section_header_group_customize_left', esc_html__('Customize Left', 'spring-framework'), 'header_customize_left', array(), array('header_layout', 'in', G5P()->settings()->get_header_customize_left_required())),
							$this->get_config_group_header_customize('section_header_group_customize_right', esc_html__('Customize Right', 'spring-framework'), 'header_customize_right', array(), array('header_layout', 'in', G5P()->settings()->get_header_customize_right_required())),
							$this->get_config_toggle(array(
								'id' => 'header_content_full_width',
								'title' => esc_html__('Header Full Width', 'spring-framework'),
								'subtitle' => esc_html__('Turn On this option if you want to expand the content area to full width', 'spring-framework'),
								'default' => '',
								'required' => array('header_layout','not in', array('header-9','header-10'))
							)),
							$this->get_config_toggle(array(
								'id' => 'header_float_enable',
								'title' => esc_html__('Header Float', 'spring-framework'),
								'subtitle' => esc_html__('Turn On this option if you want to enable header float', 'spring-framework'),
								'default' => '',
								'required' => array('header_layout','not in', array('header-9','header-10'))
							)),
							$this->get_config_toggle(array(
								'id' => 'header_sticky',
								'title' => esc_html__('Header Sticky', 'spring-framework'),
								'default' => 'scroll_up',
                                'options' => array(
                                    '' => esc_html__('Disable', 'spring-framework'),
                                    'always_show' => esc_html__('Always Show', 'spring-framework'),
                                    'scroll_up' => esc_html__('Show On Scroll Up', 'spring-framework')
                                ),
								'required' => array('header_layout','not in', array('header-9','header-10'))
							), true),
							$this->get_config_border_bottom('header_border',array(
								'required' => array('header_layout','not in', array('header-9','header-10'))
							)),
							$this->get_config_border_bottom('header_above_border',array(
								'title' => esc_html__('Header Above Border Bottom', 'spring-framework'),
								'required' =>  array('header_layout','in',array('header-7', 'header-8'))
							)),
							array(
								'id' => 'header_padding',
								'type' => 'spacing',
								'title' => esc_html__('Header Padding', 'spring-framework'),
								'subtitle' => esc_html__('If you would like to override the default header padding, then you can do so here.', 'spring-framework'),
								'required' => array('header_layout','not in', array('header-9','header-10'))
							),

							array(
								'id' => 'section_header_group_navigation',
								'title' => esc_html__('Navigation', 'spring-framework'),
								'type' => 'group',
								'required' => array('header_layout','not in', array('header-5', 'header-9','header-10')),
								'fields' => array(
									array(
										'id' => 'navigation_height',
										'type' => 'dimension',
										'title' => esc_html__('Navigation Height', 'spring-framework'),
										'subtitle' => esc_html__('If you would like to override the default navigation height, then you can do so here.', 'spring-framework'),
										'width' => false,
										'required' => array('header_layout','in',array('header-7','header-8'))
									),
									$this->get_config_spacing('navigation_spacing',array(
										'title' => esc_html__('Navigation Spacing', 'spring-framework'),
                                        'required' => array('header_layout','not in',array('header-5','header-9', 'header-10'))
									)),
                                    array(
                                        'id' => 'menu_item_active_layout',
                                        'title' => esc_html__('Menu Item Active Layout', 'spring-framework'),
                                        'type' => 'image_set',
                                        'options' => G5P()->settings()->get_menu_active_layout(),
                                        'default' => 'menu-active-01',
                                        'required' => array('header_layout','not in',array('header-5','header-9', 'header-10'))
                                    ),
                                    array(
                                        'id' => 'space_between_menu',
                                        'title' => esc_html__('Space Between Menu Text and Menu Line', 'spring-framework'),
                                        'type' => 'button_set',
                                        'options' => array(
                                            'menu-space-short' => esc_html__('Short', 'spring-framework'),
                                            'menu-space-tall' => esc_html__('Tall', 'spring-framework')
                                        ),
                                        'default' => 'menu-space-short',
                                        'required' => array(
                                            array('menu_item_active_layout','not in',array('menu-active-09', 'menu-active-10')),
                                            array('header_layout','not in',array('header-5','header-9', 'header-10'))
                                        )
                                    )
								)
							)
						)
					),

					array(
						'id' => 'section_header_group_header_mobile',
						'title' => esc_html__('Mobile', 'spring-framework'),
						'type' => 'group',
						'required' => array('header_enable','=','on'),
						'fields' => array(
							array(
								'id' => 'mobile_header_layout',
								'title' => esc_html__('Header Layout', 'spring-framework'),
								'type' => 'image_set',
								'options' => G5P()->settings()->get_header_mobile_layout(),
								'default' => 'header-1'
							),
							$this->get_config_toggle(array(
								'id' => 'mobile_header_search_enable',
								'title' => esc_html__('Search Box', 'spring-framework'),
								'subtitle' => esc_html__('Turn On this option if you want to enable search box', 'spring-framework'),
								'default' => '',
							)),
							$this->get_config_toggle(array(
								'id' => 'mobile_header_sticky',
								'title' => esc_html__('Header Sticky', 'spring-framework'),
								'default' => '',
                                'options' => array(
                                    '' => esc_html__('Disable', 'spring-framework'),
                                    'always_show' => esc_html__('Always Show', 'spring-framework'),
                                    'scroll_up' => esc_html__('Show On Scroll Up', 'spring-framework')
                                ),
							),true),
							$this->get_config_group_header_customize('section_mobile_header_group_customize', esc_html__('Customize', 'spring-framework'), 'header_customize_mobile', array('search')),
							$this->get_config_border_bottom('mobile_header_border'),
							array(
								'id' => 'mobile_header_padding',
								'type' => 'spacing',
								'title' => esc_html__('Header Padding', 'spring-framework'),
								'left' => false,
								'right' => false,
								'subtitle' => esc_html__('If you would like to override the default header padding, then you can do so here.', 'spring-framework'),
							),
						)
					)

				)
			);
		}

		/**
		 * Get Config Logo
		 *
		 * @return array
		 */
		public function get_config_section_logo()
		{
			return array(
				'id' => 'section_logo',
				'title' => esc_html__('Logo', 'spring-framework'),
				'icon' => 'dashicons dashicons-image-filter',
				'fields' => array(
					array(
						'id' => 'section_logo_group_desktop',
						'title' => esc_html__('Desktop', 'spring-framework'),
						'type' => 'group',
						'fields' => array(
							array(
								'id' => 'logo',
								'title' => esc_html__('Logo', 'spring-framework'),
								'subtitle' => esc_html__('By default, a text-based logo is created using your site title. But you can also upload an image-based logo here.', 'spring-framework'),
								'type' => 'image',
							),
							array(
								'id' => 'logo_retina',
								'title' => esc_html__('Logo Retina (2x)', 'spring-framework'),
								'subtitle' => esc_html__('If you want to upload a Retina Image, It\'s Image Size should be exactly double in compare with your normal Logo.', 'spring-framework'),
								'type' => 'image',
								'default' => ''
							),
							array(
								'id' => 'sticky_logo',
								'title' => esc_html__('Sticky Logo', 'spring-framework'),
								'type' => 'image',
								'required' => array('header_sticky', '!=', '')
							),
							array(
								'id' => 'sticky_logo_retina',
								'title' => esc_html__('Sticky Logo Retina', 'spring-framework'),
								'subtitle' => esc_html__('If you want to upload a Retina Image, It\'s Image Size should be exactly double in compare with your normal Logo.', 'spring-framework'),
								'type' => 'image',
								'default' => '',
								'required' => array('header_sticky', '!=', '')
							),
							array(
								'id' => 'logo_max_height',
								'title' => esc_html__('Logo Max Height', 'spring-framework'),
								'subtitle' => esc_html__('If you would like to override the default logo max height, then you can do so here.', 'spring-framework'),
								'type' => 'dimension',
								'width' => false
							),
							array(
								'id' => 'logo_padding',
								'title' => esc_html__('Logo Padding', 'spring-framework'),
								'subtitle' => esc_html__('If you would like to override the default logo top/bottom padding, then you can do so here.', 'spring-framework'),
								'type' => 'spacing',
								'left' => false,
								'right' => false,
							)
						)
					),
					array(
						'id' => 'section_logo_group_mobile',
						'title' => esc_html__('Mobile', 'spring-framework'),
						'type' => 'group',
						'fields' => array(
							array(
								'id' => 'mobile_logo',
								'title' => esc_html__('Logo', 'spring-framework'),
								'subtitle' => esc_html__('By default, a text-based logo is created using your site title. But you can also upload an image-based logo here.', 'spring-framework'),
								'type' => 'image',
							),
							array(
								'id' => 'mobile_logo_retina',
								'title' => esc_html__('Logo Retina (2x)', 'spring-framework'),
								'subtitle' => esc_html__('If you want to upload a Retina Image, It\'s Image Size should be exactly double in compare with your normal Logo.', 'spring-framework'),
								'type' => 'image',
								'default' => ''
							),
							array(
								'id' => 'mobile_logo_max_height',
								'title' => esc_html__('Logo Max Height', 'spring-framework'),
								'subtitle' => esc_html__('If you would like to override the default logo max height, then you can do so here.', 'spring-framework'),
								'type' => 'dimension',
								'width' => false
							),
							array(
								'id' => 'mobile_logo_padding',
								'title' => esc_html__('Logo Padding', 'spring-framework'),
								'subtitle' => esc_html__('If you would like to override the default logo top/bottom padding, then you can do so here.', 'spring-framework'),
								'type' => 'spacing',
								'left' => false,
								'right' => false,
							)
						)
					),
				)
			);
		}

		/**
		 * Get Config Page Title
		 *
		 * @return array
		 */
		public function get_config_section_page_title()
		{
			return array(
				'id' => 'section_page_title',
				'title' => esc_html__('Page Title', 'spring-framework'),
				'icon' => 'dashicons dashicons-media-spreadsheet',
				'fields' => array(
					$this->get_config_toggle(array(
						'id' => 'page_title_enable',
						'title' => esc_html__('Page Title Enable', 'spring-framework'),
						'subtitle' => esc_html__('Turn Of this option if you want to disable page title', 'spring-framework'),
						'default' => 'on',
					)),
					$this->get_config_content_block(array(
						'id' => 'page_title_content_block',
						'subtitle' => esc_html__('Specify the Content Block to use as a page title content.', 'spring-framework'),
						'required' => array('page_title_enable', '=', 'on')
					))
				)
			);
		}

		/**
		 * Get Config Footer
		 *
		 * @return array
		 */
		public function get_config_section_footer()
		{
			return array(
				'id' => 'section_footer',
				'title' => esc_html__('Footer', 'spring-framework'),
				'icon' => 'dashicons dashicons-feedback',
				'fields' => array(
					$this->get_config_toggle(array(
						'id' => 'footer_enable',
						'title' => esc_html__('Footer Enable', 'spring-framework'),
						'subtitle' => esc_html__('Turn Off this option if you want to disable footer', 'spring-framework'),
						'default' => 'on'
					)),
                    $this->get_config_content_block(array(
                        'id' => 'footer_content_block',
                        'subtitle' => esc_html__('Specify the Content Block to use as a footer content.', 'spring-framework'),
                        'required' => array('footer_enable', '=', 'on')
                    )),
                    $this->get_config_toggle(array(
                        'id' => 'footer_fixed_enable',
                        'title' => esc_html__('Footer Fixed', 'spring-framework'),
                        'default' => '',
                        'required' => array('footer_enable', '=', 'on'),
                    )),
				)
			);
		}

		/**
		 * Get Config Typography
		 *
		 * @return array
		 */
		public function get_config_section_typography()
		{
			return array(
				'id' => 'section_typography',
				'title' => esc_html__('Typography', 'spring-framework'),
				'icon' => 'dashicons dashicons-editor-textcolor',
				'general_options' => true,
				'fields' => array(
					array(
						'id' => 'section_typography_group_general',
						'title' => esc_html__('General', 'spring-framework'),
						'type' => 'group',
						'fields' => array(
							array(
								'id' => 'body_font',
								'title' => esc_html__('Body Font', 'spring-framework'),
								'subtitle' => esc_html__('Specify the body font.', 'spring-framework'),
								'type' => 'typography',
								'font_size' => true,
								'font_variants' => true,
								'default' => array(
									'font_family' => "Lato",
									'font_size' => '16px',
									'font_weight' => 'regular'
								)
							),
							array(
								'id' => 'primary_font',
								'title' => esc_html__('Primary Font', 'spring-framework'),
								'subtitle' => esc_html__('Specify the primary font family.', 'spring-framework'),
								'type' => 'typography',
								'default' => array(
									'font_family' => "Playfair Display",
								)
							)
						)
					),
                    array(
                        'id' => 'section_typography_group_heading',
                        'title' => esc_html__('Heading Fonts', 'spring-framework'),
                        'type' => 'group',
                        'fields' => array(
                            array(
                                'id' => 'h1_font',
                                'title' => esc_html__('H1 Font', 'spring-framework'),
                                'subtitle' => esc_html__('Specify the h1 font.', 'spring-framework'),
                                'type' => 'typography',
                                'font_size' => true,
                                'font_variants' => true,
                                'default' => array(
                                    'font_family' => "Lato",
                                    'font_size' => '60px',
                                    'font_weight' => '700'
                                )
                            ),
                            array(
                                'id' => 'h2_font',
                                'title' => esc_html__('H2 Font', 'spring-framework'),
                                'subtitle' => esc_html__('Specify the h2 font.', 'spring-framework'),
                                'type' => 'typography',
                                'font_size' => true,
                                'font_variants' => true,
                                'default' => array(
                                    'font_family' => "Lato",
                                    'font_size' => '48px',
                                    'font_weight' => '700'
                                )
                            ),
                            array(
                                'id' => 'h3_font',
                                'title' => esc_html__('H3 Font', 'spring-framework'),
                                'subtitle' => esc_html__('Specify the h3 font.', 'spring-framework'),
                                'type' => 'typography',
                                'font_size' => true,
                                'font_variants' => true,
                                'default' => array(
                                    'font_family' => "Lato",
                                    'font_size' => '30px',
                                    'font_weight' => '700'
                                )
                            ),
                            array(
                                'id' => 'h4_font',
                                'title' => esc_html__('H4 Font', 'spring-framework'),
                                'subtitle' => esc_html__('Specify the h4 font.', 'spring-framework'),
                                'type' => 'typography',
                                'font_size' => true,
                                'font_variants' => true,
                                'default' => array(
                                    'font_family' => "Lato",
                                    'font_size' => '20px',
                                    'font_weight' => '700'
                                )
                            ),
                            array(
                                'id' => 'h5_font',
                                'title' => esc_html__('H5 Font', 'spring-framework'),
                                'subtitle' => esc_html__('Specify the h5 font.', 'spring-framework'),
                                'type' => 'typography',
                                'font_size' => true,
                                'font_variants' => true,
                                'default' => array(
                                    'font_family' => "Lato",
                                    'font_size' => '16px',
                                    'font_weight' => '700'
                                )
                            ),
                            array(
                                'id' => 'h6_font',
                                'title' => esc_html__('H6 Font', 'spring-framework'),
                                'subtitle' => esc_html__('Specify the h6 font.', 'spring-framework'),
                                'type' => 'typography',
                                'font_size' => true,
                                'font_variants' => true,
                                'default' => array(
                                    'font_family' => "Lato",
                                    'font_size' => '14px',
                                    'font_weight' => '700'
                                )
                            )
                        )
                    ),
					array(
						'id' => 'section_typography_group_menu',
						'title' => esc_html__('Menu', 'spring-framework'),
						'type' => 'group',
						'fields' => array(
							array(
								'id' => 'menu_font',
								'title' => esc_html__('Menu Font', 'spring-framework'),
								'subtitle' => esc_html__('Specify the menu font.', 'spring-framework'),
								'type' => 'typography',
								'font_size' => true,
								'font_variants' => true,
								'default' => array(
									'font_family' => "Lato",
									'font_size' => '13px',
									'font_weight' => '700'
								)
							),
							array(
								'id' => 'sub_menu_font',
								'title' => esc_html__('Sub Menu Font', 'spring-framework'),
								'subtitle' => esc_html__('Specify the sub menu font.', 'spring-framework'),
								'type' => 'typography',
								'font_size' => true,
								'font_variants' => true,
								'default' => array(
									'font_family' => "Lato",
									'font_size' => '15px',
									'font_weight' => '700'
								)
							)
						)
					),
				)
			);
		}

		/**
		 * Get Config Color Skin
		 *
		 * @return array
		 */
		public function get_config_section_color_skin()
		{
			return array(
				'id' => 'color_skin',
				'title' => esc_html__('Skin', 'spring-framework'),
				'desc' => esc_html__('Define here all the color skin you will need.', 'spring-framework'),
				'type' => 'panel',
				'sort' => true,
				'toggle_default' => false,
				'default' => G5P()->settings()->get_color_skin_default(),
				'panel_title' => 'skin_name',
				'fields' => array(
					array(
						'id' => 'skin_name',
						'title' => esc_html__('Title', 'spring-framework'),
						'subtitle' => esc_html__('Enter your color skin name', 'spring-framework'),
						'type' => 'text',
					),
					array(
						'id' => 'skin_id',
						'title' => esc_html__('Unique Skin Id', 'spring-framework'),
						'subtitle' => esc_html__('This value is created automatically and it shouldn\'t be edited unless you know what you are doing.', 'spring-framework'),
						'type' => 'text',
						'input_type' => 'unique_id',
						'default' => 'skin-'
					),
					array(
						'id' => 'section_color_skin_row_color_1',
						'type' => 'row',
						'col' => 4,
						'fields' => array(
							array(
								'id' => 'background_color',
								'title' => esc_html__('Background Color', 'spring-framework'),
								'desc' => esc_html__('Specify the background color', 'spring-framework'),
								'type' => 'color',
								'alpha' => true,
								'default' => '#fff',
								'layout' => 'full',
							),
							array(
								'id' => 'text_color',
								'title' => esc_html__('Text Color', 'spring-framework'),
								'desc' => esc_html__('Specify the text color', 'spring-framework'),
								'type' => 'color',
								'alpha' => true,
								'default' => '#7d7d7d',
								'layout' => 'full',
							),
                            array(
                                'id' => 'text_hover_color',
                                'title' => esc_html__('Text hover Color', 'spring-framework'),
                                'desc' => esc_html__('Customize text hover color, set empty to use accent color', 'spring-framework'),
                                'type' => 'color',
                                'alpha' => true,
                                'default' => '',
                                'layout' => 'full',
                            )
						)
					),
					array(
						'id' => 'section_color_skin_row_color_2',
						'type' => 'row',
						'col' => 4,
						'fields' => array(
							array(
								'id' => 'heading_color',
								'title' => esc_html__('Heading Color', 'spring-framework'),
								'desc' => esc_html__('Specify the heading color', 'spring-framework'),
								'type' => 'color',
								'alpha' => true,
								'default' => '#333',
								'layout' => 'full',
							),

							array(
								'id' => 'disable_color',
								'title' => esc_html__('Disable Color', 'spring-framework'),
								'desc' => esc_html__('Specify the disable color', 'spring-framework'),
								'type' => 'color',
								'alpha' => true,
								'default' => '#959595',
								'layout' => 'full',
							),
							array(
								'id' => 'border_color',
								'title' => esc_html__('Border Color', 'spring-framework'),
								'desc' => esc_html__('Specify the border color', 'spring-framework'),
								'type' => 'color',
								'alpha' => true,
								'default' => '#ebebeb',
								'layout' => 'full',
							),
						)
					),
				),
			);
		}

		/**
		 * Get Config Color
		 *
		 * @return array
		 */
		public function get_config_section_colors()
		{
			return array(
				'id' => 'section_colors',
				'title' => esc_html__('Colors', 'spring-framework'),
				'icon' => 'dashicons dashicons-admin-customizer',
				'fields' => array(
					array(
						'id' => 'section_color_group_general',
						'title' => esc_html__('General', 'spring-framework'),
						'type' => 'group',
						'fields' => array(
							array(
								'id' => 'body_background',
								'title' => esc_html__('Body Background', 'spring-framework'),
								'subtitle' => esc_html__('Specify the body background color and media.', 'spring-framework'),
								'type' => 'background',
							),

							array(
								'id' => 'accent_color',
								'title' => esc_html__('Accent Color', 'spring-framework'),
								'subtitle' => esc_html__('Specify the accent color', 'spring-framework'),
								'type' => 'color',
								'default' => '#6ea820',
							),

							array(
								'id' => 'foreground_accent_color',
								'title' => esc_html__('Foreground Accent Color', 'spring-framework'),
								'subtitle' => esc_html__('Specify the foreground accent color', 'spring-framework'),
								'type' => 'color',
								'default' => '#fff',
							),

							array(
								'id' => 'content_skin',
								'title' => esc_html__('Content Skin', 'spring-framework'),
								'subtitle' => esc_html__('Specify the content color skin', 'spring-framework'),
								'type' => 'select',
								'options' => G5P()->settings()->get_color_skin(),
								'default' => 'skin-light'
							),
							array(
								'id' => 'content_background_color',
								'title' => esc_html__('Content Background Color', 'spring-framework'),
								'subtitle' => esc_html__('Specify a custom content background color.', 'spring-framework'),
								'type' => 'color',
								'alpha' => true,
								'default' => ''
							)
						)
					),
					array(
						'id' => 'section_color_group_top_drawer',
						'title' => esc_html__('Top Drawer', 'spring-framework'),
						'type' => 'group',
						'toggle_default' => false,
						'required' => array('top_drawer_mode', '!=', 'hide'),
						'fields' => array(
							array(
								'id' => 'top_drawer_skin',
								'title' => esc_html__('Top Drawer Skin', 'spring-framework'),
								'subtitle' => esc_html__('Specify the top drawer color skin', 'spring-framework'),
								'type' => 'selectize',
								'placeholder' => esc_html__('Select Color Skin', 'spring-framework'),
								'options' => G5P()->settings()->get_color_skin(true),
								'default' => 'skin-dark'
							),
							array(
								'id' => 'top_drawer_background_color',
								'title' => esc_html__('Top Drawer Background Color', 'spring-framework'),
								'subtitle' => esc_html__('Specify a custom top drawer background color.', 'spring-framework'),
								'type' => 'color',
								'alpha' => true,
								'default' => ''
							)
						)
					),
					array(
						'id' => 'section_color_group_header',
						'title' => esc_html__('Header', 'spring-framework'),
						'type' => 'group',
						'toggle_default' => false,
						'fields' => array(
							array(
								'id' => 'header_skin',
								'title' => esc_html__('Header Skin', 'spring-framework'),
								'subtitle' => esc_html__('Specify the header color skin', 'spring-framework'),
								'type' => 'select',
								'options' => G5P()->settings()->get_color_skin(),
								'default' => 'skin-light'
							),
							array(
								'id' => 'header_background_color',
								'title' => esc_html__('Header Background Color', 'spring-framework'),
								'subtitle' => esc_html__('Specify a custom header background color', 'spring-framework'),
								'type' => 'color',
								'alpha' => true,
								'default' => ''
							),
							array(
								'id' => 'header_sticky_skin',
								'title' => esc_html__('Header Sticky Skin', 'spring-framework'),
								'subtitle' => esc_html__('Specify the header sticky color skin', 'spring-framework'),
								'type' => 'select',
								'options' => G5P()->settings()->get_color_skin(),
								'default' => 'skin-light',
								'required' => array(
									array('header_sticky', '!=', ''),
									array('header_layout','not in',array('header-9','header-10'))
								)
							),
							array(
								'id' => 'header_sticky_background_color',
								'title' => esc_html__('Header Sticky Background Color', 'spring-framework'),
								'subtitle' => esc_html__('Specify a custom header sticky background color', 'spring-framework'),
								'type' => 'color',
								'alpha' => true,
								'default' => '',
								'required' => array(
									array('header_sticky', '!=', ''),
									array('header_layout','not in',array('header-9','header-10'))
								)
							),
							array(
								'id' => 'section_color_menu',
								'title' => esc_html__('Menu', 'spring-framework'),
								'type' => 'group',
								'required' => array('header_layout','not in',array('header-9','header-10')),
								'fields' => array(
									array(
										'id' => 'navigation_skin',
										'title' => esc_html__('Navigation Skin', 'spring-framework'),
										'subtitle' => esc_html__('Specify the navigation color skin', 'spring-framework'),
										'type' => 'select',
										'options' => G5P()->settings()->get_color_skin(),
										'default' => 'skin-dark',
										'required' => array('header_layout','in',array('header-7', 'header-8'))
									),
									array(
										'id' => 'navigation_background_color',
										'title' => esc_html__('Navigation Background Color', 'spring-framework'),
										'subtitle' => esc_html__('Specify a custom navigation background color', 'spring-framework'),
										'type' => 'color',
										'alpha' => true,
										'default' => '',
										'required' => array('header_layout','in',array('header-7','header-8'))
									),
									array(
										'id' => 'sub_menu_skin',
										'title' => esc_html__('Sub Menu Skin', 'spring-framework'),
										'type' => 'select',
										'placeholder' => esc_html__('Select Color Skin', 'spring-framework'),
										'options' => G5P()->settings()->get_color_skin(),
										'default' => 'skin-light'
									),
									array(
										'id' => 'sub_menu_background_color',
										'title' => esc_html__('Sub Menu Background Color', 'spring-framework'),
										'subtitle' => esc_html__('Specify a custom sub menu background color', 'spring-framework'),
										'type' => 'color',
										'alpha' => true,
										'default' => ''
									),

								)
							),
							array(
								'id' => 'section_color_canvas_sidebar',
								'title' => esc_html__('Canvas Sidebar', 'spring-framework'),
								'type' => 'group',
								'required' => array('header_customize_nav', 'contain', 'canvas-sidebar'),
								'fields' => array(
									array(
										'id' => 'canvas_sidebar_skin',
										'title' => esc_html__('Canvas Sidebar Skin', 'spring-framework'),
										'subtitle' => esc_html__('Specify the canvas sidebar color skin', 'spring-framework'),
										'type' => 'select',
										'options' => G5P()->settings()->get_color_skin(),
										'default' => 'skin-dark'
									),
									array(
										'id' => 'canvas_sidebar_background_color',
										'title' => esc_html__('Canvas Sidebar Background Color', 'spring-framework'),
										'subtitle' => esc_html__('Specify a custom canvas sidebar background color', 'spring-framework'),
										'type' => 'color',
										'alpha' => true,
										'default' => ''
									)
								)
							),
						)
					),

					array(
						'id' => 'section_color_group_header_mobile',
						'title' => esc_html__('Header Mobile', 'spring-framework'),
						'type' => 'group',
						'toggle_default' => false,
						'fields' => array(
							array(
								'id' => 'mobile_header_skin',
								'title' => esc_html__('Header Mobile Skin', 'spring-framework'),
								'subtitle' => esc_html__('Specify the header mobile color skin', 'spring-framework'),
								'type' => 'select',
								'options' => G5P()->settings()->get_color_skin(),
								'default' => 'skin-light'
							),
							array(
								'id' => 'mobile_header_background_color',
								'title' => esc_html__('Header Mobile Background Color', 'spring-framework'),
								'subtitle' => esc_html__('Specify a custom header mobile background color', 'spring-framework'),
								'type' => 'color',
								'alpha' => true,
								'default' => ''
							),
						)
					),

					array(
						'id' => 'section_color_group_page_title',
						'title' => esc_html__('Page Title', 'spring-framework'),
						'type' => 'group',
						'toggle_default' => false,
						'required' => array('page_title_enable', '=', 'on'),
						'fields' => array(
							array(
								'id' => 'page_title_skin',
								'title' => esc_html__('Page Title Skin', 'spring-framework'),
								'subtitle' => esc_html__('Specify the page title color skin', 'spring-framework'),
								'type' => 'select',
								'options' => G5P()->settings()->get_color_skin(true),
								'default' => '0'
							),
							array(
								'id' => 'page_title_background_color',
								'title' => esc_html__('Page Title Background Color', 'spring-framework'),
								'subtitle' => esc_html__('Specify a custom page title background color', 'spring-framework'),
								'type' => 'color',
								'alpha' => true,
								'default' => ''
							),
						)
					),

                    array(
                        'id' => 'section_color_group_mailchimp_popup',
                        'title' => esc_html__('MailChimp Popup', 'spring-framework'),
                        'type' => 'group',
                        'toggle_default' => false,
                        'required' => array('mailchimp_popup_enable', '=', 'on'),
                        'fields' => array(
                            array(
                                'id' => 'mailchimp_popup_skin',
                                'title' => esc_html__('MailChimp Popup Skin', 'spring-framework'),
                                'subtitle' => esc_html__('Specify the mailchimp popup color skin', 'spring-framework'),
                                'type' => 'selectize',
                                'placeholder' => esc_html__('Select Color Skin', 'spring-framework'),
                                'options' => G5P()->settings()->get_color_skin(true),
                                'default' => 'skin-light'
                            ),
                            array(
                                'id' => 'mailchimp_popup_background_color',
                                'title' => esc_html__('MailChimp Popup Wrap Background Color', 'spring-framework'),
                                'subtitle' => esc_html__('Specify a custom MailChimp Popup Wrap background color.', 'spring-framework'),
                                'type' => 'color',
                                'alpha' => true,
                                'default' => ''
                            )
                        )
                    ),
				)
			);
		}

		/**
		 * Get Config Section Connections
		 *
		 * @return array
		 */
		public function get_config_section_connections()
		{
			return array(
				'id' => 'section_connections',
				'title' => esc_html__('Connections', 'spring-framework'),
				'icon' => 'dashicons dashicons-share',
				'general_options' => true,
				'fields' => array(
					array(
						'id' => 'section_connections_group_social_share',
						'title' => esc_html__('Social Share', 'spring-framework'),
						'type' => 'group',
						'fields' => array(
							array(
								'id' => 'social_share',
								'title' => esc_html__('Social Share', 'spring-framework'),
								'subtitle' => esc_html__('Select active social share links and sort them', 'spring-framework'),
								'type' => 'sortable',
								'options' => G5P()->settings()->get_social_share(),
                                'default' => array(
                                    'facebook'  => 'facebook',
                                    'twitter'   => 'twitter',
                                    'linkedin'  => 'linkedin',
                                    'tumblr'    => 'tumblr',
                                    'pinterest' => 'pinterest'
                                )
							),
						)
					),
					array(
						'id' => 'section_connections_group_social_networks',
						'title' => esc_html__('Social Networks', 'spring-framework'),
						'type' => 'group',
						'fields' => array(
							array(
								'id' => 'social_networks',
								'title' => esc_html__('Social Networks', 'spring-framework'),
								'desc' => esc_html__('Define here all the social networks you will need.', 'spring-framework'),
								'type' => 'panel',
								'toggle_default' => false,
								'default' => G5P()->settings()->get_social_networks_default(),
								'panel_title' => 'social_name',
								'fields' => array(
									array(
										'id' => 'social_name',
										'title' => esc_html__('Title', 'spring-framework'),
										'subtitle' => esc_html__('Enter your social network name', 'spring-framework'),
										'type' => 'text',
									),
									array(
										'id' => 'social_id',
										'title' => esc_html__('Unique Social Id', 'spring-framework'),
										'subtitle' => esc_html__('This value is created automatically and it shouldn\'t be edited unless you know what you are doing.', 'spring-framework'),
										'type' => 'text',
										'input_type' => 'unique_id',
										'default' => 'social-'
									),
									array(
										'id' => 'social_icon',
										'title' => esc_html__('Social Network Icon', 'spring-framework'),
										'subtitle' => esc_html__('Specify the social network icon', 'spring-framework'),
										'type' => 'icon',
									),
									array(
										'id' => 'social_link',
										'title' => esc_html__('Social Network Link', 'spring-framework'),
										'subtitle' => esc_html__('Enter your social network link', 'spring-framework'),
										'type' => 'text',
									),
									array(
										'id' => 'social_color',
										'title' => esc_html__('Social Network Color', 'spring-framework'),
										'subtitle' => sprintf(wp_kses_post(__('Specify the social network color. Reference in <a target="_blank" href="%s">brandcolors.net</a>', 'spring-framework')), 'https://brandcolors.net/'),
										'type' => 'color'
									)
								)
							)
						)
					),
				)
			);
		}

		/**
		 * Get Config Section Blog
		 *
		 * @return array
		 */
		public function get_config_section_blog()
		{
			return array(
				'id' => 'section_blog',
				'title' => esc_html__('Blog', 'spring-framework'),
				'icon' => 'dashicons dashicons-media-text',
				'general_options' => true,
				'fields' => array(
					$this->get_config_section_blog_listing('', '', false, array(
                        $this->get_config_toggle(array(
                            'id'      => 'blog_cate_filter',
                            'title'   => esc_html__('Blog Category Filter', 'spring-framework'),
                            'default' => '',
                            'options' => array(
                                '' => esc_html__('Disable','spring-framework'),
                                'cate-filter-left' => esc_html__('Left','spring-framework'),
                                'cate-filter-center' => esc_html__('Center','spring-framework'),
                                'cate-filter-right' => esc_html__('Right','spring-framework')
                            )
                        ), true),
                        array(
                            'id'     => 'post_default_thumbnail_group',
                            'title'  => esc_html__('Post Default Thumbnail', 'spring-framework'),
                            'type'   => 'group',
                            'fields' => array(
                                $this->get_config_toggle(array(
                                    'id' => 'default_thumbnail_placeholder_enable',
                                    'title' => esc_html__('Enable Default Thumbnail Placeholder', 'spring-framework'),
                                    'desc' => esc_html__('You can set default thumbnail for post that haven\' featured image with enabling this option and uploading default image in following field', 'spring-framework'),
                                    'default' => ''
                                )),
                                array(
                                    'id' => 'default_thumbnail_image',
                                    'type' => 'image',
                                    'title' => esc_html__('Default Thumbnail Image', 'spring-framework'),
                                    'desc' => esc_html__('By default, the post thumbnail will be shown but when the post haven\'nt thumbnail then this will be replaced', 'spring-framework'),
                                    'required' => array('default_thumbnail_placeholder_enable', '=', 'on'),
                                ),
                                $this->get_config_toggle(array(
                                    'id' => 'first_image_as_post_thumbnail',
                                    'title' => esc_html__('First Image as Post Thumbnail', 'spring-framework'),
                                    'desc' => esc_html__('With enabling this options if any post have not thumbnail then theme will shows first content image as post thumbnail.', 'spring-framework'),
                                    'default' => '',
                                ))
                            )
                        )
                    )),
					$this->get_config_section_blog_listing(esc_html__('Search Listing', 'spring-framework'),'search',true),
					$this->get_config_group_single_blog()
				)
			);
		}

		/**
		 * Get Config group single blog
		 *
		 * @return array
		 */
		public function get_config_group_single_blog() {
			return array(
				'id' => 'section_blog_group_single_blog',
				'title' => esc_html__('Single Blog', 'spring-framework'),
				'type' => 'group',
				'fields' => array(
					array(
						'id' => 'single_post_layout',
						'title' => esc_html__('Post Layout', 'spring-framework'),
						'subtitle' => esc_html__('Specify your post layout', 'spring-framework'),
						'type' => 'image_set',
						'options' => G5P()->settings()->get_single_post_layout(),
						'default' => 'layout-1'
					),
                    array(
                        'id' => 'post_single_image_padding',
                        'title' => esc_html__('Single Image Padding', 'spring-framework'),
                        'subtitle' => esc_html__('Set single image padding', 'spring-framework'),
                        'type' => 'spacing',
                        'default' => array('left' => 0, 'right' => 0, 'top' => 0, 'bottom' => 0),
                        'required' => array('single_post_layout', '=', 'layout-5')
                    ),
                    array(
                        'id' => 'post_single_image_mobile_padding',
                        'title' => esc_html__('Single Image Mobile Padding', 'spring-framework'),
                        'subtitle' => esc_html__('Set single image mobile padding', 'spring-framework'),
                        'type' => 'spacing',
                        'default' => array('left' => 0, 'right' => 0, 'top' => 0, 'bottom' => 0),
                        'required' => array('single_post_layout', '=', 'layout-5')
                    ),
                    $this->get_config_toggle(array(
                        'id' => 'single_reading_process_enable',
                        'title' => esc_html__('Reading Process', 'spring-framework'),
                        'subtitle' => esc_html__('Turn Off this option if you want to hide reading process on single blog', 'spring-framework'),
                        'default' => 'on'
                    )),
					$this->get_config_toggle(array(
						'id' => 'single_tag_enable',
						'title' => esc_html__('Tags', 'spring-framework'),
						'subtitle' => esc_html__('Turn Off this option if you want to hide tags on single blog', 'spring-framework'),
						'default' => 'on'
					)),
					$this->get_config_toggle(array(
						'id' => 'single_share_enable',
						'title' => esc_html__('Share', 'spring-framework'),
						'subtitle' => esc_html__('Turn Off this option if you want to hide share on single blog', 'spring-framework'),
						'default' => 'on'
					)),
					$this->get_config_toggle(array(
						'id' => 'single_navigation_enable',
						'title' => esc_html__('Navigation', 'spring-framework'),
						'subtitle' => esc_html__('Turn Off this option if you want to hide navigation on single blog', 'spring-framework'),
						'default' => 'on'
					)),
					$this->get_config_toggle(array(
						'id' => 'single_author_info_enable',
						'title' => esc_html__('Author Info', 'spring-framework'),
						'subtitle' => esc_html__('Turn Off this option if you want to hide author info area on single blog', 'spring-framework'),
						'default' => 'on'
					)),
					array(
						'id' => 'group_single_related_posts',
						'title' => esc_html__('Related Posts', 'spring-framework'),
						'type' => 'group',
						'fields' => array(
							$this->get_config_toggle(array(
								'id' => 'single_related_post_enable',
								'title' => esc_html__('Related Posts', 'spring-framework'),
								'subtitle' => esc_html__('Turn Off this option if you want to hide related posts area on single blog', 'spring-framework'),
								'default' => ''
							)),
							array(
								'id' => 'single_related_post_algorithm',
								'title' => esc_html__('Related Posts Algorithm', 'spring-framework'),
								'subtitle' => esc_html__('Specify the algorithm of related posts', 'spring-framework'),
								'type' => 'select',
								'options' => G5P()->settings()->get_related_post_algorithm(),
								'default' => 'cat',
								'required' => array('single_related_post_enable','=','on')
							),
							$this->get_config_toggle(array(
								'id' => 'single_related_post_carousel_enable',
								'title' => esc_html__('Carousel Mode', 'spring-framework'),
								'subtitle' => esc_html__('Turn On this option if you want to enable carousel mode', 'spring-framework'),
								'default' => 'on',
								'required' => array('single_related_post_enable','=','on')
							)),
							array(
								'id' => 'single_related_post_per_page',
								'title' => esc_html__('Posts Per Page', 'spring-framework'),
								'subtitle' => esc_html__('Enter number of posts per page you want to display', 'spring-framework'),
								'type' => 'text',
								'input_type' => 'number',
								'default' => 6,
								'required' => array('single_related_post_enable','=','on')
							),
							array(
								'id' => 'single_related_post_columns_gutter',
								'title' => esc_html__('Post Columns Gutter', 'spring-framework'),
								'subtitle' => esc_html__('Specify your horizontal space between post.', 'spring-framework'),
								'type' => 'select',
								'options' => G5P()->settings()->get_post_columns_gutter(),
								'default' => '20',
								'required' => array('single_related_post_enable','=','on')
							),
							array(
								'id' => 'single_related_post_columns_group',
								'title' => esc_html__('Post Columns', 'spring-framework'),
								'type' => 'group',
								'required' => array('single_related_post_enable','=','on'),
								'fields' => array(
									array(
										'id' => 'single_related_post_columns_row_1',
										'type' => 'row',
										'col' => 3,
										'fields' => array(
											array(
												'id' => 'single_related_post_columns',
												'title' => esc_html__('Large Devices', 'spring-framework'),
												'desc' => esc_html__('Specify your post columns on large devices (>= 1200px)', 'spring-framework'),
												'type' => 'select',
												'options' => G5P()->settings()->get_post_columns(),
												'default' => '3',
												'layout' => 'full',
											),
											array(
												'id' => 'single_related_post_columns_md',
												'title' => esc_html__('Medium Devices', 'spring-framework'),
												'desc' => esc_html__('Specify your post columns on medium devices (>= 992px)', 'spring-framework'),
												'type' => 'select',
												'options' => G5P()->settings()->get_post_columns(),
												'default' => '3',
												'layout' => 'full',
											),
											array(
												'id' => 'single_related_post_columns_sm',
												'title' => esc_html__('Small Devices', 'spring-framework'),
												'desc' => esc_html__('Specify your post columns on small devices (>= 768px)', 'spring-framework'),
												'type' => 'select',
												'options' => G5P()->settings()->get_post_columns(),
												'default' => '2',
												'layout' => 'full',
											),
											array(
												'id' => 'single_related_post_columns_xs',
												'title' => esc_html__('Extra Small Devices ', 'spring-framework'),
												'desc' => esc_html__('Specify your post columns on extra small devices (< 768px)', 'spring-framework'),
												'type' => 'select',
												'options' => G5P()->settings()->get_post_columns(),
												'default' => '2',
												'layout' => 'full',
											),
                                            array(
                                                'id' => "single_related_post_columns_mb",
                                                'title' => esc_html__('Extra Extra Small Devices ', 'spring-framework'),
                                                'desc' => esc_html__('Specify your post columns on extra extra small devices (< 575px)', 'spring-framework'),
                                                'type' => 'select',
                                                'options' => G5P()->settings()->get_post_columns(),
                                                'default' => '1',
                                                'layout' => 'full',
                                            )
										)
									),
								)
							),
							array(
								'id' => 'single_related_post_paging',
								'title' => esc_html__('Post Paging', 'spring-framework'),
								'subtitle' => esc_html__('Specify your post paging mode', 'spring-framework'),
								'type' => 'select',
								'options' => G5P()->settings()->get_post_paging_small_mode(),
								'default' => 'none',
								'required' => array('single_related_post_enable','=','on')
							),
							array(
								'id' => 'single_related_post_animation',
								'title' => esc_html__('Animation', 'spring-framework'),
								'subtitle' => esc_html__('Specify your post animation', 'spring-framework'),
								'type' => 'select',
								'options' => G5P()->settings()->get_animation(true),
								'default' => '-1',
								'required' => array(
								    array('single_related_post_carousel_enable','!=','on'),
                                    array('single_related_post_enable','=','on')
                                )
							),
						)
					)
				)
			);
		}

		/**
		 * Get Config Section Customize Css & Javascript
		 *
		 * @return array
		 */
		public function get_config_section_code()
		{
			return array(
				'id' => 'section_code',
				'title' => esc_html__('Custom Javascript', 'spring-framework'),
				'icon' => 'dashicons dashicons-editor-code',
				'general_options' => true,
				'fields' => array(
					array(
						'id' => 'custom_js',
						'title' => esc_html__('Custom Javascript', 'spring-framework'),
						'subtitle' => esc_html__('Enter here your custom javascript code. Please do not include any script tags.', 'spring-framework'),
						'type' => 'ace_editor',
						'mode' => 'javascript',
						'theme' => 'monokai',
						'min_line' => 20
					),
				)
			);
		}

		/**
		 * Get Config Content Block
		 *
		 * @param $id
		 * @param array $args
		 * @param bool $inherit
		 * @return array
		 */
		public function get_config_content_block($args = array())
		{
			$defaults =  array(
				'title' => esc_html__('Content Block', 'spring-framework'),
				'placeholder' => esc_html__('Select Content Block', 'spring-framework'),
				'type' => 'selectize',
				'allow_clear' => true,
				'data' => G5P()->cpt()->get_content_block_post_type(),
				'data_args' => array(
					'numberposts' => -1,
				)
			);

			$defaults = wp_parse_args($args,$defaults);
			return $defaults;
		}

		/**
		 * Get Config Sidebar Layout
		 *
		 * @param $id
		 * @param bool $inherit
		 * @param array $args
		 * @return array
		 */
		public function get_config_sidebar_layout($args = array(), $inherit = false)
		{
			$defaults = array(
				'title' => esc_html__('Sidebar Layout', 'spring-framework'),
				'type' => 'image_set',
				'options' => G5P()->settings()->get_sidebar_layout($inherit),
				'default' => $inherit ? '' : 'right'
			);

			$defaults = wp_parse_args($args,$defaults);
			return $defaults;
		}

		/**
		 * Get Config Sidebar
		 *
		 * @param $id
		 * @param array $args
		 * @param $inherit
		 * @return array
		 */
		public function get_config_sidebar($args = array(),$inherit = false)
		{
			$defaults = array(
				'title' => esc_html__('Sidebar', 'spring-framework'),
				'type' => 'selectize',
				'placeholder' => esc_html__('Select Sidebar', 'spring-framework'),
				'data' => 'sidebar',
				'allow_clear' => true,
				'default' => ''
			);

			$defaults = wp_parse_args($args,$defaults);
			return $defaults;
		}



		/**
		 * Get Config Border Bottom
		 *
		 * @param $id
		 * @param array $args
		 * @param bool $inherit
		 * @return array
		 */
		public function get_config_border_bottom($id, $args = array(),$inherit = false)
		{
			$defaults =  array(
				'id' => $id,
				'type' => 'select',
				'title' => esc_html__('Border Bottom', 'spring-framework'),
				'subtitle' => esc_html__('Specify the border bottom mode.', 'spring-framework'),
				'options' => G5P()->settings()->get_border_layout($inherit),
				'default' => $inherit ? '' : 'none'
			);
			$defaults = wp_parse_args($args,$defaults);
			return $defaults;
		}

		/**
		 * Get Config Group Header Customize
		 *
		 * @param $id
		 * @param $title
		 * @param $prefixId
		 * @param array $default
		 * @param array $required
		 * @return array
		 */
		public function get_config_group_header_customize($id, $title, $prefixId, $default = array(), $required = array())
		{
			return array(
				'id' => $id,
				'title' => $title,
				'type' => 'group',
				'toggle_default' => true,
				'required' => $required,
				'fields' => array(
					array(
						'id' => $prefixId,
						'title' => esc_html__('Items', 'spring-framework'),
						'type' => 'sortable',
						'options' => G5P()->settings()->get_header_customize(),
						'default' => $default
					),
					$this->get_config_toggle(array(
					    'id' => "{$prefixId}_separator",
                        'title' => esc_html__('Items separator enable', 'spring-framework'),
                        'default' => '',
                        'required' => array('header_layout','not in',array('header-9','header-10'))
                    )),
                    array(
                        'id' => "{$prefixId}_separator_bg_color",
                        'title' => esc_html__('Items separator background color', 'spring-framework'),
                        'default' => '#e0e0e0',
                        'type' => 'color',
                        'alpha' => true,
                        'required' => array(
                            array('header_layout','not in',array('header-9','header-10')),
                            array("{$prefixId}_separator", '=', 'on')
                        )
                    ),
                    $this->get_config_toggle(array(
                        'id' => "{$prefixId}_search_type",
                        'title' => esc_html__('Search type', 'spring-framework'),
                        'type' => 'button_set',
                        'default' => 'icon',
                        'options' => array(
                            'icon' => esc_html__('Icon', 'spring-framework'),
                            'box' => esc_html__('Box', 'spring-framework')
                        ),
                        'required' => array($prefixId,'contain','search')
                    )),
					$this->get_config_sidebar(array(
						'id' => "{$prefixId}_sidebar",
						'required' => array($prefixId, 'contain', 'sidebar')
					)),
					array(
						'id' => "{$prefixId}_social_networks",
						'title' => esc_html__('Social Networks', 'spring-framework'),
						'type' => 'selectize',
						'multiple' => true,
						'drag' => true,
						'placeholder' => esc_html__('Select Social Networks', 'spring-framework'),
						'options' => G5P()->settings()->get_social_networks(),
						'required' => array($prefixId, 'contain', 'social-networks')
					),
					array(
						'id' => "{$prefixId}_custom_html",
						'title' => esc_html__('Custom Html Content', 'spring-framework'),
						'type' => 'ace_editor',
						'mode' => 'html',
						'required' => array($prefixId, 'contain', 'custom-html')
					),
					$this->get_config_spacing("{$prefixId}_spacing",array(
						'title' => esc_html__('Items Spacing', 'spring-framework'),
						'default' => 15
					)),
					array(
						'id' => "{$prefixId}_custom_css",
						'type' => 'text',
						'title' => esc_html__('Custom Css Class', 'spring-framework'),
						'default' => ''
					)
				)
			);
		}

		/**
		 * Get Config Spacing
		 *
		 * @param $id
		 * @param array $args
		 * @return array
		 */
		public function get_config_spacing($id, $args = array())
		{
			$defaults =  array(
				'id' => $id,
				'type' => 'slider',
				'js_options' => array(
					'step' => 1,
					'min' => 1,
					'max' => 100
				),
				'default' => 30,
			);

			$defaults = wp_parse_args($args,$defaults);
			return $defaults;
		}


		/**
		 * Get Toggle Config
		 *
		 * @param array $args
		 * @param bool $inherit
		 * @return array
		 */
		public function get_config_toggle($args = array(),$inherit = false) {

			if (!$inherit) {
				$defaults = array(
					'type' => 'switch'
				);
			}
			else {
				$defaults = array(
					'type' => 'button_set',
					'options' => G5P()->settings()->get_toggle($inherit),
					'default' => '',
				);
			}
			$defaults = wp_parse_args($args,$defaults);
			return $defaults;
		}

		public function get_config_section_blog_listing($title = '', $prefix = '',$inherit = false, $addition = array()){
			if ($prefix !== '') {
				$prefix = "{$prefix}_";
			}

			if ($title === '') {
				$title = esc_html__('Blog Listing', 'spring-framework');
			}

			$fields = array_merge(array(
				array(
					'id' => "{$prefix}post_layout",
					'title' => esc_html__('Post Layout', 'spring-framework'),
					'subtitle' => esc_html__('Specify your post layout', 'spring-framework'),
					'type' => 'image_set',
					'options' => G5P()->settings()->get_post_layout($inherit),
					'default' => $inherit ? '' : 'large-image',
                    'preset'   => array(
                        array(
                            'op'     => '=',
                            'value'  => 'large-image',
                            'fields' => array(
                                array("{$prefix}posts_per_page", 6),
                                array("{$prefix}post_image_size", '870x515')
                            )
                        ),
                        array(
                            'op'     => '=',
                            'value'  => 'medium-image',
                            'fields' => array(
                                array("{$prefix}posts_per_page", 8),
                                array("{$prefix}post_image_size", '500x350')
                            )
                        ),
                        array(
                            'op'     => '=',
                            'value'  => 'grid',
                            'fields' => array(
                                array("{$prefix}posts_per_page", 9),
                                array("{$prefix}post_image_size", '500x350'),
                                array("{$prefix}post_columns", '3'),
                                array("{$prefix}post_columns_md", '2'),
                                array("{$prefix}post_columns_sm", '2'),
                                array("{$prefix}post_columns_xs", '2'),
                                array("{$prefix}post_columns_mb", '1')
                            )
                        ),
                        array(
                            'op'     => '=',
                            'value'  => 'masonry',
                            'fields' => array(
                                array("{$prefix}posts_per_page", 9),
                                array("{$prefix}post_image_width", '400'),
                                array("{$prefix}post_columns", '3'),
                                array("{$prefix}post_columns_md", '2'),
                                array("{$prefix}post_columns_sm", '2'),
                                array("{$prefix}post_columns_xs", '2'),
                                array("{$prefix}post_columns_mb", '1')
                            )
                        ),
                        array(
                            'op'     => '=',
                            'value'  => 'zigzac',
                            'fields' => array(
                                array("{$prefix}posts_per_page", 8),
                                array("{$prefix}post_image_size", '575x380')
                            )
                        ),
                        array(
                            'op'     => '=',
                            'value'  => 'medium-image-2',
                            'fields' => array(
                                array("{$prefix}posts_per_page", 8),
                                array("{$prefix}post_image_size", '600x400')
                            )
                        ),
                    )
				),
				array(
					'id' => "{$prefix}post_item_skin",
					'title' => esc_html__('Post Item Skin','spring-framework'),
					'type'     => 'image_set',
					'options'  => G5P()->settings()->get_post_item_skin($inherit),
					'default'  => $inherit ? '' : 'post-skin-01',
					'required' => array("{$prefix}post_layout", 'in', array('grid', 'masonry')),
				)
            ),
            $addition,
            array(
                array(
                    'id'     => "{$prefix}post_image_size_group",
                    'title'  => esc_html__('Post Image Size', 'spring-framework'),
                    'type'   => 'group',
                    'fields' => array(
                        array(
                            'id'       => "{$prefix}post_image_size",
                            'title'    => esc_html__('Image size', 'spring-framework'),
                            'subtitle' => esc_html__('Enter your Post image size', 'spring-framework'),
                            'desc'     => esc_html__('Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)).', 'spring-framework'),
                            'type'     => 'text',
                            'default'  => 'large',
                            'required' => array("{$prefix}post_layout", 'not in', array('masonry'))
                        ),
                        array(
                            'id'       => "{$prefix}post_image_ratio",
                            'title'    => esc_html__('Image ratio', 'spring-framework'),
                            'subtitle' => esc_html__('Specify your image post ratio', 'spring-framework'),
                            'type'     => 'select',
                            'options'  => G5P()->settings()->get_image_ratio(),
                            'default'  => '1x1',
                            'required' => array(
                                array("{$prefix}post_layout", 'in', array('grid')),
                                array("{$prefix}post_image_size", '=', 'full')
                            )
                        ),
                        array(
                            'id'       => "{$prefix}post_image_ratio_custom",
                            'title'    => esc_html__('Image ratio custom', 'spring-framework'),
                            'subtitle' => esc_html__('Enter custom image ratio', 'spring-framework'),
                            'type'     => 'dimension',
                            'required' => array(
                                array("{$prefix}post_layout", 'in', array( 'grid')),
                                array("{$prefix}post_image_size", '=', 'full'),
                                array("{$prefix}post_image_ratio", '=', 'custom')
                            )
                        ),
                        array(
                            'id'       => "{$prefix}post_image_width",
                            'title'    => esc_html__('Image Width', 'spring-framework'),
                            'subtitle' => esc_html__('Enter image width', 'spring-framework'),
                            'type'     => 'dimension',
                            'height'   => false,
                            'default'  => array(
                                'width' => '400'
                            ),
                            'required' => array("{$prefix}post_layout", 'in', array('masonry'))
                        )
                    )
                ),
                array(
					'id' => "{$prefix}post_columns_gutter",
					'title' => esc_html__('Post Columns Gutter', 'spring-framework'),
					'subtitle' => esc_html__('Specify your horizontal space between post.', 'spring-framework'),
					'type' => 'select',
					'options' => G5P()->settings()->get_post_columns_gutter($inherit),
					'default' => $inherit ? '-1' : '30',
					'required' => array("{$prefix}post_layout", 'in', array('grid', 'masonry'))
				),
				array(
					'id' => "{$prefix}post_columns_group",
					'title' => esc_html__('Post Columns', 'spring-framework'),
					'type' => 'group',
					'required' => array("{$prefix}post_layout", 'in', array('grid', 'masonry','grid')),
					'fields' => array(
						array(
							'id' => "{$prefix}post_columns_row_1",
							'type' => 'row',
							'col' => 3,
							'fields' => array(
								array(
									'id' => "{$prefix}post_columns",
									'title' => esc_html__('Large Devices', 'spring-framework'),
									'desc' => esc_html__('Specify your post columns on large devices (>= 1200px)', 'spring-framework'),
									'type' => 'select',
									'options' => G5P()->settings()->get_post_columns($inherit),
									'default' => $inherit ? '-1' : '3',
									'layout' => 'full',
								),
								array(
									'id' => "{$prefix}post_columns_md",
									'title' => esc_html__('Medium Devices', 'spring-framework'),
									'desc' => esc_html__('Specify your post columns on medium devices (>= 992px)', 'spring-framework'),
									'type' => 'select',
									'options' => G5P()->settings()->get_post_columns($inherit),
									'default' => $inherit ? '-1' : '2',
									'layout' => 'full',
								),
								array(
									'id' => "{$prefix}post_columns_sm",
									'title' => esc_html__('Small Devices', 'spring-framework'),
									'desc' => esc_html__('Specify your post columns on small devices (>= 768px)', 'spring-framework'),
									'type' => 'select',
									'options' => G5P()->settings()->get_post_columns($inherit),
									'default' => $inherit ? '-1' : '2',
									'layout' => 'full',
								),
								array(
									'id' => "{$prefix}post_columns_xs",
									'title' => esc_html__('Extra Small Devices ', 'spring-framework'),
									'desc' => esc_html__('Specify your post columns on extra small devices (< 768px)', 'spring-framework'),
									'type' => 'select',
									'options' => G5P()->settings()->get_post_columns($inherit),
									'default' => $inherit ? '-1' : '2',
									'layout' => 'full',
								),
                                array(
                                    'id' => "{$prefix}post_columns_mb",
                                    'title' => esc_html__('Extra Extra Small Devices ', 'spring-framework'),
                                    'desc' => esc_html__('Specify your post columns on extra extra small devices (< 576px)', 'spring-framework'),
                                    'type' => 'select',
                                    'options' => G5P()->settings()->get_post_columns($inherit),
                                    'default' => $inherit ? '-1' : '1',
                                    'layout' => 'full',
                                )
							)
						),
					)
				),
				array(
					'id' => "{$prefix}posts_per_page",
					'title' => esc_html__('Posts Per Page', 'spring-framework'),
					'subtitle' => esc_html__('Enter number of posts per page you want to display. Default 10', 'spring-framework'),
					'type' => 'text',
					'input_type' => 'number',
					'default' => $inherit ? '' : 10
				),
				array(
					'id' => "{$prefix}post_paging",
					'title' => esc_html__('Post Paging', 'spring-framework'),
					'subtitle' => esc_html__('Specify your post paging mode', 'spring-framework'),
					'type' => 'select',
					'options' => G5P()->settings()->get_post_paging_mode($inherit),
					'default' => $inherit ? '-1' :  'pagination'
				),
				array(
					'id' => "{$prefix}post_animation",
					'title' => esc_html__('Animation', 'spring-framework'),
					'subtitle' => esc_html__('Specify your post animation', 'spring-framework'),
					'type' => 'select',
					'options' => G5P()->settings()->get_animation($inherit),
					'default' => $inherit ? '-1' : 'none'
				)
			));

			if ($prefix === '') {
				$fields[] = array(
					'id' => 'post_ads',
					'title' => esc_html__('Advertisement', 'spring-framework'),
					'desc' => esc_html__('Define here all the advertisement for listing post you will need.', 'spring-framework'),
					'type' => 'panel',
					'required' => array('post_layout', 'in', array('large-image', 'medium-image')),
					'panel_title' => 'name',
					'fields' => array(
						array(
							'id' => 'name',
							'title' => esc_html__('Title', 'spring-framework'),
							'subtitle' => esc_html__('Enter your advertisement name', 'spring-framework'),
							'type' => 'text',
						),
						array(
							'id' => 'content',
							'title' => esc_html__('Content', 'spring-framework'),
							'subtitle' => esc_html__('Enter your advertisement content', 'spring-framework'),
							'type' => 'editor',
						),
						array(
							'id' => 'position',
							'title' => esc_html__('Position', 'spring-framework'),
							'subtitle' => esc_html__('Enter your advertisement position', 'spring-framework'),
							'desc' => esc_html__('After how many post the ad will display.', 'spring-framework'),
							'type' => 'text',
							'input_type' => 'number'
						),
					)
				);
			}
			if ($prefix === 'search_') {
				$fields[] = array(
					'id' => 'search_post_type',
					'type' => 'checkbox_list',
					'title' => esc_html__('Post Type For Search', 'spring-framework'),
					'options' => G5P()->settings()->get_search_ajax_popup_post_type(),
					'multiple' => true,
					'default' => array('post'),
				);
			}
			$options = array(
				'id' => "{$prefix}section_blog_group_blog_listing",
				'title' => $title,
				'type' => 'group',
				'fields' => $fields
			);
			return $options;
		}

		/**
		 * Get preset config
		 *
		 * @param array $args
		 * @return array
		 */
		public function get_config_preset($args = array()) {
			$defaults = array(
				'title' => esc_html__('Preset', 'spring-framework'),
				'type'  => 'selectize',
				'allow_clear' => true,
				'data' => 'preset',
				'data-option' => G5P()->getOptionName(),
				'create_link' => admin_url('admin.php?page=gsf_options'),
				'edit_link' => admin_url('admin.php?page=gsf_options'),
				'placeholder' => esc_html__('Select Preset', 'spring-framework'),
				'multiple'    => false,
				'desc'        => esc_html__('Optionally you can choose to override the setting that is used on the page', 'spring-framework'),
			);
			return wp_parse_args($args,$defaults);
		}


        public function get_config_section_custom_post_type() {
            return array(
                'id'     => 'section_custom_post_type',
                'title'  => esc_html__('Custom Post Type', 'spring-framework'),
                'icon'   => 'dashicons dashicons-grid-view',
                'general_options' => true,
                'fields' => array(
                    array(
                        'id'       => 'custom_post_type_disable',
                        'type'     => 'checkbox_list',
                        'value_inline' => false,
                        'multiple' => true,
                        'title'    => esc_html__('Disable Custom Post Types', 'spring-framework'),
                        'subtitle' => esc_html__('You can disable the custom post types used within the theme here, by checking the corresponding box. NOTE: If you do not want to disable any, then make sure none of the boxes are checked.','spring-framework'),
                        'options'  => array(
                            'portfolio' => esc_html__('Portfolios', 'spring-framework')
                        )
                    ),
                )
            );
        }
        /**
         * Popup Setting Section
         * *******************************************************
         */
        public function get_config_section_popup()
        {
            return array(
                'id'              => 'section_popup',
                'title'           => esc_html__('Popup Setting', 'spring-framework'),
                'icon'            => 'dashicons dashicons-editor-expand',
                'general_options' => true,
                'fields'          => array(
                    array(
                        'id'     => 'section_popup_mailchimp_group_general',
                        'title'  => esc_html__('Mailchimp Popup', 'spring-framework'),
                        'type'   => 'group',
                        'fields' => array(
                            $this->get_config_toggle(array(
                                'id'      => 'mailchimp_popup_enable',
                                'title'   => esc_html__('MailChimp Popup Enable?', 'spring-framework'),
                                'default' => ''
                            )),

                            $this->get_config_content_block(array(
                                'id' => 'mailchimp_popup_content_block',
                                'subtitle' => esc_html__('Specify the Content Block to use as a MailChimp Popup content.', 'spring-framework'),
                                'required' => array('mailchimp_popup_enable', '=', 'on')
                            )),
                            array(
                                'id' => 'mailchimp_popup_bg',
                                'title' => esc_html__('MailChimp Background', 'spring-framework'),
                                'subtitle' => esc_html__('Specify the Mailchimp background color and media.', 'spring-framework'),
                                'type' => 'background',
                                'required' => array('mailchimp_popup_enable', '=', 'on')
                            ),
                            array(
                                'id'         => 'mailchimp_popup_timeout',
                                'type'       => 'text',
                                'input_type' => 'number',
                                'title'      => esc_html__('Timeout show popup', 'spring-framework'),
                                'subtitle'   => esc_html__('Enter number of timeout show popup when page loaded. Default 500 (milisecond)', 'spring-framework'),
                                'default'    => 500,
                                'required'   => array('mailchimp_popup_enable', '=', 'on')
                            ),
                        ),
                    )
                )
            );
        }

        /**
         * Get Woocommerce config
         */
        public function get_config_section_woocommerce()
        {
            return array(
                'id'     => 'section_woocommerce',
                'title'  => esc_html__('Woocommerce', 'spring-framework'),
                'icon'   => 'dashicons dashicons-cart',
                'general_options' => true,
                'fields' => array(
                    array(
                        'id'     => 'section_woocommerce_group_general',
                        'title'  => esc_html__('General', 'spring-framework'),
                        'type'   => 'group',
                        'fields' => array(
                            $this->get_config_toggle(array(
                                'id'       => 'product_featured_label_enable',
                                'title'    => esc_html__('Show Featured Label', 'spring-framework'),
                                'subtitle' => esc_html__('Turn Off this option if you want to disable featured label', 'spring-framework'),
                                'default'  => 'on'
                            )),
                            array(
                                'id'       => 'product_featured_label_text',
                                'type'     => 'text',
                                'title'    => esc_html__('Featured Label Text', 'spring-framework'),
                                'subtitle' => esc_html__('Enter product featured label text','spring-framework'),
                                'default'  => esc_html__('Hot', 'spring-framework'),
                                'required' => array('product_featured_label_enable', '=', 'on')
                            ),
                            $this->get_config_toggle(array(
                                'id'       => 'product_sale_label_enable',
                                'title'    => esc_html__('Show Sale Label', 'spring-framework'),
                                'subtitle' => esc_html__('Turn Off this option if you want to disable sale label', 'spring-framework'),
                                'default'  => 'on'
                            )),
                            array(
                                'id' => 'product_sale_flash_mode',
                                'title' => esc_html__('Sale Flash Mode','spring-framework'),
                                'type' => 'button_set',
                                'options' => array(
                                    'text' => esc_html__('Text','spring-framework'),
                                    'percent' => esc_html__('Percent','spring-framework')
                                ),
                                'default' => 'text',
                                'required' => array('product_sale_label_enable','=','on')
                            ),
                            array(
                                'id'       => 'product_sale_label_text',
                                'type'     => 'text',
                                'title'    => esc_html__('Sale Label Text', 'spring-framework'),
                                'subtitle' => esc_html__('Enter product sale label text','spring-framework'),
                                'default'  => esc_html__('Sale', 'spring-framework'),
                                'required' => array(
                                    array('product_sale_label_enable', '=', 'on'),
                                    array('product_sale_flash_mode', '=', 'text')
                                )
                            ),
                            $this->get_config_toggle(array(
                                'id'       => 'product_new_label_enable',
                                'title'    => esc_html__('Show New Label', 'spring-framework'),
                                'subtitle' => esc_html__('Turn Off this option if you want to disable new label', 'spring-framework'),
                                'default'  => 'on'
                            )),
                            array(
                                'id'       => 'product_new_label_since',
                                'type'     => 'text',
                                'input_type' => 'number',
                                'title'    => esc_html__('Mark New After Published (Days)', 'spring-framework'),
                                'subtitle' => esc_html__('Enter the number of days after the publication is marked as new','spring-framework'),
                                'default'  => '5',
                                'required' => array('product_new_label_enable', '=', 'on')
                            ),
                            array(
                                'id'       => 'product_new_label_text',
                                'type'     => 'text',
                                'title'    => esc_html__('New Label Text', 'spring-framework'),
                                'subtitle' => esc_html__('Enter product new label text','spring-framework'),
                                'default'  => esc_html__('New', 'spring-framework'),
                                'required' => array('product_new_label_enable', '=', 'on')
                            ),

                            $this->get_config_toggle(array(
                                'id'       => 'product_sale_count_down_enable',
                                'title'    => esc_html__('Show Sale Count Down', 'spring-framework'),
                                'subtitle' => esc_html__('Turn On this option if you want to enable sale count down', 'spring-framework'),
                                'default'  => ''
                            )),

                            $this->get_config_toggle(array(
                                'id'       => 'product_add_to_cart_enable',
                                'title'    => esc_html__('Show Add To Cart Button', 'spring-framework'),
                                'subtitle' => esc_html__('Turn Off this option if you want to disable add to cart button', 'spring-framework'),
                                'default'  => 'on'
                            )),
                            array(
                                'id' => 'shop_cart_empty_text',
                                'title' => esc_html__('Set cart empty text', 'spring-framework'),
                                'default' => esc_html__('No product in the cart.', 'spring-framework'),
                                'type' => 'text'
                            ),
							$this->get_config_toggle(array(
								'id'       => 'product_category_enable',
								'title'    => esc_html__( 'Show Category', 'spring-framework' ),
								'default'  => ''
							)),
							$this->get_config_toggle(array(
								'id'       => 'product_rating_enable',
								'title'    => esc_html__( 'Show Rating', 'spring-framework' ),
								'default'  => ''
							)),
							$this->get_config_toggle(array(
								'id'       => 'product_quick_view_enable',
								'title'    => esc_html__( 'Show Quick View', 'spring-framework' ),
								'default'  => 'on'
							))
                        )),
                    array(
                        'id'     => 'section_woocommerce_group_archive',
                        'title'  => esc_html__('Shop and Category Page', 'spring-framework'),
                        'type'   => 'group',
                        'fields' => array(
                            array(
                                'id' => 'product_catalog_layout',
                                'title' => esc_html__('Layout','spring-framework'),
                                'subtitle' => esc_html__('Specify your product layout','spring-framework'),
                                'type'     => 'image_set',
                                'options'  => G5P()->settings()->get_product_catalog_layout(),
                                'default'  => 'grid',
                                'preset'   => array(
                                    array(
                                        'op'     => '=',
                                        'value'  => 'grid',
                                        'fields' => array(
                                            array('product_per_page', 9),
                                            array('product_columns_gutter', 30)
                                        )
                                    ),
                                    array(
                                        'op'     => '=',
                                        'value'  => 'list',
                                        'fields' => array(
                                            array('product_per_page', 6),
                                            array('product_columns_gutter', 30)
                                        )
                                    ),
                                    array(
                                        'op'     => '=',
                                        'value'  => 'metro-01',
                                        'fields' => array(
                                            array('product_per_page', 8),
                                            array('product_columns_gutter', 10),
                                            array('product_image_size', 'medium'),
                                        )
                                    ),
                                    array(
                                        'op'     => '=',
                                        'value'  => 'metro-02',
                                        'fields' => array(
                                            array('product_per_page', 8),
                                            array('product_columns_gutter', 10),
                                            array('product_image_size', 'medium'),
                                        )
                                    ),
                                    array(
                                        'op'     => '=',
                                        'value'  => 'metro-03',
                                        'fields' => array(
                                            array('product_per_page', 10),
                                            array('product_columns_gutter', 10),
                                            array('product_image_size', 'medium'),
                                        )
                                    ),
                                    array(
                                        'op'     => '=',
                                        'value'  => 'metro-04',
                                        'fields' => array(
                                            array('product_per_page', 8),
                                            array('product_columns_gutter', 10),
                                            array('product_image_size', '400x328'),
                                        )
                                    ),
                                    array(
                                        'op'     => '=',
                                        'value'  => 'metro-05',
                                        'fields' => array(
                                            array('product_per_page', 8),
                                            array('product_columns_gutter', 10),
                                            array('product_image_size', '384x328'),
                                        )
                                    ),
                                    array(
                                        'op'     => '=',
                                        'value'  => 'metro-06',
                                        'fields' => array(
                                            array('product_per_page', 12),
                                            array('product_columns_gutter', 10),
                                            array('product_image_size', '384x328'),
                                        )
                                    ),
									array(
										'op'     => '=',
										'value'  => 'metro-07',
										'fields' => array(
											array('product_per_page', 10),
											array('product_columns_gutter', 10),
											array('product_image_size', '428x428'),
										)
									),
									array(
										'op'     => '=',
										'value'  => 'metro-08',
										'fields' => array(
											array('product_per_page', 5),
											array('product_columns_gutter', 10),
											array('product_image_size', '270x450'),
										)
									),
									array(
										'op'     => '=',
										'value'  => 'metro-09',
										'fields' => array(
											array('product_per_page', 7),
											array('product_columns_gutter', 10),
											array('product_image_size', '270x450'),
										)
									)
                                )
                            ),
							array(
								'id' => 'product_item_skin',
								'title' => esc_html__('Product Item Skin','spring-framework'),
								'type'     => 'image_set',
								'options'  => G5P()->settings()->get_product_item_skin(),
								'default'  => 'product-skin-01',
								'required' => array('product_catalog_layout', '=', 'grid'),
							),
                            array(
                                'id'     => 'product_image_size_group',
                                'title'  => esc_html__('Product Image Size', 'spring-framework'),
                                'type'   => 'group',
                                'required' => array('product_catalog_layout', 'not in', array('grid', 'list')),
                                'fields' => array(
                                    array(
                                        'id'       => 'product_image_size',
                                        'title'    => esc_html__('Image size', 'spring-framework'),
                                        'subtitle' => esc_html__('Enter your product image size', 'spring-framework'),
                                        'desc'     => esc_html__('Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)).', 'spring-framework'),
                                        'type'     => 'text',
                                        'default'  => 'medium'
                                    ),
                                    array(
                                        'id'       => 'product_image_ratio',
                                        'title'    => esc_html__('Image ratio', 'spring-framework'),
                                        'subtitle' => esc_html__('Specify your image product ratio', 'spring-framework'),
                                        'type'     => 'select',
                                        'options'  => G5P()->settings()->get_image_ratio(),
                                        'default'  => '1x1',
                                        'required' => array('product_image_size', '=', 'full')
                                    ),
                                    array(
                                        'id'       => 'product_image_ratio_custom',
                                        'title'    => esc_html__('Image ratio custom', 'spring-framework'),
                                        'subtitle' => esc_html__('Enter custom image ratio', 'spring-framework'),
                                        'type'     => 'dimension',
                                        'required' => array(
                                            array('product_image_size', '=', 'full'),
                                            array('product_image_ratio', '=', 'custom')
                                        )
                                    )
                                )
                            ),
                            $this->get_config_toggle(array(
                                'id'      => 'product_cate_filter',
                                'title'   => esc_html__('Product Category Filter', 'spring-framework'),
                                'default' => '',
                                'options' => array(
                                    '' => esc_html__('Disable','spring-framework'),
                                    'cate-filter-left' => esc_html__('Left','spring-framework'),
                                    'cate-filter-center' => esc_html__('Center','spring-framework'),
                                    'cate-filter-right' => esc_html__('Right','spring-framework')
                                )
                            ), true),
                            array(
                                'id'       => 'product_columns_gutter',
                                'title'    => esc_html__('Product Columns Gutter', 'spring-framework'),
                                'subtitle' => esc_html__('Specify your horizontal space between product.', 'spring-framework'),
                                'type'     => 'select',
                                'options'  => G5P()->settings()->get_post_columns_gutter(),
                                'default'  =>'30',
                                'required' => array('product_catalog_layout', '!=', 'list')
                            ),
                            array(
                                'id'       => 'product_columns_group',
                                'title'    => esc_html__('Product Columns', 'spring-framework'),
                                'type'     => 'group',
                                'required' => array('product_catalog_layout', '=', 'grid'),
                                'fields'   => array(
                                    array(
                                        'id'     => 'product_columns_row_1',
                                        'type'   => 'row',
                                        'col'    => 3,
                                        'fields' => array(
                                            array(
                                                'id'      => 'product_columns',
                                                'title'   => esc_html__('Large Devices', 'spring-framework'),
                                                'desc'    => esc_html__('Specify your product columns on large devices (>= 1200px)', 'spring-framework'),
                                                'type'    => 'select',
                                                'options' => G5P()->settings()->get_post_columns(),
                                                'default' => '3',
                                                'layout'  => 'full',
                                            ),
                                            array(
                                                'id'      => 'product_columns_md',
                                                'title'   => esc_html__('Medium Devices', 'spring-framework'),
                                                'desc'    => esc_html__('Specify your product columns on medium devices (>= 992px)', 'spring-framework'),
                                                'type'    => 'select',
                                                'options' => G5P()->settings()->get_post_columns(),
                                                'default' => '3',
                                                'layout'  => 'full',
                                            ),
                                            array(
                                                'id'      => 'product_columns_sm',
                                                'title'   => esc_html__('Small Devices', 'spring-framework'),
                                                'desc'    => esc_html__('Specify your product columns on small devices (>= 768px)', 'spring-framework'),
                                                'type'    => 'select',
                                                'options' => G5P()->settings()->get_post_columns(),
                                                'default' => '2',
                                                'layout'  => 'full',
                                            ),
                                            array(
                                                'id'      => 'product_columns_xs',
                                                'title'   => esc_html__('Extra Small Devices ', 'spring-framework'),
                                                'desc'    => esc_html__('Specify your product columns on extra small devices (< 768px)', 'spring-framework'),
                                                'type'    => 'select',
                                                'options' => G5P()->settings()->get_post_columns(),
                                                'default' => '1',
                                                'layout'  => 'full',
                                            ),
                                            array(
                                                'id' => "product_columns_mb",
                                                'title' => esc_html__('Extra Extra Small Devices ', 'spring-framework'),
                                                'desc' => esc_html__('Specify your product columns on extra extra small devices (< 576px)', 'spring-framework'),
                                                'type' => 'select',
                                                'options' => G5P()->settings()->get_post_columns(),
                                                'default' => '1',
                                                'layout' => 'full',
                                            )
                                        )
                                    ),
                                )
                            ),
                            array(
                                'id'         => 'product_per_page',
                                'title'      => esc_html__('Products Per Page', 'spring-framework'),
                                'subtitle'   => esc_html__('Enter number of products per page you want to display. Default 9', 'spring-framework'),
                                'type'       => 'text',
                                'default'    => '9',
                                'required' => array("woocommerce_customize[disable]", 'contain','items-show')
                            ),

                            array(
                                'id'       => 'product_paging',
                                'title'    => esc_html__('Product Paging', 'spring-framework'),
                                'subtitle' => esc_html__('Specify your product paging mode', 'spring-framework'),
                                'type'     => 'select',
                                'options'  => G5P()->settings()->get_post_paging_mode(),
                                'default'  => 'pagination'
                            ),
                            array(
                                'id'       => 'product_animation',
                                'title'    => esc_html__('Animation', 'spring-framework'),
                                'subtitle' => esc_html__('Specify your product animation', 'spring-framework'),
                                'type'     => 'select',
                                'options'  => G5P()->settings()->get_animation(),
                                'default'  => 'none'
                            ),
                            array(
                                'id'       => 'product_image_hover_effect',
                                'type'     => 'select',
                                'title'    => esc_html__( 'Product Image Hover Effect', 'spring-framework' ),
                                'subtitle' => esc_html__('Specify your product image hover effect','spring-framework'),
                                'desc'     => '',
                                'options'  => G5P()->settings()->get_product_image_hover_effect(),
                                'default'  => 'change-image'
                            ),
                            array(
                                'type' => 'group',
                                'id' => 'section_woocommerce_group_customize',
                                'title'  => esc_html__('Shop Above Customize', 'spring-framework'),
                                'fields' => array(
                                    array(
                                        'id'       => 'woocommerce_customize',
                                        'title'    => esc_html__('Shop Above Customize Options', 'spring-framework'),
                                        'type'     => 'sorter',
                                        'default'  => array(
                                            'left'  => array(
                                                'result-count'          => esc_html__('Result Count', 'spring-framework')
                                            ),
                                            'right'  => array(
                                                'ordering'     => esc_html__('Ordering', 'spring-framework'),
                                                'switch-layout'  => esc_html__('Switch Layout', 'spring-framework')
                                            ),
                                            'disable' => array(
                                                'items-show' => esc_html__('Items Show', 'spring-framework'),
                                                'sidebar'         => esc_html__('Sidebar', 'spring-framework'),
                                                'filter'  => esc_html__('Filter', 'spring-framework')
                                            )
                                        ),
                                    ),
                                    array(
                                        'id' => 'woocommerce_customize_filter',
                                        'title' => esc_html__('Filter Style', 'spring-framework'),
                                        'type' => 'button_set',
                                        'default' => 'canvas',
                                        'options' => array(
                                            'canvas' => esc_html__('Canvas', 'spring-framework'),
                                            'show-bellow' => esc_html__('Show Bellow', 'spring-framework'),
                                        ),
                                        'required' => array(
                                            array(
                                                array('woocommerce_customize[left]', 'contain', 'filter'),
                                                array('woocommerce_customize[right]', 'contain', 'filter')
                                            )
                                        )
                                    ),
                                    array(
                                        'id'       => 'woocommerce_customize_filter_columns_group',
                                        'title'    => esc_html__('Filter Columns', 'spring-framework'),
                                        'type'     => 'group',
                                        'required' => array( 'woocommerce_customize_filter', '=', 'show-bellow'),
                                        'fields'   => array(
                                            array(
                                                'id'     => 'filter_columns_row_1',
                                                'type'   => 'row',
                                                'col'    => 3,
                                                'fields' => array(
                                                    array(
                                                        'id'      => 'filter_columns',
                                                        'title'   => esc_html__('Large Devices', 'spring-framework'),
                                                        'desc'    => esc_html__('Specify your shop filter columns on large devices (>= 1200px)', 'spring-framework'),
                                                        'type'    => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '4',
                                                        'layout'  => 'full',
                                                    ),
                                                    array(
                                                        'id'      => 'filter_columns_md',
                                                        'title'   => esc_html__('Medium Devices', 'spring-framework'),
                                                        'desc'    => esc_html__('Specify your shop filter columns on medium devices (>= 992px)', 'spring-framework'),
                                                        'type'    => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '3',
                                                        'layout'  => 'full',
                                                    ),
                                                    array(
                                                        'id'      => 'filter_columns_sm',
                                                        'title'   => esc_html__('Small Devices', 'spring-framework'),
                                                        'desc'    => esc_html__('Specify your shop filter columns on small devices (>= 768px)', 'spring-framework'),
                                                        'type'    => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '2',
                                                        'layout'  => 'full',
                                                    ),
                                                    array(
                                                        'id'      => 'filter_columns_xs',
                                                        'title'   => esc_html__('Extra Small Devices ', 'spring-framework'),
                                                        'desc'    => esc_html__('Specify your shop filter columns on extra small devices (< 768px)', 'spring-framework'),
                                                        'type'    => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '2',
                                                        'layout'  => 'full',
                                                    ),
                                                    array(
                                                        'id' => "filter_columns_mb",
                                                        'title' => esc_html__('Extra Extra Small Devices ', 'spring-framework'),
                                                        'desc' => esc_html__('Specify your shop filter columns on extra extra small devices (< 600px)', 'spring-framework'),
                                                        'type' => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '1',
                                                        'layout' => 'full',
                                                    )
                                                )
                                            ),
                                        )
                                    ),
                                    array(
                                        'id' => 'woocommerce_customize_item_show',
                                        'title' => esc_html__('Products per page', 'spring-framework'),
                                        'type' => 'text',
                                        'default' => '6,12,18',
                                        'sub_title' => esc_html__('Input products per page (exp: 6,12,18)','spring-framework'),
                                        'required' => array(
                                            array(
                                                array('woocommerce_customize[left]', 'contain', 'items-show'),
                                                array('woocommerce_customize[right]', 'contain', 'items-show')
                                            )
                                        )
                                    ),
                                    $this->get_config_sidebar(array(
                                        'id' => 'woocommerce_customize_sidebar',
                                        'required' => array(
                                            array(
                                                array('woocommerce_customize[left]', 'contain', 'sidebar'),
                                                array('woocommerce_customize[right]', 'contain', 'sidebar')
                                            )
                                        )
                                    ))
                                )
                            )
                        )
                    ),
                    array(
                        'id'     => 'section_woocommerce_group_single',
                        'title'  => esc_html__('Single Product', 'spring-framework'),
                        'type'   => 'group',
                        'fields' => array(
                            array(
                                'id' => 'product_single_layout',
                                'title' => esc_html__('Layout','spring-framework'),
                                'subtitle' => esc_html__('Specify your product single layout','spring-framework'),
                                'type'     => 'image_set',
                                'options'  => G5P()->settings()->get_product_single_layout(),
                                'default'  => 'layout-04'
                            ),
                            array(
                                'id'       => 'product_related_group',
                                'title'    => esc_html__('Related Products', 'spring-framework'),
                                'type'     => 'group',
                                'fields'   => array(
                                    $this->get_config_toggle(array(
                                        'id'       => 'product_related_enable',
                                        'title'    => esc_html__( 'Show Related Products', 'spring-framework' ),
                                        'default'  => 'on'
                                    )),
                                    array(
                                        'id' => 'product_related_algorithm',
                                        'title' => esc_html__('Related Products Algorithm', 'spring-framework'),
                                        'subtitle' => esc_html__('Specify the algorithm of related products', 'spring-framework'),
                                        'type' => 'select',
                                        'options' => G5P()->settings()->get_related_product_algorithm(),
                                        'default' => 'cat-tag',
                                        'required' => array('product_related_enable','=','on')
                                    ),
									array(
										'id' => 'product_related_item_skin',
										'title' => esc_html__('Product Skin','spring-framework'),
										'subtitle' => esc_html__('Specify your related products skin','spring-framework'),
										'type'     => 'image_set',
										'options'  => G5P()->settings()->get_product_item_skin(),
										'default' => 'product-skin-01',
										'required' => array('product_related_enable','=','on')
									),
                                    $this->get_config_toggle(array(
                                        'id' => 'product_related_carousel_enable',
                                        'title' => esc_html__('Carousel Mode', 'spring-framework'),
                                        'subtitle' => esc_html__('Turn On this option if you want to enable carousel mode', 'spring-framework'),
                                        'default' => 'on',
                                        'required' => array('product_related_enable','=','on')
                                    )),
                                    array(
                                        'id'       => 'product_related_columns_gutter',
                                        'title'    => esc_html__('Product Columns Gutter', 'spring-framework'),
                                        'subtitle' => esc_html__('Specify your horizontal space between product.', 'spring-framework'),
                                        'type'     => 'select',
                                        'options'  => G5P()->settings()->get_post_columns_gutter(),
                                        'default'  =>'30',
                                        'required' => array('product_related_enable', '=', 'on')
                                    ),
                                    array(
                                        'id'       => 'product_related_columns_group',
                                        'title'    => esc_html__('Product Columns', 'spring-framework'),
                                        'type'     => 'group',
                                        'required' => array('product_related_enable', '=', 'on'),
                                        'fields'   => array(
                                            array(
                                                'id'     => 'product_related_columns_row_1',
                                                'type'   => 'row',
                                                'col'    => 3,
                                                'fields' => array(
                                                    array(
                                                        'id'      => 'product_related_columns',
                                                        'title'   => esc_html__('Large Devices', 'spring-framework'),
                                                        'desc'    => esc_html__('Specify your related products columns on large devices (>= 1200px)', 'spring-framework'),
                                                        'type'    => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '4',
                                                        'layout'  => 'full',
                                                    ),
                                                    array(
                                                        'id'      => 'product_related_columns_md',
                                                        'title'   => esc_html__('Medium Devices', 'spring-framework'),
                                                        'desc'    => esc_html__('Specify your related products columns on medium devices (>= 992px)', 'spring-framework'),
                                                        'type'    => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '3',
                                                        'layout'  => 'full',
                                                    ),
                                                    array(
                                                        'id'      => 'product_related_columns_sm',
                                                        'title'   => esc_html__('Small Devices', 'spring-framework'),
                                                        'desc'    => esc_html__('Specify your related products columns on small devices (>= 768px)', 'spring-framework'),
                                                        'type'    => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '2',
                                                        'layout'  => 'full',
                                                    ),
                                                    array(
                                                        'id'      => 'product_related_columns_xs',
                                                        'title'   => esc_html__('Extra Small Devices ', 'spring-framework'),
                                                        'desc'    => esc_html__('Specify your related products columns on extra small devices (< 768px)', 'spring-framework'),
                                                        'type'    => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '1',
                                                        'layout'  => 'full',
                                                    ),
                                                    array(
                                                        'id' => "product_related_columns_mb",
                                                        'title' => esc_html__('Extra Extra Small Devices ', 'spring-framework'),
                                                        'desc' => esc_html__('Specify your related products columns on extra extra small devices (< 576px)', 'spring-framework'),
                                                        'type' => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '1',
                                                        'layout' => 'full',
                                                    )
                                                )
                                            ),
                                        )
                                    ),
                                    array(
                                        'id'         => 'product_related_per_page',
                                        'title'      => esc_html__('Products Per Page', 'spring-framework'),
                                        'subtitle'   => esc_html__('Enter number of products per page you want to display. Default 6', 'spring-framework'),
                                        'type'       => 'text',
                                        'input_type' => 'number',
                                        'default'    => '6',
                                        'required' => array('product_related_enable', '=', 'on')
                                    ),
                                    array(
                                        'id'       => 'product_related_animation',
                                        'title'    => esc_html__('Animation', 'spring-framework'),
                                        'subtitle' => esc_html__('Specify your product animation', 'spring-framework'),
                                        'type'     => 'select',
                                        'options'  => G5P()->settings()->get_animation(true),
                                        'default'  => '',
                                        'required' => array('product_related_enable', '=', 'on')
                                    )
                                )
                            ),
                            array(
                                'id'       => 'product_up_sells_group',
                                'title'    => esc_html__('Product Up Sells', 'spring-framework'),
                                'type'     => 'group',
                                'fields'   => array(
                                    $this->get_config_toggle(array(
                                        'id'       => 'product_up_sells_enable',
                                        'title'    => esc_html__( 'Show Product Up Sells', 'spring-framework' ),
                                        'default'  => 'on'
                                    )),
                                    array(
                                        'id'       => 'product_up_sells_columns_gutter',
                                        'title'    => esc_html__('Product Columns Gutter', 'spring-framework'),
                                        'subtitle' => esc_html__('Specify your horizontal space between product.', 'spring-framework'),
                                        'type'     => 'select',
                                        'options'  => G5P()->settings()->get_post_columns_gutter(),
                                        'default'  =>'30',
                                        'required' => array('product_up_sells_enable', '=', 'on')
                                    ),
									array(
										'id' => 'product_upsell_item_skin',
										'title' => esc_html__('Product Skin','spring-framework'),
										'subtitle' => esc_html__('Specify your related products skin','spring-framework'),
										'type'     => 'image_set',
										'options'  => G5P()->settings()->get_product_item_skin(),
										'default' => 'product-skin-01',
										'required' => array('product_up_sells_enable','=','on')
									),
                                    array(
                                        'id'       => 'product_up_sells_columns_group',
                                        'title'    => esc_html__('Product Columns', 'spring-framework'),
                                        'type'     => 'group',
                                        'required' => array('product_up_sells_enable', '=', 'on'),
                                        'fields'   => array(
                                            array(
                                                'id'     => 'product_related_columns_row_1',
                                                'type'   => 'row',
                                                'col'    => 3,
                                                'fields' => array(
                                                    array(
                                                        'id'      => 'product_up_sells_columns',
                                                        'title'   => esc_html__('Large Devices', 'spring-framework'),
                                                        'desc'    => esc_html__('Specify your product up sells columns on large devices (>= 1200px)', 'spring-framework'),
                                                        'type'    => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '4',
                                                        'layout'  => 'full',
                                                    ),
                                                    array(
                                                        'id'      => 'product_up_sells_columns_md',
                                                        'title'   => esc_html__('Medium Devices', 'spring-framework'),
                                                        'desc'    => esc_html__('Specify your product up sells columns on medium devices (>= 992px)', 'spring-framework'),
                                                        'type'    => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '3',
                                                        'layout'  => 'full',
                                                    ),
                                                    array(
                                                        'id'      => 'product_up_sells_columns_sm',
                                                        'title'   => esc_html__('Small Devices', 'spring-framework'),
                                                        'desc'    => esc_html__('Specify your product up sells columns on small devices (>= 768px)', 'spring-framework'),
                                                        'type'    => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '2',
                                                        'layout'  => 'full',
                                                    ),
                                                    array(
                                                        'id'      => 'product_up_sells_columns_xs',
                                                        'title'   => esc_html__('Extra Small Devices ', 'spring-framework'),
                                                        'desc'    => esc_html__('Specify your product up sells columns on extra small devices (< 768px)', 'spring-framework'),
                                                        'type'    => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '1',
                                                        'layout'  => 'full',
                                                    ),
                                                    array(
                                                        'id' => "product_up_sells_columns_mb",
                                                        'title' => esc_html__('Extra Extra Small Devices ', 'spring-framework'),
                                                        'desc' => esc_html__('Specify your product up sells columns on extra extra small devices (< 576px)', 'spring-framework'),
                                                        'type' => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '1',
                                                        'layout' => 'full',
                                                    )
                                                )
                                            ),
                                        )
                                    ),
                                    array(
                                        'id'         => 'product_up_sells_per_page',
                                        'title'      => esc_html__('Products Per Page', 'spring-framework'),
                                        'subtitle'   => esc_html__('Enter number of products per page you want to display. Default 6', 'spring-framework'),
                                        'type'       => 'text',
                                        'input_type' => 'number',
                                        'default'    => '6',
                                        'required' => array('product_up_sells_enable', '=', 'on')
                                    ),
                                    array(
                                        'id'       => 'product_up_sells_animation',
                                        'title'    => esc_html__('Animation', 'spring-framework'),
                                        'subtitle' => esc_html__('Specify your product animation', 'spring-framework'),
                                        'type'     => 'select',
                                        'options'  => G5P()->settings()->get_animation(true),
                                        'default'  => '',
                                        'required' => array('product_up_sells_enable', '=', 'on')
                                    ),
                                )
                            )
                        )
                    ),
                    array(
                        'id'       => 'product_cart_page_group',
                        'title'    => esc_html__('Cart Page', 'spring-framework'),
                        'type'     => 'group',
                        'fields'   => array(
                            $this->get_config_toggle(array(
                                'id'       => 'product_cross_sells_enable',
                                'title'    => esc_html__( 'Show Product Cross Sells', 'spring-framework' ),
                                'default'  => 'on'
                            )),
							array(
								'id' => 'product_cross_sell_item_skin',
								'title' => esc_html__('Product Cross Sells Skin','spring-framework'),
								'subtitle' => esc_html__('Specify your related products skin','spring-framework'),
								'type'     => 'image_set',
								'options'  => G5P()->settings()->get_product_item_skin(),
								'default' => 'product-skin-01',
								'required' => array('product_cross_sells_enable','=','on')
							),
                            array(
                                'id'       => 'product_cross_sells_columns_gutter',
                                'title'    => esc_html__('Product Cross Sells Columns Gutter', 'spring-framework'),
                                'subtitle' => esc_html__('Specify your horizontal space between product.', 'spring-framework'),
                                'type'     => 'select',
                                'options'  => G5P()->settings()->get_post_columns_gutter(),
                                'default'  =>'30',
                                'required' => array('product_cross_sells_enable', '=', 'on')
                            ),
                            array(
                                'id'       => 'product_cross_sells_columns_group',
                                'title'    => esc_html__('Product Cross Sells Columns', 'spring-framework'),
                                'type'     => 'group',
                                'required' => array('product_cross_sells_enable', '=', 'on'),
                                'fields'   => array(
                                    array(
                                        'id'     => 'product_related_columns_row_1',
                                        'type'   => 'row',
                                        'col'    => 3,
                                        'fields' => array(
                                            array(
                                                'id'      => 'product_cross_sells_columns',
                                                'title'   => esc_html__('Large Devices', 'spring-framework'),
                                                'desc'    => esc_html__('Specify your product cross sells columns on large devices (>= 1200px)', 'spring-framework'),
                                                'type'    => 'select',
                                                'options' => G5P()->settings()->get_post_columns(),
                                                'default' => '4',
                                                'layout'  => 'full',
                                            ),
                                            array(
                                                'id'      => 'product_cross_sells_columns_md',
                                                'title'   => esc_html__('Medium Devices', 'spring-framework'),
                                                'desc'    => esc_html__('Specify your product cross sells columns on medium devices (>= 992px)', 'spring-framework'),
                                                'type'    => 'select',
                                                'options' => G5P()->settings()->get_post_columns(),
                                                'default' => '3',
                                                'layout'  => 'full',
                                            ),
                                            array(
                                                'id'      => 'product_cross_sells_columns_sm',
                                                'title'   => esc_html__('Small Devices', 'spring-framework'),
                                                'desc'    => esc_html__('Specify your product cross sells columns on small devices (>= 768px)', 'spring-framework'),
                                                'type'    => 'select',
                                                'options' => G5P()->settings()->get_post_columns(),
                                                'default' => '2',
                                                'layout'  => 'full',
                                            ),
                                            array(
                                                'id'      => 'product_cross_sells_columns_xs',
                                                'title'   => esc_html__('Extra Small Devices ', 'spring-framework'),
                                                'desc'    => esc_html__('Specify your product cross sells columns on extra small devices (< 768px)', 'spring-framework'),
                                                'type'    => 'select',
                                                'options' => G5P()->settings()->get_post_columns(),
                                                'default' => '1',
                                                'layout'  => 'full',
                                            ),
                                            array(
                                                'id' => "product_cross_sells_columns_mb",
                                                'title' => esc_html__('Extra Extra Small Devices ', 'spring-framework'),
                                                'desc' => esc_html__('Specify your product cross sells columns on extra extra small devices (< 576px)', 'spring-framework'),
                                                'type' => 'select',
                                                'options' => G5P()->settings()->get_post_columns(),
                                                'default' => '1',
                                                'layout' => 'full',
                                            )
                                        )
                                    ),
                                )
                            ),
                            array(
                                'id'         => 'product_cross_sells_per_page',
                                'title'      => esc_html__('Cross Sells Products Per Page', 'spring-framework'),
                                'subtitle'   => esc_html__('Enter number of products per page you want to display. Default 6', 'spring-framework'),
                                'type'       => 'text',
                                'input_type' => 'number',
                                'default'    => '6',
                                'required' => array('product_cross_sells_enable', '=', 'on')
                            ),
                            array(
                                'id'       => 'product_cross_sells_animation',
                                'title'    => esc_html__('Animation', 'spring-framework'),
                                'subtitle' => esc_html__('Specify your product animation', 'spring-framework'),
                                'type'     => 'select',
                                'options'  => G5P()->settings()->get_animation(true),
                                'default'  => '',
                                'required' => array('product_cross_sells_enable', '=', 'on')
                            ),
                        )
                    )
                )
            );
        }

        public function get_config_section_portfolio()
        {
            return array(
                'id'              => 'section_portfolio',
                'title'           => esc_html__('Portfolios', 'spring-framework'),
                'icon'            => 'dashicons dashicons-images-alt2',
                'general_options' => true,
                'fields'          => array(
                    array(
                        'id'     => 'section_portfolio_group_archive',
                        'title'  => esc_html__('Archive and Category', 'spring-framework'),
                        'type'   => 'group',
                        'fields' => array(
                            $this->get_config_toggle(array(
                                'id'      => 'portfolio_cate_filter',
                                'title'   => esc_html__('Portfolio Category Filter', 'spring-framework'),
                                'default' => '',
                                'options' => array(
                                    '' => esc_html__('Disable','spring-framework'),
                                    'cate-filter-left' => esc_html__('Left','spring-framework'),
                                    'cate-filter-center' => esc_html__('Center','spring-framework'),
                                    'cate-filter-right' => esc_html__('Right','spring-framework')
                                )
                            ), true),
                            array(
                                'id'       => 'portfolio_layout',
                                'title'    => esc_html__('Layout', 'spring-framework'),
                                'subtitle' => esc_html__('Specify your portfolio layout', 'spring-framework'),
                                'type'     => 'image_set',
                                'options'  => G5P()->settings()->get_portfolio_layout(),
                                'default'  => 'grid',
                                'preset'   => array(
                                    array(
                                        'op'     => '=',
                                        'value'  => 'grid',
                                        'fields' => array(
                                            array('portfolio_per_page', 9),
                                            array('portfolio_image_size', '480x600'),
                                            array('portfolio_columns_gutter', 10),
                                            array('portfolio_columns', 3),
                                            array('portfolio_columns_md', 3),
                                            array('portfolio_columns_sm', 2),
                                            array('portfolio_columns_xs', 2),
                                            array('portfolio_columns_xs', 1),
                                        )
                                    ),
                                    array(
                                        'op'     => '=',
                                        'value'  => 'masonry',
                                        'fields' => array(
                                            array('portfolio_per_page', 9),
                                            array('portfolio_image_width[width]', 400),
                                            array('portfolio_columns_gutter', 10),
                                            array('portfolio_columns', 3),
                                            array('portfolio_columns_md', 3),
                                            array('portfolio_columns_sm', 2),
                                            array('portfolio_columns_xs', 2),
                                            array('portfolio_columns_xs', 1),
                                        )
                                    ),
                                    array(
                                        'op'     => '=',
                                        'value'  => 'scattered',
                                        'fields' => array(
                                            array('portfolio_per_page', 8)
                                        )
                                    ),
                                    array(
                                        'op'     => '=',
                                        'value'  => 'metro-1',
                                        'fields' => array(
                                            array('portfolio_per_page', 8),
                                            array('portfolio_columns_gutter', 20),
                                            array('portfolio_image_size', '480x480'),
                                        )
                                    ),
                                    array(
                                        'op'     => '=',
                                        'value'  => 'metro-2',
                                        'fields' => array(
                                            array('portfolio_per_page', 8),
                                            array('portfolio_columns_gutter', 20),
                                            array('portfolio_image_size', '480x480'),
                                        )
                                    ),
                                    array(
                                        'op'     => '=',
                                        'value'  => 'metro-3',
                                        'fields' => array(
                                            array('portfolio_per_page', 10),
                                            array('portfolio_columns_gutter', 20),
                                            array('portfolio_image_size', '480x480'),
                                        )
                                    ),
                                    array(
                                        'op'     => '=',
                                        'value'  => 'metro-4',
                                        'fields' => array(
                                            array('portfolio_per_page', 8),
                                            array('portfolio_columns_gutter', 'none'),
                                            array('portfolio_image_size', '480x480'),
                                        )
                                    ),
                                    array(
                                        'op'     => '=',
                                        'value'  => 'metro-5',
                                        'fields' => array(
                                            array('portfolio_per_page', 8),
                                            array('portfolio_columns_gutter', 'none'),
                                            array('portfolio_image_size', '480x480'),
                                        )
                                    ),
                                    array(
                                        'op'     => '=',
                                        'value'  => 'metro-6',
                                        'fields' => array(
                                            array('portfolio_per_page', 13),
                                            array('portfolio_columns_gutter', '20'),
                                            array('portfolio_image_size', '320x320'),
                                        )
                                    ),
                                    array(
                                        'op'     => '=',
                                        'value'  => 'metro-7',
                                        'fields' => array(
                                            array('portfolio_per_page', 11),
                                            array('portfolio_columns_gutter', '20'),
                                            array('portfolio_image_size', '270x310'),
                                        )
                                    ),
                                    array(
                                        'op'     => '=',
                                        'value'  => 'metro-8',
                                        'fields' => array(
                                            array('portfolio_per_page', 5),
                                            array('portfolio_columns_gutter', '20'),
                                            array('portfolio_image_size', '485x370'),
                                        )
                                    ),
                                    array(
                                        'op'     => '=',
                                        'value'  => 'metro-9',
                                        'fields' => array(
                                            array('portfolio_per_page', 4),
                                            array('portfolio_columns_gutter', 'none'),
                                            array('portfolio_image_size', '480x480'),
                                        )
                                    ),
                                    array(
                                        'op'     => '=',
                                        'value'  => 'metro-10',
                                        'fields' => array(
                                            array('portfolio_per_page', 9),
                                            array('portfolio_columns_gutter', '20'),
                                            array('portfolio_image_size', '485x370'),
                                        )
                                    ),
                                    array(
                                        'op'     => '=',
                                        'value'  => 'metro-11',
                                        'fields' => array(
                                            array('portfolio_per_page', 8),
                                            array('portfolio_columns_gutter', '0'),
                                            array('portfolio_image_size', '450x550'),
                                        )
                                    ),
                                    array(
                                        'op'     => '=',
                                        'value'  => 'carousel-3d',
                                        'fields' => array(
                                            array('portfolio_per_page', 9),
                                            array('portfolio_image_size', '804x468'),
                                        )
                                    ),
                                )
                            ),
                            array(
                                'id'       => 'portfolio_item_skin',
                                'title'    => esc_html__('Portfolio Item Skin', 'spring-framework'),
                                'type'     => 'image_set',
                                'options'  => G5P()->settings()->get_portfolio_item_skin(),
                                'desc'     => esc_html__('Skin 01 only apply for Grid Layout and Masonry Layout', 'spring-framework'),
                                'default'  => 'portfolio-item-skin-02'
                            ),
                            array(
                                'id'       => 'portfolio_hover_color_scheme',
                                'title'    => esc_html__('Portfolio Hover Color Scheme', 'spring-framework'),
                                'type'     => 'button_set',
                                'options'  => array(
                                    'portfolio-hover-accent' => esc_html__('Accent', 'spring-framework'),
                                    'portfolio-hover-dark' => esc_html__('Dark', 'spring-framework'),
                                    'portfolio-hover-light' => esc_html__('Light', 'spring-framework'),
                                ),
                                'default'  => 'portfolio-hover-light'
                            ),
                            array(
                                'id'     => 'portfolio_image_size_group',
                                'title'  => esc_html__('Portfolio Image Size', 'spring-framework'),
                                'type'   => 'group',
                                'fields' => array(
                                    array(
                                        'id'       => 'portfolio_image_size',
                                        'title'    => esc_html__('Image size', 'spring-framework'),
                                        'subtitle' => esc_html__('Enter your portfolio image size', 'spring-framework'),
                                        'desc'     => esc_html__('Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)).', 'spring-framework'),
                                        'type'     => 'text',
                                        'default'  => 'medium',
                                        'required' => array('portfolio_layout', 'not in', array('masonry', 'scattered'))
                                    ),
                                    array(
                                        'id'       => 'portfolio_image_ratio',
                                        'title'    => esc_html__('Image ratio', 'spring-framework'),
                                        'subtitle' => esc_html__('Specify your image portfolio ratio', 'spring-framework'),
                                        'type'     => 'select',
                                        'options'  => G5P()->settings()->get_image_ratio(),
                                        'default'  => '1x1',
                                        'required' => array(
                                            array('portfolio_layout', 'not in', array('masonry', 'scattered')),
                                            array('portfolio_image_size', '=', 'full')
                                        )
                                    ),
                                    array(
                                        'id'       => 'portfolio_image_ratio_custom',
                                        'title'    => esc_html__('Image ratio custom', 'spring-framework'),
                                        'subtitle' => esc_html__('Enter custom image ratio', 'spring-framework'),
                                        'type'     => 'dimension',
                                        'required' => array(
                                            array('portfolio_layout', 'not in', array('masonry', 'scattered')),
                                            array('portfolio_image_size', '=', 'full'),
                                            array('portfolio_image_ratio', '=', 'custom')
                                        )
                                    ),
                                    array(
                                        'id'       => 'portfolio_image_width',
                                        'title'    => esc_html__('Image Width', 'spring-framework'),
                                        'subtitle' => esc_html__('Enter image width', 'spring-framework'),
                                        'type'     => 'dimension',
                                        'height'   => false,
                                        'default'  => array(
                                            'width' => '400'
                                        ),
                                        'required' => array('portfolio_layout', 'in', array('masonry'))
                                    )
                                )
                            ),

                            array(
                                'id'       => 'portfolio_columns_gutter',
                                'title'    => esc_html__('Portfolio Columns Gutter', 'spring-framework'),
                                'subtitle' => esc_html__('Specify your horizontal space between portfolio.', 'spring-framework'),
                                'type'     => 'select',
                                'options'  => array(
                                    'none'  => esc_html__('None', 'spring-framework'),
                                    '10' => '10px',
                                    '20' => '20px',
                                    '30' => '30px',
                                    '40' => '40px',
                                    '50' => '50px'
                                ),
                                'default'  => '10',
                                'required' => array('portfolio_layout', 'not in', array('scattered', 'carousel-3d'))
                            ),
                            array(
                                'id'       => 'portfolio_columns_group',
                                'title'    => esc_html__('Portfolio Columns', 'spring-framework'),
                                'type'     => 'group',
                                'required' => array('portfolio_layout', 'in', array('grid', 'masonry')),
                                'fields'   => array(
                                    array(
                                        'id'     => 'portfolio_columns_row_1',
                                        'type'   => 'row',
                                        'col'    => 3,
                                        'fields' => array(
                                            array(
                                                'id'      => 'portfolio_columns',
                                                'title'   => esc_html__('Large Devices', 'spring-framework'),
                                                'desc'    => esc_html__('Specify your portfolio columns on large devices (>= 1200px)', 'spring-framework'),
                                                'type'    => 'select',
                                                'options' => G5P()->settings()->get_post_columns(),
                                                'default' => '3',
                                                'layout'  => 'full',
                                            ),
                                            array(
                                                'id'      => 'portfolio_columns_md',
                                                'title'   => esc_html__('Medium Devices', 'spring-framework'),
                                                'desc'    => esc_html__('Specify your portfolio columns on medium devices (>= 992px)', 'spring-framework'),
                                                'type'    => 'select',
                                                'options' => G5P()->settings()->get_post_columns(),
                                                'default' => '3',
                                                'layout'  => 'full',
                                            ),
                                            array(
                                                'id'      => 'portfolio_columns_sm',
                                                'title'   => esc_html__('Small Devices', 'spring-framework'),
                                                'desc'    => esc_html__('Specify your portfolio columns on small devices (>= 768px)', 'spring-framework'),
                                                'type'    => 'select',
                                                'options' => G5P()->settings()->get_post_columns(),
                                                'default' => '2',
                                                'layout'  => 'full',
                                            ),
                                            array(
                                                'id'      => 'portfolio_columns_xs',
                                                'title'   => esc_html__('Extra Small Devices ', 'spring-framework'),
                                                'desc'    => esc_html__('Specify your portfolio columns on extra small devices (< 768px)', 'spring-framework'),
                                                'type'    => 'select',
                                                'options' => G5P()->settings()->get_post_columns(),
                                                'default' => '2',
                                                'layout'  => 'full',
                                            ),
                                            array(
                                                'id'      => "portfolio_columns_mb",
                                                'title'   => esc_html__('Extra Extra Small Devices ', 'spring-framework'),
                                                'desc'    => esc_html__('Specify your portfolio columns on extra extra small devices (< 576px)', 'spring-framework'),
                                                'type'    => 'select',
                                                'options' => G5P()->settings()->get_post_columns(),
                                                'default' => '1',
                                                'layout'  => 'full',
                                            )
                                        )
                                    ),
                                )
                            ),
                            array(
                                'id'       => 'portfolio_per_page',
                                'title'    => esc_html__('Portfolio Items Per Page', 'spring-framework'),
                                'subtitle' => esc_html__('Controls the number of posts that display per page for portfolio archive pages. Set to -1 to display all. Set to 0 to use the number of posts from Settings > Reading.', 'spring-framework'),
                                'type'     => 'text',
                                'default'  => '9',
                            ),
                            array(
                                'id'       => 'portfolio_paging',
                                'title'    => esc_html__('Portfolio Paging', 'spring-framework'),
                                'subtitle' => esc_html__('Specify your portfolio paging mode', 'spring-framework'),
                                'type'     => 'select',
                                'options'  => G5P()->settings()->get_post_paging_mode(),
                                'default'  => 'load-more'
                            ),
                            array(
                                'id'       => 'portfolio_animation',
                                'title'    => esc_html__('Animation', 'spring-framework'),
                                'subtitle' => esc_html__('Specify your portfolio animation', 'spring-framework'),
                                'type'     => 'select',
                                'options'  => G5P()->settings()->get_animation(),
                                'default'  => 'none'
                            ),
                            array(
                                'id'       => 'portfolio_hover_effect',
                                'type'     => 'select',
                                'title'    => esc_html__('Hover Effect', 'spring-framework'),
                                'subtitle' => esc_html__('Specify your portfolio hover effect', 'spring-framework'),
                                'desc'     => '',
                                'options'  => G5P()->settings()->get_portfolio_hover_effect(),
                                'default'  => 'none'
                            ),
                            array(
                                'id'       => 'portfolio_light_box',
                                'type'     => 'select',
                                'title'    => esc_html__('Light Box', 'spring-framework'),
                                'subtitle' => esc_html__('Specify your portfolio light box', 'spring-framework'),
                                'options'  => array(
                                    'feature' => esc_html__('Feature Image', 'spring-framework'),
                                    'media'   => esc_html__('Media Gallery', 'spring-framework')
                                ),
                                'default'  => 'feature'
                            )
                        )
                    ),
                    array(
                        'id'     => 'section_portfolio_group_details',
                        'title'  => esc_html__('Portfolio Details', 'spring-framework'),
                        'type'   => 'group',
                        'fields' => array(
                            array(
                                'id'             => 'single_portfolio_details',
                                'title'          => esc_html__('Portfolio Details', 'spring-framework'),
                                'desc'           => esc_html__('Define here all the portfolio details you will need.', 'spring-framework'),
                                'type'           => 'panel',
                                'toggle_default' => false,
                                'sort'           => true,
                                'default'        => G5P()->settings()->get_portfolio_details_default(),
                                'panel_title'    => 'title',
                                'fields'         => array(
                                    array(
                                        'id'       => 'title',
                                        'title'    => esc_html__('Title', 'spring-framework'),
                                        'subtitle' => esc_html__('Enter your portfolio details title', 'spring-framework'),
                                        'type'     => 'text',
                                    ),
                                    array(
                                        'id'         => 'id',
                                        'title'      => esc_html__('Unique portfolio details Id', 'spring-framework'),
                                        'subtitle'   => esc_html__('This value is created automatically and it shouldn\'t be edited unless you know what you are doing.', 'spring-framework'),
                                        'type'       => 'text',
                                        'input_type' => 'unique_id',
                                        'default'    => 'portfolio_details_'
                                    ),
                                )
                            ),
                        )
                    ),
                    array(
                        'id'     => 'section_portfolio_group_single',
                        'title'  => esc_html__('Single Portfolio', 'spring-framework'),
                        'type'   => 'group',
                        'fields' => array(
                            array(
                                'id'       => 'single_portfolio_layout',
                                'title'    => esc_html__('Layout', 'spring-framework'),
                                'subtitle' => esc_html__('Specify your single portfolio layout', 'spring-framework'),
                                'type'     => 'image_set',
                                'options'  => G5P()->settings()->get_single_portfolio_layout(),
                                'default'  => 'layout-1'
                            ),
                            array(
                                'id'       => 'single_portfolio_gallery_group',
                                'title'    => esc_html__('Gallery', 'spring-framework'),
                                'type'     => 'group',
                                'required' => array('single_portfolio_layout', '!=', 'layout-5'),
                                'fields'   => array(
                                    array(
                                        'id'       => 'single_portfolio_gallery_layout',
                                        'title'    => esc_html__('Layout', 'spring-framework'),
                                        'subtitle' => esc_html__('Specify your single portfolio gallery layout', 'spring-framework'),
                                        'type'     => 'image_set',
                                        'options'  => G5P()->settings()->get_single_portfolio_gallery_layout(),
                                        'default'  => 'carousel',
                                        'preset' => array(
                                            array(
                                                'op'     => '=',
                                                'value'  => 'carousel',
                                                'fields' => array(
                                                    array('single_portfolio_gallery_image_size', 'full'),
                                                    array('single_portfolio_gallery_image_ratio', '4x3'),
                                                )
                                            ),
                                            array(
                                                'op'     => '=',
                                                'value'  => 'thumbnail',
                                                'fields' => array(
                                                    array('single_portfolio_gallery_image_size', 'full'),
                                                    array('single_portfolio_gallery_image_ratio', '4x3'),
                                                )
                                            ),
                                            array(
                                                'op'     => '=',
                                                'value'  => 'carousel-center',
                                                'fields' => array(
                                                    array('single_portfolio_gallery_image_size', 'full'),
                                                    array('single_portfolio_gallery_image_ratio', '4x3'),
                                                )
                                            ),
                                            array(
                                                'op'     => '=',
                                                'value'  => 'grid',
                                                'fields' => array(
                                                    array('single_portfolio_gallery_image_size', 'medium')
                                                )
                                            ),
                                            array(
                                                'op'     => '=',
                                                'value'  => 'carousel-3d',
                                                'fields' => array(
                                                    array('single_portfolio_gallery_image_size', '804x468')
                                                )
                                            ),
                                            array(
                                                'op'     => '=',
                                                'value'  => 'metro',
                                                'fields' => array(
                                                    array('single_portfolio_gallery_image_size', '370x320')
                                                )
                                            )
                                        )
                                    ),
                                    array(
                                        'id'     => 'single_portfolio_gallery_image_size_group',
                                        'title'  => esc_html__('Image Size', 'spring-framework'),
                                        'type'   => 'group',
                                        'fields' => array(
                                            array(
                                                'id'       => 'single_portfolio_gallery_image_size',
                                                'title'    => esc_html__('Image size', 'spring-framework'),
                                                'subtitle' => esc_html__('Enter your portfolio gallery image size', 'spring-framework'),
                                                'desc'     => esc_html__('Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)).', 'spring-framework'),
                                                'type'     => 'text',
                                                'default'  => 'medium',
                                                'required' => array(
                                                    array('single_portfolio_gallery_layout', '!=', 'masonry')
                                                )
                                            ),
                                            array(
                                                'id'       => 'single_portfolio_gallery_image_ratio',
                                                'title'    => esc_html__('Image ratio', 'spring-framework'),
                                                'subtitle' => esc_html__('Specify your image portfolio gallery ratio', 'spring-framework'),
                                                'type'     => 'select',
                                                'options'  => G5P()->settings()->get_image_ratio(),
                                                'default'  => '1x1',
                                                'required' => array(
                                                    array('single_portfolio_gallery_image_size', '=', 'full'),
                                                    array('single_portfolio_gallery_layout', '!=', 'masonry')
                                                )
                                            ),
                                            array(
                                                'id'       => 'single_portfolio_gallery_image_ratio_custom',
                                                'title'    => esc_html__('Image ratio custom', 'spring-framework'),
                                                'subtitle' => esc_html__('Enter custom image ratio', 'spring-framework'),
                                                'type'     => 'dimension',
                                                'required' => array(
                                                    array('single_portfolio_gallery_layout', '!=', 'masonry'),
                                                    array('single_portfolio_gallery_image_size', '=', 'full'),
                                                    array('single_portfolio_gallery_image_ratio', '=', 'custom')
                                                )
                                            ),
                                            array(
                                                'id'       => 'single_portfolio_gallery_image_width',
                                                'title'    => esc_html__('Image Width', 'spring-framework'),
                                                'subtitle' => esc_html__('Enter image width', 'spring-framework'),
                                                'type'     => 'dimension',
                                                'height'   => false,
                                                'default'  => array(
                                                    'width' => '400'
                                                ),
                                                'required' => array('single_portfolio_gallery_layout', 'in', array('masonry'))
                                            )
                                        )
                                    ),
                                    array(
                                        'id'       => 'single_portfolio_gallery_columns_gutter',
                                        'title'    => esc_html__('Portfolio Gallery Columns Gutter', 'spring-framework'),
                                        'subtitle' => esc_html__('Specify your horizontal space between portfolio gallery.', 'spring-framework'),
                                        'type'     => 'select',
                                        'options'  => G5P()->settings()->get_post_columns_gutter(),
                                        'default'  => '10',
                                        'required' => array('single_portfolio_gallery_layout', 'not in', array('thumbnail', 'carousel-3d'))
                                    ),
                                    array(
                                        'id'       => 'single_portfolio_gallery_columns_group',
                                        'title'    => esc_html__('Portfolio Gallery Columns', 'spring-framework'),
                                        'type'     => 'group',
                                        'required' => array('single_portfolio_gallery_layout', 'not in', array('thumbnail', 'carousel-3d', 'metro')),
                                        'fields'   => array(
                                            array(
                                                'id'     => 'single_portfolio_gallery_columns_row_1',
                                                'type'   => 'row',
                                                'col'    => 3,
                                                'fields' => array(
                                                    array(
                                                        'id'      => 'single_portfolio_gallery_columns',
                                                        'title'   => esc_html__('Large Devices', 'spring-framework'),
                                                        'desc'    => esc_html__('Specify your portfolio gallery columns on large devices (>= 1200px)', 'spring-framework'),
                                                        'type'    => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '3',
                                                        'layout'  => 'full',
                                                    ),
                                                    array(
                                                        'id'      => 'single_portfolio_gallery_columns_md',
                                                        'title'   => esc_html__('Medium Devices', 'spring-framework'),
                                                        'desc'    => esc_html__('Specify your portfolio gallery columns on medium devices (>= 992px)', 'spring-framework'),
                                                        'type'    => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '3',
                                                        'layout'  => 'full',
                                                    ),
                                                    array(
                                                        'id'      => 'single_portfolio_gallery_columns_sm',
                                                        'title'   => esc_html__('Small Devices', 'spring-framework'),
                                                        'desc'    => esc_html__('Specify your portfolio gallery columns on small devices (>= 768px)', 'spring-framework'),
                                                        'type'    => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '2',
                                                        'layout'  => 'full',
                                                    ),
                                                    array(
                                                        'id'      => 'single_portfolio_gallery_columns_xs',
                                                        'title'   => esc_html__('Extra Small Devices ', 'spring-framework'),
                                                        'desc'    => esc_html__('Specify your portfolio gallery columns on extra small devices (< 768px)', 'spring-framework'),
                                                        'type'    => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '2',
                                                        'layout'  => 'full',
                                                    ),
                                                    array(
                                                        'id'      => 'single_portfolio_gallery_columns_mb',
                                                        'title'   => esc_html__('Extra Extra Small Devices ', 'spring-framework'),
                                                        'desc'    => esc_html__('Specify your portfolio gallery columns on extra extra small devices (< 576px)', 'spring-framework'),
                                                        'type'    => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '1',
                                                        'layout'  => 'full',
                                                    )
                                                )
                                            ),
                                        )
                                    ),

                                )

                            ),
                            array(
                                'id' => 'single_portfolio_related_group',
                                'title' => esc_html__('Related Portfolios','spring-framework'),
                                'type' => 'group',
                                'fields' => array(
                                    $this->get_config_toggle(array(
                                        'id' => 'single_portfolio_related_enable',
                                        'title' => esc_html__('Related Portfolios Enable','spring-framework'),
                                        'default' => 'on',
                                        'subtitle' => esc_html__('Turn Off this option if you want to hide related portfolios area on single portfolio','spring-framework')
                                    )),
                                    $this->get_config_toggle(array(
                                        'id' => 'single_portfolio_related_full_width_enable',
                                        'title' => esc_html__('Related Portfolios Full Width','spring-framework'),
                                        'default' => '',
                                        'subtitle' => esc_html__('Turn on this option if you want to related portfolios display full width','spring-framework'),
                                        'required' => array('single_portfolio_related_enable', '=', 'on')
                                    )),
                                    array(
                                        'id'       => 'single_portfolio_related_algorithm',
                                        'title'    => esc_html__('Related Portfolios Algorithm', 'spring-framework'),
                                        'subtitle' => esc_html__('Specify the algorithm of related portfolios', 'spring-framework'),
                                        'type'     => 'select',
                                        'options'  => G5P()->settings()->get_portfolio_related_algorithm(),
                                        'default'  => 'cat',
                                        'required' => array('single_portfolio_related_enable', '=', 'on')
                                    ),
                                    $this->get_config_toggle(array(
                                        'id'       => 'single_portfolio_related_carousel_enable',
                                        'title'    => esc_html__('Carousel Mode', 'spring-framework'),
                                        'subtitle' => esc_html__('Turn Off this option if you want to disable carousel mode', 'spring-framework'),
                                        'default'  => 'on',
                                        'required' => array('single_portfolio_related_enable', '=', 'on')
                                    )),
                                    array(
                                        'id'         => 'single_portfolio_related_per_page',
                                        'title'      => esc_html__('Portfolios Per Page', 'spring-framework'),
                                        'subtitle'   => esc_html__('Enter number of portfolios per page you want to display', 'spring-framework'),
                                        'type'       => 'text',
                                        'input_type' => 'number',
                                        'default'    => 6,
                                        'required'   => array('single_portfolio_related_enable', '=', 'on')
                                    ),
                                    array(
                                        'id'     => 'single_portfolio_related_image_size_group',
                                        'title'  => esc_html__('Related Portfolios Image Size', 'spring-framework'),
                                        'type'   => 'group',
                                        'required' => array('single_portfolio_related_enable', '=', 'on'),
                                        'fields' => array(
                                            array(
                                                'id'       => 'single_portfolio_related_image_size',
                                                'title'    => esc_html__('Image size', 'spring-framework'),
                                                'subtitle' => esc_html__('Enter your related portfolios image size', 'spring-framework'),
                                                'desc'     => esc_html__('Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)).', 'spring-framework'),
                                                'type'     => 'text',
                                                'default'  => 'medium',
                                            ),
                                            array(
                                                'id'       => 'single_portfolio_related_image_ratio',
                                                'title'    => esc_html__('Image ratio', 'spring-framework'),
                                                'subtitle' => esc_html__('Specify your related portfolios image ratio', 'spring-framework'),
                                                'type'     => 'select',
                                                'options'  => G5P()->settings()->get_image_ratio(),
                                                'default'  => '1x1',
                                                'required' => array('single_portfolio_related_image_size', '=', 'full')
                                            ),
                                            array(
                                                'id'       => 'single_portfolio_related_image_ratio_custom',
                                                'title'    => esc_html__('Image ratio custom', 'spring-framework'),
                                                'subtitle' => esc_html__('Enter custom image ratio', 'spring-framework'),
                                                'type'     => 'dimension',
                                                'required' => array(
                                                    array('single_portfolio_related_image_size', '=', 'full'),
                                                    array('single_portfolio_related_image_ratio', '=', 'custom')
                                                )
                                            ),
                                        )
                                    ),
                                    array(
                                        'id'       => 'single_portfolio_related_columns_gutter',
                                        'title'    => esc_html__('Related Portfolios Columns Gutter', 'spring-framework'),
                                        'subtitle' => esc_html__('Specify your horizontal space between portfolios related.', 'spring-framework'),
                                        'type'     => 'select',
                                        'options'  => G5P()->settings()->get_post_columns_gutter(),
                                        'default'  => '30',
                                        'required' => array('single_portfolio_related_enable', '=', 'on')
                                    ),
                                    array(
                                        'id'       => 'single_portfolio_related_columns_group',
                                        'title'    => esc_html__('Related Portfolios Columns', 'spring-framework'),
                                        'type'     => 'group',
                                        'required' => array('single_portfolio_related_enable', '=', 'on'),
                                        'fields'   => array(
                                            array(
                                                'id'     => 'single_portfolio_related_columns_row_1',
                                                'type'   => 'row',
                                                'col'    => 3,
                                                'fields' => array(
                                                    array(
                                                        'id'      => 'single_portfolio_related_columns',
                                                        'title'   => esc_html__('Large Devices', 'spring-framework'),
                                                        'desc'    => esc_html__('Specify your related portfolios columns on large devices (>= 1200px)', 'spring-framework'),
                                                        'type'    => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '3',
                                                        'layout'  => 'full',
                                                    ),
                                                    array(
                                                        'id'      => 'single_portfolio_related_columns_md',
                                                        'title'   => esc_html__('Medium Devices', 'spring-framework'),
                                                        'desc'    => esc_html__('Specify your portfolios related columns on medium devices (>= 992px)', 'spring-framework'),
                                                        'type'    => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '3',
                                                        'layout'  => 'full',
                                                    ),
                                                    array(
                                                        'id'      => 'single_portfolio_related_columns_sm',
                                                        'title'   => esc_html__('Small Devices', 'spring-framework'),
                                                        'desc'    => esc_html__('Specify your related portfolios columns on small devices (>= 768px)', 'spring-framework'),
                                                        'type'    => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '2',
                                                        'layout'  => 'full',
                                                    ),
                                                    array(
                                                        'id'      => 'single_portfolio_related_columns_xs',
                                                        'title'   => esc_html__('Extra Small Devices ', 'spring-framework'),
                                                        'desc'    => esc_html__('Specify your related portfolios columns on extra small devices (< 768px)', 'spring-framework'),
                                                        'type'    => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '2',
                                                        'layout'  => 'full',
                                                    ),
                                                    array(
                                                        'id'      => 'single_portfolio_related_columns_mb',
                                                        'title'   => esc_html__('Extra Extra Small Devices ', 'spring-framework'),
                                                        'desc'    => esc_html__('Specify your related portfolios columns on extra extra small devices (< 576px)', 'spring-framework'),
                                                        'type'    => 'select',
                                                        'options' => G5P()->settings()->get_post_columns(),
                                                        'default' => '1',
                                                        'layout'  => 'full',
                                                    )
                                                )
                                            ),
                                        )
                                    ),
                                    array(
                                        'id'       => 'single_portfolio_related_post_paging',
                                        'title'    => esc_html__('Portfolios Paging', 'spring-framework'),
                                        'subtitle' => esc_html__('Specify your portfolios paging mode', 'spring-framework'),
                                        'type'     => 'select',
                                        'options'  => G5P()->settings()->get_post_paging_small_mode(),
                                        'default'  => 'none',
                                        'required' => array(
                                            array('single_portfolio_related_carousel_enable', '!=' ,'on'),
                                            array('single_portfolio_related_enable', '=', 'on')
                                        )
                                    ),
                                    array(
                                        'id'       => 'single_portfolio_related_animation',
                                        'title'    => esc_html__('Animation', 'spring-framework'),
                                        'subtitle' => esc_html__('Specify your portfolios animation', 'spring-framework'),
                                        'type'     => 'select',
                                        'options'  => G5P()->settings()->get_animation(),
                                        'default'  => 'none',
                                        'required' => array('single_portfolio_related_enable', '=', 'on')
                                    )
                                )
                            )
                        )
                    )
                )
            );
        }
	}
}