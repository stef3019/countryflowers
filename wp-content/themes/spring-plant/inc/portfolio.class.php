<?php
if (!defined('ABSPATH')) {
    exit('Direct script access denied.');
}
if (!class_exists('Spring_Plant_Inc_Portfolio')) {
    class Spring_Plant_Inc_Portfolio
    {
        private static $_instance;
        public static function getInstance()
        {
            if (self::$_instance == NULL) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        private $_post_type = 'portfolio';
        private $_taxonomy_category = 'portfolio_cat';

        public function get_post_type() {
            return $this->_post_type;
        }

        public function get_taxonomy_category() {
            return $this->_taxonomy_category;
        }

        public function init() {
            add_filter('spring_plant_post_layout_matrix', array($this, 'layout_matrix'),10,4);
            add_action('spring_plant_after_single_portfolio',array($this,'portfolio_controls'), 15);
            add_action('spring_plant_after_single_portfolio',array($this,'portfolio_related'), 15);
            add_action('wp_head', array($this, 'portfolio_single_layout'), 10);
        }

        public function render_thumbnail_markup($args = array())
        {
            $defaults = array(
                'post_id'            => get_the_ID(),
                'image_size'         => 'thumbnail',
                'placeholder_enable' => true,
                'image_mode'         => 'background',
                'image_ratio'        => ''
            );
            $defaults = wp_parse_args($args, $defaults);
            Spring_Plant()->helper()->getTemplate('portfolio/thumbnail', $defaults);
        }

        public function layout_matrix($matrix) {
            $post_settings = Spring_Plant()->blog()->get_layout_settings();
            if ($post_settings['post_type'] !== 'portfolio') {
                $post_settings = Spring_Plant()->portfolio()->get_layout_settings();
            }
            $columns = isset($post_settings['post_columns']) ? $post_settings['post_columns'] : array(
                'xl' => 3,
                'lg' => 3,
                'md' => 2,
                'sm' => 1,
                '' => 1
            );
            $columns_class = Spring_Plant()->helper()->get_bootstrap_columns($columns);
            $columns_gutter = intval(isset($post_settings['post_columns_gutter']) ? $post_settings['post_columns_gutter'] : 30);
            $matrix[$this->get_post_type()] = array(
                'grid' => array(
                    'image_size' => Spring_Plant()->options()->get_portfolio_image_size(),
                    'placeholder_enable' => true,
                    'columns_gutter'     => $columns_gutter,
                    /*'isotope'            => array(
                        'itemSelector' => 'article',
                        'layoutMode'   => 'fitRows',
                    ),*/
                    'layout'             => array(
                        array('columns' => $columns_class, 'template' => 'grid')
                    )
                ),
                'list'    => array(
                    'placeholder_enable' => true,
                    'layout'             => array(
                        array('columns' => 'col-12', 'template' => 'list'),
                    )
                ),
                'carousel' => array(
                    'image_size' => Spring_Plant()->options()->get_portfolio_image_size(),
                    'placeholder_enable' => true,
                    'columns_gutter'     => $columns_gutter,
                    /*'isotope'            => array(
                        'itemSelector' => 'article',
                        'layoutMode'   => 'fitRows',
                    ),*/
                    'layout'             => array(
                        array('columns' => $columns_class, 'template' => 'grid')
                    )
                ),
                'masonry' => array(
                    'columns_gutter'     => $columns_gutter,
                    'isotope'            => array(
                        'itemSelector' => 'article',
                        'layoutMode'   => 'masonry',
                    ),
                    'layout'             => array(
                        array('columns' => $columns_class, 'template' => 'grid')
                    )
                ),
                'scattered' => array(
                    'columns_gutter'     => '0',
                    'layout'             => array(
                        array('columns' => 'scattered-index-1 ' . Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 3,'lg' => 3,'md' => 1,'sm' => 1,'' => 1)), 'template' => 'grid','image_size' => '320x320'),
                        array('columns' => 'scattered-index-2 ' . Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 3,'lg' => 3,'md' => 1,'sm' => 1,'' => 1)), 'template' => 'grid','image_size' => '280x470'),
                        array('columns' => 'scattered-index-3 ' . Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 3,'lg' => 3,'md' => 1,'sm' => 1,'' => 1)), 'template' => 'grid','image_size' => '320x240'),
                        array('columns' => 'scattered-index-4 ' . Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 1.5,'lg' => 1.5,'md' => 1,'sm' => 1,'' => 1)), 'template' => 'grid','image_size' => '570x240'),
                        array('columns' => 'scattered-index-5 ' . Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 3,'lg' => 3,'md' => 1,'sm' => 1,'' => 1)), 'template' => 'grid','image_size' => '280x360'),
                        array('columns' => 'scattered-index-6 ' . Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 3,'lg' => 3,'md' => 1,'sm' => 1,'' => 1)), 'template' => 'grid','image_size' => '320x320'),
                        array('columns' => 'scattered-index-7 ' . Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 3,'lg' => 3,'md' => 1,'sm' => 1,'' => 1)), 'template' => 'grid','image_size' => '320x240'),
                        array('columns' => 'scattered-index-8 ' . Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 3,'lg' => 3,'md' => 1,'sm' => 1,'' => 1)), 'template' => 'grid','image_size' => '320x320'),

                    )
                ),
                'metro-1' => array(
                    'columns_gutter'     => $columns_gutter,
                    'isotope'            => array(
                        'itemSelector' => 'article',
                        'layoutMode'   => 'masonry',
                        'percentPosition' => true,
                        'masonry' => array(
                            'columnWidth' => '.gsf-column-base',
                        ),
                        'metro' => true
                    ),
                    'image_size' => Spring_Plant()->options()->get_portfolio_image_size(),
                    'layout'             => array(
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid','layout_ratio' => '2x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid','layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid','layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid','layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid','layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid','layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid','layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid','layout_ratio' => '2x1'),

                    )
                ),
                'metro-2' => array(
                    'columns_gutter'     => $columns_gutter,
                    'isotope'            => array(
                        'itemSelector' => 'article',
                        'layoutMode'   => 'masonry',
                        'percentPosition' => true,
                        'masonry' => array(
                            'columnWidth' => '.gsf-column-base',
                        ),
                        'metro' => true
                    ),
                    'image_size' => Spring_Plant()->options()->get_portfolio_image_size(),
                    'layout'             => array(
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '2x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid','layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid','layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid','layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid','layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid','layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid','layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid','layout_ratio' => '2x1'),

                    )
                ),
                'metro-3' => array(
                    'columns_gutter'     => $columns_gutter,
                    'isotope'            => array(
                        'itemSelector' => 'article',
                        'layoutMode'   => 'masonry',
                        'percentPosition' => true,
                        'masonry' => array(
                            'columnWidth' => '.gsf-column-base',
                        ),
                        'metro' => true
                    ),
                    'image_size' => Spring_Plant()->options()->get_portfolio_image_size(),
                    'layout'             => array(
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '2x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid','layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid','layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid','layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid','layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid','layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid','layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '2x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid','layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid','layout_ratio' => '1x1'),
                    )
                ),
                'metro-4' => array(
                    'columns_gutter'     => $columns_gutter,
                    'isotope'            => array(
                        'itemSelector' => 'article',
                        'layoutMode'   => 'masonry',
                        'percentPosition' => true,
                        'masonry' => array(
                            'columnWidth' => '.gsf-column-base',
                        ),
                        'metro' => true
                    ),
                    'image_size' => Spring_Plant()->options()->get_portfolio_image_size(),
                    'layout'             => array(
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1.344512195'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1.344512195'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1.344512195'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1.344512195'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                    )
                ),
                'metro-5' => array(
                    'columns_gutter' => $columns_gutter,
                    'image_size' => Spring_Plant()->options()->get_portfolio_image_size(),
                    'isotope' =>  array(
                        'itemSelector' => 'article',
                        'layoutMode'   => 'masonry',
                        'percentPosition' => true,
                        'masonry' => array(
                            'columnWidth' => '.gsf-column-base',
                        ),
                        'metro' => true
                    ),
                    'layout' => array(
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x0.625'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1.1875'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x0.8125'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1.1875'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x0.8125'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x0.625'),
                    )
                ),
                'metro-6' => array(
                    'columns_gutter' => $columns_gutter,
                    'image_size' => Spring_Plant()->options()->get_portfolio_image_size(),
                    'isotope' =>  array(
                        'itemSelector' => 'article',
                        'layoutMode'   => 'masonry',
                        'percentPosition' => true,
                        'masonry' => array(
                            'columnWidth' => '.gsf-column-base',
                        ),
                        'metro' => true
                    ),
                    'layout' => array(
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '2x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '2x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '2x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '2x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '2x1')
                    )
                ),
                'metro-7' => array(
                    'columns_gutter' => $columns_gutter,
                    'image_size' => Spring_Plant()->options()->get_portfolio_image_size(),
                    'isotope' =>  array(
                        'itemSelector' => 'article',
                        'layoutMode'   => 'masonry',
                        'percentPosition' => true,
                        'masonry' => array(
                            'columnWidth' => '.gsf-column-base',
                        ),
                        'metro' => true
                    ),
                    'layout' => array(
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '2x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '2x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '2x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '2x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '2x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1')
                    )
                ),
                'metro-8' => array(
                    'columns_gutter' => $columns_gutter,
                    'image_size' => Spring_Plant()->options()->get_portfolio_image_size(),
                    'isotope' =>  array(
                        'itemSelector' => 'article',
                        'layoutMode'   => 'masonry',
                        'percentPosition' => true,
                        'masonry' => array(
                            'columnWidth' => '.gsf-column-base',
                        ),
                        'metro' => true
                    ),
                    'layout' => array(
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2.4,'lg' => 1,'md' => 1,'sm' => 1,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 3,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 3,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1')
                    )
                ),
                'metro-9' => array(
                    'columns_gutter' => $columns_gutter,
                    'image_size' => Spring_Plant()->options()->get_portfolio_image_size(),
                    'isotope' =>  array(
                        'itemSelector' => 'article',
                        'layoutMode'   => 'masonry',
                        'percentPosition' => true,
                        'masonry' => array(
                            'columnWidth' => '.gsf-column-base',
                        ),
                        'metro' => true
                    ),
                    'layout' => array(
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2,'lg' => 2,'md' => 1,'sm' => 1,'' => 1)), 'template' => 'grid', 'layout_ratio' => '2x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2,'lg' => 2,'md' => 1,'sm' => 1,'' => 1)), 'template' => 'grid', 'layout_ratio' => '2x1')
                    )
                ),
                'metro-10' => array(
                    'columns_gutter' => $columns_gutter,
                    'image_size' => Spring_Plant()->options()->get_portfolio_image_size(),
                    'isotope' =>  array(
                        'itemSelector' => 'article',
                        'layoutMode'   => 'masonry',
                        'percentPosition' => true,
                        'masonry' => array(
                            'columnWidth' => '.gsf-column-base',
                        ),
                        'metro' => true
                    ),
                    'layout' => array(
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2.4,'lg' => 1,'md' => 1,'sm' => 1,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 3,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 3,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2,'lg' => 2,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '2x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 2,'lg' => 2,'md' => 1,'sm' => 1,'' => 1)), 'template' => 'grid', 'layout_ratio' => '2x2'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1')
                    )
                ),
                'metro-11' => array(
                    'columns_gutter' => $columns_gutter,
                    'image_size' => Spring_Plant()->options()->get_portfolio_image_size(),
                    'isotope' =>  array(
                        'itemSelector' => 'article',
                        'layoutMode'   => 'masonry',
                        'percentPosition' => true,
                        'masonry' => array(
                            'columnWidth' => '.gsf-column-base',
                        ),
                        'metro' => true
                    ),
                    'layout' => array(
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1.5'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1.5'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1.5'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1.5'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 3,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1.5'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1.5'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 6,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1.5'),
                        array('columns' => Spring_Plant()->helper()->get_bootstrap_columns(array('xl' => 4,'lg' => 4,'md' => 2,'sm' => 2,'' => 1)), 'template' => 'grid', 'layout_ratio' => '1x1.5')
                    )
                ),
                'carousel-3d' => array(
                    'carousel' => array(
                        'items' => 2,
                        'center' => true,
                        'loop' => true,
                        'responsive' => array(
                            0 => array(
                                'items' =>  1,
                                'center' => false
                            ),
                            600 => array(
                                'items' =>  2
                            )
                        )
                    ),
                    'carousel_class' => 'carousel-3d',
                    'image_size' => Spring_Plant()->options()->get_portfolio_image_size(),
                    'layout' => array(
                        array('template' => 'grid')
                    )
                )
            );
            return $matrix;
        }

        public function get_layout_settings() {
            return array(
                'post_layout'            => Spring_Plant()->options()->get_portfolio_layout(),
                'portfolio_item_skin'    => Spring_Plant()->options()->get_portfolio_item_skin(),
                'portfolio_hover_color_scheme'    => Spring_Plant()->options()->get_portfolio_hover_color_scheme(),
                'post_columns'           => array(
                    'xl' => intval(Spring_Plant()->options()->get_portfolio_columns()),
                    'lg' => intval(Spring_Plant()->options()->get_portfolio_columns_md()),
                    'md' => intval(Spring_Plant()->options()->get_portfolio_columns_sm()),
                    'sm' => intval(Spring_Plant()->options()->get_portfolio_columns_xs()),
                    '' => intval(Spring_Plant()->options()->get_portfolio_columns_mb()),
                ),
                'post_columns_gutter'    => intval(Spring_Plant()->options()->get_portfolio_columns_gutter()),
                'portfolio_hover_effect' => Spring_Plant()->options()->get_portfolio_hover_effect(),
                'portfolio_light_box'    => Spring_Plant()->options()->get_portfolio_light_box(),
                'post_paging'            => Spring_Plant()->options()->get_portfolio_paging(),
                'post_animation'         => Spring_Plant()->options()->get_portfolio_animation(),
                'itemSelector'           => 'article',
                'category_filter_enable' => false,
                'category_filter_align' => '',
                'post_type' => $this->get_post_type(),
                'taxonomy' => $this->get_taxonomy_category()
            );
        }

        public function archive_markup($query_args = null, $settings = null) {
            if (isset($settings['tabs']) && isset($settings['tabs'][0]['query_args'])) {
                $query_args = $settings['tabs'][0]['query_args'];
            }

            if (!isset($query_args)) {
                $settings['isMainQuery'] = true;
            }

            $settings = wp_parse_args($settings,$this->get_layout_settings());
            Spring_Plant()->blog()->set_layout_settings($settings);

            $query_args = Spring_Plant()->query()->get_main_query_vars( $query_args );
            Spring_Plant()->query()->query_posts( $query_args );

            if (isset($settings['category_filter_enable']) && $settings['category_filter_enable'] === true) {
                add_action('spring_plant_before_archive_wrapper', array(Spring_Plant()->blog(), 'category_filter_markup'));
            }

            if (isset($settings['tabs'])) {
                add_action('spring_plant_before_archive_wrapper', array(Spring_Plant()->blog(), 'tabs_markup'));
            }

            Spring_Plant()->helper()->getTemplate('portfolio/archive');

            if (isset($settings['tabs'])) {
                remove_action('spring_plant_before_archive_wrapper', array(Spring_Plant()->blog(), 'tabs_markup'));
            }

            if (isset($settings['category_filter_enable']) && $settings['category_filter_enable'] === true) {
                remove_action('spring_plant_before_archive_wrapper', array(Spring_Plant()->blog(), 'category_filter_markup'));
            }

            Spring_Plant()->blog()->unset_layout_settings();

            Spring_Plant()->query()->reset_query();
        }

        public function the_permalink($post = 0) {
            $custom_link =  Spring_Plant()->metaBoxPortfolio()->get_single_portfolio_custom_link();
            if (!empty($custom_link)) {
                echo esc_url($custom_link);
            } else {
                the_permalink($post);
            }
        }

        public function portfolio_related() {
            Spring_Plant()->helper()->getTemplate('portfolio/single/portfolio-related');
        }

        public function portfolio_controls() {
            Spring_Plant()->helper()->getTemplate('portfolio/single/portfolio-controls');
        }

        public function portfolio_single_layout() {
            if (is_singular($this->get_post_type())) {
                $portfolio_single_layout = Spring_Plant()->options()->get_single_portfolio_layout();
                if ('layout-2' === $portfolio_single_layout) {
                    add_action('spring_plant_before_main_content', array(Spring_Plant()->templates(), 'portfolio_single_top'), 10);
                }
            }
        }

        function get_portfolio_term_ids( $portfolio_id) {
            $terms = get_the_terms( $portfolio_id, $this->get_taxonomy_category() );
            return ( empty( $terms ) || is_wp_error( $terms ) ) ? array() : wp_list_pluck( $terms, 'term_id' );
        }
    }
}