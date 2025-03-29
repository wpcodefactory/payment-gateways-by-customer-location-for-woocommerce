<?php
/**
 * Payment Gateways by Customer Location for WooCommerce - Main Class
 *
 * @version 1.7.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Payment_Gateways_by_Customer_Location' ) ) :

final class Alg_WC_Payment_Gateways_by_Customer_Location {

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	public $version = ALG_WC_PGBCL_VERSION;

	/**
	 * core.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 */
	public $core;

	/**
	 * @var   Alg_WC_Payment_Gateways_by_Customer_Location The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main Alg_WC_Payment_Gateways_by_Customer_Location Instance.
	 *
	 * Ensures only one instance of Alg_WC_Payment_Gateways_by_Customer_Location is loaded or can be loaded.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @static
	 * @return  Alg_WC_Payment_Gateways_by_Customer_Location - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Alg_WC_Payment_Gateways_by_Customer_Location Constructor.
	 *
	 * @version 1.7.0
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	function __construct() {

		// Check for active WooCommerce plugin
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		// Load libs
		if ( is_admin() ) {
			require_once plugin_dir_path( ALG_WC_PGBCL_FILE ) . 'vendor/autoload.php';
		}

		// Set up localisation
		add_action( 'init', array( $this, 'localize' ) );

		// Declare compatibility with custom order tables for WooCommerce
		add_action( 'before_woocommerce_init', array( $this, 'wc_declare_compatibility' ) );

		// Pro
		if ( 'payment-gateways-by-customer-location-for-woocommerce-pro.php' === basename( ALG_WC_PGBCL_FILE ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'pro/class-alg-wc-pgbcl-pro.php';
		}

		// Include required files
		$this->includes();

		// Admin
		if ( is_admin() ) {
			$this->admin();
		}
	}

	/**
	 * localize.
	 *
	 * @version 1.4.0
	 * @since   1.3.0
	 */
	function localize() {
		load_plugin_textdomain(
			'payment-gateways-by-customer-location-for-woocommerce',
			false,
			dirname( plugin_basename( ALG_WC_PGBCL_FILE ) ) . '/langs/'
		);
	}

	/**
	 * wc_declare_compatibility.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 *
	 * @see     https://github.com/woocommerce/woocommerce/wiki/High-Performance-Order-Storage-Upgrade-Recipe-Book#declaring-extension-incompatibility
	 */
	function wc_declare_compatibility() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			$files = (
				defined( 'ALG_WC_PGBCL_FILE_FREE' ) ?
				array( ALG_WC_PGBCL_FILE, ALG_WC_PGBCL_FILE_FREE ) :
				array( ALG_WC_PGBCL_FILE )
			);
			foreach ( $files as $file ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', $file, true );
			}
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @version 1.7.0
	 * @since   1.0.0
	 */
	function includes() {
		// Frontend functions
		require_once plugin_dir_path( __FILE__ ) . 'functions/alg-wc-pgbcl-functions-frontend.php';
		// Core
		$this->core = require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-pgbcl-core.php';
	}

	/**
	 * admin.
	 *
	 * @version 1.7.0
	 * @since   1.1.0
	 */
	function admin() {

		// Action links
		add_filter( 'plugin_action_links_' . plugin_basename( ALG_WC_PGBCL_FILE ), array( $this, 'action_links' ) );

		// "Recommendations" page
		$this->add_cross_selling_library();

		// WC Settings tab as WPFactory submenu item
		$this->move_wc_settings_tab_to_wpfactory_menu();

		// Admin functions
		require_once plugin_dir_path( __FILE__ ) . 'functions/alg-wc-pgbcl-functions-admin.php';

		// Settings
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );

		// Version update
		if ( get_option( 'alg_wc_gateways_by_location_version', '' ) !== $this->version ) {
			add_action( 'admin_init', array( $this, 'version_updated' ) );
		}

	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @version 1.7.0
	 * @since   1.0.0
	 *
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$custom_links = array();

		$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_gateways_by_location' ) . '">' . __( 'Settings', 'payment-gateways-by-customer-location-for-woocommerce' ) . '</a>';

		if ( 'payment-gateways-by-customer-location-for-woocommerce.php' === basename( ALG_WC_PGBCL_FILE ) ) {
			$custom_links[] = '<a target="_blank" style="font-weight: bold; color: green;" href="https://wpfactory.com/item/payment-gateways-by-customer-location-for-woocommerce/">' .
				__( 'Go Pro', 'payment-gateways-by-customer-location-for-woocommerce' ) . '</a>';
		}

		return array_merge( $custom_links, $links );
	}

	/**
	 * add_cross_selling_library.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	function add_cross_selling_library() {

		if ( ! class_exists( '\WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling' ) ) {
			return;
		}

		$cross_selling = new \WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling();
		$cross_selling->setup( array( 'plugin_file_path' => ALG_WC_PGBCL_FILE ) );
		$cross_selling->init();

	}

	/**
	 * move_wc_settings_tab_to_wpfactory_menu.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	function move_wc_settings_tab_to_wpfactory_menu() {

		if ( ! class_exists( '\WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu' ) ) {
			return;
		}

		$wpfactory_admin_menu = \WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu::get_instance();

		if ( ! method_exists( $wpfactory_admin_menu, 'move_wc_settings_tab_to_wpfactory_menu' ) ) {
			return;
		}

		$wpfactory_admin_menu->move_wc_settings_tab_to_wpfactory_menu( array(
			'wc_settings_tab_id' => 'alg_wc_gateways_by_location',
			'menu_title'         => __( 'Payment Gateways by Customer Location', 'payment-gateways-by-customer-location-for-woocommerce' ),
			'page_title'         => __( 'Payment Gateways by Customer Location', 'payment-gateways-by-customer-location-for-woocommerce' ),
		) );

	}

	/**
	 * Add Payment Gateways by Customer Location settings tab to WooCommerce settings.
	 *
	 * @version 1.7.0
	 * @since   1.0.0
	 */
	function add_woocommerce_settings_tab( $settings ) {
		$settings[] = require_once plugin_dir_path( __FILE__ ) . 'settings/class-alg-wc-settings-pgbcl.php';
		return $settings;
	}

	/**
	 * version_updated.
	 *
	 * @version 1.1.0
	 * @since   1.1.0
	 */
	function version_updated() {
		// Handle deprecated options
		if (
			version_compare( get_option( 'alg_wc_gateways_by_location_version', '' ), '1.1.0', '<' ) &&
			function_exists( 'WC' ) &&
			( $gateways = WC()->payment_gateways->payment_gateways() )
		) {
			foreach ( $gateways as $key => $gateway ) {
				foreach ( array( 'country', 'state', 'postcode' ) as $type ) {
					foreach ( array( 'include', 'exclude' ) as $incl_or_excl ) {
						if ( false !== ( $old_value = get_option( 'alg_wc_gateways_by_location_' . $type . '_' . $incl_or_excl . '_' . $key, false ) ) ) {
							delete_option( 'alg_wc_gateways_by_location_' . $type . '_' . $incl_or_excl . '_' . $key );
							$new_value = get_option( 'alg_wc_gateways_by_location_' . $type . '_' . $incl_or_excl, array() );
							$new_value[ $key ] = $old_value;
							update_option( 'alg_wc_gateways_by_location_' . $type . '_' . $incl_or_excl, $new_value );
						}
					}
				}
			}
		}
		// Update version
		update_option( 'alg_wc_gateways_by_location_version', $this->version );
	}

	/**
	 * Get the plugin url.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 *
	 * @return  string
	 */
	function plugin_url() {
		return untrailingslashit( plugin_dir_url( ALG_WC_PGBCL_FILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 *
	 * @return  string
	 */
	function plugin_path() {
		return untrailingslashit( plugin_dir_path( ALG_WC_PGBCL_FILE ) );
	}

}

endif;
