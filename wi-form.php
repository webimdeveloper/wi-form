<?php
/**
 * Plugin Name: WiForm
 * Description: WP Form Calculator
 * Version: 0.1.0
 * Author: Webim
 * Text Domain: wiform
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin core constants.
 */
define( 'WIFORM_FILE', __FILE__ );
define( 'WIFORM_PATH', plugin_dir_path( __FILE__ ) );
define( 'WIFORM_URL', plugin_dir_url( __FILE__ ) );

/**
 * Load the main plugin class.
 */
require_once WIFORM_PATH . 'includes/class-wiform-plugin.php';

/**
 * Bootstrap the plugin.
 */
function wiform_bootstrap() {
	$plugin = new WiForm_Plugin();
	$plugin->register();
}
add_action( 'plugins_loaded', 'wiform_bootstrap' );

/**
 * DEBUG: simple shortcode to verify plugin is actually loading.
 *
 * Usage: [wiform_test]
 * Expected output: "WiForm test shortcode is working."
 */
add_shortcode(
	'wiform_test',
	function () {
		return 'WiForm test shortcode is working.';
	}
);