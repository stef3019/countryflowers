<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.4.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );
$sidebar_layout = Spring_Plant()->options()->get_sidebar_layout();
if(!in_array($sidebar_layout, array('', 'none'))) {
    echo '<div class="woocommerce-has-sidebar">';
};
?>

<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>

	<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
		<thead>
			<tr>
				<th class="product-name" colspan="2"><?php esc_html_e( 'Product name', 'spring-plant' ); ?></th>
				<th class="product-price"><?php esc_html_e( 'Unit Price', 'spring-plant' ); ?></th>
				<th class="product-quantity"><?php esc_html_e( 'Quantity', 'spring-plant' ); ?></th>
				<th class="product-subtotal"><?php esc_html_e( 'Total', 'spring-plant' ); ?></th>
                <th class="product-remove">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php do_action( 'woocommerce_before_cart_contents' ); ?>

			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>
					<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
						<td class="product-thumbnail">
							<?php
								$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
								$extension = pathinfo($thumbnail, PATHINFO_EXTENSION);
								$newExtension = strtolower($extension);
								$thumbnail = str_replace($extension, $newExtension, $thumbnail);
								if ( ! $product_permalink ) {
									echo wp_kses_post($thumbnail);
								} else {
									printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
								}
							?>
						</td>

						<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'spring-plant' ); ?>">
							<?php
								if ( ! $product_permalink ) {
									echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;');
								} else {
									echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s" class="gsf-link transition03">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ));
								}
                            do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );
						// Meta data.
						echo wc_get_formatted_cart_item_data( $cart_item );

								// Backorder notification
								if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
									echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'spring-plant' ) . '</p>') );
								}
							?>
						</td>

						<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'spring-plant' ); ?>">
							<?php
								echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
							?>
						</td>

						<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'spring-plant' ); ?>">
							<?php
							if ( $_product->is_sold_individually() ) {
								$min_quantity = 1;
								$max_quantity = 1;
							} else {
								$min_quantity = 0;
								$max_quantity = $_product->get_max_purchase_quantity();
							}

							$product_quantity = woocommerce_quantity_input(
								array(
									'input_name'   => "cart[{$cart_item_key}][qty]",
									'input_value'  => $cart_item['quantity'],
									'max_value'    => $max_quantity,
									'min_value'    => $min_quantity,
									'product_name' => $_product->get_name(),
								),
								$_product,
								false
							);

							echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
							?>
						</td>

						<td class="product-subtotal" data-title="<?php esc_attr_e( 'Total', 'spring-plant' ); ?>">
							<?php
								echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
							?>
						</td>
                        <td class="product-remove">
                            <?php
                            // @codingStandardsIgnoreLine
                            echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
                                '<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-product_sku="%s"><i class="flaticon-cross"></i></a>',
                                esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                esc_attr__('Remove this item', 'spring-plant'),
                                esc_attr( $product_id ),
                                esc_attr( $_product->get_sku() )
                            ), $cart_item_key );
                            ?>
                        </td>
					</tr>
					<?php
				}
			}
			?>

			<?php do_action( 'woocommerce_cart_contents' ); ?>
			<?php do_action( 'woocommerce_after_cart_contents' ); ?>
		</tbody>
	</table>
    <div class="cart-actions d-flex align-items-center">
        <div>
            <a class="btn btn-outline btn-rounded btn-md btn-black clear-cart" onclick='javascript:if(!confirm("<?php esc_attr_e('Clear all items in your cart?', 'spring-plant'); ?>")) {return false;}' href="<?php echo wc_get_page_permalink( 'cart' ); ?>?empty-cart">
                <?php esc_html_e('Clear Shopping Cart', 'spring-plant'); ?>
            </a>
            <input type="submit" class="button btn btn-outline btn-rounded btn-md btn-black" name="update_cart" value="<?php esc_attr_e( 'Update Shopping Cart', 'spring-plant' ); ?>" />
            <?php wp_nonce_field( 'woocommerce-cart' ); ?>
        </div>
        <div class="continue-shopping">
            <a class="btn btn-classic btn-rounded btn-md btn-black" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
                <?php esc_html_e( 'Continue Shopping', 'spring-plant' ) ?>
            </a>
        </div>
    </div>
	<?php do_action( 'woocommerce_after_cart_table' ); ?>
    <?php if ( wc_coupons_enabled() ) { ?>
        <div class="coupon">
            <h4 class="woocommerce-block-title"><?php esc_html_e( 'Coupon discount', 'spring-plant' ); ?></h4>
            <p class="coupon-desc"><?php esc_html_e( 'Enter your code if you have one.', 'spring-plant' ); ?></p>
            <div class="coupon-actions d-flex align-items-center">
                <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Enter coupon code', 'spring-plant' ); ?>" />
                <input type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'spring-plant' ); ?>" />
            </div>
            <?php do_action( 'woocommerce_cart_coupon' ); ?>
        </div>
    <?php } ?>
</form>

<div class="cart-collaterals">
    <?php
    /**
     * woocommerce_cart_collaterals hook.
     *
     * @hooked woocommerce_cart_totals - 10
     * @hooked woocommerce_cross_sell_display 20
     */
    do_action( 'woocommerce_cart_collaterals' ); ?>
</div>
<?php
if(!in_array($sidebar_layout, array('', 'none'))) {
    echo '</div>';
};?>
<?php do_action( 'woocommerce_after_cart' ); ?>
