<?php
/**
 * The template for displaying logo
 * @var $header_layout
 */
$logo = Spring_Plant()->options()->get_logo();
$logo = isset($logo['url']) ? $logo['url'] : '';


$logo_retina = Spring_Plant()->options()->get_logo_retina();
$logo_retina = isset($logo_retina['url']) ? $logo_retina['url'] : '';

$sticky_logo = Spring_Plant()->options()->get_sticky_logo();
$sticky_logo = isset($sticky_logo['url']) ? $sticky_logo['url'] : '';

$sticky_logo_retina = Spring_Plant()->options()->get_sticky_logo_retina();
$sticky_logo_retina = isset($sticky_logo_retina['url']) ? $sticky_logo_retina['url'] : '';

$logo_classes = array(
	'logo-header',
    'd-flex',
    'align-items-center'
);

$logo_title = esc_attr(get_bloginfo('name', 'display')) . '-' . get_bloginfo('description', 'display');
$logo_text = get_bloginfo('name', 'display');
$header_logo_sticky_layout = array('header-1', 'header-2','header-9','header-10');
if (in_array($header_layout, $header_logo_sticky_layout) && ($sticky_logo)) {
	$logo_classes[] = 'has-logo-sticky';
}

$logo_attributes = array();
if ($logo_retina && ($logo_retina != $logo)) {
	$logo_attributes[] = 'data-retina="' . esc_url($logo_retina) . '"';
}

$logo_sticky_attributes = array();
if ($sticky_logo_retina && ($sticky_logo_retina != $sticky_logo)) {
	$logo_sticky_attributes[] = 'data-retina="' . esc_url($sticky_logo_retina) . '"';
}

$logo_class = implode(' ', array_filter($logo_classes));
?>
<div class="<?php echo esc_attr($logo_class) ?>">
    <?php $h1_is_used = false; ?>
	<a class="main-logo gsf-link" href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr($logo_title) ?>">
		<?php if (!empty($logo)): ?>
			<img <?php echo implode(' ', $logo_attributes); ?> src="<?php echo esc_url($logo) ?>" alt="<?php echo esc_attr($logo_title) ?>">
		<?php elseif(is_front_page()): ?>
			<h1 class="logo-text"><?php echo esc_html($logo_text); ?></h1>
            <?php $h1_is_used = true; ?>
        <?php else: ?>
            <h2 class="logo-text"><?php echo esc_html($logo_text); ?></h2>
        <?php endif; ?>
	</a>
	<?php if (in_array($header_layout, $header_logo_sticky_layout) && ($sticky_logo)): ?>
		<a class="sticky-logo" href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr($logo_title) ?>">
			<img <?php echo implode(' ', $logo_sticky_attributes); ?> src="<?php echo esc_url($sticky_logo) ?>" alt="<?php echo esc_attr($logo_title) ?>">
		</a>
	<?php endif; ?>
    <?php if(!$h1_is_used && is_front_page()): ?>
        <div class="site-branding-text">
            <h1 class="site-title"><?php echo esc_html($logo_text); ?></h1>
        </div>
    <?php endif; ?>
</div>
