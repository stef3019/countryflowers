<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 * @var $image_size
 * @var $post_class
 * @var $post_inner_class
 * @var $placeholder_enable
 * @var $product_item_skin
 */
$product_item_skin = isset($product_item_skin) ? $product_item_skin : Spring_Plant()->options()->get_product_item_skin();
$product_layout = isset($post_layout) ? $post_layout : Spring_Plant()->options()->get_product_catalog_layout();
if (!isset($image_size)) {
	$image_size = 'woocommerce_thumbnail';
}
if(!in_array($product_layout, array('grid', 'list'))) {
	$product_item_skin = '';
}

if('product-skin-07' === $product_item_skin) {
    $product_item_skin = 'product-skin-01';
}

Spring_Plant()->helper()->getTemplate('woocommerce/loop/layout/' .$product_item_skin, array('image_size' => $image_size));