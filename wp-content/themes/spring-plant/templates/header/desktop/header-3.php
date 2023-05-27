<?php
/**
 * The template for displaying layout-3
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
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
    'main-menu clearfix sub-menu-left',
    $menu_active_layout,
    $space_between_menu
);

$header_class = implode(' ', array_filter($header_classes));
$header_inner_class = implode(' ', array_filter($header_inner_classes));
?>
<div class="<?php echo esc_attr($header_class) ?>">
    <div class="container">
        <div class="<?php echo esc_attr($header_inner_class) ?>">
            <?php Spring_Plant()->helper()->getTemplate('header/desktop/logo',array('header_layout' => $header_layout)); ?>
            <nav  class="primary-menu d-flex align-items-center">
                <div class="primary-menu-inner d-flex align-items-center">
                    <?php if (has_nav_menu('primary') || $page_menu) {
                        $arg_menu = array(
                            'menu_id' => 'main-menu',
                            'container' => '',
                            'theme_location' => 'primary',
                            'menu_class' => join(' ', $menu_class),
                            'main_menu' => true
                        );
                        if (!empty($page_menu)) {
                            $arg_menu['menu'] = $page_menu;
                        }
                        wp_nav_menu($arg_menu);
                        Spring_Plant()->helper()->getTemplate('header/header-customize', array('customize_location' => 'nav', 'canvas_position' => 'right'));
                    } ?>
                </div>
            </nav>
        </div>
    </div>
</div>