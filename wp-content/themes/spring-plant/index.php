<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
get_header();
$blog_cate_filter = Spring_Plant()->options()->get_blog_cate_filter();
$query_args = $settings = null;
if('' !== $blog_cate_filter) {
    $settings['category_filter_enable'] = true;
    $settings['category_filter_align'] = $blog_cate_filter;
}
Spring_Plant()->blog()->archive_markup($query_args,$settings);
get_footer();