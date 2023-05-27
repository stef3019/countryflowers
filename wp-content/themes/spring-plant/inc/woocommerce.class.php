<?php
if (!defined('ABSPATH')) {
    exit('Direct script access denied.');
}
if (!class_exists('Spring_Plant_Inc_Woocommerce')) {
    class Spring_Plant_Inc_Woocommerce
    {
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
            $this->filter();
            $this->hook();
        }

        public function filter()
        {
            add_filter('gsf_shorcodes', array($this, 'register_shortcode'));
            //page title
            add_filter('spring_plant_page_title', array($this, 'page_title'));

            add_filter('spring_plant_post_layout_matrix', array($this, 'layout_matrix'));

            // remove shop page title
            add_filter('woocommerce_show_page_title', '__return_false');

            add_filter('woocommerce_product_description_heading', '__return_false');
            add_filter('woocommerce_product_additional_information_heading', '__return_false');
            add_filter('woocommerce_product_review_heading', '__return_false');

            // single products related
            add_filter('woocommerce_output_related_products_args', array($this, 'product_related_products_args'));
            add_filter('woocommerce_product_related_posts_relate_by_category', array($this, 'product_related_posts_relate_by_category'));
            add_filter('woocommerce_product_related_posts_relate_by_tag', array($this, 'product_related_posts_relate_by_tag'));

            add_filter('woocommerce_upsells_total', array($this, 'product_up_sells_posts_per_page'));

            // Cross sells
            add_filter('woocommerce_cross_sells_total', array($this, 'product_cross_sells_posts_per_page'));

            add_filter('woocommerce_cart_item_thumbnail', array($this, 'product_cart_item_thumbnail'), 10, 3);
            add_filter('woocommerce_product_review_comment_form_args', array($this, 'product_single_review_form_fields'));
        }

        public function hook()
        {
            // remove woocommerce sidebar
            remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

            // remove Breadcrumb
            remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

            // remove archive description
            remove_action('woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10);
            remove_action('woocommerce_archive_description', 'woocommerce_product_archive_description', 10);

            // remove result count and catalog ordering
            remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
            remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

            // remove pagination
            //remove_action('woocommerce_after_shop_loop','woocommerce_pagination',10);

            // remove product link close
            remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
            remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);

            //remove add to cart
            remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);

            // remove product thumb
            remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);

            // remove product title
            remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);

            // remove product rating
            remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);

            // remove compare button
            global $yith_woocompare;
            if (isset($yith_woocompare) && isset($yith_woocompare->obj)) {
                remove_action('woocommerce_after_shop_loop_item', array($yith_woocompare->obj, 'add_compare_link'), 20);
                remove_action('woocommerce_single_product_summary', array($yith_woocompare->obj, 'add_compare_link'), 35);
            }

            add_action('pre_get_posts', array($this, 'changePostPerPage'), 7);


            // product cat
            add_action('woocommerce_shop_loop_item_title', array(Spring_Plant()->templates(), 'shop_loop_product_cat'), 10);

            // product title
            add_action('woocommerce_shop_loop_item_title', array(Spring_Plant()->templates(), 'shop_loop_product_title'), 10);

            // product rating
            add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 15);

            // Product description
            add_action('woocommerce_after_shop_loop_item_title', array($this, 'shop_loop_product_excerpt'), 20);

            // product actions
            add_action('spring_plant_woocommerce_product_actions', array(Spring_Plant()->templates(), 'shop_loop_quick_view'), 10);
            add_action('spring_plant_woocommerce_product_actions', array(Spring_Plant()->templates(), 'shop_loop_add_to_cart'), 5);
            add_action('spring_plant_woocommerce_product_actions', array(Spring_Plant()->templates(), 'shop_loop_compare'), 15);
            add_action('spring_plant_product_actions', array(Spring_Plant()->templates(), 'shop_loop_list_add_to_cart'), 5);
            // Product List actions
            add_action('spring_plant_woocommerce_shop_loop_list_info', array(Spring_Plant()->templates(), 'shop_loop_list_add_to_cart'), 10);
            add_action('spring_plant_woocommerce_shop_loop_list_info', array(Spring_Plant()->templates(), 'shop_loop_quick_view'), 15);
            add_action('spring_plant_woocommerce_shop_loop_list_info', array(Spring_Plant()->templates(), 'shop_loop_wishlist'), 20);
            add_action('spring_plant_woocommerce_shop_loop_list_info', array(Spring_Plant()->templates(), 'shop_loop_compare'), 25);

            // single product
            remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

            add_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_loop_sale_flash', 10);
            add_action('woocommerce_single_product_summary', array(Spring_Plant()->templates(), 'shop_loop_rating'), 9);
            add_action('woocommerce_single_product_summary', array(Spring_Plant()->templates(), 'shop_single_loop_sale_count_down'), 15);
            add_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 25);
            add_action('woocommerce_single_product_summary', array(Spring_Plant()->templates(), 'shop_single_function'), 35);


            add_action('wp_head', array($this, 'shop_single_layout'), 10);

            // single product gallery
            add_action('spring_plant_show_product_gallery', array(Spring_Plant()->templates(), 'shop_loop_single_gallery'), 10);

            // Quick view
            add_action('wp_footer', array($this, 'quick_view'));

            add_action('woocommerce_before_quick_view_product_summary', 'woocommerce_show_product_loop_sale_flash', 10);
            add_action('woocommerce_before_quick_view_product_summary', array(Spring_Plant()->templates(), 'quick_view_show_product_images'), 20);

            add_action('woocommerce_quick_view_product_summary', array(Spring_Plant()->templates(), 'shop_loop_quick_view_product_title'), 5);
            add_action('woocommerce_quick_view_product_summary', array(Spring_Plant()->templates(), 'quickview_rating'), 10);
            add_action('woocommerce_quick_view_product_summary', 'woocommerce_template_single_price', 10);
            add_action('woocommerce_quick_view_product_summary', array(Spring_Plant()->templates(), 'shop_single_loop_sale_count_down'), 15);
            add_action('woocommerce_quick_view_product_summary', 'woocommerce_template_single_excerpt', 20);
            add_action('woocommerce_quick_view_product_summary', 'woocommerce_template_single_meta', 30);
            add_action('woocommerce_quick_view_product_summary', 'woocommerce_template_single_add_to_cart', 40);
            add_action('woocommerce_quick_view_product_summary', array(Spring_Plant()->templates(), 'shop_single_function'), 50);
            add_action('woocommerce_quick_view_product_summary', 'woocommerce_template_single_sharing', 60);


            // Cart
            add_action('init', array($this, 'woocommerce_clear_cart_url'));
            remove_action('woocommerce_cart_collaterals', 'woocommerce_cross_sell_display');
            add_action('woocommerce_cart_collaterals', 'woocommerce_cross_sell_display', 20);

            // Shortcode Product singular
            add_action('spring_product_singular_sale_flash', 'woocommerce_show_product_loop_sale_flash', 10);
            add_action('spring_product_singular_product_actions', array(Spring_Plant()->templates(), 'shop_loop_quick_view'), 5);
            add_action('spring_product_singular_product_actions', array(Spring_Plant()->templates(), 'shop_loop_add_to_cart'), 10);
            add_action('spring_product_singular_product_actions', array(Spring_Plant()->templates(), 'shop_loop_compare'), 15);

        }


        public function register_shortcode($shortcodes)
        {
            $shortcodes = array_merge($shortcodes, array(
                'gsf_products',
                'gsf_product_deals',
                'gsf_product_category_list',
                'gsf_products_horizontal',
                'gsf_product_singular',
                'gsf_product_tabs',
                'gsf_shop_category',
            ));
            sort($shortcodes);
            return $shortcodes;
        }


        public function changePostPerPage($q)
        {
            if (!is_admin() && $q->is_main_query() && ($q->is_post_type_archive('product') || $q->is_tax(get_object_taxonomies('product')))) {
                $woocommerce_customize = Spring_Plant()->options()->get_woocommerce_customize();
                if (!isset($woocommerce_customize['disable']) || !array_key_exists('items-show', $woocommerce_customize['disable'])) {
                    $product_per_page = Spring_Plant()->options()->get_woocommerce_customize_item_show();
                } else {
                    $product_per_page = Spring_Plant()->options()->get_product_per_page();
                }

                if (!empty($product_per_page)) {
                    $product_per_page_arr = explode(",", $product_per_page);
                } else {
                    $product_per_page_arr = array(intval(get_option('posts_per_page')));
                }
                $product_per_page = isset($_GET['product_per_page']) ? wc_clean($_GET['product_per_page']) : $product_per_page_arr[0];

                $q->set('posts_per_page', $product_per_page);
            }
        }

        /**
         * Get Post Layout Settings
         *
         * @return mixed
         */
        public function get_layout_settings()
        {
            $catalog_layout = Spring_Plant()->options()->get_product_catalog_layout();
            $product_item_skin = Spring_Plant()->options()->get_product_item_skin();
            return array(
                'post_layout' => $catalog_layout,
                'product_item_skin' => $product_item_skin,
                'post_columns' => array(
                    'xl' => intval(Spring_Plant()->options()->get_product_columns()),
                    'lg' => intval(Spring_Plant()->options()->get_product_columns_md()),
                    'md' => intval(Spring_Plant()->options()->get_product_columns_sm()),
                    'sm' => intval(Spring_Plant()->options()->get_product_columns_xs()),
                    '' => intval(Spring_Plant()->options()->get_product_columns_mb()),
                ),
                'post_columns_gutter' => intval(Spring_Plant()->options()->get_product_columns_gutter()),
                'post_image_size' => Spring_Plant()->options()->get_product_image_size(),
                'post_paging' => Spring_Plant()->options()->get_product_paging(),
                'post_animation' => Spring_Plant()->options()->get_product_animation(),
                'itemSelector' => 'article',
                'category_filter_enable' => false,
                'category_filter_align' => '',
                'post_type' => 'product',
                'taxonomy' => 'product_cat'
            );
        }


        public function layout_matrix($matrix)
        {
            $post_settings = Spring_Plant()->blog()->get_layout_settings();
            if ($post_settings['post_type'] !== 'product') {
                $post_settings = Spring_Plant()->woocommerce()->get_layout_settings();
            }
            $columns = isset($post_settings['post_columns']) ? $post_settings['post_columns'] : array(
                'xl' => 3,
                'lg' => 3,
                'md' => 2,
                'sm' => 1,
                '' => 1
            );
            $columns = Spring_Plant()->helper()->get_bootstrap_columns($columns);
            $columns_gutter = intval(isset($post_settings['post_columns_gutter']) ? $post_settings['post_columns_gutter'] : 30);
            $image_size = isset($post_settings['post_image_size']) ? $post_settings['post_image_size'] : 'medium';
            $matrix['product'] = array(
                'list' => array(
                    'image_size' => 'shop_catalog',
                    'columns_gutter' => $columns_gutter,
                    'layout' => array(
                        array('columns' => $columns, 'template' => 'content-product'),
                    )
                ),
                'list-02' => array(
                    'image_size' => '600x330',
                    'layout' => array(
                        array('columns' => $columns, 'template' => 'content-product-horizontal'),
                    )
                ),
                'grid' => array(
                    'placeholder_enable' => true,
                    'columns_gutter' => $columns_gutter,
                    'image_size' => 'shop_catalog',
                    'layout' => array(
                        array('columns' => $columns, 'template' => 'content-product')
                    )
                ),
                'deals' => array(
                    'placeholder_enable' => true,
                    'columns_gutter' => $columns_gutter,
                    'image_size' => $image_size,
                    'layout' => array(
                        array('columns' => $columns, 'template' => 'content-product-deals')
                    )
                ),
                'metro-01' => array(
                    'columns_gutter' => $columns_gutter,
                    'isotope' => array(
                        'itemSelector' => 'article',
                        'layoutMode' => 'masonry',
                        'percentPosition' => true,
                        'masonry' => array(
                            'columnWidth' => '.gsf-column-base',
                        ),
                        'metro' => true
                    ),
                    'image_size' => $image_size,
                    'layout' => array(
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '2x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '2x1'),

                    )
                ),
                'metro-02' => array(
                    'columns_gutter' => $columns_gutter,
                    'isotope' => array(
                        'itemSelector' => 'article',
                        'layoutMode' => 'masonry',
                        'percentPosition' => true,
                        'masonry' => array(
                            'columnWidth' => '.gsf-column-base',
                        ),
                        'metro' => true
                    ),
                    'image_size' => $image_size,
                    'layout' => array(
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '2x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '2x1'),

                    )
                ),
                'metro-03' => array(
                    'columns_gutter' => $columns_gutter,
                    'isotope' => array(
                        'itemSelector' => 'article',
                        'layoutMode' => 'masonry',
                        'percentPosition' => true,
                        'masonry' => array(
                            'columnWidth' => '.gsf-column-base',
                        ),
                        'metro' => true
                    ),
                    'image_size' => $image_size,
                    'layout' => array(
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '2x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '2x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                    )
                ),
                'metro-04' => array(
                    'columns_gutter' => $columns_gutter,
                    'isotope' => array(
                        'itemSelector' => 'article',
                        'layoutMode' => 'masonry',
                        'percentPosition' => true,
                        'masonry' => array(
                            'columnWidth' => '.gsf-column-base',
                        ),
                        'metro' => true
                    ),
                    'image_size' => $image_size,
                    'layout' => array(
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '2x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '2x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                    )
                ),
                'metro-05' => array(
                    'columns_gutter' => $columns_gutter,
                    'image_size' => $image_size,
                    'isotope' => array(
                        'itemSelector' => 'article',
                        'layoutMode' => 'masonry',
                        'percentPosition' => true,
                        'masonry' => array(
                            'columnWidth' => '.gsf-column-base',
                        ),
                        'metro' => true
                    ),
                    'layout' => array(
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '2x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '2x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '2x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '2x1')
                    )
                ),
                'metro-06' => array(
                    'columns_gutter' => $columns_gutter,
                    'image_size' => $image_size,
                    'isotope' => array(
                        'itemSelector' => 'article',
                        'layoutMode' => 'masonry',
                        'percentPosition' => true,
                        'masonry' => array(
                            'columnWidth' => '.gsf-column-base',
                        ),
                        'metro' => true
                    ),
                    'layout' => array(
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 3, 'lg' => 2, 'md' => 1, 'sm' => 1, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '2x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 6, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 6, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 6, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 6, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 3, 'lg' => 2, 'md' => 1, 'sm' => 1, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '2x1'),

                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 6, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 6, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 3, 'lg' => 2, 'md' => 1, 'sm' => 1, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '2x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 3, 'lg' => 2, 'md' => 1, 'sm' => 1, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '2x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 6, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 6, 'lg' => 4, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                    )
                ),
                'metro-07' => array(
                    'columns_gutter' => $columns_gutter,
                    'image_size' => $image_size,
                    'isotope' => array(
                        'itemSelector' => 'article',
                        'layoutMode' => 'masonry',
                        'percentPosition' => true,
                        'masonry' => array(
                            'columnWidth' => '.gsf-column-base',
                        ),
                        'metro' => true
                    ),
                    'layout' => array(
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2, 'lg' => 1, 'md' => 1, 'sm' => 1, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '2x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),

                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2, 'lg' => 1, 'md' => 1, 'sm' => 1, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '2x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1')
                    )
                ),
                'metro-08' => array(
                    'columns_gutter' => $columns_gutter,
                    'image_size' => $image_size,
                    'layout' => array(
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 1.5, 'lg' => 1, 'md' => 1, 'sm' => 1, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '2x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 3, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 3, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 3, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 3, 'lg' => 2, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1')
                    )
                ),
                'metro-09' => array(
                    'columns_gutter' => $columns_gutter,
                    'image_size' => $image_size,
                    'layout' => array(
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2, 'lg' => 1, 'md' => 1, 'sm' => 1, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '2x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 3, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 3, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 3, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 3, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 3, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4, 'lg' => 3, 'md' => 2, 'sm' => 2, '' => 1)), 'template' => 'content-product-metro', 'layout_ratio' => '1x1')
                    )
                ),
            );
            return $matrix;
        }

        public function get_product_class()
        {
            $settings = Spring_Plant()->blog()->get_layout_settings();

            if ($settings['post_type'] !== 'product') {
                $settings = Spring_Plant()->woocommerce()->get_layout_settings();
            }
            $product_item_skin = isset($settings['product_item_skin']) ? $settings['product_item_skin'] : '';

            $post_classes = array(
                'clearfix',
                'product-item-wrap',
                'product-grid',
                $product_item_skin,
            );
            if (!isset($settings['carousel']) || isset($settings['carousel_rows'])) {
                if (isset($settings['columns']) && ($settings['columns'] !== '') && !isset($settings['isMainQuery'])) {
                    $columns_lg = absint($settings['columns']);
                    $columns = array(
                        'xl' => $columns_lg,
                        'lg' => $columns_lg > 4 ? 3 : $columns_lg,
                        'md' => $columns_lg > 2 ? 2 : $columns_lg,
                        'sm' => 1,
                        '' => 1
                    );
                } else {
                    $columns = isset($settings['post_columns']) ? $settings['post_columns'] : array(
                        'xl' => 3,
                        'lg' => 3,
                        'md' => 2,
                        'sm' => 1,
                        '' => 1
                    );
                }
                $columns = Spring_Plant()->helper()->get_bootstrap_columns($columns);
                $post_classes[] = $columns;
            }
            return implode(' ', $post_classes);
        }

        public function get_product_inner_class()
        {
            $post_settings = Spring_Plant()->blog()->get_layout_settings();
            if ($post_settings['post_type'] !== 'product') {
                $post_settings = Spring_Plant()->woocommerce()->get_layout_settings();
            }
            $post_animation = isset($post_settings['post_animation']) ? $post_settings['post_animation'] : '';

            $post_inner_classes = array(
                'product-item-inner',
                'clearfix',
                Spring_Plant()->helper()->getCSSAnimation($post_animation)
            );
            return implode(' ', array_filter($post_inner_classes));
        }

        public function render_product_thumbnail_markup($args = array())
        {
            $defaults = array(
                'post_id' => get_the_ID(),
                'image_size' => 'shop_catalog',
                'placeholder_enable' => true,
                'image_mode' => 'image',
                'display_permalink' => true,
            );
            $defaults = wp_parse_args($args, $defaults);
            Spring_Plant()->helper()->getTemplate('woocommerce/loop/product-thumbnail', $defaults);
        }

        public function shop_loop_product_excerpt()
        {
            global $post;
            if (!$post->post_excerpt) {
                return;
            }
            ?>
            <div class="product-description">
                <?php echo apply_filters('woocommerce_short_description', $post->post_excerpt) ?>
            </div>
            <?php
        }

        public function archive_markup($query_args = null, $settings = null)
        {
            $gsf_query = Spring_Plant()->query()->get_query();
            if (isset($settings['tabs']) && isset($settings['tabs'][0]['query_args'])) {
                $query_args = $settings['tabs'][0]['query_args'];
            }

            if (!isset($query_args)) {
                $settings['isMainQuery'] = true;
            }
            $settings = wp_parse_args($settings, $this->get_layout_settings());
            Spring_Plant()->blog()->set_layout_settings($settings);

            if (isset($query_args)) {
                $query_args = Spring_Plant()->query()->get_main_query_vars( $query_args );
                Spring_Plant()->query()->query_posts( $query_args );
            }


            if (isset($settings['isMainQuery']) && ($settings['isMainQuery'] == true)) {
                add_action('spring_plant_before_archive_wrapper', array(Spring_Plant()->templates(), 'shop_catalog_filter'), 5);
            }

            if (isset($settings['category_filter_enable']) && $settings['category_filter_enable'] === true) {
                add_action('spring_plant_before_archive_wrapper', array(Spring_Plant()->blog(), 'category_filter_markup'));
            }

            if (isset($settings['tabs'])) {
                add_action('spring_plant_before_archive_wrapper', array(Spring_Plant()->blog(), 'tabs_markup'));
            }

            if (Spring_Plant()->query()->have_posts()) {
                if (isset($settings['isMainQuery']) && ($settings['isMainQuery'] == true)) {
                    /**
                     * woocommerce_before_shop_loop hook.
                     *
                     * @hooked wc_print_notices - 10
                     */
                    do_action('woocommerce_before_shop_loop');
                }

                woocommerce_product_loop_start();
                $post_settings = &Spring_Plant()->blog()->get_layout_settings();
                $post_layout = isset($post_settings['post_layout']) ? $post_settings['post_layout'] : 'grid';
                $item_skin = isset($post_settings['product_item_skin']) ? $post_settings['product_item_skin'] : 'product-skin-01';
                if (!in_array($post_layout, array('grid', 'list'))) {
                    $item_skin = '';
                }
                $layout_matrix = Spring_Plant()->blog()->get_layout_matrix($post_layout);
                $post_paging = isset($post_settings['post_paging']) ? $post_settings['post_paging'] : 'pagination';
                $post_animation = isset($post_settings['post_animation']) ? $post_settings['post_animation'] : '';
                $placeholder_enable = isset($layout_matrix['placeholder_enable']) ? $layout_matrix['placeholder_enable'] : false;
                $paged = Spring_Plant()->query()->query_var_paged();
                $image_size = isset($post_settings['image_size']) ? $post_settings['image_size'] : (isset($layout_matrix['image_size']) ? $layout_matrix['image_size'] : 'shop_catalog');
                $image_size_base = $image_size;
                $image_ratio = '';

                $display_type = woocommerce_get_loop_display_mode();
                if ( 'subcategories' === $display_type ) {
                    $gsf_query->post_count = 0;
                    $gsf_query->max_num_pages = 0;
                }
                if (in_array($post_layout, array('grid', 'metro-01', 'metro-02', 'metro-03', 'metro-04', 'metro-05', 'metro-06')) && ($image_size === 'full')) {
                    $image_ratio = isset($post_settings['image_ratio']) ? $post_settings['image_ratio'] : '';
                    if (empty($image_ratio)) {
                        $image_ratio = Spring_Plant()->options()->get_product_image_ratio();
                    }

                    if ($image_ratio === 'custom') {
                        $image_ratio_custom = isset($post_settings['image_ratio_custom']) ? $post_settings['image_ratio_custom'] : Spring_Plant()->options()->get_product_image_ratio_custom();
                        if (is_array($image_ratio_custom) && isset($image_ratio_custom['width']) && isset($image_ratio_custom['height'])) {
                            $image_ratio_custom_width = intval($image_ratio_custom['width']);
                            $image_ratio_custom_height = intval($image_ratio_custom['height']);
                            if (($image_ratio_custom_width > 0) && ($image_ratio_custom_height > 0)) {
                                $image_ratio = "{$image_ratio_custom_width}x{$image_ratio_custom_height}";
                            }
                        } elseif (preg_match('/x/', $image_ratio_custom)) {
                            $image_ratio = $image_ratio_custom;
                        }
                    }

                    if ($image_ratio === 'custom') {
                        $image_ratio = '1x1';
                    }
                }

                $image_ratio_base = $image_ratio;

                if (isset($layout_matrix['layout'])) {
                    $layout_settings = $layout_matrix['layout'];
                    $index = intval($gsf_query->get('index', 0));

                    $post_classes = array(
                        'clearfix',
                        'product-item-wrap',
                        $item_skin
                    );

                    $post_inner_classes = array(
                        'product-item-inner',
                        'clearfix',
                        Spring_Plant()->helper()->getCSSAnimation($post_animation)
                    );
                    $carousel_index = 0;
                    while (Spring_Plant()->query()->have_posts()) : Spring_Plant()->query()->the_post();
                        $index = $index % count($layout_settings);
                        $current_layout = $layout_settings[$index];
                        $isFirst = isset($current_layout['isFirst']) ? $current_layout['isFirst'] : false;
                        if ($isFirst && ($paged > 1) && in_array($post_paging, array('load-more', 'infinite-scroll'))) {
                            if (isset($layout_settings[$index + 1])) {
                                $current_layout = $layout_settings[$index + 1];
                            } else {
                                continue;
                            }
                        }
                        $post_inner_attributes = array();

                        if (isset($current_layout['layout_ratio'])) {
                            $layout_ratio = !empty($current_layout['layout_ratio']) ? $current_layout['layout_ratio'] : '1x1';
                            if ($image_size_base !== 'full') {
                                $image_size = Spring_Plant()->helper()->get_metro_image_size($image_size_base, $layout_ratio, $layout_matrix['columns_gutter']);
                            } else {
                                $image_ratio = Spring_Plant()->helper()->get_metro_image_ratio($image_ratio_base, $layout_ratio);
                            }
                            $post_inner_attributes[] = 'data-ratio="' . $layout_ratio . '"';
                        }

                        $post_columns = $current_layout['columns'];
                        $template = $current_layout['template'];

                        $classes = array(
                            "product-{$template}"
                        );
                        if (isset($settings['carousel_rows']) && $carousel_index == 0) {
                            echo '<div class="gf-carousel-item clearfix">';
                        }
                        if (!isset($post_settings['carousel']) || isset($settings['carousel_rows'])) {
                            $classes[] = $post_columns;
                        }
                        $classes = wp_parse_args($classes, $post_classes);
                        $post_class = implode(' ', array_filter($classes));
                        $post_inner_class = implode(' ', array_filter($post_inner_classes));
                        wc_get_template("{$template}.php", array(
                            'post_layout' => $post_layout,
                            'image_size' => $image_size,
                            'product_item_skin' => $item_skin,
                            'image_ratio' => $image_ratio,
                            'post_class' => $post_class,
                            'post_inner_class' => $post_inner_class,
                            'placeholder_enable' => $placeholder_enable,
                            'post_inner_attributes' => $post_inner_attributes
                        ));

                        if ($isFirst) {
                            unset($layout_settings[$index]);
                            $layout_settings = array_values($layout_settings);
                        }

                        if ($isFirst && $paged === 1) {
                            $index = 0;
                        } else {
                            $index++;
                        }
                        $carousel_index++;
                        if (isset($settings['carousel_rows']) && $carousel_index == $settings['carousel_rows']['items_show']) {
                            echo '</div>';
                            $carousel_index = 0;
                        }
                    endwhile;
                    if (isset($settings['carousel_rows']) && $carousel_index != $settings['carousel_rows']['items_show'] && $carousel_index != 0) {
                        echo '</div>';
                    }
                }
                woocommerce_product_loop_end();
            } else {
                /**
                 * woocommerce_no_products_found hook.
                 *
                 * @hooked wc_no_products_found - 10
                 */
                do_action('woocommerce_no_products_found');
            }

            if (isset($settings['tabs'])) {
                remove_action('spring_plant_before_archive_wrapper', array(Spring_Plant()->blog(), 'tabs_markup'));
            }

            if (isset($settings['category_filter_enable']) && $settings['category_filter_enable'] === true) {
                remove_action('spring_plant_before_archive_wrapper', array(Spring_Plant()->blog(), 'category_filter_markup'));
            }

            if (isset($settings['isMainQuery']) && ($settings['isMainQuery'] == true)) {
                remove_action('spring_plant_before_archive_wrapper', array(Spring_Plant()->templates(), 'shop_catalog_filter'), 5);
            }

            Spring_Plant()->blog()->unset_layout_settings();

            if (isset($query_args)) {
                Spring_Plant()->query()->reset_query();
            }

        }

        public function page_title($page_title)
        {
            if (is_post_type_archive('product')) {
                $shop_page_id = wc_get_page_id('shop');
                if ($shop_page_id) {
                    if (!$page_title) {
                        $page_title = get_the_title($shop_page_id);
                    }
                    $custom_page_title = Spring_Plant()->metaBox()->get_page_title_content($shop_page_id);
                    if ($custom_page_title) {
                        $page_title = $custom_page_title;
                    }
                }
            } elseif (is_singular('product')) {
                global $single_product_title;
                $page_title = get_the_title(get_the_ID());
                $single_product_title = $page_title;
            }
            return $page_title;
        }

        public function shop_single_layout()
        {
            if (is_singular('product')) {
                $product_single_layout = Spring_Plant()->options()->get_product_single_layout();
                if ('layout-02' === $product_single_layout) {
                    add_action('spring_plant_before_main_content', array(Spring_Plant()->templates(), 'shop_single_top'), 10);
                }
                if ('layout-03' === $product_single_layout) {
                    add_action('spring_plant_before_main_content', array(Spring_Plant()->templates(), 'shop_show_product_images_layout_3'), 10);
                }
                if ('layout-05' === $product_single_layout) {
                    add_action('spring_plant_before_main_content', array(Spring_Plant()->templates(), 'shop_show_product_images_layout_2'), 10);
                }
            }
        }

        public function quick_view()
        {
            $product_quick_view = Spring_Plant()->options()->get_product_quick_view_enable();
            if ('on' === $product_quick_view) {
                wp_enqueue_script('wc-add-to-cart-variation');
            }
        }

        public function product_related_products_args()
        {
            $products_per_page = intval(Spring_Plant()->options()->get_product_related_per_page());
            $args['posts_per_page'] = $products_per_page;
            return $args;
        }

        public function product_related_posts_relate_by_category()
        {
            $product_algorithm = Spring_Plant()->options()->get_product_related_algorithm();
            return (in_array($product_algorithm, array('cat', 'cat-tag'))) ? true : false;
        }

        public function product_related_posts_relate_by_tag()
        {
            $product_algorithm = Spring_Plant()->options()->get_product_related_algorithm();
            return (in_array($product_algorithm, array('tag', 'cat-tag'))) ? true : false;
        }

        public function product_up_sells_posts_per_page()
        {
            $up_sells_per_page = Spring_Plant()->options()->get_product_up_sells_per_page();
            return $up_sells_per_page;
        }

        public function product_cross_sells_posts_per_page()
        {
            $cross_sells_per_page = Spring_Plant()->options()->get_product_cross_sells_per_page();
            return $cross_sells_per_page;
        }

        public function product_cart_item_thumbnail($image, $cart_item, $cart_item_key)
        {
            if (isset($cart_item['product_id'])) {
                $image_id = get_post_thumbnail_id($cart_item['product_id']);
                $image = Spring_Plant()->image_resize()->resize(array(
                    'image_id' => $image_id,
                    'width' => '100',
                    'height' => '129'
                ));
                $image_attributes = array(
                    'src="' . esc_url($image['url']) . '"',
                    'width="' . esc_attr($image['width']) . '"',
                    'height="' . esc_attr($image['height']) . '"',
                    'title="' . esc_attr(get_the_title($cart_item['product_id'])) . '"'
                );
                $image = '<img ' . implode(' ', $image_attributes) . '>';
            }
            return $image;
        }
        public function product_single_review_form_fields($comment_form) {
            $commenter = wp_get_current_commenter();
            $comment_form['fields'] = array(
                'author' => '<div class="clearfix"><p class="comment-form-author">' .
                    '<input id="author" placeholder="' . esc_attr__( 'Name', 'spring-plant' ) . '" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" required /></p>',
                'email'  => '<p class="comment-form-email">' .
                    '<input id="email" placeholder="' . esc_attr__( 'Email', 'spring-plant' ) . '" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" aria-required="true" required /></p></div>'
            );
            $comment_form['comment_field'] = '<div class="comment-form-rating"><label for="rating">' . esc_html__( 'Your rating', 'spring-plant' ) . '</label><select name="rating" id="rating" aria-required="true" required>
							<option value="">' . esc_html__( 'Rate&hellip;', 'spring-plant' ) . '</option>
							<option value="5">' . esc_html__( 'Perfect', 'spring-plant' ) . '</option>
							<option value="4">' . esc_html__( 'Good', 'spring-plant' ) . '</option>
							<option value="3">' . esc_html__( 'Average', 'spring-plant' ) . '</option>
							<option value="2">' . esc_html__( 'Not that bad', 'spring-plant' ) . '</option>
							<option value="1">' . esc_html__( 'Very poor', 'spring-plant' ) . '</option>
						</select></div>
						<p class="comment-form-comment"><textarea id="comment" placeholder="' . esc_attr__( 'Your review', 'spring-plant' ) . '" name="comment" cols="45" rows="8" aria-required="true" required></textarea></p>';
            return $comment_form;
        }

        public function woocommerce_clear_cart_url()
        {
            global $woocommerce;
            if (isset($_GET['empty-cart'])) {
                $woocommerce->cart->empty_cart();
            }
        }
    }
}