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
$max_num_pages = Spring_Plant()->query()->get_max_num_pages();
$next_link = get_next_posts_page_link($max_num_pages);
if (empty($next_link)) return;
?>
<div data-items-paging="load-more" class="gf-paging load-more clearfix text-center" data-id="<?php echo esc_attr($settingId) ?>">
    <a class="no-animation btn btn-black btn-md" href="<?php echo esc_url($next_link); ?>">
        <?php esc_html_e('Load More', 'spring-plant') ?>
    </a>
</div>
