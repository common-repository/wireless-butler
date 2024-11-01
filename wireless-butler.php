<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.google.com/
 * @since             1.0.11
 * @package           Wireless_Butler
 *
 * @wordpress-plugin
 * Plugin Name:       Wireless Butler
 * Description:       Wireless Butler helps businesses competitively sell mobile service by instantly reading bills and recommending your service.
 * Version:           1.0.11
 * Author:            Validas
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wireless-butler
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WIRELESS_BUTLER_VERSION', '1.0.11' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wireless-butler-activator.php
 */
function activate_wireless_butler() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wireless-butler-activator.php';
	Wireless_Butler_Activator::createPluginTable();
	Wireless_Butler_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wireless-butler-deactivator.php
 */
function deactivate_wireless_butler() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wireless-butler-deactivator.php';
	Wireless_Butler_Deactivator::deactivate();
}

/**
 * The code that runs during plugin uninstall.
 * This action is documented in includes/class-wireless-butler-uninstall.php
 */
function uninstall_wireless_butler() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wireless-butler-uninstall.php';
	Wireless_Butler_Uninstall::dropPluginTable();
	Wireless_Butler_Uninstall::uninstall();
}

register_activation_hook( __FILE__, 'activate_wireless_butler' );
register_deactivation_hook( __FILE__, 'deactivate_wireless_butler' );
register_uninstall_hook( __FILE__, 'uninstall_wireless_butler' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wireless-butler.php';

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
require_once plugin_dir_path( __FILE__ ) . 'admin/class-wireless-butler-admin-regex-list.php';
require_once plugin_dir_path( __FILE__ ) . 'admin/class-wireless-butler-admin-customer-recommendation-plan.php';
require_once plugin_dir_path( __FILE__ ) . 'admin/class-wireless-butler-admin-entries.php';
require_once plugin_dir_path( __FILE__ ) . 'admin/class-wireless-butler-admin-plan-db.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wireless_butler() {

	$plugin = new Wireless_Butler();
	$plugin->run();

}
run_wireless_butler();