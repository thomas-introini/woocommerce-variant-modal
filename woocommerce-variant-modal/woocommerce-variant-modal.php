<?php
/**
 * Plugin Name:       WooCommerce Variant Modal
 * Description:       Open a modal on archive (and non-single) pages for variable products to choose attributes, show live price, and add to cart via AJAX. Customizable styles & behavior.
 * Version:           1.0.3
 * Author:            Thomas Introini
 * Text Domain:       wc-variant-modal
 * Domain Path:       /languages
 * Requires PHP:      7.4
 * Requires at least: 6.2
 * WC requires at least: 7.0
 * WC tested up to:   10.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WCVM_VERSION', '1.0.3' );
define( 'WCVM_FILE', __FILE__ );
define( 'WCVM_DIR', plugin_dir_path( __FILE__ ) );
define( 'WCVM_URL', plugin_dir_url( __FILE__ ) );

add_action( 'before_woocommerce_init', function () {
	if ( class_exists( '\\Automattic\\WooCommerce\\Utilities\\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WCVM_FILE, true );
	}
} );

register_activation_hook( __FILE__, function () {
	// Basic WooCommerce dependency check on activation.
	if ( ! class_exists( 'WooCommerce' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( esc_html__( 'WooCommerce Variant Modal requires WooCommerce to be installed and active.', 'wc-variant-modal' ) );
	}
} );

add_action( 'plugins_loaded', function () {
	load_plugin_textdomain( 'wc-variant-modal', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	// Defer includes until WooCommerce is loaded.
	if ( class_exists( 'WooCommerce' ) ) {
		require_once WCVM_DIR . 'includes/helpers.php';
		require_once WCVM_DIR . 'includes/class-wcvm-plugin.php';
		WCVM_Plugin::instance();
	}
} );

