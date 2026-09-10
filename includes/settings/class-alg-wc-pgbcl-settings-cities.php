<?php
/**
 * Payment Gateways by Customer Location for WooCommerce - Cities Section Settings
 *
 * @version 1.8.0
 * @since   1.5.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Payment_Gateways_by_Customer_Location\Settings
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PGBCL_Settings_Cities' ) ) :

	/**
	 * Alg_WC_PGBCL_Settings_Cities class.
	 *
	 * @version 1.8.0
	 * @since   1.5.0
	 */
	class Alg_WC_PGBCL_Settings_Cities extends Alg_WC_PGBCL_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 1.5.0
		 * @since   1.5.0
		 */
		public function __construct() {
			$this->id   = 'cities';
			$this->desc = __( 'Cities', 'payment-gateways-by-customer-location-for-woocommerce' );
			parent::__construct();
		}

		/**
		 * Get settings.
		 *
		 * @version 1.8.0
		 * @since   1.5.0
		 */
		public function get_settings() {

			$settings = array(
				array(
					'title' => $this->desc,
					'type'  => 'title',
					'id'    => 'alg_wc_gateways_by_location_city_section_options',
				),
				array(
					'title'   => __( 'Gateways by city', 'payment-gateways-by-customer-location-for-woocommerce' ),
					'desc'    => '<strong>' . __( 'Enable section', 'payment-gateways-by-customer-location-for-woocommerce' ) . '</strong>',
					'type'    => 'checkbox',
					'id'      => 'alg_wc_gateways_by_location_city_section_enabled',
					'default' => 'yes',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_gateways_by_location_city_section_options',
				),
			);

			$gateways = WC()->payment_gateways->payment_gateways();
			foreach ( $gateways as $key => $gateway ) {
				$settings = array_merge(
					$settings,
					array(
						array(
							'title' => $gateway->method_title,
							'type'  => 'title',
							'id'    => "alg_wc_gateways_by_location_city_options[{$key}]",
							'desc'  => __( 'One city per line.', 'payment-gateways-by-customer-location-for-woocommerce' ),
						),
						array(
							'title'    => __( 'Include cities', 'payment-gateways-by-customer-location-for-woocommerce' ),
							'desc_tip' => (
								__( 'Payment gateway will be available ONLY if customer is from selected cities.', 'payment-gateways-by-customer-location-for-woocommerce' ) . ' ' .
								__( 'If set empty - option is ignored.', 'payment-gateways-by-customer-location-for-woocommerce' )
							),
							'id'       => "alg_wc_gateways_by_location_city_include[{$key}]",
							'default'  => '',
							'type'     => 'textarea',
							'css'      => 'height:200px;',
						),
						array(
							'title'    => __( 'Exclude cities', 'payment-gateways-by-customer-location-for-woocommerce' ),
							'desc_tip' => (
								__( 'Payment gateway will NOT be available if customer is from selected cities.', 'payment-gateways-by-customer-location-for-woocommerce' ) . ' ' .
								__( 'If set empty - option is ignored.', 'payment-gateways-by-customer-location-for-woocommerce' )
							),
							'id'       => "alg_wc_gateways_by_location_city_exclude[{$key}]",
							'default'  => '',
							'type'     => 'textarea',
							'css'      => 'height:200px;',
						),
						array(
							'type' => 'sectionend',
							'id'   => "alg_wc_gateways_by_location_city_options[{$key}]",
						),
					)
				);
			}

			return $settings;
		}
	}

endif;

return new Alg_WC_PGBCL_Settings_Cities();
