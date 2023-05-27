<?php
Spring_Plant()->options()->setOptions('sidebar_layout','none');
Spring_Plant()->options()->setOptions('content_full_width','on');
$content_block = Spring_Plant()->options()->get_404_content_block();
if (!empty($content_block)) {
    Spring_Plant()->options()->setOptions('content_padding',array('left' => '', 'right' => '','top' => '', 'bottom' => ''));
}
get_header();
?>
<?php if (!empty($content_block)): ?>
    <?php echo Spring_Plant()->helper()->content_block($content_block); ?>
<?php else: ?>
    <?php $content = Spring_Plant()->options()->get_404_content(); ?>
    <div class="container">
        <div class="gf-404-wrap">
            <?php if (!empty($content)): ?>
                <?php Spring_Plant()->helper()->shortCodeContent($content); ?>
            <?php else: ?>
                <h2><?php esc_html_e('404','spring-plant'); ?></h2>
                <h4><?php esc_html_e('Oops! Page not found', 'spring-plant') ?></h4>
                <p><?php esc_html_e('Sorry, but the page you are looking for is not found. Please, make sure you have typed the current URL.', 'spring-plant') ?></p>
                <a href="<?php echo esc_url( home_url('/') ); ?>" class="btn btn-accent"><?php esc_html_e('Go to home page', 'spring-plant'); ?></a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<?php
get_footer();