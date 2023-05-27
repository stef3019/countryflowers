<?php
/**
 * The template for displaying layout-4
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

$menu_left_class = array(
    'main-menu clearfix sub-menu-left',
    $menu_active_layout,
    $space_between_menu
);

$menu_right_class = array(
    'main-menu clearfix sub-menu-right',
    $menu_active_layout,
    $space_between_menu
);

$header_class = implode(' ', array_filter($header_classes));
$header_inner_class = implode(' ', array_filter($header_inner_classes));
?>
<div class="<?php echo esc_attr($header_class) ?>">
    <div class="container">
        <div class="<?php echo esc_attr($header_inner_class) ?>">
            <nav class="primary-menu d-flex align-items-center">
                <?php Spring_Plant()->helper()->getTemplate('header/header-customize', array('customize_location' => 'left', 'canvas_position' => 'left')); ?>
                <div class="primary-menu-inner d-flex align-items-center">
                    <div class="left-menu d-flex align-items-center">
                        <?php if (has_nav_menu('left-menu') || $page_menu) {
                            $arg_menu = array(
                                'menu_id' => 'left-menu',
                                'container' => '',
                                'theme_location' => 'left-menu',
                                'menu_class' => join(' ', $menu_left_class),
                                'main_menu' => true
                            );
                            if (!empty($page_menu)) {
                                $arg_menu['menu'] = $page_menu;
                            }
                            wp_nav_menu($arg_menu);
                        } ?>
                    </div>
                </div>
            </nav>
            <?php Spring_Plant()->helper()->getTemplate('header/desktop/logo',array('header_layout' => $header_layout)); ?>
            <nav class="primary-menu d-flex align-items-center">
                <div class="right-menu d-flex align-items-center">
                    <?php if (has_nav_menu('right-menu') || $page_menu) {
                        $arg_menu = array(
                            'menu_id' => 'right-menu',
                            'container' => '',
                            'theme_location' => 'right-menu',
                            'menu_class' => join(' ', $menu_right_class),
                            'main_menu' => true
                        );
                        if (!empty($page_menu)) {
                            $arg_menu['menu'] = $page_menu;
                        }
                        wp_nav_menu($arg_menu);
                    } ?>
                </div>
                <?php Spring_Plant()->helper()->getTemplate('header/header-customize', array('customize_location' => 'right', 'canvas_position' => 'right')); ?>
            </nav>
        </div>
    </div>
</div>


