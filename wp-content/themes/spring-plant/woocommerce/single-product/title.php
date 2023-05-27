<?php
/**
 * Single Product title
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/title.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @author     WooThemes
 * @package    WooCommerce/Templates
 * @version    1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
global $single_product_title, $breadcrumb_used;
$single_layout = Spring_Plant()->options()->get_product_single_layout();
if (isset($breadcrumb_used) && !$breadcrumb_used && $single_layout !=='layout-04') {
	?>
	<div class="product-breadcrum">
		<?php Spring_Plant()->breadcrumbs()->get_breadcrumbs(); ?>
	</div>
	<?php
}
if(empty($single_product_title)) {
    the_title('<h1 class="product_title entry-title">', '</h1>');
}
