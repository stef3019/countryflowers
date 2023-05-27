<?php
/**
 * The template for displaying category.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
get_header();
$blog_cate_filter = Spring_Plant()->options()->get_blog_cate_filter();
$query_args = $settings = null;
$current_cat = get_category( get_query_var( 'cat' ) );
if('' !== $blog_cate_filter) {
    $settings['category_filter_enable'] = true;
    $settings['category_filter_align'] = $blog_cate_filter;
    $settings['current_cat'] = $current_cat->term_id;
}
Spring_Plant()->blog()->archive_markup($query_args,$settings);
get_footer();