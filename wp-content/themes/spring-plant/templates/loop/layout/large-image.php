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
 * @var $placeholder_enable
 * @var $first_image_enable
 * @var $post_link
 */
?>
<article <?php post_class($post_class) ?>>
	<div class="<?php echo esc_attr($post_inner_class); ?>">
		<ul class="gf-post-meta gf-inline">
			<li class="meta-cate">
                <?php the_category(', '); ?>
            </li>
			<?php Spring_Plant()->helper()->getTemplate('loop/post-meta') ?>
		</ul>
		<?php Spring_Plant()->helper()->getTemplate('loop/post-title', array('post_link'=> $post_link)) ?>
		<?php Spring_Plant()->blog()->render_post_thumbnail_markup(array(
			'image_size'         => $image_size,
			'placeholder_enable' => $placeholder_enable,
            'first_image_enable' => $first_image_enable,
			'mode'               => 'full',
			'image_mode'         => 'image'
		)); ?>
		<div class="gf-post-content">
			<div class="gf-post-control">
				<div class="gf-post-read-more">
					<a href="<?php echo esc_url($post_link); ?>"
					   class="btn"><?php esc_html_e('Continue Reading', 'spring-plant'); ?></a>
				</div>
				<?php Spring_Plant()->helper()->getTemplate('single/post-meta')?>
			</div>
		</div>
	</div>
</article>