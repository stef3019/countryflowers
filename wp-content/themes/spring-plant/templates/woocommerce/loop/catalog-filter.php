<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
global $wp_registered_sidebars;
if (!woocommerce_products_will_display()) return;
$woocommerce_customize = Spring_Plant()->options()->get_woocommerce_customize();
$product_layout = Spring_Plant()->options()->get_product_catalog_layout();
if(!in_array($product_layout, array('grid', 'list'))) {
    if(isset($woocommerce_customize['left']['switch-layout'])) {
        unset($woocommerce_customize['left']['switch-layout']);
    }
    if(isset($woocommerce_customize['right']['switch-layout'])) {
        unset($woocommerce_customize['right']['switch-layout']);
    }
}
$woocommerce_customize_filter = Spring_Plant()->options()->get_woocommerce_customize_filter();
?>
<div class="gsf-catalog-filter">
    <div class="woocommerce-custom-wrap d-flex align-items-center">
        <div class="woocommerce-customize-left">
            <?php if(isset($woocommerce_customize['left'])): ?>
                <ul class="gf-inline">
                    <?php foreach ($woocommerce_customize['left'] as $key => $value): ?>
                        <li class="gsf-catalog-filter-<?php echo esc_attr($key)?>">
                            <?php Spring_Plant()->helper()->getTemplate("woocommerce/loop/catalog-item/{$key}"); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <div class="woocommerce-customize-right">
            <?php if(isset($woocommerce_customize['right'])): ?>
                <ul class="gf-inline">
                    <?php foreach ($woocommerce_customize['right'] as $key => $value): ?>
                        <li class="gsf-catalog-filter-<?php echo esc_attr($key)?>">
                            <?php Spring_Plant()->helper()->getTemplate("woocommerce/loop/catalog-item/{$key}"); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    <?php if((array_key_exists('filter', $woocommerce_customize['right']) || array_key_exists('filter', $woocommerce_customize['left']))):?>
        <?php if(('show-bellow' === $woocommerce_customize_filter)): ?>
            <?php
            $woocommerce_customize_filter_columns = array(
                '' => Spring_Plant()->options()->get_filter_columns(),
                'md-' => Spring_Plant()->options()->get_filter_columns_md(),
                'sm-' => Spring_Plant()->options()->get_filter_columns_sm(),
                'xs-' => Spring_Plant()->options()->get_filter_columns_xs(),
                'mb-' => Spring_Plant()->options()->get_filter_columns_mb()
            );
            $filter_class = '';
            foreach ($woocommerce_customize_filter_columns as $key => $value) {
                $filter_class .= 'gf-filter-' . $key . $value . '-columns ';
            }
            ?>
            <div id="gf-filter-content" class="<?php echo esc_attr($filter_class); ?>">
                <div class="container d-flex">
                    <?php if (is_active_sidebar('woocommerce-filter')): ?>
                        <?php dynamic_sidebar('woocommerce-filter') ?>
                    <?php elseif (isset($wp_registered_sidebars['woocommerce-filter'])): ?>
                        <div class="no-widget-content mg-bottom-30"> <?php printf(wp_kses_post(__('Please insert widget into sidebar <b>%s</b> in Appearance > <a class="text-color-accent" title="manage widgets" href="%s">Widgets</a> ','spring-plant')),$wp_registered_sidebars['woocommerce-filter']['name'],admin_url( 'widgets.php' )); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif;
        global $wp;
        $price_url = $link = home_url($wp->request);
        $filter_arr = array(
            'price' => array(
                'active' => false,
                'title' => esc_html__('Price', 'spring-plant'),
                'value' => '',
                'url' => ''
            )
        );
        $queried_object = get_queried_object();

        if ( isset( $_GET['min_price'] ) ) {
            $link = add_query_arg( 'min_price', wc_clean( $_GET['min_price'] ), $link );
            $filter_arr['price']['active'] = true;
            $filter_arr['price']['value'] = $_GET['min_price'] . ' - ' . $_GET['max_price'];
        }

        if ( isset( $_GET['max_price'] ) ) {
            $link = add_query_arg( 'max_price', wc_clean( $_GET['max_price'] ), $link );
        }

        if ( isset( $_GET['orderby'] ) ) {
            $link = add_query_arg( 'orderby', wc_clean( $_GET['orderby'] ), $link );

        }

        if ( isset( $_GET['product_tag'] ) ) {
            $link = add_query_arg( 'product_tag', urlencode( $_GET['product_tag'] ), $link );
        }

        elseif( is_product_tag() && $queried_object ){
            $link = add_query_arg( array( 'product_tag' => $queried_object->slug ), $link );
        }

        if ( get_search_query() ) {
            $link = add_query_arg( 's', rawurlencode( wp_specialchars_decode( get_search_query() ) ), $link );
        }

        if ( isset( $_GET['post_type'] ) ) {
            $link = add_query_arg( 'post_type', wc_clean( $_GET['post_type'] ), $link );
        }

        // Min Rating Arg
        if ( isset( $_GET['rating_filter'] ) ) {
            $link = add_query_arg( 'rating_filter', wc_clean( $_GET['rating_filter'] ), $link );
        }
        $price_url = remove_query_arg('min_price', $link);
        $price_url = remove_query_arg('max_price', $price_url);
        if ( $_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes() ) {
            foreach ( $_chosen_attributes as $name => $data ) {
                $filter_name = sanitize_title( str_replace( 'pa_', '', $name ) );
                if ( ! empty( $data['terms'] ) ) {
                    $filter_arr[$filter_name] = array(
                        'active' => true,
                        'title' => $filter_name,
                        'value' => implode( ',', $data['terms'] ),
                        'url' => ''
                    );
                    $link  = add_query_arg( 'filter_' . $filter_name, implode( ',', $data['terms'] ), $link );
                    $price_url = add_query_arg( 'filter_' . $filter_name, implode( ',', $data['terms'] ), $price_url );
                }
                if ( 'or' == $data['query_type'] ) {
                    $price_url = add_query_arg( 'query_type_' . $filter_name, 'or', $price_url );
                    $link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
                }
            }
            foreach ( $_chosen_attributes as $name => $data ) {
                $filter_name = sanitize_title( str_replace( 'pa_', '', $name ) );
                $filter_arr[$filter_name]['url'] = remove_query_arg('filter_' . $filter_name, $link);
            }
        }
        $filter_arr['price']['url'] = $price_url;?>
        <div class="clear-filter-wrap">
            <?php foreach ($filter_arr as $key => $value):
                if(isset($value['active']) && $value['active']):?>
                    <a class="clear-filter-<?php echo esc_attr($key) ?> gsf-link transition03 no-animation" href="<?php echo esc_url($value['url']); ?>"><?php echo esc_attr($value['title']) . ': ' . esc_attr($value['value']); ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif;?>
</div>