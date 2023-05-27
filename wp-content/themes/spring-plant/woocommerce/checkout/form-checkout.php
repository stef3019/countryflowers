<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 * @var $checkout
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo '<div class="login-coupon-block">';
do_action( 'woocommerce_before_checkout_form', $checkout );
echo '</div>';

// If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', esc_html__( 'You must be logged in to checkout.', 'spring-plant' ) );
	return;
}
$sidebar_layout = Spring_Plant()->options()->get_sidebar_layout();
$col_1_class = 'col-sm-12 col-lg-6';
$col_2_class = 'col-sm-12 col-lg-5 offset-lg-1 offset-md-0 md-mg-top-40';
if(!in_array($sidebar_layout, array('', 'none'))) {
    $col_2_class = 'col-sm-12 col-lg-6 md-mg-top-40';
}
if(!in_array($sidebar_layout, array('', 'none'))) {
    echo '<div class="woocommerce-has-sidebar">';
};
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
    <div class="row clearfix">
        <div class="<?php echo esc_attr($col_1_class); ?>">
            <?php if ( $checkout->get_checkout_fields() ) : ?>

                <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

                <div id="customer_details">
                    <div class="checkout-col-1">
                        <?php do_action( 'woocommerce_checkout_billing' ); ?>
                    </div>

                    <div class="checkout-col-2">
                        <?php do_action( 'woocommerce_checkout_shipping' ); ?>
                    </div>
                </div>

                <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

            <?php endif; ?>
        </div>
        <div class="<?php echo esc_attr($col_2_class); ?>">
            <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
            <div id="order_review" class="woocommerce-checkout-review-order">
                <h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'spring-plant' ); ?></h3>
                <?php do_action( 'woocommerce_checkout_order_review' ); ?>
            </div>

            <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
        </div>
    </div>
</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
<?php
if(!in_array($sidebar_layout, array('', 'none'))) {
    echo '</div>';
};?>
