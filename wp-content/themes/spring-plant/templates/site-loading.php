<?php
/**
 * The template for displaying site-loading.php
 */
$loading_animation = Spring_Plant()->options()->get_loading_animation();
if (empty($loading_animation)) return;
$logo_loading = Spring_Plant()->options()->get_loading_logo();
?>
<div class="site-loading">
	<div class="block-center">
		<div class="block-center-inner">
			<?php if (isset($logo_loading['url']) && !empty($logo_loading['url'])): ?>
				<img class="logo-loading" alt="<?php esc_attr_e('Logo Loading','spring-plant') ?>" src="<?php echo esc_url($logo_loading['url']) ?>" />
			<?php endif; ?>
			<?php Spring_Plant()->helper()->getTemplate("loading/{$loading_animation}") ?>
		</div>
	</div>
</div>
