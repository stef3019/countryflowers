<?php
if (!defined('ABSPATH')) {
	exit('Direct script access denied.');
}
if (!class_exists(' G5P_Inc_Settings')) {
	class  G5P_Inc_Settings
	{
		private static $_instance;

		public static function getInstance()
		{
			if (self::$_instance == NULL) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Get Main Layout
		 *
		 * @param bool $default
		 * @return mixed|void
		 */
		public function get_main_layout($default = false)
		{
			$defaults = array();
			if ($default) {
				$defaults[''] = array(
					'label' => esc_html__('Inherit', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/default.png'),
				);
			}
			$config = apply_filters('gsf_options_main_layout', array(
				'wide'     => array(
					'label' => esc_html__('Wide', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/layout-wide.png'),
				),
				'boxed'    => array(
					'label' => esc_html__('Boxed', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/layout-boxed.png'),
				),
				'framed'   => array(
					'label' => esc_html__('Framed', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/layout-framed.png'),
				),
				'bordered' => array(
					'label' => esc_html__('Bordered', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/layout-bordered.png'),
				)
			));

			$config = wp_parse_args($config, $defaults);
			return $config;
		}

		/**
		 * Get Sidebar Layout
		 *
		 * @param bool $inherit
		 * @return mixed|void
		 */
		public function get_sidebar_layout($inherit = false)
		{
			$config = apply_filters('gsf_options_sidebar_layout', array(
				'none'  => array(
					'label' => esc_html__('No Sidebar', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/sidebar-none.png'),
				),
				'left'  => array(
					'label' => esc_html__('Left Sidebar', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/sidebar-left.png'),
				),
				'right' => array(
					'label' => esc_html__('Right Sidebar', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/sidebar-right.png'),
				)
			));

			if ($inherit) {
				$config = array(
						'' => array(
							'label' => esc_html__('Inherit', 'spring-framework'),
							'img'   => G5P()->pluginUrl('assets/images/theme-options/default.png'),
						)
					) + $config;
			}
			return $config;
		}

		/**
		 * Get Sidebar Width
		 *
		 * @param bool $inherit
		 * @return array|mixed|void
		 */
		public function get_sidebar_width($inherit = false)
		{
			$config = apply_filters('gsf_options_sidebar_width', array(
				'small' => esc_html__('Small (1/4)', 'spring-framework'),
				'large' => esc_html__('Large (1/3)', 'spring-framework')
			));
			if ($inherit) {
				$config = array(
						'' => esc_html__('Inherit', 'spring-framework')
					) + $config;
			}
			return $config;
		}

		/**
		 * Get Toggle
		 *
		 * @param bool $inherit
		 * @return array
		 */
		public function get_toggle($inherit = false)
		{
			$config = array(
				'on'  => esc_html__('On', 'spring-framework'),
				'off' => esc_html__('Off', 'spring-framework')
			);

			if ($inherit) {
				$config = array('' => esc_html__('Inherit', 'spring-framework')) + $config;
			}
			return $config;
		}

		/**
		 * Get Header Customize Nav Required
		 *
		 * @return array
		 */
		public function get_header_customize_nav_required()
		{
			return apply_filters('gsf_options_header_customize_nav_required', array('header-1', 'header-2', 'header-3', 'header-9', 'header-10'));
		}

		/**
		 * Get Header Customize Left Required
		 *
		 * @return array
		 */
		public function get_header_customize_left_required()
		{
			return apply_filters('gsf_options_header_customize_nav_required', array('header-4', 'header-7'));
		}

		/**
		 * Get Header Customize Right Required
		 *
		 * @return array
		 */
		public function get_header_customize_right_required()
		{
			return apply_filters('gsf_options_header_customize_nav_required', array('header-4', 'header-5', 'header-6', 'header-7', 'header-8'));
		}

		/**
		 * Get Search Ajax Post Type
		 *
		 * @return array
		 */
		public function get_search_ajax_popup_post_type()
		{

			$output = array(
				'post' => esc_html__('Post', 'spring-framework'),
				'page' => esc_html__('Page', 'spring-framework'),
			);

			if (class_exists('WooCommerce')) {
				$output['product'] = esc_html__('Product', 'spring-framework');
			}


			return apply_filters('gsf_options_get_search_popup_ajax_post_type', $output);
		}

		/**
		 * Get Maintenance Mode
		 *
		 * @return array
		 */
		public function get_maintenance_mode()
		{
			return apply_filters('gsf_options_maintenance_mode', array(
				'2' => 'On (Custom Page)',
				'1' => 'On (Standard)',
				'0' => 'Off',
			));
		}

		public function get_widget_title_style($inherit = false) {
		    $style = array(
                'title-default'     => array(
                    'label' => esc_html__('Style 01', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/widget-title-02.png'),
                ),
                'left-title'    => array(
                    'label' => esc_html__('Style 02', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/widget-title-01.png'),
                )
            );
            if ($inherit) {
                $style = array(
                        '' => array(
                            'label' => esc_html__('Inherit', 'spring-framework'),
                            'img'   => G5P()->pluginUrl('assets/images/theme-options/default.png'),
                        ),
                    ) + $style;
            }
            return apply_filters('gsf_widget_title_style', $style);
        }

		/**
		 * Get Header Layout
		 *
		 * @return array
		 */
		public function get_header_layout()
		{
			return apply_filters('gsf_options_header_layout', array(
				'header-1' => array(
					'img'   => G5P()->pluginUrl('assets/images/theme-options/header-1.jpg'),
					'label' => esc_html__('Header 1', 'spring-framework')
				),
				'header-2' => array(
					'img'   => G5P()->pluginUrl('assets/images/theme-options/header-2.jpg'),
					'label' => esc_html__('Header 2', 'spring-framework')
				),
				'header-3' => array(
					'img'   => G5P()->pluginUrl('assets/images/theme-options/header-3.jpg'),
					'label' => esc_html__('Header 3', 'spring-framework')
				),
				'header-4' => array(
					'img'   => G5P()->pluginUrl('assets/images/theme-options/header-4.jpg'),
					'label' => esc_html__('Header 4', 'spring-framework')
				),
				'header-5' => array(
					'img'   => G5P()->pluginUrl('assets/images/theme-options/header-5.png'),
					'label' => esc_html__('Header 5', 'spring-framework')
				),
				'header-6' => array(
					'img'   => G5P()->pluginUrl('assets/images/theme-options/header-6.png'),
					'label' => esc_html__('Header 6', 'spring-framework')
				),
				'header-7' => array(
					'img'   => G5P()->pluginUrl('assets/images/theme-options/header-7.jpg'),
					'label' => esc_html__('Header 7', 'spring-framework')
				),
				'header-8' => array(
					'img'   => G5P()->pluginUrl('assets/images/theme-options/header-8.png'),
					'label' => esc_html__('Header 8', 'spring-framework')
				),
                'header-9' => array(
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/header-9.jpg'),
                    'label' => esc_html__('Header 9', 'spring-framework')
                ),
                'header-10' => array(
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/header-10.jpg'),
                    'label' => esc_html__('Header 10', 'spring-framework')
                ),
			));
		}

        public function get_menu_active_layout()
        {
            return apply_filters('gsf_options_menu_active_layout', array(
                'menu-active-01' => array(
                    'img' => G5P()->pluginUrl('assets/images/theme-options/menu-active-01.png'),
                    'label' => esc_html__('Layout 01', 'spring-framework')
                ),
                'menu-active-02' => array(
                    'img' => G5P()->pluginUrl('assets/images/theme-options/menu-active-02.png'),
                    'label' => esc_html__('Layout 02', 'spring-framework')
                ),
                'menu-active-03' => array(
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/menu-active-03.png'),
                    'label' => esc_html__('Layout 03', 'spring-framework')
                ),
                'menu-active-04' => array(
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/menu-active-04.png'),
                    'label' => esc_html__('Layout 04', 'spring-framework')
                ),
                'menu-active-05' => array(
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/menu-active-05.png'),
                    'label' => esc_html__('Layout 05', 'spring-framework')
                ),
                'menu-active-06' => array(
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/menu-active-06.png'),
                    'label' => esc_html__('Layout 06', 'spring-framework')
                ),
                'menu-active-07' => array(
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/menu-active-07.png'),
                    'label' => esc_html__('Layout 07', 'spring-framework')
                ),
                'menu-active-08' => array(
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/menu-active-08.png'),
                    'label' => esc_html__('Layout 08', 'spring-framework')
                ),
                'menu-active-09' => array(
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/menu-active-09.png'),
                    'label' => esc_html__('Layout 09', 'spring-framework')
                ),
                'menu-active-10' => array(
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/menu-active-10.png'),
                    'label' => esc_html__('Layout 10', 'spring-framework')
                )
            ));
        }

		/**
		 * Get Header Customize
		 *
		 * @return array
		 */
		public function get_header_customize()
		{
			$settings = array(
				'search'          => esc_html__('Search', 'spring-framework'),
				'social-networks' => esc_html__('Social Networks', 'spring-framework'),
				'sidebar'         => esc_html__('Sidebar', 'spring-framework'),
				'custom-html'     => esc_html__('Custom Html', 'spring-framework'),
				'canvas-sidebar'  => esc_html__('Canvas Sidebar', 'spring-framework')
			);
			if (class_exists('WooCommerce')) {
				$settings['shopping-cart'] = esc_html__('Shopping Cart', 'spring-framework');
                $settings['product-search-ajax'] = esc_html__('Product Search Ajax', 'spring-framework');
			} else {
			    unset($settings['shopping-cart']);
                unset($settings['product-search-ajax']);
            }
			return apply_filters('gsf_options_header_customize', $settings);
		}

		/**
		 * Get Header Mobile Layout
		 *
		 * @return array
		 */
		public function get_header_mobile_layout()
		{
			return apply_filters('gsf_options_header_mobile_layout', array(
				'header-1' => array(
					'img'   => G5P()->pluginUrl('assets/images/theme-options/header-mobile-layout-1.png'),
					'label' => esc_html__('Layout 1', 'spring-framework')
				),
				'header-2' => array(
					'img'   => G5P()->pluginUrl('assets/images/theme-options/header-mobile-layout-2.png'),
					'label' => esc_html__('Layout 2', 'spring-framework')
				),
				'header-3' => array(
					'img'   => G5P()->pluginUrl('assets/images/theme-options/header-mobile-layout-3.png'),
					'label' => esc_html__('Layout 3', 'spring-framework')
				)
			));
		}




		/**
		 * Get Bottom Bar Layout
		 *
		 * @param bool $inherit
		 * @return array|mixed|void
		 */
		public function get_border_layout($inherit = false)
		{
			$config = apply_filters('gsf_options_border_layout', array(
				'none'      => esc_html__('None', 'spring-framework'),
				'full'      => esc_html__('Full', 'spring-framework'),
				'container' => esc_html__('Container', 'spring-framework')
			));

			if ($inherit) {
				$config = array(
						'' => esc_html__('Inherit', 'spring-framework')
					) + $config;
			}
			return $config;
		}

		/**
		 * Get Loading Animation
		 *
		 * @return array
		 */
		public function get_loading_animation()
		{
			return apply_filters('gsf_options_loading_animation', array(
				''              => esc_html__('None','spring-framework'),
				'chasing-dots'  => esc_html__('Chasing Dots', 'spring-framework'),
				'circle'        => esc_html__('Circle', 'spring-framework'),
				'cube'          => esc_html__('Cube', 'spring-framework'),
				'double-bounce' => esc_html__('Double Bounce', 'spring-framework'),
				'fading-circle' => esc_html__('Fading Circle', 'spring-framework'),
				'folding-cube'  => esc_html__('Folding Cube', 'spring-framework'),
				'pulse'         => esc_html__('Pulse', 'spring-framework'),
				'three-bounce'  => esc_html__('Three Bounce', 'spring-framework'),
				'wave'          => esc_html__('Wave', 'spring-framework'),
			));
		}

		/**
		 * Get Top Drawer Mode
		 *
		 * @return mixed|void
		 */
		public function get_top_drawer_mode()
		{
			return apply_filters('gsf_options_top_drawer_mode', array(
				'hide'   => esc_html__('Hide', 'spring-framework'),
				'toggle' => esc_html__('Toggle', 'spring-framework'),
				'show'   => esc_html__('Show', 'spring-framework')
			));
		}

		/**
		 * Get Color Skin default
		 *
		 * @return mixed|void
		 */
		public function &get_color_skin_default()
		{
			$skin_default = array(
				array(
					'skin_id'          => 'skin-light',
					'skin_name'        => esc_html__('Light', 'spring-framework'),
					'background_color' => '#fff',
					'text_color'       => '#7d7d7d',
                    'text_hover_color'    => '',
					'heading_color'    => '#333',
					'disable_color'    => '#959595',
					'border_color'     => '#ebebeb'
				),
				array(
					'skin_id'          => 'skin-dark',
					'skin_name'        => esc_html__('Dark', 'spring-framework'),
					'background_color' => '#252525',
					'text_color'       => '#a1a1a1',
                    'text_hover_color'    => '',
					'heading_color'    => '#fff',
					'disable_color'    => '#959595',
					'border_color'     => '#555'
				),
			);
			return $skin_default;
		}


		/**
		 * Get Color Skin
		 *
		 * @param bool $default
		 * @return array
		 */
		public function get_color_skin($default = false)
		{
			$skins = array();
			if ($default) {
				$skins[] = esc_html__('Inherit', 'spring-framework');
			}
			$custom_color_skin = G5P()->optionsSkin()->get_color_skin();
			if (is_array($custom_color_skin)) {
				foreach ($custom_color_skin as $key => $value) {
					if (isset($value['skin_name']) && isset($value['skin_id'])) {
						$skins[$value['skin_id']] = $value['skin_name'];
					}

				}
			}
			return $skins;
		}

		public function getPresetPostType()
		{
			$settings = array(
				'page_404' => array(
					'title' => esc_html__('404 Page', 'spring-framework')
				),
				'post'     => array(
					'title'  => esc_html__('Blog', 'spring-framework'),
					'preset' => array(
						'blog'        => array(
							'title' => esc_html__('Blog Listing', 'spring-framework'),
						),
						'single_blog' => array(
							'title'     => esc_html__('Single Blog', 'spring-framework'),
							'is_single' => true,
						)
					)
				)
			);

			if (class_exists('WooCommerce')) {
                $attribute_array      = array();
                $attribute_taxonomies = wc_get_attribute_taxonomies();
                if ( ! empty( $attribute_taxonomies ) ) {
                    foreach ( $attribute_taxonomies as $tax ) {
                        if ( wc_attribute_taxonomy_name( $tax->attribute_name ) ) {
                            $attribute_array[] = 'pa_' . $tax->attribute_name;
                        }
                    }
                }
                $settings = array_merge($settings, array(
                    'product' => array(
                        'title'  => esc_html__('Woocommerce', 'spring-framework'),
                        'preset' => array(
                            'archive_product' => array(
                                'title'      => esc_html__('Product Listing', 'spring-framework'),
                                'category'   => 'product_cat',
                                'tag'        => array_merge(array('product_tag'), $attribute_array),
                                'is_archive' => true,
                            ),
                            'single_product'  => array(
                                'title'     => esc_html__('Single Product', 'spring-framework'),
                                'is_single' => true,
                            )
                        )
                    )
                ));
			}
			return apply_filters('gsf_options_preset', $settings);
		}

		public function get_custom_post_layout_settings()
		{
			$settings = array(
				'search' => array(
					'title' => esc_html__('Search Listing', 'spring-framework')
				)
			);

			return apply_filters('gsf_options_custom_post_layout_settings', $settings);
		}

		/**
		 * Get social networks default
		 *
		 * @return array
		 */
		public function get_social_networks_default()
		{
			$social_networks = array(
				array(
					'social_name'  => esc_html__('Facebook', 'spring-framework'),
					'social_id'    => 'social-facebook',
					'social_icon'  => 'fa fa-facebook',
					'social_link'  => '',
					'social_color' => '#3b5998'
				),
				array(
					'social_name'  => esc_html__('Twitter', 'spring-framework'),
					'social_id'    => 'social-twitter',
					'social_icon'  => 'fa fa-twitter',
					'social_link'  => '',
					'social_color' => '#1da1f2'
				),
				array(
					'social_name'  => esc_html__('Pinterest', 'spring-framework'),
					'social_id'    => 'social-pinterest',
					'social_icon'  => 'fa fa-pinterest',
					'social_link'  => '',
					'social_color' => '#bd081c'
				),
				array(
					'social_name'  => esc_html__('Dribbble', 'spring-framework'),
					'social_id'    => 'social-dribbble',
					'social_icon'  => 'fa fa-dribbble',
					'social_link'  => '',
					'social_color' => '#00b6e3'
				),
				array(
					'social_name'  => esc_html__('LinkedIn', 'spring-framework'),
					'social_id'    => 'social-linkedIn',
					'social_icon'  => 'fa fa-linkedin',
					'social_link'  => '',
					'social_color' => '#0077b5'
				),
				array(
					'social_name'  => esc_html__('Vimeo', 'spring-framework'),
					'social_id'    => 'social-vimeo',
					'social_icon'  => 'fa fa-vimeo',
					'social_link'  => '',
					'social_color' => '#1ab7ea'
				),
				array(
					'social_name'  => esc_html__('Tumblr', 'spring-framework'),
					'social_id'    => 'social-tumblr',
					'social_icon'  => 'fa fa-tumblr',
					'social_link'  => '',
					'social_color' => '#35465c'
				),
				array(
					'social_name'  => esc_html__('Skype', 'spring-framework'),
					'social_id'    => 'social-skype',
					'social_icon'  => 'fa fa-skype',
					'social_link'  => '',
					'social_color' => '#00aff0'
				),
				array(
					'social_name'  => esc_html__('Google+', 'spring-framework'),
					'social_id'    => 'social-google-plus',
					'social_icon'  => 'fa fa-google-plus',
					'social_link'  => '',
					'social_color' => '#dd4b39'
				),
				array(
					'social_name'  => esc_html__('Flickr', 'spring-framework'),
					'social_id'    => 'social-flickr',
					'social_icon'  => 'fa fa-flickr',
					'social_link'  => '',
					'social_color' => '#ff0084'
				),
				array(
					'social_name'  => esc_html__('YouTube', 'spring-framework'),
					'social_id'    => 'social-youTube',
					'social_icon'  => 'fa fa-youtube',
					'social_link'  => '',
					'social_color' => '#cd201f'
				),
				array(
					'social_name'  => esc_html__('Foursquare', 'spring-framework'),
					'social_id'    => 'social-foursquare',
					'social_icon'  => 'fa fa-foursquare',
					'social_link'  => '',
					'social_color' => '#f94877'
				),
				array(
					'social_name'  => esc_html__('Instagram', 'spring-framework'),
					'social_id'    => 'social-instagram',
					'social_icon'  => 'fa fa-instagram',
					'social_link'  => '',
					'social_color' => '#405de6'
				),
				array(
					'social_name'  => esc_html__('GitHub', 'spring-framework'),
					'social_id'    => 'social-gitHub',
					'social_icon'  => 'fa fa-github',
					'social_link'  => '',
					'social_color' => '#4078c0'
				),
				array(
					'social_name'  => esc_html__('Xing', 'spring-framework'),
					'social_id'    => 'social-xing',
					'social_icon'  => 'fa fa-xing',
					'social_link'  => '',
					'social_color' => '#026466'
				),
				array(
					'social_name'  => esc_html__('Behance', 'spring-framework'),
					'social_id'    => 'social-behance',
					'social_icon'  => 'fa fa-behance',
					'social_link'  => '',
					'social_color' => '#1769ff'
				),
				array(
					'social_name'  => esc_html__('Deviantart', 'spring-framework'),
					'social_id'    => 'social-deviantart',
					'social_icon'  => 'fa fa-deviantart',
					'social_link'  => '',
					'social_color' => '#05cc47'
				),
				array(
					'social_name'  => esc_html__('Sound Cloud', 'spring-framework'),
					'social_id'    => 'social-soundCloud',
					'social_icon'  => 'fa fa-soundcloud',
					'social_link'  => '',
					'social_color' => '#ff8800'
				),
				array(
					'social_name'  => esc_html__('Yelp', 'spring-framework'),
					'social_id'    => 'social-yelp',
					'social_icon'  => 'fa fa-yelp',
					'social_link'  => '',
					'social_color' => '#af0606'
				),
				array(
					'social_name'  => esc_html__('RSS Feed', 'spring-framework'),
					'social_id'    => 'social-rss',
					'social_icon'  => 'fa fa-rss',
					'social_link'  => '',
					'social_color' => '#f26522'
				),
				array(
					'social_name'  => esc_html__('VK', 'spring-framework'),
					'social_id'    => 'social-vk',
					'social_icon'  => 'fa fa-vk',
					'social_link'  => '',
					'social_color' => '#45668e'
				),
				array(
					'social_name'  => esc_html__('Email', 'spring-framework'),
					'social_id'    => 'social-email',
					'social_icon'  => 'fa fa-envelope',
					'social_link'  => '',
					'social_color' => '#4285f4'
				),

			);
			return $social_networks;
		}

		public function get_social_networks()
		{
			$social_networks = G5P()->options()->get_social_networks();
			$options = array();
			if (is_array($social_networks)) {
				foreach ($social_networks as $social_network) {
					$options[$social_network['social_id']] = $social_network['social_name'];
				}
			}
			return $options;
		}

		/**
		 * Get social share
		 *
		 * @return array
		 */
		public function get_social_share()
		{
			$social_share = array(
				'facebook'  => esc_html__('Facebook', 'spring-framework'),
				'twitter'   => esc_html__('Twitter', 'spring-framework'),
				'google'    => esc_html__('Google +', 'spring-framework'),
				'linkedin'  => esc_html__('Linkedin', 'spring-framework'),
				'tumblr'    => esc_html__('Tumblr', 'spring-framework'),
				'pinterest' => esc_html__('Pinterest', 'spring-framework'),
				'email'     => esc_html__('Email', 'spring-framework'),
				'telegram'  => esc_html__('Telegram', 'spring-framework'),
				'whatsapp'  => esc_html__('WhatsApp', 'spring-framework')
			);
			return $social_share;
		}

		/**
		 * Get Post Layout
		 *
		 * @param bool $inherit
		 * @return array|mixed|void
		 */
		public function get_post_layout($inherit = false)
		{
			$config = apply_filters('gsf_options_post_layout', array(
				'large-image'    => array(
					'label' => esc_html__('Large Image', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/blog-large-image.png'),
				),
				'medium-image'   => array(
					'label' => esc_html__('Medium Image', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/blog-medium-image.png'),
				),
				'grid'         => array(
					'label' => esc_html__('Grid', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/blog-grid.png'),
				),
				'masonry'        => array(
					'label' => esc_html__('Masonry', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/blog-masonry.png'),
				),
				'zigzac'        => array(
					'label' => esc_html__('Zigzag', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/blog-zigzac.png'),
				),
				'list'        => array(
					'label' => esc_html__('Medium Image 2', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/blog-list.png'),
				)
			));
			if ($inherit) {
				$config = array(
						'' => array(
							'label' => esc_html__('Inherit', 'spring-framework'),
							'img'   => G5P()->pluginUrl('assets/images/theme-options/default.png'),
						),
					) + $config;
			}
			return $config;
		}
        public function get_single_post_layout($inherit = false)
        {
            $config = apply_filters('gsf_options_single_post_layout', array(
                'layout-1' => array(
                    'label' => esc_html__('Layout 1', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/post-layout-1.png'),
                ),
                'layout-2' => array(
                    'label' => esc_html__('Layout 2', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/post-layout-2.png'),
                ),
                'layout-3' => array(
                    'label' => esc_html__('Layout 3', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/post-layout-3.png'),
                ),
                'layout-4' => array(
                    'label' => esc_html__('Layout 4', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/post-layout-4.png'),
                ),
                'layout-5' => array(
                    'label' => esc_html__('Layout 5', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/post-layout-5.png'),
                )

            ));
            if ($inherit) {
                $config = array(
                        '' => array(
                            'label' => esc_html__('Inherit', 'spring-framework'),
                            'img'   => G5P()->pluginUrl('assets/images/theme-options/default.png'),
                        ),
                    ) + $config;
            }
            return $config;
        }
		public function get_post_item_skin($inherit = false)
		{
			$config = apply_filters('gsf_options_post_item_skin', array(
				'post-skin-01' => array(
					'label' => esc_html__('Skin 01', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/post-skin-01.png')
				),
				'post-skin-02' => array(
					'label' => esc_html__('Skin 02', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/post-skin-02.png')
				),
				'post-skin-03' => array(
					'label' => esc_html__('Skin 03', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/post-skin-03.png')
				),
				'post-skin-04' => array(
					'label' => esc_html__('Skin 04', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/post-skin-04.png')
				),
				'post-skin-05' => array(
					'label' => esc_html__('Skin 05', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/post-skin-05.png')
				),
				'post-skin-06' => array(
					'label' => esc_html__('Skin 06', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/post-skin-06.png')
				),
                'post-skin-07' => array(
                    'label' => esc_html__('Skin 07', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/post-skin-07.png')
                )
			));
			if ($inherit) {
				$config = array(
						'' => array(
							'label' => esc_html__('Inherit', 'spring-framework'),
							'img'   => G5P()->pluginUrl('assets/images/theme-options/default.png'),
						),
					) + $config;
			}
			return $config;
		}

		/**
		 * Get Post Columns
		 *
		 * @param bool $inherit
		 * @return array|mixed|void
		 */
		public function get_post_columns($inherit = false)
		{
			$config = apply_filters('gsf_options_post_columns', array(
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
                '5' => '5',
				'6' => '6'
			));

			if ($inherit) {
				$config = array(
						'' => esc_html__('Inherit', 'spring-framework')
					) + $config;
			}

			return $config;
		}

		/**
		 * Get Post Columns Gap
		 *
		 * @param bool $inherit
		 * @return array|mixed|void
		 */
		public function get_post_columns_gutter($inherit = false)
		{
			$config = apply_filters('gsf_options_post_columns_gutter', array(
				'none'  => esc_html__('None', 'spring-framework'),
				'10' => '10px',
				'20' => '20px',
				'30' => '30px'
			));

			if ($inherit) {
				$config = array(
						'' => esc_html__('Inherit', 'spring-framework')
					) + $config;
			}

			return $config;
		}

		/**
		 * Get Post Paging Mode
		 *
		 * @param bool $inherit
		 * @return array|mixed|void
		 */
		public function get_post_paging_mode($inherit = false)
		{
			$config = apply_filters('gsf_options_post_paging_mode', array(
				'pagination'      => esc_html__('Pagination', 'spring-framework'),
				'pagination-ajax' => esc_html__('Ajax - Pagination', 'spring-framework'),
				'next-prev'       => esc_html__('Ajax - Next Prev', 'spring-framework'),
				'load-more'       => esc_html__('Ajax - Load More', 'spring-framework'),
				'infinite-scroll' => esc_html__('Ajax - Infinite Scroll', 'spring-framework')
			));

			if ($inherit) {
				$config = array(
						'' => esc_html__('Inherit', 'spring-framework')
					) + $config;
			}

			return $config;
		}

		public function get_post_paging_small_mode($inherit = false)
		{
			$config = apply_filters('gsf_options_post_paging_small_mode', array(
				'none'            => esc_html__('None', 'spring-framework'),
				'pagination-ajax' => esc_html__('Ajax - Pagination', 'spring-framework'),
				'next-prev'       => esc_html__('Ajax - Next Prev', 'spring-framework'),
				'load-more'       => esc_html__('Ajax - Load More', 'spring-framework'),
			));

			if ($inherit) {
				$config = array(
						'' => esc_html__('Inherit', 'spring-framework')
					) + $config;
			}

			return $config;
		}

		/**
		 * Get Animation
		 *
		 * @param $inherit
		 * @return array|mixed|void
		 */
		public function get_animation($inherit = false)
		{
			$config = apply_filters('gsf_options_animation', array(
				'none'          => esc_html__('None', 'spring-framework'),
				'top-to-bottom' => esc_html__('Top to bottom', 'spring-framework'),
				'bottom-to-top' => esc_html__('Bottom to top', 'spring-framework'),
				'left-to-right' => esc_html__('Left to right', 'spring-framework'),
				'right-to-left' => esc_html__('Right to left', 'spring-framework'),
				'appear'        => esc_html__('Appear from center', 'spring-framework')
			));

			if ($inherit) {
				$config = array(
						'' => esc_html__('Inherit', 'spring-framework')
					) + $config;
			}

			return $config;
		}





		/**
		 * Get Related Post Algorithm
		 *
		 * @param bool $inherit
		 * @return array|mixed|void
		 */
		public function get_related_post_algorithm($inherit = false)
		{
			$config = apply_filters('gsf_options_related_post_algorithm', array(
				'cat'            => esc_html__('by Category', 'spring-framework'),
				'tag'            => esc_html__('by Tag', 'spring-framework'),
				'author'         => esc_html__('by Author', 'spring-framework'),
				'cat-tag'        => esc_html__('by Category & Tag', 'spring-framework'),
				'cat-tag-author' => esc_html__('by Category & Tag & Author', 'spring-framework'),
				'random'         => esc_html__('Randomly', 'spring-framework')
			));

			if ($inherit) {
				$config = array(
						'' => esc_html__('Inherit', 'spring-framework')
					) + $config;
			}

			return $config;

		}

        /**
         * Get Related Product Algorithm
         *
         * @param bool $inherit
         * @return array|mixed|void
         */
        public function get_related_product_algorithm()
        {
            $config = apply_filters('gsf_options_related_product_algorithm', array(
                'cat'            => esc_html__('by Category', 'spring-framework'),
                'tag'            => esc_html__('by Tag', 'spring-framework'),
                'cat-tag'        => esc_html__('by Category & Tag', 'spring-framework')
            ));
            return $config;

        }


        public function get_product_catalog_layout($inherit = false)
        {
            $config = apply_filters('gsf_options_product_catalog_layout', array(
                'grid' => array(
                    'label' => esc_html__('Grid', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/shop-grid.jpg'),
                ),
                'list' => array(
                    'label' => esc_html__('List', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/shop-list.jpg'),
                ),
                'metro-01' => array(
                    'label' => esc_html__('Metro 01', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/layout-metro-01.png'),
                ),
                'metro-02' => array(
                    'label' => esc_html__('Metro 02', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/layout-metro-02.png'),
                ),
                'metro-03' => array(
                    'label' => esc_html__('Metro 03', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/layout-metro-03.png'),
                ),
                'metro-04' => array(
                    'label' => esc_html__('Metro 04', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/layout-metro-04.png'),
                ),
                'metro-05' => array(
                    'label' => esc_html__('Metro 05', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/layout-metro-05.png'),
                ),
				'metro-06' => array(
					'label' => esc_html__('Metro 06', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/layout-metro-06.png'),
				),
                'metro-07' => array(
                    'label' => esc_html__('Metro 07', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/product-metro-07.png'),
                ),
				'metro-08' => array(
					'label' => esc_html__('Metro 08', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/product-metro-08.png'),
				),
				'metro-09' => array(
					'label' => esc_html__('Metro 09', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/product-metro-09.png'),
				),
            ));
            if ($inherit) {
                $config = array(
                        '' => array(
                            'label' => esc_html__('Inherit', 'spring-framework'),
                            'img'   => G5P()->pluginUrl('assets/images/theme-options/default.png'),
                        ),
                    ) + $config;
            }
            return $config;
        }
		public function get_product_item_skin($inherit = false)
		{
			$config = apply_filters('gsf_options_product_item_skin', array(
				'product-skin-01' => array(
					'label' => esc_html__('Skin 01', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/product-skin-01.png')
				),
				'product-skin-02' => array(
					'label' => esc_html__('Skin 02', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/product-skin-02.png')
				),
				'product-skin-03' => array(
					'label' => esc_html__('Skin 03', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/product-skin-03.png')
				),
				'product-skin-04' => array(
					'label' => esc_html__('Skin 04', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/product-skin-04.png')
				),
				'product-skin-05' => array(
					'label' => esc_html__('Skin 05', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/product-skin-05.png')
				),
                'product-skin-07' => array(
                    'label' => esc_html__('Skin 06', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/product-skin-07.png')
                )
			));
			if ($inherit) {
				$config = array(
						'' => array(
							'label' => esc_html__('Inherit', 'spring-framework'),
							'img'   => G5P()->pluginUrl('assets/images/theme-options/default.png')
						),
					) + $config;
			}
			return $config;
		}

        public function get_image_ratio($inherit = false)
        {
            $config = apply_filters('gsf_options_image_ratio', array(
                '1x1' => '1:1',
                '4x3' => '4:3',
                '3x4' => '3:4',
                '16x9' => '16:9',
                '9x16' => '9:16',
                'custom' => esc_html__('Custom','spring-framework')
            ));
            if ($inherit) {
                $config = array(
                        '' => esc_html__('Inherit', 'spring-framework'),
                    ) + $config;
            }
            return $config;
        }
        public function get_product_single_layout($inherit = false)
        {
            $config = apply_filters('gsf_options_product_single_layout', array(
                'layout-01' => array(
                    'label' => esc_html__('Layout 01', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/product-single-01.jpg')
                ),
                'layout-02' => array(
                    'label' => esc_html__('Layout 02', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/product-single-02.png')
                ),
                'layout-03' => array(
                    'label' => esc_html__('Layout 03', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/product-single-03.png')
                ),
                'layout-04' => array(
                    'label' => esc_html__('Layout 04', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/product-single-04.jpg')
                ),
				'layout-05' => array(
					'label' => esc_html__('Layout 05', 'spring-framework'),
					'img'   => G5P()->pluginUrl('assets/images/theme-options/product-single-05.png')
				)
            ));
            if ($inherit) {
                $config = array(
                        '' => array(
                            'label' => esc_html__('Inherit', 'spring-framework'),
                            'img'   => G5P()->pluginUrl('assets/images/theme-options/default.png'),
                        ),
                    ) + $config;
            }
            return $config;
        }

        public function get_product_image_hover_effect($inherit = false)
        {
            $config = apply_filters('gsf_product_image_hover_effect',array(
                'none' => esc_html__('None','spring-framework'),
                'change-image' => esc_html__('Change Image','spring-framework'),
                'flip-back' => esc_html__('Flip Back','spring-framework')
            ));

            if ($inherit) {
                $config = array(
                        '' => esc_html__('Inherit', 'spring-framework')
                    ) + $config;
            }

            return $config;
        }
        public function get_portfolio_hover_effect($inherit = false)
        {
            $config = apply_filters('gsf_portfolio_hover_effect',array(
                'none' => esc_html__('None','spring-framework'),
                'suprema' => esc_html__('Suprema','spring-framework'),
                'layla' => esc_html__('Layla','spring-framework'),
                'bubba' => esc_html__('Bubba','spring-framework'),
                'jazz' => esc_html__('Jazz','spring-framework')
            ));

            if ($inherit) {
                $config = array(
                        '' => esc_html__('Inherit', 'spring-framework')
                    ) + $config;
            }

            return $config;
        }

        public function get_portfolio_layout($inherit = false)
        {
            $config = apply_filters('gsf_options_portfolio_layout', array(
                'grid' => array(
                    'label' => esc_html__('Grid', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-grid.png'),
                ),
                'masonry' => array(
                    'label' => esc_html__('Masonry', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-masonry.png'),
                ),
                'scattered' => array(
                    'label' => esc_html__('Scattered', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-masonry-sd.png'),
                ),
                'metro-1' => array(
                    'label' => esc_html__('Metro 01', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-metro-1.png')
                ),
                'metro-2' => array(
                    'label' => esc_html__('Metro 02', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-metro-2.png')
                ),
                'metro-3' => array(
                    'label' => esc_html__('Metro 03', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-metro-3.png')
                ),
                'metro-4' => array(
                    'label' => esc_html__('Metro 04', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-metro-4.png')
                ),
                'metro-5' => array(
                    'label' => esc_html__('Metro 05', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-metro-5.png')
                ),
                'metro-6' => array(
                    'label' => esc_html__('Metro 06', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-metro-6.png')
                ),
                'metro-7' => array(
                    'label' => esc_html__('Metro 07', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-metro-7.png')
                ),
                'metro-8' => array(
                    'label' => esc_html__('Metro 08', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-metro-8.png')
                ),
                'metro-9' => array(
                    'label' => esc_html__('Metro 09', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-metro-9.png')
                ),
                'metro-10' => array(
                    'label' => esc_html__('Metro 10', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-metro-10.png')
                ),
                'metro-11' => array(
                    'label' => esc_html__('Metro 11', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-metro-11.png')
                ),
                'carousel-3d' => array(
                    'label' => esc_html__('Carousel 3D', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-3d-carousel.png')
                ),
            ));
            if ($inherit) {
                $config = array(
                        '' => array(
                            'label' => esc_html__('Inherit', 'spring-framework'),
                            'img'   => G5P()->pluginUrl('assets/images/theme-options/default.png')
                        ),
                    ) + $config;
            }
            return $config;
        }
        public function get_portfolio_item_skin($inherit = false)
        {
            $config = apply_filters('gsf_options_portfolio_item_skin', array(
                'portfolio-item-skin-01' => array(
                    'label' => esc_html__('Skin 01', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-item-skin-01.png')
                ),
                'portfolio-item-skin-02' => array(
                    'label' => esc_html__('Skin 02', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-item-skin-02.png')
                ),
                'portfolio-item-skin-03' => array(
                    'label' => esc_html__('Skin 03', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-item-skin-03.png')
                )
            ));
            if ($inherit) {
                $config = array(
                        '' => array(
                            'label' => esc_html__('Inherit', 'spring-framework'),
                            'img'   => G5P()->pluginUrl('assets/images/theme-options/default.png')
                        ),
                    ) + $config;
            }
            return $config;
        }

        public function get_portfolio_details_default()
        {
            $configs = array(
                array(
                    'title'    => esc_html__('Date','spring-framework'),
                    'id'  => 'portfolio_details_date',
                ),
                array(
                    'title'    => esc_html__('Client','spring-framework'),
                    'id'  => 'portfolio_details_client',
                ),
                array(
                    'title'    => esc_html__('Project Type','spring-framework'),
                    'id'  => 'portfolio_details_type',
                ),
                array(
                    'title'    => esc_html__('Author','spring-framework'),
                    'id'  => 'portfolio_details_author',
                )
            );
            return apply_filters('gsf_portfolio_details_default',$configs);
        }
        public function get_single_portfolio_layout($inherit = false)
        {
            $config = apply_filters('gsf_options_single_portfolio_layout', array(
                'layout-1' => array(
                    'label' => esc_html__('Layout 1', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/single-portfolio-layout-1.png'),
                ),
                'layout-2' => array(
                    'label' => esc_html__('Layout 2', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/single-portfolio-layout-2.png'),
                ),
                'layout-3' => array(
                    'label' => esc_html__('Layout 3', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/single-portfolio-layout-3.png'),
                ),
                'layout-4' => array(
                    'label' => esc_html__('Layout 4', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/single-portfolio-layout-4.png'),
                ),
                'layout-5' => array(
                    'label' => esc_html__('Layout 5', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/single-portfolio-layout-5.png'),
                ),

            ));
            if ($inherit) {
                $config = array(
                        '' => array(
                            'label' => esc_html__('Inherit', 'spring-framework'),
                            'img'   => G5P()->pluginUrl('assets/images/theme-options/default.png'),
                        ),
                    ) + $config;
            }
            return $config;
        }

        public function get_single_portfolio_gallery_layout($inherit = false)
        {
            $config = apply_filters('gsf_options_single_portfolio_gallery_layout', array(
                'carousel' => array(
                    'label' => esc_html__('Slider', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-gallery-carousel.png'),
                ),
                'thumbnail' => array(
                    'label' => esc_html__('Gallery', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-gallery-thumbnail.png'),
                ),
                'carousel-center' => array(
                    'label' => esc_html__('Slider Center', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-gallery-carousel-center.png'),
                ),
                'grid' => array(
                    'label' => esc_html__('Grid', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-gallery-grid.png'),
                ),
                'masonry' => array(
                    'label' => esc_html__('Masonry', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-masonry.png'),
                ),
                'carousel-3d' => array(
                    'label' => esc_html__('Slider 3D', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-3d-carousel.png'),
                ),
                'metro' => array(
                    'label' => esc_html__('Metro', 'spring-framework'),
                    'img'   => G5P()->pluginUrl('assets/images/theme-options/portfolio-gallery-metro.png'),
                )
            ));
            if ($inherit) {
                $config = array(
                        '' => array(
                            'label' => esc_html__('Inherit', 'spring-framework'),
                            'img'   => G5P()->pluginUrl('assets/images/theme-options/default.png'),
                        ),
                    ) + $config;
            }
            return $config;
        }

        public function get_portfolio_related_algorithm($inherit = false)
        {
            $config = apply_filters('gsf_options_portfolio_related_algorithm', array(
                'cat'            => esc_html__('by Category', 'spring-framework'),
                'author'         => esc_html__('by Author', 'spring-framework'),
                'cat-author' => esc_html__('by Category & Author', 'spring-framework'),
                'random'         => esc_html__('Randomly', 'spring-framework')
            ));

            if ($inherit) {
                $config = array(
                        '' => esc_html__('Inherit', 'spring-framework')
                    ) + $config;
            }

            return $config;

        }
	}
}