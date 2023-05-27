<?php
/**
 * The template for displaying layout-1.php
 */
get_header();
while (have_posts()) : the_post();
	Spring_Plant()->helper()->getTemplate('single/layout-1/content');
endwhile;
get_footer();