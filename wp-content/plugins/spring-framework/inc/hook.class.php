<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('G5P_Inc_Hook')) {
	class G5P_Inc_Hook {
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
			add_action('init', array($this, 'registerAssets'), 0);
			add_action('wp',array(G5P()->helper(),'getCurrentPreset'),1);
			add_action('wp_enqueue_scripts',array($this,'frontEndAssets'));
			add_action('admin_enqueue_scripts',array($this,'adminAssets'));
			add_action('wp',array($this,'setMetaBoxesToOption'),20);
			add_action('wp',array($this,'setTermMetaToOption'),20);
			add_action('pre_get_posts',array($this,'setPostLayoutToOption'),5);
            add_action('wp',array($this,'setProductLayoutToOption'),5);
            add_action('wp', array($this, 'setPortfolioLayoutToOption'), 5);
			add_action('wp',array($this,'setPostSingleToOption'),20);
            add_action('wp', array($this, 'setPortfolioSingleToOption'), 20);
            add_action('wp', array($this, 'setProductSingleToOption'), 20);
			add_action('wp',array($this,'setPageLayoutToOption'),30);
			add_action( 'wp_ajax_g5plus_install_demo', array(G5P()->core()->dashboard()->installDemo(), 'installDemo') );
			add_action('init',array($this,'allowedPostTags'));
			add_action('pre_get_posts',array($this,'changePostPerPage'),6);

			/* share */
			add_action('spring_plant_post_share',array($this,'socialShare'));
			add_action('spring_plant_single_post_share',array($this,'socialShare'));
            // single product share
            add_action('woocommerce_share',array($this, 'shopSocialShare'));
		}

		private function addFilter() {
			add_filter('gsf_theme_font_default', array($this, 'themeFontsDefault'));
			add_filter('gsf_plugin_url', array($this, 'changeSmartFrameworkUrl'));
			add_filter('gsf_theme_changelog_url',array($this,'get_changelog_url'));
			add_filter('gsf-documentation-url',array($this,'get_documentation_url'));
			add_filter('gsf-video-tutorials-url',array($this,'get_video_tutorials_url'));
		}

		public function get_changelog_url() {
		    return '//themes.g5plus.net/spring/changelog.html';
        }

        public function get_documentation_url() {
		    return '//document.g5plus.net/spring/';
        }

        public function get_video_tutorials_url() {
		    return '#';
        }

		public function changeSmartFrameworkUrl($url) {
			$pluginName = G5P()->pluginName();
			return "{$pluginName}/libs/smart-framework";
		}


		public function themeFontsDefault($fonts) {
			return array(
				array(
					'family' => 'Lato',
					'kind' => 'webfonts#webfont'
				),
				array(
					'family' => "Playfair Display",
					'kind' => 'webfonts#webfont'
				)
			);
		}

		/**
		 * Register assets
		 */
		public function registerAssets() {
			G5P()->assets()->registerScript();
			G5P()->assets()->registerStyle();
		}

		public function frontEndAssets() {
			wp_enqueue_style(G5P()->assetsHandle('admin-bar'));
		}

        public function adminAssets()
        {
            wp_enqueue_style(G5P()->assetsHandle('admin-bar'));
            if ($this->isMetaPost()) {
                wp_enqueue_script(G5P()->assetsHandle('post-format'));
            }
        }

        public function isMetaPost($screen = null) {
            if ( ! ( $screen instanceof WP_Screen ) )
            {
                $screen = get_current_screen();
            }
            return 'post' == $screen->base && ($screen->post_type == 'post');
        }

		public function socialShare($args = array()) {
		    $defaults = array(
                'layout' => 'classic',
                'show_title' => false,
                'page_permalink' => '',
                'page_title' => ''
            );
            $defaults = wp_parse_args($args, $defaults);
            G5P()->helper()->getTemplate('inc/templates/social-share',$defaults);

        }

        public function shopSocialShare() {
		    $this->socialShare(array(
                'post_type' => 'product'
            ));
        }

		public function setMetaBoxesToOption() {
			$postType =  G5P()->configMetaBox()->getPostType();
            if (is_singular($postType) || is_singular('post')) {
				$main_layout =  G5P()->metaBox()->get_main_layout();
				if ($main_layout !== '') {
					G5P()->options()->setOptions('main_layout',$main_layout);
				}

				$content_full_width = G5P()->metaBox()->get_content_full_width();
				if ($content_full_width !== '') {
					G5P()->options()->setOptions('content_full_width',$content_full_width);
				}

				$custom_content_padding =  G5P()->metaBox()->get_custom_content_padding();
				if ($custom_content_padding === 'on') {
					G5P()->options()->setOptions('content_padding',G5P()->metaBox()->get_content_padding());
				}
                $custom_content_padding_mobile = G5P()->metaBox()->get_custom_content_padding_mobile();
                if ($custom_content_padding_mobile === 'on') {
                    G5P()->options()->setOptions('mobile_content_padding', G5P()->metaBox()->get_content_padding_mobile());
                }

				// sidebar layout
				$sidebar_layout = G5P()->metaBox()->get_sidebar_layout();
				if ($sidebar_layout !== '') {
					G5P()->options()->setOptions('sidebar_layout',$sidebar_layout);
				}

				// sidebar
				$sidebar = G5P()->metaBox()->get_sidebar();
				if ($sidebar !== '') {
					G5P()->options()->setOptions('sidebar',$sidebar);
				}

				// Page Above Content
                $above_content_enable = G5P()->metaBox()->get_above_content_enable();
				if('' !== $above_content_enable) {
                    G5P()->options()->setOptions('above_content_enable', ('off' === $above_content_enable) ? '' : $above_content_enable);
                }

                $above_content = G5P()->metaBox()->get_above_content_block();
				if ('' !== $above_content) {
				    G5P()->options()->setOptions('above_content_block', $above_content);
                }

                $above_content_margin_bottom = G5P()->metaBox()->get_above_content_margin_bottom();
				if( intval($above_content_margin_bottom) >= 0) {
                    G5P()->options()->setOptions('above_content_margin_bottom', intval($above_content_margin_bottom));
                }

				$page_title_enable =  G5P()->metaBox()->get_page_title_enable();
				if ($page_title_enable !== '') {
					G5P()->options()->setOptions('page_title_enable',$page_title_enable);
				}

				$page_title_content_block =  G5P()->metaBox()->get_page_title_content_block();
				if ($page_title_content_block !== '') {
					G5P()->options()->setOptions('page_title_content_block',$page_title_content_block);
				}
			}
		}

		public function setTermMetaToOption() {
			$taxonomy =  G5P()->configTermMeta()->getTaxonomy();
			if ((in_array('category',$taxonomy) && is_category()) ||  is_tax($taxonomy)) {
				$term = get_queried_object();
				if ($term && property_exists($term, 'term_id')) {
					$term_id = $term->term_id;

					$page_title_enable =  G5P()->termMeta()->get_page_title_enable($term_id);
					if ($page_title_enable !== '') {
						G5P()->options()->setOptions('page_title_enable',$page_title_enable);
					}

					$page_title_content_block =  G5P()->termMeta()->get_page_title_content_block($term_id);
					if ($page_title_content_block !== '') {
						G5P()->options()->setOptions('page_title_content_block',$page_title_content_block);
					}
				}
			}
		}

		public function setPostSingleToOption() {
			if (is_singular('post')) {
				$single_post_layout = G5P()->metaBoxPost()->get_single_post_layout();
				if ($single_post_layout !== '') {
					G5P()->options()->setOptions('single_post_layout',$single_post_layout);
				}
                $single_image_padding_enable = G5P()->metaBoxPost()->get_custom_single_image_padding();
                if ($single_image_padding_enable !== '') {
                    $single_image_padding = G5P()->metaBoxPost()->get_post_single_image_padding();
                    G5P()->options()->setOptions('post_single_image_padding',$single_image_padding);
                }
                $single_image_mobile_padding_enable = G5P()->metaBoxPost()->get_custom_single_image_mobile_padding();
                if ($single_image_mobile_padding_enable !== '') {
                    $single_image_mobile_padding = G5P()->metaBoxPost()->get_post_single_image_mobile_padding();
                    G5P()->options()->setOptions('post_single_image_mobile_padding',$single_image_mobile_padding);
                }
                $single_reading_process_enable = G5P()->metaBoxPost()->get_single_reading_process_enable();
                if ($single_reading_process_enable !== '') {
                    G5P()->options()->setOptions('single_reading_process_enable',$single_reading_process_enable);
                }
				$single_tag_enable = G5P()->metaBoxPost()->get_single_tag_enable();
                if ($single_tag_enable !== '') {
                    G5P()->options()->setOptions('single_tag_enable',$single_tag_enable);
                }
                $single_share_enable = G5P()->metaBoxPost()->get_single_share_enable();
                if ($single_share_enable !== '') {
                    G5P()->options()->setOptions('single_share_enable',$single_share_enable);
                }
                $single_navigation_enable = G5P()->metaBoxPost()->get_single_navigation_enable();
                if ($single_navigation_enable !== '') {
                    G5P()->options()->setOptions('single_navigation_enable',$single_navigation_enable);
                }
                $single_author_info_enable = G5P()->metaBoxPost()->get_single_author_info_enable();
                if ($single_author_info_enable !== '') {
                    G5P()->options()->setOptions('single_author_info_enable',$single_author_info_enable);
                }

                // Related
                $single_related_post_enable = G5P()->metaBoxPost()->get_single_related_post_enable();
                if ($single_related_post_enable !== '') {
                    G5P()->options()->setOptions('single_related_post_enable',$single_related_post_enable);
                }
                $single_related_post_algorithm = G5P()->metaBoxPost()->get_single_related_post_algorithm();
                if ($single_related_post_algorithm !== '') {
                    G5P()->options()->setOptions('single_related_post_algorithm',$single_related_post_algorithm);
                }
                $single_related_post_carousel_enable = G5P()->metaBoxPost()->get_single_related_post_carousel_enable();
                if ($single_related_post_carousel_enable !== '') {
                    G5P()->options()->setOptions('single_related_post_carousel_enable',$single_related_post_carousel_enable);
                }
                $single_related_post_per_page = G5P()->metaBoxPost()->get_single_related_post_per_page();
                if ($single_related_post_per_page !== '') {
                    G5P()->options()->setOptions('single_related_post_per_page',$single_related_post_per_page);
                }
                $single_related_post_columns_gutter = G5P()->metaBoxPost()->get_single_related_post_columns_gutter();
                if ($single_related_post_columns_gutter !== '') {
                    G5P()->options()->setOptions('single_related_post_columns_gutter',$single_related_post_columns_gutter);
                }
                $single_related_post_columns = G5P()->metaBoxPost()->get_single_related_post_columns();
                if ($single_related_post_columns !== '') {
                    G5P()->options()->setOptions('single_related_post_columns',$single_related_post_columns);
                }

                $single_related_post_columns_md = G5P()->metaBoxPost()->get_single_related_post_columns_md();
                if ($single_related_post_columns_md !== '') {
                    G5P()->options()->setOptions('single_related_post_columns_md',$single_related_post_columns_md);
                }
                $single_related_post_columns_sm = G5P()->metaBoxPost()->get_single_related_post_columns_sm();
                if ($single_related_post_columns_sm !== '') {
                    G5P()->options()->setOptions('single_related_post_columns_sm',$single_related_post_columns_sm);
                }
                $single_related_post_columns_xs = G5P()->metaBoxPost()->get_single_related_post_columns_xs();
                if ($single_related_post_columns_xs !== '') {
                    G5P()->options()->setOptions('single_related_post_columns_xs',$single_related_post_columns_xs);
                }
                $single_related_post_columns_mb = G5P()->metaBoxPost()->get_single_related_post_columns_mb();
                if ($single_related_post_columns_mb !== '') {
                    G5P()->options()->setOptions('single_related_post_columns_mb',$single_related_post_columns_mb);
                }
                $single_related_post_paging = G5P()->metaBoxPost()->get_single_related_post_paging();
                if ($single_related_post_paging !== '') {
                    G5P()->options()->setOptions('single_related_post_paging',$single_related_post_paging);
                }
                $single_related_post_animation = G5P()->metaBoxPost()->get_single_related_post_animation();
                if ($single_related_post_animation !== '') {
                    G5P()->options()->setOptions('single_related_post_animation',$single_related_post_animation);
                }
			}
		}

        public function setPortfolioSingleToOption()
        {
            if (is_singular('portfolio')) {
                $prefix = G5P()->getMetaPrefix();
                $portfolio_layout = G5P()->metaBoxPortfolio()->getMetaValue("{$prefix}single_portfolio_layout");
                if ('' !== $portfolio_layout) {
                    $configs_Single = array(
                        'single_portfolio_layout',
                        'single_portfolio_gallery_layout',
                        'single_portfolio_gallery_image_size',
                        'single_portfolio_gallery_image_ratio',
                        'single_portfolio_gallery_image_ratio_custom',
                        'single_portfolio_gallery_image_width',
                        'single_portfolio_gallery_columns_gutter',
                        'single_portfolio_gallery_columns',
                        'single_portfolio_gallery_columns_md',
                        'single_portfolio_gallery_columns_sm',
                        'single_portfolio_gallery_columns_xs',
                        'single_portfolio_gallery_columns_mb'
                    );
                    foreach ($configs_Single as $config) {
                        $value = G5P()->metaBoxPortfolio()->getMetaValue("{$prefix}{$config}");
                        if ($value !== '') {
                            G5P()->options()->setOptions($config, $value);
                        }
                    }
                }
                $configs_Single = array(
                    'single_portfolio_related_enable',
                    'single_portfolio_related_full_width_enable',
                    'single_portfolio_related_algorithm',
                    'single_portfolio_related_carousel_enable',
                    'single_portfolio_related_per_page',
                    'single_portfolio_related_columns_gutter',
                    'single_portfolio_related_columns',
                    'single_portfolio_related_columns_md',
                    'single_portfolio_related_columns_sm',
                    'single_portfolio_related_columns_xs',
                    'single_portfolio_related_columns_mb',
                    'single_portfolio_related_post_paging',
                    'single_portfolio_related_animation',
                );
                foreach ($configs_Single as $config) {
                    $value = G5P()->metaBoxPortfolio()->getMetaValue("{$prefix}{$config}");
                    if ($value !== '') {
                        G5P()->options()->setOptions($config, $value);
                    }
                }
            }
        }

        public function setProductSingleToOption()
        {
            if (is_singular('product')) {
                $product_layout = G5P()->metaBoxProduct()->get_product_single_layout();
                if ($product_layout !== '') {
                    G5P()->options()->setOptions('product_single_layout',$product_layout);
                }
                $prefix = G5P()->getMetaPrefix();
                $configs_Single = array(
                    'product_single_layout',
                    'product_related_enable',
                    'product_related_algorithm',
                    'product_related_item_skin',
                    'product_related_carousel_enable',
                    'product_related_columns_gutter',
                    'product_related_columns',
                    'product_related_columns_md',
                    'product_related_columns_sm',
                    'product_related_columns_xs',
                    'product_related_columns_mb',
                    'product_related_per_page',
                    'product_related_animation'
                );
                foreach ($configs_Single as $config) {
                    $value = G5P()->metaBoxProduct()->getMetaValue("{$prefix}{$config}");
                    if ($value !== '') {
                        G5P()->options()->setOptions($config, $value);
                    }
                }
            }
        }

        public function changePostPerPage($q) {
			$post_type  = $q->get('post_type');
			if (!is_admin() && $q->is_main_query() &&  ($q->is_home() || $q->is_category() || $q->is_tag() || ($q->is_archive() && $post_type == 'post') || ($q->is_search() && ((is_array($post_type) && in_array('post',$post_type) || $post_type == 'post'))))) {
				$posts_per_page = intval(G5P()->options()->get_posts_per_page());
				$custom_posts_per_page = isset($_GET['posts_per_page']) ? $_GET['posts_per_page'] : '';
				if (!empty($custom_posts_per_page)) {
					$posts_per_page = $custom_posts_per_page;
				}
				if (!empty($posts_per_page)) {
                    $q->set( 'posts_per_page', $posts_per_page);
				}
			}
		}

		public function setPostLayoutToOption(){
            if (is_admin()) return;
			global $post;
			$post_type = get_post_type($post);
			$options = &GSF()->adminThemeOption()->getOptions(G5P()->getOptionName());
			$custom_post_layout_settings = G5P()->settings()->get_custom_post_layout_settings();
			foreach ($custom_post_layout_settings as $key => $value) {
				if((($key === 'search') && is_search() && !is_admin()) ||
					(($key === 'category') && is_category())
				){
					$settings = array(
						'post_layout',
						'post_columns_gutter',
						'post_columns',
						'post_columns_md',
						'post_columns_sm',
						'post_columns_xs',
                        'post_columns_mb',
						'posts_per_page',
						'post_paging',
						'post_animation'
					);

					foreach ($settings as $setting) {
						$setting_value = G5P()->options()->getOptions("{$key}_{$setting}");
						if ($setting_value !== '') {
							$options[$setting] = $setting_value;
						}
					}
					break;
				}
			}

			// custom param
			if (((is_home() || is_category() || is_tag() || is_archive()) && ($post_type == 'post')) || (is_search() && !is_admin())) {
				$post_layout = isset($_GET['post_layout']) ?  $_GET['post_layout'] : '';
				if (array_key_exists($post_layout,G5P()->settings()->get_post_layout())){
					G5P()->options()->setOptions('post_layout',$post_layout);
					switch($post_layout) {
                        case 'grid':
                            G5P()->options()->setOptions('post_image_size','480x480');
                            break;
                        case 'medium-image':
                            G5P()->options()->setOptions('post_image_size','600x330');
                            break;
						case 'list':
							G5P()->options()->setOptions('post_image_size','600x330');
							break;
						case 'zigzac':
							G5P()->options()->setOptions('post_image_size','960x580');
							break;
                        default:
                            break;
                    }
				}
                $post_item_skin = isset($_GET['post_item_skin']) ? $_GET['post_item_skin'] : '';
                if (array_key_exists($post_item_skin, G5P()->settings()->get_post_item_skin())) {
                    G5P()->options()->setOptions('post_item_skin', $post_item_skin);
                }
				$post_columns = isset($_GET['post_columns']) ?  $_GET['post_columns'] : '';
				if (in_array($post_columns, array('1', '2', '3', '4', '5', '6'))){
					G5P()->options()->setOptions('post_columns',$post_columns);
				}
				$post_paging = isset($_GET['post_paging']) ?  $_GET['post_paging'] : '';
				if (array_key_exists($post_paging, G5P()->settings()->get_post_paging_mode())){
					G5P()->options()->setOptions('post_paging',$post_paging);
				}
				$post_columns_gutter = isset($_GET['post_columns_gutter']) ?  $_GET['post_columns_gutter'] : '';
				if (array_key_exists($post_columns_gutter, G5P()->settings()->get_post_columns_gutter())){
					G5P()->options()->setOptions('post_columns_gutter',$post_columns_gutter);
				}
			}
        }

        public function setProductLayoutToOption(){
            if (!is_admin() && is_main_query() && (is_post_type_archive( 'product' ) || is_tax( get_object_taxonomies( 'product' )))) {
                $product_catalog_layout = G5P()->options()->get_product_catalog_layout();
                if (isset($_COOKIE['product_layout']) && !empty($_COOKIE['product_layout']) && in_array($_COOKIE['product_layout'], array('grid', 'list')) && in_array($product_catalog_layout, array('grid', 'list'))) {
                    G5P()->options()->setOptions('product_catalog_layout', $_COOKIE['product_layout']);
                }
                $product_catalog_layout = isset($_GET['product_layout']) ?  $_GET['product_layout'] : '';
                if (array_key_exists($product_catalog_layout,G5P()->settings()->get_product_catalog_layout())){
                    G5P()->options()->setOptions('product_catalog_layout',$product_catalog_layout);
                }
                $product_columns = isset($_GET['product_columns']) ?  $_GET['product_columns'] : '';
                if (in_array($product_columns, array('1', '2', '3', '4', '5', '6'))){
                    G5P()->options()->setOptions('product_columns',$product_columns);
                }
                $product_paging = isset($_GET['product_paging']) ?  $_GET['product_paging'] : '';
                if (array_key_exists($product_paging, G5P()->settings()->get_post_paging_mode())){
                    G5P()->options()->setOptions('product_paging',$product_paging);
                }
                $product_animation = isset($_GET['product_animation']) ?  $_GET['product_animation'] : '';
                if (array_key_exists($product_animation, G5P()->settings()->get_animation())){
                    G5P()->options()->setOptions('product_animation',$product_animation);
                }
                $product_columns_gutter = isset($_GET['product_columns_gutter']) ?  $_GET['product_columns_gutter'] : '';
                if (array_key_exists($product_columns_gutter, G5P()->settings()->get_post_columns_gutter())){
                    G5P()->options()->setOptions('product_columns_gutter',$product_columns_gutter);
                }
            }

            if (!is_admin() && is_main_query() && (is_singular( 'product' ))) {
                $product_single_layout = isset($_GET['product_single_layout']) ?  $_GET['product_single_layout'] : '';
                if (array_key_exists($product_single_layout,G5P()->settings()->get_product_single_layout())){
                    G5P()->options()->setOptions('product_single_layout',$product_single_layout);
                }
            }
        }
        public function setPortfolioLayoutToOption()
        {
            if (!is_admin() && is_main_query() && (is_post_type_archive('portfolio') || is_tax(get_object_taxonomies('portfolio_cat')))) {
                $portfolio_layout = isset($_GET['portfolio_layout']) ? $_GET['portfolio_layout'] : '';
                if (array_key_exists($portfolio_layout, G5P()->settings()->get_portfolio_layout())) {
                    G5P()->options()->setOptions('portfolio_layout', $portfolio_layout);
                    $portfolio_image_size = '';
                    $portfolio_image_width = '';
                    $portfolio_paging = '';
                    $portfolio_image_masonry = array();
                    switch ($portfolio_layout) {
                        case 'grid':
                            $portfolio_image_size = '480x600';
                            break;
                        case 'masonry':
                            $portfolio_image_width = '400';
                            $portfolio_image_masonry = array(
                                'width' =>$portfolio_image_width,
                                'height' => ''
                            );
                            break;
                        case 'metro-1':
                        case 'metro-2':
                        case 'metro-3':
                        case 'metro-4':
                        case 'metro-5':
                        case 'metro-6':
                        case 'metro-9':
                            $portfolio_image_size = '480x480';
                            break;
                        case 'metro-7':
                            $portfolio_image_size = '270x310';
                            break;
                        case 'metro-8':
                            $portfolio_image_size = '485x370';
                            break;
                        case 'metro-10':
                            $portfolio_image_size = '485x370';
                            break;
                        case 'metro-11':
                            $portfolio_image_size = '450x550';
                            break;
                        case 'carousel-3d':
                            $portfolio_image_size = '804x468';
                            $portfolio_paging = 'none';
                            break;
                    }
                    if (!empty($portfolio_image_size)) {
                        G5P()->options()->setOptions('portfolio_image_size', $portfolio_image_size);
                    }
                    if (!empty($portfolio_image_width)) {
                        G5P()->options()->setOptions('portfolio_image_width', $portfolio_image_masonry);
                    }
                    if (!empty($portfolio_paging)) {
                        G5P()->options()->setOptions('portfolio_paging', $portfolio_paging);
                    }
                }
                $portfolio_item_skin = isset($_GET['portfolio_item_skin']) ? $_GET['portfolio_item_skin'] : '';
                if (array_key_exists($portfolio_item_skin, G5P()->settings()->get_portfolio_item_skin())) {
                    G5P()->options()->setOptions('portfolio_item_skin', $portfolio_item_skin);
                }

                $hover_color_scheme = isset($_GET['hover_color_scheme']) ? $_GET['hover_color_scheme'] : '';
                if (in_array($hover_color_scheme, array('portfolio-hover-accent', 'portfolio-hover-dark', 'portfolio-hover-light'))) {
                    G5P()->options()->setOptions('portfolio_hover_color_scheme', $hover_color_scheme);
                }
                $portfolio_columns = isset($_GET['portfolio_columns']) ? $_GET['portfolio_columns'] : '';
                if (in_array($portfolio_columns, array('1', '2', '3', '4', '5', '6'))) {
                    G5P()->options()->setOptions('portfolio_columns', $portfolio_columns);
                }
                $portfolio_paging = isset($_GET['portfolio_paging']) ? $_GET['portfolio_paging'] : '';
                if (array_key_exists($portfolio_paging, G5P()->settings()->get_post_paging_mode())) {
                    G5P()->options()->setOptions('portfolio_paging', $portfolio_paging);
                }
                $portfolio_columns_gutter = isset($_GET['portfolio_columns_gutter']) ? $_GET['portfolio_columns_gutter'] : '';
                if (array_key_exists($portfolio_columns_gutter, G5P()->settings()->get_post_columns_gutter())) {
                    G5P()->options()->setOptions('portfolio_columns_gutter', $portfolio_columns_gutter);
                }
            }

        }

		public function setPageLayoutToOption() {
            $main_layout = isset($_GET['main_layout']) ?  $_GET['main_layout'] : '';
            if (array_key_exists($main_layout,G5P()->settings()->get_main_layout())){
                G5P()->options()->setOptions('main_layout',$main_layout);
            }

            $sidebar_layout = isset($_GET['sidebar_layout']) ?  $_GET['sidebar_layout'] : '';
            if (array_key_exists($sidebar_layout,G5P()->settings()->get_sidebar_layout())){
                G5P()->options()->setOptions('sidebar_layout',$sidebar_layout);
            }

            $content_full_width = isset($_GET['content_full_width']) ?  $_GET['content_full_width'] : '';
            if ($content_full_width != ''){
                G5P()->options()->setOptions('content_full_width',$content_full_width);
            }
			$remove_content_padding = isset($_GET['remove_content_padding']) ?  $_GET['remove_content_padding'] : '';
			if ($remove_content_padding == 'on'){
				G5P()->options()->setOptions('content_padding',array ( 'left' => 0, 'right' => 0, 'top' => 0, 'bottom' => 0));
			}

            $page_title_enable = isset($_GET['page_title_enable']) ?  $_GET['page_title_enable'] : '';
            if (in_array($page_title_enable, array('off', 'on'))){
                G5P()->options()->setOptions('page_title_enable', ($page_title_enable == 'off') ? '' : $page_title_enable);
            }

            $header_float_enable = isset($_GET['header_float_enable']) ?  $_GET['header_float_enable'] : '';
            if (in_array($header_float_enable, array('off', 'on'))){
                G5P()->options()->setOptions('header_float_enable', ($header_float_enable == 'off') ? '' : $header_float_enable);
            }
            $header_border = isset($_GET['header_border']) ?  $_GET['header_border'] : '';
            if (array_key_exists($header_border, G5P()->settings()->get_border_layout())){
                G5P()->options()->setOptions('header_border', $header_border);
            }
        }

		public function allowedPostTags() {
			global $allowedposttags;
			$allowedposttags['a']['data-hash'] = true;
			$allowedposttags['a']['data-product_id'] = true;
			$allowedposttags['a']['data-original-title'] = true;
			$allowedposttags['a']['aria-describedby'] = true;
			$allowedposttags['a']['data-quantity'] = true;
			$allowedposttags['a']['data-product_sku'] = true;
			$allowedposttags['a']['data-rel'] = true;
			$allowedposttags['a']['data-product-type'] = true;
			$allowedposttags['a']['data-product-id'] = true;
			$allowedposttags['a']['data-toggle'] = true;

			$allowedposttags['div']['data-owl-options'] = true;
			$allowedposttags['div']['data-plugin-options'] = true;
			$allowedposttags['div']['data-player'] = true;
			$allowedposttags['div']['data-audio'] = true;
			$allowedposttags['div']['data-title'] = true;
			$allowedposttags['div']['data-animsition-in-class'] = true;
			$allowedposttags['div']['data-animsition-out-class'] = true;
			$allowedposttags['div']['data-animsition-overlay'] = true;

			$allowedposttags['textarea']['placeholder'] = true;

			$allowedposttags['iframe']['align'] = true;
			$allowedposttags['iframe']['frameborder'] = true;
			$allowedposttags['iframe']['height'] = true;
			$allowedposttags['iframe']['longdesc'] = true;
			$allowedposttags['iframe']['marginheight'] = true;
			$allowedposttags['iframe']['marginwidth'] = true;
			$allowedposttags['iframe']['name'] = true;
			$allowedposttags['iframe']['sandbox'] = true;
			$allowedposttags['iframe']['scrolling'] = true;
			$allowedposttags['iframe']['seamless'] = true;
			$allowedposttags['iframe']['src'] = true;
			$allowedposttags['iframe']['srcdoc'] = true;
			$allowedposttags['iframe']['width'] = true;
			$allowedposttags['iframe']['defer'] = true;

			$allowedposttags['input']['accept'] = true;
			$allowedposttags['input']['align'] = true;
			$allowedposttags['input']['alt'] = true;
			$allowedposttags['input']['autocomplete'] = true;
			$allowedposttags['input']['autofocus'] = true;
			$allowedposttags['input']['checked'] = true;
			$allowedposttags['input']['class'] = true;
			$allowedposttags['input']['disabled'] = true;
			$allowedposttags['input']['form'] = true;
			$allowedposttags['input']['formaction'] = true;
			$allowedposttags['input']['formenctype'] = true;
			$allowedposttags['input']['formmethod'] = true;
			$allowedposttags['input']['formnovalidate'] = true;
			$allowedposttags['input']['formtarget'] = true;
			$allowedposttags['input']['height'] = true;
			$allowedposttags['input']['list'] = true;
			$allowedposttags['input']['max'] = true;
			$allowedposttags['input']['maxlength'] = true;
			$allowedposttags['input']['min'] = true;
			$allowedposttags['input']['multiple'] = true;
			$allowedposttags['input']['name'] = true;
			$allowedposttags['input']['pattern'] = true;
			$allowedposttags['input']['placeholder'] = true;
			$allowedposttags['input']['readonly'] = true;
			$allowedposttags['input']['required'] = true;
			$allowedposttags['input']['size'] = true;
			$allowedposttags['input']['src'] = true;
			$allowedposttags['input']['step'] = true;
			$allowedposttags['input']['type'] = true;
			$allowedposttags['input']['value'] = true;
			$allowedposttags['input']['width'] = true;
			$allowedposttags['input']['accesskey'] = true;
			$allowedposttags['input']['class'] = true;
			$allowedposttags['input']['contenteditable'] = true;
			$allowedposttags['input']['contextmenu'] = true;
			$allowedposttags['input']['dir'] = true;
			$allowedposttags['input']['draggable'] = true;
			$allowedposttags['input']['dropzone'] = true;
			$allowedposttags['input']['hidden'] = true;
			$allowedposttags['input']['id'] = true;
			$allowedposttags['input']['lang'] = true;
			$allowedposttags['input']['spellcheck'] = true;
			$allowedposttags['input']['style'] = true;
			$allowedposttags['input']['tabindex'] = true;
			$allowedposttags['input']['title'] = true;
			$allowedposttags['input']['translate'] = true;

			$allowedposttags['span']['data-id'] = true;
		}

	}
}