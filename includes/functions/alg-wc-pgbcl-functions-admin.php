<?php
/**
 * Payment Gateways by Customer Location for WooCommerce - Functions - Admin
 *
 * @version 1.8.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Payment_Gateways_by_Customer_Location\Functions
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'alg_wc_gateways_by_location_get_states' ) ) {
	/**
	 * Get states.
	 *
	 * @version 1.8.0
	 * @since   1.0.0
	 */
	function alg_wc_gateways_by_location_get_states() {
		$base_country = WC()->countries->get_base_country();
		$states       = WC()->countries->get_states( $base_country );
		return ( ! empty( $states ) ? $states : array() );
	}
}

if ( ! function_exists( 'alg_wc_gateways_by_location_get_countries' ) ) {
	/**
	 * Get countries.
	 *
	 * @version 1.6.2
	 * @since   1.0.0
	 */
	function alg_wc_gateways_by_location_get_countries() {
		$countries = array_merge(
			WC()->countries->get_countries(),
			array(
				'AN' => __( 'Netherlands Antilles', 'payment-gateways-by-customer-location-for-woocommerce' ),
				'EU' => __( 'European Union', 'payment-gateways-by-customer-location-for-woocommerce' ),
			)
		);
		if ( apply_filters( 'woocommerce_sort_countries', true ) ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			wc_asort_by_locale( $countries );
		}
		return $countries;
	}
}
