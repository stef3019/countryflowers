<?php
/**
 * The template for displaying content-single.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
?>
<div class="gf-single-wrap clearfix">
	<article id="post-<?php the_ID(); ?>" <?php post_class('post-single clearfix'); ?>>
		<ul class="gf-entry-meta-top gf-inline">
			<li class="gf-post-cat-meta">
                <?php the_category(', '); ?>
			</li>
			<li class="meta-date">
				<?php echo get_the_date(get_option('date_format')); ?>
			</li>
		</ul>
		<?php Spring_Plant()->helper()->getTemplate('single/post-title') ?>
		<?php Spring_Plant()->templates()->post_single_image(); ?>
		<div class="gf-post-content">
			<div class="gf-entry-content clearfix">
				<?php
				the_content();
				?>
			</div>
            <?php
            wp_link_pages(array(
                'before' => '<div class="page-links"><span class="page-links-title">' . esc_html__('Pages:','spring-plant') . '</span>',
                'after' => '</div>',
                'link_before' => '<span class="page-link">',
                'link_after' => '</span>',
            ));
            ?>
		</div>
	</article>
	<?php
	/**
	 * @hooked - post_single_tag - 5
	 * @hooked - post_single_share - 10
	 * @hooked - post_single_navigation - 15
	 * @hooked - post_single_author_info - 20
	 * @hooked - post_single_related - 25
	 * @hooked - post_single_comment - 30
	 *
	 **/
	do_action('spring_plant_after_single_post')
	?>
</div>
