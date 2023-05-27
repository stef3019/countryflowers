<?php
/**
 * The template for displaying pagination.php
 * @var $settingId
 * @var $pagenum_link
 */
global $wp_rewrite;

$paged   =  Spring_Plant()->query()->query_var_paged();
$max_num_pages = Spring_Plant()->query()->get_max_num_pages();
if (!isset($pagenum_link) || ($pagenum_link === '')) {
	$pagenum_link = html_entity_decode( get_pagenum_link() );
}
$query_args   = array();
$url_parts    = explode( '?', $pagenum_link );

if ( isset( $url_parts[1] ) ) {
	wp_parse_str( $url_parts[1], $query_args );
}

$pagenum_link = esc_url(remove_query_arg( array_keys( $query_args ), $pagenum_link ));
$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';
?>
<div data-items-paging="pagination-ajax" class="gf-paging blog-pagination clearfix" data-id="<?php echo esc_attr($settingId) ?>">
	<?php $page_links =  paginate_links( array(
		'base'     => $pagenum_link,
		'format'   => $format,
		'total'    => $max_num_pages,
		'current'  => $paged,
		'mid_size' => 1,
		'add_args' => array_map( 'urlencode', $query_args ),
        'prev_text' => '<i class="fa fa-arrow-left"></i>',
        'next_text' => '<i class="fa fa-arrow-right"></i>',
	) );
	$page_links = preg_replace('/page-numbers/','no-animation page-numbers transition03 gsf-link',$page_links);
	$page_links = preg_replace('/<a/','<a',$page_links);
	echo ($page_links);
	?>
</div>
