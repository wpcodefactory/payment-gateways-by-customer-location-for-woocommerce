<?php
/**
 * Payment Gateways by Customer Location for WooCommerce - Core Class
 *
 * @version 1.8.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Payment_Gateways_by_Customer_Location
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PGBCL_Core' ) ) :

	/**
	 * Alg_WC_PGBCL_Core class.
	 *
	 * @version 1.8.0
	 * @since   1.0.0
	 */
	class Alg_WC_PGBCL_Core {

		/**
		 * Force JS checkout update.
		 *
		 * @version 1.6.0
		 * @since   1.6.0
		 *
		 * @var array
		 */
		public $force_js_checkout_update;

		/**
		 * Options data.
		 *
		 * @version 1.6.0
		 * @since   1.6.0
		 *
		 * @var array
		 */
		public $options_data;

		/**
		 * Constructor.
		 *
		 * @version 1.7.1
		 * @since   1.0.0
		 */
		public function __construct() {
			$this->force_js_checkout_update = get_option(
				'alg_wc_gateways_by_location_force_js_checkout_update',
				array()
			);
			if ( ! empty( $this->force_js_checkout_update ) ) {
				add_action(
					'wp_enqueue_scripts',
					array( $this, 'enqueue_scripts' )
				);
			}

			add_filter(
				'woocommerce_available_payment_gateways',
				array( $this, 'available_payment_gateways' ),
				PHP_INT_MAX
			);
		}

		/**
		 * Enqueue scripts.
		 *
		 * @version 1.8.0
		 * @since   1.1.0
		 */
		public function enqueue_scripts() {
			$min = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ? '' : '.min' );
			wp_enqueue_script(
				'alg-wc-pgbcl-js',
				alg_wc_gateways_by_location()->plugin_url() . '/assets/js/alg-wc-pgbcl' . $min . '.js',
				array( 'jquery' ),
				alg_wc_gateways_by_location()->version,
				true
			);
			wp_localize_script(
				'alg-wc-pgbcl-js',
				'alg_wc_pgbcl',
				$this->force_js_checkout_update
			);
		}

		/**
		 * Check data.
		 *
		 * @version 1.8.0
		 * @since   1.1.0
		 *
		 * @param string $type     The type of location data ('country', 'state', 'city', 'postcode').
		 * @param mixed  $needle   The value to check.
		 * @param array  $haystack The array of values to check against.
		 *
		 * @return bool True if the needle is found in the haystack, false otherwise.
		 */
		public function check_data( $type, $needle, $haystack ) {
			switch ( $type ) {
				case 'country':
				case 'state':
				case 'city':
					return in_array( $needle, $haystack, true );
				case 'postcode':
					return alg_wc_gateways_by_location_check_postcode( $needle, $haystack );
			}
		}

		/**
		 * Clean values.
		 *
		 * @version 1.5.0
		 * @since   1.1.0
		 *
		 * @param string $value The value to clean.
		 *
		 * @return array The cleaned and filtered array of values.
		 */
		public function clean_values( $value ) {
			return array_filter(
				array_map(
					'strtoupper',
					array_map(
						'wc_clean',
						explode( PHP_EOL, $value )
					)
				)
			);
		}

		/**
		 * Available payment gateways.
		 *
		 * @version 1.8.0
		 * @since   1.0.0
		 *
		 * @param array $available_gateways The list of available payment gateways.
		 *
		 * @return array The filtered list of available payment gateways.
		 *
		 * @todo (dev) Apply `alg_wc_gateways_by_location` filter.
		 * @todo (dev) Add option to detect customer's country and state by current `$_REQUEST` (as it is now done with postcodes).
		 * @todo (feature) Add more locations options.
		 */
		public function available_payment_gateways( $available_gateways ) {
			// Prepare options data.
			if ( ! isset( $this->options_data ) ) {
				$this->options_data = array();
				foreach ( array( 'country', 'state', 'city', 'postcode' ) as $type ) {
					if ( 'no' === get_option( 'alg_wc_gateways_by_location_' . $type . '_section_enabled', 'yes' ) ) {
						continue;
					}
					foreach ( array( 'include', 'exclude' ) as $incl_or_excl ) {
						$value = get_option( 'alg_wc_gateways_by_location_' . $type . '_' . $incl_or_excl, array() );
						if ( ! empty( $value ) ) {
							if ( 'country' === $type ) {
								$value = array_map( 'alg_wc_gateways_by_location_maybe_add_european_union_countries', $value );
							} elseif ( in_array( $type, array( 'city', 'postcode' ), true ) ) {
								$value = array_map( array( $this, 'clean_values' ), $value );
							}
						}
						$this->options_data[ $type ][ $incl_or_excl ] = $value;
					}
				}
			}

			// Prepare customer data.
			foreach ( array( 'country', 'state', 'city', 'postcode' ) as $type ) {
				if ( 'no' === get_option( 'alg_wc_gateways_by_location_' . $type . '_section_enabled', 'yes' ) ) {
					continue;
				}
				$customer_data[ $type ] = alg_wc_gateways_by_location_get_location( $type );
			}

			// Check gateways.
			foreach ( $available_gateways as $key => $gateway ) {
				foreach ( array( 'country', 'state', 'city', 'postcode' ) as $type ) {
					if ( 'no' === get_option( 'alg_wc_gateways_by_location_' . $type . '_section_enabled', 'yes' ) ) {
						continue;
					}
					if ( '' !== $customer_data[ $type ] ) {
						if (
							(
								! empty( $this->options_data[ $type ]['include'][ $key ] ) &&
								! $this->check_data(
									$type,
									$customer_data[ $type ],
									$this->options_data[ $type ]['include'][ $key ]
								)
							) ||
							(
								! empty( $this->options_data[ $type ]['exclude'][ $key ] ) &&
								$this->check_data(
									$type,
									$customer_data[ $type ],
									$this->options_data[ $type ]['exclude'][ $key ]
								)
							)
						) {
							unset( $available_gateways[ $key ] );
						}
					}
				}
			}

			// Return result.
			return $available_gateways;
		}
	}

endif;

return new Alg_WC_PGBCL_Core();
