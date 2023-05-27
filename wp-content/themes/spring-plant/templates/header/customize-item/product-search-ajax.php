<?php
/**
 * The template for displaying product search ajax
 */
$ajax_nonce = wp_create_nonce("search_popup_nonce");
?>
<div data-search-ajax="true" data-search-ajax-action="search_popup"
      data-search-ajax-nonce="<?php echo esc_attr($ajax_nonce) ?>" id="search-product-ajax"
      class="search-ajax-wrap">
    <form action="<?php echo esc_url(home_url('/')) ?>" method="get" class="search-popup-form clearfix">
        <div class="categories">
            <span data-slug=""><?php esc_html_e('All Category','spring-plant') ?></span>
            <input type="hidden" name="product_cat" value="">
            <?php
            $list_args          = array(
                'hierarchical' => true,
                'taxonomy'     => 'product_cat',
                'hide_empty'   => true,
            );
            include_once get_template_directory() . '/inc/walker/product-cat-list-walker.class.php';
            $list_args['walker']                     = new GSF_Product_Cat_List_Walker();
            $list_args['menu_order'] = false;
            $list_args['title_li']                   = '';
            $list_args['current_category_ancestors'] = array();
            $list_args['pad_counts']                 = 1;
            $list_args['show_option_none']           = __( 'No product categories exist.', 'spring-plant' );

            echo '<ul class="search-category-dropdown hidden">';
            echo '<li class="cate-item all-cate"><span data-slug="">' . esc_html__('All Category','spring-plant') .'</span></li>';
            wp_list_categories( apply_filters( 'woocommerce_product_categories_widget_args', $list_args ) );
            echo '</ul>';
            ?>
        </div>
        <input data-search-ajax-control="input" name="s" class="search-popup-field" type="search"
               placeholder="<?php esc_attr_e('Search ...', 'spring-plant') ?>"
               autocomplete="off">
        <input type="hidden" name="post_type" value="product">
        <button type="submit" class="search-popup-button" ><i data-search-ajax-control="icon" class="fa fa-search"></i></button>
    </form>
    <div data-search-ajax-control="result" class="search-popup-result"></div>
</div>