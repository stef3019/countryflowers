<?php
/**
 * The template for displaying post-image.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
$single_post_layout = Spring_Plant()->options()->get_single_post_layout();
$image_size = 'blog-large';
?>
	<div class="gf-single-post-image clearfix">
		<?php Spring_Plant()->breadcrumbs()->get_breadcrumbs(); ?>
		<?php
		Spring_Plant()->blog()->render_post_thumbnail_markup(array(
			'image_size'         => $image_size,
			'placeholder_enable' => true,
			'mode'               => 'full',
			'display_permalink'  => false,
			'image_mode'         => 'background',
		));
		?>
		<div class="gf-entry-meta-top">
			<ul class="gf-entry-meta gf-inline">
				<li class="meta-cate">
                    <?php the_category(', '); ?>
				</li>
				<li class="meta-date">
					<?php echo get_the_date(get_option('date_format')); ?>
				</li>
			</ul>
			<?php Spring_Plant()->helper()->getTemplate('single/post-title') ?>
		</div>
	</div>
<?php


