<?php
/**
 * The template for displaying social-networks
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 * @var $social_networks
 * @var $social_text
 * @var $layout -  Accepts 'classic', 'circle', 'square'
 */
$social_networks_configs = &Spring_Plant()->helper()->get_social_networks();
if (!is_array($social_networks)) return;
$wrapper_classes = array(
	'gf-social-icon',
	'gf-inline'
);

if (isset($layout) && !empty($layout) && ($layout !== 'classic')) {
	$wrapper_classes[] = "social-icon-{$layout}";
}

if (isset($size) && !empty($size) && ($size !== 'normal')) {
	$wrapper_classes[] = "social-icon-{$size}";
}

$wrapper_class = implode(' ', array_filter($wrapper_classes));
?>
<ul class="<?php echo esc_attr($wrapper_class)?>">
	<?php if( $social_text !==''): ?>
		<li class="social-text"><?php echo esc_html($social_text); ?></li>
	<?php endif; ?>
	<?php foreach ($social_networks as $social): ?>
		<?php if (array_key_exists($social,$social_networks_configs)): ?>
			<?php
				$social_network = $social_networks_configs[$social];
				$social_id = $social_network['social_id'];
				$social_name = $social_network['social_name'];
				$social_link = !empty($social_network['social_link']) ? $social_network['social_link'] : '#' ;
				$social_icon = $social_network['social_icon'];
				$social_color = $social_network['social_color'];
				$social_class = '';
				if ($layout === 'circle') {
					$social_class = 'gf-hover-circle';
					$custom_css = <<<CSS
					.social-icon-circle > li.{$social_id} > a {
						border-color: $social_color;
						background-color: $social_color;
					}
CSS;
					Spring_Plant()->custom_css()->addCss($custom_css,"social-network-{$social_id}");

				}

			?>
			<li class="<?php echo esc_attr($social_id)?>">
				<?php if ($social_id === 'social-email'): ?>
					<a class="<?php echo esc_attr($social_class)?>" target="_blank" title="<?php echo esc_attr($social_name)?>" href="mailto:<?php echo esc_attr($social_link) ?>"><i class="<?php echo esc_attr($social_icon)?>"></i></a>
				<?php else: ?>
					<a class="<?php echo esc_attr($social_class)?>" target="_blank" title="<?php echo esc_attr($social_name)?>" href="<?php echo esc_url($social_link)?>"><i class="<?php echo esc_attr($social_icon)?>"></i></a>
				<?php endif; ?>

			</li>
		<?php endif; ?>
	<?php endforeach; ?>
</ul>

