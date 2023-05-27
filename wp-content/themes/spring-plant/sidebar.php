<?php
/**
 * The template for displaying sidebar
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
$sidebar_layout = Spring_Plant()->options()->get_sidebar_layout();
$sidebar = Spring_Plant()->options()->get_sidebar();
if ($sidebar_layout === 'none' || !is_active_sidebar($sidebar)) return;
$sidebar_width = Spring_Plant()->options()->get_sidebar_width();
$sidebar_sticky_enable = Spring_Plant()->options()->get_sidebar_sticky_enable();
$mobile_sidebar_enable = Spring_Plant()->options()->get_mobile_sidebar_enable();
$mobile_sidebar_canvas = Spring_Plant()->options()->get_mobile_sidebar_canvas();

$wrapper_classes = array(
	'primary-sidebar',
	'sidebar'
);

$inner_classes = array(
	'primary-sidebar-inner'
);


$sidebar_col = ($sidebar_width == 'large') ? 4 : 3;
$wrapper_classes[] = "col-lg-{$sidebar_col}";
if ($mobile_sidebar_enable !== 'on') {
	$wrapper_classes[] = 'hidden-sm';
	$wrapper_classes[] = 'hidden-xs';
} elseif ($mobile_sidebar_canvas === 'on') {
	$wrapper_classes[] = 'gf-sidebar-canvas';
}
if ($sidebar_sticky_enable === 'on') {
	$wrapper_classes[] = 'gf-sticky';
}


$wrapper_class = implode(' ', array_filter($wrapper_classes));
$inner_class = implode(' ', array_filter($inner_classes));
?>
<div class="<?php echo esc_attr($wrapper_class); ?>">
	<?php if ($mobile_sidebar_canvas === 'on'): ?>
		<a href="javascript:;" title="<?php esc_attr_e('Click to show sidebar', 'spring-plant') ?>" class="gf-sidebar-toggle"><i class="fa fa-sliders"></i></a>
	<?php endif; ?>
	<div class="<?php echo esc_attr($inner_class); ?>">
		<?php dynamic_sidebar($sidebar); ?>
	</div>
</div>
