<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'WC_Subscriptions' ) || !function_exists( 'wcs_get_available_time_periods' ) || !function_exists( 'wcs_get_subscription_period_interval_strings' ) ) {
	return;
}

if ( ! class_exists( 'PWBE_WooCommerce_Subscriptions' ) ) :

final class PWBE_WooCommerce_Subscriptions {

	function __construct() {
		add_filter( 'pwbe_select_options', array( $this, 'pwbe_select_options' ) );
		add_filter( 'pwbe_product_columns', array( $this, 'pwbe_product_columns' ) );
	}

	function pwbe_select_options( $select_options ) {
		foreach ( wcs_get_available_time_periods() as $key => $value ) {
			$select_options['_subscription_trial_period'][ $key ]['name'] = $value;
			$select_options['_subscription_trial_period'][ $key ]['visibility'] = 'both';

			$select_options['_subscription_period'][ $key ]['name'] = $value;
			$select_options['_subscription_period'][ $key ]['visibility'] = 'both';
		}

		foreach ( wcs_get_subscription_period_interval_strings() as $key => $value ) {
			$select_options['_subscription_period_interval'][ $key ]['name'] = $value;
			$select_options['_subscription_period_interval'][ $key ]['visibility'] = 'both';
		}

	    return $select_options;
	}

	function pwbe_product_columns( $columns ) {
		$columns[] = array(
			'name' => sprintf( __( 'Subscription price (%s)', 'woocommerce-subscriptions' ), get_woocommerce_currency_symbol() ),
			'type' => 'number',
			'table' => 'meta',
			'field' => '_subscription_price',
			'readonly' => 'false',
			'visibility' => 'both',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		$columns[] = array(
			'name' => __( 'Subscription period interval', 'woocommerce-subscriptions' ),
			'type' => 'select',
			'table' => 'meta',
			'field' => '_subscription_period_interval',
			'readonly' => 'false',
			'visibility' => 'both',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		$columns[] = array(
			'name' => __( 'Subscription period', 'woocommerce-subscriptions' ),
			'type' => 'select',
			'table' => 'meta',
			'field' => '_subscription_period',
			'readonly' => 'false',
			'visibility' => 'both',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		$columns[] = array(
			'name' => __( 'Subscription length', 'woocommerce-subscriptions' ),
			'type' => 'number',
			'table' => 'meta',
			'field' => '_subscription_length',
			'readonly' => 'false',
			'visibility' => 'both',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		$columns[] = array(
			'name' => sprintf( __( 'Sign-up fee (%s)', 'woocommerce-subscriptions' ), get_woocommerce_currency_symbol() ),
			'type' => 'currency',
			'table' => 'meta',
			'field' => '_subscription_sign_up_fee',
			'readonly' => 'false',
			'visibility' => 'both',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		$columns[] = array(
			'name' => __( 'Free trial', 'woocommerce-subscriptions' ),
			'type' => 'number',
			'table' => 'meta',
			'field' => '_subscription_trial_length',
			'readonly' => 'false',
			'visibility' => 'both',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		$columns[] = array(
			'name' => __( 'Free trial period', 'woocommerce-subscriptions' ),
			'type' => 'select',
			'table' => 'meta',
			'field' => '_subscription_trial_period',
			'readonly' => 'false',
			'visibility' => 'both',
			'sortable' => 'true',
			'views' => array( 'all' )
		);

		return $columns;
	}
}

new PWBE_WooCommerce_Subscriptions();

endif;

?>