<?php
/**
 * Template display product title
 *
 * @package WordPress
 * @subpackage spring
 */
$product_rating_enable = Spring_Plant()->options()->get_product_rating_enable();
?>

<h4 class="product-item-name product_title">
    <a class="gsf-link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
</h4>
