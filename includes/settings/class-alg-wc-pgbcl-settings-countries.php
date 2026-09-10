<?php
/**
 * Payment Gateways by Customer Location for WooCommerce - Countries Section Settings
 *
 * @version 1.8.0
 * @since   1.1.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Payment_Gateways_by_Customer_Location\Settings
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PGBCL_Settings_Countries' ) ) :

	/**
	 * Alg_WC_PGBCL_Settings_Countries class.
	 *
	 * @version 1.8.0
	 * @since   1.1.0
	 */
	class Alg_WC_PGBCL_Settings_Countries extends Alg_WC_PGBCL_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 1.1.0
		 * @since   1.1.0
		 */
		public function __construct() {
			$this->id   = 'countries';
			$this->desc = __( 'Countries', 'payment-gateways-by-customer-location-for-woocommerce' );
			parent::__construct();
		}

		/**
		 * Get settings.
		 *
		 * @version 1.8.0
		 * @since   1.1.0
		 */
		public function get_settings() {

			$settings = array(
				array(
					'title' => $this->desc,
					'type'  => 'title',
					'id'    => 'alg_wc_gateways_by_location_country_section_options',
				),
				array(
					'title'   => __( 'Gateways by country', 'payment-gateways-by-customer-location-for-woocommerce' ),
					'desc'    => '<strong>' . __( 'Enable section', 'payment-gateways-by-customer-location-for-woocommerce' ) . '</strong>',
					'type'    => 'checkbox',
					'id'      => 'alg_wc_gateways_by_location_country_section_enabled',
					'default' => 'yes',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_gateways_by_location_country_section_options',
				),
			);

			$countries = alg_wc_gateways_by_location_get_countries();
			$gateways  = WC()->payment_gateways->payment_gateways();
			foreach ( $gateways as $key => $gateway ) {
				$settings = array_merge(
					$settings,
					array(
						array(
							'title' => $gateway->method_title,
							'type'  => 'title',
							'id'    => "alg_wc_gateways_by_location_country_options[{$key}]",
						),
						array(
							'title'             => __( 'Include countries', 'payment-gateways-by-customer-location-for-woocommerce' ),
							'desc_tip'          => (
								__( 'Payment gateway will be available ONLY if customer is from selected countries.', 'payment-gateways-by-customer-location-for-woocommerce' ) . ' ' .
								__( 'If set empty - option is ignored.', 'payment-gateways-by-customer-location-for-woocommerce' )
							),
							'id'                => "alg_wc_gateways_by_location_country_include[{$key}]",
							'default'           => array(),
							'type'              => 'multiselect',
							'class'             => 'chosen_select',
							'options'           => $countries,
							'custom_attributes' => array( 'data-placeholder' => __( 'Select countries', 'payment-gateways-by-customer-location-for-woocommerce' ) ),
						),
						array(
							'title'             => __( 'Exclude countries', 'payment-gateways-by-customer-location-for-woocommerce' ),
							'desc_tip'          => (
								__( 'Payment gateway will NOT be available if customer is from selected countries.', 'payment-gateways-by-customer-location-for-woocommerce' ) . ' ' .
								__( 'If set empty - option is ignored.', 'payment-gateways-by-customer-location-for-woocommerce' )
							),
							'id'                => "alg_wc_gateways_by_location_country_exclude[{$key}]",
							'default'           => array(),
							'type'              => 'multiselect',
							'class'             => 'chosen_select',
							'options'           => $countries,
							'custom_attributes' => array( 'data-placeholder' => __( 'Select countries', 'payment-gateways-by-customer-location-for-woocommerce' ) ),
						),
						array(
							'type' => 'sectionend',
							'id'   => "alg_wc_gateways_by_location_country_options[{$key}]",
						),
					)
				);
			}

			return $settings;
		}
	}

endif;

return new Alg_WC_PGBCL_Settings_Countries();
