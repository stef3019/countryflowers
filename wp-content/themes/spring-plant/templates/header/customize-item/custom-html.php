<?php
/**
 * The template for displaying custom-html
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 * @var $customize_location
 */
$custom_html = Spring_Plant()->options()->getOptions("header_customize_{$customize_location}_custom_html");
echo wp_kses_post($custom_html);