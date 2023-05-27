<?php
/**
 * The template for displaying post-navigation.php
 */
$single_navigation_enable = Spring_Plant()->options()->get_single_navigation_enable();
if ($single_navigation_enable !== 'on') return;
?>
<nav class="gf-post-navigation">
	<?php
	$prev_post = get_adjacent_post(false, '', true);
	$prev_post_class = 'post-prev';
	?>
	<div class="<?php echo esc_attr($prev_post_class); ?>">
		<?php if (is_object($prev_post)): ?>
			<h4 class="gf-post-title disable-color">
				<a href="<?php the_permalink($prev_post) ?>" title="<?php echo esc_attr($prev_post->post_title); ?>"><i class="flaticon-left-arrow"></i> <?php echo esc_html($prev_post->post_title); ?></a>
			</h4>
		<?php else: ?>
			<span><?php esc_html_e('No Older Articles', 'spring-plant') ?></span>
		<?php endif; ?>
	</div>
    <?php
    $post_type = get_post_type(get_the_ID());
    $post_type_object        = get_post_type_object( $post_type );
    $post_type_archive_label = $post_type_object->labels->name;
    $post_type_archive_link = get_post_type_archive_link( $post_type ); ?>

    <div class="post-archive-link">
        <a class="gsf-link" href="<?php echo esc_url( $post_type_archive_link ); ?>" title="<?php echo esc_attr( $post_type_archive_label ); ?>"><i class="flaticon-menu"></i></a>
    </div>
	<?php
	$next_post = get_adjacent_post(false, '', false);
	$next_post_class = 'post-next';
	?>
	<div class="<?php echo esc_attr($next_post_class) ?>">
		<?php if (is_object($next_post)): ?>
			<h4 class="gf-post-title disable-color">
			<a href="<?php the_permalink($next_post) ?>"
			   title="<?php echo esc_attr($next_post->post_title); ?>"><?php echo esc_html($next_post->post_title); ?> <i class="flaticon-right-arrow"></i></a>
			</h4>
		<?php else: ?>
			<span><?php esc_html_e('No Newer Articles', 'spring-plant') ?></span>
		<?php endif; ?>
	</div>
</nav>
