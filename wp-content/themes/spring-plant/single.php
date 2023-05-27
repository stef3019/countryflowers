<?php
/**
 * The template for displaying single
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
$single_post_layout = Spring_Plant()->options()->get_single_post_layout();
Spring_Plant()->helper()->getTemplate("single/{$single_post_layout}/layout");


