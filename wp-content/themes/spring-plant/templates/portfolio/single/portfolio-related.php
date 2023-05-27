<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 9/21/2017
 * Time: 10:07 AM
 */
$single_portfolio_related_enable = Spring_Plant()->options()->get_single_portfolio_related_enable();
$single_portfolio_related_full_width =  Spring_Plant()->options()->get_single_portfolio_related_full_width_enable();
if ($single_portfolio_related_enable !== 'on') return;
global $post;
$post_id = $post->ID;
$post_algorithm = Spring_Plant()->options()->get_single_portfolio_related_algorithm();
$post_carousel_enable = Spring_Plant()->options()->get_single_portfolio_related_carousel_enable();
$posts_per_page = intval(Spring_Plant()->options()->get_single_portfolio_related_per_page());
$post_columns_gutter = intval(Spring_Plant()->options()->get_single_portfolio_related_columns_gutter());
$post_columns = intval(Spring_Plant()->options()->get_single_portfolio_related_columns());
$post_columns_md = intval(Spring_Plant()->options()->get_single_portfolio_related_columns_md());
$post_columns_sm = intval(Spring_Plant()->options()->get_single_portfolio_related_columns_sm());
$post_columns_xs = intval(Spring_Plant()->options()->get_single_portfolio_related_columns_xs());
$post_columns_mb = intval(Spring_Plant()->options()->get_single_portfolio_related_columns_mb());
$post_paging = Spring_Plant()->options()->get_single_portfolio_related_post_paging();
$post_animation = Spring_Plant()->options()->get_single_portfolio_related_animation();

$container_class = '';
if ($post_carousel_enable) {
    $post_paging = 'none';
}
$full_width_class = '';
if('on' !== $single_portfolio_related_full_width){
    $full_width_class = 'container';
}
$query_args = array(
    'ignore_sticky_posts' => true,
    'posts_per_page' => $posts_per_page,
    'post__not_in' => array($post_id),
    'post_type' => Spring_Plant()->portfolio()->get_post_type(),
    'post_status'      => 'publish',
    'tax_query'      => array(
    ),
);
switch ($post_algorithm) {
    case 'cat':
        $query_args['tax_query'][] = array(
            'taxonomy' => Spring_Plant()->portfolio()->get_taxonomy_category(),
            'field' => 'term_id',
            'terms' => Spring_Plant()->portfolio()->get_portfolio_term_ids($post_id),
            'operator' 		=> 'IN'
        );
        break;
    case 'author':
        $query_args['author'] = $post->post_author;
        break;
    case 'cat-author':
        $query_args['author']       = $post->post_author;
        $query_args['tax_query'][] = array(
            'taxonomy' => Spring_Plant()->portfolio()->get_taxonomy_category(),
            'field' => 'term_id',
            'terms' => Spring_Plant()->portfolio()->get_portfolio_term_ids($post_id),
            'operator' 		=> 'IN'
        );
        break;
    case 'random':
        $query_args['orderby'] = 'rand';
        break;
}

$settings = array(
    'post_layout' => 'grid',
    'post_paging' => $post_paging
);
if($post_animation !== '') {
    $settings['post_animation'] = $post_animation;
}
$image_size = Spring_Plant()->options()->get_single_portfolio_related_image_size();
$settings['image_size'] = $image_size;
if ($image_size === 'full') {
    $image_ratio = Spring_Plant()->options()->get_single_portfolio_related_image_ratio();
    if ($image_ratio === 'custom') {
        $image_ratio_custom = Spring_Plant()->options()->get_single_portfolio_related_image_ratio_custom();
        if (is_array($image_ratio_custom) && isset($image_ratio_custom['width']) && isset($image_ratio_custom['height'])) {
            $image_ratio_custom_width = intval($image_ratio_custom['width']);
            $image_ratio_custom_height = intval($image_ratio_custom['height']);
            if (($image_ratio_custom_width > 0) && ($image_ratio_custom_height > 0)) {
                $image_ratio = "{$image_ratio_custom_width}x{$image_ratio_custom_height}";
            }
        } elseif (preg_match('/x/',$image_ratio_custom)) {
            $image_ratio = $image_ratio_custom;
        }
    }
    if ($image_ratio === 'custom') {
        $image_ratio = '1x1';
    }
    $settings['image_ratio'] = $image_ratio;
}

if ($post_carousel_enable !== 'on') {
    $settings['post_columns_gutter'] = $post_columns_gutter;
    $settings['post_columns'] = array(
        'xl' => $post_columns,
        'lg' => $post_columns_md,
        'md' => $post_columns_sm,
        'sm' => $post_columns_xs,
        '' => $post_columns_mb
    );
} else {
    $settings['carousel'] = array(
        'dots' => true,
        'items' => $post_columns,
        'margin' => $post_columns == 1 ? 0 : $post_columns_gutter,
        'slideBy' => $post_columns,
        'responsive' => array(
            '1200' => array(
                'items' => $post_columns,
                'margin' => $post_columns == 1 ? 0 : $post_columns_gutter,
                'slideBy' => $post_columns,
                'nav' => false
            ),
            '992' => array(
                'items' => $post_columns_md,
                'margin' => $post_columns_md == 1 ? 0 : $post_columns_gutter,
                'slideBy' => $post_columns_md,
                'nav' => false
            ),
            '768' => array(
                'items' => $post_columns_sm,
                'margin' => $post_columns_sm == 1 ? 0 : $post_columns_gutter,
                'slideBy' => $post_columns_sm,
                'nav' => false
            ),
            '575' => array(
                'items' => $post_columns_xs,
                'margin' => $post_columns_xs == 1 ? 0 : $post_columns_gutter,
                'slideBy' => $post_columns_xs,
                'nav' => true
            ),
            '0' => array(
                'items' => $post_columns_mb,
                'margin' => $post_columns_mb == 1 ? 0 : $post_columns_gutter,
                'slideBy' => $post_columns_mb,
                'nav' => true
            )
        ),
        'autoHeight' => true,
    );
}
?>
<div class="gf-single-portfolio-related-wrap mg-top-90 sm-mg-top-50 pd-top-70 sm-pd-top-50">
    <div class="portfolio-related-inner <?php echo esc_attr($full_width_class)?>">
        <h4 class="gf-heading-title fs-24 mg-bottom-45 mg-top-0"><?php esc_html_e('Related Portfolios', 'spring-plant'); ?></h4>
        <?php Spring_Plant()->portfolio()->archive_markup($query_args, $settings); ?>
    </div>
</div>
