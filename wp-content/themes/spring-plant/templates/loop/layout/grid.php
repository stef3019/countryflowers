<?php
/**
 * The template for displaying content-grid.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 * @var $image_size
 * @var $post_class
 * @var $post_inner_class
 * @var $placeholder_enable
 * @var $first_image_enable
 * @var $image_ratio
 * @var $post_item_skin
 * @var $post_layout
 * @var $post_link
 */
global $hasThumb;
$excerpt = get_the_excerpt();
$excerpt = Spring_Plant()->helper()->truncateText($excerpt, 150);
?>
<article <?php post_class($post_class) ?>>
	<div class="<?php echo esc_attr($post_inner_class); ?>">
		<?php if ($post_item_skin !== "post-skin-01"): ?>
			<?php Spring_Plant()->blog()->render_post_thumbnail_markup(array(
				'image_size'         => $image_size,
				'placeholder_enable' => $placeholder_enable,
				'first_image_enable' => $first_image_enable,
				'mode'               => 'full',
				'image_ratio'        => $image_ratio,
				'post_layout' => $post_layout
			)); ?>
		<?php endif; ?>
		<div class="gf-post-content">
			<ul class="gf-post-meta">
				<?php if ($post_item_skin == "post-skin-06"): ?>
					<li class="gf-post-category">
                        <?php the_category(', '); ?>
					</li>
				<?php endif; ?>
				<li class="meta-date"><a class="gsf-link" href="<?php echo esc_url($post_link); ?>" rel="bookmark"
					   title="<?php the_title(); ?>"><?php echo get_the_date('F j, Y') ?></a></li>
				<?php if (in_array($post_item_skin,array('post-skin-04','post-skin-07'))): ?>
					<li class="meta-author"><a class="gf-post-author" title="<?php the_author() ?>"
						   href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))) ?>"><?php the_author(); ?></a></li>
				<?php endif; ?>
			</ul>
			<?php Spring_Plant()->helper()->getTemplate('loop/post-title', array('post_link' => $post_link)) ?>
			<?php if (!empty($excerpt) && ($post_item_skin !== 'post-skin-05') && ($post_item_skin !== 'post-skin-04')): ?>
				<div class="gf-post-excerpt">
					<?php echo esc_html($excerpt); ?>
				</div>
			<?php endif; ?>
			<?php if (in_array($post_item_skin,array('post-skin-01','post-skin-02','post-skin-03'))): ?>
				<div class="gf-post-author-meta">
					<div class="gf-post-author-img">
						<a href="#"><?php echo get_avatar(get_the_author_meta()); ?></a>
					</div>
					<a class="gf-post-author" title="<?php the_author() ?>"
					   href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))) ?>"> <?php echo esc_html(sprintf(__('By %s','spring-plant'),get_the_author()))?></a>
				</div>
			<?php endif; ?>
			<?php if (in_array($post_item_skin,array('post-skin-05','post-skin-06'))): ?>
				<div class="gf-post-author-meta">
					<a class="gf-post-author" title="<?php the_author() ?>"
					   href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))) ?>"><?php echo esc_html(sprintf(__('By %s','spring-plant'),get_the_author()))?></a>
				</div>
			<?php endif; ?>
			<?php if (in_array($post_item_skin, array('post-skin-04','post-skin-07'))): ?>
				<div class="gsf-post-read-more">
					<a href="<?php echo esc_url($post_link); ?>"
					   class="btn btn-black btn-link-xs"><?php esc_html_e('Read more', 'spring-plant'); ?><i
								class="flaticon-right-arrow-1"></i></a>
				</div>
			<?php endif; ?>
		</div>
	<?php if ($post_item_skin === "post-skin-01"): ?>
		<?php Spring_Plant()->blog()->render_post_thumbnail_markup(array(
			'image_size'         => $image_size,
			'placeholder_enable' => $placeholder_enable,
            'first_image_enable' => $first_image_enable,
			'mode'               => 'full',
			'image_ratio'        => $image_ratio,
			'post_layout' => $post_layout
		)); ?>
	<?php endif; ?>
	</div>
</article>