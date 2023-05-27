<?php
/**
 * The template for displaying canvas-menu
 */
$page_menu = '';
if (is_singular()) {
    $page_menu = Spring_Plant()->metaBox()->get_page_menu();
}
$skin_class = Spring_Plant()->helper()->getSkinClass('skin-light');
?>
<div id="popup-canvas-menu" class="modal fade bs-example-modal-lg <?php echo join(' ', $skin_class) ?>" tabindex="-1" role="dialog"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="gf-menu-icon gf-menu-canvas"><span></span></div>
            <div class="modal-body">
                <nav class="primary-menu">
                    <?php if (has_nav_menu('primary') || $page_menu): ?>
                        <?php
                        $arg_menu = array(
                            'menu_id' => 'main-menu',
                            'container' => '',
                            'theme_location' => 'primary',
                            'menu_class' => 'clearfix'
                        );
                        if (!empty($page_menu)) {
                            $arg_menu['menu'] = $page_menu;
                        }
                        wp_nav_menu($arg_menu);
                        ?>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </div>
</div>
