<?php
get_header();
$portfolio_filter = Spring_Plant()->options()->get_portfolio_cate_filter();
$query_args = $settings = null;
if('' !== $portfolio_filter) {
    $settings['category_filter_enable'] = true;
    $settings['category_filter_align'] = $portfolio_filter;
    if (is_tax(Spring_Plant()->portfolio()->get_taxonomy_category())) {
        global $wp_query;
        if (isset($wp_query->queried_object)) {
            $settings['current_cat'] = $wp_query->queried_object->term_id;
        }
    }
}
Spring_Plant()->portfolio()->archive_markup($query_args,$settings);
get_footer();