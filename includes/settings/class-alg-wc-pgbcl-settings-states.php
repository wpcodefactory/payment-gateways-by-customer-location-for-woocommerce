<?php
/**
 * Payment Gateways by Customer Location for WooCommerce - States Section Settings
 *
 * @version 1.8.0
 * @since   1.1.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Payment_Gateways_by_Customer_Location\Settings
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PGBCL_Settings_States' ) ) :

	/**
	 * Alg_WC_PGBCL_Settings_States class.
	 *
	 * @version 1.8.0
	 * @since   1.1.0
	 */
	class Alg_WC_PGBCL_Settings_States extends Alg_WC_PGBCL_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 1.1.0
		 * @since   1.1.0
		 */
		public function __construct() {
			$this->id   = 'states';
			$this->desc = __( 'States', 'payment-gateways-by-customer-location-for-woocommerce' );
			parent::__construct();
		}

		/**
		 * Get settings.
		 *
		 * @version 1.8.0
		 * @since   1.1.0
		 *
		 * @todo (dev) Not only "base country" states.
		 */
		public function get_settings() {

			$settings = array(
				array(
					'title' => $this->desc,
					'type'  => 'title',
					'id'    => 'alg_wc_gateways_by_location_state_section_options',
				),
				array(
					'title'   => __( 'Gateways by state', 'payment-gateways-by-customer-location-for-woocommerce' ),
					'desc'    => '<strong>' . __( 'Enable section', 'payment-gateways-by-customer-location-for-woocommerce' ) . '</strong>',
					'type'    => 'checkbox',
					'id'      => 'alg_wc_gateways_by_location_state_section_enabled',
					'default' => 'yes',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_gateways_by_location_state_section_options',
				),
			);

			$states   = alg_wc_gateways_by_location_get_states();
			$gateways = WC()->payment_gateways->payment_gateways();
			foreach ( $gateways as $key => $gateway ) {
				$settings = array_merge(
					$settings,
					array(
						array(
							'title' => $gateway->method_title,
							'type'  => 'title',
							'id'    => "alg_wc_gateways_by_location_state_options[{$key}]",
							'desc'  => __( 'Base country states only.', 'payment-gateways-by-customer-location-for-woocommerce' ),
						),
						array(
							'title'             => __( 'Include states', 'payment-gateways-by-customer-location-for-woocommerce' ),
							'desc_tip'          => (
								__( 'Payment gateway will be available ONLY if customer is from selected states.', 'payment-gateways-by-customer-location-for-woocommerce' ) . ' ' .
								__( 'If set empty - option is ignored.', 'payment-gateways-by-customer-location-for-woocommerce' )
							),
							'id'                => "alg_wc_gateways_by_location_state_include[{$key}]",
							'default'           => array(),
							'type'              => 'multiselect',
							'class'             => 'chosen_select',
							'options'           => $states,
							'custom_attributes' => array( 'data-placeholder' => __( 'Select states', 'payment-gateways-by-customer-location-for-woocommerce' ) ),
						),
						array(
							'title'             => __( 'Exclude states', 'payment-gateways-by-customer-location-for-woocommerce' ),
							'desc_tip'          => (
								__( 'Payment gateway will NOT be available if customer is from selected states.', 'payment-gateways-by-customer-location-for-woocommerce' ) . ' ' .
								__( 'If set empty - option is ignored.', 'payment-gateways-by-customer-location-for-woocommerce' )
							),
							'id'                => "alg_wc_gateways_by_location_state_exclude[{$key}]",
							'default'           => array(),
							'type'              => 'multiselect',
							'class'             => 'chosen_select',
							'options'           => $states,
							'custom_attributes' => array( 'data-placeholder' => __( 'Select states', 'payment-gateways-by-customer-location-for-woocommerce' ) ),
						),
						array(
							'type' => 'sectionend',
							'id'   => "alg_wc_gateways_by_location_state_options[{$key}]",
						),
					)
				);
			}

			return $settings;
		}
	}

endif;

return new Alg_WC_PGBCL_Settings_States();
