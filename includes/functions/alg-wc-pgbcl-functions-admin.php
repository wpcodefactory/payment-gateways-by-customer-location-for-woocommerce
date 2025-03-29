<?php
/**
 * Payment Gateways by Customer Location for WooCommerce - Functions - Admin
 *
 * @version 1.6.2
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'alg_wc_gateways_by_location_get_states' ) ) {
	/**
	 * alg_wc_gateways_by_location_get_states.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_wc_gateways_by_location_get_states() {
		$base_country = WC()->countries->get_base_country();
		$states       = WC()->countries->get_states( $base_country );
		return ( isset( $states ) && ! empty( $states ) ? $states : array() );
	}
}

if ( ! function_exists( 'alg_wc_gateways_by_location_get_countries' ) ) {
	/**
	 * alg_wc_gateways_by_location_get_countries.
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
		if ( apply_filters( 'woocommerce_sort_countries', true ) ) {
			wc_asort_by_locale( $countries );
		}
		return $countries;
	}
}
