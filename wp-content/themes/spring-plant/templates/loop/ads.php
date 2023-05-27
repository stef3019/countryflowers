<?php
/**
 * The template for displaying ads.php
 *
 * @var $post_class
 * @var $post_inner_class
 */
if (!is_home() && !is_post_type_archive('post') && !is_category()) return;
$gsf_query = Spring_Plant()->query()->get_query();
$paged = Spring_Plant()->query()->query_var_paged();
if ($paged !== 1) return;
$post_settings = &Spring_Plant()->blog()->get_layout_settings();
$post_layout = isset($post_settings['post_layout']) ? $post_settings['post_layout'] : 'large-image';
if (!in_array($post_layout, array('large-image', 'medium-image'))) return;
$post_ads = Spring_Plant()->options()->get_post_ads();
if (!is_array($post_ads)) return;
$post_class = $post_class . ' gf-ads';
?>
<?php foreach ($post_ads as $ads): ?>
	<?php $position = isset($ads['position']) ? intval($ads['position']) : -1; ?>
	<?php if ($position === ($gsf_query->current_post + 1)): ?>
		<article <?php post_class($post_class) ?>>
			<div class="<?php echo esc_attr($post_inner_class); ?>">
				<?php Spring_Plant()->helper()->shortCodeContent($ads['content']); ?>
			</div>
		</article>
	<?php endif; ?>
<?php endforeach; ?>