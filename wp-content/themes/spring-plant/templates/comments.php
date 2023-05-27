<?php
/**
 * The template for displaying comments.php
 * @var $comment
 * @var $args
 * @var $depth
 */
$GLOBALS['comment'] = $comment;
?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
	<div id="comment-<?php comment_ID(); ?>" class="comment-body clearfix">
		<?php echo get_avatar($comment, $args['avatar_size']); ?>
		<div class="comment-text entry-content">
			<ul class="gf-inline comment-top">
				<li>
					<h4 class="author-name"><?php echo get_comment_author_link() ?></h4>
				</li>
			</ul>
			<div class="gf-entry-content">
				<?php comment_text() ?>
				<?php if ($comment->comment_approved == '0') : ?>
					<em><?php esc_html_e('Your comment is awaiting moderation.','spring-plant');?></em>
				<?php endif; ?>
			</div>
			<div class="comment-meta disable-color">
				<span class="comment-meta-date disable-color">
					<?php echo (get_comment_date(get_option('date_format'))) ; ?>
				</span>
				<div class="reply-form">
					<?php edit_comment_link( esc_html__('Edit','spring-plant') . '<i class="fa fa-edit"></i> ') ; ?>
					<?php comment_reply_link(array_merge($args, array(
						'depth' => $depth,
						'max_depth' => $args['max_depth'],
						'reply_text' => esc_html__('Reply', 'spring-plant') . '<i class="fa fa-long-arrow-right"></i> ',
						'reply_to_text' =>  esc_html__('Reply to %s', 'spring-plant') . '<i class="fa fa-reply"></i> ',
						'login_text' => '<i class="fa fa-reply"></i> ' . esc_html__('Log in to Reply', 'spring-plant'),
					))) ?>
				</div>
			</div>

		</div>
	</div>
