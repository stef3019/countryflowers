<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('G5P_Widget_Wishlist')) {
    class G5P_Widget_Wishlist extends GSF_Widget
    {
        public function __construct()
        {
            $this->widget_cssclass = 'widget-wishlist';
            $this->widget_description = esc_html__("Display wishlist area", 'spring-framework');
            $this->widget_id = 'gsf-wishlist';
            $this->widget_name = esc_html__('G5Plus: Wishlist', 'spring-framework');
            $this->settings = array(
                'fields' => array(
                    array(
                        'id'      => 'title',
                        'type'    => 'text',
                        'default' => '',
                        'title'   => esc_html__('Title', 'spring-framework')
                    ),
                    array(
                        'id' => 'layout_style',
                        'type' => 'image_set',
                        'default' => 'style-01',
                        'options' => array(
                            'style-01'     => array(
                                'label' => esc_html__('Style 01', 'spring-framework'),
                                'img'   => G5P()->pluginUrl('assets/images/theme-options/wishlist-style-01.png')
                            ),
                            'style-02'     => array(
                                'label' => esc_html__('Style 02', 'spring-framework'),
                                'img'   => G5P()->pluginUrl('assets/images/theme-options/wishlist-style-02.png')
                            )
                        ),
                        'title' => esc_html__('Display Style', 'spring-framework')
                    )
                )
            );
            parent::__construct();
        }

        function widget($args, $instance)
        {
            extract($args, EXTR_SKIP);
            $title = (!empty($instance['title'])) ? $instance['title'] : '';
            $title = apply_filters('widget_title', $title, $instance, $this->id_base);
            $layout_style = (!empty($instance['layout_style'])) ? $instance['layout_style'] : 'style-01';
            echo wp_kses_post($args['before_widget']);
            if ($title) {
                echo wp_kses_post($args['before_title'] . $title . $args['after_title']);
            }
            echo '<div class="gsf-wishlist-content">';
            $wishlist_href = '#';
            if( defined( 'YITH_WCWL' ) && function_exists('yith_wcwl_object_id')) {
                $wishlist_page_id = yith_wcwl_object_id( get_option( 'yith_wcwl_wishlist_page_id' ) );
                if(!empty($wishlist_page_id)) {
                    $wishlist_href = get_the_permalink($wishlist_page_id);
                }
            }
            echo '<a href="' . esc_url($wishlist_href) . '" title="' . esc_attr__('Wishlist', 'spring-framework') . '">';
            echo '<i class="fa fa-heart-o"></i> ';
            if('style-01' === $layout_style) {
                $count = 0;
                if( defined( 'YITH_WCWL' ) && function_exists( 'yith_wcwl_count_all_products' ) ) {
                    $count = yith_wcwl_count_all_products();
                }
                echo '<span class="wishlist-count">' . esc_html($count) . '</span>';
            } else {
                echo '<span class="wishlist-title">' . esc_html__('Wishlist', 'spring-framework') . '</span>';
            }
            echo '</a></div>';
            echo wp_kses_post($args['after_widget']);
        }
    }
}