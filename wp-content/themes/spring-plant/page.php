<?php
/**
 * The template for displaying page
 *
 */
get_header();
	while (have_posts()) : the_post();
		Spring_Plant()->helper()->getTemplate('content-page');
	endwhile;
get_footer();