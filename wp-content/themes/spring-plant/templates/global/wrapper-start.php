<?php
/**
 * The template for displaying wrapper-start
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */

$content_full_width = Spring_Plant()->options()->get_content_full_width();
$sidebar_layout = Spring_Plant()->options()->get_sidebar_layout();
$sidebar_width = Spring_Plant()->options()->get_sidebar_width();
$wrapper_classes = array('col-12');

if ($content_full_width === 'on') {
	$wrapper_classes[] = 'gf-content-full-width';
}

$sidebar_col = 0;
$sidebar = Spring_Plant()->options()->get_sidebar();
if ($sidebar_layout !== 'none' && is_active_sidebar($sidebar)) {
	$sidebar_col = ($sidebar_width === 'large') ? 4 : 3;
	$wrapper_classes[] = "gsf-sidebar-" . $sidebar_layout;
}

$inner_class = array('primary-content', 'col-lg-' . (12 - $sidebar_col));

$wrapper_class = implode(' ', array_filter($wrapper_classes));
/**
 * @hooked - Spring_Plant()->templates()->page_title() - 5
 **/
do_action('spring_plant_before_main_content');
?>
<!-- Primary Content Wrapper -->
<div id="primary-content" class="<?php echo esc_attr($wrapper_class); ?>">
	<!-- Primary Content Container -->
	<?php if ($content_full_width !== 'on'): ?>
	<div class="container clearfix">
	<?php endif; ?>
		<?php do_action('spring_plant_main_content_top') ?>
		<!-- Primary Content Row -->
		<div class="row clearfix">
			<!-- Primary Content Inner -->
            <div class="<?php echo join(' ', $inner_class); ?>">
            <?php
            /**
             * @hooked - Spring_Plant()->templates()->above_content()
             **/
            do_action('spring_plant_above_content'); ?>