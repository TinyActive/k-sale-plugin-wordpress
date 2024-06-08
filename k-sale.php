<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://thangdangblog.com
 * @since             1.0.0
 * @package           K_Sale
 *
 * @wordpress-plugin
 * Plugin Name:       KSale
 * Plugin URI:        https://thangdangblog.com
 * Description:       Máy bán hàng trên nền tảng Wordpress
 * Version:           1.0.0
 * Author:            Đặng Quốc Thắng
 * Author URI:        https://thangdangblog.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       k-sale
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
define( 'K_SALE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-k-sale-activator.php
 */
function activate_k_sale() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-k-sale-activator.php';
	K_Sale_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-k-sale-deactivator.php
 */
function deactivate_k_sale() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-k-sale-deactivator.php';
	K_Sale_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_k_sale' );
register_deactivation_hook( __FILE__, 'deactivate_k_sale' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-k-sale.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_k_sale() {

	$plugin = new K_Sale();
	$plugin->run();

}
run_k_sale();
