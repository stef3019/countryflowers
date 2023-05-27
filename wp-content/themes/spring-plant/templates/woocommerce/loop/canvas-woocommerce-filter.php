<?php
/**
 * The template for displaying canvas-sidebar
 */
$skin = Spring_Plant()->options()->get_canvas_sidebar_skin();
$wrapper_classes = array(
	'canvas-sidebar-wrapper'
);

$inner_classes = array(
	'canvas-sidebar-inner',
	'sidebar'
);

$skin_classes = Spring_Plant()->helper()->getSkinClass($skin);
$wrapper_classes = array_merge($wrapper_classes,$skin_classes);

$wrapper_class = implode(' ',array_filter($wrapper_classes));
$inner_class = implode(' ',array_filter($inner_classes));
?>
<div id="canvas-filter-wrapper" class="<?php echo esc_attr($wrapper_class); ?>">
    <a href="javascript:;" class="gsf-link close-canvas" title="<?php esc_attr_e('Close', 'spring-plant'); ?>"><i class="flaticon-cross"></i></a>
	<div class="<?php echo esc_attr($inner_class)?>">
		<?php if (is_active_sidebar('woocommerce-filter')): ?>
			<?php dynamic_sidebar('woocommerce-filter'); ?>
		<?php endif; ?>
	</div>
</div>
