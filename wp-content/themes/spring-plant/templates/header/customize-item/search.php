<?php
/**
 * The template for displaying search
 * @var $customize_location
 */

$searchh_type = Spring_Plant()->options()->getOptions("header_customize_{$customize_location}_search_type");
if($searchh_type === 'box') {?>
    <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) )  ?>">
        <input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search for products', 'placeholder', 'spring-plant' ) ?>" value="<?php echo get_search_query() ?>" name="s" />
        <button type="submit" class="search-submit"><?php echo esc_attr_x( 'Search', 'submit button','spring-plant' ) ?> <i class="fa fa-search"></i></button>
        <input type="hidden" name="post_type" value="product">
    </form>
    <?php
} else {
    add_action('wp_footer',array(Spring_Plant()->templates(),'search_popup'),5);?>
    <a class="search-popup-link" href="#search-popup"><i class="fa fa-search"></i></a>
<?php } ?>