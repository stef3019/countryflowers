<?php
/**
 * The template for displaying content-large-image
 *
 * @var $image_size
 * @var $post_class
 * @var $post_inner_class
 * @var $placeholder_enable
 * @var $first_image_enable
 * @var $post_link
 */
$excerpt = get_the_excerpt();
$excerpt = Spring_Plant()->helper()->truncateText($excerpt, 150);
?>
<article <?php post_class($post_class) ?>>
	<div class="<?php echo esc_attr($post_inner_class); ?>">
		<div class="gf-post-image">
			<?php Spring_Plant()->blog()->render_post_thumbnail_markup(array(
				'image_size'         => $image_size,
				'placeholder_enable' => $placeholder_enable,
                'first_image_enable' => $first_image_enable,
				'mode'               => 'full',
				'image_mode'         => 'background'
			)); ?>
		</div>
		<div class="gf-post-right">
			<div class="gf-post-date">
				<a class="gsf-link" href="<?php echo esc_url($post_link); ?>" rel="bookmark" title="<?php the_title(); ?>">
					<span><?php echo get_the_date('d') ?></span>
					<span><?php echo get_the_date('F') ?></span>
				</a>
			</div>
			<div class="gf-post-content-wrap">
				<a class="gf-post-author" title="<?php the_author() ?>"
				   href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))) ?>"><?php the_author(); ?></a>
				<div class="gf-post-category">
                    <?php the_category(', '); ?>
                </div>
					<?php Spring_Plant()->helper()->getTemplate('loop/post-title', array('post_link'=> $post_link)) ?>
				<div class="gf-post-content">
						<div class="gf-post-excerpt">
							<?php echo esc_html($excerpt); ?>
						</div>
					<div class="gf-post-control">
						<div class="gf-post-read-more">
							<a href="<?php echo esc_url($post_link); ?>"
							   class="btn" title="<?php the_title() ?>"><?php esc_html_e('Read More', 'spring-plant'); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</article>