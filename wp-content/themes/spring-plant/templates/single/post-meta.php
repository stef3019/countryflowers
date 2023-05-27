<?php
/**
 * The template for displaying post-meta.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
?>
<ul class="gf-post-meta-group">
    <?php if(function_exists('G5P')):
        $social_share = G5P()->options()->get_social_share();
        unset($social_share['sort_order']);
        if (count($social_share) > 0):
            ?>
            <li><?php do_action('spring_plant_post_share') ?></li>
        <?php endif; ?>
    <?php endif; ?>
	<li class="meta-author"><a class="gf-post-author" title="<?php the_author() ?>"
							   href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))) ?>"><?php echo esc_html(sprintf(__('By %s','spring-plant'),get_the_author()))?></a>
	</li>
	<?php if (comments_open()) : ?>
        <li class="gf-post-comment">
            <?php comments_popup_link(esc_html__('No Comments','spring-plant'),esc_html__('One Comment','spring-plant'),esc_html__('Comments (%)','spring-plant'),'','') ?>
        </li>
    <?php endif; ?>
</ul>
