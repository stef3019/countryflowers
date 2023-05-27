<?php
/**
 * The template for displaying sidebar
 * @var $customize_location
 */
$sidebar = Spring_Plant()->options()->getOptions("header_customize_{$customize_location}_sidebar");
?>
<?php if (is_active_sidebar($sidebar)): ?>
	<?php dynamic_sidebar($sidebar) ?>
<?php endif; ?>
