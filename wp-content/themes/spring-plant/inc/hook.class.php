<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('Spring_Plant_Inc_Hook')) {
	class Spring_Plant_Inc_Hook {
		private static $_instance;
		public static function getInstance()
		{
			if (self::$_instance == NULL) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
		public function init() {
			$this->addAction();
			$this->addFilter();
		}

		private function addAction() {
			/**
			 * Initialize Theme
			 */
			add_action('after_setup_theme', array(Spring_Plant()->themeSetup(), 'init'));

			/**
			 * Required Plugins
			 */
			add_action('tgmpa_register', array(Spring_Plant()->requirePlugin(), 'init'));

			/**
			 * Register Sidebar
			 */
			add_action('widgets_init', array(Spring_Plant()->registerSidebar(), 'init'));

			add_action('init',array(Spring_Plant()->assets(),'registerAssets'));

			/**
			 * Enqueue FrontEnd Resource
			 */
			add_action('wp_enqueue_scripts', array(Spring_Plant()->assets(), 'enqueueAssets'), 99);
            add_action('wp_enqueue_scripts',array(Spring_Plant()->assets(),'enqueue_icon_font'));
            add_action('wp_enqueue_scripts',array(Spring_Plant()->assets(),'getCustomCss'), 100);

            add_action('enqueue_block_editor_assets',array(Spring_Plant()->assets(),'enqueue_block_editor_assets'));

			/**
			 * Head Meta
			 * *******************************************************
			 */
			add_action('wp_head',array(Spring_Plant()->templates(),'head_meta'), 0);

			/**
			 * Social Meta
			 * *******************************************************
			 */
			add_action('wp_head', array(Spring_Plant()->templates(),'social_meta'), 5);

            /**
             * MailChimp Popup
             */
            add_action('spring_plant_after_page_wrapper', array(Spring_Plant()->templates(),'mailchimp_popup'));

			/**
			 * Search Popup
			 * *******************************************************
			 */
			add_action('wp_ajax_nopriv_search_popup', array(Spring_Plant()->ajax(),'search_result'));
			add_action('wp_ajax_search_popup', array(Spring_Plant()->ajax(),'search_result'));

			/**
			 * Load Posts
			 * *******************************************************
			 */
			add_action('wp_ajax_nopriv_pagination_ajax', array(Spring_Plant()->ajax(),'pagination_ajax_response'));
			add_action('wp_ajax_pagination_ajax', array(Spring_Plant()->ajax(),'pagination_ajax_response'));

            /**
             * Login, Register
             */
            add_action('wp_ajax_nopriv_gsf_user_login', array(Spring_Plant()->ajax(), 'gsf_user_login_callback'));
            add_action('wp_ajax_gsf_user_login', array(Spring_Plant()->ajax(), 'gsf_user_login_callback'));
            add_action('wp_ajax_nopriv_gsf_user_login_ajax', array(Spring_Plant()->ajax(), 'gsf_user_login_ajax_callback'));
            add_action('wp_ajax_gsf_user_login_ajax', array(Spring_Plant()->ajax(), 'gsf_user_login_ajax_callback'));
            add_action('wp_ajax_nopriv_gsf_user_sign_up', array(Spring_Plant()->ajax(), 'gsf_user_sign_up_callback'));
            add_action('wp_ajax_gsf_user_sign_up', array(Spring_Plant()->ajax(), 'gsf_user_sign_up_callback'));
            add_action('wp_ajax_nopriv_gsf_user_sign_up_ajax', array(Spring_Plant()->ajax(), 'gsf_user_sign_up_ajax_callback'));
            add_action('wp_ajax_gsf_user_sign_up_ajax', array(Spring_Plant()->ajax(), 'gsf_user_sign_up_ajax_callback'));

            /**
             * Product Quickview
             */
            add_action( 'wp_ajax_nopriv_product_quick_view', array(Spring_Plant()->ajax(),'popup_product_quick_view'));
            add_action( 'wp_ajax_product_quick_view', array(Spring_Plant()->ajax(),'popup_product_quick_view') );

            // Portfolio Show Gallery
            add_action( 'wp_ajax_nopriv_portfolio_gallery', array(Spring_Plant()->ajax(),'portfolio_gallery'));
            add_action( 'wp_ajax_portfolio_gallery', array(Spring_Plant()->ajax(),'portfolio_gallery') );

            /**
			 * Site Loading Template
			 * *******************************************************
			 */
			add_action('spring_plant_before_page_wrapper',array(Spring_Plant()->templates(),'site_loading'),5);

			/**
			 * Top Drawer Template
			 * *******************************************************
			 */
			add_action('spring_plant_before_page_wrapper_content',array(Spring_Plant()->templates(),'top_drawer'),10);

			/**
			 * Header Template
			 * *******************************************************
			 */
			add_action('spring_plant_before_page_wrapper_content',array(Spring_Plant()->templates(),'header'),15);



			/**
			 * Content Wrapper Start Template
			 * *******************************************************
			 */
			add_action('spring_plant_main_wrapper_content_start',array(Spring_Plant()->templates(),'content_wrapper_start'),1);

			/**
			 * Content Wrapper End Template
			 * *******************************************************
			 */
			add_action('spring_plant_main_wrapper_content_end',array(Spring_Plant()->templates(),'content_wrapper_end'),1);

			/**
			 * Back To Top Template
			 * *******************************************************
			 */
			add_action('spring_plant_after_page_wrapper',array(Spring_Plant()->templates(),'back_to_top'),5);

			/**
			 * Page Title Template
			 * *******************************************************
			 */
			add_action('spring_plant_before_main_content',array(Spring_Plant()->templates(),'page_title'),5);

            /**
             * Page Above Content
             */
            add_action('spring_plant_above_content', array(Spring_Plant()->templates(), 'above_content'));

			/**
			 * Footer
			 * *******************************************************
			 */
			add_action('spring_plant_after_page_wrapper_content',array(Spring_Plant()->templates(),'footer'),5);

			/**
			 * Blog
			 * *******************************************************
			 */
			add_action('spring_plant_before_post_image',array(Spring_Plant()->templates(),'zoom_image_thumbnail'));
			add_action('spring_plant_after_archive_wrapper',array(Spring_Plant()->blog(),'pagination_markup'));
			//add_action('spring_plant_before_archive_wrapper',array(Spring_Plant()->blog(),'category_filter_markup'));
			add_action('spring_plant_after_archive_post',array(Spring_Plant()->blog(),'archive_ads_markup'));


			add_action( 'pre_get_posts', array( Spring_Plant()->query(), 'pre_get_posts' ) );

			/**
			 * Single Blog
			 * *******************************************************
			 */
			add_action('spring_plant_after_single_post',array(Spring_Plant()->templates(),'post_single_tag'),5);
			add_action('spring_plant_after_single_post',array(Spring_Plant()->templates(),'post_single_share'),10);
			add_action('spring_plant_after_single_post',array(Spring_Plant()->templates(),'post_single_meta_group'),15);
            add_action('spring_plant_after_single_post',array(Spring_Plant()->templates(),'post_single_author_info'),20);
			add_action('spring_plant_after_single_post',array(Spring_Plant()->templates(),'post_single_navigation'),25);
			add_action('spring_plant_after_single_post',array(Spring_Plant()->templates(),'post_single_related'),30);
			add_action('spring_plant_after_single_post',array(Spring_Plant()->templates(),'post_single_comment'),35);
			//add_action('spring_plant_main_wrapper_content_end',array(Spring_Plant()->templates(),'post_single_comment'),30);
            add_action('wp_footer',array(Spring_Plant()->templates(),'post_single_reading_process'));

			/**
			 * Single Page
			 * *******************************************************
			 */
			add_action('spring_plant_after_single_page',array(Spring_Plant()->templates(),'post_single_comment'),30);

            add_action( 'wp_ajax_gsf_custom_css_editor', array( Spring_Plant()->assets(), 'custom_css_editor_callback' ));
            add_action( 'wp_ajax_nopriv_gsf_custom_css_editor', array( Spring_Plant()->assets(), 'custom_css_editor_callback' ));

            add_action( 'wp_ajax_gsf_custom_css_block_editor', array( Spring_Plant()->assets(), 'custom_css_block_editor_callback' ));
            add_action( 'wp_ajax_nopriv_gsf_custom_css_block_editor', array( Spring_Plant()->assets(), 'custom_css_block_editor_callback' ));

		}

		private function addFilter() {
			// add icon font
			add_filter('gsf_font_icon_assets', array(Spring_Plant()->fontIcons(), 'registerAssets'));
			add_filter('gsf_font_icon_config', array(Spring_Plant()->fontIcons(), 'registerConfig'));

			add_filter('body_class',array(Spring_Plant()->helper(),'body_class'));
			add_filter('get_the_excerpt',array(Spring_Plant()->helper(),'excerpt'),100);
			add_filter('gsf_extra_class',array(Spring_Plant()->helper(),'extra_class'));
			add_filter('widget_categories_args', array(Spring_Plant()->helper(),'widget_categories_args'));
			add_filter('wp_list_categories',array(Spring_Plant()->helper(),'cat_count_span'),10,2);
			add_filter('get_archives_link', array(Spring_Plant()->helper(),'archive_count_span'));

			add_filter('wp_nav_menu_args', array(Spring_Plant()->helper(), 'main_menu_one_page'), 20);
			/*$lazy_load_images = Spring_Plant()->options()->get_lazy_load_images();
			if ($lazy_load_images === 'on') {
				add_filter( 'post_thumbnail_html', array(Spring_Plant()->helper(),'post_thumbnail_lazyLoad'), 10, 3 );
				add_filter('the_content',array(Spring_Plant()->helper(),'content_lazyLoad'));

			}*/

            add_filter('xmenu_submenu_transition', array($this, 'menuTransition'), 20,2);
			add_filter('xmenu_submenu_class',array($this,'subMenuSkin'),10,2);
            add_filter('gpl_spinner_color',array($this,'postLikeSpinnerColor'));

            add_filter( 'editor_stylesheets', array( Spring_Plant()->assets(), 'custom_editor_styles' ), 99 );
		}

		public function menuTransition($transition,$args) {
            if (isset($args->main_menu)) {
                $transition = Spring_Plant()->options()->get_menu_transition();
            }
		    return $transition;
        }


		public function subMenuSkin($classes,$args) {
		    if (isset($args->main_menu)) {
                $sub_menu_skin = Spring_Plant()->options()->get_sub_menu_skin();
                $classes[] = "gf-skin {$sub_menu_skin}";;
            }
			return $classes;
		}

        public function postLikeSpinnerColor() {
            return Spring_Plant()->options()->get_accent_color();
        }
	}
}
