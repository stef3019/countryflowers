<?php
if (!defined('ABSPATH')) {
	exit('Direct script access denied.');
}
if (!class_exists('Spring_Plant_Inc_Query')) {
	class Spring_Plant_Inc_Query {

		private static $_instance;
		public static function getInstance()
		{
			if (self::$_instance == NULL) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}



		public function get_query() {
		    global $gsf_query;
            if (!$gsf_query instanceof WP_Query) {
                global $wp_query;
                $gsf_query = $wp_query;
            }
            return $gsf_query;
        }


		public function get_main_query_vars($query_args = array()) {
            $settings =  Spring_Plant()->blog()->get_layout_settings();
			if (!isset($query_args)) {
				global $wp_query;
				$query_args = $wp_query->query_vars;
			} else {
				if ((in_array($settings['post_paging'],array('pagination-ajax','pagination'))) && !isset($query_args['paged'])) {
					$query_args['paged']   =  get_query_var( 'page' ) ? intval( get_query_var( 'page' ) ) : (get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1);
				}
			}


			// remove empty vars
			foreach ( $query_args as $_a => $_v ) {
				if ( is_array( $_v ) ) {
					if ( count( $_v ) === 0 ) {
						unset( $query_args[ $_a ] );
					}
				} else {
					if ( empty( $_v ) || $_v === 0 ) {
						unset( $query_args[ $_a ] );
					}
				}
			}


            if (!isset($query_args['post_status'])) {
                $query_args['post_status'] = 'publish';
            }

			if (isset($query_args['tag__in'])) {
				unset($query_args['tag_id']);
			}

			if (isset($settings['category_filter']) && is_array($settings['category_filter'])&& !isset($query_args['gf_cat'])) {
				unset($query_args['cat']);
				unset($query_args['category_name']);
                unset($query_args['term']);
                unset($query_args['taxonomy']);
			}

			// Remove extra vars
			unset( $query_args['suppress_filters'] );
			unset( $query_args['cache_results'] );
			unset( $query_args['update_post_term_cache'] );
			unset( $query_args['update_post_meta_cache'] );
			unset( $query_args['comments_per_page'] );
			unset( $query_args['no_found_rows'] );
			unset( $query_args['search_orderby_title'] );
			unset($query_args['lazy_load_term_meta']);

			return $query_args;
		}

		public function get_ajax_query_vars($query_args = array()) {
			$gsf_query = $this->get_query();
			$query_args = wp_parse_args($query_args,$gsf_query->query_vars);
			// remove empty vars
			foreach ($query_args as $_a => $_v ) {
				if ( is_array( $_v ) ) {
					if ( count( $_v ) === 0 ) {
						unset( $query_args[ $_a ] );
					}
				} else {
					if ( empty( $_v ) || $_v === 0 ) {
						unset( $query_args[ $_a ] );
					}
				}
			}


			if (!isset($query_args['paged'])) {
				$query_args['paged']   =  get_query_var( 'page' ) ? intval( get_query_var( 'page' ) ) : (get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1);
			}

			if (isset($query_args['tag__in'])) {
				unset($query_args['tag_id']);
			}
            $settings =  Spring_Plant()->blog()->get_layout_settings();
            if (isset($settings['category_filter']) && is_array($settings['category_filter'])&& !isset($query_args['gf_cat'])) {
                unset($query_args['cat']);
                unset($query_args['category_name']);
                unset($query_args['term']);
                unset($query_args['taxonomy']);
            }


			// Remove extra vars
			unset( $query_args['suppress_filters'] );
			unset( $query_args['cache_results'] );
			unset( $query_args['update_post_term_cache'] );
			unset( $query_args['update_post_meta_cache'] );
			unset( $query_args['comments_per_page'] );
			unset( $query_args['no_found_rows'] );
			unset( $query_args['search_orderby_title'] );
			unset($query_args['lazy_load_term_meta']);
			return $query_args;
		}

		public function parse_ajax_query($query = array()) {

			if (!isset($query['post_status'])) {
				$query['post_status'] = 'publish';
			}

			if (!isset($query['paged'])) {
				$query['paged'] = 1;
			}

			global $paged;
			$paged = $query['paged'];

			return $query;

		}

        public function query_posts($query){
            $GLOBALS['gsf_query'] = new WP_Query();
            return $GLOBALS['gsf_query']->query($query);
        }

        public function reset_query(){
            $GLOBALS['gsf_query'] = $GLOBALS['wp_the_query'];
            $this->reset_postdata();
        }

        public function have_posts() {
            $gsf_query = $this->get_query();
            return $gsf_query->have_posts();
        }

        public function the_post() {
            $gsf_query = $this->get_query();
            $gsf_query->the_post();
        }

        public function reset_postdata() {
            $gsf_query = $this->get_query();
            $gsf_query->reset_postdata();
        }

        public function query_var_paged() {
            $gsf_query = $this->get_query();
            return $gsf_query->get( 'page' ) ? intval( $gsf_query->get( 'page' ) ) : ($gsf_query->get( 'paged' ) ? intval( $gsf_query->get( 'paged' ) ) : 1);
        }

        public function get_max_num_pages(){
            $gsf_query = $this->get_query();
            return $gsf_query->max_num_pages;
        }

        public function pre_get_posts($query) {

			// add post sticky
			if (!is_admin() && isset($query->query_vars['is_home']) && ($query->get('cat', '') === '')) {
				$query->is_home = true;
			}

			if ( ! is_admin() && $query->is_main_query() ) {
				if ($query->is_search() && $query->get('post_type') !== 'product') {
					$search_post_type = Spring_Plant()->options()->get_search_post_type();
					$query->set('post_type',$search_post_type);
				}
			}
		}
	}
}