<?php
/**
 * Plugin Name: Payment Gateways by Customer Location for WooCommerce
 * Plugin URI: https://wpfactory.com/item/payment-gateways-by-customer-location-for-woocommerce/
 * Description: Set countries, states, cities or postcodes to include/exclude for WooCommerce payment gateways to show up.
 * Version: 1.8.0
 * Author: WPFactory
 * Author URI: https://wpfactory.com
 * Requires at least: 4.4
 * Text Domain: payment-gateways-by-customer-location-for-woocommerce
 * Domain Path: /langs
 * WC tested up to: 11.1
 * Requires Plugins: woocommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WPFactory\WC_Payment_Gateways_by_Customer_Location
 */

defined( 'ABSPATH' ) || exit;

if ( 'payment-gateways-by-customer-location-for-woocommerce.php' === basename( __FILE__ ) ) {
	if ( ! function_exists( 'alg_wc_pgbcl_is_pro_activated' ) ) {
		/**
		 * Check if Pro plugin version is activated.
		 *
		 * @version 1.8.0
		 * @since   1.4.0
		 */
		function alg_wc_pgbcl_is_pro_activated() {
			$plugin = 'payment-gateways-by-customer-location-for-woocommerce-pro/payment-gateways-by-customer-location-for-woocommerce-pro.php';
			return (
				in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ||
				(
					is_multisite() &&
					array_key_exists( $plugin, (array) get_site_option( 'active_sitewide_plugins', array() ) )
				)
			);
		}
	}
	if ( alg_wc_pgbcl_is_pro_activated() ) {
		defined( 'ALG_WC_PGBCL_FILE_FREE' ) || define( 'ALG_WC_PGBCL_FILE_FREE', __FILE__ );
		return;
	}
}

/**
 * Plugin version.
 *
 * @version 1.0.0
 * @since   1.0.0
 */
defined( 'ALG_WC_PGBCL_VERSION' ) || define( 'ALG_WC_PGBCL_VERSION', '1.8.0' );

/**
 * Plugin file.
 *
 * @version 1.0.0
 * @since   1.0.0
 */
defined( 'ALG_WC_PGBCL_FILE' ) || define( 'ALG_WC_PGBCL_FILE', __FILE__ );

/**
 * Main plugin class.
 *
 * @version 1.0.0
 * @since   1.0.0
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-alg-wc-pgbcl.php';

if ( ! function_exists( 'alg_wc_gateways_by_location' ) ) {
	/**
	 * Returns the main instance of Alg_WC_PGBCL to prevent the need to use globals.
	 *
	 * @version 1.8.0
	 * @since   1.0.0
	 */
	function alg_wc_gateways_by_location() {
		return Alg_WC_PGBCL::instance();
	}
}

/**
 * Initialize the plugin.
 *
 * @version 1.0.0
 * @since   1.0.0
 */
add_action( 'plugins_loaded', 'alg_wc_gateways_by_location' );
