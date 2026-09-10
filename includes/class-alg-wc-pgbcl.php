<?php
/**
 * Payment Gateways by Customer Location for WooCommerce - Main Class
 *
 * @version 1.8.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Payment_Gateways_by_Customer_Location
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PGBCL' ) ) :

	/**
	 * Alg_WC_PGBCL class.
	 *
	 * @version 1.8.0
	 * @since   1.0.0
	 */
	final class Alg_WC_PGBCL {

		/**
		 * Plugin version.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @var string
		 */
		public $version = ALG_WC_PGBCL_VERSION;

		/**
		 * Core.
		 *
		 * @version 1.6.0
		 * @since   1.6.0
		 *
		 * @var Alg_WC_PGBCL_Core
		 */
		public $core;

		/**
		 * Single instance of the class.
		 *
		 * @version 1.8.0
		 * @since   1.0.0
		 *
		 * @var Alg_WC_PGBCL
		 */
		protected static $instance = null;

		/**
		 * Main Alg_WC_PGBCL Instance.
		 *
		 * Ensures only one instance of Alg_WC_PGBCL is loaded or can be loaded.
		 *
		 * @version 1.8.0
		 * @since   1.0.0
		 *
		 * @static
		 *
		 * @return Alg_WC_PGBCL
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Alg_WC_PGBCL Constructor.
		 *
		 * @version 1.8.0
		 * @since   1.0.0
		 *
		 * @access public
		 */
		public function __construct() {
			// Check for active WooCommerce plugin.
			if ( ! function_exists( 'WC' ) ) {
				return;
			}

			// Load libs.
			if ( is_admin() ) {
				require_once plugin_dir_path( ALG_WC_PGBCL_FILE ) . 'vendor/autoload.php';
			}

			// Declare compatibility with custom order tables for WooCommerce.
			add_action( 'before_woocommerce_init', array( $this, 'wc_declare_compatibility' ) );

			// Pro.
			if ( 'payment-gateways-by-customer-location-for-woocommerce-pro.php' === basename( ALG_WC_PGBCL_FILE ) ) {
				require_once plugin_dir_path( __FILE__ ) . 'pro/class-alg-wc-pgbcl-pro.php';
			}

			// Include required files.
			$this->includes();

			// Admin.
			if ( is_admin() ) {
				$this->admin();
			}
		}

		/**
		 * Declare compatibility with WooCommerce custom order tables.
		 *
		 * @version 1.6.0
		 * @since   1.6.0
		 *
		 * @see https://developer.woocommerce.com/docs/features/high-performance-order-storage/recipe-book/
		 */
		public function wc_declare_compatibility() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				$files = (
					defined( 'ALG_WC_PGBCL_FILE_FREE' ) ?
					array( ALG_WC_PGBCL_FILE, ALG_WC_PGBCL_FILE_FREE ) :
					array( ALG_WC_PGBCL_FILE )
				);
				foreach ( $files as $file ) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
						'custom_order_tables',
						$file,
						true
					);
				}
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @version 1.7.0
		 * @since   1.0.0
		 */
		public function includes() {
			// Frontend functions.
			require_once plugin_dir_path( __FILE__ ) . 'functions/alg-wc-pgbcl-functions-frontend.php';
			// Core.
			$this->core = require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-pgbcl-core.php';
		}

		/**
		 * Admin.
		 *
		 * @version 1.7.1
		 * @since   1.1.0
		 */
		public function admin() {
			// Action links.
			add_filter( 'plugin_action_links_' . plugin_basename( ALG_WC_PGBCL_FILE ), array( $this, 'action_links' ) );

			// "Recommendations" page.
			add_action( 'init', array( $this, 'add_cross_selling_library' ) );

			// WC Settings tab as WPFactory submenu item.
			add_action( 'init', array( $this, 'move_wc_settings_tab_to_wpfactory_menu' ) );

			// Admin functions.
			require_once plugin_dir_path( __FILE__ ) . 'functions/alg-wc-pgbcl-functions-admin.php';

			// Settings.
			add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );

			// Version update.
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
		 * @param mixed $links Plugin action links.
		 *
		 * @return array
		 */
		public function action_links( $links ) {
			$custom_links = array();

			$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_gateways_by_location' ) . '">' .
				__( 'Settings', 'payment-gateways-by-customer-location-for-woocommerce' ) .
			'</a>';

			if ( 'payment-gateways-by-customer-location-for-woocommerce.php' === basename( ALG_WC_PGBCL_FILE ) ) {
				$custom_links[] = '<a target="_blank" style="font-weight: bold; color: green;" href="https://wpfactory.com/item/payment-gateways-by-customer-location-for-woocommerce/">' .
					__( 'Go Pro', 'payment-gateways-by-customer-location-for-woocommerce' ) .
				'</a>';
			}

			return array_merge( $custom_links, $links );
		}

		/**
		 * Add cross selling library.
		 *
		 * @version 1.7.0
		 * @since   1.7.0
		 */
		public function add_cross_selling_library() {
			if ( ! class_exists( '\WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling' ) ) {
				return;
			}

			$cross_selling = new \WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling();
			$cross_selling->setup( array( 'plugin_file_path' => ALG_WC_PGBCL_FILE ) );
			$cross_selling->init();
		}

		/**
		 * Move WC settings tab to WPFactory menu.
		 *
		 * @version 1.7.1
		 * @since   1.7.0
		 */
		public function move_wc_settings_tab_to_wpfactory_menu() {

			if ( ! class_exists( '\WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu' ) ) {
				return;
			}

			$wpfactory_admin_menu = \WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu::get_instance();

			if ( ! method_exists( $wpfactory_admin_menu, 'move_wc_settings_tab_to_wpfactory_menu' ) ) {
				return;
			}

			$wpfactory_admin_menu->move_wc_settings_tab_to_wpfactory_menu(
				array(
					'wc_settings_tab_id' => 'alg_wc_gateways_by_location',
					'menu_title'         => __( 'Payment Gateways by Customer Location', 'payment-gateways-by-customer-location-for-woocommerce' ),
					'page_title'         => __( 'WooCommerce Conditional Payment Methods by Location', 'payment-gateways-by-customer-location-for-woocommerce' ),
					'plugin_icon'        => array(
						'get_url_method'    => 'wporg_plugins_api',
						'wporg_plugin_slug' => 'payment-gateways-by-customer-location-for-woocommerce',
					),
				)
			);
		}

		/**
		 * Add Payment Gateways by Customer Location settings tab to WooCommerce settings.
		 *
		 * @version 1.7.0
		 * @since   1.0.0
		 *
		 * @param array $settings WooCommerce settings array.
		 *
		 * @return array Modified WooCommerce settings array.
		 */
		public function add_woocommerce_settings_tab( $settings ) {
			$settings[] = require_once plugin_dir_path( __FILE__ ) . 'settings/class-alg-wc-settings-pgbcl.php';
			return $settings;
		}

		/**
		 * Handle plugin version updates.
		 *
		 * @version 1.8.0
		 * @since   1.1.0
		 */
		public function version_updated() {
			// Handle deprecated options.
			if ( version_compare( get_option( 'alg_wc_gateways_by_location_version', '' ), '1.1.0', '<' ) ) {
				$gateways = WC()->payment_gateways->payment_gateways();
				if ( $gateways ) {
					foreach ( $gateways as $key => $gateway ) {
						foreach ( array( 'country', 'state', 'postcode' ) as $type ) {
							foreach ( array( 'include', 'exclude' ) as $incl_or_excl ) {
								$option_name = 'alg_wc_gateways_by_location_' . $type . '_' . $incl_or_excl;
								$old_value   = get_option( $option_name . '_' . $key, false );
								if ( false !== $old_value ) {
									delete_option( $option_name . '_' . $key );
									$new_value         = get_option( $option_name, array() );
									$new_value[ $key ] = $old_value;
									update_option( $option_name, $new_value );
								}
							}
						}
					}
				}
			}

			// Update version.
			update_option( 'alg_wc_gateways_by_location_version', $this->version );
		}

		/**
		 * Get the plugin url.
		 *
		 * @version 1.4.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugin_dir_url( ALG_WC_PGBCL_FILE ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @version 1.4.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( ALG_WC_PGBCL_FILE ) );
		}
	}

endif;
