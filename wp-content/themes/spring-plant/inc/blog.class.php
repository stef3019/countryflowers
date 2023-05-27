<?php
/**
 * Class Blog
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
if (!defined('ABSPATH')) {
	exit('Direct script access denied.');
}
if (!class_exists('Spring_Plant_Inc_Blog')) {
	class Spring_Plant_Inc_Blog
	{

		public $key_post_layout_settings = 'gf_post_layout_settings';

		private static $_instance;

		public static function getInstance()
		{
			if (self::$_instance == NULL) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}


		public function render_post_thumbnail_markup($args = array())
		{
			$defaults = array(
				'post_id'            => get_the_ID(),
				'image_size'         => 'full',
				'placeholder_enable' => false,
                'first_image_enable' => false,
				'display_permalink'  => true,
				'mode'               => 'simple',
				'image_mode'         => 'background',
				'image_ratio' => '',
				'post_layout' =>''
			);
			$defaults = wp_parse_args($args, $defaults);
			Spring_Plant()->helper()->getTemplate('loop/post-thumbnail', $defaults);
		}

		public function render_post_image_markup($args = array())
		{
			$defaults = array(
				'post_id'           => get_the_ID(),
				'image_id'          => '',
				'image_size'        => 'full',
				'gallery_id'        => '',
				'display_permalink' => true,
				'image_mode'        => 'background',
                'class'             => 'entry-thumbnail'
			);
			$defaults = wp_parse_args($args, $defaults);
			Spring_Plant()->helper()->getTemplate('loop/post-image', $defaults);
		}

		public function get_image_sizes()
		{
            $post_image_width = Spring_Plant()->options()->get_post_image_width();
			$image_sizes = array(
				'blog-large'      => '870x515',
				'blog-medium'     => '300x190',
                'blog-widget'     => '115x75',
				'blog-masonry'    => (isset($post_image_width['width']) && !empty($post_image_width['width'])) ?  ($post_image_width['width']. 'x0') : '370x0',
				'blog-zigzac'	  => '960x580',
				'blog-list' 	  => '600x330'
			);
			return apply_filters('spring_plant_image_sizes', $image_sizes);
		}

		public function pagination_markup()
		{
			$settings = &$this->get_layout_settings();
			$post_paging = $settings['post_paging'];
            $max_num_pages = Spring_Plant()->query()->get_max_num_pages();
			if (($max_num_pages > 1) && ($post_paging !== '') && ($post_paging !== 'none')) {

				if (!isset($settings['settingId']) || $settings['settingId'] === '') {
					$settingId = mt_rand();
				} else {
					$settingId = $settings['settingId'];
				}

				if (!isset($settings['pagenum_link']) || $settings['pagenum_link'] === '') {
					$pagenum_link = html_entity_decode(get_pagenum_link());
					$settings['pagenum_link'] = $pagenum_link;
				} else {
					$pagenum_link = $settings['pagenum_link'];
				}
				if (($post_paging !== 'pagination') && (!isset($_REQUEST['action']) || empty($_REQUEST['action']))) {

					$query_args = array();
					if (is_home()) {
						$query_args['is_home'] = true;
					}
					Spring_Plant()->custom_js()->addJsVariable(array(
						'settings' => $settings,
						'query'    => Spring_Plant()->query()->get_ajax_query_vars($query_args)
					), "spring_plant_ajax_paginate_{$settingId}");
				}

				Spring_Plant()->helper()->getTemplate("paging/{$post_paging}", array('settingId' => $settingId, 'pagenum_link' => $pagenum_link));
			}
		}

		public function category_filter_markup()
		{
			$settings = &$this->get_layout_settings();
			if (!isset($settings['settingId']) || $settings['settingId'] === '') {
				$settingId = mt_rand();
			} else {
				$settingId = $settings['settingId'];
			}

			if (!isset($settings['pagenum_link']) || $settings['pagenum_link'] === '') {
				$pagenum_link = html_entity_decode(get_pagenum_link());
				$settings['pagenum_link'] = $pagenum_link;
			} else {
				$pagenum_link = $settings['pagenum_link'];
			}


			if (!isset($_REQUEST['action']) || empty($_REQUEST['action'])) {
				$query_args = array();
				if (is_home()) {
					$query_args['is_home'] = true;
				}

				Spring_Plant()->custom_js()->addJsVariable(array(
					'settings' => $settings,
					'query'    => Spring_Plant()->query()->get_ajax_query_vars($query_args)
				), "spring_plant_ajax_paginate_{$settingId}");
			}
			Spring_Plant()->helper()->getTemplate("loop/cat-filter", array(
			    'settingId' => $settingId,
                'pagenum_link' => $pagenum_link,
                'post_type' => $settings['post_type'],
                'taxonomy' => isset($settings['taxonomy']) ? $settings['taxonomy'] : 'category',
                'category_filter' => isset($settings['cat']) ? $settings['cat'] : '',
                'current_cat' => isset($settings['current_cat']) ? $settings['current_cat'] : -1,
                'filter_vertical' => isset($settings['category_filter_vertical']) ? $settings['category_filter_vertical'] : false,
                'filter_type' => isset($settings['category_filter_type']) ? $settings['category_filter_type'] : ''
            ));
		}

		public function tabs_markup() {
			$settings = &$this->get_layout_settings();
			$tabs = isset($settings['tabs']) ? $settings['tabs'] : array();
			unset($settings['tabs']);
			if (!isset($_REQUEST['action']) || empty($_REQUEST['action'])) {
				$index = 1;
				foreach ($tabs as &$tab) {
					$settingId = mt_rand();
					$query_args = $tab['query_args'];
					$tab['settingId'] = $settingId;
					if ($index === 1) {
						$settings['settingId'] = $settingId;
					}

					if (is_home()) {
						$query_args['is_home'] = true;
					}
					Spring_Plant()->custom_js()->addJsVariable(array(
						'settings' => $settings,
						'query'    => Spring_Plant()->query()->get_ajax_query_vars($query_args)
					), "spring_plant_ajax_paginate_{$settingId}");
					$index++;
				}
			}
			Spring_Plant()->helper()->getTemplate("loop/tabs", array('tabs' => $tabs));
		}


		/**
		 * Get Post Layout Settings
		 *
		 * @return mixed
		 */
		public function &get_layout_settings()
		{
			if (isset($GLOBALS[$this->key_post_layout_settings]) && is_array($GLOBALS[$this->key_post_layout_settings])) {
				return $GLOBALS[$this->key_post_layout_settings];
			}

			$GLOBALS[$this->key_post_layout_settings] = array(
				'post_layout'            => Spring_Plant()->options()->get_post_layout(),
				'post_item_skin'         => Spring_Plant()->options()->get_post_item_skin(),
				'post_image_size'  => Spring_Plant()->options()->get_post_image_size(),
				'post_columns'           => array(
					'xl' => intval(Spring_Plant()->options()->get_post_columns()),
					'lg' => intval(Spring_Plant()->options()->get_post_columns_md()),
					'md' => intval(Spring_Plant()->options()->get_post_columns_sm()),
					'sm' => intval(Spring_Plant()->options()->get_post_columns_xs()),
                    '' => intval(Spring_Plant()->options()->get_post_columns_mb()),
				),
				'post_columns_gutter'    => intval(Spring_Plant()->options()->get_post_columns_gutter()),
				'post_paging'            => Spring_Plant()->options()->get_post_paging(),
				'post_animation'         => Spring_Plant()->options()->get_post_animation(),
				'itemSelector'           => 'article',
				'category_filter_enable' => false,
                'category_filter_align' => '',
                'post_type' => 'post'
			);
			return $GLOBALS[$this->key_post_layout_settings];
		}

		public function unset_layout_settings()
		{
			unset($GLOBALS[$this->key_post_layout_settings]);
		}

		/**
		 * Set Post Layout Settings
		 *
		 * @param $args
		 */
		public function set_layout_settings($args)
		{
			$post_settings = &$this->get_layout_settings();
			$post_settings = wp_parse_args($args, $post_settings);
		}

		public function archive_markup($query_args = null, $settings = null)
		{
		    if (isset($settings['tabs']) && isset($settings['tabs'][0]['query_args'])) {
                $query_args = $settings['tabs'][0]['query_args'];
            }

			if (!isset($query_args)) {
				$settings['isMainQuery'] = true;
			}

			if (isset($settings) && (count($settings) > 0)) {
				$this->set_layout_settings($settings);
			}

            $query_args = Spring_Plant()->query()->get_main_query_vars( $query_args );
            Spring_Plant()->query()->query_posts( $query_args );


			if (isset($settings['category_filter_enable']) && $settings['category_filter_enable'] === true) {
				add_action('spring_plant_before_archive_wrapper', array($this, 'category_filter_markup'));
			}

			if (isset($settings['tabs'])) {
				add_action('spring_plant_before_archive_wrapper', array($this, 'tabs_markup'));
			}

			Spring_Plant()->helper()->getTemplate('archive');

			if (isset($settings['tabs'])) {
				remove_action('spring_plant_before_archive_wrapper', array($this, 'tabs_markup'));
			}

			if (isset($settings['category_filter_enable']) && $settings['category_filter_enable'] === true) {
				remove_action('spring_plant_before_archive_wrapper', array($this, 'category_filter_markup'));
			}

			if (isset($settings) && (count($settings) > 0)) {
				$this->unset_layout_settings();
			}

            Spring_Plant()->query()->reset_query();

		}

		/**
		 * Get Primary Category
		 *
		 * @return array|mixed|null|object|WP_Error
		 */
		public function get_primary_cat()
		{
			// Primary category from Yoast SEO plugin
			if (class_exists('WPSEO_Primary_Term')) {
				$prim_cat = get_post_meta(get_the_ID(), '_yoast_wpseo_primary_category', true);
				if ($prim_cat) {
					$prim_cat = get_category($prim_cat);
					if (!is_wp_error($prim_cat)) {
						return $prim_cat;
					}
				}
			}

			$gsf_query = Spring_Plant()->query()->get_query();
			$prim_cat = intval($gsf_query->get('gf_cat', -1));
			if ($prim_cat > 0) {
				$prim_cat = get_category($prim_cat);
				if (!is_wp_error($prim_cat)) {
					return $prim_cat;
				}
			}

			$category__in = $gsf_query->get('category__in', array());
			if (count($category__in) > 0) {
				$categories = get_the_category();
				$arr_count = count($categories);
				for ($i = 0; $i < $arr_count; $i++) {
					if (!in_array($categories[$i]->term_id, $category__in)) {
						unset($categories[$i]);
					}
				}
				if (count($categories) > 0) {
					return current($categories);
				}

			}

			// First cat
			return current(get_the_category());
		}

		public function archive_ads_markup($args)
		{
			Spring_Plant()->helper()->getTemplate('loop/ads', $args);
		}

		public function get_layout_matrix($layout = 'large-image')
		{
			$post_settings = &Spring_Plant()->blog()->get_layout_settings();
			$post_type = isset($post_settings['post_type']) ? $post_settings['post_type'] : 'post';
			$columns = isset($post_settings['post_columns']) ? $post_settings['post_columns'] : array(
				'xl' => 2,
				'lg' => 2,
				'md' => 1,
				'sm' => 1,
                '' => 1
			);
			$columns = Spring_Plant()->helper()->get_bootstrap_columns($columns);
			$placeholder_enable = 'on' === Spring_Plant()->options()->get_default_thumbnail_placeholder_enable() ? true : false;
            $first_image_enable = 'on' === Spring_Plant()->options()->get_first_image_as_post_thumbnail() ? true : false;
			$columns_gutter = intval(isset($post_settings['post_columns_gutter']) ? $post_settings['post_columns_gutter'] : 30);
			$matrix = apply_filters('spring_plant_post_layout_matrix',array(
			    'post' => array(
                    'large-image'    => array(
                        'placeholder_enable' => $placeholder_enable,
                        'first_image_enable' => $first_image_enable,
                        'layout'             => array(
                            array('columns' => 'col-12', 'template' => 'large-image', 'image_size' => 'blog-large'),
                        )
                    ),
                    'medium-image'   => array(
                        'placeholder_enable' => $placeholder_enable,
                        'first_image_enable' => $first_image_enable,
                        'layout'         => array(
                            array('columns' => 'col-12 col-sm-12', 'template' => 'medium-image', 'image_size' => 'blog-medium'),
                        )
                    ),
                    'grid'           => array(
                        'placeholder_enable' => $placeholder_enable,
                        'first_image_enable' => $first_image_enable,
                        'columns_gutter' => $columns_gutter,
                        'layout'         => array(
                            array('columns' => $columns, 'template' => 'grid')
                        )
                    ),
                    'masonry'        => array(
						'placeholder_enable' => $placeholder_enable,
                        'first_image_enable' => $first_image_enable,
                        'columns_gutter' => $columns_gutter,
                        'isotope'        => array(
                            'itemSelector' => 'article',
                            'layoutMode'   => 'masonry',
                        ),
                        'layout'         => array(
                            array('columns' => $columns, 'template' => 'grid',  'image_size' => 'blog-masonry'),
                        )
                    ),
					'zigzac'    => array(
						'placeholder_enable' => $placeholder_enable,
                        'first_image_enable' => $first_image_enable,
						'layout'             => array(
							array('columns' => 'col-12', 'template' => 'zigzac'),
						)
					),
					'list'    => array(
						'placeholder_enable' => $placeholder_enable,
                        'first_image_enable' => $first_image_enable,
						'layout'             => array(
							array('columns' => 'col-12', 'template' => 'list'),
						)
					),
					'list-no-img'    => array(
						'layout'             => array(
							array('columns' => 'col-12', 'template' => 'list-no-img'),
						)
					)
                )
			));
			if (!isset($matrix[$post_type][$layout])) return $matrix['post']['large-image'];
			return $matrix[$post_type][$layout];
		}


		public function get_list_comments_args($args = array())
		{
			// Default arguments for listing comments.
			$defaults = array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 70,
				'callback'    => 'spring_plant_comments_callback'
			);
			// Filter default arguments to enable developers to change it. also return it.
			return apply_filters('spring_plant_list_comments_args', wp_parse_args($args, $defaults));
		}

		public function get_comments_form_args($args = array())
		{
			$commenter = wp_get_current_commenter();
			$req = get_option('require_name_email');
			$aria_req = ($req ? " aria-required='true'" : '');
			$html_req = ($req ? " required='required'" : '');
			$html5 = true;
			$fields = array(
				'author' => '<p class="comment-form-author">' . '<label for="author">' . esc_html__('Name', 'spring-plant') . ($req ? ' <span class="required">*</span>' : '') . '</label> ' .
					'<input placeholder="' . esc_attr__('Name', 'spring-plant') . '" id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" maxlength="245"' . $aria_req . $html_req . ' /></p>',
				'email'  => '<p class="comment-form-email"><label for="email">' . esc_html__('Email', 'spring-plant') . ($req ? ' <span class="required">*</span>' : '') . '</label> ' .
					'<input placeholder="' . esc_attr__('Email', 'spring-plant') . '" id="email" name="email" ' . ($html5 ? 'type="email"' : 'type="text"') . ' value="' . esc_attr($commenter['comment_author_email']) . '" size="30" maxlength="100" aria-describedby="email-notes"' . $aria_req . $html_req . ' /></p>',
			);


			$defaults = array(
				'fields'             => $fields,
				'comment_field'      => '<p class="comment-form-comment"><label for="comment">' . esc_html__('Comment', 'spring-plant') . '</label> <textarea placeholder="' . esc_attr__('Comment', 'spring-plant') . '" id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="required"></textarea></p>',
				'title_reply'        => esc_html__('Leave your comment', 'spring-plant'),
				'title_reply_before' => '<h4 id="reply-title" class="gf-heading-title"><span>',
				'title_reply_after'  => '</span></h4>',
				'label_submit'       =>  esc_html__('Submit', 'spring-plant'),
				'class_submit'       => 'btn btn-md btn-block btn-rounded'
			);

			// Filter default arguments to enable developers to change it. also return it.
			return apply_filters('spring_plant_comments_form_args', wp_parse_args($args, $defaults));
		}
	}
}