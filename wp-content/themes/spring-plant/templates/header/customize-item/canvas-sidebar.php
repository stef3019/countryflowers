<?php
/**
 * The template for displaying canvas-sidebar
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 * @var $canvas_position
 */
if (!isset($canvas_position)) {
	$canvas_position = 'left';
}
add_action('wp_footer',array(Spring_Plant()->templates(),'canvas_sidebar'),10);
//add_action('wp_footer',array(Spring_Plant()->templates(),'canvas_overlay'),15);
?>
<div data-off-canvas="true" data-off-canvas-target="#canvas-sidebar-wrapper" data-off-canvas-position="<?php echo esc_attr($canvas_position); ?>" class="gf-toggle-icon"><span></span></div>
