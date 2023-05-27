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
$excerpt = get_the_excerpt();
$excerpt = Spring_Plant()->helper()->truncateText($excerpt, 100);
?>
<article <?php post_class($post_class) ?>>
	<div class="<?php echo esc_attr($post_inner_class); ?>">
		
		<div class="gf-post-image">
			<?php Spring_Plant()->blog()->render_post_thumbnail_markup(array(
				'image_size' => $image_size,
				'placeholder_enable' => $placeholder_enable,
                'first_image_enable' => $first_image_enable,
				'mode' => 'full',
				'image_mode'         => 'background',
				'post_layout' => $post_layout
			)); ?>
		</div>
	
		<div class="gf-post-content">
			<div class="post-zigzag-arrow">
				<span></span>
			</div>
			<div class="gf-post-date">
				<a href="<?php echo esc_url($post_link); ?>" rel="bookmark"
				   title="<?php the_title(); ?>"> <?php echo get_the_date(get_option('date_format')); ?> </a>
			</div>
				<?php Spring_Plant()->helper()->getTemplate('loop/post-title', array('post_link'=> $post_link)) ?>
			<div class="gf-post-category">
                <?php the_category(', '); ?>
            </div>
			<div class="gf-post-content">
				<?php if (!(has_post_format('quote') && !empty($excerpt))): ?>
					<div class="gf-post-excerpt">
						<?php echo esc_html($excerpt); ?>
					</div>
				<?php endif; ?>
				<div class="gf-post-control">
					<div class="gf-post-read-more">
						<a href="<?php echo esc_url($post_link); ?>"
						   class="btn"><?php esc_html_e('Continue Reading', 'spring-plant'); ?></a>
					</div>
					<ul class="gf-post-meta-group">
                        <?php if(function_exists('G5P')):
                            $social_share = G5P()->options()->get_social_share();
                            unset($social_share['sort_order']);
                            if (count($social_share) > 0):
                            ?>
						        <li><?php do_action('spring_plant_post_share') ?></li>
                            <?php endif; ?>
                        <?php endif; ?>
						<li><a class="gf-post-author" title="<?php the_author() ?>"
							   href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))) ?>"><?php echo esc_html(sprintf(__('By %s','spring-plant'),get_the_author()))?></a></li>

						<?php if (comments_open()) : ?>
                            <li class="gf-post-comment">
								<?php comments_popup_link(esc_html__('No Comments','spring-plant'),esc_html__('One Comment','spring-plant'),esc_html__('Comments (%)','spring-plant'),'','') ?>
                            </li>
						<?php endif; ?>
					</ul>
					<?php edit_post_link(esc_html__( 'Edit', 'spring-plant' ), '<p class="edit-link"> ', '</p>' ); ?>
				</div>
			</div>
		</div>
	</div>
</article>