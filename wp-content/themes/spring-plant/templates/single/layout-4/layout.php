<?php
/**
 * The template for displaying layout-1.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
add_action('spring_plant_main_content_top',array(Spring_Plant()->templates(),'post_single_image'));
get_header();
while (have_posts()) : the_post();
	Spring_Plant()->helper()->getTemplate('single/layout-4/content');
endwhile;
get_footer();