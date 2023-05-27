<?php
/**
 * The template for displaying layout-1.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
Spring_Plant()->options()->setOptions('page_title_enable', '');
add_action('spring_plant_before_main_content', array(Spring_Plant()->templates(), 'post_single_full_image'), 10);
get_header();
while (have_posts()) : the_post();
	Spring_Plant()->helper()->getTemplate('single/layout-5/content');
endwhile;
get_footer();