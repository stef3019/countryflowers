<?php
/**
 * Class Defined Templates
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
if (!class_exists('Spring_Plant_Inc_Templates')) {
	class Spring_Plant_Inc_Templates {

		private static $_instance;
		public static function getInstance()
		{
			if (self::$_instance == NULL) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Template Site Loading
		 */
		public function site_loading() {
			Spring_Plant()->helper()->getTemplate('site-loading');
		}

		/**
		 * Template Top Drawer
		 */
		public function top_drawer() {
			Spring_Plant()->helper()->getTemplate('top-drawer');
		}

		/**
		 * Template Header
		 */
		public function header() {
			Spring_Plant()->helper()->getTemplate('header');
		}

		/**
		 * Template Search Popup
		 */
		public function search_popup() {
			Spring_Plant()->helper()->getTemplate('popup/search');
		}

		/**
		 * Template Canvas Sidebar
		 */
		public function canvas_sidebar() {
			Spring_Plant()->helper()->getTemplate('canvas-sidebar');
		}

        /**
         * Template Canvas Menu
         */
        public function canvas_menu() {
            Spring_Plant()->helper()->getTemplate('canvas-menu');
        }

		/**
		 * Template Content Wrapper Start
		 */
		public function content_wrapper_start() {
			Spring_Plant()->helper()->getTemplate('global/wrapper-start');
		}

		/**
		 * Template Content Wrapper End
		 */
		public function content_wrapper_end() {
			Spring_Plant()->helper()->getTemplate('global/wrapper-end');
		}

		/**
		 * Template Back To Top
		 */
		public function back_to_top() {
			Spring_Plant()->helper()->getTemplate('back-to-top');
		}

		/**
		 * Template Page Title
		 */
		public function page_title() {
			Spring_Plant()->helper()->getTemplate('page-title');
		}

        /**
         * Template Above Content
         */
        public function above_content() {
            $above_content_enable = Spring_Plant()->options()->get_above_content_enable();
            if('on' === $above_content_enable) {
                $above_content = Spring_Plant()->options()->get_above_content_block();
                if(!empty($above_content)) {
                    echo '<div class="gf-page-above-content">';
                    echo Spring_Plant()->helper()->content_block($above_content);
                    echo '</div>';
                }
            }
        }

		/**
		 * Head Meta
		 */
		public function head_meta() {
			Spring_Plant()->helper()->getTemplate('head/head-meta');
		}

		/**
		 * Social Meta
		 */
		public function social_meta() {
			Spring_Plant()->helper()->getTemplate('head/social-meta');
		}

        /**
         * mailchimp_popup
         */
        public function mailchimp_popup() {
            Spring_Plant()->helper()->getTemplate('popup/mailchimp');
        }

		/**
		 * Footer
		 */
		public function footer() {
			Spring_Plant()->helper()->getTemplate('footer');
		}

		/**
		 * Get Template Social Network
		 * @param  string $social_text
		 * @param array $social_networks
		 * @param string $layout - The layout of social network. Accepts 'classic', 'circle', 'square'
		 */
		public function social_networks($social_networks = array(),$layout = 'classic',$size = 'normal', $social_text = '') {
			Spring_Plant()->helper()->getTemplate('social-networks',array('social_networks' => $social_networks, 'layout' => $layout, 'size' => $size, 'social_text' => $social_text));
		}


		public function zoom_image_thumbnail($args)
		{
			Spring_Plant()->helper()->getTemplate('loop/zoom-image', $args);
		}


		public function post_single_tag() {
			Spring_Plant()->helper()->getTemplate('single/post-tag');
		}

		public function post_single_share() {
			Spring_Plant()->helper()->getTemplate('single/post-share');
		}

		public function post_single_navigation(){
			Spring_Plant()->helper()->getTemplate('single/post-navigation');
		}

		public function post_single_author_info() {
			Spring_Plant()->helper()->getTemplate('single/post-author-info');
		}
		public function post_single_meta_group() {
			Spring_Plant()->helper()->getTemplate('single/post-meta-group');
		}

		public function post_single_related() {
			Spring_Plant()->helper()->getTemplate('single/post-related');
		}

		public function post_single_comment(){
			Spring_Plant()->helper()->getTemplate('single/post-comment');
		}

		public function post_single_image() {
			Spring_Plant()->helper()->getTemplate('single/post-image');
		}
		public function post_single_full_image() {
			Spring_Plant()->helper()->getTemplate('single/post-full-image');
		}

		public function mobile_navigation() {
			Spring_Plant()->helper()->getTemplate('header/mobile/navigation');
		}

		public function canvas_overlay() {
			Spring_Plant()->helper()->getTemplate('canvas-overlay');
		}

        public function post_view() {
            Spring_Plant()->helper()->getTemplate('loop/post-view');
        }

        public function post_like() {
            Spring_Plant()->helper()->getTemplate('loop/post-like');
        }

        /**
         * Template Canvas Filter
         */
        public function canvas_filter() {
            Spring_Plant()->helper()->getTemplate('woocommerce/loop/canvas-woocommerce-filter');
        }


		public function userSocialNetworks($userId,$layout = '') {
			Spring_Plant()->helper()->getTemplate('user-social-networks', array('userId' => $userId,'layout' => $layout));
		}

        public function post_single_reading_process() {
            Spring_Plant()->helper()->getTemplate('single/post-reading-process');
        }

        public function shop_catalog_filter() {
            Spring_Plant()->helper()->getTemplate('woocommerce/loop/catalog-filter');
        }

        public function shop_loop_product_title() {
            Spring_Plant()->helper()->getTemplate('woocommerce/loop/title' );
        }

        public function shop_loop_product_cat() {
            Spring_Plant()->helper()->getTemplate('woocommerce/loop/product-cat' );
        }

        function shop_loop_quick_view() {
            Spring_Plant()->helper()->getTemplate( 'woocommerce/loop/quick-view' );
        }

        public function shop_loop_compare() {
            if ((in_array('yith-woocommerce-compare/init.php', apply_filters('active_plugins', get_option('active_plugins')))
                    || in_array('yith-woocommerce-compare-premium/init.php', apply_filters('active_plugins', get_option('active_plugins'))))
                && get_option('yith_woocompare_compare_button_in_products_list') == 'yes') {
                if (!shortcode_exists('yith_compare_button') && class_exists('YITH_Woocompare') && function_exists('yith_woocompare_constructor')) {
                    $context = isset($_REQUEST['context']) ? $_REQUEST['context'] : null;
                    $_REQUEST['context'] = 'frontend';
                    yith_woocompare_constructor();
                    $_REQUEST['context'] = $context;
                }


                global $yith_woocompare;
                if ( isset($yith_woocompare) && isset($yith_woocompare->obj)) {
                    remove_action( 'woocommerce_after_shop_loop_item', array($yith_woocompare->obj,'add_compare_link'), 20 );
                }

                echo do_shortcode('[yith_compare_button container="false" type="link"]');
            }
        }

        public function shop_loop_add_to_cart(){
            $product_add_to_cart_enable = Spring_Plant()->options()->get_product_add_to_cart_enable();
            $accent_color = Spring_Plant()->options()->get_accent_color();
            if ('on' === $product_add_to_cart_enable) {
                global $product;
                echo '<div class="product-action-item add_to_cart_tooltip" data-toggle="tooltip" data-original-title="'. esc_attr($product->add_to_cart_text()) .'">';
                woocommerce_template_loop_add_to_cart(array(
                    'attributes' => array(
                        'data-product_id'  => $product->get_id(),
                        'data-product_sku' => $product->get_sku(),
                        'aria-label'       => $product->add_to_cart_description(),
                        'rel'              => 'nofollow',
                        'data-style' => "zoom-in",
                        'data-spinner-size' => "25",
                        'data-spinner-color' => $accent_color
                    ),
                    'class'    => implode( ' ', array_filter( array(
                        'ladda-button',
                        'product_type_' . $product->get_type(),
                        $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : 'product_out_of_stock',
                        $product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : ''
                    ) ) )
                ));
                echo '</div>';
            }
        }

        public function shop_loop_list_add_to_cart(){
            $product_add_to_cart_enable = Spring_Plant()->options()->get_product_add_to_cart_enable();
            $accent_color = Spring_Plant()->options()->get_accent_color();
            if ('on' === $product_add_to_cart_enable) {
                global $product;
                echo '<div class="product-action-item">';
                woocommerce_template_loop_add_to_cart(array(
                    'attributes' => array(
                        'data-product_id'  => $product->get_id(),
                        'data-product_sku' => $product->get_sku(),
                        'aria-label'       => $product->add_to_cart_description(),
                        'rel'              => 'nofollow',
                        'data-style' => "zoom-in",
                        'data-spinner-size' => "25",
                        'data-spinner-color' => $accent_color
                    ),
                    'class'    => implode( ' ', array_filter( array(
                        'ladda-button add_to_cart_list',
                        'product_type_' . $product->get_type(),
                        $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : 'product_out_of_stock',
                        $product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : ''
                    ) ) )
                ));
                echo '</div>';
            }
        }

        public function shop_loop_sale_count_down() {
            Spring_Plant()->helper()->getTemplate('woocommerce/loop/sale-count-down', array('is_single'=> false));
        }

        public function shop_single_loop_sale_count_down() {
            Spring_Plant()->helper()->getTemplate('woocommerce/loop/sale-count-down', array('is_single'=> true));
        }

        public function shop_loop_wishlist() {
            if (in_array('yith-woocommerce-wishlist/init.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                echo do_shortcode('[yith_wcwl_add_to_wishlist]');
            }
        }

        public function shop_single_function() {
            Spring_Plant()->helper()->getTemplate('woocommerce/single/product-functions');
        }
		public function shop_show_product_images_layout_deals() {
			Spring_Plant()->helper()->getTemplate('woocommerce/single/product-image-deals');
		}
        public function shop_show_product_images_layout_2() {
            Spring_Plant()->helper()->getTemplate('woocommerce/single/product-image-2');
        }

        public function shop_show_product_images_layout_3() {
            Spring_Plant()->helper()->getTemplate('woocommerce/single/product-image-3');
        }
        public function shop_single_top() {
            Spring_Plant()->helper()->getTemplate('woocommerce/single/product-single-top');
        }
        public function portfolio_single_top() {
            Spring_Plant()->helper()->getTemplate('portfolio/single/portfolio-single-top');
        }

        public function shop_loop_single_gallery() {
            Spring_Plant()->helper()->getTemplate('woocommerce/single/product-gallery');
        }

        public function shop_loop_quick_view_product_title() {
            Spring_Plant()->helper()->getTemplate('woocommerce/loop/title');
        }

        public function quick_view_show_product_images() {
            Spring_Plant()->helper()->getTemplate('woocommerce/loop/product-image');
        }

        public function quickview_rating() {
            Spring_Plant()->helper()->getTemplate('woocommerce/loop/rating');
        }

        public function shop_loop_rating() {
            wc_get_template('loop/rating.php');
        }
	}
}