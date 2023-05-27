<?php
/**
 * The template for displaying layout-6
 *
 * @var $header_layout
 * @var $header_float_enable
 * @var $header_border
 * @var $header_content_full_width
 * @var $header_sticky
 * @var $navigation_skin
 * @var $page_menu
 * @var $menu_active_layout
 * @var $space_between_menu
 */

$header_classes = array(
    'header-wrap'
);

$header_inner_classes = array(
    'header-inner',
    'd-flex',
    'align-items-center',
    'x-nav-menu-container'
);

if ($header_border === 'container') {
    $header_inner_classes[] = 'gf-border-bottom';
    $header_inner_classes[] = 'border-color';
}

if ($header_border == 'full') {
    $header_classes[] = 'gf-border-bottom';
    $header_classes[] = 'border-color';
}

if ($header_sticky !== '') {
    $header_classes[] = 'header-sticky';
}

if ($header_content_full_width === 'on') {
    $header_classes[] = 'header-full-width';
}
$menu_class = array(
    'main-menu clearfix',
    $menu_active_layout,
    $space_between_menu
);

$header_class = implode(' ', array_filter($header_classes));
$header_inner_class = implode(' ', array_filter($header_inner_classes));
$header_customize_right = Spring_Plant()->options()->get_header_customize_right();
unset($header_customize_right['sort_order']);
?>
<div class="<?php echo esc_attr($header_class) ?>">
    <div class="container">
        <div class="<?php echo esc_attr($header_inner_class) ?>">
            <?php if (has_nav_menu('primary') || $page_menu): ?>
                <?php
                $arg_menu = array(
                    'menu_id'        => 'main-menu',
                    'container'      => '',
                    'theme_location' => 'primary',
                    'menu_class'     => join(' ', $menu_class),
                    'main_menu'      => true
                );
                if (!empty($page_menu)) {
                    $arg_menu['menu'] = $page_menu;
                }
                wp_nav_menu($arg_menu);
                ?>
            <?php endif; ?>
            <?php Spring_Plant()->helper()->getTemplate('header/desktop/logo',array('header_layout' => $header_layout)); ?>
            <?php if(empty($header_customize_right)): ?>
                <div class="header-customize-empty"></div>
            <?php else: ?>
                <?php Spring_Plant()->helper()->getTemplate('header/header-customize', array('customize_location' => 'right', 'canvas_position' => 'right')); ?>
            <?php endif; ?>
        </div>
    </div>
</div>


