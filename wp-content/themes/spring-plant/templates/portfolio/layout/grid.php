<?php
/**
 * @var $image_ratio
 * @var $image_size
 * @var $post_class
 * @var $post_inner_class
 * @var $placeholder_enable
 * @var $post_attributes
 * @var $post_inner_attributes
 * @var $portfolio_light_box
 * @var $portfolio_item_skin
 */

if('portfolio-item-skin-01' !== $portfolio_item_skin) {
    $placeholder_enable = true;
}
?>
<article <?php echo implode(' ', $post_attributes); ?> <?php post_class($post_class) ?>>
    <div <?php echo implode(' ', $post_inner_attributes); ?> class="<?php echo esc_attr($post_inner_class); ?>">
        <div class="entry-thumbnail-wrap">
            <?php
                Spring_Plant()->portfolio()->render_thumbnail_markup(array(
                    'image_size'         => $image_size,
                    'image_ratio'        => $image_ratio,
                    'placeholder_enable' => $placeholder_enable
                ));
            ?>
            <?php if('portfolio-item-skin-01' === $portfolio_item_skin): ?>
                <div class="portfolio-content block-center">
                    <div class="block-center-inner">
                        <div class="portfolio-action mg-bottom-20">
                            <?php Spring_Plant()->helper()->getTemplate('portfolio/loop/post-zoom', array(
                                'portfolio_light_box' => $portfolio_light_box
                            )) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php if('portfolio-item-skin-02' === $portfolio_item_skin): ?>
            <div class="portfolio-content block-center">
                <div class="block-center-inner">
                    <div class="portfolio-action mg-bottom-20">
                        <?php Spring_Plant()->helper()->getTemplate('portfolio/loop/post-zoom', array(
                            'portfolio_light_box' => $portfolio_light_box
                        )) ?>
                    </div>
                    <?php Spring_Plant()->helper()->getTemplate('portfolio/loop/post-title') ?>
                    <?php Spring_Plant()->helper()->getTemplate('portfolio/loop/post-category'); ?>
                </div>
            </div>
        <?php elseif('portfolio-item-skin-01' === $portfolio_item_skin): ?>
            <div class="portfolio-info">
                <?php Spring_Plant()->helper()->getTemplate('portfolio/loop/post-title') ?>
                <?php Spring_Plant()->helper()->getTemplate('portfolio/loop/post-category'); ?>
            </div>
        <?php else: ?>
            <div class="portfolio-content">
                <div class="portfolio-content-inner">
                    <div class="portfolio-action">
                        <?php Spring_Plant()->helper()->getTemplate('portfolio/loop/post-zoom', array(
                            'portfolio_light_box' => $portfolio_light_box
                        )) ?>
                    </div>
                    <div class="portfolio-info">
                        <?php Spring_Plant()->helper()->getTemplate('portfolio/loop/post-title') ?>
                        <?php Spring_Plant()->helper()->getTemplate('portfolio/loop/post-category'); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</article>
