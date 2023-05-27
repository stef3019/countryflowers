<?php
/**
 * The template for displaying load-more.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 * @var $settingId
 * @var $pagenum_link
 */
$paged   =  Spring_Plant()->query()->query_var_paged();
$next_classes = array(
	'no-animation',
	'transition03 gsf-link',
	'gf-button-next'
);
$prev_classes = array(
	'no-animation',
    'transition03 gsf-link',
	'gf-button-prev'
);
$max_num_pages = Spring_Plant()->query()->get_max_num_pages();
if ($paged >=  $max_num_pages) {
    $next_classes[] = 'disable';
}

if ($paged <= 1) {
    $prev_classes[] = 'disable';
}
$next_class = implode(' ', array_filter($next_classes));
$prev_class = implode(' ', array_filter($prev_classes));

$next_link = get_next_posts_page_link($max_num_pages);
$prev_link = get_previous_posts_page_link();
?>
<div data-items-paging="next-prev" class="gf-paging next-prev text-center clearfix" data-id="<?php echo esc_attr($settingId) ?>">
	<a title="<?php esc_attr_e('Prev', 'spring-plant') ?>" class="<?php echo esc_attr($prev_class)?>" href="<?php echo esc_url($prev_link); ?>">
		<i class="fa fa-arrow-left"></i>
	</a>
	<a title="<?php esc_attr_e('Next', 'spring-plant') ?>" class="<?php echo esc_attr($next_class)?>" href="<?php echo esc_url($next_link); ?>">
		<i class="fa fa-arrow-right"></i>
	</a>
</div>
