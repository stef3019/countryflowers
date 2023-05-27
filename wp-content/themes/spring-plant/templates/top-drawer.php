<?php
/**
 * The template for displaying top-drawer
 */
$top_drawer_mode = Spring_Plant()->options()->get_top_drawer_mode();
if ($top_drawer_mode === 'hide') return;
$content_block = Spring_Plant()->options()->get_top_drawer_content_block();
if (empty($content_block)) return;
$content_full_width = Spring_Plant()->options()->get_top_drawer_content_full_width();
$mobile_top_drawer_enable = Spring_Plant()->options()->get_mobile_top_drawer_enable();
$skin = Spring_Plant()->options()->get_top_drawer_skin();
$border = Spring_Plant()->options()->get_top_drawer_border();

$wrapper_classes = array(
	'top-drawer-wrap',
	"top-drawer-mode-{$top_drawer_mode}"
);
$inner_classes = array(
	'top-drawer-inner'
);

$skin_classes = Spring_Plant()->helper()->getSkinClass($skin);
$wrapper_classes = array_merge($wrapper_classes,$skin_classes);


if ($border === 'full') {
	$wrapper_classes[] = 'gf-border-bottom';
}
if ($border === 'container') {
	$inner_classes[] = 'gf-border-bottom';
	$inner_classes[] = 'border-color';
}

if ($mobile_top_drawer_enable !== 'on') {
	$wrapper_classes[] = 'gf-hidden-mobile';
}

$wrapper_class = implode(' ', array_filter($wrapper_classes));
$inner_class = implode(' ', array_filter($inner_classes));

?>
<div class="<?php echo esc_attr($wrapper_class) ?>">
	<?php if ($content_full_width !== 'on'): ?>
		<div class="container">
	<?php endif; ?>
		<div class="<?php echo esc_attr($inner_class) ?>">
            <div class="top-drawer-content">
                <?php echo Spring_Plant()->helper()->content_block($content_block); ?>
            </div>
		</div>
	<?php if ($content_full_width !== 'on'): ?>
		</div>
	<?php endif; ?>
	<?php if ($top_drawer_mode === 'toggle'): ?>
		<div class="top-drawer-toggle"><a href="#"><i class="fa fa-plus"></i></a></div>
	<?php endif; ?>
</div>
