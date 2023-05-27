<?php
/**
 * The template for displaying theme-functions.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
if (!function_exists('spring_plant_comments_callback')) {
	function spring_plant_comments_callback($comment, $args, $depth) {
		Spring_Plant()->helper()->getTemplate('comments',array(
			'comment' => $comment,
			'args' => $args,
			'depth' => $depth
		));
	}
}
