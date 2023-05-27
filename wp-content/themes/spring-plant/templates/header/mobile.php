<?php
/**
 * The template for displaying mobile
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
$mobile_header_layout = Spring_Plant()->options()->get_mobile_header_layout();
$mobile_header_sticky = Spring_Plant()->options()->get_mobile_header_sticky();
$mobile_header_border = Spring_Plant()->options()->get_mobile_header_border();
$skin = Spring_Plant()->options()->get_mobile_header_skin();

$mobile_header_classes = array(
	'mobile-header',
	$mobile_header_layout
);
$header_attributes = array();

$skin_classes = Spring_Plant()->helper()->getSkinClass($skin);
$mobile_header_classes = array_merge($mobile_header_classes,$skin_classes);
if (!empty($skin)) {
	$header_attributes[] = 'data-sticky-skin="gf-skin '. $skin .'"';
}

if('' !== $mobile_header_sticky) {
    $header_attributes[] = 'data-sticky-type="'. $mobile_header_sticky .'"';
}
$mobile_header_class = implode(' ',array_filter($mobile_header_classes));
?>
<header <?php echo implode(' ',$header_attributes)?> class="<?php echo esc_attr($mobile_header_class) ?>">
	<?php Spring_Plant()->helper()->getTemplate('header/mobile/top-bar'); ?>
	<?php Spring_Plant()->helper()->getTemplate("header/mobile/{$mobile_header_layout}",array(
		'header_layout' => $mobile_header_layout,
		'header_border' => $mobile_header_border,
		'header_sticky' => $mobile_header_sticky
	)); ?>
	<?php Spring_Plant()->helper()->getTemplate('header/mobile/search'); ?>
</header>
