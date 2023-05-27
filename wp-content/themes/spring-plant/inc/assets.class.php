<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('Spring_Plant_Inc_Assets')) {
	class Spring_Plant_Inc_Assets {
		private static $_instance;
		public static function getInstance()
		{
			if (self::$_instance == NULL) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		public function registerAssets() {
			// Bootstrap
			wp_register_style('bootstrap', Spring_Plant()->helper()->getAssetUrl('assets/vendors/bootstrap-4.0.0/css/bootstrap.min.css'), array(), '4.0.0');
            wp_register_script('popper', Spring_Plant()->helper()->getAssetUrl('assets/vendors/popper/popper.min.js'), array('jquery'), '1.0.0', true);
            wp_register_script('bootstrap-affix', Spring_Plant()->helper()->getAssetUrl('assets/vendors/bootstrap-4.0.0/js/bootstrap.affix.min.js'), array('jquery'), '1.0.0', true);
			wp_register_script('bootstrap', Spring_Plant()->helper()->getAssetUrl('assets/vendors/bootstrap-4.0.0/js/bootstrap.min.js'), array('jquery','popper','bootstrap-affix'), '4.0.0', true);
            wp_register_style('custom-bootstrap', Spring_Plant()->helper()->getAssetUrl('assets/vendors/bootstrap-4.0.0/css/custom-bootstrap.min.css'), array('bootstrap'), '4.0.0');


			//Owl.Carousel
			wp_register_style('owl-carousel', Spring_Plant()->helper()->getAssetUrl('assets/vendors/owl.carousel/assets/owl.carousel.min.css'), array(), '2.2.0');
			wp_register_style('owl-carousel-theme-default', Spring_Plant()->helper()->getAssetUrl('assets/vendors/owl.carousel/assets/owl.theme.default.min.css'), array(), '2.2.0');
			wp_register_script('owl-carousel', Spring_Plant()->helper()->getAssetUrl('assets/vendors/owl.carousel/owl.carousel.min.js'), array('jquery'), '2.2.0', true);

			// isotope
			wp_register_script('isotope', Spring_Plant()->helper()->getAssetUrl('assets/vendors/isotope/isotope.pkgd.min.js'), array('jquery'), '3.0.5', true);
			
			//slick slider
			wp_register_style('slick', Spring_Plant()->helper()->getAssetUrl('assets/vendors/slick/css/slick.min.css'), array());
			
			wp_register_script('slick', Spring_Plant()->helper()->getAssetUrl('assets/vendors/slick/js/slick.min.js'), array('jquery'), array(), true);
            // jquery.cookie
            wp_register_script('jquery-cookie', Spring_Plant()->helper()->getAssetUrl('assets/vendors/jquery.cookie/jquery.cookie.min.js'), array('jquery'), '1.4.1', true);

			// Perfect-scrollbar
            wp_register_script('perfect-scrollbar',Spring_Plant()->helper()->getAssetUrl('assets/vendors/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js'),array('jquery'),'0.6.15',true);
			wp_register_style('perfect-scrollbar', Spring_Plant()->helper()->getAssetUrl('assets/vendors/perfect-scrollbar/css/perfect-scrollbar.min.css'), array(), '0.6.15');


			// Magnific Popup
			wp_register_style('magnific-popup', Spring_Plant()->helper()->getAssetUrl('assets/vendors/magnific-popup/magnific-popup.min.css'), array(), '1.1.0');
			wp_register_script('magnific-popup', Spring_Plant()->helper()->getAssetUrl('assets/vendors/magnific-popup/jquery.magnific-popup.min.js'), array('jquery'), '1.1.0', true);

			// waypoints
			wp_register_script( 'waypoints', Spring_Plant()->helper()->getAssetUrl('assets/vendors/waypoints/jquery.waypoints.min.js'), array( 'jquery' ), '4.0.1', true );

			// animated
			wp_register_style( 'animate-css', Spring_Plant()->helper()->getAssetUrl('assets/css/animate.min.css'), array(), '1.0');

			//ladda
			wp_register_style( 'ladda', Spring_Plant()->helper()->getAssetUrl('assets/vendors/ladda/ladda-themeless.min.css'), array(), '1.0');
			wp_register_script('ladda',Spring_Plant()->helper()->getAssetUrl('assets/vendors/ladda/ladda.min.js'),array('jquery'),'1.0.0',true);
			wp_register_script('ladda-spin',Spring_Plant()->helper()->getAssetUrl('assets/vendors/ladda/spin.min.js'),array('jquery'),'1.0.0',true);

			// hc-sticky
			wp_register_script('hc-sticky',Spring_Plant()->helper()->getAssetUrl('assets/vendors/hc-sticky/jquery.hc-sticky.min.js'),array('jquery'),'1.2.43',true);


			// lazy-load
			wp_register_script('jquery-lazyload',Spring_Plant()->helper()->getAssetUrl('assets/vendors/lazyload/jquery.lazyload.min.js'),array('jquery'),'1.9.3',true);


			// modernizr
            wp_register_script('modernizr',Spring_Plant()->helper()->getAssetUrl('assets/vendors/modernizr/modernizr.js'),array('jquery'),'3.5.0',true);

            // jquery.easing
            wp_register_script('jquery-easing',Spring_Plant()->helper()->getAssetUrl('assets/vendors/jquery.easing/jquery.easing.1.3.js'),array('jquery'),'1.3',true);

            // jquery-countdown
            wp_register_script('jquery-countdown',Spring_Plant()->helper()->getAssetUrl('assets/vendors/jquery.countdown/jquery.countdown.min.js'),array('jquery'),'2.2.0',true);

            // jquery.nav
            wp_register_script('jquery-nav',Spring_Plant()->helper()->getAssetUrl('assets/vendors/jquery.nav/jquery.nav.min.js'),array('jquery'),'3.0.0',true);

			wp_register_script(Spring_Plant()->helper()->assetsHandle('core'),Spring_Plant()->helper()->getAssetUrl('assets/js/core.min.js'),array('jquery', 'imagesloaded'),'1.0',true);
            wp_register_script( 'pretty-tabs', Spring_Plant()->helper()->getAssetUrl('assets/vendors/pretty-tabs/jquery.pretty-tabs.min.js'), array( 'jquery', Spring_Plant()->helper()->assetsHandle('core' )), '1.0', true );

            wp_register_script(Spring_Plant()->helper()->assetsHandle('woocommerce'), Spring_Plant()->helper()->getAssetUrl('assets/js/woocommerce.min.js'), array(Spring_Plant()->helper()->assetsHandle('core'), 'pretty-tabs'), '2.2', true);
			wp_localize_script(Spring_Plant()->helper()->assetsHandle('woocommerce'),'g5plus_woocommerce_var',array(
				'localization' => array(
					'add_cart_quantity' => array(
						'min' => esc_html__('Sorry, the minimum value was reached','spring-plant'),
						'max' => esc_html__('Sorry, the maximum value was reached','spring-plant')


					)
				)
			));
            wp_register_script(Spring_Plant()->helper()->assetsHandle('portfolio'), Spring_Plant()->helper()->getAssetUrl('assets/js/portfolio.min.js'), array(Spring_Plant()->helper()->assetsHandle('core')), '2.2', true);

			wp_register_script(Spring_Plant()->helper()->assetsHandle('main'),Spring_Plant()->helper()->getAssetUrl('assets/js/main.min.js'),array('jquery', 'imagesloaded'),'1.0',true);

			wp_register_style('font-awesome', Spring_Plant()->helper()->getAssetUrl('assets/vendors/font-awesome/css/font-awesome.css'), array(), '4.7.0');

            $color_skin = Spring_Plant()->optionsSkin()->get_color_skin();
            if (is_array($color_skin)) {
                foreach ($color_skin as $key => $value) {
                    if (isset($value['skin_id'])) {
                        // Enqueue skin.css
                        if (function_exists('G5P') && defined('CSS_DEBUG') && CSS_DEBUG) {
                            wp_register_style(Spring_Plant()->helper()->assetsHandle("skin-{$value['skin_id']}"), admin_url('admin-ajax.php') . '?action=gsf_dev_less_skin_to_css&skin_id=' . $value['skin_id'], array(), false);
                        } else {
                            do_action('gsf_before_enqueue_skin_css', $value['skin_id']);
                            wp_register_style(Spring_Plant()->helper()->assetsHandle("skin-{$value['skin_id']}"), Spring_Plant()->helper()->getAssetUrl("assets/skin/{$value['skin_id']}.min.css"));
                        }
                    }
                }
            }
        }


		public function enqueueAssets() {


		    // modernizr
            wp_enqueue_script('modernizr');

            // jquery.easing
            wp_enqueue_script('jquery-easing');

            // jquery-countdown
            wp_enqueue_script('jquery-countdown');


			// Bootstrap
			wp_enqueue_style('bootstrap');
			wp_enqueue_script('bootstrap');
            wp_enqueue_style('custom-bootstrap');
            
			// Owl.Carousel
			wp_enqueue_style('owl-carousel');
			wp_enqueue_style('owl-carousel-theme-default');
			wp_enqueue_script('owl-carousel');

			//isotope
			wp_enqueue_script('isotope');

			// Perfect-scrollbar
			wp_enqueue_style('perfect-scrollbar');
            wp_enqueue_script('perfect-scrollbar');

			wp_enqueue_style('font-awesome');

			// Magnific Popup
			wp_enqueue_style('magnific-popup');
			wp_enqueue_script('magnific-popup');
            wp_enqueue_script('jquery-cookie');

			// animated
			wp_enqueue_style('animate-css');

			//waypoints
			wp_enqueue_script('waypoints');

			//ladda
			wp_enqueue_style( 'ladda');
			wp_enqueue_script('ladda-spin');
			wp_enqueue_script('ladda');
			
			//slick
			wp_enqueue_style('slick');
			wp_enqueue_script('slick');
			// hc-sticky
			wp_enqueue_script('hc-sticky');

            wp_enqueue_script('pretty-tabs');


			// comment
			if (is_singular()) wp_enqueue_script('comment-reply');


			// lazyLoad
			$lazy_load_images = Spring_Plant()->options()->get_lazy_load_images();
			if ($lazy_load_images === 'on') {
				wp_enqueue_script('jquery-lazyload');
			}

            $is_one_page = Spring_Plant()->metaBox()->get_is_one_page();
            if ($is_one_page === 'on') {
                wp_enqueue_script('jquery-nav');
            }

			// js Core
			wp_enqueue_script(Spring_Plant()->helper()->assetsHandle('core'));

            // js woocommerce
            if(class_exists( 'WooCommerce' )) {
                wp_enqueue_script(Spring_Plant()->helper()->assetsHandle('woocommerce'));
            }

            // js portfolio
            $custom_post_type_disable = Spring_Plant()->options()->get_custom_post_type_disable();
            if(!in_array('portfolio', $custom_post_type_disable)) {
                wp_enqueue_script(Spring_Plant()->helper()->assetsHandle('portfolio'));
            }

			// js Main
			wp_enqueue_script(Spring_Plant()->helper()->assetsHandle('main'));

			// js variable
			wp_localize_script(
				Spring_Plant()->helper()->assetsHandle('main'),
				'spring_plant_variable',
				array(
					'ajax_url' => admin_url('admin-ajax.php'),
					'theme_url' => Spring_Plant()->themeUrl(),
					'site_url' => site_url(),
                    'pretty_tabs_more_text' => wp_kses_post(__('More <span class="caret"></span>','spring-plant'))
				)
			);

			$custom_js = Spring_Plant()->options()->get_custom_js();
			if (!empty($custom_js)) {
				wp_add_inline_script(Spring_Plant()->helper()->assetsHandle('main'),$custom_js);
			}

			$presetId = Spring_Plant()->helper()->getCurrentPreset();

			$rtl = is_rtl() || Spring_Plant()->options()->get_rtl_enable() === 'on' || isset($_GET['RTL']);

			// Enqueue style.css
			if (function_exists('G5P') && defined('CSS_DEBUG') && CSS_DEBUG) {
				wp_enqueue_style(Spring_Plant()->helper()->assetsHandle('main'), admin_url('admin-ajax.php') . '?action=gsf_dev_less_to_css&_gsf_preset=' . $presetId, array(), false);
				if ($rtl) {
					wp_enqueue_style(Spring_Plant()->helper()->assetsHandle('rtl'), admin_url('admin-ajax.php') . '?action=gsf_dev_less_to_css_rtl', array(), false);
				}

			} else {
				do_action('gsf_before_enqueue_main_css',$presetId);
				if (!$presetId || !$this->enqueuePreset($presetId)) {
					wp_enqueue_style(Spring_Plant()->helper()->assetsHandle('main'), Spring_Plant()->helper()->getAssetUrl('style.min.css'));
				}

				if ($rtl) {
					do_action('gsf_before_enqueue_main_css_rtl');
					wp_enqueue_style(Spring_Plant()->helper()->assetsHandle('rtl'), Spring_Plant()->helper()->getAssetUrl('assets/css/rtl.min.css'));
				}
			}

            $skins = array();
            $skin_config = array(
                'header_skin',
                'mobile_header_skin',
                'content_skin',
                'sub_menu_skin'
            );

            // navigation skin
            $header_layout = Spring_Plant()->options()->get_header_layout();
            if (in_array($header_layout, array('header-7', 'header-8'))) {
                $skin_config[] = 'navigation_skin';
            }

            // page title skin
            $page_title_enable = Spring_Plant()->options()->get_page_title_enable();
            if ($page_title_enable === 'on') {
                $skin_config[] = 'page_title_skin';
            }

            // top drawer
            $top_drawer_mode = Spring_Plant()->options()->get_top_drawer_mode();
            if ($top_drawer_mode !== 'hide') {
                $skin_config[] = 'top_drawer_skin';
            }
            // header sticky
            $header_sticky = Spring_Plant()->options()->get_header_sticky();
            if (($header_sticky !== '') && !in_array($header_layout, array('header-9', 'header-10'))) {
                $skin_config[] = 'header_sticky_skin';
            }
            $mailchimp_popup_enable = Spring_Plant()->options()->get_mailchimp_popup_enable();
            if (($mailchimp_popup_enable === 'on')) {
                $skin_config[] = 'mailchimp_popup_skin';
            }

            foreach ($skin_config as $skin_key) {
                $skin = Spring_Plant()->options()->getOptions($skin_key);
                if (!empty($skin) && !in_array($skin, $skins)) {
                    $skins[] = $skin;
                }
            }

            foreach ($skins as $skin_id) {
                // Enqueue skin.css
                wp_enqueue_style(Spring_Plant()->helper()->assetsHandle("skin-{$skin_id}"));
            }
		}


		private function enqueuePreset($preset){
			$filename = Spring_Plant()->themeDir("assets/preset/{$preset}.min.css");
			if (!file_exists($filename)) {
				return false;
			}
			wp_enqueue_style(Spring_Plant()->helper()->assetsHandle('main'),Spring_Plant()->helper()->getAssetUrl("assets/preset/{$preset}.min.css"));
			return true;
		}

        public function enqueue_icon_font() {
		    if (!function_exists('G5P')) {
                $icon_font_css = Spring_Plant()->fontIcons()->registerAssets();
                foreach ($icon_font_css as $font_key => $font_value) {
                    wp_enqueue_style($font_key,$font_value['url'],array(),$font_value['ver']);
                }

			    $font_url = add_query_arg( 'family', urlencode( 'Lato:100,100i,300,300i,400,400i,700,700i,900,900i|Playfair Display:400,400i,700,700i,900,900i' ), "//fonts.googleapis.com/css" );
                wp_enqueue_style('google_fonts',$font_url);
            }
        }

        public function getCustomCss()
        {
            $custom_css = '';

            /**
             * Custom Background
             */
            $custom_background = array(
                /**
                 * Body Background
                 */
                'body_background' => array(
                    'selector' => 'body',
                    'default' => ''
                ),
            );
            foreach ($custom_background as $key => $value) {
                $background = Spring_Plant()->options()->getOptions($key);
                $background_attributes = array();
                if (isset($background['background_color']) && !empty($background['background_color'])) {
                    $background_attributes[] = "background-color: {$background['background_color']} !important";
                }

                if (isset($background['background_image_url']) && !empty($background['background_image_url'])) {
                    $background_repeat = isset($background['background_repeat']) ? $background['background_repeat'] : '';
                    $background_position = isset($background['background_position']) ? $background['background_position'] : '';
                    $background_size = isset($background['background_size']) ? $background['background_size'] : '';
                    $background_attachment = isset($background['background_attachment']) ? $background['background_attachment'] : '';

                    $background_attributes[] = "background-image: url('{$background['background_image_url']}')";

                    if (!empty($background_repeat)) {
                        $background_attributes[] = "background-repeat: {$background_repeat}";
                    }

                    if (!empty($background_position)) {
                        $background_attributes[] = "background-position: {$background_position}";
                    }

                    if (!empty($background_size)) {
                        $background_attributes[] = "background-size: {$background_size}";
                    }

                    if (!empty($background_attachment)) {
                        $background_attributes[] = "background-attachment: {$background_attachment}";
                    }

                }

                $background_css = implode('; ', array_filter($background_attributes));

                $custom_css .= <<<CSS
			{$value['selector']} {
				{$background_css}
			}
CSS;

            }

            /**
             * Custom Background Color
             */
            $custom_background_color = array(
                'loading_animation_bg_color' => array('.site-loading' => 'background-color'), /* loading background color */
                'content_background_color' => array('#gf-wrapper' => 'background-color'), /* content background color */
                'top_drawer_background_color' => array('.top-drawer-wrap' => 'background-color', '.top-drawer-toggle' => 'border-top-color'), /* top drawer background color */
                'header_background_color' => array('.main-header' => 'background-color'), /* header background color */
                'header_sticky_background_color' => array('.main-header .header-sticky.affix' => 'background-color'), /* header sticky background color*/
                'navigation_background_color' => array('.main-header.header-7 .primary-menu:not(.affix)' => 'background-color','.main-header.header-8 .primary-menu:not(.affix)' => 'background-color'), /* Navigation background color*/
                'sub_menu_background_color' => array('.main-menu ul.sub-menu' => 'background-color'), /* Sub menu background color */
                'canvas_sidebar_background_color' => array('.canvas-sidebar-wrapper' => 'background-color'), /* Canvas Sidebar Background Color */
                'page_title_background_color' => array('.gf-page-title' => 'background-color'), /* Page Title Background Color */
                'mobile_header_background_color' => array('.mobile-header' => 'background-color'), /* header mobile background color*/
            );
            foreach ($custom_background_color as $key => $value) {
                $color = Spring_Plant()->options()->getOptions($key);
                if (!empty($color)) {
                    foreach ($value as $selector => $property) {
                        $custom_css .= <<<CSS
				{$selector} {
					{$property}: {$color} !important;
				}
CSS;
                    }
                }
            }


            /* Custom scroll */
            $custom_scroll = Spring_Plant()->options()->get_custom_scroll();
            if ($custom_scroll === 'on') {
                $custom_scroll_width = Spring_Plant()->options()->get_custom_scroll_width();
                $custom_scroll_color = Spring_Plant()->options()->get_custom_scroll_color();
                $custom_scroll_thumb_color = Spring_Plant()->options()->get_custom_scroll_thumb_color();

                $custom_css .= <<<CSS
				body::-webkit-scrollbar {
					width: {$custom_scroll_width}px;
					background-color: {$custom_scroll_color};
				}
				body::-webkit-scrollbar-thumb {
				background-color: {$custom_scroll_thumb_color};
				}
CSS;
            }

            /* Custom Padding*/
            $custom_padding = array(
                'top_drawer_padding' => '.top-drawer-content',
                'header_padding' => '.header-inner',
                'mobile_header_padding' => '.mobile-header-inner',
                'content_padding' => '#primary-content'
            );
            $single_post_layout = Spring_Plant()->options()->get_single_post_layout();
            if ($single_post_layout === 'layout-5') {
                $custom_padding['post_single_image_padding'] = '.entry-thumb-single';
            }
            $custom_padding = apply_filters('gsf_custom_padding',$custom_padding);

            foreach ($custom_padding as $optionKey => $selector) {
                $padding = Spring_Plant()->options()->getOptions($optionKey);
                if (is_array($padding)) {
                    $padding_css = '';
                    foreach ($padding as $key => $value) {

                        if ($value !== '') {
                            $padding_css .= <<<CSS
                            padding-{$key}: {$value}px;
CSS;
                        }
                    }
                    if ($padding_css !== '') {
                        $custom_css .= <<<CSS
                        {$selector} {
                            {$padding_css}
                        }
CSS;
                    }
                }

            }

            /* Custom Padding Mobile */
            $header_responsive_breakpoint = Spring_Plant()->options()->get_header_responsive_breakpoint();
            $header_responsive_breakpoint = (int)$header_responsive_breakpoint;
            $custom_padding_mobile = array(
                'mobile_content_padding' => '#primary-content'
            );
            if ($single_post_layout === 'layout-5') {
                $custom_padding['post_single_image_mobile_padding'] = '.entry-thumb-single';
            }
            $custom_padding_mobile = apply_filters('gsf_custom_padding_mobile',$custom_padding_mobile);

            foreach ($custom_padding_mobile as $optionKey => $selector) {
                $padding = Spring_Plant()->options()->getOptions($optionKey);
                if (is_array($padding)) {
                    $padding_css = '';
                    foreach ($padding as $key => $value) {

                        if ($value !== '') {
                            $padding_css .= <<<CSS
                            padding-{$key}: {$value}px;
CSS;
                        }
                    }
                    if ($padding_css !== '') {
                        $custom_css .= <<<CSS
                        @media (max-width: {$header_responsive_breakpoint}px) {
                            {$selector} {
                                {$padding_css}
                            }
                        }

CSS;
                    }
                }

            }

            /* Image Size */
            global $_wp_additional_image_sizes;
            foreach ( get_intermediate_image_sizes() as $_size ) {
                $width = $height = 0;
                if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
                    $width  = get_option( "{$_size}_size_w" );
                    $height = get_option( "{$_size}_size_h" );
                } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
                    $width = $_wp_additional_image_sizes[ $_size ]['width'];
                    $height = $_wp_additional_image_sizes[ $_size ]['height'];
                }
                if ($height > 0 && $width > 0) {
                    $ratio = ($height/$width) * 100;
                    $custom_css .= <<<CSS
                .thumbnail-size-{$_size}:before {
                    padding-bottom: {$ratio}%;
                }
CSS;
                }
            }

            /* Content Block*/
            $content_blocks = array(
                'page_title_content_block',
                'footer_content_block',
                'top_bar_content_block',
                'mobile_top_bar_content_block',
                '404_content_block',
                'top_drawer_content_block',
                'mailchimp_popup_content_block'
            );
            foreach ($content_blocks as $content_block) {
                $contentBlockId = Spring_Plant()->options()->getOptions($content_block);
                if (!empty($contentBlockId)) {
                    wp_enqueue_style( 'js_composer_front' );

                    /**
                     * Post Custom Css
                     */
                    $post_custom_css = get_post_meta($contentBlockId, '_wpb_post_custom_css', true);
                    if (!empty($post_custom_css)) {
                        $custom_css .= $post_custom_css;
                    }

                    /**
                     * Shortcodes Custom Css
                     */
                    $shortcodes_custom_css = get_post_meta($contentBlockId, '_wpb_shortcodes_custom_css', true);
                    if (!empty($shortcodes_custom_css)) {
                        $custom_css .= $shortcodes_custom_css;
                    }
                }
            }

            /* Page Above Content */
            $above_content_margin_bottom = intval(Spring_Plant()->options()->get_above_content_margin_bottom());
            $custom_css .= <<<CSS
                .gf-page-above-content {
                    margin-bottom: {$above_content_margin_bottom}px !important;
                }
CSS;
            /*Canvas overlay*/
            $image_url = Spring_Plant()->themeUrl('assets/images/close.png');
            $custom_css .= <<<CSS
                .canvas-overlay {
                    cursor: url({$image_url}) 15 15, default;
                }
CSS;

            $custom_css = strip_tags($custom_css);
            wp_add_inline_style(Spring_Plant()->helper()->assetsHandle('main'), $custom_css);
        }

        public function getFontFamily($name) {
            if ((strpos($name, ',') === false) || (strpos($name, ' ') === false)) {
                return $name;
            }
            return "'{$name}'";
        }

        public function processFont($fonts) {
            if (isset($fonts['font_weight']) && (($fonts['font_weight'] === '') || ($fonts['font_weight'] === 'regular')) ) {
                $fonts['font_weight'] = '400';
            }

            if (isset($fonts['font_style']) && ($fonts['font_style'] === '') ) {
                $fonts['font_style'] = 'normal';
            }
            return $fonts;
        }

        public function enqueue_block_editor_assets() {
            wp_enqueue_style('font-awesome');
            if (!function_exists('G5P')) {
                $font_url = add_query_arg( 'family', urlencode( 'Lato:100,100i,300,300i,400,400i,700,700i,900,900i|Playfair Display:400,400i,700,700i,900,900i' ), "//fonts.googleapis.com/css" );
                wp_enqueue_style('google_fonts',$font_url);
            }
            if (defined('CSS_DEBUG') && CSS_DEBUG) {
                wp_enqueue_style('gsf_dev_less_to_css_block_editor', admin_url('admin-ajax.php') . '?action=gsf_dev_less_to_css_block_editor');
            } else {
                wp_enqueue_style(Spring_Plant()->helper()->assetsHandle('block-editor'),Spring_Plant()->helper()->getAssetUrl('assets/css/editor-blocks.css'));
            }
            wp_enqueue_style('gsf_custom_css_block_editor', admin_url('admin-ajax.php') . '?action=gsf_custom_css_block_editor');
        }

        public function custom_editor_styles($stylesheets) {
            $stylesheets[] =  Spring_Plant()->helper()->getAssetUrl('assets/vendors/font-awesome/css/font-awesome.css');
            if (!function_exists('G5P')) {
                $stylesheets[] = add_query_arg( 'family', urlencode( 'Lato:100,100i,300,300i,400,400i,700,700i,900,900i|Playfair Display:400,400i,700,700i,900,900i' ), "//fonts.googleapis.com/css" );
            }
            $stylesheets[] = admin_url('admin-ajax.php') . '?action=gsf_custom_css_editor';
            return $stylesheets;
        }

        public function custom_css_editor()
        {

            $custom_css =<<<CSS
            body {
              margin: 9px 10px;
            }
CSS;

            $sidebar_layout = Spring_Plant()->options()->get_sidebar_layout();
            $sidebar_width = Spring_Plant()->options()->get_sidebar_width();


            $custom_sidebar_layout = Spring_Plant()->metaBox()->get_sidebar_layout();
            if (!empty($custom_sidebar_layout)) {
                $sidebar_layout = $custom_sidebar_layout;
            }
            $content_width = 1170;
            $sidebar_text = esc_html__('Sidebar', 'spring-plant');
            if ($sidebar_width === 'large') {
                $sidebar_width = 770;
            } else {
                $sidebar_width = 870;
            }

            $custom_css .= <<<CSS
            
            .mceContentBody::after {
              display: block;
              position: absolute;
              top: 0;
              left: 102%;
              width: 10px;
              -ms-word-break: break-all;
              word-break: break-all;
              font-size: 14px;
              color: #d8d8d8;
              text-align: center;
              height: 100%;
              max-width: 330px;
              z-index: 1;
              text-transform: uppercase;
              font-family: sans-serif;
              font-weight: 600;
              line-height: 26px;
              pointer-events: none;
            }
            
            .mceContentBody.mceContentBody {
              padding-right: 25px !important;
              padding-left: 15px !important;
              border-right: 1px solid #eee;
              position: relative;
              
            }
            .mceContentBody.mceContentBody[data-site_layout="none"] {
                max-width: 1170px;
                
              }
            .mceContentBody.mceContentBody[data-site_layout="none"]:after {
                  content: '';
             }
CSS;
            if ($sidebar_layout !== 'none') {
                $content_width = $sidebar_width;

                $custom_css .= <<<CSS
				.mceContentBody::after {
				    content: '{$sidebar_text}';
				}
CSS;
            }


            $custom_css .= <<<CSS
            body {
              margin: 9px 10px;
            }

			.mceContentBody[data-site_layout="left"],
			.mceContentBody[data-site_layout="right"]{
			    max-width: {$sidebar_width}px;
			}
			
			.mceContentBody[data-site_layout="left"]::after,
			 .mceContentBody[data-site_layout="right"]::after{
				    content: '{$sidebar_text}';
				}

			.mceContentBody {
				max-width: {$content_width}px;
			}
			
CSS;

            /*font*/
            $custom_fonts = array(
                'body_font' => array(
                    'body',
                ),
                'h1_font' => array(
                    'h1'
                ),
                'h2_font' => array(
                    'h2'
                ),
                'h3_font' => array(
                    'h3'
                ),
                'h4_font' => array(
                    'h4'
                ),
                'h5_font' => array(
                    'h5'
                ),
                'h6_font' => array(
                    'h6'
                )
            );

            foreach ($custom_fonts as $optionKey => $selectors) {
                $selector = implode(',', $selectors);
                $fonts = Spring_Plant()->options()->getOptions($optionKey);
                $fonts = $this->processFont($fonts);
                $fonts_attributes = array();
                if (isset($fonts['font_family'])) {
                    $fonts['font_family'] = $this->getFontFamily($fonts['font_family']);
                    $fonts_attributes[] = "font-family: '{$fonts['font_family']}'";
                }

                if (isset($fonts['font_size'])) {
                    $fonts_attributes[] = "font-size: {$fonts['font_size']}";
                }

                if (isset($fonts['font_weight'])) {
                    $fonts_attributes[] = "font-weight: {$fonts['font_weight']}";
                }

                if (isset($fonts['font_style'])) {
                    $fonts_attributes[] = "font-style: {$fonts['font_style']}";
                }

                if (sizeof($fonts_attributes) > 0) {
                    $fonts_css = implode(';', $fonts_attributes);

                    $custom_css .= <<<CSS
                {$selector} {
                    {$fonts_css}
                }
CSS;
                }
            }

            $custom_font_family = array(
                'body_font' => array('.body-font'),
                'primary_font' => array('.primary-font','.has-drop-cap:not(:focus):first-letter')
            );

            foreach ($custom_font_family as $optionKey => $selectors) {
                $selector = implode(',', $selectors);
                $fonts = Spring_Plant()->options()->getOptions($optionKey);
                $fonts = $this->processFont($fonts);
                if (isset($fonts['font_family'])) {
                    $fonts['font_family'] = $this->getFontFamily($fonts['font_family']);
                    $custom_css .= <<<CSS
                {$selector} {
                    font-family: '{$fonts['font_family']}';
                }
CSS;
                }
            }



            // Remove comments
            $custom_css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $custom_css);
            // Remove space after colons
            $custom_css = str_replace(': ', ':', $custom_css);
            // Remove whitespace
            $custom_css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $custom_css);
            return $custom_css;
        }

        public function custom_css_editor_callback() {
            $custom_css = $this->custom_css_editor();

            /**
             * Make sure we set the correct MIME type
             */
            header( 'Content-Type: text/css' );
            /**
             * Render RTL CSS
             */
            echo sprintf('%s',$custom_css);
            die();
        }

        public function custom_css_block_editor() {
            $sidebar_layout = Spring_Plant()->options()->get_sidebar_layout();
            $sidebar_width = Spring_Plant()->options()->get_sidebar_width();


            $custom_sidebar_layout = Spring_Plant()->metaBox()->get_sidebar_layout();
            if (!empty($custom_sidebar_layout)) {
                $sidebar_layout = $custom_sidebar_layout;
            }
            $content_width = 1170 + 28;
            if ($sidebar_width === 'large') {
                $sidebar_width = 770 + 28;
            } else {
                $sidebar_width = 870 + 28;
            }

            $custom_css = '';
            if ($sidebar_layout !== 'none') {
                $content_width = $sidebar_width;
            }
            $custom_css .= <<<CSS
            
            .edit-post-layout__content[data-site_layout="left"] .wp-block,
			.edit-post-layout__content[data-site_layout="right"] .wp-block,
			.edit-post-layout__content[data-site_layout="left"] .wp-block[data-align="wide"],
			.edit-post-layout__content[data-site_layout="right"] .wp-block[data-align="wide"],
			.edit-post-layout__content[data-site_layout="left"] .wp-block[data-align="full"],
			.edit-post-layout__content[data-site_layout="right"] .wp-block[data-align="full"]{
			    max-width: {$sidebar_width}px;
			}
			
			.wp-block[data-align="full"] {
			    margin-left: auto;
			    margin-right: auto;
			}
			
            
            .wp-block,
            .wp-block[data-align="wide"],
             .wp-block[data-align="full"]{
                max-width: {$content_width}px;
            }
			
CSS;
            /*font*/
            $custom_fonts = array(
                'body_font' => array(
                    '.editor-styles-wrapper.editor-styles-wrapper',
                ),
                'h1_font' => array(
                    '.editor-styles-wrapper.editor-styles-wrapper h1'
                ),
                'h2_font' => array(
                    '.editor-styles-wrapper.editor-styles-wrapper h2'
                ),
                'h3_font' => array(
                    '.editor-styles-wrapper.editor-styles-wrapper h3'
                ),
                'h4_font' => array(
                    '.editor-styles-wrapper.editor-styles-wrapper h4'
                ),
                'h5_font' => array(
                    '.editor-styles-wrapper.editor-styles-wrapper h5'
                ),
                'h6_font' => array(
                    '.editor-styles-wrapper.editor-styles-wrapper h6'
                )
            );

            foreach ($custom_fonts as $optionKey => $selectors) {
                $selector = implode(',', $selectors);
                $fonts = Spring_Plant()->options()->getOptions($optionKey);
                $fonts = $this->processFont($fonts);
                $fonts_attributes = array();
                if (isset($fonts['font_family'])) {
                    $fonts['font_family'] = $this->getFontFamily($fonts['font_family']);
                    $fonts_attributes[] = "font-family: '{$fonts['font_family']}'";
                }

                if (isset($fonts['font_size'])) {
                    $fonts_attributes[] = "font-size: {$fonts['font_size']}";
                }

                if (isset($fonts['font_weight'])) {
                    $fonts_attributes[] = "font-weight: {$fonts['font_weight']}";
                }

                if (isset($fonts['font_style'])) {
                    $fonts_attributes[] = "font-style: {$fonts['font_style']}";
                }

                if (sizeof($fonts_attributes) > 0) {
                    $fonts_css = implode(';', $fonts_attributes);

                    $custom_css .= <<<CSS
                {$selector} {
                    {$fonts_css}
                }
CSS;
                }
            }

            $custom_font_family = array(
                'body_font' => array('.editor-styles-wrapper.editor-styles-wrapper .body-font'),
                'primary_font' => array('.editor-styles-wrapper.editor-styles-wrapper .primary-font','.editor-styles-wrapper.editor-styles-wrapper .has-drop-cap:not(:focus):first-letter')
            );

            foreach ($custom_font_family as $optionKey => $selectors) {
                $selector = implode(',', $selectors);
                $fonts = Spring_Plant()->options()->getOptions($optionKey);
                $fonts = $this->processFont($fonts);
                if (isset($fonts['font_family'])) {
                    $fonts['font_family'] = $this->getFontFamily($fonts['font_family']);
                    $custom_css .= <<<CSS
                {$selector} {
                    font-family: '{$fonts['font_family']}';
                }
CSS;
                }
            }

            // Remove comments
            $custom_css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $custom_css);
            // Remove space after colons
            $custom_css = str_replace(': ', ':', $custom_css);
            // Remove whitespace
            $custom_css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $custom_css);

            return $custom_css;
        }

        public function custom_css_block_editor_callback() {
            $custom_css = $this->custom_css_block_editor();

            /**
             * Make sure we set the correct MIME type
             */
            header( 'Content-Type: text/css' );
            /**
             * Render RTL CSS
             */
            echo sprintf('%s',$custom_css);
            die();
        }
    }
}