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

?>
<article <?php echo implode(' ', $post_attributes); ?> <?php post_class($post_class) ?>>
    <div <?php echo implode(' ', $post_inner_attributes); ?> class="<?php echo esc_attr($post_inner_class); ?>">
        <div class="entry-thumb-wrap">
            <?php
                Spring_Plant()->portfolio()->render_thumbnail_markup(array(
                    'image_size'         => $image_size,
                    'image_ratio'        => $image_ratio,
                    'placeholder_enable' => $placeholder_enable
                ));
            ?>
            <div class="portfolio-action-wrap block-center">
                <div class="block-center-inner">
                    <div class="portfolio-action mg-bottom-20">
                        <?php Spring_Plant()->helper()->getTemplate('portfolio/loop/post-zoom', array(
                            'portfolio_light_box' => $portfolio_light_box
                        )) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="portfolio-content">
            <?php Spring_Plant()->helper()->getTemplate('portfolio/loop/post-category'); ?>
            <?php Spring_Plant()->helper()->getTemplate('portfolio/loop/post-title') ?>
            <?php $excerpt = get_the_excerpt();
            if(!empty($excerpt)):?>
                <div class="portfolio-description">
                    <?php echo wp_kses_post($excerpt); ?>
                </div>
            <?php endif; ?>
            <div class="gf-portfolio-control">
                <div class="gf-portfolio-read-more">
                    <a href="<?php the_permalink(); ?>"
                       class="accent-color" title="<?php the_title() ?>"><?php esc_html_e('View More', 'spring-plant'); ?></a>
                </div>
            </div>
        </div>
    </div>
</article>
