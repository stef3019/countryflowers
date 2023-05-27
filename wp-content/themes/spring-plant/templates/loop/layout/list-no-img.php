<?php
/**
 * The template for displaying content-large-image
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 * @var $image_size
 * @var $post_class
 * @var $post_inner_class
 * @var $post_link
 */
?>
<article <?php post_class($post_class) ?>>
	<div class="<?php echo esc_attr($post_inner_class); ?>">
			<div class="gf-post-date">
				<a class="gsf-link" href="<?php echo esc_url($post_link); ?>" rel="bookmark" title="<?php the_title(); ?>">
					<span><?php echo get_the_date('d') ?></span>
					<span><?php echo get_the_date('F') ?></span>
				</a>
			</div>
			<div class="gf-post-content-wrap">
					<?php Spring_Plant()->helper()->getTemplate('loop/post-title', array('post_link' => $post_link)) ?>
				<a class="gf-post-author" title="<?php the_author() ?>"
				   href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))) ?>"><?php the_author(); ?></a>
				<div class="gf-post-category">
                    <?php the_category(', '); ?>
                </div>
			</div>
		</div>
</article>