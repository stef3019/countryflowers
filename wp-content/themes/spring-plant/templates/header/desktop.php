<?php
/**
 * The template for displaying desktop
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
$header_layout = Spring_Plant()->options()->get_header_layout();
$header_float_enable  = Spring_Plant()->options()->get_header_float_enable();
$header_border = Spring_Plant()->options()->get_header_border();
$header_content_full_width = Spring_Plant()->options()->get_header_content_full_width();
$header_sticky = Spring_Plant()->options()->get_header_sticky();
$skin = Spring_Plant()->options()->get_header_skin();
$navigation_skin = Spring_Plant()->options()->get_navigation_skin();
$page_menu = '';
if (is_singular()) {
	$page_menu = Spring_Plant()->metaBox()->get_page_menu();
}

$header_responsive_breakpoint = Spring_Plant()->options()->get_header_responsive_breakpoint();

$header_classes = array(
	'main-header',
	$header_layout
);
$skin_classes = Spring_Plant()->helper()->getSkinClass($skin);
$header_classes = array_merge($header_classes,$skin_classes);

if ($header_float_enable === 'on' && !in_array($header_layout,array('header-9','header-10'))) {
	$header_classes[] = 'header-float';
}

if (in_array($header_layout,array('header-9','header-10'))) {
	$header_classes[] = 'header-vertical';
}

/*if ($header_border == 'full') {
	$header_classes[] = 'gf-border-bottom';
}*/
$nav_spacing = Spring_Plant()->options()->get_navigation_spacing();
$menu_active_layout = Spring_Plant()->options()->get_menu_item_active_layout();
$space_between_menu = Spring_Plant()->options()->get_space_between_menu();
$header_attributes = array(
	'data-layout="'. esc_attr($header_layout) .'"',
	'data-responsive-breakpoint="'. esc_attr($header_responsive_breakpoint) .'"',
    'data-navigation="' . esc_attr($nav_spacing) . '"',
);
if (($header_sticky !== '') &&  !in_array($header_layout,array('header-9','header-10'))) {
	$sticky_skin = Spring_Plant()->options()->get_header_sticky_skin();
	$sticky_skin_classes = Spring_Plant()->helper()->getSkinClass($sticky_skin);
	$sticky_skin_class = implode(' ', $sticky_skin_classes);
	$header_attributes[] = 'data-sticky-skin="'. $sticky_skin_class .'"';
    $header_attributes[] = 'data-sticky-type="'. $header_sticky .'"';
}
$header_class = implode(' ',array_filter($header_classes));
?>
<header <?php echo implode(' ', $header_attributes) ?> class="<?php echo esc_attr($header_class); ?>">
	<?php if (!in_array($header_layout,array('header-9','header-10'))) {Spring_Plant()->helper()->getTemplate('header/desktop/top-bar');}  ?>
	<?php Spring_Plant()->helper()->getTemplate("header/desktop/{$header_layout}",array(
		'header_layout' => $header_layout,
		'header_float_enable' => $header_float_enable,
		'header_border' => $header_border,
		'header_content_full_width' => $header_content_full_width,
		'header_sticky' => $header_sticky,
		'navigation_skin' => $navigation_skin,
		'page_menu' => $page_menu,
        'menu_active_layout' => $menu_active_layout,
        'space_between_menu' => $space_between_menu
	)); ?>
</header>
