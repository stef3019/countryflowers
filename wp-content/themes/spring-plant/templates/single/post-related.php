<?php
/**
 * The template for displaying post-related.php
 *
 * @package WordPress
 * @subpackage spring
 * @since spring 1.0
 */
$single_related_post_enable = Spring_Plant()->options()->get_single_related_post_enable();
if ($single_related_post_enable !== 'on') return;
global $post;
$post_id = $post->ID;
$post_algorithm = Spring_Plant()->options()->get_single_related_post_algorithm();
$post_carousel_enable = Spring_Plant()->options()->get_single_related_post_carousel_enable();
$posts_per_page = intval(Spring_Plant()->options()->get_single_related_post_per_page());
$post_columns_gutter = intval(Spring_Plant()->options()->get_single_related_post_columns_gutter());
$post_columns = intval(Spring_Plant()->options()->get_single_related_post_columns());
$post_columns_md = intval(Spring_Plant()->options()->get_single_related_post_columns_md());
$post_columns_sm = intval(Spring_Plant()->options()->get_single_related_post_columns_sm());
$post_columns_xs = intval(Spring_Plant()->options()->get_single_related_post_columns_xs());
$post_columns_mb = intval(Spring_Plant()->options()->get_single_related_post_columns_mb());
$post_paging = Spring_Plant()->options()->get_single_related_post_paging();
$post_animation = Spring_Plant()->options()->get_single_related_post_animation();

$query_args = array(
	'ignore_sticky_posts' => true,
	'posts_per_page'      => $posts_per_page,
	'post__not_in'        => array($post_id)
);

$tag_slugs = wp_get_post_tags($post_id, array('fields' => 'slugs'));
switch ($post_algorithm) {
	case 'cat':
		$query_args['category__in'] = wp_get_post_categories($post_id);
		break;
	case 'tag':
		$query_args['tag__in'] = wp_get_object_terms($post_id, 'post_tag', array('fields' => 'ids'));
		break;
	case 'author':
		$query_args['author'] = $post->post_author;
		break;
	case 'cat-tag':
		$query_args['category__in'] = wp_get_post_categories($post_id);
		$query_args['tag__in'] = wp_get_object_terms($post_id, 'post_tag', array('fields' => 'ids'));
		break;
	case 'cat-tag-author':
		$query_args['author'] = $post->post_author;
		$query_args['category__in'] = wp_get_post_categories($post_id);
		$query_args['tag__in'] = wp_get_object_terms($post_id, 'post_tag', array('fields' => 'ids'));
		break;
	case 'random':
		$query_args['orderby'] = 'rand';
		break;
}

$settings = array(
	'post_layout' => 'grid',
	'post_item_skin' => 'post-skin-02',
	'post_paging' => $post_paging,
);

if ($post_animation !== '-1') {
	$settings['post_animation'] = $post_animation;
}
if ($post_carousel_enable !== 'on') {
	$settings['post_columns_gutter'] = $post_columns_gutter;
	$settings['post_columns'] = array(
		'xl' => $post_columns,
		'lg' => $post_columns_md,
		'md' => $post_columns_sm,
		'sm' => $post_columns_xs,
		''   => $post_columns_mb
	);
} else {
	$settings['carousel'] = array(
		'dots'       => true,
		'items'      => $post_columns,
		'margin'     => $post_columns == 1 ? 0 : $post_columns_gutter,
		'slideBy'    => $post_columns,
		'responsive' => array(
			'1200' => array(
				'items'   => $post_columns,
				'margin'  => $post_columns == 1 ? 0 : $post_columns_gutter,
				'slideBy' => $post_columns,
                'nav' => false
			),
			'992'  => array(
				'items'   => $post_columns_md,
				'margin'  => $post_columns_md == 1 ? 0 : $post_columns_gutter,
				'slideBy' => $post_columns_md,
                'nav' => false
			),
			'768'  => array(
				'items'   => $post_columns_sm,
				'margin'  => $post_columns_sm == 1 ? 0 : $post_columns_gutter,
				'slideBy' => $post_columns_sm,
                'nav' => false
			),
			'575'  => array(
				'items'   => $post_columns_xs,
				'margin'  => $post_columns_xs == 1 ? 0 : $post_columns_gutter,
				'slideBy' => $post_columns_xs,
                'nav' => true
			),
			'0'    => array(
				'items'   => $post_columns_mb,
				'margin'  => $post_columns_mb == 1 ? 0 : $post_columns_gutter,
				'slideBy' => $post_columns_mb,
                'nav' => true
			)
		),
		'autoHeight' => true,
	);
}
?>
<div class="gf-single-related-wrap">
	<h4 class="gf-heading-title"><span><?php esc_html_e('You May Also Like', 'spring-plant'); ?></span></h4>
	<?php Spring_Plant()->blog()->archive_markup($query_args, $settings); ?>
</div>
