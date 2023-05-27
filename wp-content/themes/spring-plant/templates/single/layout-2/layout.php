<?php
/**
 * The template for displaying layout-1.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
*/
get_header();
while (have_posts()) : the_post();
    Spring_Plant()->helper()->getTemplate('single/layout-2/content');
endwhile;
get_footer();