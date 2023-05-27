<?php
/**
 * The template for displaying post-meta
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 * @var $post_link
 */
if(!isset($post_link)) {
    $post_link = get_the_permalink();
}
global $cat_badge;
?>
	<li class="meta-date">
		<a href="<?php echo esc_url($post_link); ?>" rel="bookmark"
		   title="<?php the_title(); ?>"> <?php echo get_the_date(get_option('date_format')); ?> </a>
	</li>
<?php edit_post_link(esc_html__('Edit', 'spring-plant'), '<li class="edit-link">', '</li>'); ?>
<?php $cat_badge = false; ?>