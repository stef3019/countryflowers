<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 09/05/2018
 * Time: 3:48 CH
 * @var $query_args
 * @var $settings
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$gsf_query = Spring_Plant()->query()->get_query();


$settings = wp_parse_args($settings,Spring_Plant()->woocommerce()->get_layout_settings());
Spring_Plant()->blog()->set_layout_settings($settings);

$woocommerce_customize_filter = Spring_Plant()->options()->get_woocommerce_customize_filter();
if('show-bellow' !== $woocommerce_customize_filter) {
    Spring_Plant()->helper()->getTemplate('woocommerce/loop/canvas-woocommerce-filter');
}
printf( '<div id="ajax-page-title">%s</div>', wp_get_document_title() );

add_action('spring_plant_before_archive_wrapper',array(Spring_Plant()->templates(),'shop_catalog_filter'),5);
add_action('spring_plant_before_archive_wrapper', array(Spring_Plant()->blog(), 'category_filter_markup'));

if (Spring_Plant()->query()->have_posts()) {
    if (isset($settings['isMainQuery']) && ($settings['isMainQuery'] == true)) {
        /**
         * woocommerce_before_shop_loop hook.
         *
         * @hooked wc_print_notices - 10
         */
        do_action( 'woocommerce_before_shop_loop' );
    }
    woocommerce_product_loop_start();
    $post_settings = &Spring_Plant()->blog()->get_layout_settings();
    $post_layout = isset( $post_settings['post_layout'] ) ? $post_settings['post_layout'] : 'grid';
    $item_skin = isset( $post_settings['product_item_skin'] ) ? $post_settings['product_item_skin'] : 'product-skin-01';
    if(!in_array($post_layout, array('grid', 'list'))) {
        $item_skin = '';
    }
    $layout_matrix = Spring_Plant()->blog()->get_layout_matrix( $post_layout );
    $post_paging = isset( $post_settings['post_paging'] ) ? $post_settings['post_paging'] : 'pagination';
    $post_animation = isset( $post_settings['post_animation'] ) ? $post_settings['post_animation'] : '';
    $placeholder_enable = isset( $layout_matrix['placeholder_enable'] ) ? $layout_matrix['placeholder_enable'] : false;
    $paged = Spring_Plant()->query()->query_var_paged();
    $image_size = isset($post_settings['image_size']) ? $post_settings['image_size'] : isset($layout_matrix['image_size']) ? $layout_matrix['image_size'] :  'shop_catalog';

    $image_size_base = $image_size;
    $image_ratio = '';
    if (in_array($post_layout, array('grid','metro-01','metro-02','metro-03','metro-04','metro-05','metro-06')) && ($image_size === 'full')) {
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
            } elseif (preg_match('/x/',$image_ratio_custom)) {
                $image_ratio = $image_ratio_custom;
            }
        }

        if ($image_ratio === 'custom') {
            $image_ratio = '1x1';
        }
    }

    $image_ratio_base = $image_ratio;

    if ( isset( $layout_matrix['layout'] ) ) {
        $layout_settings = $layout_matrix['layout'];
        $index = intval( $gsf_query->get( 'index', 0 ) );

        $post_classes = array(
            'clearfix',
            'product-item-wrap',
            $item_skin
        );

        $post_inner_classes = array(
            'product-item-inner',
            'clearfix',
            Spring_Plant()->helper()->getCSSAnimation( $post_animation )
        );
        while ( Spring_Plant()->query()->have_posts() ) : Spring_Plant()->query()->the_post();
            $index = $index % sizeof( $layout_settings );
            $current_layout = $layout_settings[$index];
            $isFirst = isset( $current_layout['isFirst'] ) ? $current_layout['isFirst'] : false;
            if ( $isFirst && ( $paged > 1 ) && in_array( $post_paging, array( 'load-more', 'infinite-scroll' ) ) ) {
                if ( isset( $layout_settings[$index + 1] ) ) {
                    $current_layout = $layout_settings[$index + 1];
                } else {
                    continue;
                }
            }
            $post_inner_attributes = array();

            if (isset($current_layout['layout_ratio'])) {
                $layout_ratio = !empty($current_layout['layout_ratio']) ? $current_layout['layout_ratio'] : '1x1';
                if ($image_size_base !== 'full') {
                    $image_size = Spring_Plant()->helper()->get_metro_image_size($image_size_base,$layout_ratio,$layout_matrix['columns_gutter']);
                } else {
                    $image_ratio =  Spring_Plant()->helper()->get_metro_image_ratio($image_ratio_base,$layout_ratio);
                }
                $post_inner_attributes[] = 'data-ratio="'. $layout_ratio .'"';
            }

            $post_columns = $current_layout['columns'];
            $template = $current_layout['template'];
            $classes = array(
                "product-{$template}",
                $post_columns
            );
            $classes = wp_parse_args( $classes, $post_classes );
            $post_class = implode( ' ', array_filter( $classes ) );
            $post_inner_class = implode( ' ', array_filter( $post_inner_classes ) );

            wc_get_template( "{$template}.php", array(
                'post_layout' => $post_layout,
                'image_size' => $image_size,
                'image_ratio' => $image_ratio,
                'post_class' => $post_class,
                'post_inner_class' => $post_inner_class,
                'placeholder_enable' => $placeholder_enable,
                'post_inner_attributes' => $post_inner_attributes,
                'product_item_skin' => $item_skin
            ));

            if ( $isFirst ) {
                unset( $layout_settings[$index] );
                $layout_settings = array_values( $layout_settings );
            }

            if ( $isFirst && $paged === 1 ) {
                $index = 0;
            } else {
                $index++;
            }
        endwhile;
    }
    woocommerce_product_loop_end();
} else {
    /**
     * woocommerce_no_products_found hook.
     *
     * @hooked wc_no_products_found - 10
     */
    do_action( 'woocommerce_no_products_found' );
}
if (isset($settings['category_filter_enable']) && $settings['category_filter_enable'] === true) {
    remove_action('g5plus_before_archive_wrapper', array(Spring_Plant()->blog(), 'category_filter_markup'));
}

if (isset($settings['isMainQuery']) && ($settings['isMainQuery'] == true)) {
    remove_action('g5plus_before_archive_wrapper',array(Spring_Plant()->templates(),'shop_catalog_filter'),5);
}

Spring_Plant()->blog()->unset_layout_settings();
