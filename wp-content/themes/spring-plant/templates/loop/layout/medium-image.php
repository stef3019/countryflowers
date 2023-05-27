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
 * @var $post_layout
 * @var $post_link
 */
global $hasThumb;
$excerpt = get_the_excerpt();
$excerpt = Spring_Plant()->helper()->truncateText($excerpt,200);
?>
<article <?php post_class($post_class) ?>>
	<div class="<?php echo esc_attr($post_inner_class); ?>">
            <?php Spring_Plant()->blog()->render_post_thumbnail_markup(array(
                'image_size' => $image_size,
                'placeholder_enable' => $placeholder_enable,
                'first_image_enable' => $first_image_enable,
                'mode' => 'full',
				'post_layout' => $post_layout
            )); ?>
		<div class="gf-post-content">
			<ul class="gf-post-meta gf-inline">
				<li class="meta-cate">
                    <?php the_category(', '); ?>
                </li>
				<?php Spring_Plant()->helper()->getTemplate('loop/post-meta') ?>
			</ul>
			<?php Spring_Plant()->helper()->getTemplate('loop/post-title', array('post_link'=> $post_link)) ?>
                    <div class="gf-post-excerpt">
                        <?php echo esc_html($excerpt); ?>
                    </div>
		</div>
	</div>
</article>
