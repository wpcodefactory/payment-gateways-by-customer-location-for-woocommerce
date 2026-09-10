<?php
/**
 * Payment Gateways by Customer Location for WooCommerce - Postcodes Section Settings
 *
 * @version 1.8.0
 * @since   1.1.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Payment_Gateways_by_Customer_Location\Settings
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PGBCL_Settings_Postcodes' ) ) :

	/**
	 * Alg_WC_PGBCL_Settings_Postcodes class.
	 *
	 * @version 1.8.0
	 * @since   1.1.0
	 */
	class Alg_WC_PGBCL_Settings_Postcodes extends Alg_WC_PGBCL_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 1.1.0
		 * @since   1.1.0
		 */
		public function __construct() {
			$this->id   = 'postcodes';
			$this->desc = __( 'Postcodes', 'payment-gateways-by-customer-location-for-woocommerce' );
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
					'id'    => 'alg_wc_gateways_by_location_postcode_section_options',
				),
				array(
					'title'   => __( 'Gateways by postcode', 'payment-gateways-by-customer-location-for-woocommerce' ),
					'desc'    => '<strong>' . __( 'Enable section', 'payment-gateways-by-customer-location-for-woocommerce' ) . '</strong>',
					'type'    => 'checkbox',
					'id'      => 'alg_wc_gateways_by_location_postcode_section_enabled',
					'default' => 'yes',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_gateways_by_location_postcode_section_options',
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
							'id'    => "alg_wc_gateways_by_location_postcode_options[{$key}]",
							'desc'  => (
								__( 'One per line.', 'payment-gateways-by-customer-location-for-woocommerce' ) . ' ' .
								__( 'Postcodes containing wildcards (e.g., <code>CB23*</code>) and fully numeric ranges (e.g., <code>90210...99000</code>) are also supported.', 'payment-gateways-by-customer-location-for-woocommerce' )
							),
						),
						array(
							'title'    => __( 'Include postcodes', 'payment-gateways-by-customer-location-for-woocommerce' ),
							'desc_tip' => (
								__( 'Payment gateway will be available ONLY if customer is from selected postcodes.', 'payment-gateways-by-customer-location-for-woocommerce' ) . ' ' .
								__( 'If set empty - option is ignored.', 'payment-gateways-by-customer-location-for-woocommerce' )
							),
							'id'       => "alg_wc_gateways_by_location_postcode_include[{$key}]",
							'default'  => '',
							'type'     => 'textarea',
							'css'      => 'height:200px;',
						),
						array(
							'title'    => __( 'Exclude postcodes', 'payment-gateways-by-customer-location-for-woocommerce' ),
							'desc_tip' => (
								__( 'Payment gateway will NOT be available if customer is from selected postcodes.', 'payment-gateways-by-customer-location-for-woocommerce' ) . ' ' .
								__( 'If set empty - option is ignored.', 'payment-gateways-by-customer-location-for-woocommerce' )
							),
							'id'       => "alg_wc_gateways_by_location_postcode_exclude[{$key}]",
							'default'  => '',
							'type'     => 'textarea',
							'css'      => 'height:200px;',
						),
						array(
							'type' => 'sectionend',
							'id'   => "alg_wc_gateways_by_location_postcode_options[{$key}]",
						),
					)
				);
			}

			return $settings;
		}
	}

endif;

return new Alg_WC_PGBCL_Settings_Postcodes();
