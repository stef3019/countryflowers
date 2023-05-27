<?php
/**
 * Shortcode attributes
 * @var $atts
 * @var $products_per_page
 * @var $autoplay
 * @var $autoplay_timeout
 * @var $product_animation
 * @var $orderby
 * @var $order
 * @var $css_animation
 * @var $show
 * @var $product_ids
 * @var $category
 * @var $animation_duration
 * @var $animation_delay
 * @var $el_class
 * @var $css
 * @var $responsive
 * Shortcode class
 * @var $this WPBakeryShortCode_GSF_Products_Horizontal
 */
if (!function_exists('Spring_Plant')) return;
$products_per_page = $autoplay = $autoplay_timeout = $product_animation = $orderby = $order = $css_animation = $show = $product_ids = $category =
$css_animation = $animation_duration = $animation_delay = $el_class = $css = $responsive = '';
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$wrapper_classes = array(
	'gsf-products-horizontal',
    'woocommerce',
	G5P()->core()->vc()->customize()->getExtraClass($el_class),
	$this->getCSSAnimation($css_animation),
	vc_shortcode_custom_css_class( $css ),
	$responsive
);

if ('' !== $css_animation && 'none' !== $css_animation) {
	$animation_class = G5P()->core()->vc()->customize()->get_animation_class($animation_duration, $animation_delay);
	$wrapper_classes[] = $animation_class;
}


$product_visibility_term_ids = wc_get_product_visibility_term_ids();

$query_args = array(
    'posts_per_page' => intval($products_per_page),
    'post_status'    => 'publish',
    'post_type'      => 'product',
    'no_found_rows'  => 1,
    'meta_query'     => array(),
    'tax_query'      => array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'product_visibility',
            'field'    => 'term_taxonomy_id',
            'terms'    => is_search() ? $product_visibility_term_ids['exclude-from-search'] : $product_visibility_term_ids['exclude-from-catalog'],
            'operator' => 'NOT IN',
        )
    ),
    'post_parent'  => 0
);

if(($show != 'products')) {
    if (!empty($category)) {
        $query_args['tax_query'][] = array(
            'taxonomy' => 'product_cat',
            'terms' => explode(',', $category),
            'field' => 'slug',
            'operator' => 'IN'
        );
        $category = G5P()->helper()->get_term_ids_from_slugs(explode(',', $category), 'product_cat');
    }
} else {
    $category = array();
}
switch($show) {
    case 'sale':
        $product_ids_on_sale    = wc_get_product_ids_on_sale();
        $product_ids_on_sale[]  = 0;
        $query_args['post__in'] = $product_ids_on_sale;
        break;
    case 'new-in':
        $query_args['orderby'] = 'date';
        $query_args['order'] = 'DESC';
        break;
    case 'featured':
        $query_args['tax_query'][] = array(
            'taxonomy' => 'product_visibility',
            'field'    => 'term_taxonomy_id',
            'terms'    => $product_visibility_term_ids['featured'],
        );
        break;
    case 'top-rated':
        $query_args['meta_key'] = '_wc_average_rating';
        $query_args['orderby'] = 'meta_value_num';
        $query_args['order'] = 'DESC';
        $query_args['meta_query'] = WC()->query->get_meta_query();
        $query_args['tax_query'] = WC()->query->get_tax_query();
        break;
    case 'recent-review':
        add_filter( 'posts_clauses', array($this, 'order_by_comment_date_post_clauses' ) );
        break;
    case 'best-selling' :
        $query_args['meta_key'] = 'total_sales';
        $query_args['orderby'] = 'meta_value_num';
        break;
    case 'products':
        if ( ! empty( $product_ids ) ) {
            $product_ids = explode( ',', $product_ids );
            $query_args['post__in'] = $product_ids;
            $query_args['posts_per_page'] = -1;
            $query_args['orderby'] = 'post__in';
        }
        break;
}

if (in_array($show,array('all','sale','featured'))) {
    $query_args['order'] = $order;
    switch ( $orderby ) {
        case 'price' :
            $query_args['meta_key'] = '_price';
            $query_args['orderby']  = 'meta_value_num';
            break;
        case 'rand' :
            $query_args['orderby']  = 'rand';
            break;
        case 'sales' :
            $query_args['meta_key'] = 'total_sales';
            $query_args['orderby']  = 'meta_value_num';
            break;
        default :
            $query_args['orderby']  = 'date';
    }
}

if($show =='recent-review' ){
    remove_filter( 'posts_clauses', array($this, 'order_by_comment_date_post_clauses' )  );
}

$settings = array(
    'posts_per_page' => (is_numeric( $products_per_page ) && $products_per_page > 0) ? $products_per_page : '-1',
    'image_size' => '600x330',
    'post_layout' => 'list-02',
    'post_paging' => 'none',
    'cat' => $category,
);
if($product_animation !== '') {
    $settings['post_animation'] = $product_animation;
}
$owl_args = array(
    'items' => 1,
    'margin' => 0,
    'slideBy' => 1,
    'dots' => false,
    'nav' => false,
    'autoHeight' => true,
    'autoplay' => ($autoplay === 'on') ? true : false,
    'autoplayTimeout' => intval($autoplay_timeout),
);
$settings['carousel'] = $owl_args;


$class_to_filter = implode(' ', array_filter($wrapper_classes));
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts);
if (!(defined('CSS_DEBUG') && CSS_DEBUG)) {
    wp_enqueue_style(G5P()->assetsHandle('gf-products-horizontal'), G5P()->helper()->getAssetUrl('shortcodes/products-horizontal/assets/css/products-horizontal.min.css'), array(), G5P()->pluginVer());
}
wp_enqueue_script(G5P()->assetsHandle('products-horizontal'), G5P()->helper()->getAssetUrl('shortcodes/products-horizontal/assets/js/products-horizontal.min.js'), array( 'jquery' ), G5P()->pluginVer(), true);
?>
<div class="<?php echo esc_attr($css_class) ?>">
    <?php Spring_Plant()->woocommerce()->archive_markup($query_args, $settings);  ?>
</div>