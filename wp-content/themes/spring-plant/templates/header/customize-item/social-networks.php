<?php
/**
 * The template for displaying social-networks
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 * @var $customize_location
 */
$social_networks =  Spring_Plant()->options()->getOptions("header_customize_{$customize_location}_social_networks");
Spring_Plant()->templates()->social_networks($social_networks,'classic');

