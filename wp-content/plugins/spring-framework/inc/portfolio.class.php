<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('G5P_Inc_Portfolio')) {
    class G5P_Inc_Portfolio
    {
        private static $_instance;
        private $ajax_nonce = 'portfolio_featured_nonce';
        public static function getInstance()
        {
            if (self::$_instance == NULL) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        private $_permalink_optionKey = 'gsf_permalink';
        private $_permalink_post_type_base = 'gsf_portfolio_base';
        private $_permalink_category_base = 'gsf_portfolio_cat_base';

        private $_permalinks = array();

        private $_post_type = 'portfolio';
        private $_taxonomy_category = 'portfolio_cat';

        public function init(){
            $this->_permalinks = $this->get_permalink();

            add_filter('gsf_shorcodes', array($this, 'register_shortcode'));

            add_filter('gsf_options_get_search_popup_ajax_post_type', array($this, 'search_popup_ajax_post_type'));
            //page title
            add_filter('spring_plant_page_title', array($this, 'page_title'));
            add_action('admin_init',array($this,'register_permalink'));
            add_action( 'load-options-permalink.php', array( $this,'save_permalink') );

            // register post-type
            add_filter('gsf_register_post_type', array($this,'register_post_type'));

            add_action('init', array($this, 'register_portfolio_visibility_taxonomy'));

            // register taxonomy
            add_filter('gsf_register_taxonomy',array($this,'register_taxonomy'));

            // add filter category
            add_action('restrict_manage_posts', array($this,'add_category_filter'));
            add_filter('parse_query', array($this,'add_category_filter_query'));

            // custom columns
            add_filter("manage_{$this->_post_type}_posts_columns",array($this,'custom_columns_heading'));
            add_filter("manage_{$this->_post_type}_posts_custom_column",array($this,'custom_columns'),10,2);

            add_action('admin_enqueue_scripts',array($this,'adminAssets'));

            // Admin bar menus
            if ( apply_filters( 'gsf_show_admin_bar_visit_portfolio', true ) ) {
                add_action( 'admin_bar_menu', array( $this, 'admin_bar_menus' ), 32 );
            }

            //Preset
            add_filter('gsf_options_preset',array($this,'options_preset'));

            // Defined MetaBox
            add_filter('gsf_meta_box_config', array($this, 'register_meta_boxes'), 1);
            add_filter('gsf_page_setting_post_type',array($this,'page_setting'));
            add_filter('gsf_portfolio_meta_after',array($this,'register_meta_boxes_info'));

            // Change Post Per Pages
            add_action('pre_get_posts',array($this,'change_post_per_page'),6);

            add_action('wp', array($this, 'set_portfolio_single_to_option'), 20);

            // ajax portfolio featured
            add_action('wp_ajax_gsf_portfolio_featured', array($this, 'gsf_portfolio_featured'));
        }

        public function register_shortcode($shortcodes) {
            $shortcodes = array_merge($shortcodes, array(
                'gsf_portfolio_meta',
                'gsf_portfolios',
                'gsf_portfolio_horizontal'
            ));
            sort($shortcodes);
            return $shortcodes;
        }

        public function search_popup_ajax_post_type($output) {
            $output = array_merge($output, array(
                'portfolio' => esc_html__('Portfolio', 'spring-framework')
            ));
            return $output;
        }

        public function gsf_portfolio_featured() {
            $nonce = $_REQUEST['nonce'];
            if (!wp_verify_nonce($nonce, $this->ajax_nonce)) {
                wp_send_json_error();
            }
            $portfolio_id = $_REQUEST['portfolio_id'];
            $status = $_REQUEST['status'];
            if('0' == $status) {
                if(is_array(wp_set_post_terms($portfolio_id,'featured','portfolio_visibility')) ){
                    wp_send_json_success();
                } else {
                    wp_send_json_error();
                }
            } else {
                $result = wp_remove_object_terms($portfolio_id,'featured','portfolio_visibility');
                if($result === true){
                    wp_send_json_success();
                } else {
                    wp_send_json_error();
                }
            }
        }

        public function register_portfolio_visibility_taxonomy() {
            register_taxonomy( 'portfolio_visibility',
                apply_filters( 'gsf_taxonomy_objects_portfolio_visibility', array( 'portfolio' ) ),
                apply_filters( 'gsf_taxonomy_args_portfolio_visibility', array(
                    'hierarchical'      => false,
                    'show_ui'           => false,
                    'show_in_nav_menus' => false,
                    'query_var'         => is_admin(),
                    'rewrite'           => false,
                    'public'            => false,
                ) )
            );
        }

        public function page_title($page_title)
        {
            if (is_post_type_archive('portfolio')) {
                if (!$page_title) {
                    $page_title = get_the_archive_title();
                    $index = strpos($page_title, ':');
                    $page_title= substr($page_title,$index+1);
                }
                $custom_page_title = G5P()->metaBox()->get_page_title_content();
                if ($custom_page_title) {
                    $page_title = $custom_page_title;
                }

            } elseif (is_singular('portfolio')) {
                $custom_page_title = G5P()->metaBox()->get_page_title_content();
                if ($custom_page_title) {
                    $page_title = $custom_page_title;
                } else {
                    global $single_portfolio_title;
                    $page_title = get_the_title(get_the_ID());
                    $single_portfolio_title = $page_title;
                }
            }
            return $page_title;
        }

        public function adminAssets($hook) {
            global $post;
            if ( (($hook === 'post-new.php') || ($hook === 'post.php') || ($hook === 'edit.php'))
                && isset($post)
                && isset($post->post_type)
                && ($post->post_type === $this->_post_type)) {
                wp_enqueue_style(G5P()->assetsHandle('admin-portfolio'));
                wp_enqueue_script(G5P()->assetsHandle('admin-portfolio'));
                wp_localize_script(
                    G5P()->assetsHandle('admin-portfolio'),
                    'portfolio_featured_variable',
                    array(
                        'ajax_url' => admin_url('admin-ajax.php')
                    )
                );
            }
        }

        public function get_post_type() {
            return $this->_post_type;
        }

        public function get_taxonomy_category() {
            return $this->_taxonomy_category;
        }

        /**
         * Register Post Type
         *
         * @param $post_types
         * @return mixed
         */
        public function register_post_type($post_types) {
            $post_types [$this->_post_type] = array(
                'label'         => esc_html__('Portfolios', 'spring-framework'),
                'singular_name' => esc_html__('Portfolio','spring-framework'),
                'menu_icon'     => 'dashicons-images-alt2',
                'menu_position' => 25,
                'rewrite'       => array('slug' => $this->_permalinks['post_type_slug']),
            );

            return $post_types;
        }

        /**
         * Register Taxonomies
         *
         * @param $taxonomies
         * @return mixed
         */
        public function register_taxonomy($taxonomies) {
            $taxonomies[$this->_taxonomy_category] = array(
                'post_type'     => $this->_post_type,
                'label'         => esc_html__('Categories', 'spring-framework'),
                'name'          => esc_html__('Portfolio Categories', 'spring-framework'),
                'singular_name' => esc_html__('Category', 'spring-framework'),
                'rewrite'       => array('slug' => $this->_permalinks['category_slug']),
                'show_admin_column' => true,
            );
            return $taxonomies;
        }

        public function add_category_filter() {
            global $typenow;
            if ($typenow === $this->_post_type) {
                $selected      = isset($_GET[$this->_taxonomy_category]) ? $_GET[$this->_taxonomy_category] : '';
                $info_taxonomy = get_taxonomy($this->_taxonomy_category);
                wp_dropdown_categories(array(
                    'show_option_all' => sprintf(esc_html__('Show All %s', 'spring-framework'), $info_taxonomy->label),
                    'taxonomy'        => $this->_taxonomy_category,
                    'name'            => $this->_taxonomy_category,
                    'orderby'         => 'name',
                    'selected'        => $selected,
                    'show_count'      => true,
                    'hide_empty'      => true,
                    'hide_if_empty' => true
                ));
            }
        }

        public function add_category_filter_query($query) {
            global $pagenow;
            $q_vars    = &$query->query_vars;
            if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $this->_post_type && isset($q_vars[$this->_taxonomy_category]) && is_numeric($q_vars[$this->_taxonomy_category]) && $q_vars[$this->_taxonomy_category] != 0 ) {
                $term = get_term_by('id', $q_vars[$this->_taxonomy_category], $this->_taxonomy_category);
                $q_vars[$this->_taxonomy_category] = $term->slug;
            }
        }

        public function register_permalink() {
            add_settings_field(
                $this->_permalink_post_type_base,
                esc_html__('Portfolio base','spring-framework'),
                array( $this, 'permalink_post_type_base_callback' ),
                'permalink',
                'optional'
            );

            add_settings_field(
                $this->_permalink_category_base,
                esc_html__('Portfolio category base','spring-framework'),
                array( $this, 'permalink_cat_callback' ),
                'permalink',
                'optional'
            );
        }

        public function permalink_post_type_base_callback() {
            ?>
            <input type="text" name="<?php echo esc_attr($this->_permalink_post_type_base) ?>" placeholder="portfolios" class="regular-text code" value="<?php echo esc_attr($this->_permalinks['post_type_base']) ?>">
            <?php
        }

        public function permalink_cat_callback() {
            ?>
            <input type="text" name="<?php echo esc_attr($this->_permalink_category_base) ?>"
                   placeholder="portfolio-category" class="regular-text code"
                   value="<?php echo esc_attr($this->_permalinks['category_base']) ?>">
            <?php
        }

        public function get_permalink(){
            $permalinks = wp_parse_args((array)get_option($this->_permalink_optionKey, array()), array(
                'post_type_base' => '',
                'category_base'  => '',
                'tag_base'       => '',
            ));

            // Ensure rewrite slugs are set.
            $permalinks['post_type_slug'] = untrailingslashit(empty($permalinks['post_type_base']) ? _x('portfolios', 'slug', 'spring-framework') : $permalinks['post_type_base']);
            $permalinks['category_slug'] = untrailingslashit(empty($permalinks['category_base']) ? _x('portfolio-category', 'slug', 'spring-framework') : $permalinks['category_base']);
            $permalinks['tag_slug'] = untrailingslashit(empty($permalinks['tag_base']) ? _x('portfolio-tag', 'slug', 'spring-framework') : $permalinks['tag_base']);
            return $permalinks;
        }

        public function save_permalink(){
            if (!is_admin()) {
                return;
            }
            if (isset($_POST['permalink_structure'])) {
                $permalinks = (array)get_option($this->_permalink_optionKey, array());
                $permalinks['post_type_base'] = sanitize_title_with_dashes(trim($_POST[$this->_permalink_post_type_base]));
                $permalinks['category_base'] = sanitize_title_with_dashes(trim($_POST[$this->_permalink_category_base]));
                update_option($this->_permalink_optionKey, $permalinks);
            }
        }

        public function custom_columns_heading($columns) {
            $myCustomColumns['cb'] = $columns['cb'];
            $myCustomColumns['thumbnail'] = "<span class='gsf-columns-icon dashicons dashicons-format-image'></span>"; esc_html__('Thumbnail','spring-framework');
            $myCustomColumns['title'] = $columns['title'];
            $myCustomColumns['taxonomy-' . $this->_taxonomy_category] = esc_html__('Categories','spring-framework');
            $myCustomColumns['featured'] = "<span class='dashicons dashicons-star-filled parent-tips' title='" . esc_attr__('Featured','spring-framework') . "' style='cursor: help'></span>";
            $myCustomColumns['date'] = $columns['date'];
            return $myCustomColumns;
        }

        public function custom_columns($columns,$post_id) {
            if (($columns === 'thumbnail') && has_post_thumbnail($post_id)) {
                echo '<a href="' . esc_url(get_edit_post_link($post_id)) . '">';
                the_post_thumbnail('thumbnail');
                echo '</a>';
            }
            if('featured' === $columns) {
                $nonce = wp_create_nonce($this->ajax_nonce);
                $terms = wp_get_object_terms($post_id, 'portfolio_visibility', array( 'fields' => 'slugs' ));
                if(is_array($terms) && in_array('featured', $terms) ) {
                    echo '<a class="portfolio-featured" href="javascript:;" data-portolio-id="' . $post_id . '" data-status="1" data-portfolio-featured-nonce="' . esc_attr($nonce) . '">';
                    echo "<span class='dashicons dashicons-star-filled'></span>";
                } else {
                    echo '<a class="portfolio-featured" href="javascript:;" data-portolio-id="' . $post_id . '" data-status="0" data-portfolio-featured-nonce="' . esc_attr($nonce) . '">';
                    echo "<span class='dashicons dashicons-star-empty'></span>";
                }
                echo '</a>';
            }
        }

        public function admin_bar_menus($wp_admin_bar) {
            if ( ! is_admin() || ! is_user_logged_in() ) {
                return;
            }

            if ( ! is_user_member_of_blog() && ! is_super_admin() ) {
                return;
            }

            $wp_admin_bar->add_node( array(
                'parent' => 'site-name',
                'id'     => 'g5p-view-portfolio',
                'title'  => esc_html__('Visit Portfolios','spring-framework'),
                'href'   => get_post_type_archive_link($this->_post_type)
            ) );
        }

        public function options_preset($settings) {
            $settings[$this->_post_type] = array(
                'title' => esc_html__('Portfolios','spring-framework'),
                'preset' => array(
                    'archive_portfolio' => array(
                        'title'      => esc_html__('Portfolios Listing', 'spring-framework'),
                        'category'   => $this->_taxonomy_category,
                        'is_archive' => true,
                    ),
                    'single_portfolio'  => array(
                        'title'     => esc_html__('Single Portfolio', 'spring-framework'),
                        'is_single' => true,
                    )
                )
            );
            return $settings;
        }

        public function change_post_per_page($q) {
            if (!is_admin() && $q->is_main_query() && ($q->is_post_type_archive($this->_post_type) || $q->is_tax(get_object_taxonomies($this->_post_type)))) {
                $portfolio_per_page = intval(G5P()->options()->get_portfolio_per_page());
                $portfolio_per_page_custom = intval(isset($_GET['posts_per_page']) ? $_GET['posts_per_page'] : '');
                if ($portfolio_per_page_custom > 0 || $portfolio_per_page_custom == -1) {
                    $portfolio_per_page = $portfolio_per_page_custom;
                }
                if ($portfolio_per_page > 0 || $portfolio_per_page == -1) {
                    $q->set('posts_per_page', $portfolio_per_page);
                }
            }
        }

        public function page_setting($post_type) {
            $post_type[] = $this->_post_type;
            return $post_type;
        }

        public function register_meta_boxes($configs) {
            $prefix = G5P()->getMetaPrefix();
            $configs['gsf_portfolio_setting'] = array(
                'name'      => esc_html__('Portfolio Settings', 'spring-framework'),
                'post_type' => array($this->_post_type),
                'layout'    => 'inline',
                'section'  => array(
                    array(
                        'id'     => "{$prefix}section_portfolio",
                        'title'  => esc_html__('Single Portfolio', 'spring-framework'),
                        'icon'   => 'dashicons dashicons-welcome-write-blog',
                        'fields' => array_merge(
                            apply_filters('gsf_portfolio_meta_before', array()),
                            array(
                                array(
                                    'id'       => "{$prefix}single_portfolio_layout",
                                    'title'    => esc_html__('Layout', 'spring-framework'),
                                    'subtitle' => esc_html__('Specify your single portfolio layout', 'spring-framework'),
                                    'type'     => 'image_set',
                                    'options'  => G5P()->settings()->get_single_portfolio_layout(true),
                                    'default'  => ''
                                ),
                                array(
                                    'id'       => "{$prefix}single_portfolio_gallery_group",
                                    'title'    => esc_html__('Gallery', 'spring-framework'),
                                    'type'     => 'group',
                                    'required' => array("{$prefix}single_portfolio_layout", 'not in', array('layout-5','') ),
                                    'fields'   => array(
                                        array(
                                            'id'       => "{$prefix}single_portfolio_gallery_layout",
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
                                                        array("{$prefix}single_portfolio_gallery_image_size", 'full'),
                                                        array("{$prefix}single_portfolio_gallery_image_ratio", '4x3'),
                                                    )
                                                ),
                                                array(
                                                    'op'     => '=',
                                                    'value'  => 'thumbnail',
                                                    'fields' => array(
                                                        array("{$prefix}single_portfolio_gallery_image_size", 'full'),
                                                        array("{$prefix}single_portfolio_gallery_image_ratio", '4x3'),
                                                    )
                                                ),
                                                array(
                                                    'op'     => '=',
                                                    'value'  => 'carousel-center',
                                                    'fields' => array(
                                                        array("{$prefix}single_portfolio_gallery_image_size", 'full'),
                                                        array("{$prefix}single_portfolio_gallery_image_ratio", '4x3'),
                                                    )
                                                ),
                                                array(
                                                    'op'     => '=',
                                                    'value'  => 'grid',
                                                    'fields' => array(
                                                        array("{$prefix}single_portfolio_gallery_image_size", 'medium')
                                                    )
                                                ),
                                                array(
                                                    'op'     => '=',
                                                    'value'  => 'carousel-3d',
                                                    'fields' => array(
                                                        array("{$prefix}single_portfolio_gallery_image_size", '804x468')
                                                    )
                                                ),
                                                array(
                                                    'op'     => '=',
                                                    'value'  => 'metro',
                                                    'fields' => array(
                                                        array("{$prefix}single_portfolio_gallery_image_size", '370x320')
                                                    )
                                                )
                                            )
                                        ),
                                        array(
                                            'id'     => "{$prefix}single_portfolio_gallery_image_size_group",
                                            'title'  => esc_html__('Image Size', 'spring-framework'),
                                            'type'   => 'group',
                                            'fields' => array(
                                                array(
                                                    'id'       => "{$prefix}single_portfolio_gallery_image_size",
                                                    'title'    => esc_html__('Image size', 'spring-framework'),
                                                    'subtitle' => esc_html__('Enter your portfolio gallery image size', 'spring-framework'),
                                                    'desc'     => esc_html__('Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)).', 'spring-framework'),
                                                    'type'     => 'text',
                                                    'default'  => 'medium',
                                                    'required' => array(
                                                        array("{$prefix}single_portfolio_gallery_layout", '!=', 'masonry')
                                                    )
                                                ),
                                                array(
                                                    'id'       => "{$prefix}single_portfolio_gallery_image_ratio",
                                                    'title'    => esc_html__('Image ratio', 'spring-framework'),
                                                    'subtitle' => esc_html__('Specify your image portfolio gallery ratio', 'spring-framework'),
                                                    'type'     => 'select',
                                                    'options'  => G5P()->settings()->get_image_ratio(),
                                                    'default'  => '1x1',
                                                    'required' => array(
                                                        array("{$prefix}single_portfolio_gallery_layout", '!=', 'masonry'),
                                                        array("{$prefix}single_portfolio_gallery_image_size", '=', 'full')
                                                    )
                                                ),
                                                array(
                                                    'id'       => "{$prefix}single_portfolio_gallery_image_ratio_custom",
                                                    'title'    => esc_html__('Image ratio custom', 'spring-framework'),
                                                    'subtitle' => esc_html__('Enter custom image ratio', 'spring-framework'),
                                                    'type'     => 'dimension',
                                                    'required' => array(
                                                        array("{$prefix}single_portfolio_gallery_layout", '!=', 'masonry'),
                                                        array("{$prefix}single_portfolio_gallery_image_size", '=', 'full'),
                                                        array("{$prefix}single_portfolio_gallery_image_ratio", '=', 'custom')
                                                    )
                                                ),
                                                array(
                                                    'id'       => "{$prefix}single_portfolio_gallery_image_width",
                                                    'title'    => esc_html__('Image Width', 'spring-framework'),
                                                    'subtitle' => esc_html__('Enter image width', 'spring-framework'),
                                                    'type'     => 'dimension',
                                                    'height'   => false,
                                                    'default'  => array(
                                                        'width' => '400'
                                                    ),
                                                    'required' => array("{$prefix}single_portfolio_gallery_layout", 'in', array('masonry'))
                                                )
                                            )
                                        ),
                                        array(
                                            'id'       => "{$prefix}single_portfolio_gallery_columns_gutter",
                                            'title'    => esc_html__('Portfolio Gallery Columns Gutter', 'spring-framework'),
                                            'subtitle' => esc_html__('Specify your horizontal space between portfolio gallery.', 'spring-framework'),
                                            'type'     => 'select',
                                            'options'  => G5P()->settings()->get_post_columns_gutter(),
                                            'default'  => '10',
                                            'required' => array("{$prefix}single_portfolio_gallery_layout", 'not in', array('thumbnail', 'carousel-3d'))
                                        ),
                                        array(
                                            'id'       => "{$prefix}single_portfolio_gallery_columns_group",
                                            'title'    => esc_html__('Portfolio Gallery Columns', 'spring-framework'),
                                            'type'     => 'group',
                                            'required' => array("{$prefix}single_portfolio_gallery_layout", 'not in', array('thumbnail', 'carousel-3d', 'metro')),
                                            'fields'   => array(
                                                array(
                                                    'id'     => "{$prefix}single_portfolio_gallery_columns_row_1",
                                                    'type'   => 'row',
                                                    'col'    => 3,
                                                    'fields' => array(
                                                        array(
                                                            'id'      => "{$prefix}single_portfolio_gallery_columns",
                                                            'title'   => esc_html__('Large Devices', 'spring-framework'),
                                                            'desc'    => esc_html__('Specify your portfolio gallery columns on large devices (>= 1200px)', 'spring-framework'),
                                                            'type'    => 'select',
                                                            'options' => G5P()->settings()->get_post_columns(),
                                                            'default' => '3',
                                                            'layout'  => 'full',
                                                        ),
                                                        array(
                                                            'id'      => "{$prefix}single_portfolio_gallery_columns_md",
                                                            'title'   => esc_html__('Medium Devices', 'spring-framework'),
                                                            'desc'    => esc_html__('Specify your portfolio gallery columns on medium devices (>= 992px)', 'spring-framework'),
                                                            'type'    => 'select',
                                                            'options' => G5P()->settings()->get_post_columns(),
                                                            'default' => '3',
                                                            'layout'  => 'full',
                                                        ),
                                                        array(
                                                            'id'      => "{$prefix}single_portfolio_gallery_columns_sm",
                                                            'title'   => esc_html__('Small Devices', 'spring-framework'),
                                                            'desc'    => esc_html__('Specify your portfolio gallery columns on small devices (>= 768px)', 'spring-framework'),
                                                            'type'    => 'select',
                                                            'options' => G5P()->settings()->get_post_columns(),
                                                            'default' => '2',
                                                            'layout'  => 'full',
                                                        ),
                                                        array(
                                                            'id'      => "{$prefix}single_portfolio_gallery_columns_xs",
                                                            'title'   => esc_html__('Extra Small Devices ', 'spring-framework'),
                                                            'desc'    => esc_html__('Specify your portfolio gallery columns on extra small devices (< 768px)', 'spring-framework'),
                                                            'type'    => 'select',
                                                            'options' => G5P()->settings()->get_post_columns(),
                                                            'default' => '2',
                                                            'layout'  => 'full',
                                                        ),
                                                        array(
                                                            'id'      => "{$prefix}single_portfolio_gallery_columns_mb",
                                                            'title'   => esc_html__('Extra Extra Small Devices ', 'spring-framework'),
                                                            'desc'    => esc_html__('Specify your portfolio gallery columns on extra extra small devices (< 576px)', 'spring-framework'),
                                                            'type'    => 'select',
                                                            'options' => G5P()->settings()->get_post_columns(),
                                                            'default' => '1',
                                                            'layout'  => 'full',
                                                        )
                                                    )
                                                )
                                            )
                                        )
                                    )
                                ),
                                array(
                                    'id'       => "{$prefix}single_portfolio_custom_link",
                                    'title'    => esc_html__('Custom Link Url', 'spring-framework'),
                                    'subtitle' => esc_html__('Enter custom link url for portfolio.', 'spring-framework'),
                                    'desc'     => esc_html__('Leave blank for post URL.', 'spring-framework'),
                                    'type'     => 'text'
                                ),
                                array(
                                    'id' => "{$prefix}single_portfolio_media_type",
                                    'title' => esc_html__('Media Type','spring-framework'),
                                    'subtitle' => esc_html__('Specify your portfolio media type','spring-framework'),
                                    'type' => 'button_set',
                                    'options' => array(
                                        'image' => esc_html__('Image','spring-framework'),
                                        'video' => esc_html__('Video','spring-framework')
                                    ),
                                    'default' => 'image'

                                ),
                                array(
                                    'id'       => "{$prefix}single_portfolio_gallery",
                                    'title'    => esc_html__('Gallery', 'spring-framework'),
                                    'subtitle' => esc_html__('Specify your portfolio gallery', 'spring-framework'),
                                    'type'     => 'gallery',
                                    'required' => array("{$prefix}single_portfolio_media_type",'=','image')
                                ),
                                array(
                                    'id'       => "{$prefix}single_portfolio_video",
                                    'title'    => esc_html__('Video Url', 'spring-framework'),
                                    'subtitle' => esc_html__('Enter your portfolio Video Url', 'spring-framework'),
                                    'type'     => 'text',
                                    'width' => '100%',
                                    'clone'    => true,
                                    'sort'     => true,
                                    'required' => array("{$prefix}single_portfolio_media_type",'=','video')
                                ),
                            ),
                            apply_filters('gsf_portfolio_meta_after', array())
                        )
                    ),
                    array(
                        'id' => "{$prefix}single_portfolio_related_group",
                        'title' => esc_html__('Related Portfolios','spring-framework'),
                        'icon'   => 'dashicons-before dashicons-images-alt2',
                        'fields' => array(
                            G5P()->configOptions()->get_config_toggle(array(
                                'id' => "{$prefix}single_portfolio_related_enable",
                                'title' => esc_html__('Related Portfolios Enable','spring-framework'),
                                'default' => '',
                                'subtitle' => esc_html__('Turn Off this option if you want to hide related portfolios area on single portfolio','spring-framework')
                            ), true),
                            G5P()->configOptions()->get_config_toggle(array(
                                'id' => "{$prefix}single_portfolio_related_full_width_enable",
                                'title' => esc_html__('Related Portfolios Full Width','spring-framework'),
                                'default' => '',
                                'subtitle' => esc_html__('Turn on this option if you want to related portfolios display full width','spring-framework'),
                                'required' => array("{$prefix}single_portfolio_related_enable", 'in',array('on', ''))
                            ), true),
                            array(
                                'id'       => "{$prefix}single_portfolio_related_algorithm",
                                'title'    => esc_html__('Related Portfolios Algorithm', 'spring-framework'),
                                'subtitle' => esc_html__('Specify the algorithm of related portfolios', 'spring-framework'),
                                'type'     => 'select',
                                'options'  => G5P()->settings()->get_portfolio_related_algorithm(true),
                                'default'  => '',
                                'required' => array("{$prefix}single_portfolio_related_enable", 'in',array('on', ''))
                            ),
                            G5P()->configOptions()->get_config_toggle(array(
                                'id'       => "{$prefix}single_portfolio_related_carousel_enable",
                                'title'    => esc_html__('Carousel Mode', 'spring-framework'),
                                'subtitle' => esc_html__('Turn Off this option if you want to disable carousel mode', 'spring-framework'),
                                'default'  => '',
                                'required' => array("{$prefix}single_portfolio_related_enable", 'in',array('on', ''))
                            ), true),
                            array(
                                'id'         => "{$prefix}single_portfolio_related_per_page",
                                'title'      => esc_html__('Portfolios Per Page', 'spring-framework'),
                                'subtitle'   => esc_html__('Enter number of portfolios per page you want to display', 'spring-framework'),
                                'type'       => 'text',
                                'input_type' => 'number',
                                'required'   => array("{$prefix}single_portfolio_related_enable", 'in',array('on', ''))
                            ),
                            array(
                                'id'       => "{$prefix}single_portfolio_related_columns_gutter",
                                'title'    => esc_html__('Related Portfolios Columns Gutter', 'spring-framework'),
                                'subtitle' => esc_html__('Specify your horizontal space between portfolio related.', 'spring-framework'),
                                'type'     => 'select',
                                'options'  => G5P()->settings()->get_post_columns_gutter(true),
                                'default'  => '',
                                'required' => array("{$prefix}single_portfolio_related_enable", 'in',array('on', ''))
                            ),
                            array(
                                'id'       => "{$prefix}single_portfolio_related_columns_group",
                                'title'    => esc_html__('Related Portfolios Columns', 'spring-framework'),
                                'type'     => 'group',
                                'required' => array("{$prefix}single_portfolio_related_enable", 'in',array('on', '')),
                                'fields'   => array(
                                    array(
                                        'id'     => "{$prefix}single_portfolio_related_columns_row_1",
                                        'type'   => 'row',
                                        'col'    => 3,
                                        'fields' => array(
                                            array(
                                                'id'      => "{$prefix}single_portfolio_related_columns",
                                                'title'   => esc_html__('Large Devices', 'spring-framework'),
                                                'desc'    => esc_html__('Specify your related portfolios columns on large devices (>= 1200px)', 'spring-framework'),
                                                'type'    => 'select',
                                                'options' => G5P()->settings()->get_post_columns(true),
                                                'default' => '',
                                                'layout'  => 'full',
                                            ),
                                            array(
                                                'id'      => "{$prefix}single_portfolio_related_columns_md",
                                                'title'   => esc_html__('Medium Devices', 'spring-framework'),
                                                'desc'    => esc_html__('Specify your related portfolios columns on medium devices (>= 992px)', 'spring-framework'),
                                                'type'    => 'select',
                                                'options' => G5P()->settings()->get_post_columns(true),
                                                'default' => '',
                                                'layout'  => 'full',
                                            ),
                                            array(
                                                'id'      => "{$prefix}single_portfolio_related_columns_sm",
                                                'title'   => esc_html__('Small Devices', 'spring-framework'),
                                                'desc'    => esc_html__('Specify your related portfolios columns on small devices (>= 768px)', 'spring-framework'),
                                                'type'    => 'select',
                                                'options' => G5P()->settings()->get_post_columns(true),
                                                'default' => '',
                                                'layout'  => 'full',
                                            ),
                                            array(
                                                'id'      => "{$prefix}single_portfolio_related_columns_xs",
                                                'title'   => esc_html__('Extra Small Devices ', 'spring-framework'),
                                                'desc'    => esc_html__('Specify your related portfolios columns on extra small devices (< 768px)', 'spring-framework'),
                                                'type'    => 'select',
                                                'options' => G5P()->settings()->get_post_columns(true),
                                                'default' => '',
                                                'layout'  => 'full',
                                            ),
                                            array(
                                                'id'      => "{$prefix}single_portfolio_related_columns_mb",
                                                'title'   => esc_html__('Extra Extra Small Devices ', 'spring-framework'),
                                                'desc'    => esc_html__('Specify your related portfolios columns on extra extra small devices (< 576px)', 'spring-framework'),
                                                'type'    => 'select',
                                                'options' => G5P()->settings()->get_post_columns(true),
                                                'default' => '',
                                                'layout'  => 'full',
                                            )
                                        )
                                    ),
                                )
                            ),
                            array(
                                'id'       => "{$prefix}single_portfolio_related_post_paging",
                                'title'    => esc_html__('Portfolios Paging', 'spring-framework'),
                                'subtitle' => esc_html__('Specify your portfolios paging mode', 'spring-framework'),
                                'type'     => 'select',
                                'options'  => G5P()->settings()->get_post_paging_small_mode(true),
                                'default'  => '',
                                'required' => array(
                                    array("{$prefix}single_portfolio_related_carousel_enable", '!=' ,'on'),
                                    array("{$prefix}single_portfolio_related_enable", 'in',array('on', ''))
                                )
                            ),
                            array(
                                'id'       => "{$prefix}single_portfolio_related_animation",
                                'title'    => esc_html__('Animation', 'spring-framework'),
                                'subtitle' => esc_html__('Specify your portfolios animation', 'spring-framework'),
                                'type'     => 'select',
                                'options'  => G5P()->settings()->get_animation(true),
                                'default'  => '',
                                'required' => array("{$prefix}single_portfolio_related_enable", '=', 'on')
                            )
                        )
                    )
                ),

            );
            return $configs;
        }

        public function register_meta_boxes_info($configs) {
            $prefix = G5P()->getMetaPrefix();
            $single_portfolio_info =  G5P()->options()->get_single_portfolio_details();
            $single_portfolio_info_configs = array();
            foreach ($single_portfolio_info as $item) {
                if (empty($item['title'])) continue;

                $single_portfolio_info_config = array(
                    'id' => "{$prefix}{$item['id']}",
                    'title' => $item['title'],
                    'type' => 'editor',
                    'subtitle' => sprintf(esc_html__('Enter %s','spring-framework'),$item['title'])
                );
                $single_portfolio_info_configs[] = $single_portfolio_info_config;
            }

            if (count($single_portfolio_info_configs) > 0) {
                $configs = array_merge($configs,array(
                        array(
                            'id' => 'section_single_portfolio_info',
                            'title' => esc_html__('Portfolio Details','spring-framework'),
                            'type' => 'group',
                            'fields' => $single_portfolio_info_configs
                        ))
                );
            }
            return $configs;
        }

        public function set_portfolio_single_to_option() {
            if (is_singular($this->_post_type)) {
                $prefix = G5P()->getMetaPrefix();
                $single_portfolio_layout = G5P()->metaBoxPortfolio()->get_single_portfolio_layout();
                if ($single_portfolio_layout !== '') {
                    G5P()->options()->setOptions('single_portfolio_layout',$single_portfolio_layout);
                    $gallery_config = array(
                        'single_portfolio_gallery_image_size',
                        'single_portfolio_gallery_image_ratio',
                        'single_portfolio_gallery_image_ratio_custom',
                        'single_portfolio_gallery_columns_gutter',
                        'single_portfolio_gallery_columns',
                        'single_portfolio_gallery_columns_md',
                        'single_portfolio_gallery_columns_sm',
                        'single_portfolio_gallery_columns_xs',
                        'single_portfolio_gallery_columns_mb'
                    );
                    foreach ($gallery_config as $config) {
                        $value = G5P()->metaBoxPortfolio()->getMetaValue("{$prefix}{$config}");
                        if ($value !== '') {
                            G5P()->options()->setOptions($config,$value);
                        }
                    }

                }
            }
        }
    }
}